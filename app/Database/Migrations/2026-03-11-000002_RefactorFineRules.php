<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorFineRules extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('return_condition', 'loans')) {
            $this->forge->addColumn('loans', [
                'return_condition' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'good',
                    'after' => 'returned_at',
                ],
            ]);
        }

        $fineColumns = [];

        if (! $this->db->fieldExists('fine_type', 'fines')) {
            $fineColumns['fine_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'late',
                'after' => 'loan_id',
            ];
        }

        if (! $this->db->fieldExists('fine_label', 'fines')) {
            $fineColumns['fine_label'] = [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'fine_type',
            ];
        }

        if (! $this->db->fieldExists('rate_amount', 'fines')) {
            $fineColumns['rate_amount'] = [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
                'after' => 'fine_per_day',
            ];
        }

        if (! $this->db->fieldExists('rate_unit', 'fines')) {
            $fineColumns['rate_unit'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'rate_amount',
            ];
        }

        if (! $this->db->fieldExists('grace_days', 'fines')) {
            $fineColumns['grace_days'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'rate_unit',
            ];
        }

        if (! $this->db->fieldExists('quantity', 'fines')) {
            $fineColumns['quantity'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'late_days',
            ];
        }

        if (! $this->db->fieldExists('fulfillment_method', 'fines')) {
            $fineColumns['fulfillment_method'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'payment',
                'after' => 'quantity',
            ];
        }

        if (! $this->db->fieldExists('resolved_at', 'fines')) {
            $fineColumns['resolved_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'paid_at',
            ];
        }

        if ($fineColumns !== []) {
            $this->forge->addColumn('fines', $fineColumns);
        }

        try {
            $this->db->query('ALTER TABLE fines DROP INDEX loan_id');
        } catch (\Throwable) {
            // Ignore if the old unique key is already absent.
        }

        try {
            $this->db->query('ALTER TABLE fines ADD UNIQUE KEY fines_loan_type_unique (loan_id, fine_type)');
        } catch (\Throwable) {
            // Ignore if the composite unique key already exists.
        }

        $this->db->query("
            UPDATE fines
            SET
                fine_type = COALESCE(NULLIF(fine_type, ''), 'late'),
                fine_label = COALESCE(NULLIF(fine_label, ''), 'Denda Keterlambatan'),
                fine_per_day = 0,
                rate_amount = 5000,
                rate_unit = COALESCE(NULLIF(rate_unit, ''), 'week'),
                grace_days = 3,
                quantity = CASE
                    WHEN COALESCE(late_days, 0) <= 3 THEN 0
                    ELSE CEILING((COALESCE(late_days, 0) - 3) / 7)
                END,
                amount = CASE
                    WHEN COALESCE(late_days, 0) <= 3 THEN 0
                    ELSE CEILING((COALESCE(late_days, 0) - 3) / 7) * 5000
                END,
                fulfillment_method = COALESCE(NULLIF(fulfillment_method, ''), 'payment'),
                status = CASE
                    WHEN paid_amount >= (
                        CASE
                            WHEN COALESCE(late_days, 0) <= 3 THEN 0
                            ELSE CEILING((COALESCE(late_days, 0) - 3) / 7) * 5000
                        END
                    ) THEN 'paid'
                    WHEN paid_amount > 0 THEN 'partial'
                    ELSE 'unpaid'
                END
            WHERE fine_type = 'late' OR fine_type IS NULL OR fine_type = ''
        ");

        $now = date('Y-m-d H:i:s');
        $settings = [
            ['late_fine_per_week', '5000', 'number', 'Denda Keterlambatan per Minggu'],
            ['late_grace_days', '3', 'number', 'Masa Tenggang Keterlambatan (hari)'],
            ['damage_fine_amount', '100000', 'number', 'Denda Kerusakan Buku'],
        ];

        foreach ($settings as [$key, $value, $type, $label]) {
            $existing = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();

            if ($existing) {
                continue;
            }

            $this->db->table('settings')->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'value_type' => $type,
                'label' => $label,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE fines DROP INDEX fines_loan_type_unique');
        } catch (\Throwable) {
            // Ignore when rollback key is missing.
        }

        try {
            $this->db->query('ALTER TABLE fines ADD UNIQUE KEY loan_id (loan_id)');
        } catch (\Throwable) {
            // Ignore if the old key cannot be restored.
        }

        if ($this->db->fieldExists('return_condition', 'loans')) {
            $this->forge->dropColumn('loans', 'return_condition');
        }

        foreach (['fine_type', 'fine_label', 'rate_amount', 'rate_unit', 'grace_days', 'quantity', 'fulfillment_method', 'resolved_at'] as $column) {
            if ($this->db->fieldExists($column, 'fines')) {
                $this->forge->dropColumn('fines', $column);
            }
        }

        $this->db->table('settings')->whereIn('setting_key', [
            'late_fine_per_week',
            'late_grace_days',
            'damage_fine_amount',
        ])->delete();
    }
}
