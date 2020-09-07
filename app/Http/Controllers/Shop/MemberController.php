<?php

/**
 *  会员中心主页控制器
 *  author Wuxiaoping 2017.03.27
 */

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Model\Favorite;
use App\Model\Member;
use App\Model\MemberCardRecord;
use App\Model\Weixin;
use App\Module\BindMobileModule;
use App\Module\DistributeModule;
use App\Module\FavoriteModule;
use App\Module\MemberCardModule;
use App\Module\ResearchModule;
use App\Module\StoreModule;
use App\S\FavoriteService;
use App\S\Foundation\RegionService;
use App\S\Groups\GroupsDetailService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use Illuminate\Http\Request;
use MemberAddressService;
use MemberCardRecordService;
use MemberCardService;
use OrderService;
use PaymentService;
use ProductService;
use RechargeService;
use MallModule as MemberStoreService;
use Validator;
use WeixinService;
use App\Services\Wechat\ApiService;
use QrCode;
use App\S\Member\MemberCardSyncLogService;
use QrCodeService;
use App\S\BalanceLogService;
use App\S\PublicShareService;
use App\S\BalanceRuleService;
use App\Services\Permission\WeixinRoleService;
use App\S\Weixin\ShopService;
use App\Services\Permission\WeixinUserService;

class MemberController extends Controller
{


    protected $RegionService;
    public function __construct(Regionservice $RegionService, MemberService $memberService) {
        $this->RegionService = $RegionService;
        $this->memberService = $memberService;
    }

    /**
     * todo 移动端会员主页
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-17
     * @update 何书哲 2018年11月21日 添加是否外卖店铺
     **/
    public function index(Request $request,$wid)
    {
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            error('店铺id为空');
        }
        $memberName='';
        $waitPay=0;
        $waitSend=0;
        $waitReceive=0;
        $finish=0;
        $member = [];
        $weixin = Weixin::select('id','is_distribute','distribute_grade')->find($wid)->toArray();
        //预览要传递一个参数
        $preview=$request->input('preview');
        //移动端访问
        if(empty($preview)) {
            $id=session('mid');
            if(empty($id)||empty($wid))
            {
                error('登录超时');
            }
            $memberName='尊贵的用户';
            $memberLogo=config('app.source_url').'mctsource/images/m1logo.png';
            $member = Member::find($id)->toArray();
            if(!empty($member))
            {
                $memberName = $member['nickname'];
                if(!empty($member['headimgurl']))
                {
                    $memberLogo = $member['headimgurl'];
                }
            }
            $data=[];
            $data['wid']=$wid;
            $data['mid']=$id;
            //有些老订单 退款完成没有关闭订单 过滤 Herry 20171226
            $orderStatusInfo=OrderService::getOrderData($data);
            foreach($orderStatusInfo as $item)
            {
                if($item['status']==0)
                {
                    $waitPay=$item['number'];
                }
                else if($item['status']==1)
                {
                    $waitSend=$item['number'];
                }
                else if($item['status']==2)
                {
                    $waitReceive=$item['number'];
                }
                else if($item['status']==3)
                {
                    $finish=OrderService::finishStatus($wid,$id);
                }
            }

            $is_distribute_show = (new DistributeModule())->isShowDistibute($member,session('wid'));
        }

        //添加通用分享设置
        $shareData = (new PublicShareService())->publicShareSet($wid);

        $groupsNum = (new GroupsDetailService())->notCompleteOrderNum(session('mid'));
        $waitSend = $waitSend-$groupsNum;
        $memberInfo = (new WeixinRoleService())->getShopPermission();
        //我的财富按钮进行的操作
        if($member['cash']<=0)
        {
            //判断店铺有没有分销的权限
            if(in_array('merchants/distribute',$memberInfo))
            {
                if($weixin['is_distribute']==0)
                {
                    //没有分销的处理
                    $distribute = 0;
                }else{
                    //分销的处理
                    if ($weixin['distribute_grade'] == 0 || ($weixin['distribute_grade']==1 && $member['is_distribute']==1)){
                        $distribute = 1;
                    }else{
                        $distribute = 0;
                    }

                }

            }else{
                $distribute = 0;
            }

        }else{

            $distribute = 1;
        }

        //对财富眼和二维码的操作
        if(in_array('merchants/distribute',$memberInfo))
        {
            if($weixin['is_distribute']==0)
            {
                $eyeCode =0;
            }else{
                if ($weixin['distribute_grade'] == 0 || ($weixin['distribute_grade']==1 && $member['is_distribute']==1)) {
                    $eyeCode = 1;
                }else{
                    $eyeCode =0;
                }
            }
        }else{
            $eyeCode =0;
        }
        //START  add fuguowei
        //多人拼团路由
        $pingtuan = 'merchants/marketing/togetherGroupList';
        $fightGroups = 0;
        if($memberInfo){
            if(in_array($pingtuan,$memberInfo)){
                $fightGroups = 1;
            }
        }
        //end

