<?php

namespace App\Controllers;

use App\Models\ProductGroupsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class ProductGroups extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return $this->failUnauthorized();
    }

    public function get()
    {
        $post = json_decode($this->request->getPost('postdata'));
        $productGroupsModel = new ProductGroupsModel();
        if ($post == null) {
            return $this->failUnauthorized();
        }
        $builder = $productGroupsModel->builder()->select();
        $builder->where('pg_client_id', $postdata->cid);
        if (!empty($post->qry)) {
            $builder->like('pg_code', $post->qry);
            $builder->orLike('pg_name', $post->qry);
        }
        $data['product_groups'] = $builder->orderBy($post->sort)
            ->limit($post->ps, $post->pn * $post->ps)
            ->get()->getResult();
        $data['records'] = $builder->countAllResults();
        return $this->respond($data);
    }

    public function add()
    {
        $post = json_decode($this->request->getPost('postdata'));
        if ($post == null) {
            return $this->failUnauthorized();
        }
        $today = new Time('now');
        $data = [
            'pg_code' => $post->pg_code,
            'pg_client_id' => $post->pg_client_id,
            'pg_name' => $post->pg_name,
            'pg_attributes' => $post->pg_attributes,
            'pg_parent_code' => $post->pg_parent_code,
            'pg_modified' => $today->toDateTimeString()
        ];
        $productGroupsModel = new ProductGroupsModel();
        $productGroupsModel->builder()->insert($data);
        if ($productGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($productGroupsModel->db->error());
    }

    public function update()
    {
        $post = json_decode($this->request->getPost('postdata'));
        if ($post == null) {
            return $this->failUnauthorized();
        }
        $today = new Time('now');
        $data = [
            'pg_code' => $post->pg_code,
            'pg_client_id' => $post->pg_client_id,
            'pg_name' => $post->pg_name,
            'pg_attributes' => $post->pg_attributes,
            'pg_parent_code' => $post->pg_parent_code,
            'pg_modified' => $today->toDateTimeString()
        ];
        $productGroupsModel = new ProductGroupsModel();
        $productGroupsModel->builder()->where('pg_code', $post->pg_code)->update($data);
        if ($productGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($productGroupsModel->db->error());
    }

    public function delete()
    {
        $post = json_decode($this->request->getPost('postdata'));
        $productGroupsModel = new ProductGroupsModel();
        if ($post == null) {
            return $this->failUnauthorized();
        }

        if ($productGroupsModel->db->affectedRows() > 0)
            return $this->respond('SUCCESS');
        else
            return $this->respond($productGroupsModel->db->error());
    }
}
