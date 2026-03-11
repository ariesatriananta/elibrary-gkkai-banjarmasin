<?php

namespace App\Controllers;

use App\Models\AgeClassificationModel;
use App\Models\BookCopyModel;
use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\LoanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class BookController extends BaseController
{
    private BookModel $bookModel;
    private BookCopyModel $copyModel;
    private CategoryModel $categoryModel;
    private AgeClassificationModel $ageClassificationModel;
    private LoanModel $loanModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->copyModel = new BookCopyModel();
        $this->categoryModel = new CategoryModel();
        $this->ageClassificationModel = new AgeClassificationModel();
        $this->loanModel = new LoanModel();
    }

    public function index(): string
    {
        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'category_id' => trim((string) $this->request->getGet('category_id')),
            'age_classification_id' => trim((string) $this->request->getGet('age_classification_id')),
            'stock_status' => trim((string) $this->request->getGet('stock_status')),
        ];

        $books = $this->filterBooks($this->fetchBooksForIndex(), $filters);

        return view('books/index', [
            'pageTitle' => 'Data Buku',
            'activeMenu' => 'books',
            'books' => $books,
            'filters' => $filters,
            'categories' => $this->categoryModel->orderBy('sort_order', 'ASC')->findAll(),
            'ageClassifications' => $this->ageClassificationModel->orderBy('sort_order', 'ASC')->findAll(),
            'summary' => $this->buildSummary($books),
        ]);
    }

    public function create(): string
    {
        return view('books/form', [
            'pageTitle' => 'Tambah Buku',
            'activeMenu' => 'books',
            'mode' => 'create',
            'book' => $this->emptyBook(),
            'copies' => [],
            'categories' => $this->categoryModel->orderBy('sort_order', 'ASC')->findAll(),
            'ageClassifications' => $this->ageClassificationModel->orderBy('sort_order', 'ASC')->findAll(),
            'copyStatuses' => $this->copyStatuses(),
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = $this->bookValidationRules(true);

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('copy_form', ['mode' => 'create'])->with('errors', $this->validator->getErrors());
        }

        $data = $this->collectBookPayload();
        $data['cover_path'] = $this->handleCoverUpload();

        $bookId = (int) $this->bookModel->insert($data, true);
        $initialCopies = (int) $this->request->getPost('initial_copies');

        $this->createInitialCopies($bookId, $initialCopies);

        return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $book = $this->bookModel->find($id);

        if (! $book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Buku tidak ditemukan.');
        }

        $copies = $this->copyModel
            ->where('book_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('books/form', [
            'pageTitle' => 'Edit Buku',
            'activeMenu' => 'books',
            'mode' => 'edit',
            'book' => $book,
            'copies' => $copies,
            'categories' => $this->categoryModel->orderBy('sort_order', 'ASC')->findAll(),
            'ageClassifications' => $this->ageClassificationModel->orderBy('sort_order', 'ASC')->findAll(),
            'copyStatuses' => $this->copyStatuses(),
            'errors' => session('errors') ?? [],
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $book = $this->bookModel->find($id);

        if (! $book) {
            return redirect()->to(site_url('books'))->with('error', 'Buku tidak ditemukan.');
        }

        $rules = $this->bookValidationRules(false);

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('copy_form', ['mode' => 'edit', 'copy_id' => $copyId])->with('errors', $this->validator->getErrors());
        }

        $data = $this->collectBookPayload();

        if ($this->request->getPost('remove_cover') === '1') {
            $this->deleteCoverFile($book['cover_path']);
            $data['cover_path'] = null;
        }

        $newCover = $this->handleCoverUpload();

        if ($newCover !== null) {
            $this->deleteCoverFile($book['cover_path']);
            $data['cover_path'] = $newCover;
        }

        $this->bookModel->update($id, $data);

        return redirect()->to(site_url('books/' . $id . '/edit'))->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $book = $this->bookModel->find($id);

        if (! $book) {
            return redirect()->to(site_url('books'))->with('error', 'Buku tidak ditemukan.');
        }

        $copyIds = array_column(
            $this->copyModel->select('id')->where('book_id', $id)->findAll(),
            'id'
        );

        if ($copyIds !== [] && $this->loanModel->whereIn('book_copy_id', $copyIds)->countAllResults() > 0) {
            return redirect()->to(site_url('books'))->with('error', 'Buku tidak bisa dihapus karena sudah memiliki histori transaksi.');
        }

        try {
            $this->bookModel->delete($id);
            $this->deleteCoverFile($book['cover_path']);
        } catch (DatabaseException) {
            return redirect()->to(site_url('books'))->with('error', 'Buku gagal dihapus. Cek relasi data terlebih dahulu.');
        }

        return redirect()->to(site_url('books'))->with('success', 'Buku berhasil dihapus.');
    }

    public function storeCopy(int $bookId): RedirectResponse
    {
        $book = $this->bookModel->find($bookId);

        if (! $book) {
            return redirect()->to(site_url('books'))->with('error', 'Buku tidak ditemukan.');
        }

        $rules = [
            'barcode_value' => 'permit_empty|max_length[80]|is_unique[book_copies.barcode_value]',
            'legacy_code' => 'permit_empty|max_length[50]',
            'status' => 'required|in_list[available,borrowed]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nextNumber = $this->nextCopyNumber($bookId);

        $this->copyModel->insert([
            'book_id' => $bookId,
            'copy_code' => $this->generateCopyCode($bookId, $nextNumber),
            'legacy_code' => $this->nullableString($this->request->getPost('legacy_code')),
            'barcode_value' => $this->nullableString($this->request->getPost('barcode_value')),
            'status' => (string) $this->request->getPost('status'),
            'notes' => $this->nullableString($this->request->getPost('notes')),
        ]);

        return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('success', 'Copy buku berhasil ditambahkan.');
    }

    public function updateCopy(int $bookId, int $copyId): RedirectResponse
    {
        $copy = $this->copyModel
            ->where('id', $copyId)
            ->where('book_id', $bookId)
            ->first();

        if (! $copy) {
            return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('error', 'Copy buku tidak ditemukan.');
        }

        $rules = [
            'barcode_value' => 'permit_empty|max_length[80]',
            'legacy_code' => 'permit_empty|max_length[50]',
            'status' => 'required|in_list[available,borrowed]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $barcode = $this->nullableString($this->request->getPost('barcode_value'));

        if ($barcode !== null) {
            $duplicate = $this->copyModel
                ->where('barcode_value', $barcode)
                ->where('id !=', $copyId)
                ->first();

            if ($duplicate) {
                return redirect()->back()->withInput()->with('copy_form', ['mode' => 'edit', 'copy_id' => $copyId])->with('errors', [
                    'barcode_value' => 'Barcode sudah dipakai copy buku lain.',
                ]);
            }
        }

        $this->copyModel->update($copyId, [
            'legacy_code' => $this->nullableString($this->request->getPost('legacy_code')),
            'barcode_value' => $barcode,
            'status' => (string) $this->request->getPost('status'),
            'notes' => $this->nullableString($this->request->getPost('notes')),
        ]);

        return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('success', 'Copy buku berhasil diperbarui.');
    }

    public function destroyCopy(int $bookId, int $copyId): RedirectResponse
    {
        $copy = $this->copyModel
            ->where('id', $copyId)
            ->where('book_id', $bookId)
            ->first();

        if (! $copy) {
            return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('error', 'Copy buku tidak ditemukan.');
        }

        if ($copy['status'] === 'borrowed') {
            return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('error', 'Copy yang sedang dipinjam tidak bisa dihapus.');
        }

        if ($this->loanModel->where('book_copy_id', $copyId)->countAllResults() > 0) {
            return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('error', 'Copy buku tidak bisa dihapus karena memiliki histori transaksi.');
        }

        $this->copyModel->delete($copyId);

        return redirect()->to(site_url('books/' . $bookId . '/edit'))->with('success', 'Copy buku berhasil dihapus.');
    }

    private function fetchBooksForIndex(): array
    {
        $sql = "
            SELECT
                b.*,
                c.name AS category_name,
                ac.name AS age_classification_name,
                (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = b.id) AS total_copies,
                (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = b.id AND bc.status = 'available') AS available_copies,
                (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = b.id AND bc.status = 'borrowed') AS borrowed_copies,
                (SELECT GROUP_CONCAT(bc.copy_code SEPARATOR ' ') FROM book_copies bc WHERE bc.book_id = b.id) AS copy_codes,
                (SELECT GROUP_CONCAT(COALESCE(bc.legacy_code, '') SEPARATOR ' ') FROM book_copies bc WHERE bc.book_id = b.id) AS legacy_codes,
                (SELECT GROUP_CONCAT(COALESCE(bc.barcode_value, '') SEPARATOR ' ') FROM book_copies bc WHERE bc.book_id = b.id) AS barcode_values
            FROM books b
            LEFT JOIN categories c ON c.id = b.category_id
            LEFT JOIN age_classifications ac ON ac.id = b.age_classification_id
            ORDER BY b.created_at DESC, b.id DESC
        ";

        $rows = db_connect()->query($sql)->getResultArray();

        return array_map(fn (array $row): array => $this->decorateBookRow($row), $rows);
    }

    private function filterBooks(array $books, array $filters): array
    {
        return array_values(array_filter($books, function (array $book) use ($filters): bool {
            if ($filters['category_id'] !== '' && (int) $book['category_id'] !== (int) $filters['category_id']) {
                return false;
            }

            if ($filters['age_classification_id'] !== '' && (int) $book['age_classification_id'] !== (int) $filters['age_classification_id']) {
                return false;
            }

            if ($filters['stock_status'] === 'available' && (int) $book['available_copies'] < 1) {
                return false;
            }

            if ($filters['stock_status'] === 'borrowed' && ((int) $book['available_copies'] > 0 || (int) $book['total_copies'] === 0)) {
                return false;
            }

            if ($filters['q'] === '') {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $book['title'] ?? '',
                $book['author'] ?? '',
                $book['publisher'] ?? '',
                $book['isbn'] ?? '',
                $book['category_name'] ?? '',
                $book['age_classification_name'] ?? '',
                $book['copy_codes'] ?? '',
                $book['legacy_codes'] ?? '',
                $book['barcode_values'] ?? '',
            ]));

            return str_contains($haystack, mb_strtolower($filters['q']));
        }));
    }

    private function buildSummary(array $books): array
    {
        return [
            'titles' => count($books),
            'copies' => array_sum(array_map(fn (array $book): int => (int) $book['total_copies'], $books)),
            'available' => array_sum(array_map(fn (array $book): int => (int) $book['available_copies'], $books)),
            'borrowed' => array_sum(array_map(fn (array $book): int => (int) $book['borrowed_copies'], $books)),
        ];
    }

    private function decorateBookRow(array $row): array
    {
        $palette = [
            'from-primary/80 to-primary/40',
            'from-accent/80 to-accent/40',
            'from-info/80 to-info/40',
            'from-success/80 to-success/40',
            'from-warning/80 to-warning/40',
            'from-destructive/60 to-destructive/30',
        ];

        $row['cover_class'] = $palette[((int) $row['id']) % count($palette)];
        $row['total_copies'] = (int) ($row['total_copies'] ?? 0);
        $row['available_copies'] = (int) ($row['available_copies'] ?? 0);
        $row['borrowed_copies'] = (int) ($row['borrowed_copies'] ?? 0);
        if ($row['total_copies'] === 0) {
            $row['stock_status_label'] = 'Belum Ada Copy';
            $row['stock_status_class'] = 'status-badge-returned';
        } elseif ($row['available_copies'] > 0) {
            $row['stock_status_label'] = 'Tersedia';
            $row['stock_status_class'] = 'status-badge-available';
        } else {
            $row['stock_status_label'] = 'Dipinjam';
            $row['stock_status_class'] = 'status-badge-borrowed';
        }

        return $row;
    }

    private function bookValidationRules(bool $isCreate): array
    {
        $rules = [
            'title' => 'required|max_length[200]',
            'author' => 'required|max_length[150]',
            'publisher' => 'permit_empty|max_length[150]',
            'publication_year' => 'permit_empty|integer|greater_than_equal_to[1000]|less_than_equal_to[2100]',
            'isbn' => 'permit_empty|max_length[30]',
            'page_count' => 'permit_empty|integer|greater_than_equal_to[1]',
            'category_id' => 'permit_empty|integer',
            'age_classification_id' => 'permit_empty|integer',
            'shelf_location' => 'permit_empty|max_length[100]',
            'legacy_status' => 'permit_empty|max_length[100]',
            'synopsis' => 'permit_empty',
            'cover' => 'permit_empty|is_image[cover]|mime_in[cover,image/jpg,image/jpeg,image/png,image/webp]|max_size[cover,2048]',
        ];

        if ($isCreate) {
            $rules['initial_copies'] = 'required|integer|greater_than_equal_to[1]|less_than_equal_to[100]';
        }

        return $rules;
    }

    private function collectBookPayload(): array
    {
        return [
            'title' => trim((string) $this->request->getPost('title')),
            'author' => trim((string) $this->request->getPost('author')),
            'publisher' => $this->nullableString($this->request->getPost('publisher')),
            'publication_year' => $this->nullableInt($this->request->getPost('publication_year')),
            'isbn' => $this->nullableString($this->request->getPost('isbn')),
            'page_count' => $this->nullableInt($this->request->getPost('page_count')),
            'category_id' => $this->nullableInt($this->request->getPost('category_id')),
            'age_classification_id' => $this->nullableInt($this->request->getPost('age_classification_id')),
            'synopsis' => $this->nullableString($this->request->getPost('synopsis')),
            'shelf_location' => $this->nullableString($this->request->getPost('shelf_location')),
            'legacy_status' => $this->nullableString($this->request->getPost('legacy_status')),
            'status' => 'available',
        ];
    }

    private function handleCoverUpload(): ?string
    {
        $file = $this->request->getFile('cover');

        if (! $file || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $targetDirectory = ROOTPATH . 'public/assets/uploads/covers';

        if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0775, true) && ! is_dir($targetDirectory)) {
            throw new \RuntimeException('Folder upload cover tidak dapat dibuat.');
        }

        $newName = $file->getRandomName();
        $file->move($targetDirectory, $newName, true);

        return 'assets/uploads/covers/' . $newName;
    }

    private function deleteCoverFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $fullPath = ROOTPATH . 'public/' . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function createInitialCopies(int $bookId, int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            $this->copyModel->insert([
                'book_id' => $bookId,
                'copy_code' => $this->generateCopyCode($bookId, $index),
                'barcode_value' => $this->generateCopyCode($bookId, $index),
                'status' => 'available',
            ]);
        }
    }

    private function generateCopyCode(int $bookId, int $copyNumber): string
    {
        return sprintf('BK-%06d-%02d', $bookId, $copyNumber);
    }

    private function nextCopyNumber(int $bookId): int
    {
        $codes = array_column(
            $this->copyModel->select('copy_code')->where('book_id', $bookId)->findAll(),
            'copy_code'
        );

        $max = 0;

        foreach ($codes as $code) {
            if (preg_match('/-(\d+)$/', (string) $code, $matches) === 1) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max + 1;
    }

    private function emptyBook(): array
    {
        return [
            'id' => null,
            'title' => old('title', ''),
            'author' => old('author', ''),
            'publisher' => old('publisher', ''),
            'publication_year' => old('publication_year', ''),
            'isbn' => old('isbn', ''),
            'page_count' => old('page_count', ''),
            'category_id' => old('category_id', ''),
            'age_classification_id' => old('age_classification_id', ''),
            'cover_path' => null,
            'synopsis' => old('synopsis', ''),
            'shelf_location' => old('shelf_location', ''),
            'legacy_status' => old('legacy_status', ''),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        $value = trim((string) $value);

        return $value === '' ? null : (int) $value;
    }

    private function copyStatuses(): array
    {
        return [
            'available' => 'Tersedia',
            'borrowed' => 'Dipinjam',
        ];
    }
}
