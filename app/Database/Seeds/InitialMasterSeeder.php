<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialMasterSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $categories = [
            ['name' => 'Alkitab', 'description' => 'Buku-buku yang berfokus pada Alkitab, termasuk tafsir, terjemahan, dan studi Alkitab.'],
            ['name' => 'Teologi', 'description' => 'Buku-buku yang membahas doktrin-doktrin Kristen seperti Trinitas, Kristologi, dan Soteriologi.'],
            ['name' => 'Sejarah Gereja', 'description' => 'Buku-buku tentang sejarah gereja, tokoh gereja, dan peristiwa penting kekristenan.'],
            ['name' => 'Praktika Kristen', 'description' => 'Buku-buku tentang penerapan iman Kristen dalam kehidupan sehari-hari.'],
            ['name' => 'Kesusastraan Kristen', 'description' => 'Buku-buku fiksi dan non-fiksi bernuansa Kristen.'],
            ['name' => 'Teologi Pastoral', 'description' => 'Buku-buku tentang pelayanan pastoral, konseling, dan penggembalaan.'],
            ['name' => 'Pemuridan', 'description' => 'Buku-buku tentang pemuridan, pelatihan rohani, dan pertumbuhan iman.'],
            ['name' => 'Filsafat', 'description' => 'Buku-buku filsafat yang relevan dengan pemikiran Kristen.'],
            ['name' => 'Psikologi', 'description' => 'Buku-buku psikologi yang berkaitan dengan iman dan kehidupan.'],
            ['name' => 'Pendidikan', 'description' => 'Buku-buku pendidikan Kristen, kurikulum, dan metode pengajaran.'],
            ['name' => 'Keluarga', 'description' => 'Buku-buku tentang keluarga Kristen, pernikahan, dan pengasuhan anak.'],
            ['name' => 'Misi', 'description' => 'Buku-buku tentang penginjilan, misi, dan pekerjaan misionaris.'],
            ['name' => 'Musik Gereja', 'description' => 'Buku-buku musik gereja, himne, dan pujian.'],
            ['name' => 'Biografi', 'description' => 'Buku-buku biografi tokoh-tokoh Kristen yang inspiratif.'],
        ];

        foreach ($categories as $index => $category) {
            $this->db->table('categories')->insert([
                'name' => $category['name'],
                'slug' => url_title($category['name'], '-', true),
                'description' => $category['description'],
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (['Anak', 'Remaja', 'Pemuda', 'Dewasa', 'Umum'] as $index => $name) {
            $this->db->table('age_classifications')->insert([
                'name' => $name,
                'slug' => url_title($name, '-', true),
                'description' => null,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->db->table('settings')->insertBatch([
            [
                'setting_key' => 'library_name',
                'setting_value' => 'Elibrary GKKAI',
                'value_type' => 'string',
                'label' => 'Nama Perpustakaan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'fine_per_day',
                'setting_value' => '1500',
                'value_type' => 'number',
                'label' => 'Denda per Hari',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_key' => 'loan_duration_days',
                'setting_value' => '14',
                'value_type' => 'number',
                'label' => 'Durasi Pinjam Default',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $this->db->table('admins')->insert([
            'full_name' => 'Administrator',
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
