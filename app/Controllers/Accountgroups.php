<?php

namespace App\Controllers;

use App\Models\AccountGroupsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;


class AccountGroups extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->failUnauthorized();
    }

    public function getAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountGroupsModel = new AccountGroupsModel();
        $filt = "";
        if (!empty($postdata->qry))
            $filt .= "AND (ag_name LIKE '%" . $postdata->qry . "%' OR ag_type LIKE '%" . $postdata->qry . "%')";
        $builder = $accountGroupsModel->builder()->select('*')
            ->where('(1=1) ' . $filt);
        $data['accountgroups'] = $builder
            ->orderBy($postdata->sort)
            ->limit($postdata->ps, $postdata->pn * $postdata->ps)->get()->getResult();
        $data['records'] = $builder->countAllResults();

        return $this->respond($data);
    }

    public function addAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $accountGroupsModel = new AccountGroupsModel();
        $data = [
            'ag_id' => $json->ag_id,
            'ag_name' => $json->ag_name,
            'ag_type' => $json->ag_type,
            'ag_isdefault' => $json->ag_isdefault,
            'ag_client_id' => $json->ag_client_id,
            'ag_modified' => $today->toDateTimeString()
        ];
        $accountGroupsModel->builder()->insert($data);
        if ($accountGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountGroupsModel->db->error());
    }

    public function updateAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $accountGroupsModel = new AccountGroupsModel;
        $data = [
            'ag_id' => $json->ag_id,
            'ag_name' => $json->ag_name,
            'ag_type' => $json->ag_type,
            'ag_isdefault' => $json->ag_isdefault,
            'ag_client_id' => $json->ag_client_id,
            'ag_modified' => $today->toDateTimeString()
        ];
        $accountGroupsModel->builder()->where('ag_id', $json->ag_id)->update($data);
        if ($accountGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountGroupsModel->db->error());
    }

    public function deleteAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $accountGroupsModel = new AccountGroupsModel;
        $accountGroupsModel->builder()->where('ag_id', $json->ag_id)->delete();
        if ($accountGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($accountGroupsModel->db->error());
    }
}
