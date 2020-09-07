<?php

namespace App\S\Product;

use App\Lib\Redis\ProductCategory as PCRedis;
use App\S\S;

class CategoryService extends S
{
    public function __construct()
    {
        parent::__construct('ProductCategory');
    }
    
    public function getAll()
    {
        $redis = new PCRedis();
        $categoryList = $redis->get(); 
        if (empty($categoryList)) {
            $categoryList = $this->model->get()->toArray();
            $redis->set($categoryList);
        }
        return $categoryList;
    }

    public function add($data)
    {
        $id = $this->model->insertGetId($data);
        $redis = new PCRedis();
        $data['id'] = $id;
        //$data['parent_id'] = 0;
        $data['status'] = 0;
        $redis->addOne($data);
        return $id;
    }

    public function update($id, $data)
    {
        $condtion =  $id;
        $category = $this->model->where($condtion)->first();
        if (empty($category)) {
            return false;
        }
        $category->update($data);
        $redis = new PCRedis();
        $categoryArr = $category->toArray();
        $data['parent_id'] = $categoryArr['parent_id'];
        $data['status']    = $categoryArr['status'];
        $redis->updateOne($id['id'], $data);
        return true; 
    }

    public function delete($id)
    {
        $condtion['id'] = $id;
        $this->model->where($condtion)->delete();
        $redis = new PCRedis();
        $redis->deleteOne($id);
        return true; 
    }
}