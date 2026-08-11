<?php

namespace App\Commands;

use App\Models\AdminUserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Resets (or creates) the default admin login for local recovery.
 *
 * Usage: php spark admin:reset-password
 */
class ResetAdminPassword extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'admin:reset-password';
    protected $description = 'Reset admin user password to the default temporary password.';
    protected $usage       = 'admin:reset-password [username]';
    protected $arguments   = [
        'username' => 'Admin username to reset (default: admin)',
    ];

    public function run(array $params)
    {
        $username = trim((string) ($params[0] ?? 'admin'));
        if ($username === '') {
            $username = 'admin';
        }

        $password = 'Admin@12345';
        $model    = new AdminUserModel();
        $user     = $model->where('username', $username)->first();

        if ($user) {
            $ok = $model->update((int) $user['id'], [
                'password_hash'        => password_hash($password, PASSWORD_BCRYPT),
                'must_change_password' => 1,
                'status'               => 'active',
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);

            if (!$ok) {
                CLI::error('Failed to update admin password.');
                return;
            }

            CLI::write('Admin password reset successfully.', 'green');
        } else {
            $id = $model->insert([
                'username'             => $username,
                'email'                => $username . '@kotsultan.com',
                'password_hash'        => password_hash($password, PASSWORD_BCRYPT),
                'role'                 => 'superadmin',
                'must_change_password' => 1,
                'status'               => 'active',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ], true);

            if (!$id) {
                CLI::error('Failed to create admin user.');
                return;
            }

            CLI::write('Admin user created successfully.', 'green');
        }

        CLI::write('Username: ' . $username);
        CLI::write('Password: ' . $password);
        CLI::write('You will be asked to change this temporary password after login.');
    }
}
