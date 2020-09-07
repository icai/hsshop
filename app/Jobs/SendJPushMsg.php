<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/3
 * Time: 14:13
 */

namespace App\Jobs;

use App\Services\Order\OrderService;
use App\Services\WeixinService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Module\JPushModule;
use App\S\JPushService;
use Illuminate\Support\Facades\Log;
use App\S\Weixin\ShopService;

class SendJPushMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $business_type;   //0: 系统通知 1：买家发起退款 2：新订单提醒 3:买家已退货申请 4：退款临近超时提醒
    private $jpush_type;      //推送类型 Notification Message Rich Local
    private $alias;           //别名数组
    private $wid;             //店铺id
    private $relation_id;     //相关id（订单id、活动id）

    public $tries = 3;
    public $timeout = 60;

    /**
     * SendJPushMsg constructor.
     * @param $business_type 0: 系统通知 1：买家发起退款 2：新订单提醒 3:买家已退货申请 4：退款临近超时提醒
     * @param string $jpush_type 推送类型 Notification Message Rich Local
     * @param $alias 【ds_user.id数组 别名用于极光推送，例如:[66,88]】
     * @param $wid 店铺id
     * @param $relation_id 相关id（订单id、活动id），系统通知为0
     */
    public function __construct($business_type, $jpush_type=JPushModule::JPUSH_NOTIFICATION, $alias, $wid, $relation_id)
    {
        $this->business_type = $business_type;
        $this->jpush_type = $jpush_type;
        $this->alias = $alias;
        $this->wid = $wid;
        $this->relation_id = $relation_id;
    }

    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        if ($this->alias) {
            $result = config('sellerapp.jpush_msg');
            $jpush_msg = $result[$this->business_type];
            if (in_array($this->business_type, [1,2,3,4])) {
                $jpush_msg = $this->_getOrderMsg($jpush_msg);
            }
            //获取订单数据
            $response = (new JPushModule())->push($jpush_msg, $this->jpush_type, $this->alias);
            if ($response) {
                if ($response['http_code'] == 200) {
                    //推送成功添加到数据表
                    $data = [
                        'wid'     => $this->wid,
                        'type'    => $this->business_type,
                        'content' => $jpush_msg,
                        'relation_id' => $this->relation_id
                    ];
                    $jpush_id = (new JPushService())->add($data);
                    \Log::info('推送成功 jpush_id：'.$jpush_id);
                    \Log::info('推送信息 '.$jpush_msg);
                } else {
                    \Log::info('推送失败');
                    \Log::info('失败原因 '.$response['error']['message'].'('.$response['error']['code'].')');
                }
            } else {
                \Log::info('接口调用失败或者无响应');
            }
        }
    }

    private function _getOrderMsg($jpush_msg)
    {
        $res = (new OrderService())->init('wid', $this->wid)->getOrderInfo($this->relation_id);
        if ($res['success'] ==1) {
            //$res['data']['weixin'] = (new WeixinService())->init()->model->where(['id'=>$this->wid])->first()->toArray();
            $shopService = new ShopService();
            $res['data']['weixin'] = $shopService->getRowById($this->wid);
            $oid = $res['data']['oid']??'';
            $pay_price = $res['data']['pay_price']??0;
            $shop_name = isset($res['data']['weixin']) && isset($res['data']['weixin']['shop_name']) ? $res['data']['weixin']['shop_name'] : '';
            return sprintf($jpush_msg, $oid, $pay_price, $shop_name);
        } else {
            return sprintf($jpush_msg, '', 0, '');
        }
    }

}