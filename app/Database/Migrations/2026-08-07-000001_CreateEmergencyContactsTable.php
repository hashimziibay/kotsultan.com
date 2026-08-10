<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmergencyContactsTable extends Migration
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
            'category_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'category_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'department_name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'department_name_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'phone_primary' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'phone_secondary' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'address_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'address_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'working_hours_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'working_hours_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'website' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'google_maps' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'latitude' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'phone-call',
            ],
            'display_order' => [
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
        $this->forge->addKey(['status', 'display_order']);
        $this->forge->addKey('category_en');
        $this->forge->addKey('category_ur');
        $this->forge->createTable('emergency_contacts', true);
    }

    public function down()
    {
        $this->forge->dropTable('emergency_contacts', true);
    }
}
