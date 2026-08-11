<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Live WordPress DBs often have signed INT ids; our attachments table used UNSIGNED,
 * which breaks the foreign key on insert. Align wall_id with parent id type.
 * Also adds external_links JSON/text for multiple profile URLs.
 */
class FixWallAttachmentsFkAndAddLinks extends Migration
{
    public function up()
    {
        // 1) Fix wall_attachments.wall_id type to match wall_of_kot_sultan.id
        if ($this->db->tableExists('wall_attachments') && $this->db->tableExists('wall_of_kot_sultan')) {
            $dbName = $this->db->getDatabase();

            // Drop every FK that references wall_id (name may differ across hosts).
            try {
                $fks = $this->db->query(
                    'SELECT CONSTRAINT_NAME
                     FROM information_schema.KEY_COLUMN_USAGE
                     WHERE TABLE_SCHEMA = ?
                       AND TABLE_NAME = ?
                       AND COLUMN_NAME = ?
                       AND REFERENCED_TABLE_NAME IS NOT NULL',
                    [$dbName, 'wall_attachments', 'wall_id']
                )->getResultArray();

                foreach ($fks as $fk) {
                    $name = $fk['CONSTRAINT_NAME'] ?? '';
                    if ($name === '') {
                        continue;
                    }
                    try {
                        $this->db->query('ALTER TABLE `wall_attachments` DROP FOREIGN KEY `' . str_replace('`', '``', $name) . '`');
                    } catch (\Throwable $e) {
                        // already gone
                    }
                }
            } catch (\Throwable $e) {
                try {
                    $this->db->query('ALTER TABLE `wall_attachments` DROP FOREIGN KEY `wall_attachments_wall_id_foreign`');
                } catch (\Throwable $e2) {
                    // ignore
                }
            }

            $col  = $this->db->query("SHOW COLUMNS FROM `wall_of_kot_sultan` LIKE 'id'")->getRowArray();
            $type = $col['Type'] ?? 'int(11)';
            // Keep NULL/NOT NULL consistent with child column usage.
            $this->db->query("ALTER TABLE `wall_attachments` MODIFY `wall_id` {$type} NOT NULL");

            // Re-add FK only if every wall_id points at a real parent row.
            try {
                $orphans = (int) ($this->db->query(
                    'SELECT COUNT(*) AS c
                     FROM `wall_attachments` a
                     LEFT JOIN `wall_of_kot_sultan` w ON w.id = a.wall_id
                     WHERE w.id IS NULL'
                )->getRowArray()['c'] ?? 0);

                if ($orphans === 0) {
                    $this->db->query(
                        'ALTER TABLE `wall_attachments`
                         ADD CONSTRAINT `wall_attachments_wall_id_foreign`
                         FOREIGN KEY (`wall_id`) REFERENCES `wall_of_kot_sultan` (`id`)
                         ON DELETE CASCADE ON UPDATE CASCADE'
                    );
                }
            } catch (\Throwable $e) {
                // App-level parent checks still protect inserts if FK cannot be re-added.
            }
        }

        // 2) Multiple external URLs on each wall person
        if ($this->db->tableExists('wall_of_kot_sultan') && ! $this->db->fieldExists('external_links', 'wall_of_kot_sultan')) {
            $this->forge->addColumn('wall_of_kot_sultan', [
                'external_links' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'photo',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('wall_of_kot_sultan') && $this->db->fieldExists('external_links', 'wall_of_kot_sultan')) {
            $this->forge->dropColumn('wall_of_kot_sultan', 'external_links');
        }
    }
}
