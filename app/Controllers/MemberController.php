<?php

namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MemberController extends BaseController
{
    private const INDEX_PER_PAGE = 15;
    private const HISTORY_PER_PAGE = 10;

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

        $filters = $this->indexFilters();
        $pagination = $this->indexPaginationState($filters);
        $members = $this->fetchMembers($filters, $pagination['per_page'], $pagination['page']);

        return view('members/index', [
            'pageTitle' => 'Data Anggota',
            'activeMenu' => 'members',
            'members' => $members,
            'filters' => $filters,
            'pagination' => $pagination,
            'summary' => $pagination['summary'],
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

    public function export(): ResponseInterface
    {
        service('libraryService')->syncStatuses();

        $filters = $this->indexFilters();
        $members = $this->fetchMembersForExport($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Anggota');

        $headers = [
            'No',
            'Nomor Anggota',
            'Nama Lengkap',
            'Telepon',
            'Email',
            'Alamat',
            'Tanggal Bergabung',
            'Status',
            'Pinjaman Aktif',
        ];

        $sheet->fromArray($headers, null, 'A1');

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $rowNumber = 2;

        foreach ($members as $index => $member) {
            $sheet->fromArray([
                $index + 1,
                $member['member_number'] ?: '-',
                $member['full_name'] ?: '-',
                $member['phone'] ?: '-',
                $member['email'] ?: '-',
                $member['address'] ?: '-',
                $member['joined_at'] ? format_indo_date($member['joined_at']) : '-',
                (int) ($member['is_active'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif',
                (int) ($member['active_loans'] ?? 0),
            ], null, 'A' . $rowNumber);

            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'members-export-');
        $writer->save($tempFile);

        $fileName = 'master-anggota-' . date('Y-m-d-His') . '.xlsx';
        $content = file_get_contents($tempFile) ?: '';
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($content);
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

        $historyPagination = $this->memberHistoryPaginationState($id);

        return view('members/form', [
            'pageTitle' => 'Edit Anggota',
            'activeMenu' => 'members',
            'mode' => 'edit',
            'member' => $member,
            'history' => $this->memberHistory($id, $historyPagination['per_page'], $historyPagination['page']),
            'historyPagination' => $historyPagination,
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

    private function indexFilters(): array
    {
        $status = trim((string) $this->request->getGet('status'));

        return [
            'q' => trim((string) $this->request->getGet('q')),
            'status' => in_array($status, ['active', 'inactive'], true) ? $status : '',
        ];
    }

    private function indexPaginationState(array $filters): array
    {
        $summaryRow = $this->memberBaseBuilder($filters)
            ->select("
                COUNT(*) AS total_rows,
                COALESCE(SUM(CASE WHEN m.is_active = 1 THEN 1 ELSE 0 END), 0) AS active_count,
                COALESCE(SUM(CASE WHEN m.is_active = 0 THEN 1 ELSE 0 END), 0) AS inactive_count,
                COALESCE(SUM((
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.member_id = m.id
                      AND l.status IN ('borrowed', 'overdue')
                )), 0) AS active_loans_count
            ", false)
            ->get()
            ->getRowArray() ?? [];

        $totalRows = (int) ($summaryRow['total_rows'] ?? 0);
        $perPage = self::INDEX_PER_PAGE;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = max(1, (int) $this->request->getGet('page'));
        $page = min($page, $totalPages);
        $from = $totalRows > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = $totalRows > 0 ? min($from + $perPage - 1, $totalRows) : 0;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'from' => $from,
            'to' => $to,
            'summary' => [
                'total' => $totalRows,
                'active' => (int) ($summaryRow['active_count'] ?? 0),
                'inactive' => (int) ($summaryRow['inactive_count'] ?? 0),
                'loans' => (int) ($summaryRow['active_loans_count'] ?? 0),
            ],
        ];
    }

    private function fetchMembers(array $filters, int $perPage, int $page): array
    {
        $rows = $this->memberBaseBuilder($filters)
            ->select("
                m.*,
                (
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.member_id = m.id
                      AND l.status IN ('borrowed', 'overdue')
                ) AS active_loans
            ", false)
            ->orderBy('m.created_at', 'DESC')
            ->orderBy('m.id', 'DESC')
            ->limit($perPage, max(0, ($page - 1) * $perPage))
            ->get()
            ->getResultArray();

        return array_map(function (array $member): array {
            $member['active_loans'] = (int) ($member['active_loans'] ?? 0);
            $member['initials'] = person_initials($member['full_name'] ?? '');

            return $member;
        }, $rows);
    }

    private function fetchMembersForExport(array $filters): array
    {
        return $this->memberBaseBuilder($filters)
            ->select("
                m.*,
                (
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.member_id = m.id
                      AND l.status IN ('borrowed', 'overdue')
                ) AS active_loans
            ", false)
            ->orderBy('m.created_at', 'DESC')
            ->orderBy('m.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function memberBaseBuilder(array $filters)
    {
        $builder = db_connect()->table('members m');

        if ($filters['status'] === 'active') {
            $builder->where('m.is_active', 1);
        }

        if ($filters['status'] === 'inactive') {
            $builder->where('m.is_active', 0);
        }

        if ($filters['q'] !== '') {
            $builder
                ->groupStart()
                ->like('m.member_number', $filters['q'])
                ->orLike('m.full_name', $filters['q'])
                ->orLike('m.phone', $filters['q'])
                ->orLike('m.email', $filters['q'])
                ->orLike('m.address', $filters['q'])
                ->groupEnd();
        }

        return $builder;
    }

    private function memberHistoryPaginationState(int $memberId): array
    {
        $summaryRow = db_connect()->query("
            SELECT COUNT(*) AS total_rows
            FROM loans l
            WHERE l.member_id = ?
        ", [$memberId])->getRowArray() ?? [];

        $totalRows = (int) ($summaryRow['total_rows'] ?? 0);
        $perPage = self::HISTORY_PER_PAGE;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = max(1, (int) $this->request->getGet('history_page'));
        $page = min($page, $totalPages);
        $from = $totalRows > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = $totalRows > 0 ? min($from + $perPage - 1, $totalRows) : 0;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'from' => $from,
            'to' => $to,
        ];
    }

    private function memberHistory(int $memberId, int $perPage, int $page): array
    {
        $offset = max(0, ($page - 1) * $perPage);

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
            LIMIT {$perPage} OFFSET {$offset}
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
