<?php

namespace App\Controllers;

use App\Libraries\Utility;
use App\Models\AccountsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class Accounts extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->failUnauthorized();
    }

    public function getAccounts()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountsModel = new AccountsModel();

        if (!isset($postdata->cid))
            return $this->failUnauthorized();

        $builder = $accountsModel->builder()->select('*');
        $builder->where('acnt_client_id', $postdata->cid);
        if (!empty($postdata->qry)) {
            $builder->like('acnt_name', $postdata->qry);
        }
        $data['accounts'] = $builder
            ->orderBy($postdata->sort)
            ->limit($postdata->pn, $postdata->ps)
            ->get()->getResult();
        $data['records'] = $builder->countAllResults();
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
        $account_id = "";
        $today = new Time('now');

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
            $accountsModel->builder()->insert($data);
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
            return $this->failUnauthorized();
        }
        $filt .= " AND acnt_book_type IN ('CH', 'BK')";

        $data['accounts'] = $accountsModel->builder()->select()
            ->where('(1=1) ' . $filt)
            ->orderBy($postdata->sort)
            ->get()->getResult();
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
        $accountsModel->builder()->insert($data);
        if ($accountsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountsModel->db->error());
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
        $accountsModel->builder()->where('acnt_id', $json->acnt_id)->update($data);
        if ($accountsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountsModel->db->error());
    }

    public function deleteAccount()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $accountsModel = new AccountsModel;
        $accountsModel->builder()->where('acnt_id', $json->acnt_id)->delete();
        if ($accountsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountsModel->db->error());
    }
}
