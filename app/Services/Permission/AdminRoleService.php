<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/9
 * Time: 16:05
 */

namespace App\Services\Permission;


use App\Model\AdminRole;
use App\Services\Service;

class AdminRoleService extends Service
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
        $this->field = ['id','name','content','created_at','updated_at'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new AdminRole(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201159
     * @desc 获取角色权限
     * @param $id
     */
    public function getRolePermission($id)
    {
        $result = [
            'success'   => 0,
            'message'   => '',
        ];
        $adminRoleData = $this->init()->getInfo($id);
        if (!$adminRoleData){
            $result['message']  = '角色不存在';
        }
        $result['data'][0] = $adminRoleData;
        $adrolePermissionService = new AdrolePermissionService();
        $result['data'][1] = $adrolePermissionService->init()->where(['adrole_id'=>$id])->getList(false);
        $result['success'] = 1;
        return $result;

    }








}









