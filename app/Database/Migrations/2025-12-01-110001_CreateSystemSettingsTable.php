<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemSettingsTable extends Migration
{
    public function up()
    {
        // Check if table exists
        if ($this->db->tableExists('system_settings')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'general',
            ],
            'setting_type' => [
                'type'       => 'ENUM',
                'constraint' => ['text', 'number', 'boolean', 'json', 'email', 'url'],
                'default'    => 'text',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('setting_key', false, true); // Unique
        $this->forge->addKey('setting_group');
        $this->forge->createTable('system_settings');

        // Insert default settings
        $this->db->table('system_settings')->insertBatch([
            [
                'setting_key'   => 'company_name',
                'setting_value' => 'ChakaNoks',
                'setting_group' => 'company',
                'setting_type'  => 'text',
                'description'   => 'Company name displayed throughout the system',
                'is_public'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'company_tagline',
                'setting_value' => 'Masarap Kahit Walang Laman',
                'setting_group' => 'company',
                'setting_type'  => 'text',
                'description'   => 'Company tagline/slogan',
                'is_public'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'company_email',
                'setting_value' => 'info@chakanoks.com',
                'setting_group' => 'company',
                'setting_type'  => 'email',
                'description'   => 'Primary company email address',
                'is_public'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'company_phone',
                'setting_value' => '+63 912 345 6789',
                'setting_group' => 'company',
                'setting_type'  => 'text',
                'description'   => 'Primary company phone number',
                'is_public'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'company_address',
                'setting_value' => 'Davao City, Philippines',
                'setting_group' => 'company',
                'setting_type'  => 'text',
                'description'   => 'Company address',
                'is_public'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'low_stock_threshold',
                'setting_value' => '10',
                'setting_group' => 'inventory',
                'setting_type'  => 'number',
                'description'   => 'Default low stock alert threshold',
                'is_public'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'expiry_warning_days',
                'setting_value' => '7',
                'setting_group' => 'inventory',
                'setting_type'  => 'number',
                'description'   => 'Days before expiry to show warning',
                'is_public'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'default_royalty_rate',
                'setting_value' => '5.00',
                'setting_group' => 'franchise',
                'setting_type'  => 'number',
                'description'   => 'Default franchise royalty rate percentage',
                'is_public'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'maintenance_mode',
                'setting_value' => '0',
                'setting_group' => 'system',
                'setting_type'  => 'boolean',
                'description'   => 'Enable/disable maintenance mode',
                'is_public'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'backup_retention_days',
                'setting_value' => '30',
                'setting_group' => 'system',
                'setting_type'  => 'number',
                'description'   => 'Number of days to keep backup files',
                'is_public'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('system_settings', true);
    }
}

