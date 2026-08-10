<?php

namespace App\Models;

use CodeIgniter\Model;

class NavLinkModel extends Model
{
    protected $table            = 'nav_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title_en',
        'title_ur',
        'url',
        'sort_order',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'title_en' => 'required|max_length[255]',
        'title_ur' => 'required|max_length[255]',
        'url'      => 'required|max_length[255]',
        'status'   => 'in_list[active,inactive]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
