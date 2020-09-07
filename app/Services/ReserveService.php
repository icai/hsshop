<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/4/11
 * Time: 11:25
 */

namespace App\Services;


use App\Model\Reserve;

class ReserveService extends Service
{
    /**
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id','name','phone','industry','status','link_source','source','type','created_at', 'action', 'liteapp_title', 'is_register','position','enterprise_name'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Reserve(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }
}