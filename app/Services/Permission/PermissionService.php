<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 10:42
 */

namespace App\Services\Permission;

use App\Model\Permission;
use App\Services\Service;
use Route;
use Redisx;

class PermissionService extends  Service
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
        $this->field = ['id','name','route','content','created_at','type'];


    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Permission(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }


    /**
     * @auth zhangyh 20170309
     * @desc 判断用户是否有访问的权限
     */
    public function checkPermission($uid='',$wid=0)
    {
        //判断该店铺是否有权限操作
        $weixinUser = new WeixinUserService();
        if (!$uid){
            $uid = session('userInfo')['id'];
        }
        if (!$wid){
            $wid = session('wid');
        }

        $res = $weixinUser->init()->model->where(['wid'=>$wid,'uid'=>$uid])->first();
        if (!$res){
            return false;
        }

        $uri = Route::current()->getUri();
        $permissionData = $this->init()->getList(false)[0]['data'];
        $tmp = true;
        foreach ($permissionData as $val)
        {
            if ($val['route'] == $uri){
                $tmp = false;
                break;
            }
        }
        if ($tmp){
            return true;
        }
        // 实时判断
       // return $this->checkShopPermission() && $this->checkUserPermission();
        $resultPermission = Array();
        $resultPermission = json_decode(Redisx::GET($this->getKey()),true);
        if ($resultPermission){
            return in_array($uri,$resultPermission);
        }else{
            return false;
        }

    }

    /**
     * @auth zhangyh 201703091051
     * @desc 判断店铺权限
     */
    public  function checkShopPermission()
    {
        $wid = session('wid');
        //获取当前店铺权限
        $weixinRole = new WeixinRoleService();
        $shopPermission = $weixinRole->getShopPermission();
        $uri = Route::current()->getUri();
        return in_array($uri,$shopPermission);
    }

    /**
     * @auth zhangyh 201703091051
     * @desc 判断用户权限
     */
    public function checkUserPermission()
    {
        $rolePermission = new RolePermissionService();
        $permissionData = $rolePermission->getUserPermission();
        $uri = Route::current()->getUri();
        return in_array($uri,$permissionData);
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703101132
     * @desc 添加用户权限到Redis
     */
    public function addPermissionToRedis($uid=0,$wid=0)
    {
        $weixinRole = new WeixinRoleService();
        $rolePermission = new RolePermissionService();
        $shopPermission = Array();
        $permissionData = Array();
        $shopPermission = $weixinRole->getShopPermission($wid);
        $permissionData = $rolePermission->getUserPermission($uid,$wid);
        $resultPermission = array_intersect($shopPermission,$permissionData);
        //权限数据写入redis
        Redisx::SET($this->getKey(),json_encode($resultPermission));
        $this->request->session()->put('permission', $resultPermission);
        /* 手动保存session */
        $this->request->session()->save();
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703101530
     * @desc 获取redis key
     * @return string
     */
    public function getKey()
    {
        return  'WeixinUser:Permission:'.session('userInfo')['id'].':'.session('wid');
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201354
     * @desc 获取权限列表
     * @param string $tag
     * @param $id
     */
    public function getPermission($tag='',$id)
    {
        $permissionData = $this->init()->where(['type'=>['in',[1,3]]])->order('type asc')->getList(false);
        $data = [];
        if ($tag == 'adrole') {
            $adrolePermissionService = new AdrolePermissionService();
            $data = $adrolePermissionService->getPermission($id);
        }elseif($tag='role'){
            $rolePermissionService = new RolePermissionService();
            $data = $rolePermissionService->getRolePermission($id);
        }

        foreach ($permissionData[0]['data'] as &$val)
        {
            if (in_array($val['route'],$data)){
                $val['is_in'] = 1;
            }else{
                $val['is_in'] = 0;
            }
        }
        return $permissionData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180509
     * @desc 获取店铺权限
     */
    public function getSpecialPermission()
    {
        $weixinRole = new WeixinRoleService();
        $rolePermission = new RolePermissionService();
        $shopPermission = $weixinRole->getShopPermission();
        $permissionData = $rolePermission->getUserPermission();
        $resultPermission = array_intersect($shopPermission,$permissionData);

        return array_values($resultPermission);
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180509
     * @desc 获取没有权限的提示
     * @update 张永辉 2018年7月2日 根据请不同返回不同的数据
     */
    public function getNoPermissionInfo()
    {
        if ( app('request')->expectsJson() ) {
            error('您暂时没有该权限，请联系客服：0571-87796692 订购我们的产品开放此功能哦~');
        } else {
            $str = <<<EOF
         <script type="text/javascript">
            alert('您暂时没有该权限，请联系客服：0571-87796692 订购我们的产品开放此功能哦~');
            window.history.go(-1);
         </script>
EOF;
            return $str;
        }
    }

}














