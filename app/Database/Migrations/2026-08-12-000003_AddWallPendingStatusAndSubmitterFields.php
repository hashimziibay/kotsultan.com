<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Allow public personality nominations with pending admin approval.
 */
class AddWallPendingStatusAndSubmitterFields extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('wall_of_kot_sultan')) {
            return;
        }

        // Expand status to include pending submissions
        try {
            $this->db->query(
                "ALTER TABLE `wall_of_kot_sultan`
                 MODIFY COLUMN `status` ENUM('active','inactive','pending') NOT NULL DEFAULT 'active'"
            );
        } catch (\Throwable $e) {
            // already updated
        }

        if (! $this->db->fieldExists('submitter_name', 'wall_of_kot_sultan')) {
            $this->forge->addColumn('wall_of_kot_sultan', [
                'submitter_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'status',
                ],
            ]);
        }
        if (! $this->db->fieldExists('submitter_phone', 'wall_of_kot_sultan')) {
            $this->forge->addColumn('wall_of_kot_sultan', [
                'submitter_phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'submitter_name',
                ],
            ]);
        }
        if (! $this->db->fieldExists('submitter_email', 'wall_of_kot_sultan')) {
            $this->forge->addColumn('wall_of_kot_sultan', [
                'submitter_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'submitter_phone',
                ],
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('wall_of_kot_sultan')) {
            return;
        }

        foreach (['submitter_email', 'submitter_phone', 'submitter_name'] as $col) {
            if ($this->db->fieldExists($col, 'wall_of_kot_sultan')) {
                $this->forge->dropColumn('wall_of_kot_sultan', $col);
            }
        }

        try {
            $this->db->query("UPDATE `wall_of_kot_sultan` SET `status` = 'inactive' WHERE `status` = 'pending'");
            $this->db->query(
                "ALTER TABLE `wall_of_kot_sultan`
                 MODIFY COLUMN `status` ENUM('active','inactive') NOT NULL DEFAULT 'active'"
            );
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