        return view('shop.member.index',[
            'wid'          => $wid,
            'member_name'  => $memberName,
            'wait_pay'     => $waitPay,
            'wait_send'    => $waitSend,
            'wait_receive' => $waitReceive,
            'finish'       => $finish,
            'member_logo'  => $memberLogo,
            'member'       => $member,
            'shop'         => $weixin,
            'groupsNum'    => $groupsNum,
            'shareData'    => $shareData,
            'distribute'   => $distribute,
            'eyeCode'      => $eyeCode,
            'fightGroups'  => $fightGroups, //add fuguowei
            'is_distribute_show' =>  $is_distribute_show??0,
            'is_sms'            => (new BindMobileModule())->isShowChangeMobile($wid),
            'takeAwayConfig'    => (new StoreModule())->getWidTakeAway($wid) ? 1 : 0,
        ]);
    }



    /**
     * todo 会员主页json数据
     * @param Request $request
     * @param StoreService $storeService
     * @param $wid
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-24
     * @update 何书哲 2018年8月31日 会员主页获取方法改为processMobileData
     */
    public function indexHome(Request $request,$wid)
    {
        $filter=$request->input('filter')??true;
        $wid=$wid??session('wid');
        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为空';
            return $returnData;
        }
        //return MemberStoreService::processHomeData($wid,$filter);
        return MemberStoreService::processMobileData($wid,2,$filter,session('mid'));
    }

    /**
     * 我的会员卡
     * 可以有几种状（使用中，已到期，已禁用）
     * @author 吴晓平
     * modify by zhangyh 20170512
     **/
    public function myCards(Request $request,$wid)
    {
        $mid = session('mid');
        $cartList = MemberCardRecordService::getMenberCart($mid,$wid);
        $member = $this->memberService->getRowById($mid);
        return view('shop.member.mycards',[
                'cartList'  => $cartList,
                'member'    => $member,
                'title'     => '会员卡列表',
                'shareData' => (new PublicShareService())->publicShareSet($wid)
            ]
        );
    }


    /**
     * 是否有新的会员卡
     * @param Request $request
     * @param MemberCardModule $module
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCard(Request $request)
    {
        $re = MemberCardRecordService::init()->where(['mid' => $mid = $request->session()->get('mid',0),'is_new'=>1])->getInfo();
        success('操作成功','',$re);
    }

    /**
     * 是否有新的会员卡
     * @param Request $request
     * @param MemberCardModule $module
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCardCallBack(Request $request,MemberCardModule $module)
    {
        $mid = $request->session()->get('mid',0);
        $card_record_id = $request->input('recordId',0);
        $re = $module->newMemberCardCallBack($mid,$card_record_id);
        success('操作成功','',$re);
    }


    /**
     * 余额明细
     */
    public function balanceDetail(Request $request)
    {
        return view('shop.member.balanceDetail',[
            'title'   => '余额明细'
        ]);
    }

    /**
     * 余额明细
     */
    public function balanceDetailAjax(Request $request, MemberService $memberService)
    {
        $wid   = session('wid');
        $mid   = session('mid');
        $balanceLogService = new BalanceLogService();
        $type = intval($request->input('type'));
        if ($type != 1 && $type != 2) {
            $type = 0;
        }

        list($list)  = $balanceLogService->getUserLog($wid,$mid,$type);
        $data = [];

        if (!empty($list['data'])) {
            foreach ($list['data'] as $key => $value) {
                $data[$key]['type_name'] = '-';
                $data[$key]['pay_name'] = '支付';
                $data[$key]['pay_way_name'] = '余额支付';

                if ($value['type'] == 1) {
                    $data[$key]['type_name'] = '+';
                    $data[$key]['pay_name'] = '充值成功';
                    $data[$key]['pay_way_name'] = '微信安全支付';
                }

                if ($value['pay_way'] == 4) {
                    $data[$key]['pay_way_name'] = '系统操作';
                }
                if ($value['pay_way'] == 5) {
                    $data[$key]['pay_way_name'] = '系统退款';
                }
                $data[$key]['pay_desc'] = $value['pay_desc'];
                $data[$key]['money'] = $value['money']/100;
                $data[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
            }

        }
        success('', '', $data);
    }

    /**
     * 充值
     */
    public function cardRecharge()
    {
        $mid   = session('mid');
        $wid   = session('wid');

        $member = $this->memberService->getRowById($mid);
        $balanceRule = new BalanceRuleService();
        $ruleList    = $balanceRule->getWidRule($wid);
        return view('shop.member.cardRecharge',[
                'member'    =>  $member,
                'ruleList'  =>  $ruleList,
                'title'     =>  '充值'  
            ]
        );
    }

    /**
     * 充值记录
     */
    public function rechargeRecord(Request $request)
    {
        return view('shop.member.rechargeRecord',[
            'title'   => '充值记录'
        ]);
    }

    public function addBalance($wid, $money)
    {
        $wid   = session('wid');
        $mid   = session('mid');
        $balanceLogService = new BalanceLogService();
        $balanceId = $balanceLogService->insertLog($wid, $mid, $money);
        return  ['errCode' => 0, 'data' => 'balance'.$balanceId];
    }

    /**
     * 会员卡详情
     * @author 吴晓平
     * updated 梅杰 20180703 增加会员会员卡有效期限
     * updated 梅杰 20180704 会员卡领取时间格式修改为不要时分秒
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     **/
    public function cardDetail(Request $request,ShopService $shopService,$wid,$card_id)
    {
        $mid = session('mid');
        $member_power = [];
        $power_explain = '';
        $memberCardDetail = MemberCardService::getRowById($card_id);
        $powerArr = explode(',', $memberCardDetail['member_power']);
        if (in_array(1, $powerArr)) {
            $member_power['free-shipping'] = '包邮';
            $power_explain .= '享受会员包邮 ';
        }

        if (in_array(2, $powerArr)) {
            $member_power['discount'] = $memberCardDetail['discount'] . '折';
            $power_explain .= '会员折扣' . $memberCardDetail['discount'] . '折<br>';

        }
        if (in_array(3, $powerArr)) {
            $member_power['coupon'] = '';

        }

        if(in_array(4,$powerArr)){
            $member_power['score'] = '赠送'.$memberCardDetail['score'].'积分';
            $power_explain .= '随卡获赠积分'.$memberCardDetail['score'].'分 ';

        }
        $memberCardDetail['card_id'] = $card_id;
        $memberCardDetail['power_explain'] = $power_explain;
        $memberCardDetail['member_power'] = $member_power;
        list($cardExtra) = MemberCardRecordService::init('mid', $mid)->select(['card_num', 'is_default', 'active_status'])->where(['mid' => $mid, 'card_id' => $card_id])->getList();
        //组装数据
        $memberCardDetail['card_num'] = $cardExtra['data'][0]['card_num'] ??'';
        $memberCardDetail['is_default'] = $cardExtra['data'][0]['is_defult'] ??'';
        $memberCardDetail['active_status'] = $cardExtra['data'][0]['active_status'] ??'';

        //$storeData = WeixinService::getStageShop($wid);
        $storeData = $shopService->getRowById($wid);
        $storeData['member_card_detail'] = $memberCardDetail;

        //查看是否有同步记录
        //add by zhangyh 20170515
        $show = true;
        $card = MemberCardService::getCard($card_id);
        if($card['state'] != 1)
        {
            $show = false;
        }
        //查看是否有同步记录

        $memberCardSyncLogService = new MemberCardSyncLogService();
        $cardSyncLog = $memberCardSyncLogService->getRowByWhere(['mid' => $mid,'card_id' => $card['card_id']]);
        //已同步后不再显示同步二维码
        if($cardSyncLog){
            $show = false;
        }
        $member = $this->memberService->getRowById(session('mid'));
        $tag = $request->input('tag')??0;
        $record = [];
        if ($request->input('id')) {
            $record = MemberCardRecordService::init('mid', session('mid'))->model->find($request->input('id'))->toArray();
        }
        if (!$record) {
            $list = MemberCardRecordService::getMenberCart($mid,$wid);
            foreach ($list as $val) {
                if ($val['card_id'] == $card['id'] && ($val['state'] == 1 || $val['state'] == 2)) {
                    $record = $val;
                    break;
                }
            }
        }
        if ($record) {
            $record['created_at'] = date('Y-m-d',strtotime($record['created_at'] ));
            $record['time'] = (new MemberCardModule)->getCardExpire($card,$record);
        }
        $coupons = json_decode($card['coupon_conf'], 1);
        $couponData = [];
        //获取所有的优惠券信息
        if ($coupons){
            $couponService = new CouponService();
            foreach ($coupons as $coupon) {
                $couponData[] = $couponService->getDetail($coupon['coupon_id']);
            }
        }
        //$store = WeixinService::init()->getInfo($wid);
        $logo = $storeData['logo'] ? $storeData['logo'] : 'hsshop/image/static/m1logo.png';
        $shop_name = $storeData['shop_name'];;
        return view('shop.member.detail',[
                'card'       => $card,
                'member'     => $member,
                'tag'        => $tag,
                'record'     => $record,
                'couponData' => $couponData,
                'show'       => $show,
                'wid'        => session('wid'),
                'logo'       => imgUrl($logo),
                'title'      => '会员卡详情',
                'shareData'  => (new PublicShareService())->publicShareSet(session('wid')),
                'shop_name'  => $shop_name
            ]
        );
    }

    /**
     * [同步会员卡的二维码入口]
     * @param  [int] $card_id  [会员卡的id]
     * @return [type]          [description]
     */
    public function cardQrcodeCreated($card_id)
    {
        $wid = session('wid');
        $card = MemberCardService::getCard($card_id);
        $apiService = new ApiService();
        //增加一个同步会员卡的二维码入口
        $result['action_name']                           = 'QR_CARD';
        $result['expire_seconds']                        = 1800;
        $result['action_info']['card']['card_id']        = $card['card_id'];
        $result['action_info']['card']['is_unique_code'] = false;
        $result['action_info']['card']['outer_str']      = (string)session('mid');
        $result = $apiService->qrcodeCreated($wid,$result);
        try{
            if( $result['errcode'] == 0 ){
                $url = $result['show_qrcode_url'];
                success('',$url);
            }else{
                error('数据异常');
            }
        }catch (Exception $e){
            $message = $e->getMessage();
            error($message);
        }

    }

    /**
     * 设置默认会员卡
     * @author 吴晓平
     */
    public function setDefault(Request $request,$wid,$card_id)
    {
         /*add by zhangyh 20170516*/
         $mid = session('mid');
         $res = MemberCardRecordService::init('wid',session('wid'))->model->where(['mid'=>$mid,'is_default'=>1])->get(['id'])->toArray();
         if ($res){
             foreach ($res as $val){
                MemberCardRecordService::init('wid',session('wid'))->where(['id'=>$val['id']])->update(['id'=>$val['id'],'is_default'=>0],false);
             }
         }
         MemberCardRecordService::init('wid',session('wid'))->where(['id'=>$card_id])->update(['id'=>$card_id,'is_default'=>1]);

    }

    /**
     * 会员卡删除
     * @author 吴晓平
     * @update 梅杰 2018年9月18日 删除时同步修改member表会员标识
     **/
    public function cardDelete(Request $request,$wid,$card_id)
    {
        $mid       = $request->session()->get('mid');
        if ((new MemberCardModule())->deleteMemberCard($mid,$card_id)) {
            success('删除成功');
        }
        error();
    }

    /**
     * [setCard 会员设置页面]
     */
    public function memberSet($wid)
    {
        $wid = $wid??session('wid');
        $regions = $this->RegionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
        $membersData = $this->memberService->getRowById(session('mid'));

        return view('shop.member.set',
                [
                    'regions_data' => json_encode($regionList),
                    'regionList'   => $regionList,
                    'provinceList' => $provinceList,
                    'member'      => $membersData,
                    'wid'          => $wid,
                    'title'        => '会员设置',
                    'shareData'     => (new PublicShareService())->publicShareSet($wid)
                ]
            );
    }

    //会员资料的添加与编辑,会员卡激活
    public function save(Request $request)
    {
        $mid = session('mid');
        $wid = $wid??session('wid');
        //设置资料必填字段判断
        $data  = $request->input();
        $rule = [
            'name'            => 'required',
            'gender'             => 'required',
            'member_province' => 'required',
            'member_city'     => 'required',
            'member_county'   => 'required'
        ];
        $message = [
            'name.required'            => '请输入姓名',
            'gender.required'             => '请输入性别',
            'member_province.required' => '请选择省',
            'member_city.required'     => '请选择市',
            'member_county.required'   => '请选择区'
        ];
        $validator = Validator::make($data,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $saveData['truename']    = $data['name'];
        $saveData['wechat_id']   = $data['weixin']??'';
        $saveData['sex']         = $data['gender'];
        $saveData['province_id'] = $data['member_province'];
        $saveData['city_id']     = $data['member_city'];
        $saveData['area_id']     = $data['member_county'];
        $saveData['birthday']     = $data['birthday'];

        //是否有会员存档（判断用户是否关注该店铺的公众号）
        $res = $this->memberService->updateData($mid,$saveData);
        //保存统一的id
        $umData = [
            'truename'      => $data['name'],
            'nickname'      => $data['name'],
            'sex'           => $data['gender'],
        ];
        (new UnifiedMemberService())->update(session('umid'),$umData);
        if($res){  //更新
            if (isset($data['tag']) && $data['tag'] == 1){
                $id = $request->input('record_id');
                $res = MemberCardRecord::find($id);
                if (!$res){
                    return myerror('会员卡不存在');
                }
                $res = $res->toArray();
                $res['active_status'] = 1;
                //如果同步了微信卡包，同步激活
                $cardData = MemberCardService::getRowById($res['card_id']);
                if($cardData['is_sync_wechat'] == 1 )
                {
                    $card_id = $cardData['card_id'];
                    $memberCardSyncLogService = new MemberCardSyncLogService();
                    $logData = $memberCardSyncLogService->getRowByWhere(['mid'=>$mid,'card_id'=>$card_id]);
                    if(!empty($logData) && !empty($logData[0]['code'])) {
                        $logData = $logData[0];
                        $apiService = new ApiService();
                        $activeData['code'] = $logData['code'];
                        $activeData['membership_number'] = $res['card_num'];
                        $re = $apiService->activeCard($wid,$activeData);
                        if($re['errcode'] != 0)
                            error('','',$re['errmsg']);
                    }
                }
                MemberCardRecordService::init('mid',$mid)->where(['id'=>$id])->update($res,false);
            }
            success('设置成功','/shop/member/mycards/'.$wid);
        }else{
            error();
        }
    }

    /**
     * [领取会员卡]
     * @author 吴晓平
     * @param  Request $request [注入类]
     * @param  [int]  $wid     [店铺id]
     * @param  [int]  $card_id [会员卡id]
     * @return [type]          [返回提醒]
     * @update 张永辉  2018年6月27  领取成功会员卡连接错误bug修改
     */
    public function getCardAction(Request $request,$wid,$encrypt_cardId)
    {
        $cardModule = new MemberCardModule();
        $mid = $request->session()->get('mid');
        $card = $cardModule->getMemberCard($encrypt_cardId,$mid,$wid);
        $memberCardData = MemberCardService::getCard($encrypt_cardId);
        switch ($card['err_code']) {
            case 0:
                if(isset($card['is_renew'])) {
                    //续期
                    $activeFlag = $memberCardData['is_active'] && !$card['data']['active_status'];
                    $url = !$activeFlag ? "shop/member/detail/".$wid.'/'.$encrypt_cardId : 'shop/member/detail/'.$wid.'/'.$encrypt_cardId."?id=".$card['data']['id'];
                    success('已成功领取会员卡',$url,['is_active'=>$card['data']['active_status']]);
                }else {
                 //第一次领取
                    $url = $card['data']['is_active'] == 1 ?
                        'shop/member/detail/'.$wid.'/'.$encrypt_cardId."?id=".$card['data']['record_id'] :
                        "shop/member/complete";
                    success('已成功领取会员卡',$url,['is_active'=>$card['data']['is_active']]);
                }
                break;
            case 1:
                //会员卡已经删除
                $url = 'shop/index/'.$wid;
                success('该会员卡已被删除',$url);
                break;
            case 2:
                $activeFlag = $memberCardData['is_active'] && !$card['data']['active_status'];
                $url = !$activeFlag ? "shop/member/detail/".$wid.'/'.$encrypt_cardId : 'shop/member/detail/'.$wid.'/'.$encrypt_cardId."?id=".$card['data']['id'];
                success('您已经领取过该会员卡',$url,['is_active'=>$card['data']['active_status']]);
                break;
            default:
                break;
        }

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170516
     * @desc
     * @param Request $request
     */
    public function cardActive(Request $request,RegionService $regionService)
    {
        $regions = $this->RegionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
        $membersData = $this->memberService->getRowById(session('mid'));
        return view('shop.member.cardActive',
            [
                'title'         => '会员卡',
                'regions_data' => json_encode($regionList),
                'regionList'   => $regionList,
                'provinceList' => $provinceList,
                'member'        => $membersData,
                'record_id'     => $request->input('id'),
                'shareData'     => (new PublicShareService())->publicShareSet(session('wid'))

            ]
        );

    }

    /**
     * [successReceiveCard 成功领取会员卡]
     * @author 吴晓平
     * @return [type] [页面显示]
     */
    public function successReceiveCard()
    {
        return view('shop.member.successReceiveCard',
                ['title'=>'成功领取会员卡']
            );
    }

    /**
     * [商城后台发卡链接]
     * @param  [int] $wid            [微商城前台页面要传递的店铺id]
     * @param  [string] $encrypt_cardId [会员卡加密的字符串]
     * @author   <[吴晓平]>
     * redirect跳转
     */
    public function receiveCard($wid,$encrypt_cardId)
    {
        //未登录的情况下跳转到登录页面
        if(!session('mid')){
            return redirect('/auth/login');

        }else{
            //对会员卡id进行解密
            $card_id = idencrypt($encrypt_cardId,false);
            if(!$card_id){
                error();
            }
            //生成要跳转的url地址
            $url = '/member/detail';
            $redirectUrl = urlencrypt($card_id,$url,false);
            return redirect($redirectUrl);
        }
    }

    /**
     * 会员卡充值生成充值订单信息，返回相关支付数据
     * @author  吴晓平
     * @param  [int] $wid            [店铺id]
     * @param  [string] $encrypt_cardId [加密的会员卡字符串]
     * @param  [string] $encrypt_money  [加密的要充值的金额]
     * @return [array] $payArr        [支付的相关数据]
     */
    public function recharge($wid,$encrypt_cardId,$encrypt_money)
    {
        $mid = session('mid');
        $card_id = idencrypt($encrypt_cardId,false);
        if(!$card_id){
            error();
        }
        list($cardExtra)  = MemberCardRecordService::init('mid',$mid)->where(['mid'=>$mid,'card_id'=>$card_id])->getList(false);
        if(!$cardExtra['data']){
            error();
        }

        //定义充值订单数据
        $rechargeData = [];
        //定义充值订单日志数据
        $rechargeLogDatas = [];

        $rechargeData['recharge_sn'] = $wid.$mid.date('YmdHis').rand(100000,999999); //订单编号
        $rechargeData['wid']         = $wid;
        $rechargeData['mid']         = $mid;
        $rechargeData['card_id']     = $card_id;
        $rechargeData['money']       = idencrypt($encrypt_money,false);


        $rechargeLogDatas['wid'] = $wid;
        $rechargeLogDatas['mid'] = $mid;
        $rechargeLogDatas['remark'] = '会员卡充值';
        RechargeService::init('mid',$mid)->getInfo();

        $data = RechargeService::createOrder($wid,$mid,$rechargeData,$rechargeLogDatas);

        if(empty($data)){
            error('操作异常，返回重新操作');
        }

        $payData['trade_id'] = $data['recharge_sn'];
        $payData['pay_price'] = $data['money'];
        $payData['mid'] = $data['mid'];
        $payData['mid'] = $data['mid'];
        $payData['freight_price'] = 0;
        $payData['orderDetail'][0]['title'] = '会员卡充值';
        $url = '/shop/member/rechargepay';
        success('操作成功',$url ,$payData);
    }

    /**
     * [选择支付方式进行支付]
     * @param  Request $request [注入request类]
     * @return [type]           [description]
     */
    public function rechargePay(Request $request)
    {
        $mid = session('mid');
        $payType = $request->input('type');  //支付方式
        $payDataJson = $request->input('data'); //支付相关数据
        $payData = json_decode($payDataJson,true);

        $payArr = [
            1=>'wechatPay',
            2=>'alipay',
            3=>'balancePay',
            9=>'cardPay'
        ];

        PaymentService::$payArr[$payType]([$payData]);

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     * @param Request $request
     * @update 何书哲 2018年7月30日 若umid不存在，则为0
     */
    public function addressAdd(Request $request,RegionService $regionService)
    {
        $input = $request->input();
        if ($request->isMethod('post')){
            $rule = Array(
                'title'       => 'required',
                'province_id' => 'required',
                'city_id'     => 'required',
                'area_id'     => 'required',
                'address'     => 'required',
                'phone'       => 'required|regex:mobile',
                'type'        => 'required|in:0,1',
            );
            $message = Array(
                'title.required'       => '请填写收货人地址',
                'province_id.required' => '请选择省',
                'city_id.required'     => '请选择市',
                'area_id.required'     => '请选择区',
                'address.required'     => '请填写详细地址',
                'phone.required'       => '请填写手机号码',
                'phone.regex'          => '手机号码不正确',
                'type.required'        => '是否默认收货地址不能为空',
                'type.in'              => '默认收货地址只能是0和1',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $input['mid'] = session('mid');
            $input['umid'] = session('umid')??0;//何书哲 2018年7月30日 若umid不存在，则为0
            //是否存在收货地址
            list($res) = MemberAddressService::init()->where(['umid'=>$input['umid']])->getList();
            if (empty($res['data'])){
                $input['type'] = 1;
            }

            if ($input['type'] == 1){
                list($data) = MemberAddressService::init()->where(['type'=>1,'umid'=>$input['umid']])->getList(false);
                $up = [];
                foreach ($data['data'] as $val){
                    MemberAddressService::init()->where(['id'=>$val['id']])->update(['type'=>0],false);
                }
            }

            $addressData = [
                'mid'            => $input['mid'],
                'title'          => $input['title'],
                'province_id'   => $input['province_id'],
                'city_id'        => $input['city_id'],
                'area_id'        => $input['area_id'],
                'address'        => $input['address'],
                'phone'          => $input['phone'],
                'type'           => $input['type'],
                'zip_code'       => $input['zip_code']??'',
                'umid'           => session('umid')??0,//何书哲 2018年7月30日 若umid不存在，则为0
            ];

            if (isset($input['id']) && !empty($input['id'])){
                $res = MemberAddressService::init()->model->where('id',$input['id'])->update($addressData);
                if ($res){
                    $data = MemberAddressService::init()->model->find($input['id'])->load('province')->load('city')->load('area')->toArray();
                    MemberAddressService::init()->where(['id'=>$input['id']])->updateR([$data],[],false);
                    success('操作成功');
                }
            }else{
                $id = MemberAddressService::init()->add($addressData,false);
                $input['id'] = $id;
                success('','',$input);
            }

        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291058
     * @desc 收货地址列表
     * @param Request $request
     * @update 何书哲 2018年7月30日 如果是支付宝小程序,则按mid来搜索
     * @update 许立 2018年10月16日 百度小程序来源处理
     */
    public function addressList()
    {
        //何书哲 2018年7月30日 如果是支付宝小程序,则按mid来搜索
        if (!is_null(session('reqFrom')) && in_array(session('reqFrom'), ['aliapp', 'baiduapp'])) {
            list($memberAddressData) = MemberAddressService::init()->where(['mid'=>session('mid')])->getList(false);
        } else {
            list($memberAddressData) = MemberAddressService::init()->where(['umid'=>session('umid')])->getList(false);
        }
        success('操作成功','',$memberAddressData);
    }

    /**
     * @auth 陈文豪最后一次更新
     * @date 201703291058
     * @desc 同步选择微信地址
     * @param Request $request
     * @update 处理上海北京问题 2018年08月22日 陈文豪
     * @update 处理某些显市区不存在导致同步失败问题 2018年10月20日 梅杰
     */
    public function addressAddFormWechat(Request $request)
    {
        $returnData   = ['errCode'=>0,'errMsg'=>'','data'=>[]];
        $province     = $request->input('province');
        $city         = $request->input('city');
        $area         = $request->input('area');
        $detail       = $request->input('detail');
        $name         = $request->input('userName');
        $tel          = $request->input('telNumber');
        $postCode     = $request->input('postalCode');
        $province = preg_replace('/(省|市|特别行政区|自治区)/','',$province);
        $province_id = $city_id = $area_id = 0;
        if ($flag = in_array($province,['北京','上海']) ) {
            $city = $area;
            $area = '其他';
        }

        //省份信息
        $provinceData = $this->RegionService->getRowByTitle($province,0);
        $provinceData && ($province_id = $provinceData['id']);

        //市级信息
        $cityData = $this->RegionService->getRowByTitle($city,1);
        $cityData && ($city_id = $cityData['id']);

        //区或镇信息
        $areaData = $this->RegionService->getRowByTitle($area,2);
        if (!$flag && !$areaData) {
            //不存在则完善信息
            $areaData['id'] = $this->RegionService->add(['title' => $area ,'pid' => $cityData['id'] ,'level' => 2]);
        }

        $areaData && ($area_id = $areaData['id']);

        if ($province_id && $city_id && $area_id) {
            $addressData['umid']        = session('umid');
            $addressData['mid']         = session('mid');
            $addressData['title']       = $name;
            $addressData['province_id'] = $province_id;
            $addressData['city_id']     = $city_id;
            $addressData['area_id']     = $area_id;
            $addressData['address']     = $detail;
            $addressData['phone']       = $tel;
            $addressData['zip_code']    = $postCode;
            list($memberAddressData) = MemberAddressService::init()->where($addressData)->getList(false);
            if (empty($memberAddressData['data'])) {
                $addressId = MemberAddressService::init()->add($addressData,false);
                if ($addressId) {
                    $returnData['data']['address_id'] = $addressId;
                }else{
                    $returnData['errCode'] = -2;
                    $returnData['errMsg'] = '同步微信地址失败';
                }
            }

        }else {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $city.'地区选择不完整,请手动添加';
        }

        return $returnData;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291347
     * @desc 删除收货地址
     * @param Request $request
     */
    public function addressDel(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'            => '请选择删除的地址',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        MemberAddressService::init()->where(['id'=>$input['id']])->delete($input['id']);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291400
     * @desc 设置默认地址
     * @param Request $request
     * @update 何书哲 2018年7月31日 添加支付宝小程序设置默认地址，更新默认地址
     * @update 许立 2018年10月16日 百度小程序来源处理
     */
    public function addressDefault(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'            => '请选择设置默认的地址',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //何书哲 2018年7月31日 添加支付宝小程序设置默认地址
        if (!is_null(session('reqFrom')) && in_array(session('reqFrom'), ['aliapp', 'baiduapp'])) {
            list($data) = MemberAddressService::init()->where(['mid'=>session('mid')])->getList(false);
        } else {
            list($data) = MemberAddressService::init()->where(['type'=>1,'umid'=>session('umid')])->getList(false);
        }
        foreach ($data['data'] as $val)
        {
            MemberAddressService::init()->where(['id'=>$val['id']])->update(['type'=>0],false);
        }
        //何书哲 2018年7月31日 更新默认地址
        if (!is_null(session('reqFrom')) && in_array(session('reqFrom'), ['aliapp', 'baiduapp'])) {
            MemberAddressService::init()->where(['id'=>$input['id'],'mid'=>session('mid')])->update(['type'=>1]);
        } else {
            MemberAddressService::init()->where(['id'=>$input['id'],'umid'=>session('umid')])->update(['type'=>1]);
        }
    }

    /**
     * 优惠券某用户领取页面
     * @param $status string 状态 valid | invalid
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function coupons(ShopService $shopService,$wid, $status)
    {
        return view('shop.member.coupons',array(
            'title'         => '优惠列表',
            'shop'          => $shopService->getRowById($wid),
            'wid'           => $wid,
            'status'        => $status,
            'shareData'     => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /**
     * 我的优惠券领取列表
     */
    public function couponList($wid, $status)
    {
        empty($status) && error('参数不完整');
        $list = (new CouponLogService())->getCoupons($status, $wid, session('mid'));
        success('', '', $list);
    }

    /**
     * 优惠券详情
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponDetail(ShopService $shopService,$wid, $id)
    {
        //获取优惠券使用记录
        $couponLog = (new CouponLogService())->getDetail($id);
        $couponLog || error('优惠券使用记录不存在');

        //获取优惠券详情
        $coupon = (new CouponService())->getDetail($couponLog['coupon_id']);
        $coupon || error('优惠券不存在');

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        $coupon['shop_name'] = $shop['shop_name'] ?? '';
        $coupon['shop_logo'] = $shop['logo'] ?? '';

        $isValid = ($couponLog['status'] || $couponLog['end_at'] <= date('Y-m-d H:i:s')) ? 0 : 1;
        $invalidText = '';
        if (!$isValid) {
            if ($couponLog['status']) {
                $invalidText = '已使用';
            } elseif ($couponLog['end_at'] <= date('Y-m-d H:i:s')) {
                $invalidText = '已过期';
            }
        }

        return view('shop.member.couponDetail',array(
            'title'     => '优惠券详情',
            'data'      => $coupon,
            'wid'       => $wid,
            'id'        => $id,
            'isValid'   => $isValid,
            'invalidText' => $invalidText,
            'shareData' => (new PublicShareService())->publicShareSet($wid),
            'my_coupon' => $couponLog, //我的优惠券详情要使用我领取的优惠券
        ));
    }

    /**
     * 优惠券指定商品列表
     */
    public function couponProducts(ShopService $shopService,$wid, $id)
    {
        //获取优惠券使用记录
        $couponLog = (new CouponLogService())->getDetail($id);
        $couponLog || error('优惠券使用记录不存在');
        $couponLog['range_type'] || xcxerror('该优惠券适用于全店铺商品');

        return view('shop.member.couponProducts',array(
            'title'     => '优惠券详情',
            'wid'       => $wid,
            'id'        => $id,
            'shop'      => $shopService->getRowById($wid),
            'list'      => ProductService::getListById(explode(',', $couponLog['range_value'])),
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170601
     * @desc 开启财富眼
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function isOpenWeath()
    {
        $mid = session('mid');
        $member = Member::find($mid)->toArray();
        if ($member['is_open_weath'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        $res = Member::where('id',$mid)->update(['is_open_weath'=>$status]);
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }
    }

    public function complete()
    {
           return view('shop.member.complete',array(
            'title'  => '领取成功',
            'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }

    /**
     * Author: MeiJay
     * @param $wid
     * @param $card_id
     * @return array
     */
    public function checkCard($wid,$card_id)
    {
        $data = ['msg'=>'', 'status'=> 0];
        $mid = session('mid');
        $where = [
            'wid'=>$wid,
            'mid'=>$mid,
            'card_id'=>$card_id,
        ];
        //判断是该会员卡是否已经被删除
        $re = MemberCardService::getCard($card_id);
        if($re['state'] != 1) {
             return ['msg'=>'该会员卡已经被禁用或者删除', 'status'=> 1];
        }
        //判断是否已经领取过
        $re = MemberCardRecordService::init()->where($where)->getInfo();
        if($re && $re['status'] == 1) {
             return ['msg'=>'您已经领取过该会员卡', 'status'=> 2];
        }
        if($re && $re['status'] == 0) {
            return ['msg'=>'删除会员卡后再次领取', 'status'=> 3,'data'=> $re];
        }
        return $data;
    }

    /**
     * wuxiaoping 2017.09.05 会员主页生成分销二维码*
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function distributionExplan(Request $request)
    {
        //定义一个水印png文件
        $path       = public_path('hsshop/image/qrcodes/');
        $wid = session('wid');

        $url       = URL('/shop/member/distributionRedirect');
        $qrcodeUrl = $url.'?_pid_='.session('mid').'&wid='.$wid;   //设置分销pid

        $member    = $this->memberService->getRowById(session('mid'));
        if (!file_exists($path.'distribution/'.$wid.'/'.session('mid').'/qrcode.png')) {
            $logo      = ''; //设置水印logo
            if(!empty($member))
            {
                if(!empty($member['headimgurl']))
                {
                    $memberLogo = $member['headimgurl'];
                    //读取远程文件
                    $content    = $this->http_get_imgData($memberLogo);
                    //创建目录
                    if(!file_exists($path.'distribution/'.$wid.'/'.session('mid'))){
                        mkdir(iconv("UTF-8", "GBK", $path.'distribution/'.$wid.'/'.session('mid')),0777,true);
                    }
                    //定义文件名
                    $filename   = iconv("UTF-8", "GBK",$path.'distribution/'.$wid.'/'.session('mid').'/logo.png');
                    $fp         = @fopen($filename,"w+"); //将文件绑定到流 (以读写的模式写入)
                    fwrite($fp,$content); //写入文件
                    $logo       = '/public/hsshop/image/qrcodes/distribution/'.$wid.'/'.session('mid').'/logo.png';
                }else{
                    $logo       = '/public/mctsource/images/m1logo.png';
                }
            }
            //生成二维码
            $qr = QrCodeService::create($qrcodeUrl,$logo,200,'distribution/'.$wid.'/'.session('mid'));
        }

        return view('shop.member.distribution',[
            'title'     => '分销二维码',
            'wid'       => $wid,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ]);
    }


    /**
     * 分销二维码识别跳转页面
     * @return [type] [description]
     */
    public function distributionRedirect()
    {
        return view('shop.member.distributionRedirect',[
            'title' =>  '分销二维码',
            'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
        ]);
    }

    /**
     * 把远程图片下载到本地
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function http_get_imgData($url) {

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();

        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        return $return_content;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201
     * @desc
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAddress()
    {
        return view('shop.member.showAddress',[
            'title' =>  '地址管理',
        ]);
    }
    public function addAddress(Request $request)
    {
        $regionList=[];
        $regionService = new RegionService();
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }
        $addressData = [];
        if ($request->input('id')){
            $addressData = MemberAddressService::init()->getInfo($request->input('id'));
        }
        return view('shop.member.addAddress',[
            'title' =>  '添加地址',
            'regionList'    => $regionList,
            'addressData'   => $addressData,
        ]);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291058
     * @desc 收货地址列表
     * @param Request $request
     */
    public function getDefaultAddress(Request $request)
    {
        $address_id = $request->input('address_id','');
        if ($address_id){
            list($memberAddressData) = MemberAddressService::init()->where(['id'=>$address_id])->getList(false);
        }else{
            list($memberAddressData) = MemberAddressService::init()->where(['umid'=>session('umid')])->order('type desc')->getList(false);
        }

        if ($memberAddressData['data']){
            success('操作成功','',$memberAddressData['data'][0]);
        }else{
            success('操作成功','',[]);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180509
     * @desc 无权限访问页面
     */
    public function noPermission()
    {
        return view('shop.member.noPermission',[
            'title' =>  '无权限访问',
        ]);
    }

    /**
     * 三级联动地址接口
     * @return json
     * @author 许立 2018年08月13日
     */
    public function address()
    {
        $regionList=[];
        $regionService = new RegionService();
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }

        success('', '', $regionList);
    }

    /**
     * 会员中心-我的留言记录
     * @param int $wid 店铺id
     * @return view
     * @author 许立 2018年08月16日
     */
    public function researchList($wid)
    {
        return view('shop.member.researchList',[
            'title' => '我的留言记录',
            'wid'       => $wid,
            'data'  => (new ResearchModule())->memberResearches(session('mid')),
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 会员中心-我的留言记录-留言详情
     * @param int $wid 店铺id
     * @param int $id 留言id
     * @param int $times 第几次参与
     * @return view
     * @author 许立 2018年08月16日
     */
    public function researchDetail($wid, $id, $times)
    {
        // 验证参数
        if (empty($id) || empty($times)) {
            error('参数不完整');
        }
        return view('shop.member.researchDetail',[
            'title' => '留言详情',
            'data'  => (new ResearchModule())->researchRecords((int)$id, session('mid'), (int)$times),
            'wid'       => $wid,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 会员中心-我的收藏
     * @param int $wid 店铺id
     * @return view
     * @author 许立 2018年09月04日
     */
    public function favoriteList($wid)
    {
        return view('shop.member.favoriteList',[
            'title' => '我的收藏',
            'wid'   => $wid
        ]);
    }

    /**
     * 会员中心-我的收藏接口
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @param int $wid 店铺id
     * @return view
     * @author 许立 2018年09月04日
     */
    public function favoriteListApi(Request $request, FavoriteModule $favoriteModule, $wid)
    {
        // 查询条件
        $where = [
            'mid' => session('mid'),
            'wid' => $wid
        ];

        // 收藏类型, PRODUCT:商品, ACTIVITY:活动
        $type = $request->input('type', 'PRODUCT');
        $favoriteService = new FavoriteService();
        if ($type == 'PRODUCT') {
            $where['type'] = 0;
            $data = $favoriteService->listWithPage($where);
            // 处理商品是否失效
            $data[0]['data'] = $favoriteModule->handleProductValidity($data[0]['data']);
        } else {
            $where['type'] = ['>', 0];
            $data = $favoriteService->listWithPage($where);
            // 处理活动是否失效
            $data[0]['data'] = $favoriteModule->handleActivityValidity($data[0]['data']);
        }

        success('', '', $data[0]);
    }

    /**
     * 是否收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月05日
     */
    public function isFavorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && error('关联id不能为空');
        isset($input['type']) || error('类型不能为空');

        success('', '', ['isFavorite' => $favoriteModule->isFavorite(session('mid'), $input['relativeId'], $input['type'])]);
    }

    /**
     * 收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月06日
     */
    public function favorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && error('关联id不能为空');
        isset($input['type']) || error('类型不能为空');
        empty($input['title']) && error('标题不能为空');
        empty($input['price']) && error('价格不能为空');
        empty($input['image']) && error('图片不能为空');

        if ($input['type'] == Favorite::FAVORITE_TYPE_SHARE && empty($input['share_product_id'])) {
            error('享立减商品id不能为空');
        }

        // 收藏
        if ($favoriteModule->favorite(session('wid'), session('mid'), $input)) {
            success();
        } else {
            error();
        }
    }

    /**
     * 取消收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月05日
     */
    public function cancelFavorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && error('关联id不能为空');
        isset($input['type']) || error('类型不能为空');

        // 取消收藏
        if ($favoriteModule->cancelFavorite(session('mid'), $input['relativeId'], $input['type'])) {
            success();
        } else {
            error();
        }
    }

    /**
     * 扫码跳转绑定店铺核销员
     * @author 吴晓平 <2018年10月09日>
     * @param  Request           $request           [description]
     * @param  WeixinUserService $weixinUserService [description]
     * @return [type]                               [description]
     */
    public function bindHexiaoUser(Request $request,WeixinUserService $weixinUserService)
    {   
        $uid = $request->input('uid') ?? 0;
        if (empty($uid)) {
            error('参数异常');
        }
        $mid = session('mid');
        $wid = session('wid');
        $saveData['hexiao_mid'] = $mid;
        if ($weixinUserService->init()->model->where(['wid'=>$wid,'hexiao_mid' => $mid])->first()) {
            error('已绑定');
        }
        $re = $weixinUserService->init()->model->where(['uid'=>$uid,'wid'=>$wid])->update($saveData);
        if($re){
            success('成功绑定店铺核销员');
        }
        error();
    }
}
