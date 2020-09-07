<?php
namespace App\Lib\WXXCX;
use CurlBuilder;
use App\Lib\BLogger;
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/7
 * Time: 10:37
 */
class WXXCXHelper
{

    /**
     * @var string
     */
    private $appId;
    private $secret;
    private $code2session_url;

    function __construct($wxConfig)
    {
        $this->appId = isset($wxConfig["appid"]) ? $wxConfig["appid"] : "";
        $this->secret = isset($wxConfig["secret"]) ? $wxConfig["secret"] : "";
        $this->code2session_url = isset($wxConfig["code2session_url"]) ? $wxConfig["code2session_url"] : "";
    }
    /**
     * todo 获取用户openid 和 sessionKey
     * @author add by jonzhang
     * @date 2017-08-08
     */
    public function getLoginInfo($code)
    {
        try
        {
           $result= $this->authCodeAndCode2session($code);
        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            return ['code'=>ErrorCode::$SessionExpired,'message'=>$message];
        }
        return $result;
    }
    /**
     * todo 如果想解析前端传递过来的用户信息数据 可以使用此方法 2018-04-18
     * todo 根据加密后的数据得到原始的用户数据
     * @author add by jonzhang
     * @date 2017-08-08
     */
    public function getUserInfo($encryptedData,$iv,$sessionKey)
    {
        $pc = new WXXCXBizDataCrypt($this->appId,$sessionKey);
        $decodeData = "";
        //解密用户数据
        $errCode = $pc->decryptData($encryptedData, $iv, $decodeData);
        if ($errCode!=0)
        {
            return ['code'=>$errCode,'message'=>'weixin_decode_fail'];
        }
        if(!empty($decodeData))
        {
            $decodeData=json_decode($decodeData,true);
        }
        return ['code'=>ErrorCode::$OK,'data'=>$decodeData];
    }
    /**
     * todo 通过调用微信小程序api,获取openid和sessionKey
     * @author add by jonzhang
     * @date 2017-08-08
     */
    private function authCodeAndCode2session($code)
    {
        try
        {
            $code2session_url = sprintf($this->code2session_url,$this->appId,$this->secret,$code);
            //BLogger::getLogger('info')->info('code2session_url:'.$code2session_url);
            //此处需要测试此接口的可用性
            $jsonData = CurlBuilder::to($code2session_url)->get();
            $authInfo = json_decode($jsonData, true);
            if (isset($authInfo['openid']))
            {
                return ['code' => 0, 'data' => $authInfo];
            }
            else
            {
                $errCode=$authInfo['errcode']??'错误码为空';
                $errMsg=$authInfo['errmsg']??'错误信息为空';
                //获取openid出现问题后 记录下请求的url
                BLogger::getLogger('error')->error('code2session_url:'.$code2session_url);
                return ['code' => -1, 'message' =>'code:'.$errCode.',msg:'.$errMsg];
            }
        }
        catch(\Exception $ex)
        {
            return ['code' => -100, 'message' => $ex->getMessage()];
        }
    }

    /**
     * 读取/dev/urandom获取随机数
     * @param $len
     * @return mixed|string
     */
    public static function randomFromDev($len){
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }
        else
        {
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');

        return substr($result, 0, $len);
    }
}