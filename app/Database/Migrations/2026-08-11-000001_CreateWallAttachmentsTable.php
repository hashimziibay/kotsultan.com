<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWallAttachmentsTable extends Migration
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
            'wall_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'file_type' => [
                'type'       => 'ENUM',
                'constraint' => ['image', 'pdf', 'word', 'other'],
                'default'    => 'other',
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'display_order' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey('wall_id');
        $this->forge->addForeignKey('wall_id', 'wall_of_kot_sultan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('wall_attachments', true);
    }

    public function down()
    {
        $this->forge->dropTable('wall_attachments', true);
    }
}
