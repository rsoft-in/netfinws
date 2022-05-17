<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class Accounts extends BaseController
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

    public function getAccounts()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountsModel = new AccountsModel();
        $filt = "";
        if (!empty($postdata->qry))
            $filt .= "AND (acnt_name LIKE '%" . $postdata->qry . "%' OR acnt_opbal LIKE '%" . $postdata->qry . "%')";

        $data['accounts'] = $accountsModel->getAccounts($filt, $postdata->sort, $postdata->pn, $postdata->ps);
        return $this->respond($data);
    }

    public function addAccount()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $accountsModel = new AccountsModel();
        $data = [
            'acnt_id' => $json->acnt_id,
            'acnt_name' => $json->acnt_name,
            'acnt_ag_id' => $json->acnt_ag_id,
            'acnt_client_id' => $json->acnt_client_id,
            'acnt_opbal' => $json->acnt_opbal,
            'acnt_clbal' => $json->acnt_clbal,
            'acnt_inactive' => $json->acnt_inactive,
            'acnt_isdefault' => $json->acnt_isdefault,
            'acnt_remarks' => $json->acnt_remarks,
            'acnt_modified' => $today->toDateTimeString()
        ];
        $accountsModel->addAccount($data);
        echo 'SUCCESS';
    }
    public function updateAccount()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $accountsModel = new AccountsModel;
        $data = [
            'acnt_id' => $json->acnt_id,
            'acnt_name' => $json->acnt_name,
            'acnt_ag_id' => $json->acnt_ag_id,
            'acnt_client_id' => $json->acnt_client_id,
            'acnt_opbal' => $json->acnt_opbal,
            'acnt_clbal' => $json->acnt_clbal,
            'acnt_inactive' => $json->acnt_inactive,
            'acnt_isdefault' => $json->acnt_isdefault,
            'acnt_remarks' => $json->acnt_remarks,
            'acnt_modified' => $today->toDateTimeString()
        ];
        $accountsModel->updateAccount($data);
        echo 'SUCCESS';
    }
    public function deleteAccount()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $accountsModel = new AccountsModel;
        $accountsModel->deleteAccount($json->acnt_id);
        echo 'SUCCESS';
    }
}
