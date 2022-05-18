<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use App\Models\TransactionsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


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
        $account_name = "";
        $account_id = "";
        $group_id = "";
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

        $account = $accountsModel->getAccountByName($postdata->cid, $account_name);
        var_dump($account_name);
        return;

        $filt = "";
        if (!empty($postdata->qry))
            $filt .= "AND (txn_date LIKE '%" . $postdata->qry . "%')";
        $data['transactions'] = $transactionsModel->getTransaction($filt, $postdata->sort, $postdata->pn, $postdata->ps);
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