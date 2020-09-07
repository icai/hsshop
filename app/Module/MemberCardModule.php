<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/3/14
 * Time: 8:54
 */

namespace App\Module;

use App\Jobs\BatchGrantMemberCard;
use App\Jobs\SubMsgPushJob;
use App\Lib\WXXCX\ThirdPlatform;
use App\S\Market\CouponService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberCardSyncLogService;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use App\S\Order\OrderService;
use App\S\WXXCX\SubscribeMessagePushService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MemberCardService;
use MemberCardRecordService;
use PointRecordService;
use WXXCXCache;
use QrCode;

class MemberCardModule
{

    /**
     * 判断该用户是否可领取该会员卡
     * Author: MeiJay
     * @param $mid
     * @param $card_id
     * @return array
     * @update 梅杰 2018年10月25日 增减返回信息
     */
    private function checkHasGetCard($mid,$card_id)
    {
        $data = [ 'msg' => '', 'err_code' => 0,'data' => [] ];
        //判断是该会员卡是否已经被删除
        $re = MemberCardService::getCard($card_id);
        if ($re['state'] != 1) {
            $data['msg'] = '该会员卡已经被删除';
            $data['err_code'] = 1;
            return $data;
        }
        //判断是否已经领取过 且未删除
        $record = MemberCardRecordService::init()->where(['mid' => $mid ,'card_id'=> $card_id])->getInfo();
        if(!$record) {
            return $data;
        }
        switch ($record['status']) {
            case 1:
                $day = (time() - strtotime($record['created_at'])) / 86400;
                if ($re['limit_type'] == 1 && $re['limit_days'] < $day) {
                    //过期后领取
                    $data['msg'] = '逾期会员卡，再次领取';
                    $data['err_code'] = 3;
                } else {
                    $data['msg'] = '您已经领取过该会员卡';
                    $data['err_code'] = 2;
                }
                break;
            case 0:
                $data['msg'] = '该用户删除了会员卡，再次领取';
                $data['err_code'] = 3;
                break;
        }

        $record['card_title'] = $re['title'];
        $data['data'] = $record;
        return $data;
    }


    /**
     * 会员卡详情
     * Author: 梅杰
     * @param $card_id 购物车id
     * @param $mid 用户id
     * @return mixed
     * update 梅杰 20180703 返回会员会员卡期限信息 20180704 领取时间格式修改
     */
    public function memberCardDetail($card_id,$mid)
    {
        $card = MemberCardService::getCard($card_id);
        if ($card) {
            $card['coupon_data'] = [];
            //获取所有的优惠券信息
            if ($coupons = json_decode($card['coupon_conf'], 1)){
                foreach ($coupons as $coupon)
                {
                    $card['coupon_data'][] = (new CouponService())->getDetail($coupon['coupon_id']);
                }
            }
            if ($record = MemberCardRecordService::init()->where(['mid' => $mid ,'card_id'=> $card_id])->getInfo()) {
                $record['expire'] = $this->getCardExpire($card,$record);
                $record['created_at'] = date('Y-m-d',strtotime($record['created_at'] ));
            }
            $card['has_get'] = $record && $record['status'] == 1 ? 1 : 0;
            if (empty($record)) {
                $record['active_status'] = 0;
            }
            $card['member_record'] = $record;
            //是否显示卡包二维码
            $card['is_show'] =  $card['card_id'] && $card['state'] == 1 && $card['is_active']
                && !(new MemberCardSyncLogService())->getRowByWhere(['mid' => $mid,'card_id' => $card['card_id']]);
        }
        return $card;
    }

