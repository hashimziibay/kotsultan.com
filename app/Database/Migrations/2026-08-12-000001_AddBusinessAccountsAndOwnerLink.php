<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Business accounts: account_type + password on app_users,
 * and owner link businesses.user_id → app_users.id
 */
class AddBusinessAccountsAndOwnerLink extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('app_users')) {
            if (! $this->db->fieldExists('account_type', 'app_users')) {
                $this->forge->addColumn('app_users', [
                    'account_type' => [
                        'type'       => 'ENUM',
                        'constraint' => ['user', 'business'],
                        'default'    => 'user',
                        'after'      => 'phone',
                    ],
                ]);
            }
            if (! $this->db->fieldExists('password_hash', 'app_users')) {
                $this->forge->addColumn('app_users', [
                    'password_hash' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'null'       => true,
                        'after'      => 'account_type',
                    ],
                ]);
            }
            if (! $this->db->fieldExists('status', 'app_users')) {
                $this->forge->addColumn('app_users', [
                    'status' => [
                        'type'       => 'ENUM',
                        'constraint' => ['active', 'inactive'],
                        'default'    => 'active',
                        'after'      => 'theme',
                    ],
                ]);
            }
        }

        if ($this->db->tableExists('businesses') && ! $this->db->fieldExists('user_id', 'businesses')) {
            $this->forge->addColumn('businesses', [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            try {
                $this->db->query('ALTER TABLE `businesses` ADD INDEX `businesses_user_id` (`user_id`)');
            } catch (\Throwable $e) {
                // index may already exist
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('businesses') && $this->db->fieldExists('user_id', 'businesses')) {
            $this->forge->dropColumn('businesses', 'user_id');
        }
        if ($this->db->tableExists('app_users')) {
            foreach (['status', 'password_hash', 'account_type'] as $col) {
                if ($this->db->fieldExists($col, 'app_users')) {
                    $this->forge->dropColumn('app_users', $col);
                }
            }
        }
    }
}
