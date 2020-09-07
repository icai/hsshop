<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/9
 * Time: 14:58
 */

namespace App\Services\Permission;


use App\Model\WeixinRole;
use App\Services\Service;
use App\Services\WeixinService;
use StaffOperLogService;
use App\S\Weixin\ShopService;

class WeixinRoleService extends  Service
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
        $this->field = ['id','wid','admin_role_id','start_time','end_time','created_at'];


    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new WeixinRole(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh 20170309
     * @desc 获取店铺权限
     * @return array
     */
    function getShopPermission($wid=0)
    {
        $result = Array();
        if ($wid == 0){
            $wid = session('wid');
        }
        //获取用户角色
        $where = Array();
        $now = date("Y-m-d H:i:s");
        $where['wid'] = $wid;
        $where['start_time'] = ['<=',$now];
        $where['end_time'] = ['>=',$now];
        $roleData = $this->init()->where($where)->getList(false);
        if (!$roleData[0]['data']){
            return $result;
        }
        $adroleId = $roleData[0]['data'][0]['admin_role_id'];
        $this->request->session()->put('role_id', $adroleId);
        $this->request->session()->save();
        //获取角色权限
        $adrolePermission = new AdrolePermissionService();
        return $adrolePermission->getPermission($adroleId);

    }

    /**
     *
     * @auth zhangyh 201703092035
     * @desc 绑定店铺角色
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function bindWeixinRole()
    {
        $wid = $this->request->input('wid');
        $adminRoleId = $this->request->input('adminRoleId');
        $startTime = $this->request->input('startTime');
        $endTime = $this->request->input('endTime');
        //判断店铺是否存在
        /*$weixinService = new WeixinService();
        $weixinData = $weixinService->init()->getInfo($wid);*/
        $shopService = new ShopService();
        $weixinData = $shopService->getRowById($wid);
        if (!$weixinData){
            error('该店铺不存在');
        }
        $where = Array();
        $now = date('Y-m-d H:i:s');
        $where['wid'] = $wid;
        $where['start_time'] = ['<=',$now];
        $where['end_time'] = ['>=',$now];
        $list =$this->init()->where($where)->getList();
        $weixinRoleData=$list[0]['data'];
	   if ($weixinRoleData){
            $res = $this->init()->where(['id'=>$weixinRoleData[0]['id']])->delete($weixinRoleData[0]['id'],false);
            if (!$res){
                error();
            }
        }

        $id = $this->init()->add(Array(
            'wid'                   => $wid,
            'admin_role_id'        => $adminRoleId,
            'start_time'           => $startTime,
            'end_time'             => $endTime
        ),false);
        if ($id){
            StaffOperLogService::write('绑定店铺角色，id='.$id);
            success();
        }else{
            error();
        }

    }

    /**
     * todo 查询店铺是否过期
     * @param int $wid
     * @return array
     * @author jonzhang
     * @date 2018-01-02
     */
    public  function isExpire($wid=0)
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>1];
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为空';
            return $returnData;
        }
        $result=(new WeixinRole())->where(['wid'=>$wid])->first();
        if(!$result)
        {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '查询数据出现错误';
            return $returnData;
        }
        $result=$result->toArray();

        if (!in_array($result['admin_role_id'], [3, 4, 5, 6, 7, 9, 11, 12, 13])) {
            return $returnData;
        }

        if(!empty($result)&&isset($result['end_time']))
        {
            if(strtotime($result['end_time'])>time())
            {
                $returnData['data']=0;
            }
        }
        return  $returnData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180307
     * @desc 如果店铺已存在角色则，修改角色为6，更新角色时间范围，不存在则创建新角色
     * @param $wid
     */
    public function setRoleTime($wid)
    {
        $roleData = $this->init()->model->where('wid',$wid)->get()->toArray();
        if ($roleData){
            $roleData = current($roleData);
            $weixinRoleData = [
                'admin_role_id'     => 6,
                'start_time'        => date("Y-m-d H:i:s",time()),
                'end_time'          => date('Y-m-d H:i:s',strtotime('+1 year')),
            ];
            $res = $this->init()->where(['id'=>$roleData['id']])->update($weixinRoleData,false);
            if ($res){
                return true;
            }else{
                return false;
            }
        }else{
            $weixinRoleData =[
                'wid'               => $wid,
                'admin_role_id'    => 6,
                'end_time'          => date('Y-m-d H:i:s',strtotime('+1 year'))
            ];
            $this->init()->add($weixinRoleData,false)?$result=true:$result=false;
            return $result;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180529
     * @desc 获取店铺角色
     */
    public function getShopRole($wid)
    {
        $result = ['errCode'=>-100,'errMsg'=>''];
        $data = $this->init()->model->find($wid);
        if (!$data){
            $result['errMsg'] = '该店铺不存在';
            return $result;
        }
        $data = $data->toArray();
        $now = time();
        if (strtotime($data['start_time'])>$now){
            $data['status'] = 2;
        }elseif (strtotime($data['end_time'])<$now){
            $data['status'] = 3;
        }else{
            $data['status'] = 1;
        }

        $roleData = (new AdminRoleService())->init()->model->find($data['admin_role_id']);
        if (!$roleData){
            $result['errMsg'] = '店铺权限不存在';
            return $result;
        }
        $roleData = $roleData->toArray();
        $data['role_name'] = $roleData['name'];
        $data['role_content'] = $roleData['content'];
        $result['errCode'] = 0;
        $result['data'] = $data;
        return $result;
    }

    /***
     * todo 更改店铺过期信息
     * @param int $id
     * @param array $data
     * @return array
     * @author 张国军 2018年07月13日
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $updateReturnValue=$this->init()->wheres(['id'=>$id])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }

    /***
     * todo 查询店铺过期信息
     * @param array $data
     * @param string $orderBy
     * @param string $order
     * @param int $pageSize
     * @return array
     * @author 张国军 2018年07月13日
     */
    public function getListByCondition($data=[],$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value)
        {
            switch ( $key )
            {
                //店铺id
                case 'wid':
                    $where['wid'] = $value;
                    break;
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
            }
        }
        //查询数据
        $select= $this->init()->wheres($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        if(!empty($pageSize))
        {
            //$select->paginate($pageSize)返回的是:LengthAwarePaginator
            //$select->paginate($pageSize)->toArray() 返回的是数组 "total" => 7, "per_page" => 20,"current_page" => 1
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
            $returnData['total']=$select['total'];
            $returnData['currentPage']=$select['current_page'];
            $returnData['pageSize']=$select['per_page'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        $returnData['data'] = $idAttr;
        return $returnData;
    }

    /**
     * 根据店铺id获取过期时间
     * @param int $wid 店铺id
     * @return string
     * @author 许立 2018年09月28日
     */
    public function getShopExpireTime($wid)
    {
        return $this->init()
            ->model
            ->where('wid', $wid)
            ->first()
            ->end_time ?? '';
    }
}
