<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ImportBooksCommand extends BaseCommand
{
    protected $group = 'Library';
    protected $name = 'library:import-books';
    protected $description = 'Import data buku awal dari file Excel .xls/.xlsx.';
    protected $usage = 'library:import-books <file_path>';
    protected $arguments = [
        'file_path' => 'Path lengkap file Excel yang akan diimport.',
    ];

    public function run(array $params)
    {
        $filePath = $params[0] ?? null;

        if ($filePath === null || ! is_file($filePath)) {
            CLI::error('File Excel tidak ditemukan. Berikan path file yang valid.');

            return;
        }

        $summary = service('bookImportService')->import($filePath);

        CLI::write('Import selesai.', 'green');
        CLI::write('Buku baru: ' . $summary['inserted_books']);
        CLI::write('Copy baru: ' . $summary['inserted_copies']);
        CLI::write('Baris dilewati: ' . $summary['skipped_rows']);
    }
}
