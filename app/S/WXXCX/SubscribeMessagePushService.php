<?php

namespace App\S\WXXCX;

use App\Jobs\AddSubTemplateJob;
use App\Lib\BLogger;
use App\Model\Product;
use CurlBuilder;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\WXXCXConfig;
use Illuminate\Support\Facades\Cache;
use App\S\S;

/**
 * Created by PhpStorm.
 * User: Wuxiaoping
 * Date: 2019/12/18
 * Time: 15:32
 * Description: 订阅消息模板保存，发送 servie类
 */
class SubscribeMessagePushService extends S
{
    /**
     * @var 企业id
     */
    private $wid;

    /**
     * @var 企业小程序appId
     */
    private $appId;

    /**
     * @var 小程序跳转的页面路径
     */
    private $page;

    /**
     * @var array 订阅消息模板标题id
     */
    private $shortTempId = [
        ['tid' => 3519, 'scene' => '会员卡开通提醒', 'kids' => [1, 2, 3]],
        ['tid' => 4773, 'scene' => '团购结果通知', 'kids' => [1, 2, 3, 4]],
        ['tid' => 818, 'scene' => '审核结果通知', 'kids' => [4, 1, 2, 3]],
        ['tid' => 1159, 'scene' => '预售结果通知', 'kids' => [1, 3]],
        ['tid' => 310, 'scene' => '积分变更提醒', 'kids' => [1, 2, 3]],
        ['tid' => 1880, 'scene' => '收益到账通知', 'kids' => [4, 3, 5]],
        ['tid' => 855, 'scene' => '订单发货通知', 'kids' => [1, 5, 7, 4, 8]]
    ];

    /**
     *  关联当前模型
     * MessagePushService constructor.
     */
    public function __construct()
    {
        parent::__construct('XcxSubTemplate');
    }

    /**
     * @description：获取对应店铺的token信息
     * @param $wid  店铺id
     * @return bool
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月18日 17:17:22
     */
    private function getConf($wid)
    {
        $xcxConfigData = app(WXXCXConfigService::class)->getRowByIdWid($wid, 0);
        $result = (new ThirdPlatform())->getAuthorizerAccessToken(['wid' => $wid]);
        if ($xcxConfigData['errCode']==0 && !empty($xcxConfigData['data']) && $result['errCode'] == 0) {
            $xcxConfigInfo = $xcxConfigData['data'];
            $conf['app_id'] = $xcxConfigInfo['app_id'];
            $conf['token'] = $result['data'];
            $conf['wid']   = $wid;
            return $conf;
        }
        \Log::info('获取token失败：');
        \Log::info($result);
        return false;
    }

    /**
     * @description： 小程序订阅消息模板入库处理 (跑脚本调用)
     * @param $mid    店铺id (这里只要使用自己店铺的id保存公共模板库信息即可)
     * @param int $tempId  模板标题id
     * @param string $scene 发送模板场景值
     * @param string $kids 模板发送的关键词列表
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月04日 20:47:35
     */
    public function saveSubTemplate($mid, $tempId = 0, $scene = '订阅消息', $kids = '')
    {
        if (empty($mid)) {
            return false;
        }
        if ($tempId && $kids) {
            $data = ['wid' => $mid, 'tid' => $tempId, 'scene' => $scene, 'kids' => $kids];
            $this->getSubTemplate($data, true);
        } else {
            $templateList = $this->getAllTempList($mid);
            $action = 'insert';
            // 已添加过模板的店铺更新
            if (!$templateList->isEmpty()) {
                $action = 'update';
                // 把对应的已存在的模板主键id存入到配置的数组中
                foreach ($templateList as $item) {
                    foreach ($this->shortTempId as $key => &$value) {
                        if ($item['title_id'] == $value['tid']) {
                            $value['tmplId'] = $item['id'];
                        }
                    }
                }
            }

            // 队列处理微信接口添加模板
            foreach ($this->shortTempId as $key => $item) {
                $data = ['wid' => $mid, 'tid' => $item['tid'], 'scene' => $item['scene'], 'kids' => $item['kids'], 'tmplId' => $item['tmplId'] ?? 0];
                dispatch(new AddSubTemplateJob($data, $action));
            }
        }
    }

