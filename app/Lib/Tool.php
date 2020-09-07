<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/7/8
 * Time: 9:12
 * Desc: 工具类
 */

namespace App\Lib;


use App\Exceptions\CurlException;

class Tool
{

    /**
     * curl 请求
     *
     * @param string $requestUrl 请求地址
     * @param string $requestData 参数json
     * @param int $timeout 超时控制
     *
     * @return mixed 请求响应结果集
     *
     * @throws CurlException
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年07月08日 09:23:03
     */
    public static function request(string $requestUrl, string $requestData = null, int $timeout = 10)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '119.29.29.29');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 80);
        if (stripos($requestUrl, 'https://') !== false) {
            // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 自动设置Referer
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        if (!empty($requestData)) {
            // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POST, true);
            // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errNo = curl_errno($ch);
        curl_close($ch);

        if (intval($info['http_code']) == 200) {
            return json_decode($result, true);
        }

        \Log::info('curl fail:' . $errNo);
        throw new CurlException('curl fail:' . $errNo);
    }
}