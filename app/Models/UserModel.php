<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';

    public function getUsers($filter, $sortBy, $pageNo, $pageSize)
    {
        $result = $this->builder()->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
        return $result;
    }

    public function getUserByUsername($usr_name)
    {
        $result = $this->builder()->select('*')
            ->where('usr_name', $usr_name)
            ->get()->getResult();
        return $result;
    }
}
