<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixFineIndexes extends Migration
{
    public function up()
    {
        $indexes = $this->db->query('SHOW INDEX FROM fines')->getResultArray();
        $grouped = [];

        foreach ($indexes as $index) {
            $grouped[$index['Key_name']][] = $index;
        }

        foreach ($grouped as $keyName => $parts) {
            $isUnique = isset($parts[0]['Non_unique']) && (int) $parts[0]['Non_unique'] === 0;
            $columns = array_map(static fn (array $part): string => (string) $part['Column_name'], $parts);

            if ($isUnique && $columns === ['loan_id']) {
                $this->db->query('ALTER TABLE fines DROP INDEX `' . str_replace('`', '``', $keyName) . '`');
            }
        }

        $indexes = $this->db->query('SHOW INDEX FROM fines')->getResultArray();
        $hasComposite = false;

        foreach ($indexes as $index) {
            if (
                (string) $index['Key_name'] === 'fines_loan_type_unique'
                && (string) $index['Column_name'] === 'loan_id'
            ) {
                $hasComposite = true;
                break;
            }
        }

        if (! $hasComposite) {
            $this->db->query('ALTER TABLE fines ADD UNIQUE KEY fines_loan_type_unique (loan_id, fine_type)');
        }
    }

    public function down()
    {
        // No-op. This migration only repairs a partially migrated schema.
    }
}
