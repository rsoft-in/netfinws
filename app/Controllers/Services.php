<?php

namespace App\Controllers;

use App\Models\UserModel;

class Services extends BaseController {

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    }

    public function index() {
        return view('unauthorized_access');
    }

    public function checkUser()
    {
        $userModel = new UserModel;
        $res = $userModel->getUsers('', 'usr_name', 0, 30);
    }
}