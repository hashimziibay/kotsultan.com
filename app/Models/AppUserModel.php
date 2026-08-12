<?php

namespace App\Models;

use CodeIgniter\Model;

class AppUserModel extends Model
{
    protected $table            = 'app_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'phone',
        'account_type',
        'password_hash',
        'locale',
        'theme',
        'status',
        'api_token',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        return $digits;
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(array $user, string $password): bool
    {
        $hash = (string) ($user['password_hash'] ?? '');
        if ($hash === '') {
            return false;
        }
        return password_verify($password, $hash);
    }

    public function issueToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update($userId, ['api_token' => $token]);
        return $token;
    }

    public function findByToken(?string $token): ?array
    {
        if ($token === null || $token === '') {
            return null;
        }
        $user = $this->where('api_token', $token)->first();
        if (! $user) {
            return null;
        }
        if (($user['status'] ?? 'active') !== 'active') {
            return null;
        }
        return $user;
    }

    public function publicProfile(array $user): array
    {
        $type = ($user['account_type'] ?? 'user') === 'business' ? 'business' : 'user';

        return [
            'id'           => (int) $user['id'],
            'name'         => $user['name'],
            'phone'        => $user['phone'],
            'account_type' => $type,
            'is_business'  => $type === 'business',
            'has_password' => ! empty($user['password_hash']),
            'locale'       => $user['locale'] ?? 'en',
            'theme'        => $user['theme'] ?? 'light',
            'status'       => $user['status'] ?? 'active',
        ];
    }
}
