<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    public function getUsers($filter, $sortBy, $pageNo, $pageSize)
    {
        
        $builder = $db->table('users');
        $result = $builder->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
    }
}
