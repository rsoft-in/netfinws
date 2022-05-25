<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Services extends BaseController
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

    public function getUsers()
    {
        $userModel = new UserModel;
        $data['users'] = $userModel->getUsers('', 'usr_name', 0, 30);
        return $this->respond($data);
    }

    public function checkUser()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $today = new Time('now');
        if ($postdata->username == 'super-admin' && $postdata->password == 'Elia1092') {
            $dataArray = array(
                'usr_id' => $postdata->username,
                'usr_client_id' => 'super-admin',
                'usr_name' => $postdata->username,
                'usr_pwd' => '',
                'usr_displayname' => 'Admin',
                'usr_level' => '5',
                'usr_remarks' => 'Administrator',
                'usr_modified' => $today->toDateTimeString(),
                'usr_inactive' => '0'
            );
            echo json_encode($dataArray);
        } else {
            $userModel = new UserModel;
            $users = $userModel->getUserByUsername($postdata->username);
            if (sizeof($users) > 0) {
                $user = $users[0];
                $dataArray = array(
                    'usr_id' => $user->usr_id,
                    'usr_client_id' => $user->usr_client_id,
                    'usr_name' => $user->usr_name,
                    'usr_pwd' => $user->usr_pwd,
                    'usr_displayname' => $user->usr_displayname,
                    'usr_level' => '0',
                    'usr_remarks' => $user->usr_remarks,
                    'usr_modified' => $today->toDateTimeString(),
                    'usr_inactive' => $user->usr_inactive
                );
                echo json_encode($dataArray);
            } else {
                echo 'FAILED: INCORRECT USERNAME';
            }
        }
    }
    public function updatePassword()
    {
        $post = $this->request->getPost('postdata');
        if (!isset($post)) {
            echo 'INVALID ACCESS';
            return;
        }
        $today = new Time('now');
        $userModel = new UserModel;
        $postdata = json_decode($post);
        $data = [
            'usr_name' => $postdata->usr_name,
            'usr_pwd' => $postdata->usr_pwd,
            'usr_modified' => $today->toDateTimeString()
        ];
        $userModel->updatePassword($data);
        echo 'SUCCESS';
    }
}
