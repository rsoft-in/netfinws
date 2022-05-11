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

    public function addClient()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $clientsModel = new ClientsModel;
        $data = [
            'client_id' => $json->client_id,
            'client_name' => $json->client_name,
            'client_address' => $json->client_address,
            'client_phone' => $json->client_phone,
            'client_mobile' => $json->client_mobile,
            'client_email' => $json->client_email,
            'client_meta' => $json->client_meta,
            'client_inactive' => $json->client_inactive,
            'client_modified' => $today->toDateTimeString()
        ];
        $clientsModel->addClient($data);
        echo 'SUCCESS';
    }
    public function updateClient()
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $today = new Time('now');
        $clientsModel = new ClientsModel;
        $data = [
            'client_id' => $json->client_id,
            'client_name' => $json->client_name,
            'client_address' => $json->client_address,
            'client_phone' => $json->client_phone,
            'client_mobile' => $json->client_mobile,
            'client_email' => $json->client_email,
            'client_meta' => $json->client_meta,
            'client_inactive' => $json->client_inactive,
            'client_modified' => $today->toDateTimeString()
        ];
        $clientsModel->updateClient($data);
        echo 'SUCCESS';
    }
    public function delete($id = null)
    {
        $post = $this->request->getPost('postdata');
        $json = json_decode($post);
        $clientsModel = new ClientsModel;
        $clientsModel->deleteClient($json->client_id);
        echo 'SUCCESS';
    }
}
