<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialLibrarySchema extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'username' => ['type' => 'VARCHAR', 'constraint' => 60],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('admins', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('age_classifications', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'author' => ['type' => 'VARCHAR', 'constraint' => 150],
            'publisher' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'publication_year' => ['type' => 'SMALLINT', 'constraint' => 4, 'null' => true],
            'isbn' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'page_count' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'age_classification_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cover_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'synopsis' => ['type' => 'TEXT', 'null' => true],
            'shelf_location' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'legacy_status' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'available'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('category_id');
        $this->forge->addKey('age_classification_id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('age_classification_id', 'age_classifications', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('books', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'book_id' => ['type' => 'INT', 'unsigned' => true],
            'copy_code' => ['type' => 'VARCHAR', 'constraint' => 50],
            'legacy_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'barcode_value' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'available'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('copy_code');
        $this->forge->addUniqueKey('barcode_value');
        $this->forge->addKey('book_id');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('book_copies', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'member_number' => ['type' => 'VARCHAR', 'constraint' => 30],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'joined_at' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('member_number');
        $this->forge->createTable('members', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'member_id' => ['type' => 'INT', 'unsigned' => true],
            'book_copy_id' => ['type' => 'INT', 'unsigned' => true],
            'processed_by_admin_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'borrowed_at' => ['type' => 'DATETIME'],
            'due_at' => ['type' => 'DATETIME'],
            'returned_at' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'borrowed'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addKey('book_copy_id');
        $this->forge->addKey('processed_by_admin_id');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('book_copy_id', 'book_copies', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('processed_by_admin_id', 'admins', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('loans', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'loan_id' => ['type' => 'INT', 'unsigned' => true],
            'fine_per_day' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'late_days' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'unpaid'],
            'calculated_at' => ['type' => 'DATETIME'],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('loan_id');
        $this->forge->addForeignKey('loan_id', 'loans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fines', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'loan_id' => ['type' => 'INT', 'unsigned' => true],
            'created_by_admin_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'note' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('loan_id');
        $this->forge->addKey('created_by_admin_id');
        $this->forge->addForeignKey('loan_id', 'loans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by_admin_id', 'admins', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('loan_bonus_notes', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
            'value_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'string'],
            'label' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('loan_bonus_notes', true);
        $this->forge->dropTable('fines', true);
        $this->forge->dropTable('loans', true);
        $this->forge->dropTable('members', true);
        $this->forge->dropTable('book_copies', true);
        $this->forge->dropTable('books', true);
        $this->forge->dropTable('age_classifications', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('admins', true);
    }
}
