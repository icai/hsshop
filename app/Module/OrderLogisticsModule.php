<?php
/**
 * 订单打单模块
 * @author 何书哲 2018年6月27日
 */

namespace App\Module;

use App\Model\WeixinAddress;
use App\S\Order\OrderLogisticsService;
use App\Services\Order\OrderRefundService;
use App\Services\WeixinService;
use App\S\Foundation\RegionService;
use OrderService;
use OrderDetailService;
use App\S\Weixin\ShopService;

class OrderLogisticsModule {
    /**
     * 快递100订单导入生成签名
     * @param $kuaidi_app_key 快递100订单导入app_key
     * @param $kuaidi_app_secret 快递100订单导入app_secret
     * @param $appuid 快递100的用户名
     * @param $timestamp 时间戳，自1970年1月1日至现在的毫秒数
     * @return string 签名
     * @create 何书哲 2018年6月27日
     */
    public function sign($kuaidi_app_key, $kuaidi_app_secret, $appuid, $timestamp) {
        $str = strtoupper(md5($kuaidi_app_key.strval($timestamp).$appuid));
        return strtoupper(md5($kuaidi_app_secret.$str));
    }

    /**
     * 快速打印跳转
     * @param $wid 店铺id
     * @param $orderIds 需要打印的订单id数组（例：[12345,23456,34567]）
     * @return array
     * @create 何书哲 2018年6月27日 快速打印跳转
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function fastPrint($wid, $orderIds) {
        $result = ['status'=>0, 'message'=>'', 'data'=>[]];
        //获取店铺快递100配置参数
        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        $timestamp = time()*1000;
        $sign = $this->sign($shopData['kuaidi_app_id'], $shopData['kuaidi_app_secret'], $shopData['kuaidi_app_uid'], $timestamp);
        $printlist = implode(',', $orderIds);
        //快速打印跳转url
        $url = "http://b.kuaidi100.com/v5/open/api/print?appid={$shopData['kuaidi_app_id']}&appuid={$shopData['kuaidi_app_uid']}&sign={$sign}&timestamp={$timestamp}&printlist={$printlist}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//不需要页面内容
        curl_setopt($ch, CURLOPT_NOBODY, 1);//不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回最后的Location
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($ch);
        $effective_url = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        $result['data']['url'] = $effective_url;
        return $result;
    }

    /**
     * curl订单导入
     * @param $url url链接
     * @param array $datas 内容数组
     * @return array
     * @create 何书哲 2018年6月27日 curl订单导入
     */
    public function expressJsonCurlData($url, $datas = [])
    {
        $postUrl = $url;
        $postData =$datas;
        $postData = http_build_query($postData);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        curl_setopt($curl, CURLOPT_USERAGENT,'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * 订单打单发送至快递管家
     * @param $wid 店铺id
     * @param $oid 订单id
     * @return array
     * @create 何书哲 2018年6月27日 订单打单发送至快递管家
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function orderSend($wid, $oid) {
        $result = ['status'=>0, 'message'=>''];
        //获取店铺数据
        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        //获取订单数据
        $orderData = OrderService::init()->where(['id'=>$oid])->getInfo($oid);
        //获取发货地址
        $weixinAddress = $this->getSendLocation($wid);
        //拼接数组
        $timestamp = time()*1000;
        $sendData = [
            'recMobile' => $orderData['address_phone'],  //收货人电话
            'recTel'    => '',                           //收件人固话
            'recName'   => $orderData['address_name'],   //收件人姓名
            'recAddr'   => $orderData['address_detail'], //收件人详细地址
            'sendMobile'=> $weixinAddress['mobile'],     //寄件人电话
            'sendTel'   => '',                           //寄件人固话
            'sendName'  => $weixinAddress['name'],       //寄件人姓名
            'sendAddr'  => $weixinAddress['province_id'].$weixinAddress['city_id'].$weixinAddress['area_id'].$weixinAddress['address'],
                                                         //寄件人详细地址
            'orderNum'  => $orderData['id'],             //贵方订单的唯一标识
        ];
        $sendArr = [
            'appid'     => $shopData['kuaidi_app_id'],
            'appuid'    => $shopData['kuaidi_app_uid'],
            'timestamp' => $timestamp,
            'sign'      => $this->sign($shopData['kuaidi_app_id'], $shopData['kuaidi_app_secret'], $shopData['kuaidi_app_uid'], $timestamp),
            'data'      => json_encode($sendData)
        ];
        //curl请求导入订单
        $curlRes = $this->expressJsonCurlData('http://b.kuaidi100.com/v5/open/api/send', $sendArr);
        if ($curlRes['status'] != 200) {
            $result['status'] = $curlRes['status'];
            $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'导入快递管家失败,'.$curlRes['message'];
            return $result;
        }
        //这儿不进行订单是否导入状态更改，在回调的时候才进行状态更改
        $result['status'] = $curlRes['status'];
        $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'导入快递管家成功';
        return $result;
    }

    /**
     * 获取发货商家地址（发货地址）
     * @param $wid 店铺id
     * @return array 商家店铺数组
     * @create 何书哲 2018年6月27日 获取发货商家地址（发货地址）
     */
    public function getSendLocation($wid) {
        $address = WeixinAddress::where(['wid'=>$wid,'is_send_default'=>1])->orWhere(['wid'=>$wid,'is_send_default'=>0,'type'=>2])->orderBy('is_send_default', 'DESC')->first();
        if ($address) {
            $address = $address->toArray();
            $temp = [$address['province_id'], $address['city_id'], $address['area_id']];
            $region = (new RegionService())->getListById($temp);
            $address['province_id'] = isset($region[$address['province_id']]) ? $region[$address['province_id']]['title'] : '';
            $address['city_id'] = isset($region[$address['city_id']]) ? $region[$address['city_id']]['title'] : '';
            $address['area_id'] = isset($region[$address['area_id']]) ? $region[$address['area_id']]['title'] : '';
            return $address;
        }
        return [];
    }

    /**
     * 检测订单是否可以订单打单导入
     * @param $oids 订单id数组
     * @param $type 订单导入场景 0:付款成功回调 1:订单列表导入
     * @return array
     * @create 何书哲 2018年6月27日 检测订单是否可以订单打单导入
     * @update 何书哲 2018年11月16日 卡密订单不能导入快递管家
     */
    public function checkOrderIfSend($oids, $type=0) {
        $result = ['status'=>0, 'message'=>''];
        foreach ($oids as $oid) {
            $orderData = OrderService::init()->where(['id'=>$oid])->getInfo($oid);
            //如果订单不存在
            if (!$orderData) {
                $result['status'] = -1;
                $result['message'] = '快速打单 订单id:'.$oid.'不存在，无法导入快递管家';
                return $result;
            }
            //如果是自提订单则不导入
            if ($orderData['is_hexiao']) {
                $result['status'] = -2;
                $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'是自提订单，无法导入快递管家';
                return $result;
            }
            //如果是卡密订单则不导入
            if ($orderData['type'] == 12) {
                $result['status'] = -10;
                $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'是卡密订单，无法导入快递管家';
                return $result;
            }
            //如果是订单列表导入 && 已导入订单
            if ($type == 1 && $orderData['is_import'] == 1) {
                $result['status'] = -8;
                $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'已导入过快递管家，不能重复导入';
                return $result;
            }
            if ($orderData['status'] != 1 || !in_array($orderData['groups_status'], [0,2])) {
                $result['status'] = -9;
                $result['message'] = '快速打单 订单编号:'.$orderData['oid'].'不满足待发货条件，无法导入快递管家';
                return $result;
            }
        }
        return $result;
    }

    /**
     * 检测店铺是否可以订单打单导入
     * @param $wid 店铺id
     * @return array
     * @create 何书哲 2018年6月27日 检测店铺是否可以订单打单导入
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年11月16日 外卖店铺不能导入快递管家
     */
    public function checkShopIfSend($wid) {
        $result = ['status'=>0, 'message'=>''];
        //获取店铺信息
        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        if (empty($shopData)) {
            $result['status'] = -3;
            $result['message'] = '快速打单 店铺id:'.$wid.'无法导入快递管家，因为店铺不存在';
            return $result;
        }
        //如果是外卖店铺
        if ((new StoreModule())->getWidTakeAway($wid)) {
            $result['status'] = -11;
            $result['message'] = '快速打单 店铺id:'.$wid.'无法导入快递管家，因为是外卖店铺';
            return $result;
        }
        //店铺是否配置订单打单参数
        $flag = in_array($shopData['print_type'], [1,2]) && $shopData['kuaidi_app_id'] && $shopData['kuaidi_app_secret'] && $shopData['kuaidi_app_uid'];
        if (!$flag) {
            $result['status'] = -4;
            $result['message'] = '快速打单 店铺<'.$shopData['shop_name'].'>无法导入快递管家，因为订单打单导入参数未设置';
            return $result;
        }
        //商家发货地址是否存在
        if (!$weixinAddress = $this->getSendLocation($wid)) {
            $result['status'] = -5;
            $result['message'] = '快速打单 店铺<'.$shopData['shop_name'].'>无法导入快递管家，因为商家发货地址不存在';
        }
        return $result;
    }

    /**
     * 检测订单id列表是否可以打印快递单
     * @param $orderIds 订单id数组
     * @return array
     * @create 何书哲 2018年6月28日 检测订单id列表是否可以打印快递单
     */
    public function checkOrderIdsIfPrint($orderIds) {
        $result = ['status'=>0, 'message'=>'', 'data'=>['flag'=>0]];
        foreach ($orderIds as $key => $val) {
            $orderData = OrderService::init()->where(['id'=>$val])->getInfo($val);
            //订单未导入
            if ($orderData['is_import'] == 0) {
                $result['status'] = -8;
                $result['message'] = '订单编号:'.$orderData['oid'].'未导入过快递管家，不能打单';
                return $result;
            }
            //如果订单已关闭
            if ($orderData['status'] == 4) {
                $result['status'] = -12;
                $result['message'] = '订单编号:'.$orderData['oid'].'已关闭，不能打单';
                return $result;
            }
            //已退款到账，遍历订单下的所有detail
            $orderDetail = OrderDetailService::init()->model->where('oid',$val)->get(['product_id','product_prop_id'])->toArray();
            $is_refund = 1;
            foreach ($orderDetail as $detail) {
                $refundData = (new OrderRefundService())->init('oid', $val)->where(['oid' => $val, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
                if (!$refundData || $refundData && $refundData['status'] != 8) {
                    $is_refund = 0;
                    break;
                }
            }
            //已全部退款完成
            if ($is_refund) {
                $result['status'] = -15;
                $result['message'] = '订单编号:'.$orderData['oid'].'已退款完成，不能打单';
                return $result;
            }
            //已完成
            if ($orderData['status'] == 3) {
                $result['status'] = -16;
                $result['message'] = '订单编号:'.$orderData['oid'].'已完成，不能打单';
                return $result;
            }
            //如果订单是退款中 除去refund_status为0,2,5,8,9的其他状态
            if (!in_array($orderData['refund_status'], [0,2,5,8,9])) {
                $result['data']['flag'] = 1;
            }
        }
        return $result;
    }

    /**
     * 根据条件获取某一字段去重数组
     * @param array $where 条件数组
     * @param string $field 字段名称
     * @return array 去重字段数组
     * @create 何书哲 2018年6月29日 根据条件获取某一字段去重数组
     */
    public function getFieldByWhere($where=[], $field='') {
        $orderLogisticsList = (new OrderLogisticsService())->model->where($where)->pluck($field)->toArray(); 
        return array_unique($orderLogisticsList);
    }

}