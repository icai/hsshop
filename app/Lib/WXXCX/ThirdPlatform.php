<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/8
 * Time: 9:12
 */

namespace App\Lib\WXXCX;
use App\Module\XCXModule;
use Illuminate\Http\Request;
use Log;
use WXXCXCache;
use CurlBuilder;
use App\S\WXXCX\WXXCXConfigService;
use App\S\WXXCX\WXXCXComponentTicketService;
use App\Lib\BLogger;
use App\S\WXXCX\WXXCXTemplateService;
use App\S\Staff\StaffOperLogService;
use MemberService;
use App\Jobs\XCXRelease;
use DB;


class ThirdPlatform
{
    //ds_wxxcx_config_operate_log 表 action 目前使用到的是19
    private $appId='';
    private $appSecret='';
    private $token='';
    private $encodingAesKey='';

    public function __construct()
    {
        $this->appId =config('app.third_appId');
        $this->appSecret =config('app.third_appSecret');
        $this->token =config('app.third_token');
        $key=config('app.third_encodingAesKey');
        $this->encodingAesKey =base64_decode($key . "=");
    }

    /**
     * todo 小程序第三方平台
     * @param Request $request
     * @author jonzhang
     * @date 2017-09-08
     */
    public function receiveEvent(Request $request)
    {
        $responseData= isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        if (empty($responseData))
        {
            //接收回调数据
            $responseData= file_get_contents('php://input');
        }
        try
        {
            if ($responseData) {
                //回调数据转化为数组
                $responseData = $this->xmlToArray($responseData);
                /**
                 * json_encode($responseData);
                 * 接收的数据为：
                 * {"AppId":"wxd881b53d164067db","Encrypt":"d9CkhjqN++cHq+sciGKoIL30kUU3r4qzNZhAoK84GZqTppFrEXb5NqGL0e0uaU2otzPxSgyoHDa99GOimMyrpqoCX6XoMgS1kSNPySXyjZTrBe4+EXFKTglmaKkvO4GzsXBoNJ+CCUkc1+zvzcWFeahexcmUpV\/cPmqAg8RQwLOmeFNFgJhd7e\/U8kb2VXjF1woAJ\/uvQhcsIAxrcZzPFTAnNhYptA9WES61VH1hcQQqUrJum8G4DPRxQOarvOkX0h8I0tdls5Ir8pZLA7uRRdHpR5YDR\/3oV772vInFThz4+l\/ZBVJH8VjgANaTvEFC93P2qH2cTAxaIHxHzK2orS0F0UsxB8gCzqgEDli5hp6SvV8RtyUdb8FxMxw8\/fErWx3nMoznKSnWFk9MVSwwcBvnx09MKuvhXc98DxLmgfQ+OlkgEFqz2C6Ul+gBmZgJkliVkj\/4XZu8oqfssLZONg=="}
                 */
                if ($this->checkMsgSignature($request, $responseData['Encrypt'])) {
                    /**
                     * $a=$request->input();
                     * json_encode($a);
                     * {"signature":"b885e7ff19c2b7ba234cfb8565eed68a344831e2","timestamp":"1505194686","nonce":"172637144","encrypt_type":"aes","msg_signature":"69e5e1484cfaa619e13027795a9ed0cc25f51f95"}
                     *
                     */
                    //对$responseData['Encrypt']解密
                    $responseData['clearText'] = $this->decrypt($responseData['Encrypt']);
                    if ($responseData['clearText']) {
                        //把$responseData['clearText']数据转化为数组
                        $responseData['clearText'] = $this->xmlToArray($responseData['clearText']);
                        try {
                            //BLogger::getLogger('info')->info('receive weixin data:' . json_encode($responseData));
                        } catch (\Exception $e) {
                            Log::error('error' . $e->getMessage());
                            Log::info('receive weixin data:' . json_encode($responseData));
                        }
                        //Log::info('receive weixin data:'.json_encode($responseData));
                        if ($responseData['clearText']['AppId'] != $this->appId) {
                            BLogger::getLogger('error')->error('两处appid不相等');
                            exit;
                        }
                        switch ($responseData['clearText']['InfoType']) {
                            case 'component_verify_ticket':
                                //微信服务器会向其“授权事件接收URL”每隔10分钟定时推送component_verify_ticket
                                if ($this->setComponentVerifyTicket($responseData['clearText']['ComponentVerifyTicket'])) {
                                    echo "success";
                                } else {
                                    BLogger::getLogger('error')->error($responseData['clearText']['ComponentVerifyTicket'] . '保存数据失败');
                                }
                                break;
                            case 'unauthorized':
                                BLogger::getLogger('info')->info('xcx cancel authorize:' . json_encode($responseData));
                                //小程序第三方平台全网发布 测试用例
                                if ($responseData['clearText']['AuthorizerAppid'] == 'wx570bc396a51b8ff8') {
                                    echo "success";
                                    break;
                                }
                                //取消授权
                                $returnValue = $this->unauthorized($responseData['clearText']['AuthorizerAppid']);
                                if ($returnValue > 0) {
                                    echo "success";
                                } else {
                                    BLogger::getLogger('error')->error($responseData['clearText']['AuthorizerAppid'] . '取消授权失败' . $returnValue);
                                }
                                break;
                            case 'authorized':
                                BLogger::getLogger('info')->info('xcx authorize:' . json_encode($responseData));
                                if ($responseData['clearText']['AuthorizerAppid'] == 'wx570bc396a51b8ff8') {
                                    echo "success";
                                    break;
                                }
                                $wxxcxConfigData=(new WXXCXConfigService())->getListByCondition(['app_id'=>$responseData['clearText']['AuthorizerAppid'],'current_status'=>0]);
                                if($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data'])) {
                                    (new StaffOperLogService())->write(json_encode($responseData['clearText']), 22, $wxxcxConfigData['data'][0]['id']);
                                }
                                break;
                        }
                    }
                } else {
                    BLogger::getLogger('error')->error('签名验证失败');
                }
            } else {
                BLogger::getLogger('error')->error('接收数据有问题');
            }
        }
        catch(\Exception $ext)
        {
            BLogger::getLogger('error')->error('compontent_verify_ticket捕获到异常:'.$ext->getMessage());
        }
    }

    /**
     * todo 签名效验
     * @param Request $request
     * @param $encryptMsg
     * @return bool
     * @author jonzhang
     * @date 2017-09-12
     */
    private function checkMsgSignature(Request $request,$encryptMsg)
    {
        $timestamp=$request->input('timestamp');
        $msgSignature=$request->input('msg_signature');
        $nonce=$request->input('nonce');
        if (empty($timestamp) || empty($msgSignature) || empty($nonce))
        {
            Log::error('checkMsgSignature方法中$request参数为空');
            return false;
        }
        // 加工出自己的 signature
        $signatureStr = array($encryptMsg,$this->token,$timestamp,$nonce);
        sort($signatureStr, SORT_STRING);
        $signatureStr = implode($signatureStr);
        $signatureStr = sha1($signatureStr);
        // 用自己的 signature 去跟请求里的 signature 对比
        if ($signatureStr == $msgSignature)
        {
            return true;
        }
        return false;
    }

    /**
     * todo 解密过程
     * @param $encrypted
     * @return bool|string
     * @author jonzhang
     * @date 2017-09-12
     */
    private function decrypt($encrypted)
    {
        try
        {
            $iv = substr($this->encodingAesKey, 0, 16);
            $decrypted = openssl_decrypt($encrypted,'AES-256-CBC',substr($this->encodingAesKey, 0, 32),OPENSSL_ZERO_PADDING,$iv);
        }
        catch (\Exception $e)
        {
            Log::error('解密过程出错'.$e->getMessage());
            return false;
        }

        try
        {
            //去除补位字符
            $result = $this->cryptDecode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
            {
                Log::error('解析字符串出错');
                return false;
            }
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
        }
        catch (\Exception $e)
        {
            Log::error('解密补位出错'.$e->getMessage());
            return false;
        }

        if ($from_appid != $this->appId)
        {
            Log::info('两处appid不同,解密后：'.$from_appid.',原始为：'.$this->appId);
            return false;
        }
        return $xml_content;
    }

