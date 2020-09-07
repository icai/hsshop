<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/10/11
 * Time: 15:17
 */

namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Model\MeetingTmpLog;
use App\Model\Product;
use App\Module\DistributeModule;
use App\Module\EvaluateModule;
use App\Module\MeetingGroupsRuleModule;
use App\Module\OrderModule;
use App\Module\ProductModule;
use App\Module\WeChatAuthModule;
use App\S\Foundation\VerifyCodeService;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Member\MemberService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductService;
use App\S\Product\RemarkService;
use App\S\Wechat\WeixinLableService;
use App\Services\Order\OrderDetailService;
use OrderService;
use App\Services\ProductEvaluateService;
use App\Services\Shop\MemberAddressService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use PaymentService;
use WeixinService as WweixinService;
use App\S\Weixin\ShopService;

class GroupsMeetingController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 订单支付
     * @desc
     */
    public function index(Request $request){
        $input = $request->input();
        $wechatAuthModule = new WeChatAuthModule();
        $conf = $wechatAuthModule->getConf(session('wid'));
        if ((!config('wechat.is_open_deposit') && $conf['type'] == 2) || $conf['status'] != 1){
            error('店铺未配置或开启微信支付');
        }
        $result = $wechatAuthModule->isAuth(session('wid'),session('umid'));
        if ($result['success']){
            $shopAuth = $wechatAuthModule->shopAuth($result['data'],session('wid'));
            if ($shopAuth){
                return $shopAuth;
            }
        }
        $res = (new MeetingGroupsRuleModule())->wechatPay($input['id']);
        return view('shop.groupsmeeting.payIndex',array(
            'jsApi'  => $res['jsApi'],
            'detail' => $res['detail'],
        ));
    }




    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购详情
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function detail(Request $request,ShopService $shopService,$ruleId)
    {
        //计算佣金
        $wid = session('wid');
        $mid = session('mid');
        $ruleModule = new MeetingGroupsRuleModule();
        $ruleData = $ruleModule->getById($ruleId,$mid);
        if (!$ruleData){
            error('团不存在');
        }

        /*$shopData = (new WeixinService())->init()->model->where('id',$wid)->get()->toArray();
        if ($shopData){
            $shopData = current($shopData);
        }*/
        $shopData = $shopService->getRowById($wid);
        $max = $ruleData['max'];
        if (!$ruleData['max']) {
            $max = $ruleData['min'];
        }
        $tid = $ruleData['distribute_template_id'];
        $distribute = (new DistributeModule())->getProductDistributePrice($shopData,$max,$mid,$tid);
        $ruleData['distribute'] = $distribute[0];
        //end

        $groupsData = $ruleModule->getDetailByRuleId($ruleId);
        $ruleData['pnum'] = $groupsData['pnum'];
        if (strtotime($ruleData['end_time'])<=time()){
            $ruleData['is_over'] = 1;
        }else{
            $ruleData['is_over'] = 0;
        }
        //获取店铺标签
        $wid = session('wid');
        $ruleData['weixinLable'] = $ruleModule->getShopLable($wid);
        $ruleData['surplus'] = $ruleModule->limtNum($mid,$ruleId);
        $ruleData['member'] = (new MemberService())->getRowById(session('mid'));
        $shopData['sign'] = md5(session('wid').session('mid').'huisou');
        $ruleData['shop'] = $shopData;
        //判断状态
        if ($ruleData['status'] == -1){
            $ruleData['state'] = -1; //失效
        }elseif (strtotime($ruleData['start_time'])>=time()){
            $ruleData['state'] = 2; //未开始
        }elseif (strtotime($ruleData['start_time'])<=time() && strtotime($ruleData['end_time'])>=time()){
            $ruleData['state'] = 1; //正在进行中
        }elseif (strtotime($ruleData['end_time'])<=time()){
            $ruleData['state'] = 3; //已过期
        }
        //判断当前用户是否有未完成的团
        $res = $ruleModule->isExistNoEndGroups($mid,$ruleId);

        $ruleData['isAutoFrame'] = 0;
        if ($res){
            $isAutoFrame = $ruleModule->isFrame($mid,$wid);
            $ruleData['isExistGroups'] = 1;
            if (!$isAutoFrame){
                $ruleData['isAutoFrame'] = 1;
            }
        }else{
            $ruleData['isExistGroups'] = 0;
        }
        success('操作成功','',$ruleData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购列表
     */
    public function getGroups($ruleId)
    {
        $ruleModule = new MeetingGroupsRuleModule();
        $result['num'] = $ruleModule->getGroupsNum($ruleId,session('mid'));
        $result['data'] = $ruleModule->getGroups($ruleId,session('mid'));
        success('操作成功','',$result);
    }

    public function getDetailEvaluate($pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $evaluateModule = new EvaluateModule();
        $result['num'] = $productEvaluate->getEvaluateNumByPid($pid);
        $data = $evaluateModule->getProductEvaluate($pid);
        $result['data'] = [];
        if ($data){
            $result['data'] = $data[0];
        }
        success('操作成功','',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取商品评价信息
     * @param $pid
     */
    public function getEvaluateClassify(Request $request,$pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $res = $productEvaluate->getCountByClassify($pid);
        success('操作成功','',$res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取商品评价信息
     * @param Request $request
     * @param $pid
     */
    public function getProductEvaluate(Request $request,$pid)
    {
        $classifyName = $request->input('classifyName')??'';
        $evaluateModule = new EvaluateModule();
        $result = $evaluateModule->getProductEvaluate($pid,$classifyName);
        success('操作成功','',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc
     */
    public function recommendGroups(Request $request)
    {
        $wid = session('wid');
        $res = (new MeetingGroupsRuleModule())->getRecommend($wid);
        success('操作成功','',$res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc  获取团购商品sku
     * @param Request $request
     * @param GroupsSkuService $groupsSkuService
     * @param GroupsRuleService $groupsRuleService
     */
    public function getSkus(Request $request,GroupsSkuService $groupsSkuService,GroupsRuleService $groupsRuleService,$rule_id)
    {
        $ruleData = $groupsRuleService->getRowById($rule_id);
        if (!$ruleData){
            error('团不存在');
        }
        $skus = (new ProductPropsToValuesService())->getSkuList($ruleData['pid']);
        $res = $groupsSkuService->getlistByRuleId($rule_id);
        $groupsSkus = [];
        foreach ($res as $val){
            $groupsSkus[$val['sku_id']] = $val;
        }
        foreach ($skus['stocks'] as &$v){
            $v['price'] = $groupsSkus[$v['id']]['price']??$val['price'];
        }

        success('操作成功','',$skus);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 获取结算信息
     * @param Request $request
     */
    public function getSettlementInfo(Request $request)
    {
        $meetingGroupsRuleModule = new MeetingGroupsRuleModule();
        $groups_id = $request->input('groups_id','');
        $groups_id = intval($groups_id);
        if ($request->input('flag') == 1 || !$groups_id){
            $res = $this->createOrder($request);
           $orderData = $res['data'];
            $meetingGroupsRuleModule->afterOrder($orderData);
            return redirect('/shop/meeting/groupon/'.$orderData['groups_id'].'/'.session('wid'));
        }else{

            if (!$groups_id){
                error('参团数据错误！请重新操作');
            }
            $gdetail = (new GroupsDetailService())->model->where('groups_id',$groups_id)->where('member_id',session('mid'))->first();
            if (!$gdetail){
                $gData = (new GroupsService())->getRowById($groups_id);
                $addGroupsNum = $meetingGroupsRuleModule->isAddGroups(session('mid'),$gData['rule_id']);
                if ($addGroupsNum>0){
                    error('您已帮别人参过团了');
                }
                $res = $this->createOrder($request);
                $orderData = $res['data'];
                (new MeetingGroupsRuleModule())->afterOrder($orderData);
                $lackNum = $meetingGroupsRuleModule->lackNum($orderData['groups_id']);
                if ($lackNum==0){
                    $remakNo = $meetingGroupsRuleModule->getHeadRemakNo($orderData['groups_id']);
                }
                $pid = $orderData['orderDetail'][0]['product_id'];
                $remak_no = $orderData['orderDetail'][0]['remark_no'];
            }else{
                $gdetail = $gdetail->toArray();
                $orderDetail = (new OrderDetailService())->init()->where(['oid'=>$gdetail['oid']])->getInfo();
                $pid=$orderDetail['product_id'];
                $remak_no = $gdetail['remark_no'];
            }
            $result = Product::find($pid);
            if ($result){
                $result = $result->toArray();
            }
            $result['goupos'] = (new GroupsService())->getRowById($groups_id);
            //获取参团信息
            $res = (new GroupsDetailService())->getListByWhere(['groups_id'=>$groups_id], $skip = "", $perPage = "", $orderBy = "is_head", $order = "DESC");
            $mids = array_column($res,'member_id');
            $member = (new MemberService())->model->whereIn('id',$mids)->select(['id','headimgurl','nickname'])->get()->toArray();
            $member = $meetingGroupsRuleModule->dealKey($member);
            $temp = [];
            foreach ($res as $val){
                $temp[] = $member[$val['member_id']];
            }
            $result['member'] = $temp;
            $ruleData  = (new GroupsRuleService())->getRowById($result['goupos']['rule_id']);
            $result['goupos']['lackNum'] = $ruleData['groups_num'] - $result['goupos']['num'];
            //判断当前用户是否有未完成的团

            $res = $meetingGroupsRuleModule->isExistNoEndGroups(session('mid'),$result['goupos']['rule_id']);

            $result['isAutoFrame'] = 0;
            if ($res){
                $isAutoFrame = $meetingGroupsRuleModule->isFrame(session('mid'),session('wid'));
                $result['isExistGroups'] = 1;
                if (!$isAutoFrame){
                    $result['isAutoFrame'] = 1;
                }
            }else{
                $result['isExistGroups'] = 0;
            }
            return view('shop.groupsmeeting.getSettlementInfo',array(
                'title'     =>'感谢您帮TA拼团成功',
                'data'      => $result,
                'remak_no'  => $remak_no,
            ));
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 创建订单
     */
    public function createOrder(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'pid'   => 'required',
            'num'   => 'required',
        );
        $message = Array(
            'pid.required'          => '商品id不能为空',
            'num.required'          => '数量不能为空',
        );
        $input['address_id'] = 0;
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $addressData['id'] = 0;
        $addressData['title'] = $addressData['phone'] = $addressData['detail'] = $addressData['province']['title'] = $addressData['city']['title'] = $addressData['area']['title']='';


        $groups_id = $request->input('groups_id')??0;
        $groups_id = intval($groups_id);
        $meetingGroupsRuleModule = new MeetingGroupsRuleModule();
        if ($request->input('rule_id') && !$groups_id){
            $res = $meetingGroupsRuleModule->createGroups($request->input('rule_id'));
            if ($res['success'] == 0){
                error($res['message']);
            }else{
                $groups_id = $res['data'];
            }
        }

        $mData = (new MemberService())->getRowById(session('mid'));
        $res = $meetingGroupsRuleModule->createOrder($mData,$addressData,$groups_id,session('wid'));
        if ($res['errCode'] != 0){
            error($res['errMsg']);
        }else{
            $result = ['status' => 1, 'info' => '', 'url' => '', 'data' => $res['data'] ];
            return $result;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 参团信息
     */
    public function groupsDetail(Request $request,$groups_id)
    {
        $groupsService = new GroupsService();
        $ruleModule = new MeetingGroupsRuleModule();
        $groupsData = $groupsService->getRowById($groups_id);
        if (!$groupsData){
            error('该团不存在');
        }
        $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
        $res =  $ruleModule->getGroupsById($groups_id,session('mid'));
        if ($res['success'] == 0){
            error($res['message']);
        }
        $result['rule'] = $res['rule'];
        $result['rule']['save'] = bcsub($result['rule']['product']['price'],$result['rule']['min'],2);
        $groups = [];
        $result['groups'] = $res['groups'];
        $result['groups']['is_join'] = 0;
        $mid = session('mid');

        foreach ($res['groupsDetail'] as $val){
            $groups[] = [
                'id'            => $val['id'],
                'groups_id'    => $val['groups_id'],
                'oid'           => $val['oid'],
                'is_head'       => $val['is_head'],
                'headimgurl'    => $val['member']['headimgurl'],
                'nickname'      => $val['member']['nickname'],
                'created_at'    => $val['created_at'],
                'member_id'     => $val['member_id'],
            ];
            if ($mid == $val['member_id']){
                $result['groups']['is_join'] = 1;
                $result['groups']['join_time'] = $val['created_at'];
            }
        }

        //如果手动成团添加虚拟名额
        if ($result['groups']['status'] == 2 && $result['rule']['groups_num']>count($groups)){
            for ($i=0;$i<($result['rule']['groups_num']-count($groups));$i++){
                $groups[] = [
                    'id'            => '',
                    'groups_id'    => '',
                    'oid'           => '',
                    'is_head'       => '0',
                    'headimgurl'    => $ruleModule->fictitiousMember($groups_id),
                    'nickname'      => '',
                    'created_at'    => '',
                ];
            }
        }

        $result['groupsDetail'] = $groups;

        /*  拼团结束时间*/
        $start_time = $result['groups']['open_time'];

        //未成团订单存在时间 取活动配置的小时数 Herry 20171108
        if ($result['rule']['expire_hours']) {
            $end_time = date("Y/m/d H:i:s",(strtotime($start_time) + $result['rule']['expire_hours'] * 3600));
            if (strtotime($end_time)>strtotime($result['rule']['end_time'])){
                $end_time=$result['rule']['end_time'];
            }
        } else {
            $end_time=$result['rule']['end_time'];
        }
        if (strtotime($result['rule']['end_time'])<=time()){
            $result['rule']['is_over'] = 1;
        }else{
            $result['rule']['is_over'] = 0;
        }
        $result['groups']['end_time'] = date('Y/m/d H:i:s', strtotime($end_time));
        $result['groups']['now_time'] = date('Y/m/d H:i:s',time());
        $result['groups']['oid'] = $res['order_id'];
        $result['order'] = $res['order'];
        //获取店铺标签

        $result['weixinLable'] = $ruleModule->getShopLable(session('wid'));
        $result['surplus'] = $ruleModule->limtNum($mid,$groupsData['rule_id']);
        $result['addGroupsNum'] = $ruleModule->isAddGroups(session('mid'),$groupsData['rule_id']);
        $result['groups']['pnum'] = $result['rule']['product']['sold_num'];
        if ($result['rule']['status'] == -1){
            $result['rule']['state'] = -1; //失效
        }elseif (strtotime($result['rule']['start_time'])>=time()){
            $result['rule']['state'] = 2; //未开始
        }elseif (strtotime($result['rule']['start_time'])<=time() && strtotime($result['rule']['end_time'])>=time()){
            $result['rule']['state'] = 1; //正在进行中
        }elseif (strtotime($result['rule']['end_time'])<=time()){
            $result['rule']['state'] = 3; //已过期
        }

        //判断当前用户是否有未完成的团
        $result['isExistGroups'] = 0;
        $res = $ruleModule->isExistNoEndGroups($mid,$result['rule']['id']);

        $result['isAutoFrame'] = 0;

        $tempDetail = current($result['groupsDetail']);
        if ($res && $tempDetail && $tempDetail['member_id'] != session('mid') ){
            $isAutoFrame = $ruleModule->isFrame($mid,session('wid'));

            $result['isExistGroups'] = 1;
            if (!$isAutoFrame){
                $result['isAutoFrame'] = 1;
            }
        }
        success('操作成功','',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 团列表
     */
    public function groupsList(Request $request)
    {
        $ids = $request->input('ids')??[];
        if ($ids){
            $ids = json_decode($ids,true);
        }
        $ruleModule = new MeetingGroupsRuleModule();
        $data = $ruleModule->groupsList($ids,session('wid'));
        success('操作成功','',$data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 我的团购
     */
    public function myGroups(Request $request)
    {
        $ruleModule = new MeetingGroupsRuleModule();
        $data = $ruleModule->myGroups(session('mid'),$request->input('status'));
        success('操作成功','',[$data,session('wid'),'tag'=>1]);
    }

    public function myOrder(Request $request)
    {
        $wid = session('wid');
        $where = [
            'mid'	=> session('mid'),
            'wid'  => $wid,
            'admin_del' => 0
        ];
        $page = $request->input('page')?$request->input('page'):1;
        $pagesize = config('database.perPage');
        $offset = ($page-1)*$pagesize;
        $orderData = OrderService::init('wid',$wid)->model->wheres($where)->orderBy('id','desc')->skip($offset)->take($pagesize)->get() ->load('orderDetail')->load('weixin')->load('orderLog')->toArray();

        success('操作成功','',['order'=>$orderData,'tag'=>2]);
    }


    public function addGroupList()
    {
        return view('shop.groupsmeeting.addGroupList',array(
            'title'     =>'一键参团',
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171025
     * @desc 获取团购消息
     */
    public function getGroupsMessage()
    {
        $result = (new MeetingGroupsRuleModule())->getGroupsMessage(session('wid'));
        success('操作成功','',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 获取分享信息
     * @param $groups_id
     */
    public function getShareData($groups_id)
    {
        $result = (new MeetingGroupsRuleModule())->getShareData($groups_id);
        success('操作成功','',$result);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $id
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function groupsById($id)
    {
        $res = (new GroupsService())->getRowById($id);
        success('操作成功','',$res);
    }

    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function showMyGroups(ShopService $shopService)
    {
        $wid = session('wid');
        //$storeInfo=WweixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = $shopService->getRowById($wid);
        $store=['store_name'=>'','logo_url'=>config('app.source_url').'mctsource/images/m1logo.png'];
        if(!empty($storeInfo)) {
            $store['store_name'] = $storeInfo['shop_name'];
            if (!empty($storeInfo['logo'])) {
            $store['logo_url'] = imgUrl() . $storeInfo['logo'];
            }
        }

        $shareData['share_title'] = $storeInfo['share_title'] ? $storeInfo['share_title'] : $store['store_name'];
        $shareData['share_desc']  = $storeInfo['share_desc'] ? str_replace(PHP_EOL, '', $storeInfo['share_desc']) :''; //去掉换行符
        $shareData['share_img']  = $storeInfo['share_logo'] ? imgUrl() .$storeInfo['share_logo'] : $store['logo_url'];

        return view('shop.groupsmeeting.showMyGroups',array(
            'title'     =>'我的团购',
            'shareData' => $shareData
        ));
    }

    /**
     * 拼团获取运费接口
     */
    public function getFreight(Request $request)
    {
        //检查参数
        $input = $request->input();
        $rule = [
            'pid' => 'required',
            'num' => 'required|integer|min:1',
        ];
        $message = [
            'pid.required'      => '商品id不能为空',
            'num.required'      => '数量不能为空',
            'num.integer'       => '数量必须是正整数',
            'num.min'           =>'最小数量为1',
        ];
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $addressID = $request->input('address_id', 0);

        $groupBuyInfo = [
            [
                'product_id' => $input['pid'],
                'prop_id' => $input['sku_id'],
                'num' => $input['num']
            ]
        ];

        $freight = (new OrderModule())->getFreightByCartIDArr([], session('wid'), session('mid'), session('umid'), $addressID, $groupBuyInfo);

        success('', '', $freight);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180418
     * @desc
     * @param $rule_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail2($rule_id)
    {
        return view('shop.groupsmeeting.detail2',array(
            'title'     =>'拼团详情',
            'rule_id'   => $rule_id,
        ));
    }


    public function groupon2($group_id,MeetingGroupsRuleModule $MeetingGroupsRuleModule)
    {
        $groupsDetailService = new GroupsDetailService();
        $res = $groupsDetailService->getListByWhere(['groups_id'=>$group_id,'is_head'=>0,'member_id'=>session('mid')]);
        if ($res){
            return redirect('/shop/meeting/groups/getSettlementInfo?groups_id='.$group_id);
        }
        return view('shop.groupsmeeting.groupon2',array(
            'title'        =>'拼团详情',
            'group_id'     => $group_id,
            'shareData'    => '',
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180416
     * @desc 获取团购数量
     */
    public function getGroupsNum($ruleid)
    {
        $res = (new MeetingGroupsRuleModule())->getNumInfo($ruleid);
        success('操作成功','',$res);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180416
     * @desc 保存留言
     * @param Request $request
     */
    public function saveRemark(Request $request)
    {
        $input = $request->input();
        $noteList = (new ProductMsgService())->getListByProduct($input['pid']);
        if (!$noteList){
            error('该商品不需要留言');
        }

        $remarkService = new RemarkService();
        $remarkNo = (new ProductModule())->getIdentifier();
        foreach ($noteList as $key=>$val){
            $content = $input['name_'.$val['id']]??'';
            if ($val['required'] && !$content){
                error('请填写'.$val['title']);
            }
            $temp = [
                'remark_no' => $remarkNo,
                'pmid'       => $val['id'],
                'title'     => $val['title'],
                'type'      => $val['type'],
                'content'   => $content,
            ];
            $remarkService->add($temp);
        }
        $data = [
            'mid'           => session('mid'),
            'remark_no'     => $remarkNo,
        ];
        MeetingTmpLog::insertGetId($data);
        success('操作成功','',$remarkNo);
    }



}