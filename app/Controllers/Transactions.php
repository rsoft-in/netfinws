<?php

namespace App\Controllers;

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

        $filt = "";
        if (!isset($postdata->cid)) {
            return $this->respond('INVALID REQUEST');
        }
        if (!empty($postdata->fdate))
            $filt .= " AND (txn_date >= '" . $postdata->fdate . "')";
        if (!empty($postdata->tdate))
            $filt .= " AND (txn_date <= '" . $postdata->tdate . "')";

        $data['transactions'] = $transactionsModel->getTransaction($postdata->acnt_id, $filt, $postdata->ps, $postdata->pn * $postdata->ps);
        $data['records'] = $transactionsModel->getTransactionsCount($postdata->acnt_id, $filt);
        $data['op_totals'] = $transactionsModel->getOpeningTotals($postdata->acnt_id, $postdata->fdate);
        $data['cl_totals'] = $transactionsModel->getClosingTotals($postdata->acnt_id, $postdata->tdate);
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
