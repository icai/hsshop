<?php
namespace App\Services\Wechat;

use App\Exceptions\WechatException;
use App\Lib\Redis\AuthorizationRedis;
use App\Lib\Redis\Wechat;
use App\Lib\Weixin\WXBizMsgCrypt;
use App\S\Wechat\WeChatShopConfService;
use App\S\Wechat\WeChatShopService;
use Log;
use PaymentService;
use App\S\Wechat\ComponentTicketService;
use DB;
use App\S\Wechat\WeixinCustomMenuService;
use App\S\Wechat\WeixinMaterialAdvancedService;
use App\S\Wechat\WeixinMaterialWechatService;
use App\S\Wechat\WeixinReplyRuleService;
use App\S\Weixin\ShopService;
use App\Lib\Redis\ShopRedis;

class AuthorizationService
{

    protected $AppId;
    protected $AppSecret;
    protected $EncodingAesKey = 'A78L1bp0Hlk4FyQrwu70S8jC7o04pkDzM4rnPypW28r';
    protected $Token = '7N3WE6P8X';

    public function __construct()
    {
        $this->AppId = config('app.auth_appid');
        $this->AppSecret = config('app.auth_secret');
    }

    public function getComponentVerifyTicket($input, $from_xml)
    {
        $wechatRedis = new Wechat('component_verify_ticket');
        $pc = new WXBizMsgCrypt($this->Token, $this->EncodingAesKey, $this->AppId);

        $msg = '';
        $errCode = $pc->decryptMsg($input['msg_signature'], $input['timestamp'], $input['nonce'], $from_xml, $msg);

        if ($errCode == 0) {
            $param = PaymentService::xmlToArray($msg);
            switch ($param ['InfoType']) {
                case 'component_verify_ticket' : // 授权凭证
                    $component_verify_ticket = $param ['ComponentVerifyTicket'];
                    (new ComponentTicketService())->storageTicket($component_verify_ticket);
                    $wechatRedis->set($component_verify_ticket); // 保存到redis
                    $component_access_token = $this->getComponentAccessToken($component_verify_ticket);//获取access_token
                    break;
                case 'unauthorized' : // 取消授权
                    $status = 2;
                    //add by wuxiaoping 2018.05.02
                    $weChatShopConfService = new WeChatShopConfService();
                    $configSub = $weChatShopConfService->getConfigByAppid($param['AuthorizerAppid']);
                    if ($configSub) {
                        $this->relieveAuth($configSub['wid']);
                    }
                    break;
                case 'authorized' : // 授权
                    $status = 1;
                    break;
                case 'updateauthorized' : // 更新授权
                    break;
            }
        } else {
            Log::info($errCode . "\n");
        }
    }


    /**
     * 通过ticket获取component_access_token
     * @param  [string] $verify_ticket [开放平台接收到的ticket值]
     * @return [array]  component_access_token + expires_in过期时间
     */
    public function getComponentAccessToken($component_verify_ticket)
    {

        $wechatRedis = new Wechat('component_access_token');

        //先读redis，如果存在并且没有过期，否则从微信服务器获取
        if (!$wechatRedis->get()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
            $data['component_verify_ticket'] = $component_verify_ticket;
            $data['component_appid'] = $this->AppId;
            $data['component_appsecret'] = $this->AppSecret;
            $ret = jsonCurl($url, json_encode($data));
            $component_access_token = $ret['component_access_token'] ?? '';

            $wechatRedis->set($component_access_token);
        } else {
            $component_access_token = $wechatRedis->get();
        }

        return $component_access_token;
    }

