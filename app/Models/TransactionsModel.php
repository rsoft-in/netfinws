<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{

    protected $table = 'transactions';
    protected $primaryKey = 'txn_id';

    public function getTransaction($acnt_id, $filter, $pageNo, $pageSize)
    {
        $result = $this->builder()->select("transactions.*, acnt_dr.acnt_name as acnt_name_dr, acnt_cr.acnt_name as acnt_name_cr, if(acnt_dr.acnt_id = '" . $acnt_id . "', 'DR', 'CR') as etype")
            ->join('accounts acnt_dr', 'acnt_dr.acnt_id = transactions.txn_acnt_id_dr', 'inner')
            ->join('accounts acnt_cr', 'acnt_cr.acnt_id = transactions.txn_acnt_id_cr', 'inner')
            ->where("(txn_acnt_id_dr = '" . $acnt_id . "') OR (txn_acnt_id_cr = '" . $acnt_id . "') " . $filter)
            ->orderBy('txn_date')
            ->limit($pageNo, $pageSize)
            ->get()->getResult();

        return $result;
    }
    public function getTransactionsCount($acnt_id, $filter) {
        $result = $this->builder()->select("transactions.*, acnt_dr.acnt_name as acnt_name_dr, acnt_cr.acnt_name as acnt_name_cr, if(acnt_dr.acnt_id = '" . $acnt_id . "', 'DR', 'CR') as etype")
            ->join('accounts acnt_dr', 'acnt_dr.acnt_id = transactions.txn_acnt_id_dr', 'inner')
            ->join('accounts acnt_cr', 'acnt_cr.acnt_id = transactions.txn_acnt_id_cr', 'inner')
            ->where("(txn_acnt_id_dr = '" . $acnt_id . "') OR (txn_acnt_id_cr = '" . $acnt_id . "') " . $filter)
            ->orderBy('txn_date')
            ->countAllResults();

        return $result;
    }
    public function addTransaction($data)
    {
        $this->builder()->insert($data);
    }
    public function updateTransaction($data)
    {
        $this->builder()->where('txn_id', $data['txn_id'])->update($data);
    }
    public function deleteTransaction($txn_id)
    {
        $this->builder()->where('txn_id', $txn_id)->delete();
    }
}
