<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 10:23
 */

namespace App\Services\Permission;


use App\Model\WeixinUser;
use App\Services\Service;
use App\Services\UserService;
use App\Services\Wechat\ApiService;

class WeixinUserService extends Service
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
        $this->field = ['id','wid','uid','oper_id','role_id','created_at','open_id','nick_name','hexiao_mid'];
        //关联关系
        $this->with(['user','weixin']);

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new WeixinUser(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }


    /**
     * @auth zhangyh
     * @desc 店铺管理添加管理员
     * @param $userId
     */
    function addAdmin($userId,$roleId)
    {
        $wid = $this->request->session()->get('wid');
        $operId = $this->request->session()->get('userInfo')['id'];
        //判断是否存在避免重复数据
        $res = $this->init()->model->where(['wid' => $wid,'uid'=>$userId])->first();
        if ($res){
            error('该用户已有存在管理员列表中');
        }
        $weixinUserData = Array(
            'wid'       => $wid,
            'uid'       => $userId,
            'oper_id'   => $operId,
            'role_id'   => $roleId,
        );
        $this->init()->add($weixinUserData);
    }


    /**
     * @auth zhangyh 2017030811603
     * @desc 根据店铺ID获取管理员信息
     * @param $wid
     */
    function getManager($wid, $pageSize=15)
    {
        array_push($this->with,'oper');
        array_push($this->with,'role');
        array_push($this->with,'member');
       $data = $this->init()->where(['wid'=>$wid])->perPage($pageSize)->getList();
       return $data;
    }

    /**
     * @auth zhangyh 20170309
     * @desc 修改用户角色
     * @param $roleId
     * @param $id 对应weixin_user 的ID
     */
    public function modifyRole($roleId,$id)
    {
        //获取当前用户和店铺
        $userId = session('userInfo')['id'];
        $weixinUserData = $this->init()->getInfo($id);
        if (!$weixinUserData){
            error( 'ID不存在');
        }
        if ($userId == $weixinUserData['uid']){
            error('不能修改自己的角色');
        }
        $roleService = new RoleService();
        $roleData = $roleService->init()->getInfo($roleId);
        if (!$roleId){
            error('选择的角色不存在');
        }
        $this->init()->where(['id'=>$id])->update(['role_id'=>$roleId]);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170807
     * @desc 获取店铺管理员
     * @param $wid
     */
    public function getUser($wid)
    {
        $mangerData = $this->init()->model->where('wid',$wid)->get()->toArray();
        $userIds = [];
        $headerId = '';
        foreach ($mangerData as &$val){
            $userIds[] = $val['uid'];
            if ($val['uid'] == $val['oper_id']){
                $val['is_header'] = 1;
                $headerId = $val['uid'];
            }else{
                $val['is_header'] = 0;
            }
        }
        $userService = new UserService();
        $userData = $userService->init()->whereIn('id',$userIds)->get(['id','name','head_pic'])->toArray();
        foreach ($userData as &$item){
            $item['head_pic'] = $item['head_pic'] ? $item['head_pic'] : 'hsshop/image/static/m1logo.png';
            $item['head_pic'] = config('app.source_img_url').$item['head_pic'];
            if ($item['id'] == $headerId){
                $item['is_header'] = 1;
            }else{
                $item['is_header'] = 1;
            }
        }
        return $userData;
    }

    /**
     * 绑定微信接收消息
     */
    public function bindAdmin($wid,$openId,$uid){
        $data = [
            'code'=> 0,
            'msg' => '绑定成功'
        ];
        $re = $this->init()->model->where(['uid'=>$uid,'wid'=> $wid,'open_id'=>null])->first();
        if(!$re){
            return $data = [
                'code'=> 1,
                'msg' => '该管理员已绑定微信，请先解绑'
            ];
        }
        //判断是否已经绑定
        $re = $this->init()->model->where(['open_id'=>$openId,'wid'=> $wid])->first();
        if($re){
            return $data = [
                'code'=> 2,
                'msg' => '该微信号已绑定了本店铺管理员，请先解绑'
            ];
        }
        $res = $this->init()->model->where(['uid'=>$uid,'wid'=> $wid])->first();
        if($res){
            //获取用户信息
            $apiService = new ApiService();
            $userInfo =  $apiService->getUserInfo($wid,$openId);
            if(isset($userInfo['errcode'])){
                return $data = [
                    'code'=> 3,
                    'msg' => '获取用户信息失败'
                ];
            }
            $input['open_id'] = $openId;
            $input['nick_name'] = $userInfo['nickname'];
            $re =$this->init()->model->where(['uid'=>$uid,'wid'=> $wid])->update($input);
            if(!$re){
                $data = [
                    'code'=> 4,
                    'msg' => '绑定失败'
                ];
            }
        }else{
            $data = [
                'code'=> 5,
                'msg' => '请先将该用户添加到店铺管理员列表'
            ];
        }
        return $data;
    }

    public function isBindWeixin($wid,$hexiao_mid)
    {
        $res = $this->init()->model->where(['wid' => $wid,'hexiao_mid' => $hexiao_mid])->first();
        if ($res) {
            return true;
        }
        return false;
    }

    /**
     * @description：小程序核销验证有区别与微商城的验证
     * @param $wid   店铺id
     * @param $mids  用户id数组（因为同一店铺微商城跟小程序授权的mid可能会不同，这里根据nickname获取用户id数组进行验证）
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月24日 14:49:52
     */
    public function isBindFromXcx($wid, $mids)
    {
        $res = $this->init()->model->where(['wid' => $wid])->whereIn('hexiao_mid', $mids)->pluck('id')->all();
        if ($res) {
            return true;
        }
        return false;
    }

}
