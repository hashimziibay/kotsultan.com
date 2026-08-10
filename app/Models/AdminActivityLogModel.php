<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminActivityLogModel extends Model
{
    protected $table            = 'admin_activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'admin_id',
        'admin_username',
        'action',
        'module',
        'record_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public static function log(string $action, string $module, $recordId = null, string $details = ''): bool
    {
        $session  = session();
        $request  = service('request');
        $logModel = new self();

        return (bool) $logModel->insert([
            'admin_id'       => $session->get('admin_id'),
            'admin_username' => $session->get('admin_username') ?? 'system',
            'action'         => $action,
            'module'         => $module,
            'record_id'      => (string) $recordId,
            'details'        => $details,
            'ip_address'     => $request->getIPAddress(),
            'user_agent'     => substr((string) $request->getUserAgent(), 0, 250),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
