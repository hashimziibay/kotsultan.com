<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppUsersTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'locale' => [
                'type'       => 'ENUM',
                'constraint' => ['en', 'ur'],
                'default'    => 'en',
            ],
            'theme' => [
                'type'       => 'ENUM',
                'constraint' => ['light', 'dark'],
                'default'    => 'light',
            ],
            'api_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
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
        $this->forge->addUniqueKey('phone');
        $this->forge->addKey('api_token');
        $this->forge->createTable('app_users', true);
    }

    public function down()
    {
        $this->forge->dropTable('app_users', true);
    }
}
