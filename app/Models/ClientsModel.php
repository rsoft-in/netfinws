<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientsModel extends Model
{

    protected $table = 'clients';
    protected $primaryKey = 'client_id';

    public function getClients($filter, $sortBy, $pageNo, $pageSize)
    {
        $result = $this->builder()->select('*')
            ->where('(1=1) ' . $filter)
            ->orderBy($sortBy)
            ->limit($pageNo, $pageSize)
            ->get()->getResult();
        return $result;
    }
    public function addClient($data)
    {
        $this->builder()->insert($data);
    }
    public function updateClient($data)
    {
        $this->builder()->where('client_id', $data['client_id'])->update($data);
    }
    public function deleteClient($client_id)
    {
        $this->builder()->where('client_id', $client_id)->delete();
    }
}
