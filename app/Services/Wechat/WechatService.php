<?php

namespace App\Services\Wechat;
define("TOKEN", "7N3WE6P8X");

use App\Lib\Redis\RedisClient;
use App\Lib\Weixin\WXBizMsgCrypt;
use App\Module\LiShareEventModule;
use App\S\Product\ProductGroupService;
use App\S\ShareEvent\ActivityRegisterService;
use App\S\ShareEvent\MeetingNexusService;
use App\S\Wechat\WeChatShopConfService;
use App\S\Wechat\WeixinCustomMenuService;
use App\S\Wechat\WeixinMaterialAdvancedService;
use App\S\Wechat\WeixinMaterialWechatService;
use App\S\Wechat\WeixinReplyRuleService;
use App\Services\Permission\WeixinUserService;
use Log;
use MemberHomeService;
use MicroPageService;
use ProductService;
use WeixinService;
use App\S\Member\MemberCardSyncLogService;
use MicroPageTypeService as WechatMicroPageTypeService;
use App\S\Market\SmokedEggsService;
use App\S\Wheel\ActivityWheelService;
use App\Services\Wechat\CustomService;
use App\S\Book\BookService;
use App\S\Weixin\ShopService;

/**
 * 微信通讯类
 *
 * 当用户发送消息给公众号时（或某些特定的用户操作引发的事件推送时），会产生一个POST请求，开发者可以在响应包（Get）中返回特定XML结构，来对该消息进行响应（现支持回复文本、图片、图文、语音、视频、音乐）。严格来说，发送被动响应消息其实并不是一种接口，而是对微信服务器发过来消息的一次回复。
 *
 * 微信服务器在将用户的消息发给公众号的开发者服务器地址（开发者中心处配置）后，微信服务器在五秒内收不到响应会断掉连接，并且重新发起请求，总共重试三次，如果在调试中，发现用户无法收到响应的消息，可以检查是否消息处理超时。关于重试的消息排重，有msgid的消息推荐使用msgid排重。事件类型消息推荐使用FromUserName + CreateTime 排重。
 *
 * 如果开发者希望增强安全性，可以在开发者中心处开启消息加密，这样，用户发给公众号的消息以及公众号被动回复用户消息都会继续加密（但），详见被动回复消息加解密说明。
 *
 * 假如服务器无法保证在五秒内处理并回复，必须做出下述回复，这样微信服务器才不会对此作任何处理，并且不会发起重试（这种情况下，可以使用客服消息接口进行异步回复），否则，将出现严重的错误提示。详见下面说明：
 * 1、（推荐方式）直接回复success
 * 2、直接回复空串（指字节长度为0的空字符串，而不是XML结构体中content字段的内容为空）
 *
 * 一旦遇到以下情况，微信都会在公众号会话中，向用户下发系统提示“该公众号暂时无法提供服务，请稍后再试”：
 * 1、开发者在5秒内未回复任何内容
 * 2、开发者回复了异常数据，比如JSON数据等
 *
 * 另外，请注意，回复图片等多媒体消息时需要预先通过素材管理接口上传临时素材到微信服务器，可以使用素材管理中的临时素材，也可以使用永久素材。
 *
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月14日 16:24:01
 */
class WechatService
{
    /**
     * http请求类
     *
     * @var Request
     */
    private $request;

    /**
     * 店铺信息
     *
     * @var array
     */
    private $info;
    private $wid;


    /**
     * 微信消息
     *
     * @var array
     */
    private $msg;

    private $EncodingAESKey = 'A78L1bp0Hlk4FyQrwu70S8jC7o04pkDzM4rnPypW28r';

    private $AppID;

    /**
     * 消息类型、事件
     *
     * @var array
     */
    private $listevent = [
        'text' => '文本信息',
        'image' => '图片消息',
        'voice' => '语音消息',
        'video' => '视频消息',
        'location' => '位置消息',
        'link' => '链接消息',
        'event' => [
            'subscribe' => '订阅账号',
            'unsubscribe' => '取消订阅',
            'scan' => '订阅扫描',
            'LOCATION' => '位置上报',
            'CLICK' => '菜单点击',
        ],
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = app('request');
        $this->AppID = config('app.auth_appid');
    }

    /**
     * 验证签名
     *
     * @param  Integer $wid [店铺id]
     * @return boolean [验证签名]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function checkSignature($wid)
    {
        $this->wid = $wid;
        $token = TOKEN;
        // 查询店铺信息 获取token
        /*$uid = D('Weixin')->model->where('id', $this->wid)->value('uid');
        $weixinService = D('Weixin', 'uid', $uid);
        $this->info = $weixinService->getInfo($this->wid);*/
        $this->info = (new ShopService())->getRowById($this->wid);
        // if ( !isset($this->info['weixinConfigSub']['token']) || empty($this->info['weixinConfigSub']['token']) ) {
        //     error('非法操作');
        // }

