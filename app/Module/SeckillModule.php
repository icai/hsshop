<?php
/**
 * 秒杀活动模块
 */

namespace App\Module;

use App\Jobs\Distribution;
use App\Lib\Redis\RedisClient;
use App\Model\Cart;
use App\Model\Order;
use App\Model\Seckill;
use App\Model\SeckillSku;
use App\S\Market\SeckillService;
use App\S\Market\SeckillSkuService;
use App\Lib\Redis\Seckill as SeckillRedis;
use App\Lib\Redis\SeckillSku as SeckillSkuRedis;
use App\S\MarketTools\MessagesPushService;
use App\S\Product\ProductSkuService;
use App\Services\Shop\CartService;
use Carbon\Carbon;
use ProductService;
use OrderService;
use OrderCommon;
use DB;
use OrderLogService;
use Log;
use MemberCardRecordService;

class SeckillModule
{
    /**
     * 新增一条秒杀活动
     * @param $data array
     * @return int 新增秒杀id
     * @update 许立 2018年08月22日 秒杀库存限制
     * @update 许立 2018年08月27日 秒杀暂时不使用redis队列处理并发
     * @update 何书哲 2018年11月07日 秒杀价格限制改为100万
     */
    public function add($data)
    {
        //判断库存设置
        if (empty($data['skuData'])) {
            error('请设置商品库存');
        }
        $sku = $data['skuData'];
        unset($data['skuData']);

        //新增秒杀活动
        $seckillID = Seckill::insertGetId($data);

        //新增秒杀库存
        $skuModel = new SeckillSku();
        foreach ($sku as $v) {
            if ($v['seckill_price'] >= 1000000) {
                error('秒杀价格不能超过1000000');
            }
            // 秒杀库存限制 临时方案
            $v['seckill_stock'] > 10000 && $v['seckill_stock'] = 10000;
            $skuData = [
                'seckill_id' => $seckillID,
                'sku_id' => $v['sku_id'],
                'seckill_price' => $v['seckill_price'],
                'seckill_stock' => $v['seckill_stock']
            ];
            $skuModel->insertGetId($skuData);
        }

        return $seckillID;
    }

    /**
     * 更新一条秒杀活动
     * @param $data array
     * @return int 新增秒杀id
     * @update 许立 2018年08月22日 秒杀库存限制
     * @update 许立 2018年08月27日 秒杀暂时不使用redis队列处理并发
     * @update 何书哲 2018年11月07日 秒杀价格限制改为100万
     */
    public function update($data)
    {
        //判断库存设置
        if (empty($data['skuData'])) {
            error('请设置商品库存');
        }
        $sku = $data['skuData'];
        unset($data['skuData']);

        //更新秒杀活动
        Seckill::where('id', $data['id'])->update($data);
        (new SeckillRedis)->updateRow($data);

        //删除sku
        $this->deleteSkuBySeckillId($data['id']);

        //新增秒杀库存
        $skuModel = new SeckillSku();
        foreach ($sku as $v) {
            if ($v['seckill_price'] >= 1000000) {
                error('秒杀价格不能超过1000000');
            }
            $v['seckill_stock'] > 10000 && $v['seckill_stock'] = 10000;
            $skuData = [
                'seckill_id' => $data['id'],
                'sku_id' => $v['sku_id'],
                'seckill_price' => $v['seckill_price'],
                'seckill_stock' => $v['seckill_stock']
            ];
            $skuModel->insertGetId($skuData);
        }

        return true;
    }

    /**
     * 根据秒杀ID删除sku
     * @param $seckillId
     */
    private function deleteSkuBySeckillId($seckillId)
    {
        $seckill_sku_service = new SeckillSkuService();
        //获取图片id列表
        $ids = $seckill_sku_service->model->select('id')->where('seckill_id', $seckillId)->pluck('id')->toArray();
        //删除图片列表
        (new SeckillSkuRedis())->deleteArr($ids);
        $seckill_sku_service->model->where('seckill_id', $seckillId)->delete();
    }

