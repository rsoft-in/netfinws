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
    public function getAccountByName($acnt_client_id, $acnt_name)
    {
        $result = $this->builder()->select('*')
            ->where(" (acnt_client_id = '" . $acnt_client_id . "') AND (acnt_name = '" . $acnt_name . "') AND (acnt_isdefault = 1)")
            ->get()->getResult();
        return $result;
    }
    public function getAccountByCount($filter)
    {
        $result = $this->builder()->select('accounts.*')
            ->where('(1=1) ' . $filter)           
            ->countAllResults();
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
