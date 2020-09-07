<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/8/24
 * Time: 17:17
 */

namespace App\Module;


use App\Lib\Redis\NewUserFlagRedis;
use App\Lib\Redis\RedisClient;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use App\S\Member\WeixinMemberService;
use App\S\Wechat\WeChatAgentService;
use App\S\Wechat\WeChatShopConfService;
use App\S\Weixin\ShopService;


class WeChatAuthModule
{

    protected $appId;
    protected $appSecret;
    protected $accessToken;
    protected $payee;
    protected $mch_id;
    protected $mch_key;
    protected $request;
    //服务方（第三方平台）appid
    protected $component_appId;


    public function __construct()
    {
        $this->request   = app('request');
        $this->appId     = config('wechat.app_id');
        $this->appSecret = config('wechat.secret');
        $this->payee = config('wechat.payee');
        $this->mch_id = config('wechat.mch_id');
        $this->mch_key = config('wechat.mch_key');
        $this->component_appId = config('app.auth_appid');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170824
     * @desc 微信授权登陆
     * @update 张永辉 2018年7月6日  授权登陆去掉重复code数据
     */
    public function auth()
    {
        $weChatAgentService = new WeChatAgentService();
        $code = $this->request->input('code');
        if (empty(session('randCode')) || empty($code)) {
            $comUrl = $this->request->url();
            $input = $this->request->input();
            if ($input){
                $comUrl .= '?1=1';
                $keys = ['appid','code','from','isappinstalled','state'];
                foreach ($input as $key=>$item){
                    if (!in_array($key,$keys)){
                        $comUrl .='&'.$key.'='.$item;
                    }
                }
            }
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appId."&redirect_uri=".urlencode($comUrl)."&response_type=code&scope=snsapi_base&state=STATE&component_appid=".$this->component_appId."#wechat_redirect";
            $this->request->session()->put('randCode',rand(1,1000));
            $this->request->session()->save();
            return redirect($url);
        } else {
            $result = $weChatAgentService->getAccessTokenByCode($this->appId,$code,$this->component_appId);
            if (isset($result['errcode'])) {
                \Log::info('auth error 1:');
                \Log::info($result);
                \Log::info($this->request->fullUrl());
                \Log::info('code='.$code.',randCode='.session('randCode'));
                error('授权错误1');
            }
            $this->request->offsetSet('code','');
            $this->request->session()->forget('randCode');
            $openid = $result['openid'];
            //设置用户 session信息
            $this->authMember($openid);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170825
     * @desc 用户是否存在
     */
    public function authMember($openid)
    {
        $umService = new UnifiedMemberService();
        $umData = $umService->getRowByOpenid($openid);
        $mobile = '';
        if (!$umData) {
            $umData = [
                'openid' => $openid,
            ];
            $umid        = $umService->add($umData);
            if (!$umid) {
                \Log::info('用户授权失败openid=：'.$openid);
                exit();
            }
        } else {
            $umid = $umData['id'];
            $mobile = $umData['mobile'];
        }
        $this->request->session()->put('umid', $umid);
        //手机号码写入session
        $this->request->session()->put('mobile', $mobile);
        $this->request->session()->save();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170831
     * @desc 验证用户在该店铺下是否存在信息
     * @param $umid
     * @param $wid
     * @update 何书哲 2018年8月8日 如果是享立减新用户，则发送新用户日志
     */
    public function authShopMember($umid,$wid,$userInfo='',$appid='')
    {
        //判断小程序端

        //判断店铺用户信息是否存在
        $memberService = new MemberService();
        $where = [
            'umid' => $umid,
            'wid'  => $wid,
        ];
        $memberData = $memberService->getList($where);
        $memberData = array_pop($memberData);
        if (!$memberData) {
            $memberData = [
                'umid'      => $umid,
                'wid'       => $wid,
                'openid'    => $userInfo['openid']??'',
                'appid'     => $appid,
                'truename'  => $userInfo['nickname']??'',
                'nickname'  => $userInfo['nickname']??'',
                'headimgurl'=> $userInfo['headimgurl']??'',
                'sex'        => $userInfo['sex']??1,
                'unionid'    => $userInfo['unionid']??'',
            ];
            $memberModule = new MemberModule();
            if ($memberModule->memberCheck($wid,$umid)){
                $mid = $memberService->add($memberData);
                (new NewUserFlagRedis())->set($mid);
                //何书哲 2018年11月12日 店铺未设定分销门槛，新用户发送分销客消息通知
                $shopData = (new ShopService())->getRowById($wid);
                if (isset($shopData['distribute_grade']) && $shopData['distribute_grade'] == 0) {
                    (new MessagePushModule($wid, MessagesPushService::BecomePromoter))->sendMsg(['mid'=>$mid]);
                }
            }
        }else{
            if ($userInfo && $appid){
                $up = [
                    'openid'    => $userInfo['openid'],
                    'appid'     => $appid,
                    'truename'      => $userInfo['nickname'],
                    'nickname'      => $userInfo['nickname'],
                    'headimgurl'    => $userInfo['headimgurl'],
                    'sex'            => $userInfo['sex'],
                    'unionid'    => $userInfo['unionid']??'',
                ];
                $memberService->updateData($memberData['id'],$up);
            }
            $mid = $memberData['id'];
        }
        $this->request->session()->put('mid', $mid);
        $this->request->session()->save();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180620
     * @desc 判断是否存在小程序
     * @other 修改请通知zhangyh
     * @param $wid 店铺id
     * @param $uid 用户唯一id
     */
    public function judgeMinAppIsExist($umid,$wid)
    {
        $mobile = session('mobile');
        if (empty($mobile)){
            return false;
        }
        $where = [
            'mobile' => $mobile,
            'wid'  => $wid,
            'source'
        ];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170825
     * @desc 用户对于该店铺微信公众号是否认证
     */
    public function isAuth($wid, $umid)
    {
        $result    = ['success' => 0, 'message' => '', 'data' => ''];
        $memberService = new MemberService();
        $conf       = (new WeChatShopConfService())->getConfigByWid($wid);
        if (!$conf) {
            return $result;
        } else {
            if ($conf['service_type_info'] != 2 ) {
                return $result;
            }
            if($conf['verify_type_info']  != 0){
                return $result;
            }
            if($conf['verify_type_info'] != 0){
                return $result;
            }

            $result['success'] = 1;
            $result['data']    = $conf;
            return $result;

//            $where  = [
//                'wid'   => $wid,
//                'umid'   => $umid,
//                'appid' => $conf['app_id'],
//            ];
//            $wmData = $memberService->getList($where);
//            $wmData = array_pop($wmData);
//            $redisClient = (new RedisClient())->getRedisClient();
//            if ($wmData && !empty($wmData['openid'])) {
//                if ($redisClient->get($this->getUserUpKey($wmData['id']))){
//                    return $result;
//                }else{
//                    $result['success'] = 1;
//                    $result['data']    = $conf;
//                    return $result;
//                }
//
//            } else {
//                $result['success'] = 1;
//                $result['data']    = $conf;
//                return $result;
//            }
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170828
     * @desc 店铺认证获取openid
     */
    public function shopAuth($conf,$wid)
    {
        $weChatAgentService = new WeChatAgentService();
        $code = $this->request->input('code');
        if (empty(session('randCode2')) || empty($code) || $code == session('checkCode')) {


            $comUrl = $this->request->url();
            $input = $this->request->input();
            if ($input){
                $comUrl .= '?1=1';
                $keys = ['appid','code','from','isappinstalled','state'];
                foreach ($input as $key=>$item){
                    if (!in_array($key,$keys)){
                        $comUrl .='&'.$key.'='.$item;
                    }
                }
            }

            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$conf['app_id']."&redirect_uri=".urlencode($comUrl)."&response_type=code&scope=snsapi_userinfo&state=STATE&component_appid=".$this->component_appId."#wechat_redirect";
            $this->request->session()->put('checkCode',$code);
            $this->request->session()->put('randCode2',rand(1,1000));
            $this->request->session()->save();
            return redirect($url);
        } else {
            $this->request->session()->put('randCode2','');
            $this->request->session()->save();

            $result = $weChatAgentService->getAccessTokenByCode($conf['app_id'],$code,$this->component_appId);
            if (isset($result['errcode'])) {
                \Log::info('授权错误2');
                \Log::info($result);
                error('微信服务异常,请返回微信后重试');
            }
            $this->request->session()->forget('randCode2');
            //获取用户信息，头像性别等
            $userInfo = $weChatAgentService->getUserInfo($result['access_token'],$result['openid']);
            if (isset($userInfo['errcode'])) {
                error('用户授权失败');
            }
            $this->upUnifiedMember($userInfo);
            //设置用户 session信息
            $this->upMember($conf,$userInfo,$wid);
        }
    }


    public function upUnifiedMember($data)
    {
        $umData = [
            'truename'      => $data['nickname'],
            'nickname'      => $data['nickname'],
            'headimgurl'    => $data['headimgurl'],
            'sex'            => $data['sex'],
        ];
        (new UnifiedMemberService())->update(session('umid'),$umData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170828
     * @desc 更新数据信息
     * @param $conf
     * @param $openid
     */
    public function upMember($conf,$userInfo,$wid)
    {
        $memberService = new MemberService();
        $where = [
            'openid'       => $userInfo['openid'],
            'wid'          => $wid,
        ];
        $memberData = $memberService->getList($where);
        $memberData = array_pop($memberData);
        if ($memberData){
            //判断用户老数据是否存在，如果存在则把用户的umid写入，保存用户原来的数据信息
            $mid = $memberData['id'];
            $up = [
                'umid'    => session('umid'),
                'appid'     => $conf['app_id'],
                'truename'      => $userInfo['nickname'],
                'nickname'      => $userInfo['nickname'],
                'headimgurl'    => $userInfo['headimgurl'],
                'sex'            => $userInfo['sex'],
                'unionid'        => $userInfo['unionid']??'',
            ];
            $res = $memberService->updateData($memberData['id'],$up);
            if ($res['errCode'] != 0){
                error('更新错误');
            }
            $this->request->session()->put('mid', $mid);
            $this->request->session()->save();
        }else{
            //如果用户数据原来不存在则写入新的数据
            $this->authShopMember(session('umid'),$wid,$userInfo,$conf['app_id']);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170828
     * @desc 获取微信配置信息
     * @param $wid
     * //如果用户没有配置微信支付信息则返回会搜微信支付信息 type=1 //商户支付信息，type=2,会搜支付信息
     */
    public function getConf($wid)
    {
        $conf = (new WeChatShopConfService())->getPayConfigByWid($wid);
        if (!$conf){
            $conf = [
                'payee'         => $this->payee,
                'app_id'        => $this->appId,
                'app_secret'    => $this->appSecret,
                'mch_id'        => $this->mch_id,
                'mch_key'       => $this->mch_key,
                'type'          => 2,
                'status'        => 1 //add MayJay
            ];
            return $conf;

        }else{
            $conf['type'] = 1;
            return $conf;
        }
    }


    /**
     * 获取用户定时器key
     * @author 2019年5月6日
     */
    public function getUserUpKey($mid=0)
    {
        return 'merchantlog:info:flag:'.$mid;
    }





}