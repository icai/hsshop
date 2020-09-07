<?php 

namespace App\Services;

use App\Model\User;
use App\Model\UserInfo;
use App\Model\Weixin;
use App\S\Foundation\RegionService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Permission\WeixinRoleService;
use App\Services\Permission\WeixinUserService;
use MicPage;

/**
 * 店铺（公众号）
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年1月14日 10:32:35
 */
class WeixinService extends Service {
    /**
     * 所有关联关系
     * 
     * @var array
     */
    public $withAll = ['weixinConfigMaster', 'weixinPayment', 'weixinConfigSub'];
    
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

    }

    /**
     * 初始化 设置唯一标识和redis键名
     * 
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * 
     * @return this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new Weixin(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151319
     * @desc 根据条件获取店铺信息
     * @param $is_export bool 导出则不分页 且可能需要导出列表中没显示的字段 如 订单数等 Herry
     */
    public function getShop($is_export = false)
    {
        $input = $this->request->input();
        $where = ['1'=>1];
        if (isset($input['shopName']) && !empty($input['shopName'])){
            $where['shop_name'] = ['like','%'.$input['shopName'].'%'];
        }
        if (isset($input['mphone']) && !empty($input['mphone'])){
            $userService = new UserService();
            $userData = $userService->init()->model->where('mphone',$input['mphone'])->get(['id','mphone'])->toArray();
            $userData = array_pop($userData);
            $where['uid'] = $userData['id']??'';
        }
        if ((isset($input['startTime']) && !empty($input['startTime'])) && (isset($input['endTime']) && !empty($input['endTime']))){
            $where['created_at'] = array('between', [$input['startTime'], $input['endTime']]);
        }else{
            if (isset($input['startTime']) && !empty($input['startTime'])){
                $where['created_at'] = ['>=',$input['startTime']];
            }
            if (isset($input['endTime']) && !empty($input['endTime'])){
                $where['created_at'] = ['<=',$input['endTime']];
            }
        }

        /*搜索店铺分类*/
        if (isset($input['category']) && !empty($input['category'])){
            $weixinBusinessService = new WeixinBusinessService();
            $tmpwhere = [
                'title'=>['like',$input['category']]
            ];
            $businessData = $weixinBusinessService->init()->where($tmpwhere)->getList(false)[0]['data'];
            $businessIds = [];
            if ($businessData){
                foreach ($businessData as $val)
                {
                    $businessIds[] = $val['id'];
                }
            }
            $where['business_id'] = ['in',$businessIds];
        }

        //排序 Herry
        $orderBy = $order = '';
        $order_string = 'id desc';
        if (!empty($input['orderby'])) {
            $orderBy = $input['orderby'];
            $order = $input['order'];
            $order_string = $orderBy . ' ' . $order;
        }

        //省市区
        !empty($input['province_id']) && $where['province_id'] = $input['province_id'];
        !empty($input['city_id']) && $where['city_id'] = $input['city_id'];
        !empty($input['area_id']) && $where['area_id'] = $input['area_id'];

        //总销售额
        if ((isset($input['sum_from']) && !empty($input['sum_from'])) && (isset($input['sum_to']) && !empty($input['sum_to']))){
            $where['sale_sum'] = array('between', [$input['sum_from'], $input['sum_to']]);
        }else{
            if (isset($input['sum_from']) && !empty($input['sum_from'])){
                $where['sale_sum'] = ['>=',$input['sum_from']];
            }
            if (isset($input['sum_to']) && !empty($input['sum_to'])){
                $where['sale_sum'] = ['<=',$input['sum_to']];
            }
        }

        $shopData = $this->init()->where($where)->order($order_string)->getList(!$is_export);

        $this->dealShopData($shopData[0]['data'], $is_export);
        return $shopData;
    }

    /**
     * 处理店铺数据
     * @param array $data 店铺数据
     * @param bool $is_export 许立 2018年04月19日 导出则不分页 且可能需要导出列表中没显示的字段 如 订单数等
     * @return array
     * @author 张永辉 2017年03月16日
     * @update 许立   2018年07月13日 增加导出店铺过期时间
     */
    public function dealShopData(&$data, $is_export)
    {
        $regionService = new RegionService();
        $weixinBusinessService = new WeixinBusinessService();
        $weixinBusinessData = $weixinBusinessService->init()->getList(false)[0]['data'];
        $category = [];
        $regionTmp = [];
        $ids = [];
        $uid = [];
        $wid_array = [];
        foreach ($data as $val){
            $ids[$val['province_id']] = $val['province_id'];
            $ids[$val['city_id']] = $val['city_id'];
            $ids[$val['area_id']] = $val['area_id'];
            $uid[]  = $val['uid'];
            $wid_array[] = $val['id'];
        }
        unset($ids[0]);
        $ids = array_keys($ids);
        $uid = array_unique($uid);
        $res = User::whereIn('id',$uid)->get(['id','mphone'])->toArray();
        $userData = [];
        foreach ($res as $val){
            $userData[$val['id']] = $val;
        }
        $regionData = $regionService->getListById($ids);
        foreach ($weixinBusinessData as $val)
        {
            $category[$val['id']]= $val['title'];
        }
        foreach ($regionData as $val)
        {
            $regionTmp[$val['id']] = $val['title'];
        }

        $paid_order_count_array = [];
        $role_array = [];
        // 过期时间
        $end_time_array = [];
        if ($is_export && $wid_array) {
            //获取订单数 Herry
            $connect = \DB::connection('mysql_dc_log');
            $orders = $connect->select("select wid, sum(order_payed_count) as paid_order_count from dc_order where wid in ( " . implode(',', $wid_array) . ") group by wid");
            if (!empty($orders)) {
                foreach ($orders as $v) {
                    $paid_order_count_array[$v->wid] = $v->paid_order_count;
                }
            }

            //获取店铺角色 Herry
            $roles = \DB::table('weixin as w')
                ->leftJoin('weixin_role as wr', 'w.id', '=', 'wr.wid')
                ->leftJoin('admin_role as ar', 'wr.admin_role_id', '=', 'ar.id')
                ->whereIn('w.id', $wid_array)
                ->whereNull('wr.deleted_at')
                ->get(['w.id', 'ar.name', 'wr.end_time'])
                ->toArray();
            if (!empty($roles)) {
                foreach ($roles as $v) {
                    $role_array[$v->id] = $v->name;
                    // 过期时间
                    $end_time_array[$v->id] = $v->end_time;
                }
            }
        }

        foreach ($data as &$val)
        {
            $val['province_name'] = $regionTmp[$val['province_id']]??'';
            $val['city_name'] = $regionTmp[$val['city_id']]??'';
            $val['area_name'] = $regionTmp[$val['area_id']]??'';
            $val['category_name'] = $category[$val['business_id']] ?? '';
            $val['userData'] = $userData[$val['uid']];
            $val['paid_order_count'] = $paid_order_count_array[$val['id']] ?? 0;
            $val['role'] = $role_array[$val['id']] ?? '测试店铺';
            // 过期时间
            $val['end_time'] = $end_time_array[$val['id']] ?? '不过期';
        }
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151636
     * @desc 获取我的店铺信息
     * @param $uid
     */
    public function getMyShop($uid = '')
    {
        if (empty($uid)){
            $uid = session('userInfo')['id'];
        }

        $weixinUserService = new WeixinUserService();
        $weixinUserData = $weixinUserService->init()->where(['uid'=>$uid])->getList(false)[0]['data'];
        $wids = [];
        foreach ($weixinUserData as $val)
        {
            $wids[] = $val['wid'];
        }
        $this->with($this->withAll);
        $weixinData = $this->init()->where(['id'=>['in',$wids]])->perPage(9)->getList();
        return $weixinData;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151738
     * @desc 删除我的店铺,只有店铺创建者和总后台管理员才有权限删除
     * @param $id
     * @return array
     */
    public function delMyShop($id,$uid = '')
    {
        $result = ['success'=>0,'message'=>''];
        if (empty($uid)){
            $uid = session('userInfo')['id'];
        }
        $weixinData = $this->init()->getInfo($id);
        if (!$weixinData){
            $result['message'] = '店铺不存在';
            return $result;
        }
        if ($weixinData['uid'] != $uid ){
            $result['message'] = '该店铺不属于你！你无权限删除';
            return $result;
        }else{
            //删除关联表信息
           if ($this->init('uid', $uid)->where(['id'=>$id])->delete($id,false)){
                $weixinUserService = new WeixinUserService();
                $weixinUserData = $weixinUserService->init()->where(['wid'=>$id])->getList(false)[0]['data'];
                if ($weixinUserData){
                    $weixinUserIds = [];
                    foreach ($weixinUserData as $val)
                    {
                        $weixinUserService->init()->where(['wid'=>$id])->delete($val['id'],false);
                    }

                    $weixinRoleService = new WeixinRoleService();
                    $weixinRoleData = $weixinRoleService->init()->model->where(['wid'=>$id])->get()->toArray();
                    if ($weixinRoleData){
                        foreach ($weixinRoleData as $val)
                        {
                            $weixinRoleService->init()->where(['wid'=>$id])->delete($val['id'],false);
                        }
                    }
                    $weChatShopConfService = new WeChatShopConfService();
                    if(!empty($weChatShopConfService->getRowByWid($id))){
                        $weChatShopConfService->delData(['wid'=>$id]);
                    }
                }
           }
           $result['success'] = 1;
           return $result;
        }


    }


    /**
     * [微商城前台面获取店铺信息]
     * @param  [int] $wid [店铺id]
     * @return [array] $returnData [店铺相关数据]
     */
    public function getStageShop($wid)
    {
        $uid = session('weixin_uid');

        if ( empty($uid) ) {
            // 从数据库读取uid
            $uid = $this::init()->model->where('id', $wid)->value('uid');
            $request = app('request');
            $request->session()->put('weixin_uid', $uid);
            $request->session()->save();
        }

        return $this::init('uid', $uid)->getInfo($wid);
    }

    /**
     * todo 获取店铺信息[更改此方法了通知我]
     * @param $wid
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-28
     */
    public function getStoreInfo($wid, $isXCX=0)
    {
        $returnData = array('errCode'=>0,'errMsg'=>'','data'=>[]);
        if(empty($wid))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '店铺id为空';
            return $returnData;
        }
        $weixinData = $this->init()->where(['id'=>$wid])->getInfo();
        $storeInfo  = MicPage::to($wid,$isXCX)->storeDataToPage($weixinData);
        $returnData['data'] = $storeInfo === false ? [] : $storeInfo;
        return $returnData;
    }

    /**
     * todo 通过id 来更改店铺信息 【此方法更改了烦请通知一下张国军】
     * @param array $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-02
     */
    public function updateData($id,$data=[])
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
        $updateReturnValue=Weixin::where(['id'=>$id])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 查询积分的开关状态
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-06
     */
    public function selectPointStatus($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='查询数据为空';
            return $returnData;
        }
        $result=Weixin::select(['id','is_point','created_at'])->where($data)->get()->toArray();
        $returnData['data']=$result;
        return $returnData;
    }
	
     /** todo 通过店铺id查询店铺信息
     * @param $id
     * @return array
     * @author jonzhang
     * @date 2017-05-27
     */
    public function getStore($id)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为空';
            return $returnData;
        }
        $store=[];
        $weixinData=Weixin::where(['id'=>$id])->get()->toArray();
        if(!empty($weixinData)) {
            $store=$weixinData[0];
        }
        $returnData['data']=$store;
        return $returnData;
    }

    /**
     * 检查对应条件的店铺是否存在
     * @author hsz
     * @param array $where
     * @return bool
     */
    public function checkStoreIsExist($where = []) {
        $return = false;
        $weixinData=Weixin::where($where)->get()->toArray();
        if($weixinData) {
            $return = true;
        }
        return $return;
    }

    /***
     * todo 查询店铺数据
     * @param array $data 查询条件
     * @param string $orderBy 排序字段
     * @param string $order 正序/倒序
     * @param int $pageSize 页面数
     * @return array
     * @author jonzhang
     * @date 2018-06-25
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
                //uid
                case 'uid':
                    $where['uid'] = $value;
                    break;
                // 店铺名称
                case 'shop_name':
                    $where['shop_name'] = ['LIKE',$value.'%'];
                    break;
            }
        }
        //查询数据
        $select= $this->init()->model->wheres($where);
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
     * 全部更新店铺数据
     * @param array $data
     * @return array
     * @author 何书哲 2018年9月10日
     */
    public function allUpdateData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $updateReturnValue=Weixin::where([])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }
    
}
