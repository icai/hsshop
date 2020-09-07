<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/9/25
 * Time: 16:34
 */

namespace App\Module;


use App\Jobs\BindMobile;
use App\Lib\Redis\RedisClient;
use App\Lib\Redis\SMSKeys;
use App\S\Market\CouponLogService;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use App\S\Wechat\WeixinSmsConfService;
use App\Services\Lib\RestService;
use App\Services\MemberCardRecordService;
use App\Services\Order\OrderService;
use App\Services\Shop\MemberAddressService;
use App\Services\WeixinService;
use DB;
use WXXCXCache;
use App\Model\WeixinConfigSub;
use App\S\Weixin\ShopService;

class BindMobileModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170925
     * @desc 处理小程序member数据
     * @param $phone
     * @param $wid
     */
    public function dealMember($mid,$memberData)
    {
        $memberService = new MemberService();

        //获取当前店铺的的用户信息
        $nowMemberData = $memberService->getRowById($mid);

        $data['xcx_openid'] = $memberData['xcx_openid'];
        $data['money']  = $nowMemberData['money']+$memberData['money'];
        $data['truename']  = $memberData['truename'];
        $data['nickname']  = $memberData['nickname'];
        $data['headimgurl']  = $memberData['headimgurl'];
        $data['score']  = $memberData['score']+$nowMemberData['score'];
        $data['buy_num']  = $memberData['buy_num']+$nowMemberData['buy_num'];
        $data['cash']  = $memberData['cash']+$nowMemberData['cash'];
        $memberService->updateData($nowMemberData['id'],$data);
        $memberService->updateData($memberData['id'],['xcx_openid'=>'']);

        //处理订单信息
        $orderService = new OrderService();
        $res = $orderService->init()->model->where('mid',$memberData['id'])->get(['id'])->toArray();
        $oids = [];
        $odData = [
            'mid'   => $nowMemberData['id'],
            'umid'  => $nowMemberData['umid'],
        ];
        foreach ($res as $val){
            $odData['id'] = $val['id'];
            $orderService->init()->where(['id'=>$val['id']])->update($odData,false);
        }

        //更新地址信息
        $memberAddressService = new MemberAddressService();
        $addrData = $memberAddressService->init()->model->where('mid',$memberData['id'])->get(['id'])->toArray();
        foreach ($addrData as $val){
            $memberAddressService->init()->where(['id'=>$val['id']])->update(['id'=>$val['id'],'umid'=>$nowMemberData['umid'],'mid'=>$nowMemberData['id']],false);
        }
        //更新会员卡信息
        $recordService = new MemberCardRecordService();
        $recordData = $recordService->init()->model->where('mid',$memberData['id'])->get(['id'])->toArray();
        foreach ($recordData as $val){
            $recordService->init()->where(['id'=>$val['id']])->update(['id'=>$val['id'],'mid'=>$nowMemberData['id']],false);
        }
        //更新优惠券信息
        $couponLogService = new CouponLogService();
        $couponData = $couponLogService->listWithoutPage(['mid' => $memberData['id']])[0]['data'];
        foreach ($couponData as $val){
            $couponLogService->update($val['id'], ['mid' => $nowMemberData['id']]);
        }
        return true;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170926
     * @desc 当前用户是否需要绑定手机号码
     * @param $mid
     * @param $wid
     */
    public function isBind($umid,$wid)
    {
        $smsService = new WeixinSmsConfService();
        $umService = new UnifiedMemberService();
        $smsData = $smsService->getList(['wid'=>$wid]);
        if (!$smsData){
            return false;
        }
        $umData = $umService->getRowById($umid);
        if (!$umData['mobile']){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20170926
     * @desc 发送验证码
     * @param $mid
     * @param $wid
     * @param $phone
     * @return \App\Services\Lib\内容数据|array|mixed|\SimpleXMLElement
     */
    public function sendCode($mid,$wid,$phone)
    {
        $result = ['errCode'=> 0,'errMsg'=>''];
        //获取店铺配置信息
        $weixinSmsConfService = new WeixinSmsConfService();
        $res = $weixinSmsConfService->getList(['wid'=>$wid]);
        if(!$res){
            $result['errCode'] = 1;
            $result['errMsg']  = '店铺未配置短信';
            return $result;
        }
        $wscData = $res[0];
        //生成验证码 随机生成4位
        $code = rand(1000,9999);
        $datas = [$code,1,$wscData['phone']];
        $restService  = new RestService('app.cloopen.com',8883,'2013-12-26');
        $restService->setAccount($wscData['account_sid'],$wscData['account_token']);
        $restService->setAppId($wscData['app_id']);
        $res = $restService->sendTemplateSMS($phone,$datas,$wscData['code']);
        if($res->statusCode!=0) {
            $result['errCode'] = 2;
            $result['errMsg']  = (string)$res->statusMsg;
            return $result;
        }else{
            $smsKeys = new SMSKeys('bindmobile'.$mid.'phone'.$phone);
            $codeSms = $smsKeys->get();
            $codeSms = json_decode($codeSms,true)??[];
            array_push($codeSms,$code);
            $smsKeys->set(json_encode($codeSms));
            return $result;
        }
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 小程序绑定手机号码
     * @param $wid
     * @param $mid
     * @param $phone
     * @param $token
     * @update 张永辉 2018年7月16日 绑定手机号码之后写入小程序配置信息到token信息里面
     */
    public function xcxBindMobile($wid,$mid,$mobile,$token)
    {
        //绑定当前用户手机号码
        $memberService = new MemberService();
        DB::beginTransaction();
        $res = $memberService->batchUpdate([$mid],['mobile'=>$mobile]);
        if (!$res){
            DB::rollBack();
            return false;
        }
        //判断公众号是否绑定该号码
        $umService = new UnifiedMemberService();
        $umData = $umService->getRowByMobile($mobile);
        if (!$umData){
            DB::commit();
            return true;
        }
        //判断用户该店铺是否绑定过
        $weChatMemberData = $memberService->getList(['wid'=>$wid,'umid'=>$umData['id']]);
        if (!$weChatMemberData){
            $memberService->batchUpdate([$mid],['umid'=>$umData['id']]);
            DB::commit();
            return true;
        }else{
            $weChatMemberData = $weChatMemberData[0];
            $nowMemberData = $memberService->getRowById($mid);
            if ($weChatMemberData['id'] != $nowMemberData['id']){
                $data['xcx_openid'] = $nowMemberData['xcx_openid'];
                $data['money']  = $nowMemberData['money']+$weChatMemberData['money'];
                $data['score']  = $weChatMemberData['score']+$nowMemberData['score'];
                $data['buy_num']  = $weChatMemberData['buy_num']+$nowMemberData['buy_num'];
                $data['cash']  = $weChatMemberData['cash']+$nowMemberData['cash'];

                $memberService->updateData($weChatMemberData['id'],$data);
                $memberService->updateData($nowMemberData['id'],['xcx_openid'=>'','status'=>-1]);
                //更新当前用户mid
                $xcxid = (new CommonModule())->getXcxConfigIdByToken($token);
                $value= $nowMemberData['xcx_openid'].','.$wid.','.$weChatMemberData['id'].','.$xcxid;
                WXXCXCache::set($token,$value,'3rd_session');
                DB::commit();
                $job = new BindMobile($nowMemberData['id'],$weChatMemberData['id']);
                dispatch($job);
            }
            return true;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 是否需要绑定小程序
     * @param $mid
     * @param $wid
     * @return 0:不需要绑定，1：需要绑定
     * @update 陈文豪 2018年8月15号 没有配置微信公众号，默认不打开小程序绑定
     * @update 陈文豪 2018年8月29号 单独处理某用户
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function xcxIsBind($mid,$wid)
    {
        //当微信公众号未配置 不打开小程序账号打通
//        $weixinConfigSub = new WeixinConfigSub();
//        $obj = $weixinConfigSub->wheres(['wid' => $wid])->first();
//        if(!$obj && $wid != 1310){
//            return 0;
//        }

        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        $memberData = (new MemberService())->getRowById($mid);

        if ($shopData && $shopData['is_sms'] == 1 && !$memberData['mobile']){
            return 1;
        }else{
            return 0;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 更改绑定手机号码
     * @param $mid
     */
    public function changeMobile($mid,$mobile)
    {
        $memberService = new MemberService();
        $memberData = $memberService->getRowById($mid);
        if ($memberData['umid']){
            $umService = new UnifiedMemberService();
            $data = [
                'mobile'=>$mobile
            ];
            $umService->update($memberData['umid'],$data);
            $memberData = $memberService->getList(['umid'=>$memberData['umid']]);
            $ids = [];
            foreach ($memberData as $val){
                $ids[] = $val['id'];
            }
            return $memberService->batchUpdate($ids,$data);
        }else{
            return $memberService->batchUpdate([$mid],['mobile'=>$mobile]);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 公众号端绑定手机号码
     * @param $umid
     * @param $mobile
     * @param $wid
     * @param $mid
     * @return bool
     */
    public function bindMobile($umid,$mobile,$wid,$mid)
    {
        $request= app('request');
        $data = [
            'mobile'        => $mobile,
        ];
        DB::beginTransaction();
        $umService = new UnifiedMemberService();
        $res = $umService->update($umid,$data);
        if (!$res){
            DB::rollBack();
            return false;
        }
        //判断小程序是否有绑定该手机号码的用户
        $memberService = new MemberService();
        $where = [
            'wid'       => $wid,
            'mobile'    => $mobile,
            'source'    => 6,
        ];

        $mData = $memberService->getList($where);
        if ($mData && $mData[0]['id'] != $mid){
            $mData = $mData[0];
            $nowMemberData = $memberService->getRowById($mid);
            $data['umid'] = $nowMemberData['umid'];
            $data['openid'] = $nowMemberData['openid'];
            $data['wechat_id'] = $nowMemberData['wechat_id'];
            $data['score'] = $nowMemberData['score']+$mData['score'];
            $data['buy_num'] = $nowMemberData['buy_num']+$mData['buy_num'];
            $data['cash'] = $nowMemberData['cash']+$mData['cash'];
            $data['money'] = $nowMemberData['money']+$mData['money'];
            $memberService->updateData($mData['id'],$data);
            $memberService->updateData($nowMemberData['id'],['umid'=>'','appid'=>'','openid'=>'','status'=>-1]);
            $request->session()->put('mid', $mData['id']);
            $request->session()->save();
            $job = new BindMobile($nowMemberData['id'],$mData['id'],$umid);
            dispatch($job);
        }

        $memberData = $memberService->getList(['umid'=>$umid]);
        $ids = [];
        foreach ($memberData as $val){
            $ids[] = $val['id'];
        }
        $memberService->batchUpdate($ids,['mobile'=>$mobile]);
        DB::commit();
        $request->session()->put('mobile', $mobile);
        $request->session()->save();
        return true;
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171211
     * @desc 个人中心是否显示修改手机号码
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isShowChangeMobile($wid)
    {
        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        if ($shopData && $shopData['is_sms'] == 1){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171215
     * @desc 是否可以发送验证码
     */
    public function isAccessSendCode($mid)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $key = $this->getKey($mid);
        $num = $redisClient->get($key);
        if ($num && $num>9){
            return false;
        }else{
            if ($redisClient->EXISTS($key)){
                $redisClient->INCR($key);
            }else{
                $redisClient->INCR($key);
                $redisClient->EXPIRE($key,86400);
            }
            return true;
        }
    }

    /*获取key*/
    public function getKey($mid){
        return 'send_code_num:'.$mid;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171218
     * @desc 处理用户绑定信息
     * @param $umid
     * @param $mobile
     */
    public function dealMobile($mid,$mobile){
        $memberService = new MemberService();
        $mdata = $memberService->getRowById($mid);
        if ($mdata && empty($mdata['mobile']) && !empty($mobile)){
            $memberService->updateData($mdata['id'],['mobile'=>$mobile]);
        }
    }


}