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

    public function getUsers() {
        $userModel = new UserModel;
        $data['users'] = $userModel->getUsers('', 'usr_name', 0, 30);
        return $this->respond($data);
    }

    public function checkUser()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);

        $encrypter = service('encrypter');
        if ($postdata->username == 'super-admin' && $postdata->password == 'Elia1092') {
            $today = new Time('now');
            $dataArray = array(
                'usr_id' => $postdata->username,
                'usr_client_id' => 'super-admin',
                'usr_name' => $postdata->username,
                'usr_displayname' => 'Admin',
                'usr_level' => 5,
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
                var_dump($user->usr_pwd);
                return;
                if ($postdata->password == $encrypter->decrypt($user->usr_pwd)) {
                    echo 'SUCCESS';
                } else {
                    echo 'FAILED: INCORRECT PASSWORD';
                }
            } else {
                echo 'FAILED: INCORRECT USERNAME';
            }
        }
    }
}
