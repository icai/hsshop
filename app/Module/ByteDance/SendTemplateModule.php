<?php
/**
 * Created by zhangyh.
 * User: 张永辉 [zhangyh_private@foxmail.com]
 * Date: 2019/10/10 9:50
 */

namespace App\Module\ByteDance;


use App\Model\ByteDanceTemplate;
use App\Model\Member;
use App\Model\Order;
use App\S\WXXCX\WXXCXCollectFormIdService;

class SendTemplateModule
{

    /**
     * @var  int
     *
     * @desc 支付消息通知类型
     */
    const PAY_ORDER = 1;

    /**
     * @VAR STRING
     *
     * @DESC 发送链接
     */
    const SEND_URL = 'https://developer.toutiao.com/api/apps/game/template/send';

    /**
     * @desc 消息类型
     *
     * @var int
     */
    public $type;

    /**
     * @desc 消息内容
     *
     * @var array
     */
    public $data;

    /**
     * @desc 企业id
     *
     * @var int
     */
    public $wid;

    public $member;

    /**
     * @desc 发送字节跳动模板消息
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 10 月 10 日
     */
    public function send()
    {
        switch ($this->type) {
            case self::PAY_ORDER :
                $this->payOrderSendTempate();
                break;
            default:

        }

    }

    /**
     * @desc 订单支付发送消息模板
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 10 月 10 日
     */
    public function payOrderSendTempate()
    {
        $orderData = Order::with(['orderDetail'])->find($this->data['oid'] ?? '');
        if (!$orderData) {
            \Log::info(__FILE__ . __LINE__ . '订单不存在');
            return false;
        }
        $this->wid = $orderData->wid;
        $this->member = Member::select(['id', 'byte_openid'])->find($orderData->mid);
        if (empty($this->member->byte_openid)) {
            \Log::info(__FILE__ . __LINE__ . '用户不存在或用户不属于字节跳动用户');
            return false;
        }
        try {
            $data = $this->getBaseData();
        } catch (\Exception $exception) {
            \Log::info(__FILE__ . __LINE__ . $exception->getMessage());
            return false;
        }
        $data['page'] = 'pages/main/pages/order/orderDetail/orderDetail';
        $data['data']['keyword1']['value'] = $orderData->created_at->toDateTimeString();
        $data['data']['keyword2']['value'] = substr($orderData->orderDetail->first()->title, 0, 6) . '...';
        $data['data']['keyword3']['value'] = strval($orderData->oid);
        $data['data']['keyword4']['value'] = $orderData->pay_price;
        $res = self::post(self::SEND_URL, $data);
        if (isset($res['errcode']) && $res['errcode'] == 0) {
            return true;
        } else {
            \Log::info($res);
            return false;
        }
    }


    /**
     * @desc 发送请求
     * @param $url 发送链接
     * @param $data 发送数据
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 10 月 10 日
     */
    public static function post($url, $data)
    {
        $headers = array(
            "Content-type: application/json"
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if (0 === strpos(strtolower($url), 'https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $rtn = curl_exec($ch);
        curl_close($ch);
        return json_decode($rtn, true);
    }

    /**
     * @desc 获取发送模板基础数据
     * @return array 基础数据
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 10 月 10 日
     */
    public function getBaseData()
    {
        $data['access_token'] = BaseModule::getAccessToken($this->wid);
        $data['touser'] = $this->member->byte_openid;
        $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($this->member->id) ?: 'dads';
        $template = ByteDanceTemplate::where('wid', $this->wid)->where('msg_type', $this->type)->first();
        if (!$template) {
            throw new \Exception('企业没有配置模板');
        }
        $data['template_id'] = $template->template_id;
        return $data;
    }


    /**
     * @desc 设置数据
     * @param array $data 消息数据内容
     * @return $this 本服务类
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 10 月 10 日
     */
    public function setData(array $data)
    {
        $this->data = $data['param'] ?? [];
        $this->type = $data['type'] ?? 0;
        return $this;
    }


}