    /**
     * 领取会员卡
     * Author: MeiJay
     * @param $card_id
     * @param $mid
     * @param $wid
     * @return array
     * @update 梅杰 2018年9月18 领取会员卡是修改
     * @update 梅杰 2018年9月18 领取之前删除过的会员卡bug修复 增加消息提醒
     */
    public function getMemberCard($card_id,$mid,$wid)
    {
        //检查是否已经领取过改会员卡
        $check = $this->checkHasGetCard($mid,$card_id);
        if (in_array($check['err_code'],[1,2]) ) {
            return $check;
        }
        if ($check['err_code'] == 3) {
            $update = [ 'out_card_at' => null,'created_at' => date('Y-m-d H:i:s'),'status' => 1];
            MemberCardRecordService::init('wid',$wid)->where(['id'=> $check['data']['id']])->update($update,false);
            $this->checkIsMember($mid);
            //发送消息通知
            $check['data']['mid'] = $mid;
            $data = app(MessagesPushService::class)->handDbData($wid, MessagesPushService::GetMemberCard);
            // 只有开启小程序模板发送才发送订阅消息
            if (in_array(4, $data['config'])) {
                // 小程序领取会员卡通知改为发送订阅模板消息 吴晓平 2019年12月19日 15:26:51
                // 模板发送的初步数据
                $data = [
                    'wid' => $wid,
                    'openid' => '',
                    'param' => []
                ];
                // 发送模板的相关内容
                $param = [
                    'mid' => $mid,
                    'title' => $check['data']['card_title'],
                    'number' => $check['data']['card_num'],
                ];
                // 组装后的数据
                $sendData = app(SubscribeMessagePushService::class)->packageSendData(1, $data);
                dispatch(new SubMsgPushJob(1, $wid, $sendData, $param));
            }
            // 发送公众号模板消息
            (new MessagePushModule($wid, MessagesPushService::GetMemberCard))->sendMsg($check['data']);

            $check['err_code'] = 0;
            $check['is_renew'] = 1;
            return $check;
        }
        //获取该会员卡详细信息
        $memberCardData = MemberCardService::getCard($card_id);
        $recordData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'card_id'   => $card_id,
            'card_num'  => MemberCardRecordService::getCardNo(),
            'active_status' =>  $memberCardData['is_active'] == 1 ? 0 : 1,
            'in_card_at'    => date("Y-m-d H:i:s"),
            'is_new'      => 1,
        ] ;
        $check['data']['card_num'] = $recordData['card_num'];
        $check['data']['card_title'] = $memberCardData['title'];
        //将会员卡中的优惠券转换为数组
        $couponConf = json_decode($memberCardData['coupon_conf'],true);
        $id = MemberCardRecordService::init()->add($recordData,false);
        $this->checkIsMember($mid);
        if($id){
            $memberService = new MemberService();
            $memberService->updateData($mid,['is_member'=>1]);
            //修改积分
            $memberService->incrementScore($mid,$memberCardData['score']);
            //添加积分领取类型
            $input = [
                'wid'       =>$wid,
                'mid'       =>$mid,
                'point_type'=> 7,
                'is_add'    => 1,
                'score'     =>$memberCardData['score'],
            ];
            PointRecordService::insertData($input);
            //拼接会员卡中的优惠券id
            if($couponConf) {
                $couponModule = new CouponModule();
                foreach ($couponConf as $v) {
                    $couponModule->createCouponLog($mid,$v['coupon_id'],$v['num'], $wid);
                }
                (new CouponService())->updateCoupon($couponConf);
            }
            //需要激活会员卡
            $check['data']['is_active'] = $memberCardData['is_active'];
            $check['data']['record_id'] = $id;
            #TODO 服务通知
            $check['data']['mid'] = $mid;
            (new MessagePushModule($wid, MessagesPushService::GetMemberCard, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($check['data']);

            (new MessagePushModule($wid, MessagesPushService::GetMemberCard))->sendMsg($check['data']);

        }
        return $check;
    }

    /**
     * 获取用户所有会员卡
     * Author: MeiJay
     * @param $mid
     * @param $wid
     * @return mixed
     */
    public function getMemberCardList($mid,$wid)
    {
        return MemberCardRecordService::getMenberCart($mid,$wid);
    }




    /**
     * 设置默认会员卡
     * Author: MeiJay
     * @param $mid
     * @param $wid
     * @param $card_id
     */
    public function setDefaultMemberCard($mid,$wid,$card_record_id)
    {
        $res = MemberCardRecordService::init('wid',$wid)->model->where(['mid'=>$mid,'is_default'=>1])->get(['id'])->toArray();
        if ($res){
            foreach ($res as $val){
                MemberCardRecordService::init('wid',$wid)->where(['id'=>$val['id']])->update(['id'=>$val['id'],'is_default'=>0],false);
            }
        }
        return MemberCardRecordService::init('wid',$wid)->where(['id'=>$card_record_id])->update(['id'=>$card_record_id,'is_default'=>1],false);
    }


    /**
     * 通过会员卡领取记录id会员卡删除
     * Author: MeiJay
     * @param $mid
     * @param $card_record_id
     * @return bool
     * @update 梅杰 2018年9月18日 同步修改member表 会员标识
     */
    public function deleteMemberCard($mid,$card_record_id)
    {
        $data = MemberCardRecordService::init('mid',$mid)->where(['mid'=>$mid,'id'=>$card_record_id,'status'=> 1])->getInfo();
        if($data){
            $data['status'] = 0;
            $data['out_card_at'] = date('Y-m-d H:i:s');
            //修改member表的数据
            return MemberCardRecordService::init()->update($data,false) && $this->checkIsMember($mid);

        }
        return false;
    }

    /**
     * 通过会员卡id删除会员卡
     * Author: MeiJay
     * @param $mid
     * @param $card_id
     * @return bool
     */
    public function deleteMemberCardByCardId($mid,$card_id)
    {
        $update = $data = MemberCardRecordService::init('mid',$mid)->where(['mid'=>$mid,'card_id'=>$card_id,'status'=> 1])->getInfo();
        if($data){
            $update['status'] = 0;
            $update['out_card_at'] = date('Y-m-d H:i:s');
            unset($update['id']);
            return  MemberCardRecordService::init()->where(['id'=>$data['id']])->update($update,false);
        }
        return false;
    }


    /**
     *
     * Author: MeiJay
     * @param $mid
     * @param $card_id
     * @return array
     */
    public function getMemberCardCode($mid,$card_id)
    {
        $data = [ 'msg' => 'success', 'err_code' => 0,'data' => [] ];
        if (!MemberCardService::getCard($card_id)) {
            $data['msg'] = '该会员卡不存在';
            $data['err_code'] = 1;
            return $data;
        }
        //判断是否已经领取过
        $re = MemberCardRecordService::init()->where(['mid' => $mid ,'card_id'=> $card_id,'status' => 1])->getInfo();
        if (!$re) {
            $data['msg'] = '请先领取会员卡';
            $data['err_code'] = 2;
            return $data;
        }
        $path = 'hsshop/xcx/membercard';
        $file = public_path($path.'/'.$re['card_num'].".png");
        if (file_exists($file)) {
            $data['data'] = $path.'/'.$re['card_num'].".png";
            return $data;
        }
        if (!file_exists($path)) {
            mkdir(public_path($path));
        }
        QrCode::format('png')->size(400)->generate($re['card_num'],public_path($path.'/'.$re['card_num'].".png"));
        //生成二维码并保存
        $data['data'] = $path.'/'.$re['card_num'].".png";
        return $data;
    }

    /**
     * 会员卡激活
     * Author: MeiJay
     * @param $data
     * @param $mid
     * @param $wid
     * @param $card_id
     * @return array
     */

    public function ActiveMemberCard($data,$mid,$wid,$card_id)
    {
        $saveData['truename']    = $data['name'];
        $saveData['wechat_id']   = $data['weixin'];
        $saveData['sex']         = $data['gender'];
        $saveData['province_id'] = $data['member_province'];
        $saveData['city_id']     = $data['member_city'];
        $saveData['area_id']     = $data['member_county'];
        $saveData['birthday']     = $data['birthday'];
//        $saveData['mobile']     = $data['mobile'];
        //是否有会员存档（判断用户是否关注该店铺的公众号）
        try{
            $res = (new MemberService())->updateData($mid,$saveData);
            //保存统一的id
            $umData = [
                'truename'      => $data['name'],
                'nickname'      => $data['name'],
                'sex'           => $data['gender'],
            ];
            $member = (new MemberService)->getRowById($mid);
            (new UnifiedMemberService())->update($member['umid'],$umData);
            if($res){  //更新
                if (isset($data['tag']) && $data['tag'] == 1){
                    $res = MemberCardRecordService::init()->where(['mid' => $mid ,'card_id'=> $card_id])->getInfo();
                    if (!$res){
                        return [ 'msg' => '未领取该会员卡' ,'err_code'=> 1 ,'data' => [] ];
                    }
                    //如果同步了微信卡包，同步激活
//                    $cardData = MemberCardService::getRowById($res['card_id']);
//                    if($cardData['is_sync_wechat'] == 1 ) {
//                        $memberCardSyncLogService = new MemberCardSyncLogService();
//                        $logData = $memberCardSyncLogService->getRowByWhere(['mid'=>$mid,'card_id'=>$card_id]);
//                        if(!empty($logData) && !empty($logData[0]['code'])) {
//                            $logData = $logData[0];
//                            $apiService = new ApiService();
//                            $activeData['code'] = $logData['code'];
//                            $activeData['membership_number'] = $res['card_num'];
//                            $re = $apiService->activeCard($wid,$activeData);
//                            if($re['errcode'] != 0)
//                                return [ 'msg' => '微信同步激活失败' ,'err_code'=> 2 ,'data' => [] ];
//                        }
//                    }
                    $res['active_status'] = 1;
                    MemberCardRecordService::init('mid',$mid)->where(['id'=>$res['id']])->update($res,false);
                }
            }
        }catch(\ErrorException $exception){
            return [ 'msg' => $exception->getMessage() ,'err_code'=> 0 ,'data' => [] ];
        }
        return [ 'msg' => 'success' ,'err_code'=> 0 ,'data' => [] ];
    }

    /**
     * 生成会员卡小程序码
     * Author: MeiJay
     * @param $wid
     * @param $card_id
     * @updated_at 线上bug 临时修改 2018年10月12日 梅杰
     */
    public function qrCodeLinkMemberCard($wid,$card_id)
    {
        //先取缓存，缓存木有请求接口
        $cache  =  WXXCXCache::get($wid.":".$card_id,"member_card_cache",false);
        if (!$cache) {
            $thirdPlatform = new ThirdPlatform();
            $scene = "'card_id':".$card_id.",'record_id':1";
            $page = 'pages/main/pages/member/memberCard/memberCardDetail/memberCardDetail';
            $data = $thirdPlatform->createXcxQrCode($wid,$page,$scene);
            if ($data['errCode'] == 0) {
                $cache  = $data['data'];
                WXXCXCache::set($wid.":".$card_id,$data['data'],'member_card_cache',7200);
            }
        }
        return $cache;
    }

    /**
     * 下载小程序码
     * Author: MeiJay
     * @param $wid
     * @param $card_id
     * @return bool|string
     */
    public function downloadXcxCode($wid,$card_id)
    {
        $param = 'hsshop/image/qrcodes/xcx_member_card';
        $filename = $param."/".$card_id.".jpg";
        if (file_exists($filename)) {
            return $filename;
        }
        if(!file_exists(iconv("UTF-8", "GBK", public_path($param)))) {
            mkdir(iconv("UTF-8", "GBK", public_path($param)),0777,true);
        }
        if ($code = $this->qrCodeLinkMemberCard($wid,$card_id)) {
            $img = base64_decode($code);
            $a = file_put_contents($filename, $img);//返回的是字节数
            return $filename;
        }
        return false;
    }

    /**
     * 向指定人发放会员卡以及批量发卡
     * Author: MeiJay
     * @param $wid
     * @param $mid
     * @param $card_id
     * @return array
     */
    public function grantCardToMember($wid,$mid,$card_id)
    {
        $return = [
            'err_code' => 0,
            'err_msg'  => ''
        ];
        if (is_array($mid)) {
            //批量发卡，队列执行
            foreach ($mid as $item) {
                // $this->getMemberCard($card_id,$item,$wid);
                $job = (new BatchGrantMemberCard($item,$wid,$card_id))->onQueue('GrantMemberCard');
                dispatch($job);
            }
            return $return;
        }
        //向单独的某个人发卡
        $card = $this->getMemberCard($card_id,$mid,$wid);
        if ($card['err_code'] == 0) {
            return $return ;
        }
        $return['err_code'] = -1;
        $return['err_msg']  = $card['err_code'] == 2 ? '该会员已领取过该卡' : '发卡失败' ;
        return $return;
    }

    /**
     * 获取会员未领取的会员卡信息
     * Author: MeiJay
     * @param $wid
     * @param $mid
     * @return array
     */
    public function getUnclaimedMemberCardList($wid,$mid)
    {
        $haveIds = [];
        if ($mid) {
            $have =  MemberCardRecordService::getMenberCart($mid,$wid);
            foreach ($have as $value) {
                if (in_array($value['state'] ,[1,2]) ){
                    $haveIds[] =  $value['memberCard']['id'];
                }
            }
        }
        $where = [
            'wid' => $wid,
            'state' => 1
        ];
        if ($haveIds) {
           $where['id']  = ['not in',$haveIds];
        }
        $data = MemberCardService::getListByWhere($where);
        foreach ($data as $k => $v) {
            if ($v['limit_type'] == 2 && strtotime($v['limit_end']) < time()) {
                unset($data[$k]);
            }
        }
        return array_values($data);
    }

    /**
     * 给会员删除会员卡
     * Author: MeiJay
     * @param $wid
     * @param $mid
     * @param $card_id
     * @return array
     */
    public function deleteCardForMember($wid,$memberData)
    {
        $return = [
            'err_code' => 0,
            'err_msg' => ''
        ];
        foreach ($memberData as $v) {
            $re = $this->deleteMemberCardByCardId($v['mid'],$v['card_id']);
//            $job = (new BatchGrantMemberCard($v['mid'], $wid, $v['card_id'],  1))->onQueue('GrantMemberCard');
//            dispatch($job);
        }
        return $return;
    }

    /**
     * 获取会员卡的有效期限
     * @param $card 会员卡信息
     * @param $record 会员卡领取记录
     * @return string
     * @author: 梅杰 20180703
     * @updated: 20180704 时间格式只返回年月日
     */
    public function getCardExpire($card,$record)
    {
        //如果领取则处理显示的会员卡有效期限
        switch ($card['limit_type']) {
            case 0://无期限
                $record['time'] = '无期限';
                break;
            case 1:
                //有限天数
                $time = strtotime($record['created_at'] ."+ {$card['limit_days']} days ");
                $record['time'] =  date('Y-m-d',strtotime($record["created_at"]))."～".date('Y-m-d',$time);
                break;
            case 2:
                //限制时间段
                $record['time'] = $card['limit_end'] < date('Y-m-d h:i:s') ? '已过期' :date('Y-m-d',strtotime($card['limit_start']))."～".date('Y-m-d',strtotime($card['limit_end']));
                break;
        }
        $record['time'] = $card['is_active'] && $record['active_status'] == 0 ? '未激活 ' :  $record['time'];
        return $record['time'];
    }


    /**
     * 查看会员卡时清除标志
     * @param $mid 用户id
     * @param $recordId 记录id
     * @return mixed
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCardCallBack($mid,$recordId)
    {
        if ($res = MemberCardRecordService::init()->where(['mid' => $mid ,'id'=> $recordId])->getInfo()) {
            $res['is_new'] = 0;
            return MemberCardRecordService::init('mid',$mid)->where(['id'=>$res['id']])->update($res,false);
        }
        return true;
    }

    /**
     * 判断是否有新会员卡
     * @param $mid
     * @return mixed
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCard($mid)
    {
        return MemberCardRecordService::init()->where(['mid' => $mid ,'is_new'=>1])->getInfo();
    }


    /**
     * 判断用户是否是会员
     * @param $mid
     * @author: 梅杰 2018 年9月18
     */
    public function checkIsMember($mid)
    {
        $re = DB::table('member_card_record as l')->leftJoin('member_card as c','l.card_id','=','c.id')
            ->select(["l.mid",'l.id',"l.status","l.created_at","limit_days","limit_type","limit_start","limit_end","state"])
            ->whereNull('c.deleted_at')->where(['mid'=>$mid])->get();
        $is_member = 0;
        foreach ($re as $val) {

            if ($val->limit_type == 1){
                $day = (time()-strtotime($val->created_at))/86400;
                if ($val->limit_days < $day){
                    $is_member = 2;
                    continue;
                }
            }

            if ($val->limit_type == 2){
                if (strtotime($val->limit_end)<time()){
                    $is_member = 2;
                    continue;
                }
            }

            if (in_array($val->state,[-1,0]) || $val->status == 0) {
                $is_member = 2;
                continue;
            }
            $is_member = 1;
            break;
        }
        $memberService = new MemberService();
        $memberService->updateData($mid,['is_member'=>$is_member]);
        return true;
    }


    /**
     * 按规则发放会员卡
     * @param $mid
     * @param $wid
     * @author: 梅杰 2018年9月21日
     * @return array
     */
    public function grantRuleMemberCard($mid,$wid)
    {
        //获取所有该店铺的按规则发放会员卡
        $ruleCards =  MemberCardService::getListByWhere(['wid'=>$wid,'card_status'=>1,'state'=>1],"","",'card_rank','asc');
        //获取消费信息
        $userOrderInfo = (new OrderService())->getMemberOrderInfo($mid);
        $member = (new MemberService())->getRowById($mid);
        if (!$member) {
            \Log::info('用户不存在'.$mid);
            return [];
        }
        $card = $return = [];
        foreach ($ruleCards as $key => $val) {
            $rule = explode('||',$val['up_condition']);
            if (!$rule) {
                continue;
            }

            if (!empty($rule[0]) && $rule[0] <= $userOrderInfo['num']) {

                $card[] = $val;
                continue ;
            }

            if (!empty($rule[1]) && $rule[1] <= $userOrderInfo['amount']) {

                $card[] = $val;
                continue ;
            }

            if (!empty($rule[2]) && $rule[2] <= $member['score']) {

                $card[] = $val;
                continue ;
            }

        }
        $cardIds = array_column($card,'id');
        foreach ($cardIds as $value) {
            $re = $this->getMemberCard($value,$mid,$wid);
            if ($re['err_code'] == 0) {
                $return[] = $re['data'];
            }
        }
        return $return;
    }

}
