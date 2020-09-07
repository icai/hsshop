<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/2/23
 * Time: 10:04
 */

namespace App\Module;


use App\Lib\Redis\RedisClient;
use App\Model\User;
use App\S\Staff\SellerappAdService;

class BaseModule
{

    public $redis;
    public $timeOut;

    public function __construct()
    {
        $this->redis = (new RedisClient())->getRedisClient();
        $this->timeOut = config('app.app_login_time_out')??259200;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180223
     * @desc 获取基础信息
     * @udpate 张永辉 2019年7月22日 添加新的APP版本
     */
    public function getBase($input)
    {
        $token = $input['token']??'';
        if ($token && $this->redis->EXISTS($this->getKey($token))){
            $key = $this->getKey($token);
            $this->redis->EXPIRE($key, $this->timeOut);
            $tokenData = json_decode($this->redis->GET($this->getKey($token)),true);
            $result = [
                'token'     => $token,
                'is_login'  => $tokenData['is_login'],
            ];
        }else{
            $result = $this->_getToken();
        }
        if(empty($input['platform'])){
            apperror('平台和版本信息不能为空');
        }
        if ($input['platform'] == '1'){
            $androidHost = config('sellerapp.android');
            $result['api_host'] = $androidHost[$input['version']]??end($androidHost);
        }elseif ($input['platform'] == '2'){
            $iosHost = config('sellerapp.ios');
            $result['api_host'] =  $iosHost[$input['version']]??end($iosHost);
        }
        $result['img_url'] = imgUrl();
        $result['source_url'] = config('app.source_url');
        $result['single_page_url'] = config('app.single_page_url');
        $result['androidcode'] = config('sellerapp.version')['android'];
        $result['ioscode'] = config('sellerapp.version')['ios'];
        $result['androidurl'] = 'http://image.cdn.huisou.cn/hsyun20200821.apk';
        $result['iosurl'] = 'https://itunes.apple.com/cn/app/%E6%B1%87%E6%90%9C%E4%BA%91%E5%95%86%E5%AE%B6%E7%89%88/id1376377732?mt=8';
        $result['androidcontent'] = '新增 用户协议 隐私政策 账号注销常驻入口';
        $result['ioscontent'] = '客服功能';
        $result['isforce'] = '0';
        $result['isframe'] = '0';
        $result['phone'] = '0571-87796692';
        $adResult = $this->getAd();
        $result['adpic'] = $adResult['img']??'';
        $result['adurl'] = $adResult['url']??'';
        $result['adsec'] = $adResult['sec']??'';
        $upTokenData = [
            'baseInfo'  => [
                'platform'      => $input['platform'],
                'version'       => $input['version'],
            ],
        ];
        $this->setDataInToken($result['token'],$upTokenData);

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date $20180305
     * @desc 获取登陆令牌
     */
    private function _getToken()
    {
        $token = md5(uniqid('',true));
        $key = $this->getKey($token);
        if ($this->redis->EXISTS($key)){
            return $this->_getToken();
        }else{
            $result = [
                'token'      => $token,
                'is_login'   => 0
            ];
            $this->redis->SET($key,json_encode($result));
            $this->redis->EXPIRE($key, $this->timeOut);
            return $result;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180223
     * @desc 获取关键字
     * @param $token
     * @return string
     */
    public function getKey($token)
    {
        return 'seller_app:'.$token;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180306
     * @desc 向token中写入数据
     */
    public function setDataInToken($token,$data)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        if (!$token || !$data || !is_array($data) || !is_string($token)){
            $result['errCode'] = 40003;
            $result['errMsg'] = '令牌不能为空或数据必须是数组';
            return $result;
        }
        $key = $this->getKey($token);
        $sourceData = json_decode($this->redis->GET($key),true);
        if ($sourceData){
            $lastData = array_merge($sourceData,$data);
        }else{
            $result['errCode'] = 40003;
            $result['errMsg'] = '令牌已过期或不存在';
            return $result;
        }

        $this->redis->SET($key,json_encode($lastData));
        $this->redis->EXPIRE($key, $this->timeOut);
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date $20180306
     * @desc 获取token数据
     * @param $token
     * @param array $field
     * @return array|mixed
     */
    public function getTokenData($token,$field = [])
    {
        if (!$token){
            return [];
        }
        $key = $this->getKey($token);
        $result = $this->redis->GET($key);
        if (!$result){
            return [];
        }
        $this->redis->EXPIRE($key, $this->timeOut);
        $result = json_decode($result,true);
        if ($field){
            $temp = [];
            foreach ($field as $item) {
                $temp[$item] = array_column($result,$item);
            }
            return $temp;
        }else{
            return $result;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180315
     * @desc 删除token信息
     * @param $token
     * @param $field
     */
    public function delTokenData($token,$field)
    {
        if (!$token){
            return false;
        }
        $key = $this->getKey($token);
        $result = $this->redis->GET($key);
        if (!$result){
            return false;
        }
        $this->redis->EXPIRE($key, $this->timeOut);
        $result = json_decode($result,true);
        foreach ($field as $val){
            if (isset($result[$val])){
                unset($result[$val]);
            }
        }
        $this->redis->SET($key,json_encode($result));
        return true;

    }


    function curl($url, $datas = [], $second = 30)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '119.29.29.29');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 80);
        if ( stripos($url, 'https://') !== false ) {
            // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 自动设置Referer
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        if ( !empty($datas) ) {
            // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POST, true);
            // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        $res = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);

        if ( intval($info['http_code']) == 200 ) {
            $result['errCode'] = 0;
            $result['errMsg'] = '';
            $result['data'] = json_decode($res, true);
            return $result;
        } else {
            $result['errCode'] = -11;
            $result['errMsg'] = '请求失败';
            return $result;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180502
     * @desc 获取广告页
     */
    public function getAd()
    {
        $sellerAdService = new SellerappAdService();
        $res = $sellerAdService->getlistByWhere(['is_open'=>'1']);
        $res = current($res);
        if (!empty($res['img'])){
            $res['img'] = imgUrl($res['img']);
        }
        return $res;
    }

    /**
     * @description: 注销账号
     * @param $uid 登录账号对应的主键id
     * @return bool|mixed
     * @author 吴晓平 2019年12月27日 18:00:31
     */
    public function accountCancel($uid)
    {
        if (User::query()
            ->where('id', $uid)
            ->exists()) {
            return User::query()->where('id', $uid)->delete();
        }
        return false;
    }
}
