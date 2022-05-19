<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'usr_id';

    public function getUsers($filter, $sortBy, $pageNo, $pageSize)
    {
        $result = $this->builder()->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
        return $result;
    }
    public function getUsersCount($filter)
    {
        $result = $this->builder()->select('users.*')
            ->where('(1=1) ' . $filter)           
            ->countAllResults();
        return $result;
    }
    public function addUser($data)
    {
        $this->builder()->insert($data);
    }
    public function updateUser($data)
    {
        $this->builder()->where('usr_id', $data['usr_id'])->update($data);
    }
    public function deleteUser($usr_id)
    {
        $this->builder()->where('usr_id', $usr_id)->delete();
    }

    public function getUserByUsername($usr_name)
    {
        $result = $this->builder()->select('*')
            ->where('usr_name', $usr_name)
            ->get()->getResult();
        return $result;
    }
}
