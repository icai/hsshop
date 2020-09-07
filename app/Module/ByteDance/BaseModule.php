<?php

namespace App\Module\ByteDance;

use App\Lib\Redis\RedisClient;
use App\Model\ByteDanceConfig;
use App\Module\MemberModule;
use App\S\Member\MemberService;
use Illuminate\Http\Request;

/**
 * Created by zhangyh.
 * User: 张永辉 [zhangyh_private@foxmail.com]
 * Date: 2019/9/19 16:08
 * Desc 字节跳动基础服务类
 */
class BaseModule
{

    /**
     * @desc 获取字节跳动token地址链接
     * @author 张永辉 2019年12月2日14:55:53
     */
    const accessTokenUrl = 'https://developer.toutiao.com/api/apps/token';
    /**
     * @desc 字节跳动授权登陆地址
     * @author 张永辉 2019年12月2日14:56:29
     */
    const loginUrl = 'https://developer.toutiao.com/api/apps/jscode2session';
    /**
     * @desc 登陆过期时间
     * @author 张永辉 2019年12月2日14:56:57
     */
    const timeOut = 259200;

    /**
     * @desc 获取店铺accesstoken
     * @param $wid int 店铺id
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public static function getAccessToken($wid)
    {
        $key = self::getAccessTokenKey($wid);
        $redisClient = (new RedisClient())->getRedisClient();
        if ($accessToken = $redisClient->get($key)) {
            return $accessToken;
        }
        $result = jsonCurl(self::getAccessTokenUrl($wid));
        if (empty($result['errcode'])) {
            $redisClient->setex($key, $result['expires_in'] - 200, $result['access_token']);
            return $result['access_token'];
        } else {
            error($result['errmsg']);
        }
    }


    /**
     * @desc 设置企业配置信息
     * @param $wid 店铺id
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public static function getAccessTokenUrl($wid)
    {
        $byteDanceconfig = ByteDanceConfig::where('wid', $wid)->first();
        return self::accessTokenUrl . '?appid=' . ($byteDanceconfig->appid ?? '') . '&secret=' . ($byteDanceconfig->secret ?? '') . '&grant_type=client_credential';
    }

    /**
     * @desc 获取redis存储key
     * @param $wid 店铺id
     * @return string
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public static function getAccessTokenKey($wid)
    {
        return 'byte:dance:access_token:' . $wid;
    }

    /**
     * @desc 登陆接口
     * @param $input
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public function login($input)
    {
        $result = jsonCurl($this->getLoginUrl($input));
        if (!empty($result['errcode'])) {
            xcxerror($result['errmsg']);
        }
        $memberService = new MemberService();
        $redisClient = (new RedisClient())->getRedisClient();
        $memberData = $memberService->model->where('byte_openid', $result['openid'])->where('wid', $input['wid'])->select(['id', 'wid'])->first();
        if ($memberData) {
            $token = $this->getToken();
            $res = [
                'token'       => $token,
                'mid'         => $memberData->id,
                'wid'         => $input['wid'],
                'openid'      => $result['openid'],
                'session_key' => $result['session_key'],
            ];
            $redisClient->setex($this->getTokenKey($token), self::timeOut, json_encode($res));
            return $res;
        }

        $data = [
            'wid'         => $input['wid'],
            'byte_openid' => $result['openid'],
            'source'      => 8
        ];
        $memberModule = new MemberModule();
        try {
            if (!$memberModule->memberCheck($input['wid'], $result['openid'])) {
                throw new \Exception('memberCheck 插入失败');
            }
        } catch (\Exception $exception) {
            xcxerror('重复登陆');
        }
        $mid = $memberService->add($data);
        $token = $this->getToken();
        $res = [
            'token'       => $token,
            'mid'         => $mid,
            'wid'         => $input['wid'],
            'openid'      => $result['openid'],
            'session_key' => $result['session_key'],
        ];
        $redisClient->setex($this->getTokenKey($token), self::timeOut, json_encode($res));
        return $res;
    }


    /**
     * @desc 获取登陆的连接信息
     * @param $input
     * @return string
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public function getLoginUrl($input)
    {
        $byteDanceconfig = ByteDanceConfig::where('wid', $input['wid'])->first();
        return self::loginUrl . '?appid=' . ($byteDanceconfig->appid ?? '') . '&secret=' . ($byteDanceconfig->secret ?? '') . '&code=' . $input['code'];
    }

    /**
     * @desc 获取字节跳动小程序token
     * @return string token
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public function getToken()
    {
        $token = str_random(10) . '_bytedance';
        $redisClient = (new RedisClient())->getRedisClient();
        if ($redisClient->get($this->getTokenKey($token))) {
            $this->getToken();
        }
        return $token;
    }

    /**
     * @desc token 存储rediskey值
     * @param $token token
     * @return string key
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     */
    public function getTokenKey($token)
    {
        return 'byte:dance:token:' . $token;
    }


    /**
     * @desc
     * @param $token
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 23 日
     */
    public function checkLogin($token, Request $request)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $res = $redisClient->get($this->getTokenKey($token));
        if (!$res) {
            return false;
        }
        $redisClient->EXPIRE($this->getTokenKey($token), self::timeOut);
        $result = json_decode($res, true);
        $request->offsetSet('mid', $result['mid']);
        $request->offsetSet('wid', $result['wid']);
        return $result;
    }


    /**
     * @desc 获取token中的数据
     * @param $token 令牌
     * @return bool|mixed
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 23 日
     */
    public function getTokenData($token)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $res = $redisClient->get($this->getTokenKey($token));
        if (!$res) {
            return [];
        }
        $result = json_decode($res, true);
        return $result;
    }

}