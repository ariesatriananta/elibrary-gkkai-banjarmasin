<?php

namespace App\Services;

use App\Models\AgeClassificationModel;
use App\Models\BookCopyModel;
use App\Models\BookModel;
use App\Models\CategoryModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookImportService
{
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Rekap Buku') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $categoryModel = new CategoryModel();
        $classificationModel = new AgeClassificationModel();
        $bookModel = new BookModel();
        $copyModel = new BookCopyModel();

        $summary = [
            'inserted_books' => 0,
            'inserted_copies' => 0,
            'skipped_rows' => 0,
        ];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            $title = $this->cleanValue($row['C'] ?? null);

            if ($title === null) {
                $summary['skipped_rows']++;
                continue;
            }

            $author = $this->cleanValue($row['G'] ?? null) ?? 'Tanpa Penulis';
            $publisher = $this->cleanValue($row['F'] ?? null);
            $categoryName = $this->cleanValue($row['D'] ?? null);
            $classificationName = $this->cleanValue($row['E'] ?? null);
            $legacyCode = $this->cleanValue($row['B'] ?? null);
            $pageCount = $this->toIntOrNull($row['H'] ?? null);
            $shelfLocation = $this->cleanValue($row['I'] ?? null);
            $isbn = $this->cleanValue($row['J'] ?? null);
            $year = $this->toIntOrNull($row['K'] ?? null);
            $stock = max(1, $this->toIntOrNull($row['L'] ?? null) ?? 1);
            $synopsis = $this->cleanValue($row['M'] ?? null);
            $legacyStatus = $this->cleanValue($row['N'] ?? null);

            $categoryId = $this->resolveCategoryId($categoryModel, $categoryName);
            $classificationId = $this->resolveClassificationId($classificationModel, $classificationName);

            $existingBook = $bookModel
                ->where('title', $title)
                ->where('author', $author)
                ->first();

            if ($existingBook) {
                $bookId = (int) $existingBook['id'];
            } else {
                $bookId = (int) $bookModel->insert([
                    'title' => $title,
                    'author' => $author,
                    'publisher' => $publisher,
                    'publication_year' => $year,
                    'isbn' => $isbn,
                    'page_count' => $pageCount,
                    'category_id' => $categoryId,
                    'age_classification_id' => $classificationId,
                    'synopsis' => $synopsis,
                    'shelf_location' => $shelfLocation,
                    'legacy_status' => $legacyStatus,
                    'status' => 'available',
                ], true);

                $summary['inserted_books']++;
            }

            for ($copyIndex = 1; $copyIndex <= $stock; $copyIndex++) {
                $copyCode = $this->generateCopyCode($bookId, $copyIndex);

                if ($copyModel->where('copy_code', $copyCode)->first()) {
                    continue;
                }

                $copyModel->insert([
                    'book_id' => $bookId,
                    'copy_code' => $copyCode,
                    'legacy_code' => $legacyCode,
                    'barcode_value' => $copyCode,
                    'status' => 'available',
                    'notes' => $legacyStatus,
                ]);

                $summary['inserted_copies']++;
            }
        }

        return $summary;
    }

    private function resolveCategoryId(CategoryModel $model, ?string $name): ?int
    {
        if ($name === null) {
            return null;
        }

        $existing = $model->where('slug', url_title($name, '-', true))->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        return (int) $model->insert([
            'name' => $name,
            'slug' => url_title($name, '-', true),
            'sort_order' => 999,
        ], true);
    }

    private function resolveClassificationId(AgeClassificationModel $model, ?string $name): ?int
    {
        if ($name === null) {
            return null;
        }

        $existing = $model->where('slug', url_title($name, '-', true))->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        return (int) $model->insert([
            'name' => $name,
            'slug' => url_title($name, '-', true),
            'sort_order' => 999,
        ], true);
    }

    private function cleanValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function toIntOrNull(mixed $value): ?int
    {
        $value = $this->cleanValue($value);

        if ($value === null || ! is_numeric(str_replace(',', '.', $value))) {
            return null;
        }

        return (int) $value;
    }

    private function generateCopyCode(int $bookId, int $copyIndex): string
    {
        return sprintf('BK-%06d-%02d', $bookId, $copyIndex);
    }
}
