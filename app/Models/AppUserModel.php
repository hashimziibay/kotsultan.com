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
        'locale',
        'theme',
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
        return $this->where('api_token', $token)->first();
    }

    public function publicProfile(array $user): array
    {
        return [
            'id'     => (int) $user['id'],
            'name'   => $user['name'],
            'phone'  => $user['phone'],
            'locale' => $user['locale'] ?? 'en',
            'theme'  => $user['theme'] ?? 'light',
        ];
    }
}
