<?php


namespace App\Module;

use App\S\MarketTools\MessagesPushService;
use App\S\Weixin\DeliveryPrinterService;
use App\S\Weixin\ShopService;
use App\Services\Order\LogisticsService;
use OrderDetailService;
use OrderService;
use OrderLogService;
use DB;


/**
 * 外卖Module
 * Class TakeAwayModule
 * @package App\Module
 * @author 何书哲 2018年11月15日
 */

class TakeAwayModule {

    const AddOrderUrl = 'http://open.printcenter.cn:8080/addOrder';
    const QueryOrderUrl = 'http://open.printcenter.cn:8080/queryOrder';
    const QueryPrinterStatusUrl = 'http://open.printcenter.cn:8080/queryPrinterStatus';

    protected $wid;
    protected $device_no;
    protected $key;
    protected $times;

    public function __construct($wid) {
        $this->wid = $wid;
        $printerData = (new DeliveryPrinterService())->getRowByWhere(['wid'=>$wid, 'is_on'=>1]);
        if ($printerData) {
            $this->device_no = $printerData['device_no'];
            $this->key = $printerData['key'];
            $this->times = $printerData['times'];
        }
    }

    /**
     * 外卖订单导入第三方（获取小票机打印内容、发送到第三方、订单发货成功后续操作）
     * @param array $datas 订单列表数据
     */
    public function addOrder($datas=[]) {
        foreach ($datas as $data) {
            //获取小票机打印内容
            $content = $this->getContent($data);
            //调用接口发送到第三方
            $result = $this->sendSelfFormatOrderInfo($content);
            //订单发货成功后续操作
            $this->dealOrder($data, $result);
        }
    }

    /**
     * 查询订单状态
     * @param $orderIndex
     * @return json
     * ----------S2小票机返回的结果有如下几种：----------
     *{"responseCode":0,"msg":"已打印"}
     *{"responseCode":0,"msg":"未打印"}
     *{"responseCode":1,"msg":"请求参数错误"}
     *{"responseCode":2,"msg":"没有找到该索引的订单"}
     *{"responseCode":4,"msg":"错误的机器号或口令"}
     */
    public function queryOrder($orderIndex) {
        //11648306721542016920150
        $selfMessage = [
            'deviceNo' => $this->device_no,
            'key' => $this->key,
            'orderindex' => $orderIndex
        ];
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded ",
                'method'  => 'POST',
                'content' => http_build_query($selfMessage),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents(self::QueryOrderUrl, false, $context);
        return $result;
    }

    /**
     * 查询打印机状态
     * ----------S2小票机返回的结果有如下几种：----------
     *{"responseCode":0,"msg":"离线"}
     *{"responseCode":0,"msg":"在线,工作状态正常"}
     *{"responseCode":0,"msg":"在线,工作状态不正常"}
     *{"responseCode":1,"msg":"请求参数错误"}
     *{"responseCode":4,"msg":"错误的机器号或口令"}
     */
    public function queryPrinterStatus() {
        $selfMessage = [
            'deviceNo' => $this->device_no,
            'key' => $this->key,
        ];
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded ",
                'method'  => 'POST',
                'content' => http_build_query($selfMessage),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents(self::QueryPrinterStatusUrl, false, $context);
        return $result;
    }

    /**
     * 获取小票机打印内容
     * @param array $data 订单信息
     * @return string
     * @author 何书哲 2018年11月16日
     */
    private function getContent($data=[]) {
        $shopData = (new ShopService())->getRowById($this->wid);
        header("Content-Type: text/html;charset=utf-8");
        //拼接小票打印内容
        $content = '';
        $content .= "<CB>".$shopData['shop_name']."</CB><BR>";
        $content .= "名称　　　　　      数量  小计<BR>";
        $content .= "--------------------------------<BR>";
        foreach ($data['orderDetail'] as $detail) {
            $content .= $this->msubstr($detail['title'], 0,20).sprintf('%4d', $detail['num']).' '.sprintf('%6.2f', $detail['price']*$detail['num'])."<BR>";
        }
        $content .= '备注：'.($data['buy_remark']?:'无')."<BR>";
        $content .= "--------------------------------<BR>";
        $data['discount_amount'] > 0 && $content .= '满减金额：'.$data['discount_amount']."<BR>";
        $data['coupon_price'] > 0 && $content .= '优惠金额：'.$data['coupon_price']."<BR>";
        $data['bonus_point_amount'] > 0 && $content .= '积分抵现：'.$data['bonus_point_amount']."<BR>";
        $content .= '合计金额：'.$data['pay_price'].' (含运费: '.$data['freight_price'].')'."<BR>";
        $content .= '顾客姓名：'.$data['address_name']."<BR>";
        $content .= '送餐地点：'.$data['address_detail']."<BR>";
        $content .= '联系电话：'.$data['address_phone']."<BR>";
        $content .= '订餐时间：'.$data['created_at']."<BR>";
        return $content;
    }