    private function cryptDecode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * todo 把微信小程序支付接口返回的xml转化为数组
     * @param $xml
     * @return mixed
     * @author jonzhang
     * @date 2017-08-15
     */
    private function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring), true);

        return $val;
    }

    /**
     * todo 保存componentVerifyTicket到缓存中
     * @param $componentVerifyTicket
     * @author jonzhagn
     * @date 2017-09-08
     */
    private function setComponentVerifyTicket($componentVerifyTicket)
    {
        WXXCXCache::set('component_verify_ticket',$componentVerifyTicket,'3rd_component_verify_ticket');
        $componentTicketService=new WXXCXComponentTicketService();
        $componentTicketData=$componentTicketService->getRowById(1);
        if($componentTicketData['errCode']==0&&!empty($componentTicketData['data']))
        {
            $componentTicketService->updateData(1,['component_verify_ticket'=>$componentVerifyTicket]);
        }
        else if($componentTicketData['errCode']==0&&empty($componentTicketData['data']))
        {
            $data=['component_verify_ticket'=>$componentVerifyTicket];
            $componentTicketService->insertData($data);
        }
        return true;
    }


    /**
     * todo 获取 ComponentVerifyTicket
     * @author jonzhagn
     * @date 2017-09-08
     */
    private function getComponentVerifyTicket()
    {
        $componentVerifyTicket=WXXCXCache::get('component_verify_ticket','3rd_component_verify_ticket');
        //缓存中第三方平台发送的ticket不存在
        if(empty($componentVerifyTicket))
        {
            //从数据库中查询保存的第三方平台发送的ticket
            $componentTicketService=new WXXCXComponentTicketService();
            $componentTicketData=$componentTicketService->getRowById(1);
            if($componentTicketData['errCode']==0&&!empty($componentTicketData['data']))
            {
                $componentVerifyTicket=$componentTicketData['data']['component_verify_ticket'];
            }
            else
            {
                $componentVerifyTicket=false;
            }
        }
        return $componentVerifyTicket;
    }

    /**
     * todo 保存componentAccessToken到缓存中
     * @param $componentAccessToken
     * @author jonzhang
     * @date 2017-09-12
     */
    private function setComponentAccessTokenValue($componentAccessToken)
    {
        WXXCXCache::set('componentAccessToken',$componentAccessToken,'3rd_componentAccessToken',6600);
    }

    /**
     * todo 获取 componentAccessToken
     * @author jonzhang
     * @date 2017-09-08
     */
    private function getComponentAccessTokenValue()
    {
        return WXXCXCache::get('componentAccessToken','3rd_componentAccessToken',false);
    }

    /**
     * 获取componentAccessToken
     * @return array
     * @author: 梅杰 20198年9月20日
     */
    private function getComponentAccessToken()
    {

        if ($componentAccessToken = $this->getComponentAccessTokenValue()) {
            return [ 'code'=> 0,'data' => $componentAccessToken];
        }

        if ($componentVerifyTicket = $this->getComponentVerifyTicket()) {
            $postData['component_appid']            = $this->appId;
            $postData['component_appsecret']        = $this->appSecret;
            $postData['component_verify_ticket']    = $componentVerifyTicket;
            $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
            $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
            $jsonData = json_decode($jsonData,true);
            if (isset($jsonData['component_access_token'])) {
                $this->setComponentAccessTokenValue($jsonData['component_access_token']);
                return ['code' => 0,'data' => $jsonData['component_access_token']];
            }
            $code = $jsonData['errcode']??'api_component_token错误码为空';
            $msg = $jsonData['errmsg']??'api_component_token错误信息为空';
            $message = 'api_component_token code:'.$code.' msg:'.$msg;
            BLogger::getLogger('error')->error('compenent_access_token:failure:'.json_encode($jsonData));
            return ['code' => -2,'message' => $message];
        }

        return ['code'=>-3,'message'=>'ComponentVerifyTicket为空'];
    }

    /***
     * todo 访问authorizer_access_token 过期时，重新授权
     * @param $data
     * @return bool
     * @author jonzhang
     * @date 2017-09-11
     */
    private function refreshAuthorizerToken($data)
    {
        $componentAccessTokenResult=$this->getComponentAccessToken();
        //判断ComponentAccessToken数值是否存在
        if($componentAccessTokenResult['code']!=0)
        {
            return $componentAccessTokenResult;
        }
        try
        {
            //微信第三方平台appid
            $postData['component_appid'] = $this->appId;
            //授权方appid
            $postData['authorizer_appid'] = $data['app_id'];
            $postData['authorizer_refresh_token'] = $data['authorizer_refresh_token'];

            $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s';
            $componentAccessToken=$componentAccessTokenResult['data'];
            $postUrl=sprintf($postUrl,$componentAccessToken);

            $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
            if($jsonData)
            {
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['authorizer_access_token']))
                {
                    $wxxcxConfigService=new WXXCXConfigService();
                    //current_status为0 表示正常小程序配置信息
                    $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['app_id'=>$data['app_id'],'current_status'=>0]);
                    if($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']))
                    {
                        $id=$wxxcxConfigData['data'][0]['id'];
                        $expireTime=time()+7200;
                        $updateData=[
                                'authorizer_access_token'=>$jsonData['authorizer_access_token'],
                                'authorizer_refresh_token'=>$jsonData['authorizer_refresh_token'],
                                'authorizer_expire_time'=>$expireTime
                            ];
                        $updateResult=$wxxcxConfigService->updateData($id,$updateData);
                        if($updateResult['errCode']==0)
                        {
                            return ['code'=>0,'data'=>$jsonData['authorizer_access_token'],'expireTime'=>$expireTime];
                        }
                        else
                        {
                            return ['code'=>-5,'message'=>$updateResult['errCode'].''.$updateResult['errMsg']];
                        }
                    }
                    else
                    {
                        $message='该appId：'.$wxxcxConfigData['errCode'].' 错误信息:'.$wxxcxConfigData['errMsg'];
                        return ['code'=>-4,'message'=>$message];
                    }
                }
                else
                {
                    $code=$jsonData['errcode']??'api_authorizer_token 错误码为空';
                    $msg=$jsonData['errmsg']??'api_authorizer_token 错误信息为空';
                    $message='api_authorizer_token:'.$code.' msg:'.$msg;
                    return ['code'=>-3,'message'=>$message];
                }
            }
            else
            {
                $msg='调用 api_authorizer_token 失败';
                return ['code'=>-2,'message'=>$msg];
            }
        }
        catch(\Exception $ex)
        {
            $msg='调用 api_authorizer_token 出现异常:'.$ex->getMessage();
            return ['code'=>-1,'message'=>$msg];
        }
    }

    /**
     * todo 获取访问authorizer_access_token [供其他地方使用]
     * @param $data
     * @return bool|mixed|string
     * @author jonzhang
     * @date 2017-09-11
     * @update 张永辉 2018年07月23日 小程序配置排序
     */
    public function getAuthorizerAccessToken($data)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>'','expireTime'=> ''];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='数据为空';
            return $returnData;
        }
        $selectData=['current_status'=>0];
        if(!empty($data['id']))
        {
            $selectData['id']=$data['id'];
        }
        if(!empty($data['wid']))
        {
            $selectData['wid']=$data['wid'];
        }
        $wxxcxConfigService=new WXXCXConfigService();
        $xcxConfigData=$wxxcxConfigService->getListByCondition($selectData,'id','asc');
        //有小程序配置信息
        if ($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data']))
        {
            $xcxConfigInfo=$xcxConfigData['data'][0];
            $authorizerData=[
                'app_id'=>$xcxConfigInfo['app_id'],
                'authorizer_refresh_token'=>$xcxConfigInfo['authorizer_refresh_token']
                ];
            if ($xcxConfigInfo['authorizer_expire_time'] > time())
            {
                $returnData['data']=$xcxConfigInfo['authorizer_access_token'];
                $returnData['expireTime'] = $xcxConfigInfo['authorizer_expire_time'];
                return $returnData;
            }
            else
            {
                //authorizer_access_token 过期 重新获取
                $result=$this->refreshAuthorizerToken($authorizerData);
                if($result['code']==0)
                {
                    $returnData['data']=$result['data'];
                    $returnData['expireTime'] = $result['expireTime'];
                    return $returnData;
                }
                else
                {
                    $returnData['errCode']=$result['code'];
                    $returnData['errMsg']=$result['message'];
                    return $returnData;
                }
            }
        }//没有小程序配置信息
        else if ($xcxConfigData['errCode']==0&&empty($xcxConfigData['data']))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='该店铺还没有绑定小程序';
            return $returnData;
        }
        else
        {
            return $xcxConfigData;
        }
    }



    /**
     * todo 二维码生成的URL
     * @return array
     * @author jonzhang
     * @date 2017-09-11
     */
    public function  startAuthorizer($wid,$type='')
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>''];
        //生成二维码所需要的pre_auth_code
        $result=$this->getPreAuthCode();
        if($result['errCode']!=0)
        {
            return $result;
        }
        $preAuthCode=$result['data'];
        //小程序二维码授权 回调url
        if ($type =='updateauthorized')
        {
            //更新授权跳转链接加上type参数
            $redirectUri=config('app.url').'xcx/third/sendCallBack?wid='.$wid.'-updateauthorized';
        }else{
            $redirectUri=config('app.url').'xcx/third/sendCallBack?wid='.$wid;
        }
        $requestUrl='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';
        $requestUrl=sprintf($requestUrl,$this->appId,$preAuthCode,$redirectUri);
        $returnData['data']=$requestUrl;
        return $returnData;
    }

    /**
     * todo 生成二维码所需要的pre_auth_code
     * @return bool
     * @author jonzhang
     * @date 2017-09-11
     */
    private function getPreAuthCode()
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>''];
        $componentAccessTokenResult=$this->getComponentAccessToken();
        if($componentAccessTokenResult['code']!=0)
        {
            $returnData['errCode']=$componentAccessTokenResult['code'];
            $returnData['errMsg']=$componentAccessTokenResult['message'];
            return $returnData;
        }
        $componentAccessToken = $componentAccessTokenResult['data'];
        if ($componentAccessToken)
        {
            $postData['component_appid'] = $this->appId;
            $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s';
            $postUrl=sprintf($postUrl,$componentAccessToken);
            try
            {
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                if($jsonData)
                {
                    $jsonData=json_decode($jsonData,true);
                    if (isset($jsonData['pre_auth_code']))
                    {
                        $returnData['data']=$jsonData['pre_auth_code'];
                        return $returnData;
                    }
                    else
                    {
                        $code = $jsonData['errcode']??'-1000';
                        $msg = $jsonData['errmsg']??'api_create_preauthcode错误信息为空';
                        $returnData['errCode']=$code;
                        $returnData['errMsg']=$msg;
                        return $returnData;
                    }
                }
                else
                {
                    $returnData['errCode']=-100;
                    $returnData['errMsg']='调用 api_create_preauthcode不通';
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $msg='调用 api_create_preauthcode 出现异常:'.$ex->getMessage();
                $returnData['errCode']=-101;
                $returnData['errMsg']=$msg;
                return $returnData;
            }
        }
        $returnData['errCode']=-102;
        $returnData['errMsg']='ComponentAccessToken为空';
        return $returnData;
    }

    /**
     * todo 通过二维码 扫描授权回调
     * @param $data
     * @return bool
     * @author jonzhang
     * @date 2017-09-11
     *
     * @update by wuxiaoping 添加type参数
     * 当type不为空，且type=updateauthorized时，表示更新授权
     * @update 梅杰 20180705 店铺可以授权多个小程序
     * @update 梅杰 20180712 非默认小程序插入默认小程序的支付配置信息
     * @update 梅杰 20180726 如果是解绑后再次绑定不再重新生成记录，修改状态值
     * @update 梅杰 20180929 获取配置时筛选条件bug
     * @update 何书哲 2018年11月21日 新授权小程序设置域名
     */
    private function authorized($wid,$authCode,$type='')
    {
        $wxxcxConfigService=new WXXCXConfigService();
        //add by jonzhang 2018-05-09 判断店铺是否已经绑定小程序
//        if(empty($type))
//        {
//            $xcxConfigWidData = $wxxcxConfigService->getListByCondition(['wid' => $wid, 'current_status' => 0]);
//            if ($xcxConfigWidData['errCode'] == 0 && !empty($xcxConfigWidData['data'])) {
//                error('该店铺已经绑定了小程序');
//            }
//        }
        // 使用授权码换取小程序的接口调用凭据和授权信息
        if (!empty($authCode))
        {
            $componentAccessTokenResult = $this->getComponentAccessToken();
            if($componentAccessTokenResult['code']!=0)
            {
                BLogger::getLogger('error')->error('ComponentAccessToken有问题'.$componentAccessTokenResult['code'].$componentAccessTokenResult['message']);
                return false;
            }
            $componentAccessToken = $componentAccessTokenResult['data'];
            if ($componentAccessToken)
            {
                $postData['component_appid'] = $this->appId;
                $postData['authorization_code'] = $authCode;

                $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s';
                $postUrl=sprintf($postUrl,$componentAccessToken);

                try
                {
                    $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                    if ($jsonData)
                    {
                        $jsonData=json_decode($jsonData,true);
                        if(!empty($jsonData['authorization_info']))
                        {
                            $authorizationInfo=$jsonData['authorization_info'];
                            //current_status为0表示正常配置
                            $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['app_id'=>$authorizationInfo['authorizer_appid'],'current_status'=>0]);
                            if(empty($type)&&$wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']))
                            {
                                //add by jonzhang 2018-03-23 重新授权时，token信息进行更改
                                $expireTime=time()+7200;
                                $updateData=[
                                    'authorizer_access_token'=>$authorizationInfo['authorizer_access_token'],
                                    'authorizer_refresh_token'=>$authorizationInfo['authorizer_refresh_token'],
                                    'authorizer_expire_time'=>$expireTime
                                ];
                                $id = $wxxcxConfigData['data'][0]['id'];
                                //更新token信息
                                $wxxcxConfigService->updateData($id,$updateData);
                                error('该小程序已经绑定了店铺');
                            }
                            else if(($wxxcxConfigData['errCode']==0&&empty($wxxcxConfigData['data'])) || ($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']) && $type=='updateauthorized'))
                            {
                                $func=null;
                                $funcInfo=$authorizationInfo['func_info'];
                                if(is_array($funcInfo))
                                {
                                    if(!empty($funcInfo))
                                    {
                                        $func=json_encode($funcInfo);
                                    }
                                }
                                else
                                {
                                    if(!empty($funcInfo))
                                    {
                                        $func=$funcInfo;
                                    }
                                }
                                $expireTime=time()+7200;
                                $insertData=[
                                    'authorizer_access_token'=>$authorizationInfo['authorizer_access_token'],
                                    'authorizer_refresh_token'=>$authorizationInfo['authorizer_refresh_token'],
                                    'authorizer_expire_time'=>$expireTime,
                                    'func_info'=>$func,
                                    'app_id'=>$authorizationInfo['authorizer_appid'],
                                    'wid'=>$wid
                                ];
                                //获取默认小程序配置信息，将微信商户信息存入进去
                                $defaultXcxData = $wxxcxConfigService->getRow($wid);
                                if (!$defaultXcxData['errCode'] && $defaultXcxData['data']) {
                                    $insertData['app_pay_secret'] = $defaultXcxData['data']['app_pay_secret'];
                                    $insertData['merchant_no']    = $defaultXcxData['data']['merchant_no'];
                                }
                                //Herry 授权成功获取小程序名称头像等信息
                                $info = $this->_getAuthorizerInfo($componentAccessToken, $this->appId, $authorizationInfo['authorizer_appid']);
                                $insertData['title'] = $info['title'];
                                $insertData['verify_type'] = $info['verify_type'];
                                $insertData['signature'] = $info['signature'];
                                $insertData['business_info'] = $info['business_info'];
                                $insertData['service_type'] = $info['service_type'];
                                $insertData['user_name'] = $info['user_name'];
                                $insertData['principal_name'] = $info['principal_name'];
                                $insertData['head_img'] = $info['head_img'];
                                $insertData['qrcode_url'] = $info['qrcode_url'];

                                if ($type =='updateauthorized'&&($wxxcxConfigData['errCode']==0 &&!empty($wxxcxConfigData['data'])))
                                {
                                    //add by jonzhang 2018-01-27
                                    if($wid!=$wxxcxConfigData['data'][0]['wid'])
                                    {
                                        error('不是同一个店铺，不能够更新授权');
                                    }
                                    //更新授权
                                    $id = $wxxcxConfigData['data'][0]['id'];
                                    $insertResult = $wxxcxConfigService->updateData($id,$insertData);
                                    if($insertResult['errCode']==0)
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        BLogger::getLogger('info')->info('更新小程序配置数据失败,原始数据为：'.json_encode($insertData).'错误码:'.$insertResult['errCode'].'错误信息:'.$insertResult['errMsg']);
                                        return false;
                                    }
                                }
                                else if($type == 'updateauthorized'&&($wxxcxConfigData['errCode']==0 &&empty($wxxcxConfigData['data'])))
                                {
                                    error('只支持更新当前小程序授权类目，或者头像等信息更新。不支持绑定其他小程序');
                                }
                                else
                                {
                                    $wxxcxConfigData = $wxxcxConfigService->getListByCondition(['app_id'=>$authorizationInfo['authorizer_appid']]);
                                    if ($wxxcxConfigData['errCode'] == 0 && $wxxcxConfigData['data']) {
                                        //存在则更新,不存在则插入
                                        $id = $wxxcxConfigData['data'][0]['id'];
                                        $insertData['current_status'] = 0;
                                        //更新token信息
                                        $insertResult = $wxxcxConfigService->updateData($id,$insertData);
                                    }else {
                                        //授权
                                        $insertResult =$wxxcxConfigService->insertData($insertData);
                                        //何书哲 2018年11月21日 新授权小程序设置域名
                                        $insertResult['errCode'] == 0 && $insertResult['data'] &&
                                        (new XCXModule())->modifyDomain($insertResult['data'], 'set', config('app.request_domain'), 999999, 'system');

                                    }

                                    if($insertResult['errCode']==0)
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        BLogger::getLogger('info')->info('添加小程序配置数据失败,原始数据为：'.json_encode($insertData).'错误码:'.$insertResult['errCode'].'错误信息:'.$insertResult['errMsg']);
                                        return false;
                                    }
                                }
                            }
                            else
                            {
                                $code=$wxxcxConfigData['errCode']??'小程序配置信息没有错误码';
                                $msg=$wxxcxConfigData['errMsg']??'小程序配置信息没有错误信息';
                                BLogger::getLogger('info')->info('配置信息问题'.$code.$msg);
                                return false;
                            }
                        }
                        else
                        {
                            $code=$jsonData['errcode']??'api_query_auth 错误码为空';
                            $msg=$jsonData['errmsg']??'api_query_auth 错误信息为空';
                            BLogger::getLogger('error')->error('api_query_auth:'.$code.' msg:'.$msg);
                            return false;
                        }
                    }
                    else
                    {
                        BLogger::getLogger('info')->info('调用 api_query_auth 失败');
                        return false;
                    }
                }
                catch(\Exception $ex)
                {
                    BLogger::getLogger('error')->error('调用 api_query_auth 出现异常:'.$ex->getMessage());
                    return false;
                }
            }
            else
            {
                BLogger::getLogger('info')->info('getComponentAccessToken失败');
                return false;
            }
        }
        else
        {
            BLogger::getLogger('info')->info('授权数据有问题');
            return false;
        }
    }


    /**
     * @param $wid
     * @param $authCode
     * @param string $type
     * @return bool
     * @author: 梅杰 2018年9月29号
     */
    private function authorizedV2($wid,$authCode,$type='')
    {
        $wxxcxConfigService = new WXXCXConfigService();

        if (empty($authCode)) {
            BLogger::getLogger('info')->info('授权数据有问题');
            return false;
        }
        $componentAccessTokenResult = $this->getComponentAccessToken();
        if ($componentAccessTokenResult['code'] != 0) {
            BLogger::getLogger('error')->error('ComponentAccessToken有问题'.$componentAccessTokenResult['code'].$componentAccessTokenResult['message']);
            return false;
        }

        $componentAccessToken = $componentAccessTokenResult['data'];
        $postData['component_appid'] = $this->appId;
        $postData['authorization_code'] = $authCode;
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s';
        $postUrl = sprintf($postUrl,$componentAccessToken);

        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();

        $authData = json_decode($jsonData,true);

        if (!isset($authData['authorization_info'])) {
            $code = $authData['errcode']??'api_query_auth 错误码为空';
            $msg  = $authData['errmsg']??'api_query_auth 错误信息为空';
            BLogger::getLogger('error')->error('api_query_auth:'.$code.' msg:'.$msg);
            return false;
        }

        $authorizationInfo = $authData['authorization_info'];

        //current_status为0表示正常配置
        $ConfigData = $wxxcxConfigService->getListByCondition(['app_id' => $authorizationInfo['authorizer_appid']]);

        if ($ConfigData['errCode'] ) {

            BLogger::getLogger('error')->error('获取app_id为：'.$authorizationInfo['authorizer_appid'].'error');
            return false;
        }
        $wxxcxConfigData['data'] = $ConfigData['data'][0];

        //更新授权不能跨店铺更新
        if ($wxxcxConfigData['data'] && $wxxcxConfigData['data']['current_status'] == 0) {
            $wxxcxConfigData['data']['wid'] != $wid && error('该小程序已经授权绑定了其他店铺');
            $wxxcxConfigData['data']['app_id'] != $authorizationInfo['authorizer_appid'] && error('只支持当前小程序更新');
        }

        $funcInfo = $authorizationInfo['func_info'];

        is_array($funcInfo) && $funcInfo = json_encode($funcInfo);

        $dbData = [
            'authorizer_access_token' => $authorizationInfo['authorizer_access_token'],
            'authorizer_refresh_token' => $authorizationInfo['authorizer_refresh_token'],
            'authorizer_expire_time' => time() + 7200,
            'func_info'  => $funcInfo,
            'app_id'  => $authorizationInfo['authorizer_appid'],
            'wid'     => $wid
        ];

        //Herry 授权成功获取小程序名称头像等信息
        $info = $this->_getAuthorizerInfo($componentAccessToken, $this->appId, $authorizationInfo['authorizer_appid']);
        $dbData['title']        = $info['title'];
        $dbData['verify_type']  = $info['verify_type'];
        $dbData['signature']    = $info['signature'];
        $dbData['business_info']    = $info['business_info'];
        $dbData['service_type']     = $info['service_type'];
        $dbData['user_name']        = $info['user_name'];
        $dbData['principal_name']   = $info['principal_name'];
        $dbData['head_img']         = $info['head_img'];
        $dbData['qrcode_url']       = $info['qrcode_url'];
        $dbData['current_status']  = 0;


        if ($wxxcxConfigData['data'] ) {
            //存在更新
            $re = $wxxcxConfigService->updateData($wxxcxConfigData['data']['id'],$dbData);
        }else {
            $defaultXcxData = $wxxcxConfigService->getRow($wid);
            if (!$defaultXcxData['errCode'] && $defaultXcxData['data']) {
                $dbData['app_pay_secret'] = $defaultXcxData['data']['app_pay_secret'];
                $dbData['merchant_no'] = $defaultXcxData['data']['merchant_no'];

            }
            $re = $wxxcxConfigService->insertData($dbData);
        }


        if ($re['errCode']  ) {
            BLogger::getLogger('info')->info('添加小程序配置数据失败,原始数据为：'.json_encode($dbData).'错误码:'.$re['errCode'].'错误信息:'.$re['errMsg']);
            return false;
        }

        return true;
    }

    /**
     * todo 二维码授权回调
     * @author jonzhang
     * @date 2017-09-11
     */
    public function sendCallBack(Request $request)
    {
        BLogger::getLogger('info')->info('授权二维码回调,请求信息：'.json_encode($request->input()));
        $wid=$request->input('wid');
        $authCode=$request->input('auth_code');
        $type = '';
        if(strpos($wid,'updateauthorized')){
            $arrParam = explode('-',$wid);
            $wid      = $arrParam[0];
            $type     = $arrParam[1];
        }

        if(!empty($wid)&&!empty($authCode))
        {
            //回调数据转化为数组
            if ($this->authorized($wid, $authCode,$type))
            {
                return $type ? $type : true;
            }
        }
        return false;
    }

    /**
     * 授权成功获取小程序名称头像等信息
     */
    private function _getAuthorizerInfo($componentAccessToken, $component_appid, $authorizer_appid)
    {
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $componentAccessToken;
        $postData = [
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid
        ];
        //调用微信接口
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //Log::info('=========get authorizer info: ' . $result);
        $title = '';
        $verifyType = '';
        $signature = '';
        $business_info = '';
        $service_type = '';
        $user_name = '';
        $principal_name = '';
        $head_img = '';
        $qrcode_url = '';
        if (!empty($result)) {
            $result = json_decode($result, true);
            if (!empty($result['authorizer_info'])) {
                $title = $result['authorizer_info']['nick_name'] ?? '';
                $verifyType = $result['authorizer_info']['verify_type_info']['id'] ?? 0;
                $signature = $result['authorizer_info']['signature'] ?? '';
                $business_info = json_encode($result['authorizer_info']['business_info']);
                $service_type = $result['authorizer_info']['service_type_info']['id'] ?? 0;
                $user_name = $result['authorizer_info']['user_name'] ?? '';
                $principal_name = $result['authorizer_info']['principal_name'] ?? '';
                $head_img = $result['authorizer_info']['head_img'] ?? '';
                $qrcode_url = $result['authorizer_info']['qrcode_url'] ?? '';
            }
        }

        return [
            'title' => $title,
            'verify_type' => $verifyType,
            'signature' => $signature,
            'business_info' => $business_info,
            'service_type' => $service_type,
            'user_name' => $user_name,
            'principal_name' => $principal_name,
            'head_img' => $head_img,
            'qrcode_url' => $qrcode_url
        ];
    }

    /** todo 生成小程序码的URL
     * @param $wid
     * @param $pageType int 跳转页面类型 0:主页,1:优惠券
     * @param $id int 如果是详情 详情页的ID
     * @return array
     * @author jonzhang
     * @date 2017-09-21
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function getXCXCode($xcxid,$width=430,$page="pages/index/index")
    {
        $result=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $staffOperService = new StaffOperLogService();
        if(empty($xcxid))
        {
            $result['errCode']=-1;
            $result['errMsg']='wid为null';
            return $result;
        }
        $isCache=false;
        $xcxCode=false;
        //只有店铺主页默认宽度才进行缓存
        if($width==430&&$page=="pages/index/index")
        {
            $isCache=true;
            //获取缓存中保存的小程序码
            $xcxCode=$this->getXCXCodeValue($xcxid);
        }
        if(!$xcxCode)
        {
            $accessTokenData = $this->getAuthorizerAccessToken(['id' => $xcxid]);
            if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data'])) {
                $accessToken = $accessTokenData['data'];
                //获取小程序码的URL
                $postUrl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s";
                $postUrl = sprintf($postUrl, $accessToken);
                $postData = ['scene' => 'xxx', 'page' => $page,'width'=>$width];
                try {
                    $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                    $returnValue = json_decode($jsonData, true);
                    if (empty($returnValue)) {
                        //二进制数据流转化为base64
                        $result['data'] = base64_encode($jsonData);
                        //把店铺主页的小程序码保存下来
                        if($isCache)
                        {
                            $this->setXCXCodeValue($xcxid, $result['data']);
                        }
                        $staffOperService->write('操作成功', 14, $xcxid);
                        return $result;
                    } else {
                        $code = $returnValue['errcode']??'小程序码错误码';
                        $msg = $returnValue['errmsg']??'小程序码错误信息';
                        $staffOperService->write('操作失败: '.json_encode($returnValue), 14, $xcxid);
                        $result['errCode'] = -102;
                        $result['errMsg'] = '错误码:' . $code . '错误信息:' . $msg;
                        return $result;
                    }
                } catch (\Exception $ex) {
                    $result['errCode'] = -101;
                    $result['errMsg'] = $ex->getMessage();
                    return $result;
                }
            }
            else
            {
                if($accessTokenData['errCode']==-2)
                {
                    $accessTokenData['errMsg']='店铺没有绑定小程序或者小程序已经下架';
                }
                $staffOperService->write('操作失败: '.json_encode($accessTokenData), 14, $xcxid);
                return $accessTokenData;
            }
        }
        $result['data']=$xcxCode;
        $staffOperService->write('操作成功', 14, $xcxid);
        return  $result;
    }

    /**
     * todo 保存64位小程序码
     * @param $xcxCode
     * @author jonzhang
     * @date 2017-09-28
     */
    private function setXCXCodeValue($wid,$xcxCode ,$pre = 'xcx_code_new')
    {
        WXXCXCache::set($wid,$xcxCode,$pre,7200);
    }

    /**
     * todo 获取保存的64位小程序码
     * @return mixed
     * @author jonzhang
     * @date 2017-09-28
     */
    private function getXCXCodeValue($wid,$pre = 'xcx_code_new')
    {
        return WXXCXCache::get($wid,$pre,false);
    }

    /***
     * todo 取消授权
     * @param $appId
     * @return array|int
     * @author jonzhang
     * @date 2017-09-22
     */
    private function unauthorized($appId)
    {
        if(empty($appId))
        {
            return -1;
        }
        $wxxcxConfigService=new WXXCXConfigService();
        //current_status为0表示正常配置
        $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['app_id'=>$appId,'current_status'=>0]);
        if($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']))
        {
            $id=$wxxcxConfigData['data'][0]['id'];
            $returnValue=$wxxcxConfigService->updateData($id,['current_status'=>-1]);
            if($returnValue['errCode']==0)
            {
                return 1;
            }
            else
            {
                return -4;
            }
        }
        else if($wxxcxConfigData['errCode']==0&&empty($wxxcxConfigData['data']))
        {
            return -2;
        }
        else
        {
            return -3;
        }

    }

    /**
     * todo 小程序审核成功
     * @param $responseData
     * @return bool
     * @author jonzhang
     * @date 2017-09-28
     * @upadte 陈文豪 2018年07月10日 修改为ID提交
     */
    private function auditSuccess($appId,$responseData)
    {
        if(empty($responseData)||empty($appId))
        {
            return false;
        }
        $where=['app_id'=>$appId,'current_status'=>0];
        $xcxConfigService=new WXXCXConfigService();
        $staffOperService=new StaffOperLogService();
        $xcxConfigData=$xcxConfigService->getListByCondition($where);
        if($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data']))
        {
            $id=$xcxConfigData['data'][0]['id'];
            //add by jonzhang 2018-03-01
            $wid=$xcxConfigData['data'][0]['wid']??0;
            $isON=$xcxConfigData['data'][0]['is_auth_submit']??0;
            $staffOperService->write(json_encode($responseData), 20, $id);
            //status为4表示审核成功
            $updateData=[
                'audit_result_time'=>$responseData['SuccTime'],
                'news_create_time'=>$responseData['CreateTime'],
                'from_user_name'=>$responseData['FromUserName'],
                'status'=>4,
                'status_time'=>time()
            ];
            $result=$xcxConfigService->updateData($id,$updateData);
            if($result['errCode']==0)
            {
                if($isON)
                {
                    dispatch((new XCXRelease($id))->onQueue('xcxRelease')->delay(60));
                }
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * todo 审核失败
     * @param $responseData
     * @return bool
     * @author jonzhang
     * @date 2017-09-28
     */
    private function auditFail($appId,$responseData)
    {
        if(empty($responseData)||empty($appId))
        {
            return false;
        }
        $where=['app_id'=>$appId,'current_status'=>0];
        $xcxConfigService=new WXXCXConfigService();
        $staffOperService=new StaffOperLogService();
        $xcxConfigData=$xcxConfigService->getListByCondition($where);
        if($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data']))
        {
            $id=$xcxConfigData['data'][0]['id'];
            //status为3表示审核失败
            //$responseData['Reason'] htmlspecialchars()对审核失败数据进行处理否则会出现问题
            $staffOperService->write(json_encode($responseData), 21, $id);
            $updateData=[
                'audit_result_time'=>$responseData['FailTime'],
                'news_create_time'=>$responseData['CreateTime'],
                'from_user_name'=>$responseData['FromUserName'],
                'status'=>3,
                'status_time'=>time(),
                'reason'=>htmlspecialchars($responseData['Reason'])
            ];
            $result=$xcxConfigService->updateData($id,$updateData);
            if($result['errCode']==0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * todo 小程序上传代码审核结果回调
     * @param Request $request
     * @author jonzhang
     * @date 2017-09-28
     */
    public function receiveAudit(Request $request,$appId)
    {
        $responseData= isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        if (empty($responseData))
        {
            //接收回调数据
            $responseData= file_get_contents('php://input');
        }
        try
        {
            BLogger::getLogger('info')->info('xcx audit code appId:'.$appId);
        }
        catch(\Exception $e)
        {
            Log::error('error'.$e->getMessage());
            Log::info('xcx audit code appId:'.$appId);
        }
        try
        {
            //Log::info('this is begin');
            if ($responseData) {
                //回调数据转化为数组
                $responseData = $this->xmlToArray($responseData);
                /**
                 * json_encode($responseData);
                 * 接收的数据为：
                 * {"AppId":"wxd881b53d164067db","Encrypt":"d9CkhjqN++cHq+sciGKoIL30kUU3r4qzNZhAoK84GZqTppFrEXb5NqGL0e0uaU2otzPxSgyoHDa99GOimMyrpqoCX6XoMgS1kSNPySXyjZTrBe4+EXFKTglmaKkvO4GzsXBoNJ+CCUkc1+zvzcWFeahexcmUpV\/cPmqAg8RQwLOmeFNFgJhd7e\/U8kb2VXjF1woAJ\/uvQhcsIAxrcZzPFTAnNhYptA9WES61VH1hcQQqUrJum8G4DPRxQOarvOkX0h8I0tdls5Ir8pZLA7uRRdHpR5YDR\/3oV772vInFThz4+l\/ZBVJH8VjgANaTvEFC93P2qH2cTAxaIHxHzK2orS0F0UsxB8gCzqgEDli5hp6SvV8RtyUdb8FxMxw8\/fErWx3nMoznKSnWFk9MVSwwcBvnx09MKuvhXc98DxLmgfQ+OlkgEFqz2C6Ul+gBmZgJkliVkj\/4XZu8oqfssLZONg=="}
                 */
                if ($this->checkMsgSignature($request, $responseData['Encrypt'])) {
                    /**
                     * $a=$request->input();
                     * json_encode($a);
                     * {"signature":"b885e7ff19c2b7ba234cfb8565eed68a344831e2","timestamp":"1505194686","nonce":"172637144","encrypt_type":"aes","msg_signature":"69e5e1484cfaa619e13027795a9ed0cc25f51f95"}
                     *
                     */
                    //对$responseData['Encrypt']解密
                    $responseData['clearText'] = $this->decrypt($responseData['Encrypt']);
                    if ($responseData['clearText'])
                    {
                        //把$responseData['clearText']数据转化为数组
                        $responseData['clearText'] = $this->xmlToArray($responseData['clearText']);

                        BLogger::getLogger('info')->info('小程序审核结果数据为:' . json_encode($responseData));

                        if (isset($responseData['clearText']['MsgId'])) {
                            //客服消息转发给客服系统
                            $this->sendToJavaKf($responseData['clearText']);

                        }else {
                            switch ($responseData['clearText']['Event'])
                            {
                                //审核成功
                                case 'weapp_audit_success':
                                    BLogger::getLogger('info')->info('审核成功数据:' . json_encode($responseData['clearText']));
                                    if ($this->auditSuccess($appId, $responseData['clearText']))
                                    {
                                        echo "success";
                                    }
                                    else {
                                        BLogger::getLogger('error')->error('审核成功，更改数据失败，原始数据为：' . json_encode($responseData['clearText']));
                                    }
                                    break;
                                //审核失败
                                case 'weapp_audit_fail':
                                    BLogger::getLogger('info')->info('审核失败数据:' . json_encode($responseData['clearText']));
                                    if ($this->auditFail($appId, $responseData['clearText']))
                                    {
                                        echo "success";
                                    } else {
                                        BLogger::getLogger('error')->error('审核失败，更改数据失败，原始数据为：' . json_encode($responseData['clearText']));
                                    }
                                    break;
                                case 'user_enter_tempsession':
                                    //进入会话事件
                                    $this->sendToJavaKfSessionEvent($responseData['clearText']['FromUserName']);
                                    break;
                            }
                        }
                    }
                } else {
                    BLogger::getLogger('error')->error('签名验证失败');
                }
            } else {
                BLogger::getLogger('error')->error('接收数据有问题');
            }
        }
        catch(\Exception $et)
        {
            BLogger::getLogger('error')->error('小程序审核,捕获到异常:'.$et->getMessage());
        }
    }

    /**
     * todo 小程序访问页面
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-26
     */
    public function accessPages($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>''];
        $authorizerData=$this->getAuthorizerAccessToken(['wid'=>$data['wid']]);
        if($authorizerData['errCode']==0&&!empty($authorizerData['data']))
        {
            $authorizerAccessToken=$authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=%s';
            $postUrl=sprintf($postUrl,$authorizerAccessToken);
            try
            {
                $postData=[ "begin_date"=>$data['beginDate']??'20171024',
                        "end_date"=>$data['endDate']??"20171024"];
                //此处为post请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['ref_date'])&&isset($jsonData['list']))
                {
                    $returnData['data']=$jsonData['list'];
                    return $returnData;
                }
                else
                {
                    $code=$jsonData['errcode']??'错误码为空';
                    $message=$jsonData['errmsg']??'错误信息为空';
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='错误码:'.$code.'错误信息:'.$message;
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $returnData['errCode']=-10;
                $returnData['errMsg']=$ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            return $authorizerData;
        }
    }

    /**
     * todo 日趋势
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public  function visitTrendForDaily($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>''];
        $str='';
        if(empty($data['wid']))
        {
            $str.='店铺id为空';
        }
        if(empty($data['beginDate']))
        {
            $str.='开始日期为空';
        }
        if(empty($data['endDate']))
        {
            $str.='结束日期为空';
        }
        if(strlen($str)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$str;
            return $returnData;
        }
        $authorizerData=$this->getAuthorizerAccessToken(['wid'=>$data['wid']]);
        if($authorizerData['errCode']==0&&!empty($authorizerData['data']))
        {
            $authorizerAccessToken=$authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend?access_token=%s';
            $postUrl=sprintf($postUrl,$authorizerAccessToken);
            try
            {
                $postData=[
                    "begin_date"=>$data['beginDate'],
                    "end_date"=>$data['endDate']
                ];
                //此处为post请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['list']))
                {
                    $returnData['data']=$jsonData['list'];
                    return $returnData;
                }
                else
                {
                    $code=$jsonData['errcode']??'日趋势错误码为空';
                    $message=$jsonData['errmsg']??'日趋势错误信息为空';
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='错误码:'.$code.'错误信息:'.$message;
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $returnData['errCode']=-10;
                $returnData['errMsg']=$ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            return $authorizerData;
        }
    }

    /**
     * todo 周趋势
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public  function visitTrendForWeekly($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>''];
        $str='';
        if(empty($data['wid']))
        {
            $str.='店铺id为空';
        }
        if(empty($data['beginDate']))
        {
            $str.='开始日期为空';
        }
        if(empty($data['endDate']))
        {
            $str.='结束日期为空';
        }
        if(strlen($str)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$str;
            return $returnData;
        }
        $authorizerData=$this->getAuthorizerAccessToken(['wid'=>$data['wid']]);
        if($authorizerData['errCode']==0&&!empty($authorizerData['data']))
        {
            $authorizerAccessToken=$authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend?access_token=%s';
            $postUrl=sprintf($postUrl,$authorizerAccessToken);
            try
            {
                $postData=[
                    "begin_date"=>$data['beginDate'],
                    "end_date"=>$data['endDate']
                ];
                //此处为post请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['list']))
                {
                    $returnData['data']=$jsonData['list'];
                    return $returnData;
                }
                else
                {
                    $code=$jsonData['errcode']??'周趋势错误码为空';
                    $message=$jsonData['errmsg']??'周趋势错误信息为空';
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='错误码:'.$code.'错误信息:'.$message;
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $returnData['errCode']=-10;
                $returnData['errMsg']=$ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            return $authorizerData;
        }
    }

    /***
     * todo 月趋势
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public  function visitTrendForMonthly($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>''];
        $str='';
        if(empty($data['wid']))
        {
            $str.='店铺id为空';
        }
        if(empty($data['beginDate']))
        {
            $str.='开始日期为空';
        }
        if(empty($data['endDate']))
        {
            $str.='结束日期为空';
        }
        if(strlen($str)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$str;
            return $returnData;
        }
        $authorizerData=$this->getAuthorizerAccessToken(['wid'=>$data['wid']]);
        if($authorizerData['errCode']==0&&!empty($authorizerData['data']))
        {
            $authorizerAccessToken=$authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend?access_token=%s';
            $postUrl=sprintf($postUrl,$authorizerAccessToken);
            try
            {
                $postData=[
                    "begin_date"=>$data['beginDate'],
                    "end_date"=>$data['endDate']
                ];
                //此处为post请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['list']))
                {
                    $returnData['data']=$jsonData['list'];
                    return $returnData;
                }
                else
                {
                    $code=$jsonData['errcode']??'月趋势错误码为空';
                    $message=$jsonData['errmsg']??'月趋势错误信息为空';
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='错误码:'.$code.'错误信息:'.$message;
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $returnData['errCode']=-10;
                $returnData['errMsg']=$ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            return $authorizerData;
        }
    }

    /**
     * todo 访问分布
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-31
     */
    public  function visitDistribution($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>''];
        $str='';
        if(empty($data['wid']))
        {
            $str.='店铺id为空';
        }
        if(empty($data['beginDate']))
        {
            $str.='开始日期为空';
        }
        if(empty($data['endDate']))
        {
            $str.='结束日期为空';
        }
        if(strlen($str)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$str;
            return $returnData;
        }
        $authorizerData=$this->getAuthorizerAccessToken(['wid'=>$data['wid']]);
        if($authorizerData['errCode']==0&&!empty($authorizerData['data']))
        {
            $authorizerAccessToken=$authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitdistribution?access_token=%s';
            $postUrl=sprintf($postUrl,$authorizerAccessToken);
            try
            {
                $postData=[
                    "begin_date"=>$data['beginDate'],
                    "end_date"=>$data['endDate']
                ];
                //此处为post请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $jsonData=json_decode($jsonData,true);
                if(isset($jsonData['list']))
                {
                    $returnData['data']= $jsonData['list'];
                    return $returnData;
                }
                else
                {
                    $code=$jsonData['errcode']??'月趋势错误码为空';
                    $message=$jsonData['errmsg']??'月趋势错误信息为空';
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='错误码:'.$code.'错误信息:'.$message;
                    return $returnData;
                }
            }
            catch(\Exception $ex)
            {
                $returnData['errCode']=-10;
                $returnData['errMsg']=$ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            return $authorizerData;
        }
    }


    /**
     * 生成没有限制的二维码
     * Author: MeiJay
     * @param $wid
     * @param $scene
     * @param $page
     * @return array|bool|mixed|string
	 * @update 梅杰 20180710 生成指定小程序二维码
     * @update 梅杰 20180925 去除前缀
     */
    public function createQrCode($wid,$scene,$page ,$xcxConfigId = '')
    {
        $result=['errCode'=>0,'errMsg'=>'','data'=>[]];
        //获取缓存中保存的小程序码
        $xcxCode = '';
        if(!$xcxCode) {
            $accessTokenData = $this->getAuthorizerAccessToken(['wid' => $wid,'id'=> $xcxConfigId]);
            if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data'])) {
                $accessToken = $accessTokenData['data'];
                //获取小程序码的URL
                $postUrl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s";
                $postUrl = sprintf($postUrl, $accessToken);
                $postData = ['scene' => $scene, 'page' => $page];
                try {
                    $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                    $returnValue = json_decode($jsonData, true);
                    if (empty($returnValue)) {
                        //二进制数据流转化为base64
                        $result['data'] = base64_encode($jsonData);
                        //把店铺主页的小程序码保存下来
//                        $this->setXCXCodeValue($wid,$result['data'],$pre);
                        return $result;
                    } else {
                        $code = $returnValue['errcode']??'小程序码错误码';
                        $msg = $returnValue['errmsg']??'小程序码错误信息';
                        $result['errCode'] = -102;
                        $result['errMsg'] = '错误码:' . $code . '错误信息:' . $msg;
                        return $result;
                    }
                } catch (\Exception $ex) {
                    $result['errCode'] = -101;
                    $result['errMsg'] = $ex->getMessage();
                    return $result;
                }
            } else {
                return $accessTokenData;
            }
        }
        $result['data']=$xcxCode;
        return  $result;
    }

    /***
     * todo 获取草稿箱内的所有临时代码草稿
     * @return array
     * @author jonzhang
     * @date 2018-01-18
     */
    public function getTemplateDraftList()
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>0];
        $accessTokenData=$this->getComponentAccessToken();
        if($accessTokenData['code']!=0)
        {
            $returnData['errCode']=$accessTokenData['code'];
            $returnData['errMsg']=$accessTokenData['message'];
            return $returnData;
        }
        if($accessTokenData['data'])
        {
            $getUrl = "https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token=%s";
            $getUrl=sprintf($getUrl,$accessTokenData['data']);
            $jsonData = CurlBuilder::to($getUrl)->asJsonRequest()->get();
            //BLogger::getLogger('info')->info('获取草稿箱内的所有临时代码草稿:'.$jsonData);
            $jsonData=json_decode($jsonData,true);
            if(isset($jsonData['errcode'])&&isset($jsonData['draft_list']))
            {
                if($jsonData['errcode']==0&&!empty($jsonData['draft_list']))
                {
                    $cnt=0;
                    $wxxcxTemplateService=new  WXXCXTemplateService();
                    foreach($jsonData['draft_list'] as $item)
                    {
                        if(isset($item['create_time'])&&isset($item['user_version'])&&isset($item['user_desc'])&&isset($item['draft_id']))
                        {
                            $wxxcxTemplateData=$wxxcxTemplateService->getListByCondition(['draft_id'=>$item['draft_id'],'type'=>1]);
                            if($wxxcxTemplateData['errCode']==0&&empty($wxxcxTemplateData['data']))
                            {
                                $insertTemplateData=$wxxcxTemplateService->insertData(['create_time'=>$item['create_time'],'user_version'=>$item['user_version'],'user_desc'=>$item['user_desc'],'draft_id'=>$item['draft_id'],'type'=>1]);
                                if($insertTemplateData['errCode']==0&&$insertTemplateData['data']>0)
                                {
                                    $cnt++;
                                }
                            }
                        }
                    }
                    $returnData['data']=$cnt;
                }
            }
            else
            {
                $returnData['errCode']=-103;
                $returnData['errMsg']='调用微信接口没有返回数据';
                return $returnData;
            }
        }
        else
        {
            $returnData['errCode']=-105;
            $returnData['errMsg']='access_token 有问题';
            return  $returnData;
        }
        return  $returnData;
    }

    /**
     * todo 草稿箱的草稿选为小程序代码模版
     * @return array
     * @author jonzhang
     * @date 2018-01-18
     */
    public function insertTemplate($draftId=0)
    {
        $returnData = ['errCode' => 0, 'errMsg'=>''];
        if(empty($draftId))
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']='草稿id为空';
            return $returnData;
        }
        $accessTokenData=$this->getComponentAccessToken();
        if($accessTokenData['code']!=0)
        {
            $returnData['errCode']=-102;
            $returnData['errMsg']=$accessTokenData['message'];
            return $returnData;
        }
        if($accessTokenData['data'])
        {
            $postUrl="https://api.weixin.qq.com/wxa/addtotemplate?access_token=%s";
            $postUrl=sprintf($postUrl,$accessTokenData['data']);
            $postData['draft_id']=$draftId;
            $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
            //BLogger::getLogger('info')->info('草稿箱的草稿选为小程序代码模版:'.$jsonData);
            $jsonData=json_decode($jsonData,true);
            if(isset($jsonData['errcode']))
            {
                if($jsonData['errcode']!=0)
                {
                    $jsonData['errcode']=$jsonData['errcode']??'错误码为空';
                    $jsonData['errmsg']=$jsonData['errmsg']??'错误信息为空';
                    $returnData['errCode']=-104;
                    $returnData['errMsg']=$jsonData['errcode'].',错误信息:'.$jsonData['errmsg'];
                    return $returnData;
                }
                else
                {
                    $jsonData['errcode']=$jsonData['errcode']??'错误码为空';
                    $jsonData['errmsg']=$jsonData['errmsg']??'错误信息为空';
                    $returnData['errMsg']='code:'.$jsonData['errcode'].',msg:'.$jsonData['errmsg'];
                    return $returnData;
                }
            }
            else
            {
                $returnData['errCode']=-103;
                $returnData['errMsg']='调用微信接口没有返回数据';
                return $returnData;
            }
        }
        else
        {
            $returnData['errCode']=-105;
            $returnData['errMsg']='access_token 有问题';
            return  $returnData;
        }
    }

    /**
     * todo 获取代码模版库中的所有小程序代码模版
     * @return array
     * @author jonzhang
     * @date 2018-01-18
     */
    public function getTemplateList()
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>0];
        $accessTokenData=$this->getComponentAccessToken();
        if($accessTokenData['code']!=0)
        {
            $returnData['errCode']=-102;
            $returnData['errMsg']=$accessTokenData['message'];
            return $returnData;
        }
        if($accessTokenData['data'])
        {
            $getUrl = "https://api.weixin.qq.com/wxa/gettemplatelist?access_token=%s";
            $getUrl=sprintf($getUrl,$accessTokenData['data']);
            $jsonData = CurlBuilder::to($getUrl)->asJsonRequest()->get();
            //BLogger::getLogger('info')->info('获取代码模版库中的所有小程序代码模版:'.$jsonData);
            $jsonData=json_decode($jsonData,true);
            if(isset($jsonData['errcode'])&&isset($jsonData['template_list']))
            {
                if($jsonData['errcode']==0&&!empty($jsonData['template_list']))
                {
                    $cnt=0;
                    $wxxcxTemplateService=new  WXXCXTemplateService();
                    foreach($jsonData['template_list'] as $item)
                    {
                        if(isset($item['create_time'])&&isset($item['user_version'])&&isset($item['user_desc'])&&isset($item['template_id']))
                        {
                            $wxxcxTemplateData=$wxxcxTemplateService->getListByCondition(['template_id'=>$item['template_id'],'type'=>2]);
                            if($wxxcxTemplateData['errCode']==0&&empty($wxxcxTemplateData['data']))
                            {
                                $insertTemplateData=$wxxcxTemplateService->insertData(['create_time'=>$item['create_time'],'user_version'=>$item['user_version'],'user_desc'=>$item['user_desc'],'template_id'=>$item['template_id'],'type'=>2]);
                                if($insertTemplateData['errCode']==0&&$insertTemplateData['data']>0)
                                {
                                    $cnt++;
                                }
                            }
                        }
                    }
                    $returnData['data']=$cnt;
                }
            }
            else
            {
                $returnData['errCode']=-103;
                $returnData['errMsg']='调用微信接口没有返回数据';
                return $returnData;
            }
        }
        else
        {
            $returnData['errCode']=-105;
            $returnData['errMsg']='access_token 有问题';
            return  $returnData;
        }
        return $returnData;
    }

    /**
     * todo 删除指定小程序代码模版
     * @return array
     * @author jonzhang
     * @date 2018-01-18
     */
    public function deleteTemplate($templateId=0)
    {
        $returnData = ['errCode' => 0, 'errMsg'=>''];
        if(empty($templateId))
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']='模板id为空';
            return $returnData;
        }
        $accessTokenData=$this->getComponentAccessToken();
        if($accessTokenData['code']!=0)
        {
            $returnData['errCode']=-102;
            $returnData['errMsg']=$accessTokenData['message'];
            return $returnData;
        }
        if($accessTokenData['data'])
        {
            $getUrl ="https://api.weixin.qq.com/wxa/deletetemplate?access_token=%s";
            $getUrl =sprintf($getUrl,$accessTokenData['data']);
            $postData['template_id']=$templateId;
            $jsonData = CurlBuilder::to($getUrl)->asJsonRequest()->withData($postData)->post();
            //BLogger::getLogger('info')->info('删除指定小程序代码模版:'.$jsonData);
            $jsonData=json_decode($jsonData,true);
            if(isset($jsonData['errcode']))
            {
                if($jsonData['errcode']==0)
                {
                    return $returnData;
                }
                else
                {
                    $jsonData['errcode']=$jsonData['errcode']??'错误码为空';
                    $jsonData['errmsg']=$jsonData['errmsg']??'错误信息为空';
                    $returnData['errCode']=-104;
                    $returnData['errMsg']='code:'.$jsonData['errcode'].',msg:'.$jsonData['errmsg'];
                    return $returnData;
                }
            }
            else
            {
                $returnData['errCode']=-103;
                $returnData['errMsg']='调用微信接口没有返回数据';
                return $returnData;
            }
        }
        else
        {
            $returnData['errCode']=-105;
            $returnData['errMsg']='access_token 有问题';
            return  $returnData;
        }
    }


    /**
     * Author: MeiJay
     * @param string $XcxOpenId
     */
    public function sendToJavaKfSessionEvent($XcxOpenId = '')
    {
        $member = MemberService::getRowByXcxOpenId($XcxOpenId);
        if (!$member) {
            return ;
        }
        $data = (new ThirdPlatform())->getAuthorizerAccessToken(['wid'=>$member['wid']]);
        if($data['errCode'] != 0) {
            return ;
        }
        $postData = [
            'shopId' => $member['wid'],
            'user'   => $member,
            'token'  => $data['data'],
            'expireTime' => $data['expireTime']
        ];
        //post url
        $postUrl = config('app.chat_url').'/user/userJoin';
//        $postUrl ="192.168.0.118:8080/user/receiveMessage";
        //调用微信接口
        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        $result = json_decode($jsonData, true);
        if ($result && $result['code'] == 100 ) {
            \Log::info('小程序客服创建会话事件推送成功');
        }else {
            \Log::info('小程序客服创建会话事件推送失败');
        }

    }


    /**
     * 将消息转发给客服系统（考虑队列）
     * Author: MeiJay
     * @param array $content
     */
    public function sendToJavaKf($content = [])
    {
        $shopId = (new WXXCXConfigService())->getShopByOriginId($content['ToUserName']);
        if (!$shopId) {
            return ;
        }
        if ($content['MsgType'] == 'miniprogrampage') {
            $re = explode("=",$content['PagePath']);
            $message = [
                'url'   => $re[0],
                'price' => $re[1]
            ];
            $content['PagePath'] = json_encode($message);
        }
        $postData = [
            'message' => $content,
            'shopId'  => $shopId['wid']
        ];
        //post url
        $postUrl = config('app.chat_url').'/user/reviiceMessage';
//        $postUrl ="192.168.0.118:8080/user/receiveMessage";
        //调用微信接口
        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        $result = json_decode($jsonData, true);
        if ($result && $result['code'] == 100 ) {
            \Log::info('小程序客服消息发送成功');
        }else {
            \Log::info('小程序客服消息发送失败');
        }
    }


    /**
     * 小程序码
     * Author: MeiJay
     * @param $wid
     * @param $pagePath 跳转小程序页面路径
     * @param $code_scene 小程序场景值
     * @return array
     */
    public function createXcxQrCode($wid ,$pagePath ,$code_scene)
    {
        $accessTokenData = $this->getAuthorizerAccessToken(['wid' => $wid]);
        $result = [
            'errCode'   => 0,
            'errMsg'    => 'success',
            'data'      => ''
        ];
        if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data'])) {
            $accessToken = $accessTokenData['data'];
            //获取小程序码的URL
            $postUrl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s";
            $postUrl = sprintf($postUrl, $accessToken);
            $postData = ['scene' => $code_scene, 'page' => $pagePath];
            $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
            $returnValue = json_decode($jsonData, true);
            if (empty($returnValue)) {
                //二进制数据流转化为base64
                $result['data'] = base64_encode($jsonData);
            } else {
                $code = $returnValue['errcode'] ?? '小程序码错误码';
                $msg = $returnValue['errmsg'] ?? '小程序码错误信息';
                $result['errCode'] = -102;
                $result['errMsg'] = '错误码:' . $code . '错误信息:' . $msg;
            }
        }
        return $result;
    }

    /***
     * todo 获取小程序二维码
     * @param $wid
     * @param int $width
     * @param string $path
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-03-09
     * @update 梅杰 20180710 增加指定小程序首页小程序码并做兼容
     * @update 何书哲 2018年8月23日 添加后台操作日志
     */
    public function getXCXQRCode($wid,$width=430,$path="pages/index/index",$xcxConfigId = 0)
    {
        $result=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $staffOperService = new StaffOperLogService();
        if(empty($wid))
        {
            $result['errCode']=-1;
            $result['errMsg']='wid为null';
            return $result;
        }
        $key= $xcxConfigId == 0 ? $wid.'XCXQR': $wid.'-'.$xcxConfigId.'XCXQR';
        $isCache=false;
        $xcxCode=false;
        //只有店铺主页默认宽度才进行缓存
        if($width==430&&$path=="pages/index/index")
        {
            $isCache=true;
            //获取缓存中保存的小程序二维码
            $xcxCode=$this->getXCXCodeValue($key);
        }
        if(!$xcxCode)
        {
            $accessTokenData = $this->getAuthorizerAccessToken(['wid' => $wid,'id'=>$xcxConfigId]);
            if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data'])) {
                $accessToken = $accessTokenData['data'];
                //获取小程序码的URL
                $postUrl = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=%s";
                $postUrl = sprintf($postUrl, $accessToken);
                $postData = ["width" =>$width, "path" => $path];
                try {
                    $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                    $returnValue = json_decode($jsonData, true);
                    if (empty($returnValue)) {
                        //二进制数据流转化为base64
                        $result['data'] = base64_encode($jsonData);
                        //把店铺主页的二维码保存下来
                        if($isCache)
                        {
                            $this->setXCXCodeValue($key, $result['data']);
                        }
                        $staffOperService->write('操作成功', 13, $xcxConfigId);
                        return $result;
                    } else {
                        $code = $returnValue['errcode']??'小程序码错误码';
                        $msg = $returnValue['errmsg']??'小程序码错误信息';
                        $staffOperService->write('操作失败: '.json_encode($returnValue), 13, $xcxConfigId);
                        $result['errCode'] = -102;
                        $result['errMsg'] = '错误码:' . $code . '错误信息:' . $msg;
                        return $result;
                    }
                } catch (\Exception $ex) {
                    $result['errCode'] = -101;
                    $result['errMsg'] = $ex->getMessage();
                    return $result;
                }
            }
            else
            {
                if($accessTokenData['errCode']==-2)
                {
                    $accessTokenData['errMsg']='店铺没有绑定小程序或者小程序已经下架';
                }
                return $accessTokenData;
            }
        }
        $result['data']=$xcxCode;
        $staffOperService->write('操作成功', 13, $xcxConfigId);
        return  $result;
    }

    /**
     * todo 更改小程序是否可以搜索
     * @param string $action
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-03-09
     */
    public function changeVisitStatus($where=[],$action="close",$operatorId=0,$operator='')
    {
        $result=['errCode'=>0,'errMsg'=>''];
        $staffOperService = new StaffOperLogService();
        //where条件只能够为空id或者wid
        if(empty($where))
        {
            $result['errCode']=-100;
            $result['errMsg']='条件为空';
            return $result;
        }
        $wid=0;
        if(isset($where['wid']))
        {
            $wid = $where['wid'];
            unset($where['wid']);
        }
        $appId="";
        if(isset($where['appId']))
        {
            $appId=$where['appId'];
            unset($where['appId']);
        }
        $accessTokenData = $this->getAuthorizerAccessToken($where);
        if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data'])) {
            $accessToken = $accessTokenData['data'];
            //获取小程序码的URL
            $postUrl = "https://api.weixin.qq.com/wxa/change_visitstatus?access_token=%s";
            $postUrl = sprintf($postUrl, $accessToken);
            $postData = ['action' =>$action];
            try
            {
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
                $returnValue = json_decode($jsonData, true);
                if (isset($returnValue['errcode'])) {
                    if ($returnValue['errcode'] != 0) {
                        $staffOperService->write('操作失败：'.json_encode($returnValue), 19, $where['id']);
                        $result['errCode'] = -103;
                        $result['errMsg'] = $returnValue['errmsg'] ?? '';
                        return $result;
                    }
                    DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                        [$wid, $appId,18,'下架小程序',time(),$operatorId,$operator]);
                    $staffOperService->write('操作成功：'.json_encode($postData), 19, $where['id']);
                }
                else
                {
                    $code = $returnValue['errcode']??'小程序码错误码';
                    $msg = $returnValue['errmsg']??'小程序码错误信息';
                    $staffOperService->write('操作失败：'.json_encode($returnValue), 19, $where['id']);
                    $result['errCode'] = -102;
                    $result['errMsg'] = '错误码:' . $code . '错误信息:' . $msg;
                    return $result;
                }
                return $result;
            } catch (\Exception $ex) {
                $result['errCode'] = -101;
                $result['errMsg'] = $ex->getMessage();
                return $result;
            }
        } else {
            $staffOperService->write('操作失败：'.json_encode($accessTokenData), 19, $where['id']);
            return $accessTokenData;
        }
    }

    /***
     * todo 已经发布出去的小程序版本回退
     * @param array $where
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-03-30
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function revertCodeRelease($where=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $staffOperService = new StaffOperLogService();
        //where条件只能够为空id或者wid
        if(empty($where))
        {
            $returnData['errCode']=-100;
            $returnData['errMsg']='查询条件为空';
            return $returnData;
        }
        $accessTokenData = $this->getAuthorizerAccessToken($where);
        if ($accessTokenData['errCode'] == 0 && !empty($accessTokenData['data']))
        {
            $accessToken = $accessTokenData['data'];
            //获取小程序码的URL
            $postUrl = "https://api.weixin.qq.com/wxa/revertcoderelease?access_token=%s";
            $postUrl = sprintf($postUrl, $accessToken);
            try {
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->get();
                $jsonData = json_decode($jsonData, true);
                if(isset($jsonData['errcode']))
                {
                    if($jsonData['errcode']==0)
                    {
                        //何书哲 2018年8月30日 添加后台操作日志
                        $staffOperService->write('操作成功', 17, $where['id']);
                        return $returnData;
                    }
                    else
                    {
                        $jsonData['errcode']=$jsonData['errcode']??'错误码为空';
                        $jsonData['errmsg']=$jsonData['errmsg']??'错误信息为空';
                        $staffOperService->write('操作失败：'.json_encode($jsonData), 17, $where['id']);
                        $returnData['errCode']=-104;
                        $returnData['errMsg']='code:'.$jsonData['errcode'].',msg:'.$jsonData['errmsg'];
                        return $returnData;
                    }
                }
                else
                {
                    $staffOperService->write('操作失败：'.json_encode($jsonData), 17, $where['id']);
                    $returnData['errCode']=-103;
                    $returnData['errMsg']='调用微信接口没有返回数据';
                    return $returnData;
                }
            } catch (\Exception $ex) {
                $returnData['errCode'] = -101;
                $returnData['errMsg'] = $ex->getMessage();
                return $returnData;
            }
        }
        else
        {
            $staffOperService->write('操作失败：'.json_encode($accessTokenData), 17, $where['id']);
            if($accessTokenData['errCode']==-2)
            {
                $accessTokenData['errMsg']='店铺没有绑定小程序或者小程序已经下架';
            }
            return $accessTokenData;
        }
    }


    /**
     *
     * @param $appId
     * @param $code
     * @return bool|mixed
     * @author: 梅杰 2018年9月18
     */
    public function wxLogin($appId,$code)
    {
        $componentAccessToken = $this->getComponentAccessToken();
        if ($componentAccessToken['code']) {
            //异常处理
            return false;
        }
        $componentAccessToken = $componentAccessToken['data'];
        $url = "https://api.weixin.qq.com/sns/component/jscode2session?appid={$appId}&js_code={$code}&grant_type=authorization_code&component_appid={$this->appId}&component_access_token={$componentAccessToken}";
        $jsonData = CurlBuilder::to($url)->asJsonRequest()->get();
        $jsonData = json_decode($jsonData, true);
        if (isset($jsonData['errcode'])) {
            BLogger::getLogger('error')->error('开放平台微信小程序登录error:'.json_encode($jsonData));
            return false;
        }
        return $jsonData;
    }

}