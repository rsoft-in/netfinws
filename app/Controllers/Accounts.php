<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use App\Libraries\Utility;
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

        if (isset($postdata->cid))
            $filt .= " AND (acnt_client_id = '" . $postdata->cid . "')";
        else
            $filt .= " AND (acnt_isdefault = 1)";

        if (!empty($postdata->qry))
            $filt .= "AND (acnt_name LIKE '%" . $postdata->qry . "%' OR acnt_opbal LIKE '%" . $postdata->qry . "%')";
        $data['accounts'] = $accountsModel->getAccounts($filt, $postdata->sort, $postdata->ps, $postdata->pn * $postdata->ps);
        $data['records'] = $accountsModel->getAccountByCount($filt);
        return $this->respond($data);
    }

    public function getAccountsCashBank()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountsModel = new AccountsModel();
        $filt = "";

        // CREATE DEFAULT ACCOUNTS
        $utility = new Utility();
        // $account_name = "";
        $account_id = "";
        // $group_id = "";
        $today = new Time('now');
        // switch ($postdata->book) {
        //     case 'CH':
        //         $account_name = "Cash Account";
        //         $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
        //         break;
        //     case 'BK':
        //         $account_name = "Bank Account";
        //         $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
        //         break;
        //     case 'SA':
        //         $account_name = "Sales Account";
        //         $group_id = "C1EF16E9-F3DA-69A0-9C29-E23A5E1A96A8";
        //         break;
        //     case 'PU':
        //         $account_name = "Purchase Account";
        //         $group_id = "8FB61DF9-9086-5DBD-BD33-3F3A4068829C";
        //         break;
        //     default:
        //         $account_name = "Cash Account";
        //         $group_id = "E4C13199-7492-0EFE-BC5C-41B82047623E";
        //         break;
        // }

        $accounts = $accountsModel->getAccountByName($postdata->cid, 'Cash Account');
        if (sizeof($accounts) == 0) {
            $account_id = $utility->guid();
            $data = [
                'acnt_id' => $account_id,
                'acnt_name' => 'Cash Account',
                'acnt_ag_id' => 'E4C13199-7492-0EFE-BC5C-41B82047623E',
                'acnt_client_id' => $postdata->cid,
                'acnt_opbal' => 0,
                'acnt_clbal' => 0,
                'acnt_inactive' => 0,
                'acnt_isdefault' => 1,
                'acnt_book_type' => 'CH',
                'acnt_remarks' => 'Default',
                'acnt_modified' => $today->toDateTimeString()
            ];
            $accountsModel->addAccount($data);
        }
        $accounts = $accountsModel->getAccountByName($postdata->cid, 'Bank Account');
        if (sizeof($accounts) == 0) {
            $account_id = $utility->guid();
            $data = [
                'acnt_id' => $account_id,
                'acnt_name' => 'Bank Account',
                'acnt_ag_id' => 'E4C13199-7492-0EFE-BC5C-41B82047623E',
                'acnt_client_id' => $postdata->cid,
                'acnt_opbal' => 0,
                'acnt_clbal' => 0,
                'acnt_inactive' => 0,
                'acnt_isdefault' => 1,
                'acnt_book_type' => 'BK',
                'acnt_remarks' => 'Default',
                'acnt_modified' => $today->toDateTimeString()
            ];
            $accountsModel->addAccount($data);
        }
        // END OF DEFAULT ACCOUNTS

        if (isset($postdata->cid))
            $filt .= " AND (acnt_client_id = '" . $postdata->cid . "')";
        else {
            echo 'INVALID REQUEST';
            return;
        }
        $filt .= " AND acnt_book_type IN ('CH', 'BK')";

        $data['accounts'] = $accountsModel->getAccounts($filt, $postdata->sort, $postdata->ps, $postdata->pn * $postdata->ps);
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
            'acnt_book_type' => $json->acnt_book_type,
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
            'acnt_book_type' => $json->acnt_book_type,
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