    /**
     * [getPreAuthCode description]
     * @return [type] [description]
     */
    public function getPreAuthCode()
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
        $component_access_token = $this->getComponentAccessToken($component_verify_ticket);
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $component_access_token;
        $data['component_appid'] = $this->AppId;
        $result = jsonCurl($url, json_encode($data));
        return $result;
    }

    /**
     * 用auth_code获取实际的authorizer_access_token
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getAuthrizerAccessToken($auth_code, $wid = 0)
    {
        //读取component_access_token的redis值
        $wechatRedis = new Wechat('component_verify_ticket');
        $component_access_token = $this->getComponentAccessToken($wechatRedis->get());

        //用auth_code获取authorizer_access_token
        $data['component_appid'] = $this->AppId;
        $data['authorization_code'] = $auth_code;
        try {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=' . $component_access_token;
            $result = jsonCurl($url, json_encode($data));
            if (empty($result)) {
                error('access_token过期或数据异常');
            }
        } catch (\Exception $e) {
            \Log::info('获取实际authorizer_access_token异常错误---' . $e->getMessage());
            error($e->getMessage());
        }
        $ret = [];
        $ret ['authorizer_appid'] = $result['authorization_info']['authorizer_appid'];
        $ret['authorizer_access_token'] = $result['authorization_info']['authorizer_access_token'];
        $ret['expires_in'] = $result['authorization_info']['expires_in'];
        $ret['authorizer_refresh_token'] = $result['authorization_info']['authorizer_refresh_token'];
        //保存authorizer_access_token到redis
        $authorizer_access_token = $ret['authorizer_access_token'];
        $wechatAuthorizerAccessTokenRedis = new Wechat('authorizer_access_token');
        $wechatAuthorizerAccessTokenRedis->set($authorizer_access_token);
        \Log::info('AccessTokenRedis-----' . $wechatAuthorizerAccessTokenRedis->exists());
        //authorizer_access_token值过期，则重新刷新
        if (!$wechatAuthorizerAccessTokenRedis->exists()) {
            $ret = $this->refreshAccessToken($result['authorization_info']['authorizer_appid'], $result['authorization_info']['authorizer_refresh_token'], $component_access_token);
        }

        if ($wid) {
            $shopService = new ShopService();
            // 更新数据库和redis (access_token的值)
            /*$uid = D('Weixin')->model->where('id', $wid)->value('uid');
            $weixinService = D('Weixin', 'uid', $uid);*/
            $datas['access_token'] = $ret['authorizer_access_token'];
            $datas['expires_at'] = date('Y-m-d H:i:s', $ret['expires_in'] + time());
            $datas['authorizer_refresh_token'] = $ret['authorizer_refresh_token'];
            //$rs = $weixinService->where(['id'=>$wid])->update($datas, false);
            $rs = $shopService->update($wid, $datas);
            if (!$rs) {
                error('数据更新失败');
            }

        }
        return $ret;

    }

    /**
     * 通过authorizer_refresh_token刷新authorizer_access_token
     * @param  [type] $authorizer_appid         [公众号的appid]
     * @param  [type] $authorizer_refresh_token [接口调用凭据刷新令牌]
     * @return [type]                           [description]
     */
    public function refreshAccessToken($authorizer_appid, $authorizer_refresh_token, $component_access_token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;
        $data['component_appid'] = $this->AppId;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['authorizer_refresh_token'] = $authorizer_refresh_token;
        $result = jsonCurl($url, json_encode($data));

        return $result;
    }

    /**
     * 获取授权方的帐号基本信息
     * 该API用于获取授权方的基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL。
     * 需要特别记录授权方的帐号类型，在消息及事件推送时，对于不具备客服接口的公众号，需要在5秒内立即响应；而若有客服接口，则可以选择暂时不响应，而选择后续通过客服接口来发送消息触达粉丝。
     *
     * 保存用户信息到数据库
     * @param  [type] $authorizer_appid       [服务appid]
     * @param  [type] $component_access_token [保存在本地的access_token]
     * @param  [int] $wid  店铺id
     * @param  [int] $uid  用户id
     * @param  [string] $type 区别更新授权 当type有值，且type=updateauthorized时，表示更新授权
     * @return [type]                         [用户信息]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getAuthUsers($authorizer_appid, $component_access_token, $wid, $uid, $type = '')
    {
        $request = app('request');
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $component_access_token;
        $data['component_appid'] = $this->AppId;
        $data['authorizer_appid'] = $authorizer_appid;

        $userInfo = jsonCurl($url, json_encode($data));
        //店铺配置副表(公众号配置)
        //先查看weixin表的字段access_token值是否为空

        /***********************重写***************************/
        $weChatShopConfService = new WeChatShopConfService();
        $data = $weChatShopConfService->getList(['original_id' => $userInfo['authorizer_info']['user_name']]);

        //保存weixin_conf_sub表记录
        $configData['wid'] = $wid;
        $configData['payee'] = $configData['name'] = $userInfo['authorizer_info']['nick_name'];
        $configData['original_id'] = $userInfo['authorizer_info']['user_name'];
        $configData['wechat_id'] = $userInfo['authorizer_info']['alias'];
        $configData['app_id'] = $authorizer_appid;
        $configData['service_type_info'] = $userInfo['authorizer_info']['service_type_info']['id']; //授权公众号类型 0-订阅号 1-订阅号升级 2-服务号
        $configData['verify_type_info'] = $userInfo['authorizer_info']['verify_type_info']['id']; //授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证
        $configData['app_secret'] = '';
        $configData['mch_id'] = '';
        $configData['mch_key'] = '';

        //update by wuxiaoping 2018.01.23
        //更新授权操作
        if ($type && $type == 'updateauthorized') {
            if (empty($data)) {
                error('只支持更新当前公众号授权类目，或者头像等信息更新。不支持绑定其他公众号');
            } else {
                $id = $data[0]['id'];
                $rs = $weChatShopConfService->update($id, $configData);
            }
        } else {
            if (!empty($data)) {
                error('该微信公众号已在其他店铺完成绑定，无法绑定当前店铺');
            } else {
                $rs = $weChatShopConfService->createData($configData);
            }
        }
        /* if(!empty($data)){
             if ($type && $type == 'updateauthorized') {
                 $id = $data[0]['id'];
                 $rs = $weChatShopConfService->update($id,$configData);
             }else {
                 error('该微信公众号已在其他店铺完成绑定，无法绑定当前店铺');
             }
         }else {
              $rs = $weChatShopConfService->createData($configData);
         }*/

        if ($rs) {
            // 同步店铺redis数据
            /*weixinService = D('Weixin', 'uid', $uid);
            $redisDatas['weixinConfigSub'] = $configData;
            $weixinService->setRedisKey()->getInfo($wid);
            $dbResult = $weixinService->updateR($wid, $redisDatas, false);*/
            $shopService = new ShopService();
            $redisDatas['weixinConfigSub'] = json_encode($configData);
            (new ShopRedis())->updateHashRow($wid, $redisDatas);
        } else {
            error('授权失败，请重试');
        }

        return $userInfo;
    }

    /**
     * 微信公众号解除绑定
     * @param  int $wid [店铺id]
     * @return [type]      [description]
     * add by wuxiaoping 2018.05.02
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function relieveAuth(int $wid)
    {
        $result = DB::transaction(function () use ($wid) {
            /****清空菜单***/
            $WeixinCustomMenuService = new WeixinCustomMenuService();
            $menuDatas = $WeixinCustomMenuService->getAllList($wid, [], false);
            foreach ($menuDatas as $menu) {
                $WeixinCustomMenuService->del($menu['id']);
            }

            /***清空图文信息***/
            //高级图文
            $weixinMaterialAdvancedService = new WeixinMaterialAdvancedService();
            $advancedData = $weixinMaterialAdvancedService->getAllList($wid, [], false);
            foreach ($advancedData as $ad) {
                $weixinMaterialAdvancedService->del($ad['id']);
            }

            //微信图文
            $weixinMaterialWechatService = new WeixinMaterialWechatService();
            $wechatData = $weixinMaterialWechatService->getAllList($wid, [], false);
            foreach ($wechatData as $we) {
                $weixinMaterialWechatService->del($we['id']);
            }

            /****清空关键回复规则 清空redis与数据库****/
            $weixinReplyRuleService = new WeixinReplyRuleService();
            $replyRuleData = $weixinReplyRuleService->getAllList($wid, [], false);
            foreach ($replyRuleData as $reply) {
                $weixinReplyRuleService->del($reply['id']);
            }

            /****清空关键词****/
            //mysql操作
            $weixinReplyKeywordService = D('WeixinReplyKeyword');
            $weixinReplyKeywordService->model->wheres(['wid' => $wid])->delete();

            /*******清空回复内容****/
            // mysql操作
            $weixinReplyContentService = D('WeixinReplyContent');
            $dbResult = $weixinReplyContentService->model->wheres(['wid' => $wid])->delete();

            /**********************重写*****************************/
            //删除微信支付信息配置
            $weChatConfService = new WeChatShopConfService();
            if ($weChatConfService->getRowByWid($wid)) {
                $res = $weChatConfService->delData(['wid' => $wid]);
            }

            /**********************end*****************************/
            if ($res) {
                //添加店铺解绑标识
                $authRedis = new AuthorizationRedis('wid_' . $wid . '_unset_flag');
                $authRedis->set(time());
                //更新redis数据
                $redisDatas['weixinConfigSub'] = ''; //把configSub的数据设置为空值，然后更新redis
                $redisDatas['weixinPayments'] = '';
                /*$weixinService = D('Weixin', 'uid', session('userInfo')['id']);
                $weixinService->updateR($wid, $redisDatas, false);*/
                (new ShopRedis())->updateHashRow($wid, $redisDatas);
            }
            return true;
        });

        return $result;
    }


    /**
     * 重新获取基本信息
     * @param $component_access_token
     * @param $authorizer_appid
     * @return string
     * @throws WechatException
     * @author: 梅杰 20190122
     */
    public function getRefreshToken($component_access_token, $authorizer_appid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $component_access_token;
        $data['component_appid'] = $this->AppId;
        $data['authorizer_appid'] = $authorizer_appid;

        $re = jsonCurl($url, json_encode($data));

        if (!empty($re['errcode'])) {

            throw new WechatException('获取账户基本信息失败：' . $re['errcode'] . "(" . $re['errmsg'] . ")");

        }

        return $re['authorization_info']['authorizer_refresh_token'];
    }

}


?>
