<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use App\Models\TransactionsModel;
use App\Libraries\Utility;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Utility as GlobalUtility;

class Transactions extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('unauthorized_access');
    }

    public function getTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $transactionsModel = new TransactionsModel();
        $accountsModel = new AccountsModel();

        $filt = "";
        if (!isset($json->cid)) {
            return $this->respond('INVALID REQUEST');
        }
        if (!empty($json->fdate))
            $filt .= " AND (txn_date >= '" . $json->fdate . "')";
        if (!empty($json->tdate))
            $filt .= " AND (txn_date <= '" . $json->tdate . "')";

        $data['transactions'] = $transactionsModel->getTransaction($json->acnt_id, $filt, $json->ps, $json->pn * $json->ps);
        $data['op_balance'] = $accountsModel->builder()
            ->select('acnt_opbal, accountgroups.ag_type')
            ->join('accountgroups', 'accountgroups.ag_id = accounts.acnt_ag_id', 'inner')
            ->where('acnt_id', $json->acnt_id)->get()->getResult();
        $data['records'] = $transactionsModel->getTransactionsCount($json->acnt_id, $filt);
        $data['op_totals'] = $transactionsModel->getOpeningTotals($json->acnt_id, $json->fdate);
        $data['cl_totals'] = $transactionsModel->getClosingTotals($json->acnt_id, $json->tdate);
        return $this->respond($data);
    }

    public function addTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $transactionsModel = new transactionsModel();
        $data = [
            'txn_id' => $json->txn_id,
            'txn_date' => $json->txn_date,
            'txn_ref_nr' => $json->txn_ref_nr,
            'txn_client_id' => $json->txn_client_id,
            'txn_acnt_id_dr' => $json->txn_acnt_id_dr,
            'txn_acnt_id_cr' => $json->txn_acnt_id_cr,
            'txn_amount_dr' => $json->txn_amount_dr,
            'txn_amount_cr' => $json->txn_amount_cr,
            'txn_remarks' => $json->txn_remarks,
            'txn_type' => $json->txn_type,
            'txn_user_id' => $json->txn_user_id,
            'txn_modified' => $today->toDateTimeString()
        ];
        $transactionsModel->addTransaction($data);
        echo 'SUCCESS';
    }
    public function updateTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $transactionsModel = new TransactionsModel;
        $data = [
            'txn_id' => $json->txn_id,
            'txn_date' => $json->txn_date,
            'txn_ref_nr' => $json->txn_ref_nr,
            'txn_client_id' => $json->txn_client_id,
            'txn_acnt_id_dr' => $json->txn_acnt_id_dr,
            'txn_acnt_id_cr' => $json->txn_acnt_id_cr,
            'txn_amount_dr' => $json->txn_amount_dr,
            'txn_amount_cr' => $json->txn_amount_cr,
            'txn_remarks' => $json->txn_remarks,
            'txn_type' => $json->txn_type,
            'txn_user_id' => $json->txn_user_id,
            'txn_modified' => $today->toDateTimeString()
        ];
        $transactionsModel->updateTransaction($data);
        echo 'SUCCESS';
    }
    public function deleteTransaction()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $transactionsModel = new TransactionsModel;
        $transactionsModel->deleteTransaction($json->txn_id);
        echo 'SUCCESS';
    }
}
