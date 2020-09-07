<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/8/30
 * Time: 10:34
 */

namespace App\S\Log;


use App\S\S;

class BatchDeliveryLogService extends S
{

    public function __construct($modelName = null)
    {
        parent::__construct('BatchDeliveryLog');
    }


    public function create($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getListPage($where = [])
    {
        return $this->model->where($where)->orderBy('created_at','DESC')->paginate(15);
    }


}