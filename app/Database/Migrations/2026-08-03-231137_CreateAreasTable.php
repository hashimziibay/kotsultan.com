<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAreasTable extends Migration
{
    public function up()
    {
        // 1. Areas Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'name_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('areas', true);

        // 2. Add area_id to businesses
        $fields = [
            'area_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'category_id',
            ],
        ];
        $this->forge->addColumn('businesses', $fields);
        
        // Add foreign key
        $this->db->query('ALTER TABLE businesses ADD CONSTRAINT fk_businesses_area_id FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE businesses DROP FOREIGN KEY fk_businesses_area_id');
        $this->forge->dropColumn('businesses', 'area_id');
        $this->forge->dropTable('areas', true);
    }
}
