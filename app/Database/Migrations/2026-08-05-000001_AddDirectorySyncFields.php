<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDirectorySyncFields extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'name_en' => ['type' => 'VARCHAR', 'constraint' => 100], 'name_ur' => ['type' => 'VARCHAR', 'constraint' => 100], 'slug' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true], 'created_at' => ['type' => 'DATETIME', 'null' => true]]);
        $this->forge->addKey('id', true); $this->forge->createTable('villages', true);
        $this->forge->addColumn('businesses', [
            'village_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'area_id'],
            'source_post_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true, 'unique' => true],
            'description_en' => ['type' => 'TEXT', 'null' => true], 'description_ur' => ['type' => 'TEXT', 'null' => true],
            'address_en' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], 'address_ur' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], 'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'opening_hours' => ['type' => 'TEXT', 'null' => true], 'gallery_images' => ['type' => 'TEXT', 'null' => true], 'social_links' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->db->query('ALTER TABLE businesses ADD CONSTRAINT fk_businesses_village_id FOREIGN KEY (village_id) REFERENCES villages(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }
    public function down() { $this->db->query('ALTER TABLE businesses DROP FOREIGN KEY fk_businesses_village_id'); $this->forge->dropColumn('businesses', ['village_id','source_post_id','description_en','description_ur','address_en','address_ur','website','email','opening_hours','gallery_images','social_links']); $this->forge->dropTable('villages', true); }
}
