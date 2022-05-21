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
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    }

    public function index()
    {
        return view('unauthorized_access');
    }

    public function getTransaction()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $transactionsModel = new TransactionsModel();
        $accountsModel = new AccountsModel();
        $utility = new Utility();
        $account_name = "";
        $account_id = "";
        $account_op_balance = 0;
        $group_id = "";
        $today = new Time('now');
        switch ($postdata->book) {
            case 'CH':
                $account_name = "Cash Account";
                $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
                break;
            case 'BK':
                $account_name = "Bank Account";
                $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
                break;
            case 'SA':
                $account_name = "Sales Account";
                $group_id = "C1EF16E9-F3DA-69A0-9C29-E23A5E1A96A8";
                break;
            case 'PU':
                $account_name = "Purchase Account";
                $group_id = "8FB61DF9-9086-5DBD-BD33-3F3A4068829C";
                break;
            default:
                $account_name = "Cash Account";
                $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
                break;
        }

        $accounts = $accountsModel->getAccountByName($postdata->cid, $account_name);
        if (sizeof($accounts) > 0) {
            $account = $accounts[0];
            $account_id = $account->acnt_id;
            $account_op_balance = $account->acnt_opbal;
        } else {
            $account_id = $utility->guid();
            $data = [
                'acnt_id' => $account_id,
                'acnt_name' => $account_name,
                'acnt_ag_id' => $group_id,
                'acnt_client_id' => $postdata->cid,
                'acnt_opbal' => 0,
                'acnt_clbal' => 0,
                'acnt_inactive' => 0,
                'acnt_isdefault' => 1,
                'acnt_remarks' => 'Default',
                'acnt_modified' => $today->toDateTimeString()
            ];
            $accountsModel->addAccount($data);
        }

        $filt = "";
        if (!empty($postdata->fdate))
            $filt .= " AND (txn_date >= '" . $postdata->fdate . "')";
        if (!empty($postdata->tdate))
            $filt .= " AND (txn_date <= '" . $postdata->tdate . "')";

        $data['transactions'] = $transactionsModel->getTransaction($account_id, $filt, $postdata->ps, $postdata->pn * $postdata->ps);
        $data['records'] = $transactionsModel->getTransactionsCount($account_id, $filt);
        $data['op_balance'] = $account_op_balance;
        $data['op_totals'] = $transactionsModel->getOpeningTotals($account_id, $postdata->fdate);
        $data['cl_totals'] = $transactionsModel->getClosingTotals($account_id, $postdata->tdate);
        $data['account_id'] = $account_id;
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
