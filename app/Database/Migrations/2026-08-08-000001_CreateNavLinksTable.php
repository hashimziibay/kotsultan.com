<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNavLinksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title_en' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'title_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addKey('status');
        $this->forge->addKey('sort_order');
        $this->forge->createTable('nav_links', true);

        // Load language to get current exact translations
        $langEn = \Config\Services::language();
        $langEn->setLocale('en');

        // We can't rely strictly on service('language') changing locale reliably inside migration in all CI4 versions,
        // so we'll fetch them from the array directly if possible, or just require the files.
        $en = require APPPATH . 'Language/en/App.php';
        $ur = require APPPATH . 'Language/ur/App.php';

        $data = [
            [
                'title_en'   => $en['nav_home'] ?? 'Home',
                'title_ur'   => $ur['nav_home'] ?? 'صفحہ اول',
                'url'        => '/',
                'sort_order' => 1,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_directory'] ?? 'Directory',
                'title_ur'   => $ur['nav_directory'] ?? 'ڈائریکٹری',
                'url'        => 'directory',
                'sort_order' => 2,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_emergency'] ?? 'Emergency Contacts',
                'title_ur'   => $ur['nav_emergency'] ?? 'ہنگامی رابطے',
                'url'        => 'emergency-numbers',
                'sort_order' => 3,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_wall'] ?? 'Wall of Kot Sultan',
                'title_ur'   => $ur['nav_wall'] ?? 'وال آف کوٹ سلطان',
                'url'        => 'wall-of-kot-sultan',
                'sort_order' => 4,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_volunteer'] ?? 'Volunteer',
                'title_ur'   => $ur['nav_volunteer'] ?? 'رضاکار',
                'url'        => 'volunteer',
                'sort_order' => 5,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_about'] ?? 'About',
                'title_ur'   => $ur['nav_about'] ?? 'ہمارے بارے میں',
                'url'        => 'about',
                'sort_order' => 6,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title_en'   => $en['nav_contact'] ?? 'Contact',
                'title_ur'   => $ur['nav_contact'] ?? 'رابطہ کریں',
                'url'        => 'contact',
                'sort_order' => 7,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('nav_links')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('nav_links', true);
    }
}
