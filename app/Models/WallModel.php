<?php

namespace App\Models;

use CodeIgniter\Model;

class WallModel extends Model
{
    protected $table            = 'wall_of_kot_sultan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'photo',
        'name_en',
        'name_ur',
        'intro_en',
        'intro_ur',
        'years_of_service',
        'display_order',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveWallEntries()
    {
        return $this->where('status', 'active')
                    ->orderBy('display_order', 'ASC')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
