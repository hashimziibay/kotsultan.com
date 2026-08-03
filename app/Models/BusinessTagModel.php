<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessTagModel extends Model
{
    protected $table            = 'business_tags';
    protected $primaryKey       = 'business_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['business_id', 'tag_id'];

    public function getTagsForBusiness($businessId)
    {
        return $this->select('tags.*')
                    ->join('tags', 'tags.id = business_tags.tag_id')
                    ->where('business_tags.business_id', $businessId)
                    ->findAll();
    }
}
