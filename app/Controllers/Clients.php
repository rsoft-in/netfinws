<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class Clients extends BaseController
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

    public function getClients()
    {
        $post = $this->request->getPost('postdata');
        $postdata = json_decode($post);
        $clientsModel = new ClientsModel;
        $filt = "";
        if (!empty($postdata->qry))
            $filt .= "AND (client_name LIKE '%" . $postdata->qry . "%' OR client_address LIKE '%" . $postdata->qry . "%' OR client_email LIKE '%" . $postdata->qry . "%' OR client_mobile LIKE '%" . $postdata->qry . "%')";

        $data['clients'] = $clientsModel->getClients($filt, $postdata->sort, $postdata->pn, $postdata->ps);
        return $this->respond($data);
    }
}