    /**
     * 获取秒杀详情
     * @param $id int 秒杀ID
     * @return array
     */
    public function getSeckillDetail($id, $isXCX = false)
    {
        //获取秒杀活动
        $seckill = (new SeckillService())->getDetail($id);
        if (empty($seckill)) {
            $isXCX ? xcxerror('秒杀活动不存在') : error('秒杀活动不存在');
        }

        //获取商品以及秒杀价格和库存
        $product = ProductService::getDetail($seckill['product_id']);
        if (empty($product)) {
            $isXCX ? xcxerror('秒杀商品不存在') : error('秒杀商品不存在');
        }

        $seckillSku = (new SeckillSkuService())->getListBySeckillID($id);
        if (empty($seckillSku)) {
            $isXCX ? xcxerror('秒杀商品价格库存不存在') : error('秒杀商品价格库存不存在');
        }

        //处理秒杀价格和库存区间
        $priceArr = array_column($seckillSku, 'seckill_price');
        $stockArr = array_column($seckillSku, 'seckill_stock');
        $minPrice = min($priceArr);
        $maxPrice = max($priceArr);
        $minStock = min($stockArr);
        $maxStock = max($stockArr);
        $seckill['price_range'] = $minPrice == $maxPrice ? $minPrice : ($minPrice . '~' . $maxPrice);
        $seckill['stock_range'] = $minStock == $maxStock ? $minStock : ($minStock . '~' . $maxStock);

        //秒杀总库存
        $seckill['stock_sum'] = array_sum($stockArr);

        //多规格商品 原价区间
        $seckill['oprice_range'] = $product['price'];
        if ($product['sku_flag']) {
            //商品sku
            $sku = (new ProductSkuService())->getListByProductID($product['id']);

            //参与秒杀的规格
            $skuArr = array_column($seckillSku, 'sku_id');
            $opriceArr = [];
            foreach ($sku as $v) {
                if (in_array($v['id'], $skuArr)) {
                    $opriceArr[] = $v['price'];
                }
            }
            if (empty($opriceArr)) {
                return false;
            }
            //拼接原价区间
            $seckill['oprice_range'] = $product['price'];
            if (!empty($opriceArr)) {
                $minPrice = min($opriceArr);
                $maxPrice = max($opriceArr);
                $seckill['oprice_range'] = $minPrice == $maxPrice ? $minPrice : ($minPrice . '~' . $maxPrice);
            }
        }

        //秒杀活动状态 1:未开时 2:进行中 3:已结束 4:失效
        $now = date('Y-m-d H:i:s');
        $seckill['status'] = 2;
        if ($seckill['invalidate_at'] > '0000-00-00 00:00:00') {
            $seckill['status'] = 4;
        } elseif ($seckill['start_at'] > $now) {
            $seckill['status'] = 1;
        } elseif ($seckill['end_at'] < $now) {
            $seckill['status'] = 3;
        }

        //返回服务器当前时间
        $seckill['now_at'] = date('Y-m-d H:i:s');

        return [
            'seckill' => $seckill,
            'product' => $product,
            'sku'     => $seckillSku
        ];
    }

    /**
     * 删除一个秒杀活动
     * @param $id int 秒杀ID
     * @return bool
     */
    public function deleteSeckill($id)
    {
        //删除秒杀
        Seckill::where('id', $id)->delete();
        (new SeckillRedis)->delete($id);

        //删除秒杀库存
        (new SeckillSkuService())->deleteSkuBySeckillID($id);

        return true;
    }

