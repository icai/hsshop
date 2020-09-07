<?php
namespace App\Module\BaiduApp;
use App\Lib\Redis\BaiduRedisClient;
use App\Lib\BLogger;

class BaiduClientModule {


	public $redis;
    public $timeOut;
    public $request;

    public function __construct() {
        $this->request   = app('request');
        $this->redis = (new BaiduRedisClient())->getRedisClient();
        $this->timeOut = 259200;
    }

    /**
     * 用登录code换取session_key的值
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getSessionKey($input)
    {
    	$returnData = ['errCode' => 0,'errMsg' => '','data' => []]; 
        $appKey    = config('app.baidu_app_key'); //智能小程序的App Key
        $appSecret = config('app.baidu_app_secret'); //智能小程序的App Secret
        $code = $input['code'] ?? '';
        $wid = $input['wid'] ?? 0;
        if ($code && $wid) {
            $this->redis->SET('loginParam',json_encode(['wid' => $wid,'code' => $code]));
            $this->redis->EXPIRE('loginParam',$this->timeOut);
        }
        $paramInfo = $this->redis->GET('loginParam');
        $paramData = json_decode($paramInfo,true);
        if (empty($paramData)) {
            $returnData['errCode'] = -100;
            $returnData['errMsg'] = '登录code未传或wid未传';
            return $returnData;
        }
        BLogger::getLogger('info','baidu')->info('baiduClientModule param',$paramData);

        try {
            $data = ['code' => $paramData['code'],'client_id' => $appKey,'sk' => $appSecret];
            $url = 'https://openapi.baidu.com/nalogin/getSessionKeyByCode';
            $result = jsonCurl($url,$data);
            BLogger::getLogger('info','baidu')->info('api return result',$result);
            if (isset($result['error']) && $result['error']) {
                $returnData['errCode'] = $result['error'];
                $returnData['errMsg'] = $result['error_description'];
                return $returnData;
            }
        } catch (\Exception $e) {
            $returnData['errCode'] = -500;
            $returnData['errMsg'] = '请求获取session_key失败';
            return $returnData;
        }
        $result['wid'] = $paramData['wid'];
        $this->redis->set('sessionKeyResult',json_encode($result));
        $this->redis->EXPIRE('sessionKeyResult',$this->timeOut);
        $returnData['data'] = $result;
        return $returnData;
    }

    // 
    public function setToken($key,$data) {
        $this->redis->SET($key,$data);
        $this->redis->EXPIRE($key,$this->timeOut);
    }

    /**
     * 获取token存储数据
     * @author 吴晓平 <2018.10.12>
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getToken($data) {
        $token = md5(uniqid('',true));
        $key = 'baiduapp:'.$token;
        if ($this->redis->EXISTS($key)){
            return $this->getToken();
        }else{
            $data['token'] = $token;
            $this->setToken($key,json_encode($data));
            return $data;
        }
    }

    /**
     * 获取token存储数据
     * @param $token
     * @param array $field
     * @return array|mixed
     * @author 吴晓平
     */
    public function getTokenData($token)
    {
        if (!$token){
            return [];
        }
        $key = 'baiduapp:'.$token;
        $result = $this->redis->GET($key);
        if (!$result){
            return [];
        }
        $this->redis->EXPIRE($key, $this->timeOut);
        $result = json_decode($result,true);
        
        return $result;
    }

	/**
     * 数据解密：低版本使用mcrypt库（PHP < 5.3.0），高版本使用openssl库（PHP >= 5.3.0）。
     *
     * @param string $ciphertext    待解密数据，返回的内容中的data字段
     * @param string $iv            加密向量，返回的内容中的iv字段
     * @param string $app_key       创建小程序时生成的app_key
     * @param string $session_key   登录的code换得的
     * @return string | false
     */
    public function decrypt($ciphertext, $iv, $app_key, $session_key) {
        $session_key = base64_decode($session_key);
        $iv = base64_decode($iv);
        $ciphertext = base64_decode($ciphertext);

        $plaintext = false;
        if (function_exists("openssl_decrypt")) {
            $plaintext = openssl_decrypt($ciphertext, "AES-192-CBC", $session_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        } else {
            $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, null, MCRYPT_MODE_CBC, null);
            mcrypt_generic_init($td, $session_key, $iv);
            $plaintext = mdecrypt_generic($td, $ciphertext);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        }
        if ($plaintext == false) {
            return false;
        }

        // trim pkcs#7 padding
        $pad = ord(substr($plaintext, -1));
        $pad = ($pad < 1 || $pad > 32) ? 0 : $pad;
        $plaintext = substr($plaintext, 0, strlen($plaintext) - $pad);

        // trim header
        $plaintext = substr($plaintext, 16);
        // get content length
        $unpack = unpack("Nlen/", substr($plaintext, 0, 4));
        // get content
        $content = substr($plaintext, 4, $unpack['len']);
        // get app_key
        $app_key_decode = substr($plaintext, $unpack['len'] + 4);
        
        return $app_key == $app_key_decode ? $content : false;
    }
}