<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table            = 'admin_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'username',
        'email',
        'password_hash',
        'role',
        'must_change_password',
        'status',
        'last_login_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Verifies admin login credentials securely.
     */
    public function verifyCredentials(string $login, string $password): ?array
    {
        $user = $this->where('status', 'active')
                     ->groupStart()
                         ->where('username', $login)
                         ->orWhere('email', $login)
                     ->groupEnd()
                     ->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last_login_at
            $this->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
            return $user;
        }

        return null;
    }

    /**
     * Updates password securely with bcrypt.
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->update($userId, [
            'password_hash'        => password_hash($newPassword, PASSWORD_BCRYPT),
            'must_change_password' => 0,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);
    }
}
