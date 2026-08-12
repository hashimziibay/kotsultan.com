<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * HTML-styled years_of_service needs more than VARCHAR(500).
 */
class ExpandWallYearsOfServiceForHtml extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('wall_of_kot_sultan', [
            'years_of_service' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('wall_of_kot_sultan', [
            'years_of_service' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
    }
}
