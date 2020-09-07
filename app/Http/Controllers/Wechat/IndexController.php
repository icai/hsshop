<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Lib\Weixin\WXBizMsgCrypt;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Wechat\ApiService;
use App\Services\Wechat\AuthorizationService;
use App\Services\Wechat\WechatService;
use App\Services\WeixinConfigSubService;
use Illuminate\Http\Request;
use Log;
use PaymentService;


/**
 * 微信基础api
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月10日 11:16:19
 */
class IndexController extends Controller {

    protected $AppId;
    protected $AppSecret;
    protected $EncodingAesKey = 'A78L1bp0Hlk4FyQrwu70S8jC7o04pkDzM4rnPypW28r';
    protected $Token = '7N3WE6P8X';

    public function __construct()
    {
        $this->AppId = config('app.auth_appid');
        $this->AppSecret = config('app.auth_secret');
    }

    /**
     * 微信基础接口
     * @param  WechatService $wechatService [微信消息类]
     * @param  Integer       $wid           [店铺id]
     * @return Mixed                        [description]
     */
    public function index( WechatService $wechatService ) {
        if(is_file('original.log')){
            $original_id = file_get_contents('original.log');
        }
        $weChatConfService = new WeChatShopConfService();
        $data = $weChatConfService->getList(['original_id' => $original_id]);
        
        $wid = $data[0]['wid'] ?? session('wid');
        // 验证签名
        $wechatService->checkSignature($wid);

        // 消息处理
        $wechatService->dealMsg();
    }

    /**
     * 通过该URL接收公众号消息和事件推送，该参数按规则填写
     * @param  [string] $appId [微信公众平台appid]
     * @return [Mixed]        [description]
     * @update 梅杰 20180710 增加未获取到相关公众号配置错误处理
     */
    public function receiveMsg($appId, WechatService $wechatService,AuthorizationService $authorizationService)
    {
        
        if($appId == 'wx570bc396a51b8ff8'){
            $request = app('request');
            $input = $request->input();
            $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
            if(empty($postStr)){  
                $postStr = file_get_contents('php://input');  
            }  
            if($postStr!=''){  
                $postStr = PaymentService::xmlToArray($postStr); 
                $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
                $from_xml = sprintf($format, $postStr['Encrypt']);
            }
            $pc = new WXBizMsgCrypt($this->Token, $this->EncodingAesKey, $this->AppId);

            $msg = '';
            $errCode = $pc->decryptMsg($input['msg_signature'], $input['timestamp'], $input['nonce'], $from_xml, $msg);
            if ($errCode == 0) {
                $param = PaymentService::xmlToArray( $msg ); 
                $keyword = isset($param['Content']) ? trim($param['Content']) : '';
                // 案例1 - 发送事件
                if (isset($param['Event']) && $param['ToUserName'] == 'gh_3c884a361561') {
                    $contentStr = $param ['Event'] . 'from_callback';
                }
                // 案例2 - 返回普通文本
                elseif ($keyword == "TESTCOMPONENT_MSG_TYPE_TEXT") {
                    $contentStr = "TESTCOMPONENT_MSG_TYPE_TEXT_callback";
                }// 案例3 - 返回Api文本信息
                elseif (strpos ( $keyword, "QUERY_AUTH_CODE:" ) !== false) { // 案例3  
                    $authcode = str_replace ( "QUERY_AUTH_CODE:", "", $keyword );  
                    $contentStr = $authcode . "_from_api";  
                    $tokenInfo = $authorizationService->getAuthrizerAccessToken($authcode);
                    $param ['authorizerAccessToken'] = $tokenInfo['authorizer_access_token']; 
 
                    $data['touser'] = $param ['FromUserName'];
                    $data['msgtype'] = 'text';
                    $data['text'] = array('content'=>$contentStr);
                    // 客服消息接口 
                    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$param ['authorizerAccessToken'];
                    $res = jsonCurl($url,json_encode($data));
                    exit; 
                }  
                if (!empty($contentStr)) {
                    $wechatService->dealMsgFirst($contentStr);
                }
            }
        }else{
            $weChatConfService = new WeChatShopConfService();
            $data = $weChatConfService->getList(['app_id' => $appId]);
            
            if ($data && $wid = $data[0]['wid']) {
                // 验证签名
                $wechatService->checkSignature($wid);

                // 消息处理
                $wechatService->dealMsg();
            } else {
                \Log::info("未获取到appId为：{$appId}店铺的公众号配置信息");
            }

        }
    }   


    /**
     * [getResponse 微信开放平台授权事件接收URL]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getResponse(Request $request,AuthorizationService $authorizationService)
    {
        // 第三方发送消息给公众平台
        $input = $request->input();
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        if(empty($postStr)){  
            $postStr = file_get_contents('php://input');  
        }  
        if($postStr!=''){  
            $postStr = PaymentService::xmlToArray($postStr); 
            $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
            $from_xml = sprintf($format, $postStr['Encrypt']);
        }

        $authorizationService->getComponentVerifyTicket($input,$from_xml);
       
    }


    public function networkPublish($input,$fromXml)
    {
        $Wxcrypt = new WXBizMsgCrypt($this->Token, $this->EncodingAesKey, $this->AppId);
        $errCode = $Wxcrypt->decryptMsg($input['msg_signature'], $input['timestamp'], $input['nonce'], $fromXml, $msg);
        $param = PaymentService::xmlToArray( $msg );
        $keyword = isset($param['Content']) ? trim($param['Content']) : '';
    }

    /**
     * 测试接口
     * @param  ApiService $apiService [api服务类]
     * @param  Integet    $wid        [店铺id]
     * @return [type]                 [description]
     */
    public function getAccessToken( ApiService $apiService, WechatService $wechatService, $wid ) {
        $datas = [
            'button' => [
                [
                    'type'=> 'view',
                    'name'=> '会搜商城',
                    'url'=> 'http://hsshop.huisou.cn/shop/index/82'
                ]
            ]
        ];
        $result = $apiService->customMenuCreate($wid, $datas);
        dd($result);
    }
}
