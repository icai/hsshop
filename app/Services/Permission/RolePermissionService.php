<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 10:30
 */

namespace App\Services\Permission;


use App\Model\RolePermission;
use App\Services\Service;
use StaffOperLogService;

class RolePermissionService extends Service
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
        $this->field = ['id','role_id','permission_id','created_at','updated_at'];
        $this->with(['Permission']);


    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new RolePermission(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh 201703081034
     * @desc 获取用户权限
     * @param $wuId
     */
    public function getUserPermission($uid=0,$wid=0)
    {
        $result = Array();
        if (!$uid){
            $uid = session('userInfo')['id'];
        }
        if (!$wid){
            $wid = session('wid');
        }

        //获取用户角色
        $weixinUserService = new WeixinUserService();
        $weixinUserData = $weixinUserService->init()->model->where(['wid'=>$wid,'uid'=>$uid])->get()->toArray();
        if (!$weixinUserData){
            return $result;
        }
        //获取用户权限
        $where = Array();
        $where['role_id'] = $weixinUserData[0]['role_id'];
        $rolePermissionData = $this->init()->where($where)->getList(false);
        foreach ($rolePermissionData[0]['data'] as $val)
        {
            $result[] = $val['Permission']['route'];
        }
        return $result;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703100916
     * @desc 绑定前台角色和权限
     * @param $roleId
     * @param $permissionIds
     */
    public  function  bindRolePermission($roleId,$permissionIds)
    {
        //如果该角色已绑定权限则删除
       $this->init()->model->where('role_id',$roleId)->delete();
        //添加绑定信息
        $data = [];
        foreach ($permissionIds as $val)
        {
            $tmpData['permission_id'] = $val;
            $tmpData['role_id'] = $roleId;
            $data[] = $tmpData;
        }
        $this->init()->model->insert($data);
        StaffOperLogService::write('绑定前台角色权限关系,roleId'.$roleId.json_encode($permissionIds));
        success();
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201405
     * @desc 获取前台角色权限
     * @param $id
     */
    public function getRolePermission($id)
    {
        $rolePermissionData = $this->init()->where(['role_id'=>$id])->getList(false)[0]['data'];
        $result = [];
        foreach ($rolePermissionData as $val){
            $result[] = $val['Permission']['route'];
        }
        return $result;
    }













}











