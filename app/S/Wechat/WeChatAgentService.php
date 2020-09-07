<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/29
 * Time: 8:27
 */

namespace App\S\Wechat;


use App\Lib\Redis\AuthorizationRedis;
use App\Lib\Redis\Wechat;
use App\S\Member\MemberService;
use App\Services\Wechat\AuthorizationService;


class WeChatAgentService
{

    /**
     * @var 服务方Appid
     */
    protected $component_appid;

    /**
     * @var店铺公众号appID
     */
    protected $appId;
    protected $appSecret;
    protected $wid;

    //通过code换取access_token
    public function getAccessTokenByCode($appId, $code, $component_appId)
    {
        $wechatRedis = new Wechat('component_verify_ticket');
        $component_verify_ticket = $wechatRedis->get();
        //如果redis中的数据为空（清空redis时）
        if (empty($component_verify_ticket)) {
            $ticketData = (new ComponentTicketService())->getTicketVal();
            if ($ticketData) {
                $component_verify_ticket = $ticketData['component_verify_ticket'];
            } else {
                error('component_verify_ticket数据为空，请稍后再试');
            }
        }
        $service = new AuthorizationService();
        $component_access_token = $service->getComponentAccessToken($component_verify_ticket);
        $url = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=" . $appId . "&code=" . $code . "&grant_type=authorization_code&component_appid=" . $component_appId . "&component_access_token=" . $component_access_token;
        $res = jsonCurl($url);
        return $res;
    }


    //获取用户信息
    public function getUserInfo($access_token, $openId)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openId . "&lang=zh_CN";
        $res = jsonCurl($url);
        return $res;
    }

    //有微信配置（服务号）的店铺处理
    public function handleConfigUserData($userInfo, $auth)
    {
        // 构建会员数据
        $memberDatas['nickname'] = $userInfo['nickname'] ?: '会员' . str_random(2) . '_' . str_numeric_random(8);
        $memberDatas['headimgurl'] = $userInfo['headimgurl'] ?: config('app.source_url') . 'public/static/images/member_default.png';
        $memberDatas['sex'] = $userInfo['sex'] ?: 0;
        // 新增/编辑会员信息（db+redis）
        $memberInfo = (new MemberService())->getRowByOpenid($auth['openid']);
        if ($memberInfo) {
            $mid = $memberInfo['id'];
            $result = (new MemberService())->updateData($mid, $memberDatas);
        } else {
            // 新增
            $memberDatas['wid'] = $this->wid;
            $memberDatas['openid'] = $userInfo['openid'];
            $memberDatas['truename'] = $memberDatas['nickname'];
            $memberDatas['score'] = 0;
            $memberDatas['source'] = 1;
            $memberDatas['buy_num'] = 0;
            $memberDatas['province_id'] = 0;
            $memberDatas['city_id'] = 0;
            $memberDatas['area_id'] = 0;
            $memberDatas['is_member'] = 0;
            $mid = (new MemberService())->add($memberDatas);
            $result = $mid;
        }
        if (!$result) {
            error('获取用户信息失败');
        }
        //如果该店铺有重新绑定标识
        $shopRedis = new AuthorizationRedis('wid_' . $this->wid . '_reset_flag');
        $shop_reset_flag = $shopRedis->get();
        if ($shop_reset_flag != false) {
            $memberRedis = new AuthorizationRedis($this->wid . '_mid_' . $mid . '_reset_flag');
            $member_reset_flag = $memberRedis->get();
            if ($memberRedis == false || $member_reset_flag != $shop_reset_flag) {
                $memberRedis->set($shop_reset_flag);
            }
        }
        return $mid;
    }

    //无微信配置
    public function handleNoConfigUser($openId = '')
    {
        $memberDatas['nickname'] = '会员' . str_random(2) . '_' . str_numeric_random(8);
        $memberDatas['headimgurl'] = config('app.source_url') . 'public/static/images/member_default.png';
        $memberDatas['sex'] = 0;
        // 新增/编辑会员信息（db+redis）
        $memberInfo = (new MemberService())->getRowByOpenid($openId);
        if ($memberInfo) {
            // 编辑
            $mid = $memberInfo['id'];
            $result = (new MemberService())->updateData($mid, $memberDatas);
        } else {
            // 新增
            $memberDatas['wid'] = $this->wid;
            $memberDatas['openid'] = $openId;
            $memberDatas['truename'] = $memberDatas['nickname'];
            $memberDatas['score'] = 0;
            $memberDatas['source'] = 1;
            $memberDatas['buy_num'] = 0;
            $memberDatas['province_id'] = 0;
            $memberDatas['city_id'] = 0;
            $memberDatas['area_id'] = 0;
            $memberDatas['is_member'] = 0;
            $mid = (new MemberService())->add($memberDatas);
            $result = $mid;
        }
        if (!$result) {
            error('获取用户信息失败');
        }
        return $mid;
    }

}