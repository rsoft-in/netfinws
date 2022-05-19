<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountGroupsModel extends Model
{

    protected $table = 'accountgroups';
    protected $primaryKey = 'ag_id';

    public function getAccountGrp($filter, $sortBy, $pageNo, $pageSize)
    {
        $result = $this->builder()->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
        return $result;
    }
    public function getAccountGrpCount($filter)
    {
        $result = $this->builder()->select('accountgroups.*')
            ->where('(1=1) ' . $filter)           
            ->countAllResults();
        return $result;
    }

    public function addAccountGrp($data)
    {
        $this->builder()->insert($data);
    }
    public function updateAccountGrp($data)
    {
        $this->builder()->where('ag_id', $data['ag_id'])->update($data);
    }
    public function deleteAccountGrp($ag_id)
    {
        $this->builder()->where('ag_id', $ag_id)->delete();
    }
}
