<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/9
 * Time: 14:58
 */

namespace App\Services\Permission;


use App\Model\AdrolePermission;
use App\Model\Member;
use App\Model\Permission;
use App\Services\Service;
use StaffOperLogService;

class AdrolePermissionService extends  Service
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
        $this->field = ['id','adrole_id','permission_id','created_at'];
        /*关联关系*/
        $this->with(['permission']);

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new AdrolePermission(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh 201703091541
     * @desc 根据用户角色获取用户权限
     * @param $adroleId
     */
    public function  getPermission($adroleId)
    {
        $where = Array();
        $where['adrole_id'] = $adroleId;
        $permissionData = $this->init()->where($where)->getList(false);
        $result = Array();
        foreach ($permissionData[0]['data'] as $val)
        {
            $result[] = $val['permission']['route'];
        }
        return $result;
    }


    /**
     * @auth zhangyh 20170309
     * @desc 角色绑定权限
     * @param $adroleId
     * @param $permissionData
     */
    public function bindPermission($adroleId,$permissionData)
    {
        //删除角色已经绑定的权限
        $this->init()->model->where('adrole_id',$adroleId)->delete();

        //添加绑定信息
        $data = [];
        foreach ($permissionData as $val) {
            $tmpData['adrole_id'] = $adroleId;
            $tmpData['permission_id'] = $val;
            $data[] = $tmpData;
        }
        $this->init()->model->insert($data);
        StaffOperLogService::write('绑定角色权限,adroleid'.$adroleId.json_encode($permissionData));
        success();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180608
     * @desc 判断店铺角色和权限问题
     * @param $data
     * @param $roleData
     * @update 吴晓平 2019年11月04日 11:25:06 修改权限判断（10， 11， 12， 13）
     */
    public function judgeShopPermission(&$data,$roleData)
    {

        if (!$roleData || strtotime($roleData[0]['start_time'])>time() || strtotime($roleData[0]['end_time']) < time()){
            $data['minapp_status'] = $data['wx_status'] = 0;
            return ;
        }
        $roleData = current($roleData);
        switch ($roleData['admin_role_id']){
            case  '1' :
                $data['minapp_status'] = 0;
                $data['wx_status'] = 1;
                break;
            case '2' :
                $data['minapp_status'] = 0;
                $data['wx_status'] = 1;
                break;
            case '3' :
                $data['minapp_status'] = 1;
                $data['wx_status'] = 1;
                break;
            case '4' :
                $data['minapp_status'] = 1;
                $data['wx_status'] = 1;
                break;
            case '5' :
                $data['minapp_status'] = 1;
                $data['wx_status'] = 1;
                break;
            case '6' :
                $data['minapp_status'] = 1;
                $data['wx_status'] = 0;
                break;
            case '7' :
                $data['minapp_status'] = 1;
                $data['wx_status'] = 0;
                break;
            case '8' :
                $data['minapp_status'] = 0;
                $data['wx_status'] = 1;
                break;
            case '9' :
            case '11':
            case '12':
            case '13':
                $data['minapp_status'] = 1;
                $data['wx_status'] = 1;
                break;
            case '10' :
                $data['minapp_status'] = 0;
                $data['wx_status'] = 1;
                break;
            default:
                $data['minapp_status'] = 0;
                $data['wx_status'] = 0;
                break;
        }

        if ($data['minapp_status'] == 1 && $data['wxxcxConfig']['data']){
            $data['minapp_status'] = 2;
        }
        if ($data['wx_status'] == 1 && $data['weixinConfigSub'] != 'null'  && !empty($data['weixinConfigSub']) ){
            $data['wx_status'] = 2;
        }

    }


}