    /**
     * 调用第三方接口发送外卖订单信息
     * @param $orderInfo 订单信息
     * @return bool|string
     * @author 何书哲 2018年11月16日
     * ----------S2小票机返回的结果有如下几种：----------
     *{"responseCode":0,"msg":"服务器接收订单成功","orderindex":"xxxxxxxxxxxxxxxxxx"}
     *{"responseCode":1,"msg":"打印机编号错误"}
     *{"responseCode":2,"msg":"服务器处理订单失败"}
     *{"responseCode":3,"msg":"打印内容太长"}
     *{"responseCode":4,"msg":"请求参数错误"}
     *{"responseCode":4,"msg":"错误的机器号或口令"}
     */
    private function sendSelfFormatOrderInfo($orderInfo) {
        $selfMessage = [
            'deviceNo'     => $this->device_no,
            'printContent' => $orderInfo,
            'key'          => $this->key,
            'times'        => $this->times
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded ",
                'method'  => 'POST',
                'content' => http_build_query($selfMessage),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents(self::AddOrderUrl, false, $context);
        return $result;
    }

    /**
     * 截取一定长度字符串，长度不够以空补够
     * @param $str 字符串
     * @param int $start 开始位置
     * @param $len 长度
     * @return string
     * @author 何书哲 2018年11月16日
     */
    private function msubstr($str, $start=0, $len) {
        $str = iconv('utf-8', 'gb2312', $str);
        $tmpstr = "";
        $strlen = $start + $len;
        for($i = 0; $i < $strlen; $i++) {
            if(ord(substr($str, $i, 1)) > 0xa0) {
                if ($i+1 == $strlen) {
                    continue;
                }
                $tmpstr .= substr($str, $i, 2);
                $i++;
            } else {
                $tmpstr .= substr($str, $i, 1);
            }
        }
        $tmpstr = str_pad($tmpstr, $len);
        $return = iconv('gb2312', 'utf-8', $tmpstr);
        return $return;
    }

    /**
     * 处理外卖订单发货后续操作
     * @param array $data 订单数据
     * @param $result 外卖订单导入第三方结果数据
     * @return bool
     * @author 何书哲 2018年11月16日
     */
    private function dealOrder($data=[], $result) {
        \Log::info('[外卖导入结果] 店铺id:'.$this->wid.' '.$result);
        $result = json_decode($result, true);
        //服务器接受订单成功
        if ($result['responseCode'] == 0 && $result['orderindex']) {
            //完成订单自动发货流程
            try {
                DB::beginTransaction();

                //添加物流记录
                $odid = array_column($data['orderDetail'], 'id');
                $logistics = [
                    'oid'                => $data['id'],
                    'odid'               => implode(',', $odid),
                    'no_express'         => 1,
                ];
                if ((new LogisticsService())->init()->add($logistics, false)) {
                    //更新订单详情状态
                    foreach ($odid as $detailId) {
                        OrderDetailService::init()->where(['id'=>$detailId])->update(['id'=>$detailId, 'is_delivery'=>1, 'delivery_time'=>time()], false);
                    }
                }
                //更新订单状态
                OrderService::init('wid', $this->wid)->where(['id'=>$data['id']])->update(['status'=>2, 'order_index'=>$result['orderindex']], false);
                //添加订单发货操作记录
                $orderLog = [
                    'oid'       => $data['id'],
                    'wid'       => $this->wid,
                    'mid'       => $data['mid'],
                    'action'    => 3,
                    'remark'    => '商家发货',
                ];
                OrderLogService::init()->add($orderLog, false);
                OrderService::upOrderLog($data['id'], $this->wid);

                DB::commit();

                //发送发货通知
                $data['source'] == 0 && (new MessagePushModule($this->wid, MessagesPushService::DeliverySuccess))->sendMsg(['oid'=>$data['id'], 'odid'=>implode(',', $odid)]);
                $data['source'] == 1 && (new MessagePushModule($this->wid, MessagesPushService::DeliverySuccess,MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['oid'=>$data['id'],'odid'=>implode(',', $odid)]);

            } catch (\Exception $e) {
                DB::rollBack();
                return false;
            }
        }
    }





}