<?php

namespace App\Services\Lib;

use App\Lib\Redis\Wechat;
use App\Services\Wechat\ApiService;

/**
 * 移动端微信分享功能
 */
class JSSDK
{

    private $appId;
    private $appSecret;
    private $wid;

    public function __construct($appId, $appSecret, $wid = 0)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->wid = $wid;
    }

    public function getSignPackage($url = '')
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // //处理$url的80端口
        // if(strpos($url,':80')){
        // 	$url = str_replace(':80', '', $url);
        // }
        //$url = app('request')->url();
        //$url = url()->current();

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新
        $wechatRedis = new Wechat($this->wid . 'jsTicket');
        if (!$wechatRedis->exists()) {
//		    $accessToken = $this->getAccessToken();
            $accessToken = (new ApiService())->getAccessToken($this->wid);
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = jsonCurl($url);
            $ticket = $res['ticket'];
            $wechatRedis->set($ticket);
        } else {
            $ticket = $wechatRedis->get();
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新
        $wechatRedis = new Wechat($this->wid . 'jsAccessToken');
        if (!$wechatRedis->exists()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = jsonCurl($url);
            if (isset($res['error']) && $res['error']) {
                return myerror('微信公众号APPID或APPSECRET设置错误');
            }
            $access_token = $res['access_token'];
            $wechatRedis->set($access_token);

        } else {
            $access_token = $wechatRedis->get();
        }

        return $access_token;
    }

}

