<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\Services\Staff;
use App\Model\Recommend;
use App\Services\Service;

class RecommendService extends Service
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id','name','content','uri','status','created_at'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Recommend(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

}























