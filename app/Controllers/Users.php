<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->failUnauthorized();
    }

    public function getUsers()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $userModel = new UserModel();

        $filt = "";
        if (empty($postdata->cid))
            return;
        else
            $filt .= " AND (usr_client_id = '" . $postdata->cid . "')";


        if (!empty($postdata->qry))
            $filt .= " AND (usr_name LIKE '%" . $postdata->qry . "%' OR usr_displayname LIKE '%" . $postdata->qry . "%' )";

        $data['users'] = $userModel->getUsers($filt, $postdata->sort, $postdata->pn, $postdata->ps);
        $data['records'] = $userModel->builder()->where('(1=1) ' . $filt)->countAllResults();
        return $this->respond($data);
    }

    public function addUser()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $userModel = new UserModel();
        $data = [
            'usr_id' => $json->usr_id,
            'usr_client_id' => $json->usr_client_id,
            'usr_name' => $json->usr_name,
            'usr_pwd' => $json->usr_pwd,
            'usr_displayname' => $json->usr_displayname,
            'usr_level' => $json->usr_level,
            'usr_remarks' => $json->usr_remarks,
            'usr_inactive' => $json->usr_inactive,
            'usr_modified' => $today->toDateTimeString()
        ];
        $userModel->addUser($data);
        echo 'SUCCESS';
    }
    public function updateUser()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $userModel = new UserModel;
        $data = [
            'usr_id' => $json->usr_id,
            'usr_client_id' => $json->usr_client_id,
            'usr_name' => $json->usr_name,
            'usr_pwd' => $json->usr_pwd,
            'usr_displayname' => $json->usr_displayname,
            'usr_level' => $json->usr_level,
            'usr_remarks' => $json->usr_remarks,
            'usr_inactive' => $json->usr_inactive,
            'usr_modified' => $today->toDateTimeString()
        ];
        $userModel->updateUser($data);
        echo 'SUCCESS';
    }
    public function deleteUser()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $userModel = new UserModel;
        $userModel->deleteUser($json->usr_id);
        echo 'SUCCESS';
    }
}
