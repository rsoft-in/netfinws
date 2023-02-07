<?php

namespace App\Controllers;

use App\Models\AccountsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class Accounts extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('unauthorized_access');
    }

    public function getAccounts()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountsModel = new AccountsModel();

        $builder = $accountsModel->builder()->select('*');
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