    /**
     * @description：根据模板标题id获取订阅消息模板
     * @param $appId 小程序授权appid
     * @param $titleId 模板标题id
     * @param $scene  订阅消息模板发送场景
     * @param $kids   对应模板的关键词列表
     * @param bool $flag 是否可直接入库标识，默认false
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 17:11:21
     */
    public function getSubTemplate($data, $flag = false)
    {
        try {
            $conf = $this->getConf($data['wid']);
            if (!$conf) {
                return false;
            }
            $result = $this->addTemplate($conf['token'], $data);
            $saveData = ['wid' => $data['wid'], 'title_id' => $data['tid'], 'template_id' => $result['priTmplId'], 'scene' => $data['scene']];
            // 表示可以插入单条数据
            if ($flag) {
                $this->model->create($saveData);
            }
            return $saveData;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * @description：添加小程序订阅消息模板
     * @param $token  第三方token值
     * @param $priTmplId  要添加小程序订阅模板对应的数组数据
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2020年01月02日 10:49:17
     */
    public function addTemplate($token, $data)
    {
        try {
            $postUrl = 'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=' . $token;
            $sendData = ['tid' => $data['tid'], 'kidList' => $data['kids'], 'sceneDesc' => $data['scene']];
            $result = $this->thirdRequest($postUrl, $sendData, 'post');
            if ($result['errcode']) {
                BLogger::getLogger('error', 'msg_push')->error($data['wid'] . '小程序标题id' . $data['tid'] . '添加订阅消息模板结果：' . json_encode($result));
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * @description：删除小程序订阅消息模板
     * @param $token  第三方token值
     * @param $priTmplId  要删除小程序订阅模板id
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2020年01月02日 10:49:17
     */
    public function deleteTemplate($token, $priTmplId)
    {
        try {
            $postUrl = 'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token=' . $token;
            $sendData = ['priTmplId' => $priTmplId];
            $result = $this->thirdRequest($postUrl, $sendData, 'post');
            if ($result['errcode']) {
                BLogger::getLogger('error', 'msg_push')->error('小程序订阅模板id:' . $priTmplId . ' 删除订阅消息模板结果：' . json_encode($result));
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * @description：更新小程序订阅消息模板id
     * @param $data  添加小程序订阅模板信息数组数据
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2020年01月02日 10:45:44
     */
    public function updatePriTmplId($data)
    {
        try {
            $conf = $this->getConf($data['wid']);
            if (!$conf) {
                return false;
            }
            $result = $this->addTemplate($conf['token'], $data);
            $xcxSub = $this->model->find($data['tmplId']);
            if (empty($xcxSub)) {
                return false;
            }
            // 删除对应的小程序订阅模板消息
            $this->deleteTemplate($conf['token'], $xcxSub->template_id);
            return $xcxSub->update(['template_id' => $result['priTmplId']]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * @description： 发送订阅模板消息
     * @param $wid    店铺id
     * @param $data   发送模板消息的数组数据
     *
     * @return bool
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 19:49:21
     */
    public function messageSend($wid, $data)
    {
        try {
            $conf = $this->getConf($wid);
            if (!$conf) {
                return false;
            }
            $postUrl = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $conf['token'];
            $result = $this->thirdRequest($postUrl, $data, 'post');
            BLogger::getLogger('info', 'msg_push')->info($wid . '小程序发送订阅消息模板结果：' . json_encode($result));
            if ($result['errcode']) {
                return false;
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $url   第三方请求的url
     * @param array $data 第三方请求参数
     * @param string $method 第三方请求的方式（get post）
     * @return mixed
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月19日 11:34:47
     */
    public function thirdRequest($url, $data = [], $method = 'get')
    {
        $result = CurlBuilder::to($url)->asJsonRequest()->withData($data)->$method();
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * @description：获取所有的订阅消息模板
     * @param $mid   店铺id
     *
     * @return mixed
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月06日 15:32:38
     */
    public function getAllTempList($mid)
    {
        return $this->model->where('wid', $mid)->get(['id', 'title_id', 'template_id', 'scene']);
    }

    /**
     * @description：组装要发送模板的数据内容
     * @param $type：发送模板的类型
     * @param $data：发送模板对应的数据参数
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月11日 16:41:07
     */
    public function packageSendData($type, $data)
    {
        $templateList = $this->getAllTempList($data['wid']);
        if ($templateList->isEmpty()) {
            return false;
        }
        $templateData = $templateList->pluck('template_id', 'title_id')->all();
        $sendData = [];
        switch ($type) {
                // 会员卡开通提醒
            case 1:
                $this->setPage('pages/main/pages/member/memberCard/memberCard', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['3519'],
                    "page" => $this->page,
                    "keys" => ['thing1', 'character_string2', 'time3']
                ];
                break;
                // 团购结果通知
            case 2:
                $this->setPage('pages/activity/pages/grouppurchase/orderDetail/orderDetail', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['4773'],
                    "page" => $this->page,
                    "keys" => ['thing1', 'amount2', 'number3', 'thing4']
                ];
                break;
                // 审核结果通知
            case 3:
                $this->setPage('pages/main/pages/member/distribute/distribute/distribute', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['818'],
                    "page" => $this->page,
                    "keys" => ['thing4', 'thing1', 'phrase2', 'time3']
                ];
                break;
                // 预售结果通知
            case 4:
                $this->setPage('pages/cart1/cart1', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['1159'],
                    "page" => $this->page,
                    "keys" => ['thing1', 'time3']
                ];
                break;
                // 积分变更通知
            case 5:
                $this->setPage('pages/main/pages/member/point/mypoint/mypoint', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['310'],
                    "page" => $this->page,
                    "keys" => ['character_string1', 'character_string2', 'thing3']
                ];
                break;
                // 收益到账通知
            case 6:
                $this->setPage('groupModule/pages/groupGoodsDetail/groupGoodsDetail', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['1880'],
                    "page" => $this->page,
                    "keys" => ['amount4', 'time3', 'phrase5']
                ];
                break;
                // 订单发货通知
            case 7:
                $this->setPage('pages/main/pages/order/orderDetail/orderDetail', $data['param']);
                $sendData = [
                    "touser" => $data['openid'],
                    "template_id" => $templateData['855'],
                    "page" => $this->page,
                    "keys" => ['character_string1', 'date5', 'thing7', 'character_string4', 'thing8']
                ];
                break;
            default:
                break;
        }
        return $sendData;
    }

    /**
     * @description：设置订阅消息发送模板后的跳转路径
     * @param $page：原始的路径
     * @param array $param： 路径参数
     *
     * @return $this
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月11日 16:39:52
     */
    public function setPage($page, $param = [])
    {
        $this->page = $page;
        if ($param) {
            $paramStr = http_build_query($param);
            $this->page = $page . '?' . $paramStr;
        }
        return $this->page;
    }
}

