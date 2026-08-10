<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWallCategoriesTable extends Migration
{
    public function up()
    {
        // 1. Create wall_categories table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'name_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'user',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'emerald',
            ],
            'display_order' => [
                'type'       => 'INT',
                'constraint' => 5,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('wall_categories', true);

        // 2. Add category_id and additional fields to wall_of_kot_sultan table
        $fields = [
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'category_id',
            ],
            'profession_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'name_ur',
            ],
            'profession_ur' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'profession_en',
            ],
            'achievements_en' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'intro_ur',
            ],
            'achievements_ur' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'achievements_en',
            ],
            'awards_en' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'achievements_ur',
            ],
            'awards_ur' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'awards_en',
            ],
            'birth_date' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'years_of_service',
            ],
            'death_date' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'birth_date',
            ],
            'featured' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'death_date',
            ],
            'views' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'featured',
            ],
        ];

        $this->forge->addColumn('wall_of_kot_sultan', $fields);

        // Add indexes & foreign key
        $this->db->query('ALTER TABLE `wall_of_kot_sultan` ADD INDEX `idx_wall_status_order` (`status`, `display_order`)');
        $this->db->query('ALTER TABLE `wall_of_kot_sultan` ADD INDEX `idx_wall_category` (`category_id`)');
        $this->db->query('ALTER TABLE `wall_of_kot_sultan` ADD CONSTRAINT `fk_wall_category` FOREIGN KEY (`category_id`) REFERENCES `wall_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `wall_of_kot_sultan` DROP FOREIGN KEY `fk_wall_category`');
        $this->forge->dropColumn('wall_of_kot_sultan', [
            'category_id',
            'slug',
            'profession_en',
            'profession_ur',
            'achievements_en',
            'achievements_ur',
            'awards_en',
            'awards_ur',
            'birth_date',
            'death_date',
            'featured',
            'views',
        ]);
        $this->forge->dropTable('wall_categories', true);
    }
}
