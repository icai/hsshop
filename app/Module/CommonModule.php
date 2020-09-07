<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/17
 * Time: 14:37
 */

namespace App\Module;
use App\Lib\WXXCX\ThirdPlatform;
use WXXCXCache;
use QrCode;
use QrCodeService;
use APP\Module\ByteDance\BaseModule;

class CommonModule
{
    /**
     * todo 通过token 获取mid
     * @param $token
     * @author jonzhang
     * @date 2017-08-17
     * @update 张永辉 字节跳动小程序相关返回
     */
    public function getMidByToken($token)
    {
        //$token不存在
        if(empty($token))
        {
            return false;
        }//判断token是否过期

        if (strpos($token, '_bytedance') !== false) {
            $result = (new BaseModule())->getTokenData($token);
            return $result['mid'] ?? false;
        }

        $xcxUser=WXXCXCache::get($token,'3rd_session');
        if(!$xcxUser)
        {
            return false;
        }//分割数据
        $userInfo=explode(',',$xcxUser);
        //mid不存在返回
        if(empty($userInfo[2]))
        {
            return false;
        }
        return $userInfo[2];
    }

    /***
     * todo 通过token 获取wid[店铺id]
     * @param $token
     * @return bool
     * @author jonzhang
     * @date 2017-08-17
     * @update 张永辉 字节跳动小程序返回wid
     */
    public function getWidByToken($token)
    {
        //$token不存在
        if(empty($token))
        {
            return false;
        }//判断token是否过期

        if (strpos($token, '_bytedance') !== false) {
            $result = (new BaseModule())->getTokenData($token);
            return $result['wid'] ?? false;
        }
        $xcxUser=WXXCXCache::get($token,'3rd_session');
        if(!$xcxUser)
        {
            return false;
        }//分割数据
        $userInfo=explode(',',$xcxUser);
        //wid不存在返回
        if(empty($userInfo[1]))
        {
            return false;
        }
        return $userInfo[1];
    }

    /**
     * todo 通过token获取openid
     * @param $token
     * @return bool
     * @author jonzhang
     * @date 2017-08-17
     * @update 张永辉 字节跳动小程序相关返回
     */
    public function getOpenidByToken($token)
    {
        //$token不存在
        if(empty($token))
        {
            return false;
        }//判断token是否过期

        if (strpos($token, '_bytedance') !== false) {
            $result = (new BaseModule())->getTokenData($token);
            return $result['openid'] ?? false;
        }

        $xcxUser=WXXCXCache::get($token,'3rd_session');
        if(!$xcxUser)
        {
            return false;
        }//分割数据
        $userInfo=explode(',',$xcxUser);
        //openid不存在返回
        if(empty($userInfo[0]))
        {
            return false;
        }
        return $userInfo[0];
    }

    /**
     * todo 通过token 获取openid,店铺id,用户id信息
     * @param $token
     * @return array|bool
     * @author jonzhang
     * @date 2017-08-18
     * @update 张永辉 字节跳动小程序相关返回
     */
    public function getAllByToken($token)
    {
        //$token不存在
        if(empty($token))
        {
            return false;
        }//判断token是否过期
        if (strpos($token, '_bytedance') !== false) {
            $result = (new BaseModule())->getTokenData($token);
            return [$result['openid'], $result['wid'], $result['mid']];
        }
        $xcxUser=WXXCXCache::get($token,'3rd_session');
        if(!$xcxUser)
        {
            return false;
        }//分割数据
        $userInfo=explode(',',$xcxUser);
        return $userInfo;
    }

    /**
     * 通过token 获取 小程序配置信息id
     * @param $token
     * @return int
     * @author: 梅杰 20180709
     */
    public function getXcxConfigIdByToken($token)
    {
        if ($token &&  $xcxUser = WXXCXCache::get($token,'3rd_session')) {
            $userInfo = explode(',',$xcxUser);
            return $userInfo[3] ?? 0;
        }
        return 0;
    }

    /**
     * 生成二维码
     * @param int $wid 店铺id
     * @param int $url 扫码后的链接
     * @param int $type 0:微商城, 1:小程序
     * @param int $size 二维码尺寸
     * @return string
     * @author 许立 2018年08月07日
     */
    public function qrCode($wid, $url, $type = 0, $size = 200)
    {
        if (empty($url)) {
            return '';
        }
        if ($type == 0) {
            return QrCode::size($size)->generate(url($url));
        } else {
            return (new ThirdPlatform())->getXCXQRCode($wid, $size, $url);
        }
    }

    /**
     * 下载二维码
     * @param int $wid 店铺id
     * @param int $url 扫码后的链接
     * @param int $type 0:微商城, 1:小程序
     * @param int $size 二维码尺寸
     * @return file
     * @author 许立 2018年08月07日
     */
    public function qrCodeDownload($wid, $url, $type = 0, $size = 200)
    {
        if (empty($url)) {
            return '';
        }
        if ($type == 0) {
            return response()->download(QrCodeService::create($url, '', $size), time() . '.png');
        } else {
            header("Content-type: application/octet-stream");
            header("Content-Disposition:attachment;filename = " . time() . '.png');
            echo base64_decode((new ThirdPlatform())->getXCXQRCode($wid, $size, $url)['data']);
            exit;
        }
    }

    /**
     * todo 通过token获取sessionKey
     * @param $token
     * @return bool
     * @author 梅杰
     * @date 2017-10-24
     */
    public function getSessionKeyByToken($token)
    {
        if ($xcxUser=WXXCXCache::get($token,'3rd_session')) {
            $userInfo = explode(',',$xcxUser);
            return $userInfo[4] ?? false;
        }
       return false;
    }

}