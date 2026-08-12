<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Allow wall personalities to belong to multiple categories.
 */
class AddWallEntryCategoriesPivot extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('wall_entry_categories')) {
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
                'category_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['wall_id', 'category_id'], 'wall_entry_cat_unique');
            $this->forge->addKey('category_id', false, false, 'wall_entry_cat_category');
            $this->forge->createTable('wall_entry_categories', true);
        }

        // Backfill from existing single category_id
        if ($this->db->tableExists('wall_of_kot_sultan') && $this->db->fieldExists('category_id', 'wall_of_kot_sultan')) {
            $rows = $this->db->table('wall_of_kot_sultan')
                ->select('id, category_id')
                ->where('category_id IS NOT NULL')
                ->where('category_id >', 0)
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $exists = $this->db->table('wall_entry_categories')
                    ->where('wall_id', (int) $row['id'])
                    ->where('category_id', (int) $row['category_id'])
                    ->countAllResults();
                if ($exists < 1) {
                    $this->db->table('wall_entry_categories')->insert([
                        'wall_id'     => (int) $row['id'],
                        'category_id' => (int) $row['category_id'],
                    ]);
                }
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('wall_entry_categories')) {
            $this->forge->dropTable('wall_entry_categories', true);
        }
    }
}
