<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountsModel extends Model
{

    protected $table = 'accounts';
    protected $primaryKey = 'acnt_id';

    public function getAccounts($filter, $sortBy, $pageNo, $pageSize)
    {
        $result = $this->builder()->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
        return $result;
    }
    public function addAccount($data)
    {
        $this->builder()->insert($data);
    }
    public function updateAccount($data)
    {
        $this->builder()->where('acnt_id', $data['acnt_id'])->update($data);
    }
    public function deleteAccount($acnt_id)
    {
        $this->builder()->where('acnt_id', $acnt_id)->delete();
    }
}