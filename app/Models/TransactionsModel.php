<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{

    protected $table = 'transactions';
    protected $primaryKey = 'txn_id';

    public function getTransaction($acnt_id, $filter, $pageNo, $pageSize)
    {
        $result = $this->builder()->select("SELECT transactions.*, acnt_name, 'DR' as etype 
        FROM transactions
        INNER JOIN accounts ON accounts.acnt_id = transactions.txn_acnt_id_cr
        WHERE (txn_acnt_id_dr = '" . $acnt_id . "') " . $filter . "
            UNION
            SELECT transactions.*, acnt_name, 'CR' as etype 
                FROM transactions
                INNER JOIN accounts ON accounts.acnt_id = transactions.txn_acnt_id_dr
                WHERE (txn_acnt_id_cr = '" . $acnt_id . "') " . $filter . "
            ORDER BY txn_date LIMIT " . $pageNo . ", " . $pageSize)
            ->get()->getResult();
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