        // 获取到微信请求里包含的几项内容
        $input = $this->request->input();
        if (!isset($input['timestamp']) || !isset($input['nonce']) || !isset($input['signature'])) {
            error('非法操作');
        }
        // 加工出自己的 signature
        $signatureStr = [$token, $input['timestamp'], $input['nonce']];
        sort($signatureStr, SORT_STRING);
        $signatureStr = implode($signatureStr);
        $signatureStr = sha1($signatureStr);
        // 用自己的 signature 去跟请求里的 signature 对比
        if ($signatureStr == $input['signature']) {
            // 验证服务器地址
            if ($this->request->isMethod('get') && isset($input['echostr'])) {
                echo $input['echostr'];
                exit;
            }
            return true;
        }
        return false;
    }

    /**
     * 初始化函数 设置成员属性
     *
     * @param  array $para [其他参数]
     * @return $this
     */
    public function init($para = [])
    {
        foreach ($para as $key => $val) {
            $this->$key = $val;
        }

        return $this;
    }

    public function dealMsgFirst($content)
    {
        $this->receiveMsg();
        $this->textFirst($content);
        $this->fail();
    }

    public function textFirst($content)
    {
        $data['content'] = $content;
        $info['type'] = 1;
        $info['config'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->reply($info);
    }

    /**
     * 消息处理
     *
     * @return Mixed [失败执行通讯失败方法，成功返回true]
     */
    public function dealMsg()
    {
        // 接收消息
        $this->receiveMsg();
        // 调用对应方法

        //地理位置事件未做处理
        if (isset($this->msg['Event']) && ($this->msg['Event'] == 'LOCATION')) {
            $this->fail();
        }

        if (isset($this->msg['MsgType']) && method_exists($this, $this->msg['MsgType'])) {
            $msgType = $this->msg['MsgType'];
            $this->$msgType();

        }

        $this->fail();
    }

    /**
     * 接收消息
     *
     * @return Mixed [失败执行通讯失败方法，成功返回接收到的信息]
     */
    public function receiveMsg()
    {

        $msg = $this->request->getContent();
        $input = $this->request->input();
        $postStr = $this->decryptMsg($input, $msg);
        $msg = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (empty($msg)) {
            $this->fail();
        }
        $this->msg = (array)$msg;
        return $this->msg;
    }

    public function decryptMsg($input, $msg)
    {
        $pc = new WXBizMsgCrypt(TOKEN, $this->EncodingAESKey, $this->AppID);
        $decryptMsg = "";  //解密后的明文
        $errCode = $pc->DecryptMsg($input['msg_signature'], $input['timestamp'], $input['nonce'], $msg, $decryptMsg);
        $postStr = $decryptMsg;
        return $postStr;
    }

    /**
     * 接收普通消息 - 文本消息
     *
     * @return Mixed
     * @update 梅杰 随机回复时如果未设置不处理
     * @update 何书哲 2019年05月30日 完全匹配添加判断，防止回复内容为空的情况
     */
    public function text()
    {
        // 查询所有关键词
        $weixinReplyRuleService = new WeixinReplyRuleService();
        $bookService = new BookService();
        if (!$this->info) {
            return;
        }

        if ($this->wid == 626 && $this->msg['Content'] == '客服') {
            $service = new CustomService($this->wid);
            $waitCount = $service->getWaitCase();
            if (!empty($waitCount['errcode'])) {
                \Log::info($waitCount);
            }
            if ($waitCount['waitcaselist']) {
                $waitOpenid = array_column($waitCount['waitcaselist'], 'openid');
                rsort($waitOpenid);
                $pos = array_search($this->msg['FromUserName'], $waitOpenid);
                if (!$pos) {
                    $pos = count($waitOpenid) + 1;
                } else {
                    $pos = $pos + 1;
                }
            } else {
                $pos = 1;
            }
            $sendData = [
                'touser' => $this->msg['FromUserName'],
                'msgtype' => 'text',
                'text' => ['content' => "正在为您转接客服,当前您排在第{$pos}位,请稍等~~"]
            ];
            $service->sendMsg($sendData);
            $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                    </xml>";
            $result = sprintf($xmlTpl, $this->msg['FromUserName'], $this->msg['ToUserName'], time());
            echo $result;
            return;
        }

        if ($this->msg['Content'] == '我的会场热度') {
            $res = (new ActivityRegisterService())->getNum($this->msg['FromUserName']);
            $this->transmitText($this->msg, '您共邀请' . $res . '人');

        }


        /*add by wuxiaoping 微预约触发关键词回复图文信息 开始*/
        $bookList = $bookService->getAllList($this->info['id'], ['keywords' => $this->msg['Content']], '', false);
        if ($bookList) {
            $bookDatas = [];
            $tag = 0;
            foreach ($bookList as $bKey => $bValue) {
                $keywords = explode(',', $bValue['keywords']);
                if (in_array($this->msg['Content'], $keywords)) {
                    $tag++;
                    $bookDatas[$bKey]['Title'] = $bValue['title'];
                    $bookDatas[$bKey]['Description'] = preg_replace('/&nbsp;/', '', strip_tags(htmlspecialchars_decode($bValue['details'])));
                    $bookDatas[$bKey]['PicUrl'] = $bValue['cover_img'];
                    $bookDatas[$bKey]['Url'] = $tag >= 2 ? config('app.url') . 'shop/book/index/' . $this->info['id'] . '?keywords=' . $this->msg['Content'] : config('app.url') . 'shop/book/detail/' . $this->info['id'] . '/' . $bValue['id'];
                }
            }
            if ($bookDatas) {
                /*如果触发的关键词匹配多条记录，则随机获取一条*/
                $bookCount = count($bookDatas);
                if ($bookCount > 1) {
                    $bookKeys = array_keys($bookDatas);
                    $randKey = array_rand($bookKeys, 1);
                    $bookDatas = $bookDatas[$bookKeys[$randKey]];
                    $content = [$bookDatas];
                } else {
                    $content = $bookDatas;
                }
                $resultStr = $this->transmitNews($this->msg, $content);
                return $resultStr;
            }
        }
        /*微预约触发关键词回复图文信息 结束*/

        $list = $weixinReplyRuleService->getAllList($this->info['id'], [], false);
        $info = [];
        $data = [];
        foreach ($list as $key => $value) {
            $value['weixinReplyKeyword'] = json_decode($value['weixinReplyKeyword'], true);
            if (!empty($value['weixinReplyKeyword']) && is_array($value['weixinReplyKeyword'])) {
                foreach ($value['weixinReplyKeyword'] as $val) {
                    // 完全匹配
                    if ($val['type'] == 0 && $val['keyword'] == $this->msg['Content']) {
                        $value['weixinReplyContent'] = json_decode($value['weixinReplyContent'], true);
                        if ($value['reply_all']) {
                            //回复全部
                            $this->replyAll($value['weixinReplyContent']);
                            return;
                        }

                        //添加判断，防止回复内容为空的情况
                        if (!empty($value['weixinReplyContent']) && is_array($value['weixinReplyContent'])) {
                            $info = $value['weixinReplyContent'][mt_rand(0, (count($value['weixinReplyContent']) - 1))];
                            $this->reply($info);
                        }
                        break 2;
                    }
                    // 模糊匹配
                    if ($val['type'] == 1 && str_contains($val['keyword'], $this->msg['Content'])) {
                        $value['weixinReplyContent'] = json_decode($value['weixinReplyContent'], true);

                        if ($value['reply_all']) {
                            //回复全部
                            $this->replyAll($value['weixinReplyContent']);
                            return;
                        }
                        if ($value['weixinReplyContent']) {
                            $info = $value['weixinReplyContent'][mt_rand(0, (count($value['weixinReplyContent']) - 1))];
                            $this->reply($info);
                        }
                        break 2;
                    }
                }
            }

        }

        // 被动回复消息（消息托管）
        $content = '';
        list($list) = $weixinReplyRuleService->getAllList($this->wid, ['type' => 3]);
        if ($list['data']) {
            $list['data'][0]['weixinReplyContent'] = json_decode($list['data'][0]['weixinReplyContent'], 1);
        }
        $replyData = [];
        $jsonData = [];
        foreach ($list['data'] as $key => $value) {

            if (!empty($value['weixinReplyContent']) && is_array($value['weixinReplyContent'])) {
                $replyData = $value['weixinReplyContent'][mt_rand(0, (count($value['weixinReplyContent']) - 1))];
                $jsonCont = $replyData['config'];
                $jsonData = json_decode($jsonCont, true);
            }
        }
        if (!empty($replyData)) {
            if (isset($jsonData['type']) && $jsonData['type']) {
                //图文信息
                if ($replyData['type'] == 3) {
                    $content = [];
                    $content = $this->getMaterial($jsonData);
                } //其他信息
                else if ($replyData['type'] == 6) {
                    $content = [];
                    $otherDatas = $this->getOther($jsonData);
                    $content = [$otherDatas];
                }
            } //文本信息
            else {
                $content = $jsonData['content'];
            }

        }
        if (!$content) {
            return;
        }
        if (is_array($content)) {
            $resultStr = $this->transmitNews($this->msg, $content);
        } else {
            $resultStr = $this->transmitText($this->msg, strip_tags($content));
        }
        return $resultStr;

    }

    /**
     * 通讯失败
     *
     * @return string [返回空字符串并中断程序执行]
     */
    public function fail()
    {
        echo 'success';
        exit;
    }

    /**
     * 回复内容数据解析
     *
     * 回复类型：1文本（表情、链接、文字）；2图片；3(多)图文（微信图文，高级图文）；4语音；5音乐；6其他（1商品；2商品分类；3微页面；4微页面分类；5店铺主页；6会员主页）
     *
     * 内容配置信息，示例为全部类型的示例，实际情况只会出现一种类型
     * 第一层（text/img/music...）只是为了更明显的指定每种类型对应什么数据，实际存储应该把这一层去掉
     * {
     *     "text": {
     *         "content": "文本类型内容（文字、链接、表情）"
     *     },
     *     "img": {
     *         "url": "http://www.huihaokeji.cn/mctsource/images/merchants_logo.png",
     *         "media_id": "通过素材管理接口上传多媒体文件，得到的id"
     *     },
     *     "voice": {
     *         "url": "语音类型的音乐文件地址",
     *         "media_id": "通过素材管理接口上传多媒体文件，得到的id"
     *     },
     *     "music": {
     *         "title": "音乐标题",
     *         "desc": "音乐描述",
     *         "img": "缩略图",
     *         "normal": "普通音质网络地址",
     *         "hd": "高清音质网络地址，WIFI环境优先使用该链接播放音乐",
     *         "media_id": "缩略图的媒体id，通过素材管理接口上传多媒体文件，得到的id"
     *     },
     *     "pictxt": {
     *         "type": "1微信图文；2高级图文",
     *         "id": "对应类型的数据id",
     *         "title": "对应类型的数据标题"
     *     },
     *     "other": {
     *         "type": "1商品；2商品分类；3微页面；4微页面分类；5店铺主页；6会员主页，7微信客服",
     *         "id": "对应类型的数据id",
     *         "title": "对应类型的数据标题"
     *     }
     * }
     *
     * @param  [array] $datas [回复内容配置信息数组]
     * @return [Mixed]        [解析成功返回对应XML字符串，解析失败调用通讯失败方法]
     */
    public function parsing($datas)
    {
        if (empty($datas) || !isset($datas['type']) || !isset($datas['config'])) {
            return false;
        }

        $datas['config'] = json_decode($datas['config'], true);
        empty($datas['config']) && $this->fail();
        switch ($datas['type']) {
            // 回复文本消息
            case '1':
                $this->replyContentConfigVerify($datas['config'], ['content']);
                // $this->replyType    = 'text';
                // $this->replyContent = '<Content><![CDATA[' . $datas['config']['content'] . ']]></Content>';
                $this->transmitText($this->msg, $datas['config']['content']);
                break;
            // 回复图片消息
            case '2':
                $this->replyType = 'image';
                $this->transmitImg($this->msg, $datas['config']['media_id']);
                break;
            // 回复图文消息
            case '3':
                // 查询图文素材信息
                $materialDatas = $this->getMaterial($datas['config']);
                $this->replyType = 'news';
                $this->transmitNews($this->msg, $materialDatas);
                break;
            // 回复语音消息
            case '4':
                !isset($datas['config']['voice']['media_id']) && $this->fail();
                $this->replyType = 'voice';
                $this->replyContent = '<Voice>
                                       <MediaId><![CDATA[' . $datas['config']['voice']['media_id'] . ']]></MediaId>
                                       </Voice>';
                break;
            // 回复音乐消息
            case '5':
                if (!isset($datas['config']['music']) || count($datas['config']['music']) != 6) {
                    $this->fail();
                }
                $this->replyType = 'music';
                $this->replyContent = '<Music>
                                      <Title><![CDATA[' . $datas['config']['music']['title'] . ']]></Title>
                                      <Description><![CDATA[' . $datas['config']['music']['desc'] . ']]></Description>
                                      <MusicUrl><![CDATA[' . $datas['config']['music']['normal'] . ']]></MusicUrl>
                                      <HQMusicUrl><![CDATA[' . $datas['config']['music']['hd'] . ']]></HQMusicUrl>
                                      <ThumbMediaId><![CDATA[' . $datas['config']['music']['media_id'] . ']]></ThumbMediaId>
                                      </Music>';
                break;
            // 回复其他消息
            case '6':
                $otherDatas = $this->getOther($datas['config']);
                if (empty($otherDatas)) {
                    $this->transmitText($this->msg, '请先在后台设置该关键词匹配规则');
                }
                $this->replyType = 'news';
                $this->transmitNews($this->msg, [$otherDatas]);
                break;
            case 7:
                $service = new CustomService($this->wid);
                $waitCount = $service->getWaitCase();
                if (!empty($waitCount['errcode'])) {
                    \Log::info($waitCount);
                    break;
                }
                if ($waitCount['waitcaselist']) {
                    $waitOpenid = array_column($waitCount['waitcaselist'], 'openid');
                    rsort($waitOpenid);
                    $pos = array_search($this->msg['FromUserName'], $waitOpenid);
                    if (!$pos) {
                        $pos = count($waitOpenid) + 1;
                    } else {
                        $pos = $pos + 1;
                    }
                } else {
                    $pos = 1;
                }
                $sendData = [
                    'touser' => $this->msg['FromUserName'],
                    'msgtype' => 'text',
                    'text' => ['content' => "正在为您转接客服,当前您排在第{$pos}位,请稍等~~"]
                ];
                $service->sendMsg($sendData);
                $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                    </xml>";
                $result = sprintf($xmlTpl, $this->msg['FromUserName'], $this->msg['ToUserName'], time());
                echo $result;
                return;
                break;
            default:
                return false;
        }

        return $this->buildReplyXml();
    }

    /**
     * 回复内容配置数据验证
     *
     * @param  array $datas [配置数据数组]
     * @param  array $param [待验证参数]
     * @return mixed
     */
    public function replyContentConfigVerify($datas, $param)
    {
        foreach ($param as $value) {
            if (!isset($datas[$value]) || empty($datas[$value])) {
                $this->fail();
            }
        }

        return true;
    }

    /**
     * 解析其他类型并查询出对应数据
     *
     * 其他类型：1商品；2商品分类；3微页面；4微页面分类；5店铺主页；6会员主页
     *
     * @param  array $config [回复内容其他类型的配置参数]
     * @return array         [回复所需数据]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getOther($config)
    {
        switch ($config['type']) {
            // 商品
            case '1':
//                $productData = ProductService::init('wid', $this->info['id'])->getInfo($config['id']);
                $productData = ProductService::getDetail($config['id']);
                if ($productData) {
                    $datas['Title'] = $productData['title'];
                    $datas['Description'] = strip_tags(htmlspecialchars_decode($productData['introduce']));
                    $datas['PicUrl'] = config('app.url') . $productData['img'];
                    $datas['Url'] = $this->parseUrl($this->info['id'], $productData['id'], '/product/detail');
                }
                break;
            // 商品分类
            case '2':
                $productGroupService = new ProductGroupService();
                //$productGroupData = $productGroupService->init('wid', $this->info['id'])->getInfo($config['id']);
                $productGroupData = $productGroupService->getDetail($config['id']);
                if ($productGroupData) {
                    $datas['Title'] = $productGroupData['title'];
                    $datas['Description'] = $productGroupData['introduce'] ? strip_tags(htmlspecialchars_decode($productGroupData['introduce'])) : '商品分组';
                    $datas['PicUrl'] = config('app.url') . 'mctsource/static/images/microPage.jpg';
                    $datas['Url'] = $this->parseUrl($this->info['id'], $productGroupData['id'], '/group/detail');
                }
                break;
            // 微页面
            case '3':
                $MicroPageData = MicroPageService::getRowById($config['id']);
                if ($MicroPageData['errCode'] == 0 && !empty($MicroPageData['data'])) {
                    $microPage = $MicroPageData['data'];
                    $datas['Title'] = $microPage['page_title'];
                    $datas['Description'] = $microPage['page_description'];
                    $datas['PicUrl'] = config('app.url') . 'mctsource/static/images/microPage.jpg';
                    $datas['Url'] = $this->parseUrl($this->info['id'], $microPage['id'], '/microPage/index');
                }
                break;
            // 微页面分类
            case '4':
                $MicroPageTypeData = WechatMicroPageTypeService::getListByCondition(['id' => $this->info['id']]);
                if ($MicroPageTypeData['errCode'] == 0 && !empty($MicroPageTypeData['data'])) {
                    $result = $MicroPageTypeData['data'][0];
                    $datas['Title'] = $result['title'];
                    $datas['Description'] = '微页面分类-' . $result['title'];
                    $datas['PicUrl'] = 'http://discuz.comli.com/weixin/weather/icon/cartoon.jpg';
                    $datas['Url'] = $this->parseUrl($this->info['id'], $result['id'], '/microPage/type');
                }
                break;
            // 店铺主页
            case '5':
                /*$uid = D('Weixin')->model->where('id', $this->wid)->value('uid');
                $weixinService = D('Weixin', 'uid', $uid);
                $StoreData= $weixinService->getInfo($this->wid);*/
                $StoreData = (new ShopService())->getRowById($this->wid);
                if ($StoreData) {
                    $datas['Title'] = $StoreData['shop_name'];
                    $datas['Description'] = '店铺主页';
                    $datas['PicUrl'] = $StoreData['logo'];
                    $datas['Url'] = $this->parseUrl($this->info['id'], 0, '/index');
                }
                break;
            // 会员主页
            case '6':
                $wid = $this->info['id'];
                $memberHomeData = MemberHomeService::getRow($wid);
                if ($memberHomeData['errCode'] == 0 && !empty($memberHomeData['data'])) {
                    $datas['Title'] = $memberHomeData['data']['home_name'];
                    $datas['Description'] = '会员主页';
                    $custom_info = json_decode($memberHomeData['data']['custom_info'], true);
                    $datas['PicUrl'] = config('app.url') . 'mctsource/static/images/memberHome.jpg';
                    $datas['Url'] = $this->parseUrl($wid, 0, '/member/index', false);
                }
                break;

            //营销活动
            case '7':
                if ($config['activeType'] == 1) { //表示砸金蛋活动
                    $smokedEggsService = new SmokedEggsService();
                    $activityList = $smokedEggsService->getInfoById($config['id']);
                    $datas['Title'] = $activityList['title'];
                    $datas['Description'] = '砸金蛋活动';
                    $datas['PicUrl'] = config('app.url') . $activityList['start_img_url'];
                    $datas['Url'] = $this->parseUrl($this->info['id'], $config['id'], '/activity/egg/index');

                } else if ($config['activeType'] == 2) { //表示大转盘活动
                    $activityWheelService = new ActivityWheelService();
                    $activityList = $activityWheelService->getRowById($config['id']);
                    $datas['Title'] = $activityList['title'];
                    $datas['Description'] = '大转盘活动';
                    $datas['PicUrl'] = config('app.url') . 'mctsource/images/rotatePlan.png';
                    $datas['Url'] = $this->parseUrl($this->info['id'], $config['id'], '/activity/wheel');
                }
                break;

            default:
                $datas = [];
                break;
        }
        return $datas;
    }

    /**
     * 微信公众号回复url生成
     * @param  int $wid [店铺id]
     * @param  string $uri [跳转路由uri，注意开头不要加反斜杠，因为在环境配置中项目url在最后加了反斜杠]
     * @param  int $id [对应跳转页面的id  微页面，会员主页，店铺主页，商品等]
     * @return url
     */
    public function parseUrl($wid = 0, $id = 0, $uri = '')
    {
        $token = $id ? '/' . $id : '';
        $param = '?wid=' . $wid;
        return config('app.url') . 'shop' . $uri . '/' . $wid . $token . $param;
    }

    /**
     * 获取图文素材信息
     *
     * @param  string $id [图文素材配置数组]
     * @return array      [获取成功返回回复图文所需数组，获取失败调用通讯失败方法]
     */
    public function getMaterial($config)
    {
        if (!isset($config['type']) || !isset($config['id'])) {
            $this->fail();
        }

        $content = [];
        if ($config['type'] == 1) {
            // 微信图文
            $weixinMaterialWechatService = new WeixinMaterialWechatService();
            $list = $weixinMaterialWechatService->getRowById($config['id']);
            if (empty($list)) {
                $this->fail();
            }
            if ($list['type'] == 1) {
                $result[] = $list;
            } else {
                $_childData = $weixinMaterialWechatService->getChildList($config['id']);
                $result[] = $list;
                foreach ($_childData as $child) {
                    $result[] = $child;
                }

            }

            foreach ($result as $key => $li) {
                $content[$key]['Title'] = $li['title'];
                $content[$key]['Description'] = $li['digest'];
                $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                $content[$key]['Url'] = config('app.url') . 'shop/material/detail/' . $this->info['id'] . '/1/' . $li['id'];
            }
        } elseif ($config['type'] == 2) {
            // 高级图文
            $weixinMaterialAdvancedService = new WeixinMaterialAdvancedService();
            $list = $weixinMaterialAdvancedService->getRowById($config['id']);
            if (empty($list)) {
                $this->fail();
            }
            if ($list['type'] == 1) {
                $result[] = $list;
            } else {
                $_childData = $weixinMaterialAdvancedService->getChildList($config['id']);
                $result[] = $list;
                foreach ($_childData as $child) {
                    $result[] = $child;
                }

            }
            // 转为树形结构
            //$result = $weixinMaterialAdvancedService->listToTree($list['data']);
            foreach ($result as $key => $li) {
                $content[$key]['Title'] = $li['title'];
                $content[$key]['Description'] = $li['digest'];
                $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                $content[$key]['Url'] = $li['href']; //config('app.url').'shop/material/detail/'.$this->info['id'].'/2/'.$li['id'];
            }
        } else {
            $this->fail();
        }


        return $content;
    }

    /**
     * 构建回复xml
     *
     * @return boolean [成功返回true，失败返回false]
     */
    public function buildReplyXml()
    {
        if (empty($this->replyContent)) {
            return false;
        }
        $input = $this->request->input();
        $this->replyXml = '<xml>
              <ToUserName><![CDATA[' . $this->msg['FromUserName'] . ']]></ToUserName>
              <FromUserName><![CDATA[' . $this->msg['ToUserName'] . ']]></FromUserName>
              <CreateTime>' . time() . '</CreateTime>
              <MsgType><![CDATA[' . $this->replyType . ']]></MsgType>' . $this->replyContent;
        $this->replyXml .= '<FuncFlag>0</FuncFlag>';
        $this->replyXml .= '</xml>';

        $this->replyXml = $this->encyptMsg($this->replyXml, $input);

        return true;
    }

    /**
     * [encyptMsg 回复消息加密]
     * @param  [type] $replyXml [description]
     * @param  [type] $input    [description]
     * @return [type]           [description]
     */
    public function encyptMsg($replyXml, $input)
    {
        $pc = new WXBizMsgCrypt(TOKEN, $this->EncodingAESKey, $this->AppID);
        $encryptMsg = ''; //加密后的密文
        $errCode = $pc->encryptMsg($replyXml, $input['timestamp'], $input['nonce'], $encryptMsg);
        $result = $encryptMsg;
        return $result;
    }

    /**
     * 回复消息
     *
     * @param  array $datas [回复内容数据数组]
     * @return mixed        [成功返回xml，失败调用通讯失败方法]
     */
    public function reply($datas)
    {
        // 数据解析
        if ($this->parsing($datas)) {
            $this->send();
        }

        $this->fail();
    }

    /**
     * 发送回复消息
     *
     * @return void
     */
    public function send()
    {
        if (empty($this->replyXml)) {
            $this->fail();
        }
        echo $this->replyXml;

        exit;
    }

    /**
     * 事件推送
     *
     * @return Mixed
     */
    public function event()
    {
        $content = '';
        $isStrap = false;
        if ($this->msg['Event'] == 'subscribe') {
            if ($re = preg_match("/^qrscene_bindAdmin_(\d+)$/", $this->msg['EventKey'], $matches)) {
                $openId = $this->msg['FromUserName'];
                $service = new WeixinUserService();
                $res = $service->bindAdmin($this->wid, $openId, $matches[1]);
                $content = $res['msg'];
            } else {
                $weixinReplyRuleService = new WeixinReplyRuleService();
                list($list) = $weixinReplyRuleService->getAllList($this->wid, ['type' => 2]);
                if ($list['data']) {
                    $list['data'][0]['weixinReplyContent'] = $list['data'][0]['weixinReplyContent'] = json_decode($list['data'][0]['weixinReplyContent'], 1);
                }
                $replyData = [];
                $jsonData = [];
                foreach ($list['data'] as $key => $value) {

                    if (!empty($value['weixinReplyContent']) && is_array($value['weixinReplyContent'])) {
                        $replyData = $value['weixinReplyContent'][mt_rand(0, (count($value['weixinReplyContent']) - 1))];
                        $jsonCont = $replyData['config'];
                        $jsonData = json_decode($jsonCont, true);
                    }
                }
                if (!empty($replyData)) {
                    if (isset($jsonData['type']) && $jsonData['type']) {
                        //图文信息
                        if ($replyData['type'] == 3) {
                            $content = [];
                            $content = $this->getMaterial($jsonData);
                        } //其他信息
                        else if ($replyData['type'] == 6) {
                            $content = [];
                            $otherDatas = $this->getOther($jsonData);
                            $content = [$otherDatas];
                        }
                    } //文本信息
                    else {
                        $content = $jsonData['content'];
                    }

                } else {
                    $data = (new WeChatShopConfService())->getRowByWid($this->wid);
                    $content = '您好，欢迎关注' . $data['name'] . '！';
                }

                if (in_array($this->wid, config('app.li_wid')) || $this->wid == '823') {
                    $isStrap = true;
                    $content = $this->getSpecialMsg();
                }
                if ($this->wid == '3714') {
                    $isStrap = true;
                    $content = $this->getExhibitionMsg();
                }
            }
        } else if ($this->msg['Event'] == 'CLICK') {
            $WeixinCustomMenuService = new WeixinCustomMenuService();
            $list = $WeixinCustomMenuService->getAllList($this->wid, [], false);

            if (!empty($list)) {
                foreach ($list as $key => $val) {
                    if ($this->msg['EventKey'] == $val['value']) {
                        /**
                         * 表示event为click，又是图文回复
                         * 因为view事件无须响应，将直接跳转过去
                         * 所以只要回复一个图文或纯文本
                         * 只有纯文本，图文，营销活动要回复，其他将直接跳转
                         */
                        if ($val['type'] == 1 && json_decode($val['content'])) {
                            $data = json_decode($val['content'], true);
                            if ($data['type'] == 7) {
                                $weixinMaterialWechatService = new WeixinMaterialWechatService();
                                $MaterialWechatList = $weixinMaterialWechatService->getRowById($data['content_id']);
                                //微信单条图文
                                if ($MaterialWechatList['type'] == 1) {
                                    $result[] = $MaterialWechatList;
                                } else {
                                    $_childData = $weixinMaterialWechatService->getChildList($data['content_id']);
                                    $result[] = $MaterialWechatList;
                                    foreach ($_childData as $child) {
                                        $result[] = $child;
                                    }

                                }
                                $content = [];
                                foreach ($result as $key => $li) {
                                    $content[$key]['Title'] = $li['title'];
                                    $content[$key]['Description'] = $li['digest'];
                                    $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                                    $content[$key]['Url'] = config('app.url') . 'shop/material/detail/' . $this->info['id'] . '/1/' . $li['id'];
                                }
                            } //高级图文
                            else if ($data['type'] == 8) {

                                $weixinMaterialAdvancedService = new WeixinMaterialAdvancedService();
                                $MaterialAdvancedList = $weixinMaterialAdvancedService->getRowById($data['content_id']);
                                //高级单条图文
                                if ($MaterialAdvancedList['type'] == 1) {
                                    $result[] = $MaterialAdvancedList;
                                } else {
                                    $_childData = $weixinMaterialAdvancedService->getChildList($data['content_id']);
                                    $result[] = $MaterialAdvancedList;
                                    foreach ($_childData as $child) {
                                        $result[] = $child;
                                    }
                                }
                                $content = [];
                                foreach ($result as $key => $li) {
                                    $content[$key]['Title'] = $li['title'];
                                    $content[$key]['Description'] = $li['digest'];
                                    $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                                    $content[$key]['Url'] = $li['href']; //config('app.url').'shop/material/detail/'.$this->info['id'].'/2/'.$li['id'];
                                }
                            } //营销活动
                            else if ($data['type'] == 14) {
                                if ($data['activityType'] == 1) { //表示砸金蛋活动
                                    $smokedEggsService = new SmokedEggsService();
                                    $activityList = $smokedEggsService->getInfoById($data['content_id']);
                                    $activityList['digest'] = '砸金蛋活动';
                                    $activityList['cover'] = $activityList['start_img_url'];
                                    $activityList['href'] = $data['url'];
                                    $result[] = $activityList;
                                } else if ($data['activityType'] == 2) { //表示大转盘活动
                                    $activityWheelService = new ActivityWheelService();
                                    $activityList = $activityWheelService->getRowById($data['content_id']);
                                    $activityList['digest'] = '大转盘活动';
                                    $activityList['cover'] = 'mctsource/images/rotatePlan.png';
                                    $activityList['href'] = $data['url'];
                                    $result[] = $activityList;
                                }

                                $content = [];
                                foreach ($result as $key => $li) {
                                    $content[$key]['Title'] = $li['title'];
                                    $content[$key]['Description'] = $li['digest'];
                                    $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                                    $content[$key]['Url'] = $li['href'];
                                }
                            } //微预约
                            else if ($data['type'] == 17) {
                                $bookService = new BookService();
                                $bookInfo = $bookService->getRowById($data['content_id']);
                                $bookInfo['digest'] = $bookInfo['detail'];
                                $bookInfo['cover'] = $bookInfo['cover_img'];
                                $bookInfo['href'] = $data['url'];
                                $result[] = $bookDatas;

                                $content = [];
                                foreach ($result as $key => $li) {
                                    $content[$key]['Title'] = $li['title'];
                                    $content[$key]['Description'] = $li['digest'];
                                    $content[$key]['PicUrl'] = config('app.url') . $li['cover'];
                                    $content[$key]['Url'] = $li['href'];
                                }
                            } else if ($data['type'] == 18) {
                                //微信客服
                                $service = new CustomService($this->wid);
                                $waitCount = $service->getWaitCase();
                                if (!empty($waitCount['errcode'])) {
                                    \Log::info($waitCount);
                                    break;
                                }
                                if ($waitCount['waitcaselist']) {
                                    $waitOpenid = array_column($waitCount['waitcaselist'], 'openid');
                                    rsort($waitOpenid);
                                    $pos = array_search($this->msg['FromUserName'], $waitOpenid);
                                    if (!$pos) {
                                        $pos = count($waitOpenid) + 1;
                                    } else {
                                        $pos = $pos + 1;
                                    }
                                } else {
                                    $pos = 1;
                                }
                                $sendData = [
                                    'touser' => $this->msg['FromUserName'],
                                    'msgtype' => 'text',
                                    'text' => ['content' => "正在为您转接客服,当前您排在第{$pos}位,请稍等~~"]
                                ];
                                $service->sendMsg($sendData);
                                $xmlTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                                </xml>";
                                $result = sprintf($xmlTpl, $this->msg['FromUserName'], $this->msg['ToUserName'], time());
                                echo $result;
                                return;
                            }
                            // $content = [
                            //     "Title"       => $data['content_title'],
                            //     "Description" => "您正在使用自定义菜单测试接口",
                            //     "PicUrl"      => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
                            //     "Url"         => !empty($data['url']) ? $data['url'] : config('app.url').'/shop/index/'.$this->info['id']
                            // ];
                        } else {
                            $content = preg_replace('/&nbsp;/', '', $val['content']);
                            $content = str_replace('<p>', '', $content);
                            $content = str_replace('</p>', "\n", $content);
                        }
                    }
                }
            } else {
                $content = '数据不一致，请先清空缓存';
            }

        } else if ($this->msg['Event'] == 'user_get_card') { //领取卡券
            $data = [];
            $mid = $this->msg['OuterStr'] ?? 0; //二维码Url中自定义的自符串参数
            $card_id = $this->msg['CardId'];
            $code = $this->msg['UserCardCode'];
            $data['code'] = $code;
            //把用户id,同步会员卡的id保存到数据库中
            $data['mid'] = $mid;
            $data['card_id'] = $card_id;
            $memberCardSyncLogService = new MemberCardSyncLogService();
            $memberCardSyncLogService->add($data);

        } else if ($this->msg['Event'] == 'SCAN') { //管理员绑定微信账号
            #todo 获取该用户的基本信息
            $key = explode('_', $this->msg['EventKey']);
//            $content = '绑定成功';
            if ($key[0] == 'bindAdmin') {
                $openId = $this->msg['FromUserName'];
                $service = new WeixinUserService();
                $res = $service->bindAdmin($this->wid, $openId, $key[1]);
                $content = $res['msg'];
            }
            //判断是否是特定店铺
            if (in_array($this->wid, config('app.li_wid'))) {
                $userInfo = $this->getUserInfo();
                $this->setNexus($userInfo, 2);
            }

            return 'success';
        }
        if (is_array($content)) {
            $resultStr = $this->transmitNews($this->msg, $content);
        } else {
            if (!$isStrap) {
                $content = strip_tags($content);
            }
            $resultStr = $this->transmitText($this->msg, $content);
        }
        return $resultStr;

    }

    /**
     * [transmitText 自定义菜单点击事件推送文本]
     * @param  [array]  $data     [微信回复的数据 注：其实原本微信服务器返回的是一个对象，通过receiveMsg方法转为数组]
     * @param  [string]  $content  [回复的内容]
     * @param  integer $funcFlag [description]
     * @return [type]            [description]
     */
    private function transmitText($data, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $data['FromUserName'], $data['ToUserName'], time(), $content, $funcFlag);

        echo $resultStr;
    }

    /**
     * [transmitNews 自定义菜单点击事件推送图文]
     * @param  [array]  $data     [微信回复的数据 注：其实原本微信服务器返回的是一个对象，通过receiveMsg方法转为数组]
     * @param  [array]  $arr_item [回复的内容信息]
     * @param  integer $funcFlag [description]
     * @return [type]            [description]
     * @update 何书哲 2019年06月20日 item_str在外赋值，避免网址中的%号在sprintf需要传值，会报错
     */
    private function transmitNews($data, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
        if (!is_array($arr_item)) {
            return;
        }

        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";
        $item_str = "";
        foreach ($arr_item as $item) {
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }

        $flag = empty($data['FromUserName']) || empty($data['ToUserName']) || empty($item_str);
        if ($flag) {
            return;
        }

        //update 何书哲 2019年06月20日 item_str在外赋值，避免网址中的%号在sprintf需要传值，会报错
        $newsTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <Content><![CDATA[]]></Content>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>%s</Articles>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";

        $resultStr = sprintf($newsTpl, $data['FromUserName'], $data['ToUserName'], time(), count($arr_item), $item_str, $funcFlag);

        echo $resultStr;
    }

    /**
     * [transmitImg 自定义菜单点击事件推送图片]
     * @param  [array]  $data      [微信回复的数据 注：其实原本微信服务器返回的是一个对象，通过receiveMsg方法转为数组]
     * @param  [string]  $media_id [上传图片的media_id]
     * @return [type]            [description]
     */
    public function transmitImg($data, $media_id)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                    </xml>";
        $resultStr = sprintf($textTpl, $data['FromUserName'], $data['ToUserName'], time(), $media_id);
        echo $resultStr;
    }


    //add MayJay 全部发送
    public function replyAll($replayData)
    {
        foreach ($replayData as $replayDatum) {
            $this->replyAllParsing($replayDatum);
        }
    }


    public function replyAllParsing($datas)
    {
        if (empty($datas) || !isset($datas['type']) || !isset($datas['config']) || empty($datas['config'])) {
            return false;
        }
        $datas['config'] = json_decode($datas['config'], true);
//        empty($datas['config']) && $this->fail();
        $sendData['touser'] = $this->msg['FromUserName'] ?? 'o2TW-wa-GcAyN-w_aaV6uM2c4GZQ';
        switch ($datas['type']) {
            // 回复文本消息
            case '1':
                $sendData['msgtype'] = 'text';
                $sendData['text'] = ['content' => $datas['config']['content']];
                break;
            // 回复图片消息
            case '2':
                $sendData['msgtype'] = 'image';
                $sendData['image'] = ['media_id' => $datas['config']['media_id']];
                break;
            // 回复图文消息
            case '3':
                // 查询图文素材信息
                $materialDatas = $this->getMaterial($datas['config']);
                $sendData['msgtype'] = 'news';
                $articles = [];
                foreach ($materialDatas as $materialData) {
                    $articles[] = [
                        'title' => $materialData['Title'],
                        'description' => $materialData['Description'],
                        'url' => $materialData['Url'],
                        'picurl' => $materialData['PicUrl']
                    ];
                }
                $sendData['news'] = [
                    'articles' => $articles
                ];
                break;
            // 回复语音消息
            case '4':
                !isset($datas['config']['voice']['media_id']) && $this->fail();
                $sendData['msgtype'] = 'voice';
                $sendData['voice'] = ['media_id' => $datas['config']['voice']['media_id']];
                break;
            // 回复音乐消息
            case '5':
                if (!isset($datas['config']['music']) || count($datas['config']['music']) != 6) {
                    $this->fail();
                }
                $sendData['msgtype'] = 'music';
                $sendData['music'] = [
                    'title' => $datas['config']['music']['title'],
                    'description' => $datas['config']['music']['desc'],
                    'musicurl' => $datas['config']['music']['normal'],
                    'hqmusicurl' => $datas['config']['music']['hd'],
                    'thumb_media_id' => $datas['config']['music']['media_id']
                ];
                break;
            // 回复其他消息
            case '6':
                $otherDatas = $this->getOther($datas['config']);
                if (empty($otherDatas)) {
                    return;
                }
                $sendData['msgtype'] = 'news';
                $sendData['news'] = [
                    'articles' => [
                        [
                            'title' => $otherDatas['Title'],
                            'description' => $otherDatas['Description'],
                            'url' => $otherDatas['Url'],
                            'picurl' => $otherDatas['PicUrl']
                        ]
                    ]
                ];
                break;
            default:
                return false;
                break;

        }
        $re = (new CustomService($this->wid))->sendMsg($sendData);
        \Log::info($re);

    }
    // end


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180206
     * @desc 获取定制关注推送信息
     */
    public function getSpecialMsg()
    {
        $userInfo = $this->getUserInfo();
        $this->setNexus($userInfo, 1);
        $eventKey = explode('_', $this->msg['EventKey']);
        if (isset($eventKey[1]) && $eventKey[1] == 'meeting') {
            return $this->couseContent($userInfo);
        } else {
            //return $this->minAppContent($userInfo);
        }


    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @param $userInfo
     * @return string
     * @update 张永辉 2018年6月27日 添加一键拼团免费领取总裁班课程连接
     */
    public function minAppContent($userInfo)
    {
        if (isset($userInfo['unionid'])) {
            $isGet = (new LiShareEventModule())->isGetMinApp($this->wid, $userInfo['unionid']);
        } else {
            $isGet = false;
        }

        if ($isGet) {
            $content =
                <<<EOF
感谢您的关注，\n 
更多:\n
<a href="http://t.cn/RmgOLmp">☞ 如何注册小程序</a>\n
<a href="http://t.cn/Rmg0kT5">☞ 如何搭建小程序</a>\n
<a href="https://hsxy.huisou.cn/sxyback/html/index.html">☞ 小程序运营知识</a>\n
点击链接即可获得

EOF;
            $content = sprintf($content, $this->wid, $this->wid);
        } else {
            $content =
                <<<EOF
感谢您的关注，\n 
更多:\n
<a href="http://t.cn/RmgOLmp">☞ 如何注册小程序</a>\n
<a href="http://t.cn/Rmg0kT5">☞ 如何搭建小程序</a>\n
<a href="https://hsxy.huisou.cn/sxyback/html/index.html">☞ 小程序运营知识</a>\n
点击链接即可获得
EOF;
            $content = sprintf($content, $this->wid);
        }
        return $content;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @param $userInfo
     * @return string
     */
    public function couseContent($userInfo)
    {
        if (isset($userInfo['unionid'])) {
            $isGet = (new LiShareEventModule())->isGetCourse($userInfo['unionid']);
        } else {
            $isGet = false;
        }

        if ($isGet) {
            $content =
                <<<EOF
恭喜您！已成功报名价值12800元的3天3夜移动互联网实战总裁班课程。届时请准时参加！\n
<a href="https://www.huisou.cn/shop/freeXCX/apply/%s">☞ 小程序免费领</a>\n
<a href="https://v.qq.com/x/page/l0564nugj7u.html">☞ 如何注册小程序</a>\n
<a href="https://v.qq.com/x/page/k056418bwas.html">☞ 如何搭建小程序</a>\n
<a href="https://www.huisou.cn/shop/kf/index?wid=%s">☞ 在线客服</a>\n
EOF;
            $content = sprintf($content, $this->wid, $this->wid);
        } else {
            $content =
                <<<EOF
欢迎关注会搜商业智慧官方平台！\n
<a href="https://hsxy.huisou.cn/sxyback/html/course.html?navIndex=1">☞ 课程报名</a>\n
<a href="https://www.huisou.cn/shop/kf/index?wid=%s">☞ 在线客服</a>\n
EOF;
            $content = sprintf($content, $this->wid);
        }
        return $content;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180206
     * @desc 获取用户基础信息
     */
    public function getUserInfo()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
        $accessToken = (new ApiService())->getAccessToken($this->wid);
        \Log::info('获取参数accessToken=' . $accessToken);
        $url = sprintf($url, $accessToken, $this->msg['FromUserName']);
        $userInfo = jsonCurl($url);
        if (isset($userInfo['errcode'])) {
            \Log::info(__FILE__ . '的' . __LINE__ . '行');
            \Log::info($userInfo);
            return false;
        } else {
            \Log::info($userInfo);
            $this->setRedis($userInfo);
            return $userInfo;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180208
     * @desc 设置微信openID
     */
    public function setRedis($userInfo)
    {
        if (isset($userInfo['unionid']) && $userInfo['unionid']) {
            $redisClient = (new RedisClient())->getRedisClient();
            $key = (new LiShareEventModule())->getKey($userInfo['unionid']);
            $redisClient->SET($key, $userInfo['openid']);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180307
     * @desc 设置推荐关系
     */
    public function setNexus($userInfo, $type)
    {
        $meetingNexusService = new MeetingNexusService();
        if ($type == 1) {
            $eventKey = explode('_', $this->msg['EventKey']);
            \Log::info('特殊关注信息');
            \Log::info($eventKey);
            if (isset($eventKey[1]) && $eventKey[1] == 'meeting' && isset($eventKey[2]) && $eventKey[2]) {
                if ($meetingNexusService->getRowByOpenId($userInfo['openid'])) {
                    return false;
                }
                $data = [
                    'mid' => $eventKey[2],
                    'openid' => $userInfo['openid'],
                    'unionid' => $userInfo['unionid'] ?? '',
                ];
                (new ActivityRegisterService())->increment(['mid' => $eventKey[2]], 'num', 1);
                $meetingNexusService->add($data);
            }
        } else {
            $eventKey = explode('_', $this->msg['EventKey']);
            \Log::info('特殊关注信息');
            \Log::info($eventKey);
            if ($meetingNexusService->getRowByOpenId($userInfo['openid'])) {
                return false;
            }
            if (isset($eventKey[0]) && $eventKey[0] == 'meeting' && $eventKey[1]) {
                $data = [
                    'mid' => $eventKey[1],
                    'openid' => $userInfo['openid'],
                    'unionid' => $userInfo['unionid'] ?? '',
                ];
                (new ActivityRegisterService())->increment(['mid' => $eventKey[1]], 'num', 1);
                $meetingNexusService->add($data);
            }
        }

    }


    /**
     * 获取店铺展示推送信息
     * @author 张永辉 2018年9月30日
     */
    public function getExhibitionMsg()
    {
        $content =
            <<<EOF
您好，欢迎关注会搜云，我们提供微信营销一站式服务。内容包括，APP定制、小程序定制、微商城开发、移动互联网实战培训。\n
1.助力计划，创业项目低成本展开微信营销，数量有限。\n                                   <a href="https://www.huisou.cn/shop/index/3714">-点击了解</a>\n
2.推广员招募，单笔保底佣金可达25%，店主、渠道伙伴可参与。\n                                   <a href="https://www.huisou.cn/shop/distribute/apply/3714/4">-我要申请</a>\n
添加官方合作对接微信：sanjie57
EOF;

        return $content;
    }


}
