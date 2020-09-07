<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 15:40
 */

namespace App\Services\Permission;


use App\Model\Role;
use App\Services\Service;

class RoleService extends Service
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
        $this->field = ['id','name','content','status','created_at'];


    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Role(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }
}