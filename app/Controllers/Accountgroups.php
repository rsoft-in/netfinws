<?php

namespace App\Controllers;

use App\Models\AccountGroupsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class AccountGroups extends BaseController
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

    public function getAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $accountGroupsModel = new AccountGroupsModel();
        $filt = "";
        if (!empty($postdata->qry))
            $filt .= "AND (ag_name LIKE '%" . $postdata->qry . "%' OR ag_type LIKE '%" . $postdata->qry . "%')";
        $data['accountgroups'] = $accountGroupsModel->getAccountGrp($filt, $postdata->sort, $postdata->pn, $postdata->ps);
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
        $accountGroupsModel->addAccountGrp($data);
        echo 'SUCCESS';
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
        $accountGroupsModel->updateAccountGrp($data);
        echo 'SUCCESS';
    }
    public function deleteAccountGrp()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $accountGroupsModel = new AccountGroupsModel;
        $accountGroupsModel->deleteAccountGrp($json->ag_id);
        echo 'SUCCESS';
    }
}
