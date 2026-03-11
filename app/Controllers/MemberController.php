<?php

namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\HTTP\RedirectResponse;

class MemberController extends BaseController
{
    private MemberModel $memberModel;
    private LoanModel $loanModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->loanModel = new LoanModel();
    }

    public function index(): string
    {
        service('libraryService')->syncStatuses();

        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'status' => trim((string) $this->request->getGet('status')),
        ];

        $members = $this->fetchMembers();
        $members = array_values(array_filter($members, function (array $member) use ($filters): bool {
            if ($filters['status'] === 'active' && (int) $member['is_active'] !== 1) {
                return false;
            }

            if ($filters['status'] === 'inactive' && (int) $member['is_active'] !== 0) {
                return false;
            }

            if ($filters['q'] === '') {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $member['member_number'],
                $member['full_name'],
                $member['phone'],
                $member['email'],
                $member['address'],
            ]));

            return str_contains($haystack, mb_strtolower($filters['q']));
        }));

        return view('members/index', [
            'pageTitle' => 'Data Anggota',
            'activeMenu' => 'members',
            'members' => $members,
            'filters' => $filters,
            'summary' => [
                'total' => count($members),
                'active' => count(array_filter($members, fn (array $member): bool => (int) $member['is_active'] === 1)),
                'inactive' => count(array_filter($members, fn (array $member): bool => (int) $member['is_active'] === 0)),
                'loans' => array_sum(array_map(fn (array $member): int => (int) $member['active_loans'], $members)),
            ],
        ]);
    }

    public function create(): string
    {
        return view('members/form', [
            'pageTitle' => 'Tambah Anggota',
            'activeMenu' => 'members',
            'mode' => 'create',
            'member' => $this->emptyMember(),
            'history' => [],
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $memberId = (int) $this->memberModel->insert([
            'member_number' => service('libraryService')->nextMemberNumber(),
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'phone' => $this->nullableString($this->request->getPost('phone')),
            'email' => $this->nullableString($this->request->getPost('email')),
            'address' => $this->nullableString($this->request->getPost('address')),
            'notes' => $this->nullableString($this->request->getPost('notes')),
            'is_active' => $this->request->getPost('is_active') === '1' ? 1 : 0,
            'joined_at' => $this->nullableString($this->request->getPost('joined_at')) ?? date('Y-m-d'),
        ], true);

        return redirect()->to(site_url('members/' . $memberId . '/edit'))->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        service('libraryService')->syncStatuses();

        $member = $this->memberModel->find($id);

        if (! $member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Anggota tidak ditemukan.');
        }

        return view('members/form', [
            'pageTitle' => 'Edit Anggota',
            'activeMenu' => 'members',
            'mode' => 'edit',
            'member' => $member,
            'history' => $this->memberHistory($id),
            'errors' => session('errors') ?? [],
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $member = $this->memberModel->find($id);

        if (! $member) {
            return redirect()->to(site_url('members'))->with('error', 'Anggota tidak ditemukan.');
        }

        if (! $this->validate($this->validationRules($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->memberModel->update($id, [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'phone' => $this->nullableString($this->request->getPost('phone')),
            'email' => $this->nullableString($this->request->getPost('email')),
            'address' => $this->nullableString($this->request->getPost('address')),
            'notes' => $this->nullableString($this->request->getPost('notes')),
            'is_active' => $this->request->getPost('is_active') === '1' ? 1 : 0,
            'joined_at' => $this->nullableString($this->request->getPost('joined_at')) ?? $member['joined_at'],
        ]);

        return redirect()->to(site_url('members/' . $id . '/edit'))->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $member = $this->memberModel->find($id);

        if (! $member) {
            return redirect()->to(site_url('members'))->with('error', 'Anggota tidak ditemukan.');
        }

        if ($this->loanModel->where('member_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('members/' . $id . '/edit'))->with('error', 'Anggota tidak bisa dihapus karena memiliki histori transaksi. Nonaktifkan saja bila perlu.');
        }

        $this->memberModel->delete($id);

        return redirect()->to(site_url('members'))->with('success', 'Anggota berhasil dihapus.');
    }

    private function fetchMembers(): array
    {
        $sql = "
            SELECT
                m.*,
                (
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.member_id = m.id
                      AND l.status IN ('borrowed', 'overdue')
                ) AS active_loans
            FROM members m
            ORDER BY m.created_at DESC, m.id DESC
        ";

        $rows = db_connect()->query($sql)->getResultArray();

        return array_map(function (array $member): array {
            $member['active_loans'] = (int) ($member['active_loans'] ?? 0);
            $member['initials'] = person_initials($member['full_name'] ?? '');

            return $member;
        }, $rows);
    }

    private function memberHistory(int $memberId): array
    {
        $sql = "
            SELECT
                l.id,
                l.borrowed_at,
                l.due_at,
                l.returned_at,
                l.return_condition,
                l.status,
                b.title AS book_title,
                bc.copy_code,
                (
                    SELECT COALESCE(SUM(f.amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_amount,
                (
                    SELECT COALESCE(SUM(f.paid_amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_paid_amount,
                (
                    SELECT COUNT(*)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fine_type = 'lost'
                      AND f.status = 'open'
                ) AS open_replacement_count
            FROM loans l
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            WHERE l.member_id = ?
            ORDER BY l.borrowed_at DESC, l.id DESC
        ";

        return db_connect()->query($sql, [$memberId])->getResultArray();
    }

    private function validationRules(?int $id = null): array
    {
        $emailRule = 'permit_empty|valid_email|max_length[120]';

        if ($id !== null) {
            $emailRule .= '|is_unique[members.email,id,' . $id . ']';
        } else {
            $emailRule .= '|is_unique[members.email]';
        }

        return [
            'full_name' => 'required|max_length[120]',
            'phone' => 'permit_empty|max_length[30]',
            'email' => $emailRule,
            'address' => 'permit_empty',
            'notes' => 'permit_empty',
            'joined_at' => 'permit_empty|valid_date[Y-m-d]',
            'is_active' => 'required|in_list[0,1]',
        ];
    }

    private function emptyMember(): array
    {
        return [
            'id' => null,
            'member_number' => service('libraryService')->nextMemberNumber(),
            'full_name' => old('full_name', ''),
            'phone' => old('phone', ''),
            'email' => old('email', ''),
            'address' => old('address', ''),
            'notes' => old('notes', ''),
            'is_active' => old('is_active', '1'),
            'joined_at' => old('joined_at', date('Y-m-d')),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
