<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/27
 * Time: 11:22
 */

namespace App\Module;


use App\Jobs\SendGroupsLog;
use App\Jobs\SendMeetingGroupSMS;
use App\Jobs\SendTplMsg;
use App\Lib\Redis\RedisClient;
use App\Model\GroupsDetail;
use App\Model\GroupsRule;
use App\Model\GroupsSku;
use App\Model\MeetingTmpLog;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductImg;
use App\S\Foundation\VerifyCodeService;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Groups\GroupsDetailService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductService;
use App\S\Product\ProductSkuService;
use App\S\Product\RemarkService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Foundation\PaymentService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderRefundService;
use Illuminate\Pagination\LengthAwarePaginator;
use OrderService;
use EasyWeChat\ShakeAround\Group;
use DB;
use MallModule as ProductStoreService;
use Redisx;
use MemberAddressService;
use App\S\Market\CommendDetailService;
use App\S\Market\CommendInfoService;
use OrderCommon;

class MeetingGroupsRuleModule
{

    public $request;
    public function __construct()
    {
        $this->request = app('request');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 获取团购
     * @desc
     */
    public function getRule($where=[],$orderBy = '', $order = '')
    {
        $time = date('Y-m-d H:i:s',time());
        $where['status']= ['<>',-2];
        if ($status = $_REQUEST['status']??0){
            switch ($status){
                case 1:
                    $where['start_time'] = ['>=',$time];
                    break;
                case 2:
                    $where['start_time'] = ['<=',$time];
                    $where['end_time'] = ['>=',$time];
                    $where['status'] = 0;
                    break;
                case 3:
                    $where['end_time'] = ['<=',$time];
                    break;
                default:

            }
        }
        $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getlistPage($where,$orderBy, $order);
        $ruleids = [];
        $now = time();
        foreach ($data[0]['data'] as &$val){
            $pids[] = $val['pid'];
            $ruleids[] = $val['id'];
            if ($val['status'] == -1){
                $val['state'] = -1;
            }elseif (strtotime($val['start_time'])>=$now){
                $val['state'] = 2; //未开始
            }elseif (strtotime($val['start_time'])<=$now && strtotime($val['end_time'])>=$now){
                $val['state'] = 1; //正在进行中
            }elseif (strtotime($val['end_time'])<=$now){
                $val['state'] = 3; //已过期
            }
        }
        if ($data[0]['data']){
            $res = Product::whereIn('id',$pids)->get(['id','img','price'])->toArray();
            $skus = $this->getSkus($ruleids);
            $pdata = [];
            foreach ($res as $value){
                $pdata[$value['id']] = $value;
            }
            foreach ($data[0]['data'] as &$v){
                $v['product'] = $pdata[$v['pid']];
                //判断是否有活动图片
                if ($v['img']){
                    $v['product']['img'] = $v['img'];
                }
                $v['skus'] = $skus[$v['id']]??[];
                $temp = [];
                if ($v['skus']){
                    foreach ($v['skus'] as $item){
                        $temp[] = $item['price'];
                    }
                }

                $v['min'] = $temp[0]??0;
                $v['max'] = array_pop($temp)??'';
            }
        }

        return $data;

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170705
     * @desc
     * @param $ruleId
     */
    public function getSkus($ruleIds)
    {
       $groupsSkuService = new GroupsSkuService();
       $skus = $groupsSkuService->getlistByRuleIds($ruleIds);
       $result = [];
       foreach ($skus as $val)
       {
            $result[$val['rule_id']][] = $val;
       }
       return $result;
   }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170705
     * @desc  获取单个团购
     */
   public function getById($id,$mid=0)
   {
       $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getRowById($id);
        if (!$data){
            return [];
        }
        $img = ProductImg::where('product_id',$data['pid'])->where('status',1)->get(['id','img'])->toArray();
        $data['img'] = $img;
        $skus = $this->getSkus([$data['id']]);
       $temp = [];
       //团长价格
       $headPriceArr = [];
       if ($skus){
           foreach ($skus[$data['id']] as $item){
               $temp[] = $item['price'];
               $headPriceArr[] = $item['head_price'];
           }
       }
       sort($temp);
       sort($headPriceArr);
       $data['headMin'] = $headPriceArr[0] ?? 0;
       $data['min'] = $temp[0]??0;
       $data['max'] = array_pop($temp)??'';
       if ($data['max']==$data['min']){
           $data['max'] = '';
       }
       $product = Product::find($data['pid']);
       if ($product){
           $product = $product->toArray();
       }
       if(!empty($product['content'])) {
           $product['content'] = ProductStoreService::processTemplateData($data['wid'], $product['content']);

           $product['content'] = ProductModule::addProductContentHost($product['content']);

           //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
           $product['content'] = dealWithProductContent($data['wid'], $product['content']);
       }
       $product['noteList'] = (new ProductMsgService())->getListByProduct($product['id']);
       $this->dealNoteList($product['noteList'],$mid);
       $data['product'] = $product;
        return $data;
   }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180416
     * @desc 用户留言默认自
     */
   public function dealNoteList(&$data,$mid)
   {
       $logData = MeetingTmpLog::where('mid',$mid)->orderBy('id','DESC')->first();
       if (!$logData){
           foreach ($data as &$item){
               $item['content'] = '';
           }
           return true;
       }
       $logData = $logData->toArray();
       $res = (new RemarkService())->getByRemarkNo($logData['remark_no']);
       $remarkData = [];
       foreach ($res as $val) {
            $remarkData[$val['pmid']] = $val;
       }
       foreach ($data as &$item){
           $item['content'] = $remarkData[$item['id']]['content']??'';
       }
       return true;
   }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170717
     * @desc 创建团购
     * @param $rule_id
     */
    public function createGroups($rule_id)
    {
        $result = [
            'success'   => 0,
            'message'   => '',
        ];
        $groupsRuleService = new GroupsRuleService();
        $groupsService = new GroupsService();
        $groupsDetailService = new GroupsDetailService();
        $rule = $groupsRuleService->getRowById($rule_id);
        if (!$rule){
            $result['message'] = '团被删除或不存在';
            return $result;
        }
        //判断状态
        if ($rule['status'] == -1){
            $rule['state'] = -1; //失效
        }elseif (strtotime($rule['start_time'])>=time()){
            $rule['state'] = 2; //未开始
        }elseif (strtotime($rule['start_time'])<=time() && strtotime($rule['end_time'])>=time()){
            $rule['state'] = 1; //正在进行中
        }elseif (strtotime($rule['end_time'])<=time()){
            $rule['state'] = 3; //已过期
        }
        if ($rule['state'] != 1){
            $result['message'] = '该拼团活动已结束';
            return $result;
        }
        //判断团购限购商品数量
        if ($rule['num']>0 && $_REQUEST['num']> $rule['num']){
            $result['message'] = '该团购商品最多购买'.$rule['num'].'件';
            return $result;
        }
        $groupsData = [
            'identifier' => $groupsService->getIdentifier(),
            'wid'         => $rule['wid'],
            'rule_id'   => $rule_id,
            'num'        => 0,
            'type'       => 1,
        ];

        $groups_id = $groupsService->add($groupsData);

        $result['success'] = 1;
        $result['data'] = $groups_id;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170711
     * @desc 订单支付以后处理团购数据
     * @param $order
     * @udpate 张永辉 2018年7月16日 开团成团发送数据到数据中心
     * @update 何书哲 2018年8月7日 修改发送参团数据类型
     */
    public function afterOrder($order)
    {
        $result = [
            'success'=> 0,
            'message'=> '',
        ];
        $groupsService = new GroupsService();
        $groupsDetailService = new GroupsDetailService();
        $groupsRuleService = new GroupsRuleService();

        $groupsData = $groupsService->getRowById($order['groups_id']);
        $groupsRuleData = $groupsRuleService->getRowById($groupsData['rule_id']);
        if (!$groupsData){
            $result['message'] = '该团不存在';
            return $result;
        }
        //添加团购详情
        $count = $groupsDetailService->model->where('groups_id',$order['groups_id'])->count();
        if ($count<=0){
            $is_head = 1;
            //add MayJay 团长消息队列
            $time = strtotime($groupsRuleData['end_time']) - time() ;
            if($groupsRuleData['expire_hours'] > 0 && $time > $groupsRuleData['expire_hours'] * 3600 ){
                $delayTime = $groupsRuleData['expire_hours'] * 3600;
            }else{
                $delayTime = $time;
            }
            (new MessagePushModule($groupsData['wid'], MessagesPushService::ActivityGroup))->setDelay($delayTime*4/5)->sendMsg(['oid'=>$order['id'],'num'=>$groupsRuleData['groups_num'],'group_type'=>'group_dead_time']);
        }else{
            $is_head = 0;
        }
        //留言编号
        $orderDetail = (new OrderDetailService())->init()->model->where('oid',$order['id'])->get()->toArray();
        if ($orderDetail){
            $orderDetail = current($orderDetail);
        }
        $groupsDetailData = [
            'groups_id'     => $order['groups_id'],
            'member_id'     => $order['mid'],
            'is_head'       => $is_head,
            'oid'            => $order['id'],
            'remark_no'     => $orderDetail['remark_no']??'',
        ];
        $groupsdetailId = $groupsDetailService->add($groupsDetailData);
        //何书哲 2018年8月7日 修改发送参团数据类型
        dispatch((new SendGroupsLog($groupsdetailId,$is_head == 1 ? 1 : 2))->onQueue('SendGroupsLog'));//发送参团数据到数据中心队列

        //更新团购
        $count = $count+1;
        if (!$groupsData['open_time']){
            $groupsData['open_time'] = date("Y-m-d H:i:s",time());
            $groupsData['status'] = 1;
        }
        if ($groupsRuleData['groups_num']<=$count){
            $groupsData['status'] = 2;
            $groupsData['complete_time'] = date('Y-m-d H:i:s',time());

            /*判断该团购是否开启抽奖 add by wuxiaoping 2017.11.07 */
            if ($groupsRuleData['is_open_draw'] == 1) { //如果开启了抽奖,则设置订单状态为待抽奖

                $this->saveDrawGroupOrderStatus($order);
            }else {  //如果未开启抽奖则按以前的正常流程
                //更新订单状态
                $this->upCompleteGroupsOrder($order);
            }
            //注册成功之后发送短信注册账号等信息
            $this->register($order['groups_id']);
            //Add MayJay 成团提醒
            //获取所有参团人信息

            $groupsDetail = $groupsDetailService->getListByWhere(['groups_id'=>$order['groups_id']]);

            $mids = array_column($groupsDetail,'member_id');

            foreach($mids as $mid) {
                (new MessagePushModule($groupsData['wid'], MessagesPushService::ActivityGroup))->sendMsg(['oid'=>$order['id'],'mid' => $mid ,'group_type'=>'group_success']);
            }
            dispatch((new SendGroupsLog($groupsdetailId,'3'))->onQueue('SendGroupsLog'));  //发送成团数据到数据中心队列
        }
        $groupsData['num'] = $count;
        $groupsData['pnum'] =  $groupsData['pnum']+$this->getProductNum($order['id']);
        unset($groupsData['deleted_at']);
        unset($groupsData['updated_at']);
        unset($groupsData['created_at']);
        $groupsService->update($groupsData['id'],$groupsData);
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180423
     * @desc 如果团man发送注册信息给团长
     * @param $gids
     * @update 张永辉 2018年6月27日 626店铺只发送短信不注册店铺不注册账号等
     */
    public function register($gid)
    {
        $groupsDetailService = new GroupsDetailService();
        $groupsService = new GroupsService();
        $res = $groupsDetailService->getListByWhere(['groups_id'=>$gid]);
        $groupsData = $groupsService->getRowById($gid);
        foreach ($res as $val){
            $phone = $this->getRemarkPhone($val['remark_no']);
            if ($groupsData['wid'] == '626'){
                (new VerifyCodeService())->groupPurchaseNoitice($phone,[],15);
            }else{
                $job = (new SendMeetingGroupSMS($phone,$val))->onQueue('SendMeetingGroupSMS')->delay(rand(1,60));
                dispatch($job);
            }
        }

    }

    public function  upCompleteGroupsOrder($order){
        $groups_id =  $order['groups_id'];
        $where = [
            'groups_id'=>$groups_id,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id'])->toArray();
        foreach ($orderData as $val){
            $val['groups_status'] = 2;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
//            $this->sendGroupMsg($order);

        }
        //更新未支付订单
        $where = [
            'groups_id'=>$groups_id,
            'status'   => 0,
            'id'      =>['<>',$order['id']],
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id'])->toArray();
        foreach ($orderData as $val){
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
        }
    }

    /**
     * 更改抽奖团的订单状态
     * 如果是抽奖团，支付成功已成团的情况下，订单状态改为待抽奖
     * @author wuxiaoping 2017.11.13 
     * @return [type] [description]
     */
    public function saveDrawGroupOrderStatus($order)
    {
        $groups_id =  $order['groups_id'];
        $where = [
            'groups_id'=>$groups_id,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id','status'])->toArray();
        foreach ($orderData as $val){
            if ($val['status'] == 1) {
                $val['groups_status'] = 2;
                $val['status'] = 7;
            }
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
//            $this->sendGroupMsg($order);

        }
        //更新未支付订单
        $where = [
            'groups_id'=>$groups_id,
            'status'   => 0,
            'id'      =>['<>',$order['id']],
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id'])->toArray();
        foreach ($orderData as $val){
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
        }
    }

    /**
     * 分别更新拼团用户是否中奖的订单状态
     * @author [name] <wuxiaoping 2017.11.08>
     * @param  [array] $drawGroups [拼团活动规则数据]
     * @return [array]                 [description]
     */
    public function getDrawData($drawGroups)
    {
        //定义中奖更新状态数据，未中奖更新数据，未中奖条件，中奖条件
        $saveWinData = $saveNoWinData = $saveData = [];
        $closeOrderIds = $drawOrderIds = [];
        foreach ($drawGroups as $key => $value) {
            (new GroupsService())->update($key,['is_draw' => 1]); //更新该拼团已进行抽奖(标识)
            $orderWhere['groups_id'] = $key;
            $orderData = OrderService::init()->model->wheres($orderWhere)->get(['id','groups_id','mid','address_phone'])->toArray();
            //设置中奖人数为0，或订单状态为未支付（转为退款处理）
            if ($value['draw_pnum'] == 0) {
                if ($orderData) {
                    foreach ($orderData as $orderKey => $orderValue) {
                        $closeOrderIds[] = $orderValue['id'];
                    }
                }
                array_push($saveWinData,[]);
                array_push($saveNoWinData,$closeOrderIds);  
            }else {
                if ($orderData) {
                    foreach ($orderData as $k => $orderVal) {
                        $mids[] = $orderVal['mid'];
                    }
                    $mids = array_unique($mids);  //过滤掉数组中重复的值
                    //设置中奖类型为随机
                    if ($value['draw_type'] == 0) { 
                        $returnData = $this->handleData($mids,$orderData,$value['draw_pnum']);
                        array_push($saveWinData,$returnData['drawOrderIds']);
                        array_push($saveNoWinData,$returnData['closeOrderIds']);
                    }
                    //设置中奖类型为指定用户(以手机号作为标识)
                    else {
                        $phones = explode(',',$value['draw_phones']);
                        $matchMids = [];
                        foreach ($orderData as $k => $orderVal) {
                            if (in_array($orderVal['address_phone'],$phones)) {
                                $matchMids[] = $orderVal['mid'];
                            }
                        }
                        $prizeUserNum = count($matchMids);
                        //如果匹配的中奖手机号与后台设置的中奖人数不一致，则其他的随机获取
                        if ($prizeUserNum <> $value['draw_pnum']) {
                            if($prizeUserNum >= $value['draw_pnum']){
                                $returnData = $this->handleData($mids,$orderData,0,$matchMids);
                            }else {
                                $leftNum = ((int)$value['draw_pnum'] - (int)$prizeUserNum);
                                $returnData = $this->handleData($mids,$orderData,$leftNum,$matchMids);
                            }
                            array_push($saveWinData,$returnData['drawOrderIds']);
                            array_push($saveNoWinData,$returnData['closeOrderIds']);
                        }else {
                            //中奖用户
                            $returnData = $this->handleData($mids,$orderData,0,$matchMids);
                            array_push($saveWinData,$returnData['drawOrderIds']);
                            array_push($saveNoWinData,$returnData['closeOrderIds']);
                        }
                    } 
                }
            }
        }
        //处理中奖与未中奖的订单id数据
        $drawOrderIds = []; 
        if ($saveWinData[0]) {
            $winWhere['id'] = ['in',$saveWinData[0]];
            $winOrderData = OrderService::init()->model->wheres($winWhere)->with('orderDetail')->get(['id','wid','groups_id','mid','status','pay_price'])->toArray();
            if ($winOrderData) {
                foreach ($winOrderData as $wkey => $wValue) {
                    if($wValue['status'] == 7){  //选取待抽奖用户进行抽奖
                        $drawOrderIds[] = $wValue['id'];
                    }
                }
            }
        }
        //获取需要关闭的订单信息
        $notWinOrderData = [];
        if ($saveNoWinData[0]) {
            $notWinWhere['id'] = ['in',$saveNoWinData[0]];
            $notWinOrderData = OrderService::init()->model->wheres($notWinWhere)->with('orderDetail')->get(['id','wid','groups_id','mid','status','pay_price','pay_way'])->load('orderDetail')->toArray();
        }

        if ($drawOrderIds) {
            $this->upDrawOrder($drawOrderIds);
        }
        if ($notWinOrderData) {
            $this->closeDrawOrder($notWinOrderData);
        }
        
    }

    /**
     * [upDrawOrder 更新中奖订单]
     * @param  [array] $oids [订单id数组]
     * @return [type]       [description]
     */
    public function upDrawOrder($oids)
    {
        foreach ($oids as $key => $value) {
            $where['id'] = $value;
            $saveData['status'] = 1;
            $saveData['groups_status'] = 2;
            OrderService::init()->where($where)->update($saveData,false);
        }
        
    }

    /**
     * [closeDrawOrder 更新未中奖订单]
     * @param  [array] $oids [订单id数组]
     * @return [type]       [description]
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function closeDrawOrder($orders)
    {
        $orderModule = new OrderModule();
        $refundModule = new RefundModule();
        foreach ($orders as $key => $value) {
            $where['id'] = $value['id'];
            $saveData['refund_status'] = 3; //商家同意退款
            $saveData['groups_status'] = 3; //未成团
            OrderService::init()->where($where)->update($saveData,false);

            //已付款 关闭订单 需要退款
            $res = $orderModule->groupOrderRefund($value['id'], $value['orderDetail'][0]['product_id']);

            $prop_id = $value['orderDetail'][0]['product_prop_id'] ?? 0;

            if ($res['code'] == 'SUCCESS') {
                //微信退款审核成功 更改订单状态为微信审核成功
                if ($value['pay_way'] != 3 && $value['pay_way'] != 2) {
                    //非余额支付订单的退款 才改变状态 Herry 20171226
                    OrderService::init('wid', $value['wid'])
                        ->where(['id' => $value['id']])
                        ->update(['status' => 4, 'refund_status' => 4], false);
                }
            } else {
                //如果直接退款失败 可能是没上传商户证书或者商家余额不足等原因
                //模拟用户申请退款 走通用退款流程 商家可以在后台同意退款 但是商家不可拒绝退款
                //如果商家拒绝退款 提示未成团订单退款必须同意
                $refundModule->closeGroupOrderApplyRefund($value, $value['orderDetail'][0]['product_id'], $value['pay_price'], $prop_id);
            }
        }  
    }

    /**
     * 团购中奖与未中奖数据处理
     * @return [type] [description]
     */
    public function handleData($mids,$orderData,$draw_pnum=0,$matchMids=[])
    {
        $drawMids = $notWinMids = [];
        $returnData = $drawOrderIds = $closeOrderIds = [];
        //$number必须大于0
        if ($draw_pnum) {
            if ($matchMids) {
                $leftMids = array_diff($mids, $matchMids);
                $number = $draw_pnum >= count($leftMids) ? count($leftMids) : $draw_pnum;
                //随机获取对应的中奖数
                $randKeys = array_rand($leftMids,$number);
            }else{
                $number = $draw_pnum >= count($mids) ? count($mids) : $draw_pnum;
                //随机获取对应的中奖数
                $randKeys = array_rand($mids,$number);
            }

            //是否获取多个匹配用户
            if (is_array($randKeys)) {
                foreach ($randKeys as $val) {
                    $drawMids[] = $mids[$val];
                }
            }else {
                $drawMids[] = $mids[$randKeys];
            }
        }
        //合并匹配中奖的用户
        if ($matchMids) {
            $drawMids = array_merge($matchMids,$drawMids); 
        }
        //未中奖用户
        $notWinMids = array_diff($mids,$drawMids);

        foreach ($orderData as $k => $orderVal) {
            if (in_array($orderVal['mid'],$drawMids)) {
                $drawOrderIds[] = $orderVal['id'];
            }else {
                $closeOrderIds[] = $orderVal['id'];
            }
        }
        $returnData['drawOrderIds']  = $drawOrderIds;
        $returnData['closeOrderIds'] = $closeOrderIds;

        return $returnData;
    }

    //商品名称
    //{{keyword1.DATA}}
    //订单金额
    //{{keyword2.DATA}}
    //发货时间
    //{{keyword3.DATA}}
    //温馨提示
    //{{keyword4.DATA}}
    public function sendGroupMsg($order)
    {
        $memberService = new MemberService();
        $memberInfo = $memberService->model->select(['openid','xcx_openid'])->find($order['mid']);
        $memberInfo = $memberInfo->toArray();
        if(!$memberInfo['xcx_openid']){
            return ;
        }
        $orderInfo = OrderService::getOrderInfo($order['id']);
        $title = '';
        foreach ($orderInfo['data']['orderDetail'] as $value) {
            $title .= $value['title'] ." ";
        }
        $data['touser'] = $memberInfo['xcx_openid'];
        $data['form_id'] = $order['prepay_id'];
        $data['page'] = 'pages/order/orderDetail/orderDetail?oid='.$order['id'];
        $data['data']['keyword1'] = [
            'value' => $title,
        ];
        $data['data']['keyword2'] = [
            'value' => $order['pay_price'],
        ];
        $data['data']['keyword3'] = [
            'value' => '商家将于5天内发货',
        ];
        $data['data']['keyword4'] = [
            'value' => "如果未按承诺时间发货，系统将按照规则进行赔偿。",
        ];
        (new WXXCXSendTplService($order['wid']))->sendTplNotify($data,WXXCXSendTplService::GROUP_NOTIFY);
    }





    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171023
     * @desc 获取团购订单的商品数量
     */
    public function getProductNum($oid)
    {
        $orderDetailService = new OrderDetailService();;
        $orderData = $orderDetailService->init()->model->where('oid',$oid)->sum('num');
        return $orderData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170711
     * @desc 根据团id，获取拼团信息
     * @param $groups_id
     */
    public function getGroupsById($groups_id,$mid)
    {
        $result = ['success'=>0,'message'=>''];
        $groupsData = (new GroupsService())->getRowById($groups_id);
        if (!$groupsData){
            $result['message'] = '团不存在';
            return $result;
        }

        $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
        $product = Product::select(['id','img','price','title','stock','sold_num'])->find($ruleData['pid']);
        if ($product){
            $product = $product->toArray();
        }
        $product['noteList'] = (new ProductMsgService())->getListByProduct($product['id']);
        $this->dealNoteList($product['noteList'],$mid);
        $ruleData['product'] = $product;
        //获取团购最低价格
        $skus = $this->getSkus([$groupsData['rule_id']]);
        $temp = [];
        if ($skus){
            foreach ($skus[$groupsData['rule_id']] as $item){
                $temp[] = $item['price'];
            }
        }
        sort($temp);
        $ruleData['min'] = $temp[0]??0;
        $ruleData['max'] = array_pop($temp)??'';
        if ($ruleData['max']==$ruleData['min']){
            $ruleData['max'] = '';
        }
        //团购名单
        $groupsDetailData = (new GroupsDetailService())->getListByWhere(['groups_id'=>$groups_id],'','','id','asc');
        $memberIds = [];
        foreach ($groupsDetailData as $val){
            $memberIds[] = $val['member_id'];
        }
        $memberData = (new MemberService())->getListById(array_unique($memberIds));
        $members = [];
        foreach ($memberData as $val){
            $members[$val['id']] = $val;
        }
        foreach ($groupsDetailData as &$v){
            $v['member'] = $members[$v['member_id']];
        }
        //获取当前用户的该团订单id
        $order = Order::select(['id','address_name','address_phone','address_detail','address_id'])->where('mid',$mid)->where('groups_id',$groups_id)->first();

        $result['success'] = 1;
        $result['groups'] = $groupsData;
        $result['rule'] = $ruleData;
        $result['groupsDetail'] = $groupsDetailData;
        $result['order_id'] = $order->id??0;
        if ($order){
            $result['order'] = $order->toArray();
        }else{
            $result['order'] = [];
        }
        return $result;

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170712
     * @desc 获取团
     * @param $rule_id
     */
    public function getGroups($rule_id,$mid='')
    {
        $ids = $this->getMyGroupsIds($mid);
        list($res) = (new GroupsService())->getlistPage(['rule_id'=>$rule_id,'status'=>1,'id'=>['not in',$ids]],'num','desc');
        //获取团信息
        $groups = [];
        if (!$this->request->input('page')){
            $groups = array_slice($res['data'],0,10);
        }
//        show_debug($groups);
        if ($groups){
            $groupsDetailService = new GroupsDetailService();
            $ids = [];
            foreach ($groups as $val){
                $ids[] = $val['id'];
            }
            $groupsDataiData = $groupsDetailService->getListByWhere(['groups_id'=>['in',$ids],'is_head'=>1]);
            $memberids = [];
            $groupsDetail = [];
            foreach ($groupsDataiData as $v){
                $memberids[] = $v['member_id'];
                $groupsDetail[$v['groups_id']] = $v;
            }
            //获取开团用户图片
            $res = (new MemberService())->getListById($memberids);
            $member = [];
            foreach ($res as $value)
            {
                $member[$value['id']] = $value;
            }
            foreach ($groupsDetail as &$item){
                $item['members'] = $member[$item['member_id']];
            }
            foreach ($groups as &$val){
                $val['groupDetail'] =$groupsDetail[$val['id']]??'';
            }
        }
        return $this->dealGroups($groups,$mid);
    }

    public function dealGroups($groups,$mid)
    {
        $result = [];
        if ($groups){
            $ruleData = (new GroupsRuleService())->getRowById($groups[0]['rule_id']);
            foreach ($groups as $val){
                if ($mid == $val['groupDetail']['member_id']){
                    continue;
                }
                $temp = [];
                $temp['id'] = $val['id'];
                $temp['identifier'] = $val['identifier'];
                $temp['headimgurl'] = $val['groupDetail']['members']['headimgurl']??'';
                $temp['nickname'] = $val['groupDetail']['members']['nickname']??'';
                $temp['num'] = $ruleData['groups_num']-$val['num'];

                //未成团订单存在时间 取活动配置的小时数 Herry 20171108
                //$temp['end_time'] = strtotime($val['open_time'])+86400;
                if ($ruleData['expire_hours']) {
                    $temp['end_time'] = strtotime($val['open_time']) + $ruleData['expire_hours'] * 3600;
                    if ($temp['end_time']>strtotime($ruleData['end_time'])){
                        $temp['end_time']=strtotime($ruleData['end_time']);
                    }
                } else {
                    $temp['end_time']=strtotime($ruleData['end_time']);
                }

                $temp['now_time'] = date("Y/m/d H:i:s",time());
                $temp['stop_time'] = date('Y/m/d H:i:s',$temp['end_time']);

                $temp['end_time'] = implode(':',$this->dealTime($temp['end_time']));
                $result[] = $temp;
            }
        }

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170719
     * @desc 判断商品是否能够删除
     */
    public function isDelProduct($ids,$oper='edit')
    {
        $groupsRuleService = new GroupsRuleService();
        $now = date("Y-m-d H:i:s",time());
        $res = $groupsRuleService->model->where('end_time','>',$now)->where('status',0)->whereIn('pid',$ids)->first();
        if ($res){
            return false;
        }else{
            if ($oper == 'del'){
                //删除过期的团购活动
                $data = $groupsRuleService->model->whereIn('pid',$ids)->get(['id','pid'])->toArray();
                if ($data){
                    foreach ($data as $val){
//                        $groupsRuleService->del($val['id']);
                        $groupsRuleService->update($val['id'],['status'=>-2]);
                    }
                }
            }
            return true;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 定时任务处理团购
     */
    public function autoGroups()
    {
        /*添加where status=2条件，查询开启抽奖团并成团的订单*/

        $field = ['g.id','g.rule_id','g.num','g.open_time','g.status','gr.end_time','gr.pid','gr.auto_success','gr.expire_hours','gr.is_open_draw','gr.draw_pnum','gr.draw_type','gr.draw_phones'];
        $res =  DB::table('groups as g')->leftJoin('groups_rule as gr','g.rule_id','=','gr.id')->where('g.status',1)
            ->orWhere(function($query){
                $query->where('g.status',2)->where('g.is_draw',0)->where('gr.is_open_draw',1);
            })->whereNull('g.deleted_at')->get($field)->toArray();
        $now = time();

        //成团id
        $ids = [];
        //关闭id
        $closeIDs = $closeDrawIds = [];
        //商品id
        $pids = [];

        /*抽奖团数据 add by wuxiaoping 2017.11.13*/
        $drawGroups = [];

        if ($res){
            foreach ($res as $val){
                //开团24小时，或团活动结束前未成团则改变状态
                //未成团订单存在时间 取活动配置的小时数 Herry 20171108
                //$endTime = strtotime($val->open_time)+86400;
                if ($val->status == 0) {
                    //未付款订单关闭时间直接以拼团结束为准
                    $endTime=strtotime($val->end_time);
                } else {
                    if ($val->expire_hours) {
                        $endTime = strtotime($val->open_time) + $val->expire_hours * 3600;
                        if ($endTime>strtotime($val->end_time)){
                            $endTime=strtotime($val->end_time);
                        }
                    } else {
                        $endTime=strtotime($val->end_time);
                    }
                }

                if ($now>=$endTime){
                    if ($val->auto_success) {
                        //自动成团
                        if ($val->status == 1) { //过虑掉已成团的group_id
                            $ids[] = $val->id;
                        }
                           
                    } else {
                        if ($val->is_open_draw == 1) {
                            if ($val->status == 2) {   //开启抽奖，已成团
                                $drawGroups[$val->id]['draw_pnum'] = $val->draw_pnum;
                                $drawGroups[$val->id]['draw_type'] = $val->draw_type;
                                $drawGroups[$val->id]['draw_phones'] = $val->draw_phones;
                            }else {   //开启抽奖，未成团
                                $closeDrawIds[] = $val->id;
                                $pids[$val->id] = $val->pid;
                            }        
                        }else {
                            //自动关闭
                            $closeIDs[] = $val->id;
                            $pids[$val->id] = $val->pid;
                        }
                        
                    }
                }
            }
        }
        //合并关闭的抽奖团id
        $closeIDs = ($closeIDs + $closeDrawIds);
        //更新团状态为未成团,已支付订单设置为待退款，未支付订单设置为关闭订单
        DB::beginTransaction();

        //批量更新团,更新团为已成团
        $now = date('Y-m-d H:i:s',time());
        $groupService = new GroupsService();
        $groupService->batchUpdate($ids,['status'=>2,'complete_time'=>$now]);

        //批量更新团为关闭
        $groupService->batchUpdate($closeIDs,['status'=>3]);

        //更新自动成团订单
        $this->upOrder($ids);

        //更新自动关闭拼团
        $this->closeGroups($closeIDs, $pids);

        /*add by wuxiaoping 2017.11.14 抽奖团信息处理*/
        if ($drawGroups) {
            $this->getDrawData($drawGroups);
        }

        DB::commit();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 处理订单
     */
    public function upOrder($ids)
    {
        //更新订单已支付订单
        $where = [
            'groups_id'=>['in',$ids],
            'status'   => 1,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id','status','pay_price','wid'])->toArray();
        foreach ($orderData as $val){
            unset($val['status']);
            unset($val['pay_price']);
//            $val['refund_status'] = 1;
            $val['groups_status'] = 2;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
            //Add MayJay 成团提醒
            //获取所有参团人信息

            $groupsDetail = (new GroupsDetailService())->getListByWhere(['groups_id'=>$val['groups_id']]);

            $mids = array_column($groupsDetail,'member_id');

            foreach($mids as $mid) {
                (new MessagePushModule($val['wid'], MessagesPushService::ActivityGroup))->sendMsg(['oid'=>$val['id'],'mid' => $mid ,'group_type'=>'group_success']);
            }

//            $this->addRefund($val);
        }

        //更新未支付订单
        $where = [
            'groups_id'=>['in',$ids],
            'status'   => 0,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id','status'])->toArray();
        foreach ($orderData as $val){
            unset($val['status']);
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
        }
    }

    /**
     * 关闭拼团 已支付的进行退款
     * @param $ids
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function closeGroups($ids, $pids)
    {
        //更新订单已支付订单
        $where = [
            'groups_id'=>['in',$ids],
            'status'   => 1,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id','status','pay_price','mid','wid','address_phone','pay_way'])->load('orderDetail')->toArray();
        $orderModule = new OrderModule();
        $refundModule = new RefundModule();
        foreach ($orderData as $val){
            $status = $val['status'];
            $payPrice = $val['pay_price'];
            unset($val['status']);
            unset($val['pay_price']);
            $val['groups_status'] = 3;

            $prop_id = $val['orderDetail'][0]['product_prop_id'] ?? 0;
            unset($val['orderDetail']);

            //0元订单 直接关闭订单
            if ($payPrice <= 0) {
                $val['status'] = 4;
                $val['refund_status'] = 8;
            }

            
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
            //add MayJay 团订单关闭
            (new MessagePushModule($val['wid'], MessagesPushService::ActivityGroup))->sendMsg(['oid'=>$val['id'],'group_type'=>'group_close']);
            if ($payPrice > 0) {
                //已付款 关闭订单 需要退款
                $res = $orderModule->groupOrderRefund($val['id'], $pids[$val['groups_id']]);
                //\Log::info('【拼团】拼团未成团自动退款微信审核返回结果：' . json_encode($res));
                if (!empty($res['code']) && $res['code'] == 'SUCCESS') {
                    //微信退款审核成功 更改订单状态为微信审核成功
                    //微信审核成功 就关闭订单 后续是微信打款过程
                    if ($val['pay_way'] != 3 && $val['pay_way'] != 2) {
                        //非余额支付订单的退款 才改变状态 Herry 20171226
                        OrderService::init()->where(['id'=>$val['id']])->update(['refund_status' => 4, 'status' => 4],false);
                    }
                } else {
                    //如果直接退款失败 可能是没上传商户证书或者商家余额不足等原因
                    //模拟用户申请退款 走通用退款流程 商家可以在后台同意退款 但是商家不可拒绝退款
                    //如果商家拒绝退款 提示未成团订单退款必须同意
                    $data = DB::table('groups as g')
                        ->leftJoin('groups_rule as gr','g.rule_id','=','gr.id')
                        ->where('g.id', $val['groups_id'])->get(['gr.pid']);
                    if ($data) {
                        $refundModule->closeGroupOrderApplyRefund($val, $data[0]->pid, $payPrice, $prop_id);
                    }
                }
            }
        }

        //更新未支付订单
        $where = [
            'groups_id'=>['in',$ids],
            'status'   => 0,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id','groups_id','status'])->toArray();
        foreach ($orderData as $val){
            unset($val['status']);
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id'=>$val['id']])->update($val,false);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170808
     * @desc 添加退款详情
     */
    public function addRefund($order)
    {
        //orderDetail
        $orderDetailService = new OrderDetailService();
        $orderDetailData = $orderDetailService->init()->model->where('oid',$order['id'])->get(['id','product_id'])->toArray();
        $data = [
            'oid' => $order['id'],
            'pid' => $orderDetailData[0]['product_id'],
            'amount' => $order['pay_price'],
            'type' => 0,
            'order_status' => 0,
            'reason' => 0,
            'phone' => 0,
            'remark' => '团购未成团进行退款',
            'imgs' => '',
        ];

        //插入订单退款表
        $orderRefundService = new OrderRefundService();
        $resRefund = $orderRefundService->init('oid', $order['id'])->add($data, false);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc  处理结束时间距离现在的时间
     * @param $time 结束时间戳
     */
    public function dealTime($t)
    {
        $result = [];
        $time = $t-time();
        if ($time>0){
            $result['hour'] = (int)($time/3600);
            $result['minute'] = (int)($time%86400%3600/60);
            $result['sec'] = $time%86400%3600%60;
        }
        foreach ($result as &$item){
            if (strlen($item)<2){
                $item = '0'.$item;
            }
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 团购列表
     * @desc status=0 全部列表，1、未开始，2、正在进行中，3、已结束，4，未开始和正在进行中
     */
    public function getGroupsRuleList($wid,$status=0,$orderBy='', $order='')
    {
        $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getGroupRuleList($wid,$status,$orderBy='', $order='');
        $data[0]['data'] = $this->dealGroupsRule($data[0]['data']);
        return $data;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 处理团购数据
     * @return mixed
     */
    public function dealGroupsRule($data)
    {
        $pids = [];
        if (!$data){
            return $data;
        }
        $pids = $ruleids = [];
        foreach ($data as $val){
            $pids[] = $val['pid'];
            $ruleids[] = $val['id'];
        }

        $res = (new ProductService())->getListById($pids);
        $pData = [];
        foreach ($res as $val){
            $pData[] = [
                'id'        => $val['id'],
                'title'     => $val['title'],
                'img'       => $val['img'],
                'price'     => $val['price'],
            ];
        }
        $pData = $this->dealKey($pData);
        $skus = $this->getSkus($ruleids);
        foreach ($data as &$v){
            $v['skus'] = $skus[$v['id']]??[];
            $temp = [];
            if ($v['skus']){
                foreach ($v['skus'] as $item){
                    $temp[] = $item['price'];
                }
            }
            sort($temp);
            $v['min'] = $temp[0]??0;
            $v['max'] = array_pop($temp)??0;
            $v['groups'] = $this->getDetailByRuleId($v['id']);
            $v['pdata'] = $pData[$v['pid']];
        }
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购信息
     */
    public function getDetailByRuleId($ruleId)
    {
        $groupsService = new GroupsService();
        $groupsDetailService = new GroupsDetailService();
        $memberService = new MemberService();
        $groupsData = $groupsService->getListByRuleId($ruleId);
        $num = $pnum = $mnum = 0;
        $memberData = [];
        $groupsIds = [];
        if ($groupsData){
            foreach ($groupsData as $val) {
                $num = $num+$val['num'];
                $pnum = $pnum+$val['pnum'];
                $groupsIds[] = $val['id'];
            }
            $detailData = $groupsDetailService->getListByWhere(['groups_id'=>['in',$groupsIds]]);
            $mids = [];
            if ($detailData){
                foreach ($detailData as $val){
                    $mnum = $mnum+1;
                    $mids[] = $val['member_id'];
                }
                $res = $memberService->getListById($mids);
                foreach ($res as $val) {
                    $memberData[] = [
                        'id'             =>$val['id'],
                        'headimgurl'    =>$val['headimgurl'],
                        'nickname'      =>$val['nickname'],
                    ];
                }
            }
        }
        $pnum = (new GroupsRuleService())->getProductSoldNum($ruleId);
        $data = [
            'num'   =>$num,
            'pnum'  => $pnum,
            'mnum'  => $mnum,
            'member'=> $memberData,
        ];
        return $data;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取团购数量
     * @param $ruleId
     */
    public function getGroupsNum($ruleId,$mid)
    {
        return (new GroupsService())->getGroupsCount($ruleId,$mid);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取推荐团购
     */
    public function getRecommend($wid)
    {
        $time = date('Y-m-d H:i:s',time());
        $where['start_time'] = ['<=',$time];
        $where['end_time'] = ['>=',$time];
        $where['status'] = 0;
        $where['wid'] = $wid;

        //add by jonzhang 2018-04-08 团购推荐
        $isAuto=false;
        $ids = [];
        $commendData=(new  CommendInfoService())->getListByCondition(['type'=>2,'wid'=>$wid]);
        if($commendData['errCode']==0&&!empty($commendData['data']))
        {
            //定向推荐
            $isAuto=$commendData['data'][0]['is_auto'];
            if ($isAuto)
            {
                $cid=$commendData['data'][0]['id'];
                //按id排序后，查询出来的id,按照id由大到小
                $commendDetailData=(new CommendDetailService())->getListBycondition(['cid'=>$cid,'current_status'=>0],'id','desc',20);
                if($commendDetailData['errCode']==0&&!empty($commendDetailData['data']))
                {
                    $groupIDs=[];
                    foreach($commendDetailData['data'] as $item)
                    {
                        $where['id']= $item['recommendation_id'];
                        //此处查询出来的ID 按照rids中数组的顺序
                        $groupId = (new GroupsRuleService())->model->wheres($where)->get(['id'])->toArray();
                        //$cnt=count($groupIDs);
                        if(!empty($groupId)&&count($groupId)>0)
                        {
                            array_push($groupIDs,$groupId[0]);
                        }
                    }
                    if(count($groupIDs)>0)
                    {
                        $cnt=count($groupIDs);
                        //拼团商品推荐 最多展示四个
                        if($cnt>=4)
                        {
                            for($i=0;$i<4;$i++)
                            {
                                array_push($ids,$groupIDs[$i]);
                            }
                        }
                        else
                        {
                            //1,3条数据时，只显示两个或者一个
                            if($cnt%2!=0)
                            {
                                unset($groupIDs[$cnt-1]);
                                if(count($groupIDs)>0)
                                {
                                    $ids = $groupIDs;
                                }
                            }
                            else
                            {
                                //2条数据
                                if($cnt>0)
                                {
                                    $ids = $groupIDs;
                                }
                            }
                        }
                    }
                }
            }
        }
        //end
        if(!$isAuto)
        {
            $res = (new GroupsRuleService())->model->wheres($where)->get(['id'])->toArray();
            //$ids = [];
            if (count($res) > 4) {
                foreach (array_rand($res, 4) as $val) {
                    $ids[] = $res[$val]['id'];
                }
            } else {
                foreach ($res as $val) {
                    $ids[] = $val['id'];
                }
            }
        }
        $where['id'] = ['in',$ids];
        $data = (new GroupsRuleService())->getList($where);
        $data = $this->dealGroupsRule($data);
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 获取结算信息
     * @param $pid
     * @param string $skuId
     * @return mixed
     */
    public function getSettlementInfo($pid,$skuId='')
    {

        $productService = new ProductService();
        $productData = $productService->getDetail($pid);
        $productData['skuData'] = [
            'img'       => $productData['img'],
        ];
        if ($productData['sku_flag']){
            if (empty($skuId)){
                $result['errCode'] = -1;
                $result['errMsg'] = '商品规格不能为空';
                return $result;
            }
            $propService = new ProductSkuService();
            $skuData = $propService->getSkuDetail($skuId);
            if (!$skuData){
                $result['errCode'] = -2;
                $result['errMsg'] = '商品规格不存在';
                return $result;
            }
            $productData['skuData'] = $skuData;
        }

        if (!$productData['skuData']['img']){
            $productData['skuData']['img'] = $productData['img'];
        }

        $result['errCode'] = 0;
        $result['data'] = $productData;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 随机获取团列表
     * @desc 20171016
     * @param array $ids
     */
    public function groupsList($ids = [],$wid)
    {
        $groupsService = new GroupsService();
        $query = $groupsService->model;
        if ($ids){
            $query = $query->whereNotIn('id',$ids);
        }
        $query = $query->where('status',1)->where('wid',$wid);
        $res = $query->groupBy('rule_id')->inRandomOrder()->get()->take(5)->toArray();
        if (count($res)<5 && !$ids){
            $res2 = $groupsService->model->whereIn('id',$ids)->take(5-count($res))->get()->toArray();
            $res = array_merge($res,$res2);
        }
        $ruleIds = $gids = [];
        foreach ($res as $val){
            $ruleIds[] = $val['rule_id'];
            $gids[] = $val['id'];
        }
        $ruleService = new GroupsRuleService();
        $where['id'] = ['in',$ruleIds];
        $ruleData = $ruleService->getList($where);
        $ruleData = $this->dealGroupsRule($ruleData);
        $res = $this->dealKey($res,'rule_id');
        $now = date('Y/m/d H:i:s',time());
        //获取参团信息
        $result = (new GroupsDetailService())->getGroups($gids);
        $gData = [];
        foreach ($result as $val){
            $gData[$val['groups_id']][] = $val;
        }
        foreach ($ruleData as &$item){
            $item['groupData'] = $res[$item['id']];
            $item['groupData']['member'] = $gData[$item['groupData']['id']]??'';
            /*  拼团结束时间*/
            $start_time = $item['groupData']['open_time'];

            //未成团订单存在时间 取活动配置的小时数 Herry 20171108
            //$end_time = date("Y/m/d H:i:s",(strtotime($start_time)+86400));
            if ($item['expire_hours']) {
                $end_time = date("Y/m/d H:i:s",(strtotime($start_time) + $item['expire_hours'] * 3600));
                if (strtotime($end_time)>strtotime($item['end_time'])){
                    $end_time=$item['end_time'];
                }
            } else {
                $end_time=$item['end_time'];
            }

            $item['groupData']['end_time'] = date('Y/m/d H:i:s', strtotime($end_time));
            $item['now_time'] = $now;
        }
        return $ruleData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $data
     * @param string $key
     * @return array
     */
    public function dealKey($data,$key='id')
    {
        $result = [];
        foreach ($data as $val){
            $result[$val[$key]] = $val;
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $mid
     * @param string $status
     * @return array
     */
    public function getMyGroups($mid,$status='')
    {
        $page = app('request')->input('page')??1;
        $pagesize = config('database.perPage');
        $offset = ($page-1)*$pagesize;
        $sql = 'SELECT g.id,gd.member_id,gd.oid,g.rule_id,gd.is_head,g.identifier,g.status,g.num FROM ds_groups_detail as gd LEFT JOIN ds_groups as g ON gd.groups_id = g.id WHERE gd.member_id='.$mid.' and g.type=1';
        if ($status){
            $sql = $sql.' AND g.`status`= '.$status;
        }
        $sql = $sql.' ORDER BY gd.id DESC  LIMIT '.$pagesize.' OFFSET '.$offset;
        $res = DB::select($sql);
        $result = [];
        foreach ($res as $val){
            $result[] = json_decode(json_encode($val),true);
        }
        return $result;
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171017
     * @desc 我的团购列表
     * @param $status 1:待成团，2：已发货，3：未成团
     */
    public function myGroups($mid,$status='')
    {
        $groupsDetailService = new GroupsDetailService();
        $groups = $this->getMyGroups($mid,$status);
        $gids = $oids = $rids = [];
        foreach ($groups as $val){
            $gids[] = $val['id'];
            $oids[] = $val['oid'];
            $rids[] = $val['rule_id'];
        }
        $where = [
            'id'=> ['in',$rids]
        ];
        $res = (new GroupsRuleService())->getList($where);
        $ruleData = $this->dealGroupsRule($res);
        $temp = [];
        foreach ($ruleData as $val) {
            $temp[] = [
                'id'             =>  $val['id'],
                'ptitle'        => $val['pdata']['title'],
                'pimg'          => $val['pdata']['img'],
                'min'           => $val['min'],
                'max'           => $val['max'],
                'groups_num'   => $val['groups_num'],
            ];
        }
        $ruleData = $this->dealKey($temp);
        $detailData = (new GroupsDetailService())->getListByWhere(['groups_id'=>['in',$gids]]);
        $groupsDetail = [];
        $mids = [];
        if ($detailData){
            $mids = array_column($detailData,'member_id');
        }
        $memberTmp = (new MemberService())->model->whereIn('id',$mids)->select(['id','headimgurl','nickname'])->get()->toArray();
        $memberData = [];
        foreach ($memberTmp as $value){
            $memberData[$value['id']] = $value;
        }
        foreach ($detailData as $val) {
            $memberData[$val['member_id']]['is_head'] = $val['is_head'];
            if (isset($groupsDetail[$val['groups_id']]) && count($groupsDetail[$val['groups_id']])>=4){
                continue;
            }
            $groupsDetail[$val['groups_id']][] = $memberData[$val['member_id']]??'';
        }

        foreach ($groups as &$item){
            $item['rule'] = $ruleData[$item['rule_id']]??[];
            $item['shareData'] = $this->getShareData($item['id']);
            $item['detail'] = $groupsDetail[$item['id']];
        }
        return $groups;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171019
     * @desc 根据id获取
     * @return  errCode = -1,团不存在,，-2：团活动未开始，-3，t团活动已结束，-4团不正常
     */
    public function getRuleById($id)
    {
        $ruleService = new GroupsRuleService();
        $ruleData = $ruleService->getRowById($id);
        if (!$ruleData){
            $result['errCode'] = -1;
            $result['errMsg'] = '团不存在';
            return $result;
        }
        $now = time();
        if (strtotime($ruleData['start_time'])>$now){
            $result['errCode'] = -2;
            $result['errMsg'] = '团活动未开始';
            return $result;
        }
        if (strtotime($ruleData['end_time'])<$now){
            $result['errCode'] = -3;
            $result['errMsg'] = '团活动已结束';
            return $result;
        }
        if($ruleData['status'] != 0){
            $result['errCode'] = -4;
            $result['errMsg'] = '团活动不正常';
            return $result;
        }

        list($data) = $this->dealGroupsRule([$ruleData]);
        $result['errCode'] = 0;
        $result['data'] = $data;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171023
     * @desc 获取假的用户头像
     */
    public function fictitiousMember($groups_id)
    {

        $key = 'fictitious_member_hreadimg:'.$groups_id;
        $headImg = Redisx::GET($key);
        if (!$headImg){
            $sourceData = ['001','002','003','004','005','006','007','008','009','010'];
            $headImgkey = array_rand($sourceData,1);
            $headImg = config('app.url').'hsshop/other/headimg/'.$sourceData[$headImgkey].'.jpg';
            Redisx::SET($key,$headImg);
        }
        return $headImg;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171025
     * @desc 获取拼团信息
     * @param $wid
     */
    public function getGroupsMessage($wid)
    {
        $groupDetailService = new GroupsDetailService();
        $res = $groupDetailService->getGroupsMessage($wid);
        $mids = [];
        foreach ($res as $val){
            $mids[] = $val['member_id'];
        }
        $memberData = (new MemberService())->getListById($mids);
        $memberData = $this->dealKey($memberData);
        $result = [];
        foreach ($res as $val){
            $result[] = [
                'id'         => $val['id'],
                'groups_id' => $val['groups_id'],
                'headimgurl'=> $memberData[$val['member_id']]['headimgurl']??'',
                'nickname'  => $memberData[$val['member_id']]['nickname']??'',
                'sec'       => rand(1,11),
            ];
        }

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 根据团ID获取团分享信息
     * @param $groups_id
     */
    public function getShareData($groups_id)
    {
        $groupsData = (new GroupsService())->getRowById($groups_id);
        $result = [];
        if ($groupsData){
            $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
            if (!$ruleData){
                return $result;
            }
            $result = [
                'share_title'   => $ruleData['share_title']?$ruleData['share_title']:$ruleData['title'],
                'share_desc'    => $ruleData['share_desc']?$ruleData['share_desc']:$ruleData['subtitle'],
                'share_img'     => $ruleData['share_img']?$ruleData['share_img']:$ruleData['img2'],
            ];
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 根据店铺id获取店铺标签
     * @param $wid
     */
    public function getShopLable($wid)
    {
        if (in_array($wid,config('app.li_wid'))){
            return [
                'img'       => 'hsshop/other/headimg/276874300591292416.png',
                'wid'       => $wid,
                'title'       => '服务保障',
                'id'           => 1,
                'content'      =>[
                    [
                        'title'     =>'小程序定制先行者·助力商户畅享小程序流量红利',
                        'content'   => ''
                    ],
                ]
            ];
        }else{
            return [
                'img'       => 'hsshop/other/headimg/276874300591292416.png',
                'wid'       => $wid,
                'title'       => '服务保障',
                'id'           => 1,
                'content'      =>[
                    [
                        'title'     =>'全场包邮',
                        'content'   => 'you家支持全国绝大部分地区包邮（偏远地区除外，如新疆、西藏、内蒙古、宁夏、青海、甘肃等）'
                    ],
                    [
                        'title'     =>'品质保证',
                        'content'   => 'you家精选商城所售商品，直接与大型品牌生产厂家合作，保证商品品质。'
                    ],
                    [
                        'title'     =>'七天无忧退换',
                        'content'   => '买家收到商品后7天内，符合消费者保障法规，可以申请无理由退换货（特殊商品除外，如直接接触皮肤商品、食品类商品、定做类商品、明示不支持无理由退换货商品等）'
                    ]
                ]
            ];
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171027
     * @desc 获取我的参团id
     */
    public function getMyGroupsIds($mid)
    {
        $res = (new GroupsDetailService())->getListByWhere(['member_id'=>$mid]);
        $ids = [];
        if ($res){
            foreach ($res as $val){
                $ids[] = $val['groups_id'];
            }
        }
        return $ids;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171225
     * @desc当前可购买数量
     * @param $mid
     * @param $rule_id
     */
    public function limtNum($mid,$rule_id)
    {
        $groupsRuleService = new GroupsRuleService();
        $ruleData = $groupsRuleService->getRowById($rule_id);
        if ($ruleData['limit_type'] == -1){
            return -1;
        }elseif($ruleData['limit_type'] == 0){
            return $ruleData['num'];
        }elseif($ruleData['limit_type'] == 1){
            $groupsService = new GroupsService();
            $groupsData = $groupsService->getListByRuleId($rule_id);
            if ($groupsData){
                $gids = array_column($groupsData,'id');
                $num = OrderService::getGroupsNum($gids,$mid);
                return $ruleData['num'] - $num;
            }else{
                return $ruleData['num'];
            }
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201820416
     * @desc 获取统计 嘻嘻
     */
    public function getNumInfo($ruleid)
    {
        $ruleData = (new GroupsRuleService())->getRowById($ruleid);
        if (!$ruleData){
            error('活动id错误');
        }
        $productData = Product::select(['id','sold_num'])->find($ruleData['pid']);
        return $productData->sold_num;

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc 创建订单
     * @param $mData
     * @param $addr
     * @param int $groups_id
     * @param string $wid
     * @param int $source
     * @return mixed
     */
    public function createOrder($mData,$addr,$groups_id=0,$wid='',$source=0)
    {
        $request = app('request');
        $num = $request->input('num');
        $remarkNo = $request->input('remark_no','');
        if ($num<=0 || floor($num) != $num){
            $result['errCode'] = -5;
            $result['errMsg'] = '数量必须为正整数!';
            return $result;
        }
        $pid = $request->input('pid');
        $sku_id = $request->input('sku_id')??0;
        $formId = $request->input('formId')??0;
        $orderData['oid'] = OrderCommon::createOrderNumber();
        $orderData['trade_id'] = $orderData['oid'];
        $orderData['wid'] = empty($wid)?$mData['wid']:$wid;
        $orderData['mid'] = $mData['id'];
        $orderData['umid'] = $mData['umid'];
        $orderData['groups_id'] = $groups_id;
        $orderData['source'] = $source;
        $orderData['form_id'] = $formId;
        $res = (new GroupsRuleModule())->getSettlementInfo($pid,$sku_id);
        if ($res['errCode'] != 0){
            return $res;
        }
        if ($groups_id != 0){
            $groupDetailData = (new GroupsDetailService())->getListByWhere(['groups_id'=>$groups_id,'member_id'=>$mData['id']]);
            if ($groupDetailData){
                $result['errCode'] = -3;
                $result['errMsg'] = '您已参加过该团!';
                return $result;
            }
            //判断限购
//            $ruleNum = (new GroupsService())->getLimitNumByGroupsId($groups_id);
            $groupsData = (new GroupsService())->getRowById($groups_id);
            $ruleNum = (new GroupsRuleModule())->limtNum($mData['id'],$groupsData['rule_id']);
            if ($ruleNum>0 && $ruleNum<$num){
                $result['errCode'] = -4;
                $result['errMsg'] = '购买数量超过限购数量!';
                return $result;
            }
            //判断该店铺
            $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
            if ($ruleData['wid'] != $wid){
                $result['errCode'] = -4;
                $result['errMsg'] = '该活动不属于该店铺';
                return $result;
            }
        }

        $res = $res['data'];
        $orderData['address_id'] = $addr['id'];
        $orderData['address_name'] = $addr['title'];
        $orderData['address_phone'] = $addr['phone'];
        $orderData['address_detail'] = $addr['detail'];
        $orderData['address_province'] = $addr['province']['title'];
        $orderData['address_city']  = $addr['city']['title'];
        $orderData['address_area'] = $addr['area']['title'];
        $orderData['type'] = 3;
        $orderData['buy_remark'] = $request->input('remark')??'';
        if ($groups_id){
            $orderData['status'] = 4;
            $orderData['admin_del'] = 1;
            $orderData['groups_status'] = 1;
            $result = (new OrderModule())->getGroupsOrder($groups_id,$num, $orderData['wid'], $orderData['mid'], $orderData['umid'], $addr['id']);
        }else{
            $result = $this->getPriceInfo($pid);
        }

        $orderData['pay_price'] = $result['lastAmount'];
        $orderData['products_price'] = $result['productTotalAmount'];
        $orderData['freight_price'] = $result['freight'];

        $orderData['discount_amount'] = $result['head_discount'];
        $orderData['head_discount'] = $result['head_discount'];
        $orderData['groups_id'] = $groups_id;
        $orderData['use_point'] = 0;


        DB::beginTransaction();
        //创建订单
        $id = Order::insertGetId($orderData);
        $orderData = Order::find($id)->toArray();
        //减库存
        $reduce = (new OrderModule())->reduceStock($pid,$sku_id,$num);
        if (!$reduce){
            $result['errCode'] = -2;
            $result['errMsg'] = '商品库存不足';
            return $result;
        }
        //创建订单详情
        $data = [
            'pid'        => $pid,
            'title'      => $res['title'],
            'img'        => $res['skuData']['img'],
            'oprice'    => $res['oprice'],
            'price'     => $res['price'],
            'num'        =>$num,
            'sku_id'    => $sku_id,
            'skuData'   => $res['skuData']??[],
            'remark_no' => $remarkNo,
        ];
        $orderDetailData =(new OrderModule())->createOrderDetail($orderData,$data);
        //添加订单日志
        $orderLogData = (new OrderModule())->addOrderLog($orderData['id'],$mData['wid'],$mData['id']);
        //存redis
        $orderData['orderDetail'][] = $orderDetailData;
        $orderData['orderLog'][] = $orderLogData;
        OrderService::init()->addR($orderData,false);
        DB::commit();
        $result['errCode'] = 0;
        $result['data'] = $orderData;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180416
     * @desc 创建订单
     */
    public function getPriceInfo($pid)
    {
        $product = Product::find($pid)->toArray();
        return [
            'head_discount'        => 0,
            'productTotalAmount'  => $product['price'],
            'lastAmount'           => $product['price'],
            'freight'               => 0,
            'productPrice'          => $product['price'],
        ];

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180416
     * @desc 统一下单接口
     * @param $id
     * @return array
     */
    public function wechatPay($id)
    {
        $paymentService = new PaymentService();
        $orderList = $paymentService->getOrderList($id);
        // 获取微信支付配置信息
        $conf = (new WeChatAuthModule())->getConf($orderList[0]['wid']);

        // 定义页面展示所需数据
        $detail['tradeId']  = $orderList[0]['trade_id'];
        $detail['payTotal'] = array_sum(array_column($orderList, 'pay_price'));
        //Herry 处理金额精度问题
        $detail['payTotal'] = sprintf("%.2f", $detail['payTotal']);
        $detail['payee']    = $conf['payee'];
        $detail['id']       = $orderList[0]['id'];

        /******* 统一下单(获取预支付交易会话标识) 开始 *******/
        // 公众账号ID 微信支付分配的公众账号ID（企业号corpid即为此appId）
        $parameters['appid']            = $conf['app_id'];
        // 商户号 微信支付分配的商户号
        $parameters['mch_id']           = $conf['mch_id'];
        // 随机字符串 长度要求在32位以内
        $parameters['nonce_str']        = $paymentService->createNoncestr();
        // 商品描述 微信浏览器公众号支付此参数官方文档制定规则：商家名称-销售商品类目
        $parameters['body']             = mb_substr($orderList[0]['orderDetail'][0]['title'], 0, 40, 'utf-8');
        // 附加数据 在查询API和支付通知中原样返回，可作为自定义参数使用
        $parameters['attach']           = implode(',', array_column($orderList, 'id')).'#'.$conf['type'];
        // 商户订单号 商户系统内部订单号，要求32个字符内、且在同一个商户号下唯一
        $parameters['out_trade_no']     = $orderList[0]['id'].'_'.rand(); //拼接随机数，防止修改订单总价后商户订单号重复
        // 标价金额 订单总金额，单位为分
        $parameters['total_fee']        = intval(strval($detail['payTotal'] * 100));
        // 终端IP APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
        $parameters['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        // 交易起始时间 订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010
        // $parameters['time_start'] = '20170401202734';
        // 交易结束时间 注意：最短失效时间间隔必须大于5分钟 订单失效时间，格式为yyyyMMddHHmmss如2009年12月27日9点10分10秒表示为20091227091010
        // $parameters['time_expire'] = '20170401202734';
        // 通知地址 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
        $parameters['notify_url']       = config('app.url') . 'foundation/payment/wechatPayNotify';
        // 交易类型 取值如下：JSAPI，NATIVE，APP等
        $parameters['trade_type']       = 'JSAPI';
        // 买家微信openid
        $parameters['openid']           = $paymentService->getMemberInfo($conf);
        // 签名 通过签名算法计算得出的签名值
        $parameters['sign']             = $paymentService->getSign( $parameters, $conf );
        // 数组转xml
        $xml = $paymentService->arrayToXml($parameters);
        // 以post方式提交xml到统一下单接口
        $result = $paymentService->postXmlCurl($xml, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        // xml转数组
        $result = $paymentService->xmlToArray($result);

        $result['return_code']  = $result['return_code'] ?? '';
        $result['result_code']  = $result['result_code'] ?? '';
        $result['return_msg']   = $result['return_msg'] ?? '未知错误';
        $result['err_code_des'] = $result['err_code_des'] ?? '未知错误';

        if ( $result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS' ) {
            $prepay_id = $result['prepay_id'];
        } elseif ( $result['return_code'] === 'FAIL' ) {
            error($result['return_msg']);
        } elseif ( $result['result_code'] === 'FAIL' ) {
            error($result['err_code_des']);
        } else {
            error('通信失败');
        }
        /******* 统一下单(获取预支付交易会话标识) 结束 *******/

        /******* 设置jsapi的参数 开始 *******/
        // 公众号id 商户注册具有支付权限的公众号成功后即可获得
        $jsApi['appId']     = $conf['app_id'];
        // 时间戳 当前的时间
        $jsApi['timeStamp'] = strval( time() );
        // 随机字符串 不长于32位
        $jsApi['nonceStr']  = $paymentService->createNoncestr();
        // 订单详情扩展字符串 统一下单接口返回的prepay_id参数值，提交格式如：prepay_id=***
        $jsApi['package']   = 'prepay_id=' . $prepay_id;
        // 签名方式 签名算法，暂支持MD5
        $jsApi['signType']  = 'MD5';
        // 签名 通过签名算法计算得出的签名值
        $jsApi['paySign']   = $paymentService->getSign( $jsApi, $conf );
        /* 数组转json */
        $jsApi = json_encode( $jsApi );
        /******* 设置jsapi的参数 结束 *******/

        // 响应支付展示页面
        response()->view('shop.groupsmeeting.payIndex', [
            'jsApi'  => $jsApi,
            'detail' => $detail,
        ])->send();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc 获取拼团
     */
    public function getRemarkPhone($remarkNo)
    {
        $data = (new RemarkService())->getByRemarkNo($remarkNo);
        foreach ($data as $val){
            if ($val['type'] == 7){
                return $val['content'];
            }
        }
        return false;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getRuleTitle($groups_id)
    {
        $groupsService = new GroupsService();
        $groupsRuleService = new GroupsRuleService();
        $groups = $groupsService->getRowById($groups_id);
        if (!$groups){
            return '';
        }
        $ruleData = $groupsRuleService->getRowById($groups['rule_id']);
        return $ruleData['title']??'';
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $groups_id
     * @param $remark_no
     * @param int $type
     */
    public function sendSMS($groups_id,$remark_no,$type=8)
    {
        $phone = $this->getRemarkPhone($remark_no);
        if ($phone){
            $title = $this->getRuleTitle($groups_id);
            \Log::info('发送短信参数:');
            \Log::info([$phone,$title,$type]);
            (new VerifyCodeService())->groupPurchaseNoitice($phone,[$title],$type);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180417
     * @desc 拼团还差多少个人
     */
    public function lackNum($groups_id)
    {
        $groupsService = new GroupsService();
        $groupsRuleService = new GroupsRuleService();
        $groups = $groupsService->getRowById($groups_id);
        if (!$groups){
            return false;
        }
        $ruleData = $groupsRuleService->getRowById($groups['rule_id']);
        return $ruleData['groups_num'] - $groups['num'];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getHeadRemakNo($groups_id)
    {
        $groupsDetailService = new GroupsDetailService();
        $res = $groupsDetailService->getListByWhere(['groups_id'=>$groups_id,'is_head'=>1]);
        return $res[0]['remark_no']??'';
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function isAddGroups($mid,$rule_id='')
    {
        if (in_array($mid,['3847','3976'])){
            return 0;
        }
        if (session('wid') == 634){
            $sql = 'SELECT COUNT(*) as num FROM ds_groups_detail as gd LEFT JOIN ds_groups as g ON gd.groups_id=g.id WHERE g.type=1 AND gd.member_id='.$mid.' AND gd.is_head=0 AND g.id>11142';
        }else{
            $sql = 'SELECT COUNT(*) as num FROM ds_groups_detail as gd LEFT JOIN ds_groups as g ON gd.groups_id=g.id WHERE g.type=1 AND gd.member_id='.$mid.' AND gd.is_head=0';
        }
        $res = DB::select($sql);
        if ($rule_id == '2131'){
            return $res[0]->num - 2;
        }
        return $res[0]->num;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180425
     * @desc 获取留言信息
     */
    public function getRemark($rule_id,$input,$mid=0)
    {
        $remarkService = new RemarkService();
        $field = [
            'g.id',
            'g.identifier',
            'g.rule_id',
            'g.num',
            'g.pnum',
            'g.open_time',
            'g.status',
            'gd.oid',
            'gd.member_id',
            'gd.is_head',
            'gd.remark_no',
            'gd.created_at',
            'g.complete_time',
        ];
        $query = DB::table('groups_detail as gd')
            ->leftJoin('groups as g','gd.groups_id','=','g.id')
            ->orderBy('gd.id','desc')
            ->select($field);

        if ($rule_id){
            $query->where('g.rule_id', $rule_id);
        }
        if (!empty($input['is_head'])){
            $query->where('gd.is_head',$input['is_head']);
        }
        if (!empty($input['goups_id'])){
            $query->where('g.id',$input['goups_id']);
        }
        if (!empty($input['status'])){
            $query->where('g.status',$input['status']);
        }
        if (!empty($input['starttime'])){
            $query->where('gd.created_at','>',$input['starttime']);
        }
        if (!empty($input['endtime'])){
            $query->where('gd.created_at','<=',$input['endtime']);
        }

        if (!empty($input['content'])){
            $where = ['content'=>['like',"%".$input['content']."%"]];
            $remarkData = $remarkService->getList($where);
            $remarkNos = array_column($remarkData,'remark_no');
            $query->whereIn('gd.remark_no',$remarkNos);
        }
        if (is_array($mid)){
            $query->whereIn('gd.member_id',$mid);
        }elseif ($mid){
            $query->where('gd.member_id',$mid);
        }
        $page = $input['page']??1;
        $pageSize = 15;
        $offset = ($page-1)*$pageSize;
        $countQuery = $query;
        $count = $countQuery->count();

        $paginator = new LengthAwarePaginator([], $count, $pageSize, null, ['path' => $this->request->url()]);
        $list = $paginator->appends($input);
        $pageHtml = $list->links();
        if (is_array($mid)){
            $data = $query->get()->toArray();
        }else{
            $data = $query->skip($offset)->take($pageSize)->get()->toArray();
        }

        $data = json_decode(json_encode($data),true);
        $nos = array_column($data,'remark_no');
        $remarkData = $remarkService->getList(['remark_no'=>['in',$nos]]);
        $remarkData = $this->dealRemakData($remarkData,'remark_no');
        foreach ($data as &$item){
            $item['remark'] = $remarkData[$item['remark_no']]??[];
        }
        $result = ['page'=>$pageHtml,'data'=>$data,'count'=>$count];
        return $result;
    }


    function dealRemakData($data,$key="id")
    {
        $result = [];
        foreach ($data as $val){
            $result[$val[$key]][] = $val;
        }
        return $result;
    }


    public function isExistNoEndGroups($mid,$rule_id='',$wid='')
    {
        if ($rule_id){
            $sql = 'SELECT g.id,g.rule_id FROM ds_groups as g LEFT JOIN ds_groups_detail as gd ON g.id=gd.groups_id WHERE g.rule_id='.$rule_id.' AND gd.member_id='.$mid.' AND gd.is_head=1 AND g.`status`=1';
        }else{
            $sql = 'SELECT g.id,g.rule_id FROM ds_groups as g LEFT JOIN ds_groups_detail as gd ON g.id=gd.groups_id WHERE g.wid='.$wid.' AND gd.member_id='.$mid.' AND gd.is_head=1 AND g.`status`=1';
        }
        $res = DB::select($sql);
        if ($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180605
     * @desc 是否弹框
     */
    public function isFrame($mid,$wid)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $key = 'goupsMeeting:isFrame:'.$wid.':'.$mid;
        if ($redisClient->get($key)){
            return true;
        }else{
            $redisClient->SET($key,1);
            $timeOut = strtotime(date("Y-m-d").' 23:59:59')-time();
            $redisClient->EXPIRE($key, $timeOut);
            return false;
        }
    }


}