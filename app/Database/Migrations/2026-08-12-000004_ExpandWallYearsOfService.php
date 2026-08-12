<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * years_of_service was VARCHAR(50) — truncates longer descriptions.
 * Widen to TEXT so full submitted text is stored and shown.
 */
class ExpandWallYearsOfService extends Migration
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
                'constraint' => 50,
                'null'       => true,
            ],
        ]);
    }
}