    /**
     * 使失效一个秒杀活动
     * @param $id int 秒杀ID
     * @return bool
     */
    public function invalidateSeckill($id)
    {
        Seckill::where('id', $id)->update(['invalidate_at' => date('Y-m-d H:i:s')]);
        (new SeckillRedis)->updateRow(['id' => $id, 'invalidate_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    public function getSeckillSku($productSku, $seckillSku)
    {
        //处理秒杀库存
        $newSeckillSku = [];
        foreach ($seckillSku as $k => $v) {
            $newSeckillSku[$v['sku_id']] = $v;
        }

        //设置秒杀价格和库存的sku
        $seckillProductSku = [];
        $seckillSkuIDArr = [];
        foreach ($seckillSku as $k => $s) {
            foreach ($productSku['stocks'] as $kk => $p) {
                if ($s['sku_id'] == $p['id']) {
                    //设置秒杀价格库存
                    $p['price'] = $s['seckill_price'];
                    $p['stock_num'] = $s['seckill_stock'];
                    $seckillProductSku[$p['id']] = $p;
                    $seckillSkuIDArr[] = $p['id'];
                }
            }
        }

        //todo 把秒杀没使用到的规格库存设置为0 方便前端处理 后期优化改成只返回
        foreach ($productSku['stocks'] as $k => $v) {
            if (in_array($v['id'], $seckillSkuIDArr)) {
                $v = $seckillProductSku[$v['id']];
            } else {
                $v['stock_num'] = 0;
            }
            $productSku['stocks'][$k] = $v;
        }

        //todo 把秒杀没使用到的规格库存设置为0 方便前端处理 后期优化改成只返回
        //属性值ID去重
        /*$seckillValueArr = [];
        foreach ($productSku['stocks'] as $k => $v) {
            if ($v['v1_id']) {
                $seckillValueArr[] = $v['v1_id'];
            }
            if ($v['v2_id']) {
                $seckillValueArr[] = $v['v2_id'];
            }
            if ($v['v3_id']) {
                $seckillValueArr[] = $v['v3_id'];
            }
        }
        $seckillValueArr = array_unique($seckillValueArr);
        //去除多余属性值
        foreach ($productSku['props'] as $k => $v) {
            foreach ($v['values'] as $kk => $vv) {
                if (!in_array($vv['id'], $seckillValueArr)) {
                    unset($productSku['props'][$k]['values'][$kk]);
                }
            }
        }*/

        return $productSku;
    }

    /**
     * 检查某用户是否达到秒杀限额
     */
    public function isLimited($seckillID, $mid, $num)
    {
        //获取秒杀活动
        $seckill = (new SeckillService())->getDetail($seckillID);
        if (empty($seckill)) {
            return $seckill['limit_num'];
        }

        //获取用户共秒杀商品数
        $numArr = DB::table('order as o')
            ->leftJoin('order_detail as d','d.oid','=','o.id')
            ->where('o.status', '<>', 4)
            ->where('o.mid', $mid)
            ->where('o.seckill_id', $seckillID)
            ->pluck('num')
            ->toArray();

        if ((array_sum($numArr) + $num) > $seckill['limit_num']) {
            return $seckill['limit_num'];
        } else {
            return 0;
        }
    }

    /**
     * 检查某用户是否秒杀成功(抢到资格)
     */
    public function canSeckill($seckillID, $skuID, $num)
    {
        //获取秒杀当前商品规格的库存
        $stock = (new SeckillSkuService())->getStock($seckillID, $skuID);
        if ($stock >= $num) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 秒杀代付款订单
     */
    public function getWaitPayOrder($cart, $wid, $mid, $umid, $addressID = 0)
    {
        //优惠金额
        $couponAmount = 0.00;

        //运费
        $freight = (new OrderModule())->getFreightByCartIDArr([$cart['id']], $wid, $mid, $umid, $addressID);

        //秒杀单价
        $seckillSku = SeckillSku::select('seckill_price')
            ->where('seckill_id', $cart['seckill_id'])
            ->where('sku_id', $cart['prop_id'])
            ->first();

        //秒杀不取购物车价格 直接取秒杀价
        $price = $cart['price'];
        if (!empty($seckillSku)) {
            $price = $seckillSku->seckill_price;
        }

        //商品原价小计
        $productTotalAmount = $price * $cart['num'];
        $productTotalAmount = sprintf('%.2f', $productTotalAmount);

        //最终付款价格
        $lastAmount = $productTotalAmount + $freight;
        $lastAmount = sprintf('%.2f', $lastAmount);

        return [
            'couponAmount'        => $couponAmount,
            'productTotalAmount'  => $productTotalAmount,
            'lastAmount'          => $lastAmount,
            'freight'             => $freight,
            'seckillPrice'        => $price
        ];
    }

    /**
     * 提交秒杀订单
     * @update 何书哲 2018年11月16日 标记外卖订单
     * @update 何书哲 2018年11月22日 外卖店铺添加订单提交约束
     */
    public function createSeckillOrder($cardId,$request,$wid,$mid,$umid, $isXCX = 0)
    {
        $address_id = $request->input('address_id',0);

        $orderData['oid'] = OrderCommon::createOrderNumber();
        $orderData['trade_id'] = $orderData['oid'];
        $orderData['wid'] = $wid;
        $orderData['mid'] = $mid;
        $orderData['umid'] = $umid;

        //购物车信息
        $cartData = Cart::find($cardId[0]);
        if ($cartData){
            $cartData = $cartData->toArray();
        }else{
            $returnData['errCode']=-3;
            $returnData['errMsg']='购物车不存在';
            //坑
            exit(json_encode($returnData));
        }

        //何书哲 2018年11月22日 外卖店铺添加订单提交约束
        $checkRes = (new StoreModule())->checkIfSubmitOrder($wid);
        if ($checkRes['errCode'] != 0) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = $checkRes['errMsg'];
            exit(json_encode($returnData));
        }

        //查看购物车的商品详情
        $productData = ProductService::getDetail($cartData['product_id']);

        //计算商品价格,团长优惠金额，最终价格等
        $result = $this->getWaitPayOrder($cartData, $wid, $mid, $umid, $address_id);
        $orderData['pay_price'] = $result['lastAmount'];
        $orderData['products_price'] = $result['productTotalAmount'];
        $orderData['freight_price'] = $result['freight'];
        $orderData['head_discount'] = 0.00;
        $address = OrderService::getDeliveryAddress($umid, $address_id);
        if (!$address){
            $returnData['errCode']=-3;
            $returnData['errMsg']='请选择收货地址';
            //坑
            exit(json_encode($returnData));
        }
        $orderData['address_id'] = $address['areaId'];
        $orderData['address_name'] = $address['name'];
        $orderData['address_phone'] = $address['phone'];
        $orderData['address_detail'] = $address['address'];

        $orderData['address_province'] = $address['province'];
        $orderData['address_city'] = $address['city'];
        $orderData['address_area'] = $address['area'];

        //秒杀订单
        $orderData['type'] = 7;
        $orderData['buy_remark'] = $request->input('remark')??'';
        $orderData['discount_amount'] = $result['couponAmount'];
        $orderData['seckill_id'] = $cartData['seckill_id'];
        $orderData['use_point'] = 0;

        //区分是否是小程序订单
        $orderData['source'] = $isXCX;
        // 张永辉 2018年7月9日 写入小程序配置id
        $token = $request->input('token','0');
        $orderData['xcx_config_id'] = (new CommonModule())->getXcxConfigIdByToken($token);
        $orderData['is_takeaway'] = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0; //何书哲 2018年11月16日 标记外卖订单

        //当团购商品为核销商品，并且在商品核销的有效时间内生成核销订单
        //add by wuxiaoping 2017.09.22
        /*if($productData['is_hexiao'] == 1){
            $hexiao_start = strtotime($productData['hexiao_start'].'00:00:00'); //从选择时间的零时开始
            $hexiao_end   = strtotime($productData['hexiao_end'].'23:59:59');
            if($hexiao_start <= time() && time() <= $hexiao_end ){
                $orderData['is_hexiao'] = 1;
                $orderData['hexiao_code'] = OrderService::createCode(5);
            } 
        }*/

        DB::beginTransaction();
        //减库存
        $res = $this->reduceSeckillStock($cartData['seckill_id'], $cartData['product_id'],$cartData['prop_id'],$cartData['num']);
        if (!$res){
            $returnData['errCode']=-3;
            $returnData['errMsg']='库存不足';
            //坑
            exit(json_encode($returnData));
        }
        $id = Order::insertGetId($orderData);
        $orderData = Order::find($id)->toArray();

        //创建订单详情
        $orderDetailData = OrderService::createOrderDetail($orderData,$cartData);
        //添加订单日志
        $orderLogData = OrderService::addOrderLog($orderData['id'], $wid, $mid);
        //存redis
        $orderData['orderDetail'][] = $orderDetailData;
        $orderData['orderLog'] = $orderLogData;
        OrderService::init()->addR($orderData,false);
        //删除g购物车
        (new CartService())->init()->delete($cartData['id'],false);

        DB::commit();
        //计算分销
        dispatch((new Distribution($orderData,'2'))->onQueue('Distribution'));

        $isXCX == 0 && (new MessagePushModule($wid,MessagesPushService::TradeUrge))->setDelay(60)->sendMsg($id);
        $isXCX == 1 && (new MessagePushModule($wid,MessagesPushService::TradeUrge,MessagePushModule::SEND_TARGET_WECHAT_XCX))->setDelay(60)->sendMsg($id,$orderData['xcx_config_id'] );

        return $orderData;

    }

    /**
     * 秒杀订单生成后减库存
     * @param $pid
     * @param $skuid
     * @param $num
     */
    private function reduceSeckillStock($seckillID, $pid,$skuid,$num)
    {
        $productData = ProductService::getDetail($pid);
        //秒杀减库存 商品表不减库存 只增加销量
        //$up['stock'] = $productData['stock']-$num;
        $up['sold_num'] = $productData['sold_num']+$num;
        ProductService::update($pid,$up);
        if ($skuid != 0){
            //更新销量 更新原始商品规格的销量
            $productSkuService = new ProductSkuService();
            $productSkuData = $productSkuService->getRowById($skuid);
            //$skuData['stock'] = $productSkuData['stock']-$num;
            $skuData['sold_num'] = $productSkuData['sold_num'] + $num;
            $productSkuService->update($skuid,$skuData);
        }

        //更新库存 包括有规格和无规格
        $seckillSkuService = new SeckillSkuService();
        $seckillSku = $seckillSkuService->getRowByWhere(['seckill_id' => $seckillID, 'sku_id' => $skuid]);
        if ($seckillSku) {
            $res = $seckillSkuService->updateReduce($seckillSku['id'], ['seckill_stock' => $seckillSku['seckill_stock'] - $num],$num);
            if (!$res){
                return false;
            }
        }
        return true;
    }

    /**
     * 定时任务 秒杀超时未支付自动取消订单 释放库存
     */
    public function autoCancel()
    {
        //代付款秒杀订单
        DB::table('order')->select('id','seckill_id','created_at','wid','mid')
            ->where('status' ,0)
            ->where('seckill_id', '>', 0)
            ->chunk(100, function($orders) {
                $seckillService = new SeckillService();
                //$productSkuService = new ProductSkuService();
                foreach ($orders as $order) {
                    //秒杀详情
                    $now = time();
                    $seckill = $seckillService->getDetail($order->seckill_id);
                    if (empty($seckill)) {
                        continue;
                    }
                    //秒杀订单过期未支付
                    if ($now - strtotime($order->created_at) >= $seckill['cancel_minutes'] * 60) {

                        DB::beginTransaction();

                        //超时未支付 关闭订单
                        OrderService::init('wid', $order->wid)->where(['id' => $order->id])->update(['status' => 4], false);

                        //走完订单一生
                        $orderLog = [
                            'oid'       => $order->id,
                            'wid'       => $order->wid,
                            'mid'       => $order->mid,
                            'action'    => 14,
                            'remark'    => '秒杀超时未支付，系统自动关闭订单',
                        ];
                        OrderLogService::init('wid', $order->wid)->add($orderLog, false);
                        OrderService::upOrderLog($order->id, $order->wid);

                        //秒杀活动未结束未失效 则释放库存
                        if ($seckill['invalidate_at'] == '0000-00-00 00:00:00' && $seckill['end_at'] > date('Y-m-d H:i:s')) {
                            $this->returnSeckillStock($order->id, $seckill['id']);
                        }

                        DB::commit();
                    }
                }
            });
    }

    /**
     * todo 获取秒杀详情-微页面使用  更改了请联系张国军 2017-08-03
     * @param $id int 秒杀ID
     * @return array
     * @update 许立 2018年08月31日 秒杀优化增加字段, 没有规格情况价格处理
     */
    public function getSeckillInfo($id)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        //获取秒杀活动
        $seckill = (new SeckillService())->getDetail($id);
        if (empty($seckill)) {
            $returnData['errCode']=-1;
            $returnData['errCode']='秒杀活动不存在';
            return $returnData;
        }

        //获取商品以及秒杀价格和库存
        $product = ProductService::getDetail($seckill['product_id']);
        if (empty($product))
        {
            $returnData['errCode']=-2;
            $returnData['errCode']='秒杀商品不存在';
            return $returnData;
        }

        $seckillSku = (new SeckillSkuService())->getListBySeckillID($id);
        if (empty($seckillSku))
        {
            $returnData['errCode']=-3;
            $returnData['errCode']='秒杀商品价格库存不存在';
            return $returnData;
        }

        //处理秒杀价格和库存区间
        $priceArr = array_column($seckillSku, 'seckill_price');
        $stockArr = array_column($seckillSku, 'seckill_stock');
        $minPrice = min($priceArr);
        $maxPrice = max($priceArr);
        $minStock = min($stockArr);
        $maxStock = max($stockArr);
        $seckill['price_range'] = $minPrice == $maxPrice ? $minPrice : ($minPrice . '~' . $maxPrice);
        $seckill['stock_range'] = $minStock == $maxStock ? $minStock : ($minStock . '~' . $maxStock);

        //秒杀总库存
        $seckill['stock_sum'] = array_sum($stockArr);
        $seckill['seckill_stock'] = $seckill['stock_sum'];
        $seckill['seckill_price'] = $minPrice;
        $moneyArr = explode('.', $minPrice);
        $seckill['seckill_price_dollar'] = $moneyArr[0];
        $seckill['seckill_price_cent'] = $moneyArr[1];
        $seckill['seckill_sold_num'] = $this->getSoldNumBySeckillId($id);
        $seckill['seckill_oprice'] = $product['price'];
        if ($product['sku_flag']) {
            $productSku = (new ProductSkuService())->getListByProductID($seckill['product_id']);
            $seckill['seckill_oprice'] = min(array_column($productSku, 'price'));
        }
        $seckill['seckill_discount_price'] = sprintf('%.2f', $seckill['seckill_oprice'] - $seckill['seckill_price']);
        $seckill['now_at'] = date('Y-m-d H:i:s');
        // 进行中
        $seckill['seckill_status'] = 'NORMAL';
        if ($seckill['seckill_stock'] <= 0) {
            // 已售罄
            $seckill['seckill_status'] = 'SELLOUT';
        } elseif ($seckill['invalidate_at'] > '0000-00-00 00:00:00' || $seckill['end_at'] <= $seckill['now_at']) {
            // 已结束
            $seckill['seckill_status'] = 'EXPIRED';
        } elseif ($seckill['start_at'] > $seckill['now_at']) {
            // 未开始
            $seckill['seckill_status'] = 'COMING';
        }

        //多规格商品 原价区间
        $seckill['oprice_range'] = $product['price'];
        if ($product['sku_flag']) {
            //商品sku
            $sku = (new ProductSkuService())->getListByProductID($product['id']);

            //参与秒杀的规格
            $skuArr = array_column($seckillSku, 'sku_id');
            $opriceArr = [];
            foreach ($sku as $v) {
                if (in_array($v['id'], $skuArr)) {
                    $opriceArr[] = $v['price'];
                }
            }

            //拼接原价区间
            $seckill['oprice_range'] = $product['price'];
            if (!empty($opriceArr)) {
                $minPrice = min($opriceArr);
                $maxPrice = max($opriceArr);
                $seckill['oprice_range'] = $minPrice == $maxPrice ? $minPrice : ($minPrice . '~' . $maxPrice);
            }
        }

        //秒杀活动状态 1:未开时 2:进行中 3:已结束 4:失效
        $now = date('Y-m-d H:i:s');
        $seckill['status'] = 2;
        if ($seckill['invalidate_at'] > '0000-00-00 00:00:00') {
            $seckill['status'] = 4;
        } elseif ($seckill['start_at'] > $now) {
            $seckill['status'] = 1;
        } elseif ($seckill['end_at'] < $now) {
            $seckill['status'] = 3;
        }
        $returnData['data']['seckill']=$seckill;
        $returnData['data']['product']=$product;

        //获取商品原价或sku原价
        $skuService = new ProductSkuService();
        foreach ($seckillSku as $k => $v) {
            if ($v['sku_id']) {
                //有sku
                $row = $skuService->getRowById($v['sku_id']);
                $v['seckill_oprice'] = $row['price'];
            } else {
                //无规格
                $v['seckill_oprice'] = $product['price'];
            }
            $seckillSku[$k] = $v;
        }

        $returnData['data']['sku']=$seckillSku;
        return $returnData;
    }

    /**
     * 返回秒杀库存
     * 1 订单超时未支付
     * 2 买家取消订单
     * 3 商家取消订单
     * @update 许立 2018年08月27日 秒杀暂时不使用redis队列处理并发
     */
    public function returnSeckillStock($orderID, $seckillID)
    {
        $seckillSkuService = new SeckillSkuService();
        $orderDetail = DB::table('order_detail')->select('product_prop_id','num')->where('oid', $orderID)->first();
        if (!empty($orderDetail)) {
            $seckillSku = $seckillSkuService->getRowByWhere(['seckill_id' => $seckillID, 'sku_id' => $orderDetail->product_prop_id]);
            if ($seckillSku) {
                //更新秒杀商品库存
                $seckillSkuService->update($seckillSku['id'], ['seckill_stock' => $seckillSku['seckill_stock'] + $orderDetail->num]);
                //更新秒杀商品规格的销量 todo 销量是否需要减？
                /*$productSkuData = $productSkuService->getRowById($orderDetail['product_prop_id']);
                $newSoldNum = $productSkuData['sold_num'] - $orderDetail['num'];
                $productSkuService->update($orderDetail['product_prop_id'], ['sold_num' => $newSoldNum]);*/
            }
        }
    }

	/**
	 * 取消超时未付款的秒杀订单
	 * @author mafanding
	 */
	public function cancelTimeoutNonPaymentSeckillOrder($orderId)
	{
		$orderInfo = Order::find($orderId);
		if (is_null($orderInfo) || $orderInfo->status != 0) {
			return;
		}
		$now = time();
        $seckillService = new SeckillService();
		$seckill = $seckillService->getDetail($orderInfo->seckill_id);
		if (empty($seckill) || ($now - strtotime($orderInfo->created_at) >= $seckill['cancel_minutes'] * 60)) {

			DB::beginTransaction();
			try {
				//超时未支付 关闭订单
				OrderService::init('wid', $orderInfo->wid)->where(['id' => $orderInfo->id])->update(['status' => 4], false);

				//走完订单一生
				$orderLog = [
					'oid'       => $orderInfo->id,
					'wid'       => $orderInfo->wid,
					'mid'       => $orderInfo->mid,
					'action'    => 14,
					'remark'    => '秒杀超时未支付，系统自动关闭订单',
				];
				OrderLogService::init('wid', $orderInfo->wid)->add($orderLog, false);
                OrderService::upOrderLog($orderInfo->id, $orderInfo->wid);

				//秒杀活动未结束未失效 则释放库存
				if ($seckill['invalidate_at'] == '0000-00-00 00:00:00' && $seckill['end_at'] > date('Y-m-d H:i:s')) {
					$this->returnSeckillStock($orderInfo->id, $seckill['id']);
				}

				DB::commit();
			} catch (\Exception $e) {
				DB::rollback();
				Log::error('取消超时未付款秒杀订单时发生错误，订单ID：' . $orderId . '；错误信息：' . $e->getMessage());
			}
		}
	}

    /**
     * 获取秒杀活动的销量
     * @param int $seckillId 秒杀活动id
     * @return int
     * @author 许立 2018年08月31日
     * @update 许立 2018年09月29日 缓存秒杀销量
     */
    public function getSoldNumBySeckillId($seckillId)
    {
        try {
            $redisClient = (new RedisClient())->getRedisClient();
            $key         = 'seckill:sold_num:' . $seckillId;
            if ($redisClient->EXISTS($key)) {
                return $redisClient->get($key);
            }
            $select = DB::table('order_detail as d')
                ->select(DB::raw("SUM(ds_d.num) AS soldNum"))
                ->leftJoin('order as o', 'd.oid', '=', 'o.id')
                ->where('o.seckill_id', $seckillId)
                ->get()
                ->toArray();
            $num    = $select[0]->soldNum ?: 0;;
            $redisClient->SET($key, $num);
            $redisClient->EXPIRE($key, 300);
            return $num;
        } catch (\Exception $exception) {
            \Log::info('操作错误');
            return 0;
        }
    }
}
