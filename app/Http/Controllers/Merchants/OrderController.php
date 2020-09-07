<?php

namespace App\Http\Controllers\Merchants;

use App\Jobs\BatchDelivery;
use App\Jobs\ImportOrderLogistics;
use App\Jobs\sendCodeKeyProduct;
use App\Jobs\SendGroupsLog;
use App\Jobs\SendTakeAway;
use App\Jobs\SendTplMsg;
use App\Jobs\SendWeChatTemplatesMsg;
use App\Jobs\SubMsgPushJob;
use App\Model\Groups;
use App\Model\GroupsRule;
use App\Model\Member;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderRefund;
use App\Model\Product;
use App\Model\ProductSku;
use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Module\ExportModule;
use App\Module\GroupsRuleModule;
use App\Module\MessagePushModule;
use App\Module\NotificationModule;
use App\Module\OrderLogisticsModule;
use App\Module\OrderModule;
use App\Module\RefundModule;
use App\Module\SeckillModule;
use App\Module\StoreModule;
use App\Module\WechatBakModule;
use App\S\Foundation\ExpressService;
use App\S\Foundation\RegionService;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsService;
use App\S\Log\BatchDeliveryLogService;
use App\S\Market\CouponLogService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\Model\Income;
use App\Model\Weixin;
use App\Model\WeixinAddress;
use App\S\NotificationService;
use App\S\Order\EvaluateClassifyService;
use App\S\Order\OrderLogisticsService;
use App\S\Product\ProductEvaluateClassifyService;
use App\S\Product\RemarkService;
use App\S\WXXCX\SubscribeMessagePushService;
use App\S\WXXCX\WXXCXCollectFormIdService;
use App\S\WXXCX\WXXCXConfigService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Order\LogisticsService;
use App\Services\Order\OrderRefundService;
use App\Services\OrderRefundMessageService;
use App\Services\ProductEvaluatePraiseService;
use App\Services\Shop\MemberAddressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\S\Wechat\WeixinRefundService;
use App\Http\Controllers\Controller;
use Excel;
use PointRecordService as OPointRecordService;
use OrderService;
use OrderLogService;
use ProductEvaluateService;
use ProductEvaluateDetailService;
use OrderDetailService;
use Validator;
use ProductService;
use WeixinService;
use App\S\RelationService;
use App\Services\CashLogService;
use App\S\Order\HexiaoLogService;
use App\S\Order\OrderService as orService;
use DB;
use App\S\ShareEvent\ShareEventService;
use App\S\ShareEvent\ShareEventRecordService;
use App\S\Order\OrderZitiService;
use App\S\Cam\CamActivityService;
use App\S\Cam\CamListService;
use App\S\Weixin\ShopService;

/**
 * 店铺订单
 */
class OrderController extends Controller
{
    protected $memberService;
    protected $regionService;

    /**
     * 构造函数
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 11:51:20
     */
    public function __construct(MemberService $memberService, RegionService $regionService)
    {
        /* 设置左侧导航标识 */
        $this->leftNav = 'order';
        $this->memberService = $memberService;
        $this->regionService = $regionService;
    }

    /**
     * 订单概况
     * @return view
     * @author 黄东 2017年2月8日
     * @update 许立 2018年6月26日 修改退款中订单
     */
    public function index(ShopService $shopService)
    {
        $wid = session('wid');
        $relationservice = new RelationService();
        // 七天前日期字符串
        $sevenDayBeforeStr = date('Y-m-d', strtotime('-7 days'));
        //店铺信息
        /*$weixinService = D('Weixin', 'uid', session('userInfo')['id']);
        $weixinInfo = $weixinService->getInfo($wid);*/
        $weixinInfo = $shopService->getRowById($wid);
        // 查询条件
        $where['wid'] = session('wid');
        OrderLogService::where($where);
        $where['_closure'] = function ($query) {
            $query->whereIn('status', [0, 1])->orWhereIn('refund_status', [1, 3]);
        };

        // 订单统计（待付款、待发货、积压维权）
        $orderStatisticalList = OrderService::where($where)->statistical();

        $cashLogService = new CashLogService();
        // 7天内订单统计（下单数和收入）
        $distributionSevenList = $cashLogService->statical($wid, 2, [strtotime($sevenDayBeforeStr . ' 00:00:00')]);
        //order日志7天下单数量
        $orderLogSevenDayList = OrderLogService::statistical([1], [strtotime($sevenDayBeforeStr . ' 00:00:00')]);

        //order日志7天收入金额
        $orderLogSevenDayIcome = OrderLogService::statistical([2], [strtotime($sevenDayBeforeStr . ' 00:00:00')]);
        $return = [];
        $return['total_income'] = $return['total_count'] = 0;
        //减去分销打款的金额7
        if (!empty($distributionSevenList)) {
            $orderLogSevenDayIcome['income'] = 0.00;
            foreach ($distributionSevenList as $dis) {
                if ($orderLogSevenDayIcome) {
                    $orderLogSevenDayIcome['income'] -= $dis['money'];
                }
            }
        }

        // 许立 2018年6月26日 积压维权 退款中订单与订单列表中保持一致
        $orderRefundCount = 0;
        foreach (OrderRefund::REFUNDING_STATUS_ARRAY as $status) {
            if (!empty($orderStatisticalList['refundStatus'][$status]['count'])) {
                $orderRefundCount += $orderStatisticalList['refundStatus'][$status]['count'];
            }
        }

        // 昨日日期字符串
        $yestodayStr = date('Y-m-d', strtotime('-1 days'));
        $distributionDayList = $cashLogService->statical($wid, 2, [strtotime($yestodayStr . ' 00:00:00')]);
        //昨日下单数量
        $orderLogOneDayList = OrderLogService::statistical([1], [strtotime($yestodayStr . ' 00:00:00')]);
        //昨日收入金额
        $orderLogOneDayIcome = OrderLogService::statistical([2], [strtotime($yestodayStr . ' 00:00:00')]);
        if ($orderLogOneDayIcome) {
            $orderLogOneDayIcome['income'] = sprintf('%.2f', $orderLogOneDayIcome['income']);
        }
        //减去分销打款的金额
        if (!empty($distributionDayList)) {
            $orderLogOneDayIcome['income'] = 0.00;
            foreach ($distributionDayList as $item) {
                if ($orderLogOneDayIcome) {
                    $orderLogOneDayIcome['income'] -= $item['money'];
                }
            }
        }
        // 七天日期数据数组 - 前端js所需数据 - 打印到前台时需要转为Json
        $sevenDayJson = [];
        // 七天创建订单数据数组 - 前端js所需数据 - 打印到前台时需要转为Json
        $sevenCreateOrderJson = [];
        // 七天支付订单数据数组 - 前端js所需数据 - 打印到前台时需要转为Json
        $sevenpayOrderJson = [];
        for ($i = 7; $i > 0; $i--) {
            $tempDate = date('Y-m-d', strtotime('-' . $i . ' days'));
            $sevenDayJson[] = $tempDate;
            $sevenCreateOrderJson[] = isset($orderLogSevenDayList[1]['list'][$tempDate]['countUnit']) ? $orderLogSevenDayList[1]['list'][$tempDate]['countUnit'] : 0;
            $sevenpayOrderJson[] = isset($orderLogSevenDayList[2]['list'][$tempDate]['countUnit']) ? $orderLogSevenDayList[2]['list'][$tempDate]['countUnit'] : 0;
        }

        return view('merchants.order.index', array(
            'title' => '订单概况',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'orderStatisticalList' => $orderStatisticalList,
            'orderLogSevenDayList' => $orderLogSevenDayList,
            'orderLogOneDayList' => $orderLogOneDayList,
            'orderRefundCount' => $orderRefundCount,
            'yestodayStr' => $yestodayStr,
            'sevenDayBeforeStr' => $sevenDayBeforeStr,
            'sevenDayJson' => json_encode($sevenDayJson),
            'sevenCreateOrderJson' => json_encode($sevenCreateOrderJson),
            'sevenpayOrderJson' => json_encode($sevenpayOrderJson),
            'orderLogSevenDayIcome' => $orderLogSevenDayIcome,
            'orderLogOneDayIcome' => $orderLogOneDayIcome

        ));
    }

    /**
     * 订单列表
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 11:51:20
     *
     * @param  integer $menu [订单二级菜单类型标识：0所有订单；1加星订单；2维权订单]
     * @param  integer $nav [订单导航类型标识：0全部订单；1同城送订单；2自提订单；3货到付款订单]
     * @return view
     * @update 张永辉 2018年7月19日 根据个人mid 查询订单信息
     * @update 梅杰 2018年8月15日  小程序显示具体小程序名称
     * @update 何书哲 2018年11月16日 外卖店铺取消待发货、已导入、已打单状态
     */
    public function orderList(Request $request, $menu = 0, $nav = 0)
    {
        /* 配送方式数组 04.27隐藏未做功能 ： 同城送订单，自提订单，货到付款订单*/
        //$navTypeList = ['全部订单', '同城送订单', '自提订单', '货到付款订单'];

        $navTypeList = ['全部订单'];

        /* 获取固定数据数组 */
        list($fieldList, $typeList, $expressList, $payWayList, $statusList, $refundStatusList) = OrderService::getStaticList();

        /* 构建筛选、搜索查询条件数组 */
        OrderService::init('wid', session('wid'))->buildWhere();

        /* 查询条件 */
        $where = [];

        /* 订单二级菜单类型标识 */
        if ($menu) {
            switch (strval($menu)) {
                /* 加星订单 */
                case '1':
                    $where['star_level'] = array('>', 0);
                    break;
                /* 维权订单 */
                case '2':
                    !isset($where['refund_status']) && $where['refund_status'] = array('>', 0);
                    break;
                default:
                    # code...
                    break;
            }
        }
        /* 订单导航类型标识 0全部订单；1同城送订单；2自提订单；3货到付款订单 */
        if ($nav) {
            switch (strval($nav)) {
                /* 同城送订单 */
                case '1':
                    $where['express_type'] = 2;
                    break;
                /* 自提订单 */
                case '2':
                    $where['express_type'] = 3;
                    break;
                /* 货到付款订单 */
                case '3':
                    $where['pay_way'] = 4;
                    break;
                default:
                    # code...
                    break;
            }
        }

        //查询单个人的订单
        if (!empty($request->input('mid'))) {
            OrderService::whereAdd(['mid' => $request->input('mid')]);
        }

        // 追加查询条件
        OrderService::whereAdd($where);
        OrderService::whereAdd(['wid' => session('wid')]);
        if ($request->input('groups_id')) {
            $groups_id = $request->input('groups_id');
            OrderService::whereAdd(['groups_id' => $groups_id]);
        }
        //团购订单
        $groups = [];
        if ($request->input('status') == -1) {

            OrderService::whereAdd(['groups_id' => ['<>', 0]]);
            OrderService::whereAdd(['groups_status' => 1]);
            OrderService::whereAdd(['status' => 1]);
        }
        $wid = session('wid');
        if ($request->input('status') == 2) {
            $groupsWhere = [
                'groups_status' => ['in', [0, 2]],
            ];
            OrderService::whereAdd($groupsWhere);
            //加入条件（过滤掉核销订单显示）
            OrderService::whereAdd(['is_hexiao' => ['<>', 1]]);
        }
        // 关联关系
        $with = ['orderDetail'];
        /* 获取数据 */
        list($list, $pageHtml) = OrderService::with($with)->getList();
        $input = app('request')->input();
        //退款信息
        $refundService = new OrderRefundService();
        $productService = new \App\S\Product\ProductService();

        foreach ($list['data'] as &$order) {
            foreach ($order['orderDetail'] as &$detail) {
                $detail['product_code'] = $productService->getProductCode($detail['product_id'], $detail['product_prop_id']);
                $refund = $refundService->init('oid', $order['id'])->where(['oid' => $order['id'], 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
                $detail['status_string'] = '';
                if ($refund) {
                    $detail['status_string'] = $refundService->getStatusString($refund['status']);
                }
            }
            if ($order['source']) {
                //获取小程序名称
                $xcxInfo = (new WXXCXConfigService())->getListByCondition(['wid' => $wid, 'id' => $order['xcx_config_id']]);
                $order['xcxTitle'] = $xcxInfo['data'] ? $xcxInfo['data'][0]['title'] : '';
            }
            //何书哲 2018年6月28日 返回每个订单是否导入快递100
            $order['is_print'] = (new OrderLogisticsService())->getRowByWhere(['oid' => $order['id']]) ? 1 : 0;
        }

        $shopAddress = WeixinAddress::where('wid', session('wid'))->whereIn('type', [0, 3])->orderBy('is_default', 'desc')->first();
        if ($shopAddress) {
            $shopAddress = $shopAddress->toArray();
        }

        //todo 优化
        $regions = $this->regionService->getAll();
        foreach ($regions as $key => $item) {
            if ($item['status'] == -1) {
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];

//        if ($request->input('order_type') !=3){
//            unset($statusList[-1]);
//        }
        //获取团购信息
        if ($request->input(['status']) != -1) {
            $ids = [];
            foreach ($list['data'] as $key => $val) {
                $ids[] = $val['id'];
            }
            if ($ids) {
                $groups = (new GroupsDetailService())->getListByOrderIds($ids);
            }
        }

        //何书哲 2018年11月16日 外卖店铺取消待发货、已导入、已打单状态
        if ($takeAwayConfig = (new StoreModule())->getWidTakeAway($wid) ? 1 : 0) {
            unset($statusList['2'], $statusList['9'], $statusList['10']);
        }
        return view('merchants.order.orderList', array(
            'title' => '所有订单',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderList_' . $menu,
            'navTypeList' => $navTypeList,
            'fieldList' => $fieldList,
            'typeList' => $typeList,
            'expressList' => $expressList,
            'payWayList' => $payWayList,
            'statusList' => $statusList,
            'refundStatusList' => $refundStatusList,
            'list' => $list,
            'pageHtml' => $pageHtml,
            'page' => $input['page'] ?? 1, //返回当前页 导出订单目前只导出当前页
            'orderListType' => $menu, //订单列表类型 0所有订单；1加星订单；2维权订单
            'shopAddress' => $shopAddress,
            'regions_data' => json_encode($regionList),
            'regionList' => $regionList,
            'provinceList' => $provinceList,
            'groups' => $groups,
            'admin_del_show' => config('app.del_wid') == session('wid') ? 1 : 0,
            'takeAwayConfig' => $takeAwayConfig, //是否是外卖店铺 1:是 0:否
        ));
    }

    /**
     * 订单列表添加可以修改收货地址
     * @author 吴晓平 <2018年08月17日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeSendOrderAddr(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        if (empty($oid)) {
            error('订单不存在或已被删除');
        }
        $orderData = OrderService::init()->getInfo($oid, false);
        if (empty($orderData)) {
            error('该订单不存在或已删除');
        }
        $addrData = [
            'address_province' => $orderData['address_province'],
            'address_city' => $orderData['address_city'],
            'address_area' => $orderData['address_area'],
            'address_detail' => $orderData['address_detail'],
            'address_name' => $orderData['address_name'],
            'address_phone' => $orderData['address_phone']
        ];
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rules = [
                'mid' => 'required',
                'province_id' => 'required',
                'city_id' => 'required',
                'area_id' => 'required',
                'address' => 'required',
                'address_name' => 'required',
                'address_phone' => 'required'
            ];
            $message = [
                'mid.required' => '该收货人信息不存在或已被删除',
                'province_id.required' => '请选择省份或直辖市',
                'city_id.required' => '请选择该省份对应的市',
                'area_id.required' => '请选择该市对应的区或镇',
                'address.required' => '请输入详细地址',
                'address_name.required' => '请输入收货人姓名',
                'address_phone.required' => '请输入联系人电话'
            ];
            $validator = Validator::make($input, $rules, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            try {
                $dbResult = DB::transaction(function () use ($input, $oid, $addrData) {
                    $saveData['mid'] = $input['mid'] ?? 0;
                    $saveData['title'] = $input['address_name'];
                    $saveData['province_id'] = $input['province_id'];
                    $saveData['city_id'] = $input['city_id'];
                    $saveData['area_id'] = $input['area_id'];
                    $saveData['address'] = $input['address'];
                    $saveData['phone'] = $input['address_phone'];
                    $addressId = (new MemberAddressService())->init()->add($saveData, false);
                    if ($addressId) {
                        $temp = [$input['province_id'], $input['city_id'], $input['area_id']];
                        $regions = $this->regionService->getListByIdWithoutDel($temp);
                        $saveOrderData['address_id'] = $addressId;
                        $saveOrderData['address_name'] = $input['address_name'];
                        $saveOrderData['address_phone'] = $input['address_phone'];
                        $saveOrderData['address_province'] = $regions[$input['province_id']]['title'];
                        $saveOrderData['address_city'] = $regions[$input['city_id']]['title'];
                        $saveOrderData['address_area'] = $regions[$input['area_id']]['title'];
                        $saveOrderData['address_detail'] = $saveOrderData['address_province'] . $saveOrderData['address_city'] . $saveOrderData['address_area'] . $input['address'];
                        if (OrderService::init('wid', session('wid'))->where(['id' => $oid])->update($saveOrderData, false)) {
                            $saveOrderLogData['oid'] = $oid;
                            $saveOrderLogData['wid'] = session('wid');
                            $saveOrderLogData['mid'] = $input['mid'];
                            $saveOrderLogData['action'] = 17;
                            $saveOrderLogData['remark'] = '[修改发货地址] ' . $addrData['address_detail'] . ' 改为 ' . $saveOrderData['address_detail'];
                            if (OrderLogService::init()->add($saveOrderLogData, false)) {
                                return true;
                            }
                            return false;
                        }
                        return false;
                    }
                    return false;
                });
            } catch (\Exception $e) {
                error($e->getMessage());
            }
            if ($dbResult) {
                success('发货地址修改成功');
            }
            error('发货地址修改失败');
        }
        //获取地区全部数据
        $regions = $this->regionService->getAllWithoutDel();
        foreach ($regions as $key => $item) {
            if ($item['status'] == -1) {
                unset($regions[$key]);
            }
        }
        foreach ($regions as $key => $item) {
            $regionList[$item['pid']][] = $item;
        }
        //单独列出省份列表信息
        $provinceList = $regionList[-1];
        $data = ['regionList' => $regionList, 'provinceList' => $provinceList, 'addrData' => $addrData];
        success('', '', $data);
    }

    /**
     * [核销日志订单列表页]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoOrder(Request $request)
    {
        $input = $request->input();
        /* 构建筛选、搜索查询条件数组 */
        OrderService::init('wid', session('wid'))->buildWhere();
        $where['is_hexiao'] = 1;
        // 追加查询条件
        OrderService::whereAdd($where);

        // 关联关系
        $with = ['orderDetail'];
        /* 获取数据 */
        list($list, $pageHtml) = OrderService::with($with)->getList();

        $productService = new \App\S\Product\ProductService();
        foreach ($list['data'] as &$order) {
            foreach ($order['orderDetail'] as &$detail) {
                $detail['product_code'] = $productService->getProductCode($detail['product_id'], $detail['product_prop_id']);
            }
        }

        if (!empty($input['type']) && $input['type'] == 'express') {
            $result = [];
            $pro_info = $pro_img = $price_num = '';
            foreach ($list['data'] as $key => $items) {
                if ($items['status'] == 0) {
                    $items['status'] = '未支付';
                } else if ($items['status'] == 1) {
                    $items['status'] = '已支付';
                } else if ($items['status'] == 2) {
                    $items['status'] = '已完成';
                } else if ($items['status'] == 3) {
                    $items['status'] = '已关闭';
                } else if ($items['status'] == 4) {
                    $items['stat8s'] = '退款中';
                }
                $titles = join(',', array_column($items['orderDetail'], 'title'));
                $specs = join(',', array_column($items['orderDetail'], 'spec'));
                $imgs = join(',', array_column($items['orderDetail'], 'img'));
                $prices = join(',', array_column($items['orderDetail'], 'price'));
                $nums = join(',', array_column($items['orderDetail'], 'num'));
                $items['pro_info'] = $titles . '/' . $specs;
                $items['img'] = $imgs;
                $items['price-num'] = $prices . '/' . $nums;
                $result[] = $items;
            }
            OrderService::exportExcel($result, 'hexiaoOrder');
        }
        //获取相关的商品信息
        return view('merchants.order.hexiaoOrder', [
            'title' => '核销订单',
            'leftNav' => $this->leftNav,
            'slidebar' => 'hexiaoOrder',
            'data' => $list['data'],
            'pageHtml' => $pageHtml,
        ]);
    }

    /**
     * [结单操作]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function finishOrder(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $orderId = $request->input('orderId') ?? 0;

        if (!$orderId) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '订单不存在';
            return $returnData;
        }
        $wid = session('wid');
        $orderData = OrderService::init('wid', $wid)->where(['id' => $orderId, 'status' => 1])->getInfo(false);
        if (empty($orderData)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '核销订单状态不正确';
            return $returnData;
        }

        //对比输入的核销码
        if ($orderData['hexiao_code'] != $request->input('code')) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '该用户提货码不存在，请检查后再试';
            return json_encode($returnData);
        }
        //订单说详情数据
        $orderDetailData = (new orService())->getOrderDetail($orderId);
        if (empty($orderDetailData)) {
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = '订单数据不存在';
            return $returnData;
        }
        $result = DB::transaction(function () use ($wid, $orderId, $orderData) {
            //更新订单表
            OrderService::init('wid', $wid)->where(['id' => $orderId])->update(['status' => 2], false);
            //更新订单详情已发货
            OrderDetailService::init()->model->where(['oid' => $orderId])->update(['is_delivery' => 1, 'delivery_time' => time()], false);
            //添加订单日志表记录
            $orderLogData = [];
            $orderLogData['oid'] = $orderId;
            $orderLogData['wid'] = $wid;
            $orderLogData['mid'] = $orderData['mid'];
            $orderLogData['action'] = 3; //商家发货（表示用户已自提）
            $orderLogData['remark'] = '自提订单' . $orderData['oid'] . '用户已提货';
            OrderLogService::init()->add($orderLogData, false);
            OrderService::upOrderLog($orderId, $wid);
            OrderService::upOrderDetail($orderId, $wid);
            return true;
        });

        if (!$result) {
            $returnData['errCode'] = -5;
            $returnData['errMsg'] = '核销操作失败';
        }
        return $returnData;

    }

    /**
     * 设置核销日志订单备注
     * @param Request $request [description]
     */
    public function setHexiaoRemark(Request $request)
    {
        $input = $request->input();
        $orderId = $input['orderId'] ?? 0;
        if (!$orderId) {
            error('订单不存在');
        }
        $result = (new HexiaoLogService())->saveByOid($orderId, ['seller_remark' => $input['seller_remark']]);
        if ($result['errcode'] == 0) {
            OrderService::init('wid', session('wid'))->where(['id' => $orderId])->update(['seller_remark' => $input['seller_remark']]);
            success();
        } else {
            error();
        }
    }


    /**
     * 核销批量导出
     * @return [type] [description]
     */
    public function hexiaoExpress()
    {
        OrderService::exportExcel($data, 'hexiaoOrder');
    }

    /**
     * 订单设置星级
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月20日 15:29:41
     */
    public function setStar()
    {
        /* 数据验证 */
        $input = OrderService::verify(['id', 'star_level']);

        /* 更新数据 */
        $where['id'] = $input['id'];
        $where['wid'] = session('wid');
        OrderService::init('wid', session('wid'))->where($where)->update(['star_level' => $input['star_level']]);
    }

    /**
     * 订单设置商家备注
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月20日 15:29:41
     */
    public function setSellerRemark()
    {
        /* 数据验证 */
        $input = OrderService::verify(['id', 'seller_remark']);

        /* 更新数据 */
        $where['id'] = $input['id'];
        $where['wid'] = session('wid');
        OrderService::init('wid', session('wid'))->where($where)->update(['seller_remark' => $input['seller_remark']]);
    }

    /**
     * 订单详情
     * @param $$logisticsService 物流service
     * @param $id 订单id
     * @param $notify_id 消息id
     * @return view 视图
     * @update 梅杰 2018年9月27 订单日志付款方式优化
     * @update 许立 2018年10月08日 文案修改
     * @update 何书哲 2018年11月16日 是否是外卖店铺
     */
    public function orderDetail(LogisticsService $logisticsService, $id, $notify_id = 0)
    {
        /* 查询订单详情 */
        $detail = OrderService::init('wid', session('wid'))->model->where('id', $id)->get()->load('orderDetail')->load('orderLog')->toArray();
        $detail = current($detail);
        if (!isset($detail['wid']) || $detail['wid'] != session('wid')) {
            error('该订单不属于该店铺，禁止访问');
        }
        //区分消息已读未读，故引入notify_id hsz 2018/6/25
        if ($notify_id) {
            (new NotificationModule())->setReadNotification($notify_id);
        }
        //add MayJay
        $detail['no_express'] = 0;
        //end
        if ($detail['status'] == 2 || $detail['status'] == 3) {
            $logistics = $logisticsService->init()->model->where(['oid' => $id])->first();
            if ($logistics) {
                $logistics = $logistics->toArray();
                $detail['logistics'] = $logistics['express_name'];

                //add MayJay
                $detail['no_express'] = $logistics['no_express'];
                //end

            } else {
                $detail['logistics'] = '';
            }
        }
        //退款信息
        $refundService = new OrderRefundService();
        list($refund) = $refundService->init('oid', $id)->getList(false);
        foreach ($refund['data'] as $k => $v) {
            $v['status_string'] = $refundService->getStatusString($v['status']);
            $refund['data'][$v['pid']] = $v;
            unset($refund['data'][$k]);
        }
        /* 获取固定数据数组 */
        list($fieldList, $typeList, $expressList, $payWayList, $statusList, $refundStatusList) = OrderService::getStaticList();
        $detail['groups'] = [];
        if ($detail['groups_id'] != 0) {
            $detail['groups'] = (new GroupsService())->getRowById($detail['groups_id']);
            if ($detail['groups']['status'] == 2) {
                $detail['groups']['group_status'] = $detail['status'] + 2;
            } else {
                $detail['groups']['group_status'] = $detail['status'] + 1;
            }
        }

        //add by Herry 秒杀金额 特殊处理
        if ($detail['type'] == 7) {
            $product = ProductService::getDetail($detail['orderDetail'][0]['product_id']);
            if ($product) {
                //商品原价
                $detail['orderDetail'][0]['price'] = $product['price'];
                //秒杀优惠金额(商品原价 - 秒杀商品金额)
                $detail['seckill_coupon'] = $product['price'] * $detail['orderDetail'][0]['num'] - $detail['products_price'];
            }
        }
        $detail['member'] = (new MemberService())->getRowById($detail['mid']);
        //add by zhangyh 20180119
        $remarkService = new RemarkService();
        foreach ($detail['orderDetail'] as &$item) {
            $item['noteList'] = [];
            if ($item['remark_no'] ?? '') {
                $item['noteList'] = $remarkService->getByRemarkNo($item['remark_no']);
            }
        }//end
        $view_html = $detail['is_hexiao'] == 1 ? 'merchants.order.zitiOrderDetail' : 'merchants.order.orderDetail';
        if ($detail['is_hexiao'] == 1) {
            $where['oid'] = $id;
            $where['wid'] = session('wid');
            $where['mid'] = $detail['mid'];
            $zitiData = (new OrderZitiService())->getDataByCondition($where);
            if ($zitiData) {
                $temp = [$zitiData['orderZiti']['province_id'], $zitiData['orderZiti']['city_id'], $zitiData['orderZiti']['area_id']];
                $regionService = new RegionService();
                $region = $regionService->getListById($temp);

                $tmpAddr = [];
                foreach ($region as $val) {
                    $tmpAddr[$val['id']] = $val['title'];
                }
                $zitiData['orderZiti']['province_title'] = $tmpAddr[$zitiData['orderZiti']['province_id']];
                $zitiData['orderZiti']['city_title'] = $tmpAddr[$zitiData['orderZiti']['city_id']];
                $zitiData['orderZiti']['area_title'] = $tmpAddr[$zitiData['orderZiti']['area_id']];
                $detail['ziti'] = $zitiData;
            }

        }
        //add by wuxiaoping 2018年08月20日 获取订单日志的所有状态
        $logStatus = OrderLogService::getAllStatus();
        foreach ($detail['orderLog'] as &$v) {
            if ($v['action'] == 2) {
                $payWay = static::getPayWay($detail['pay_way']);
                $v['remark'] = '付款方式: ' . $payWay;
            }
        }

        //何书哲 2018年11月16日 是否是外卖店铺
        $takeAwayConfig = (new StoreModule())->getWidTakeAway(session('wid')) ? 1 : 0;

        return view($view_html, array(
            'title' => '订单详情',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderDetail',
            'detail' => $detail,
            'typeList' => $typeList,
            'payWayList' => $payWayList,
            'expressList' => $expressList,
            'refund' => $refund['data'],
            'logStatus' => $logStatus,
            'statusList' => $statusList,
            'takeAwayConfig' => $takeAwayConfig,
        ));
    }

    /**
     * 评价管理
     * @return [type] [description]
     */
    public function evaluateOrder(Request $request, $status = 0)
    {
        $where = [];
        if ($status != 0) {
            $where['status'] = $status;
        }
        $evaluateData = ProductEvaluateService::init('wid', session('wid'))->where($where)->getList();
        $ids = [];
        foreach ($evaluateData[0]['data'] as $val) {
            $ids[] = $val['id'];
        }
        $where = [
            'mid' => 0,
            'eid' => ['in', $ids],
        ];
        list($evaluateDetail) = ProductEvaluateDetailService::init()->where($where)->getList(false);
        $tmp = [];
        foreach ($evaluateDetail['data'] as $val) {
            $tmp[$val['eid']] = $val;
        }
        //评价标签
        $ecData = (new ProductEvaluateClassifyService())->getList(['eid' => ['in', $ids]]);
        foreach ($ecData as $val) {
            $result[$val['eid']][] = $val;
        }
        foreach ($evaluateData[0]['data'] as &$val) {
            if (isset($tmp[$val['id']])) {
                $val['detail'] = $tmp[$val['id']];
            } else {
                $val['detail'] = '';
            }
            $val['ec'] = $result[$val['id']] ?? [];
        }
        return view('merchants.order.evaluateOrder', array(
            'title' => '评价管理',
            'leftNav' => $this->leftNav,
            'slidebar' => 'evaluateOrder',
            'evaluate' => $evaluateData,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc 回复评价
     * @param Request $request
     */
    public function evaluateReply(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'eid' => 'required',
            'content' => 'required',
        );
        $message = Array(
            'eid.required' => '评论ID不能为空',
            'content.required' => '评论回复内容不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $evaluateData = [
            'eid' => $input['eid'],
            'mid' => 0,
            'reply_id' => isset($input['reply_id']) ? $input['reply_id'] : '',
            'content' => $input['content']
        ];
        ProductEvaluateDetailService::init()->add($evaluateData);

    }

    /**
     * 订单-评价管理 删除评论
     */
    public function evaluateDelete(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id)) {
            error('评论ID不能为空');
        }

        //评论详情
        $info = ProductEvaluateService::init('wid', session('wid'))->getInfo($id);
        if (empty($info)) {
            error('评论不存在');
        }
        if ($info['wid'] != session('wid')) {
            error('不能删除其他店铺的评论');
        }

        //删除主表
        ProductEvaluateService::init('wid', session('wid'))->where(['id' => $id])->delete($id, false);

        //删除附属表
        $details = ProductEvaluateDetailService::init()->select('id')->where(['eid' => $id])->getList(false);
        if ($details[0]['data']) {
            $detail_ids = array_column($details[0]['data'], 'id');
            foreach ($detail_ids as $v) {
                ProductEvaluateDetailService::init()->where(['id' => $v])->delete($v, false);
            }
        }
        $praise_service = new ProductEvaluatePraiseService();
        $praises = $praise_service->init()->select('id')->where(['eid' => $id])->getList(false);
        if ($praises[0]['data']) {
            $praise_ids = array_column($praises[0]['data'], 'id');
            foreach ($praise_ids as $v) {
                $praise_service->init()->where(['id' => $v])->delete($v, false);
            }
        }
        $classify_service = new ProductEvaluateClassifyService();
        $classifies = $classify_service->model->select('id')->where(['eid' => $id])->get()->toArray();
        if ($classifies) {
            $classify_ids = array_column($classifies, 'id');
            foreach ($classify_ids as $v) {
                $classify_service->del($v);
            }
        }

        success();
    }


    /**
     * 分销采购单
     * @return [type] [description]
     */
    public function distributionOrder(Request $request)
    {
        return view('merchants.order.distributionOrder', array(
            'title' => '分销采购单',
            'leftNav' => $this->leftNav,
            'slidebar' => 'distributionOrder'
        ));
    }

    /**
     * 订单-导出列表
     * @param Request $request
     * @return excel文件
     * @author Herry
     * @since 2018/6/20
     */
    public function export(Request $request)
    {
        //表单提交
        if ($request->isMethod('post')) {
            //获取参数
            $input = $request->input();
            //初始化查询条件数组
            $where = [
                'created_at' => [
                    'between',
                    [
                        $input['exportStart'],
                        $input['exportEnd']
                    ]
                ]
            ];

            //构建查询条件数组
            !empty($input['exportOrderId']) && $where['oid'] = $input['exportOrderId'];
            !empty($input['exportBuyerName']) && $where['address_name'] = $input['exportBuyerName'];
            !empty($input['exportBuyerPhone']) && $where['address_phone'] = $input['exportBuyerPhone'];
            !empty($input['exportOrderType']) && $where['type'] = $input['exportOrderType'];
            !empty($input['exportPayWay']) && $where['pay_way'] = $input['exportPayWay'];
            if (!empty($input['exportOrderStatus'] && $input['exportOrderStatus'] == -1)) {
                $where['status'] = 1;
                $where['groups_status'] = 1;
                $where['groups_id'] = ['>', 0];
            }
            if (!empty($input['exportOrderStatus']) && $input['exportOrderStatus'] != -1) {
                if ($input['exportOrderStatus'] == 100) {
                    $where['status'] = ['in', [1, 2, 3]];
                } else {
                    $where['status'] = $input['exportOrderStatus'] - 1;

                    // 待付款订单要过滤掉待成团订单和核销订单 Herry 20180622
                    if ($input['exportOrderStatus'] == 2) {
                        $where['groups_status'] = ['in', [0, 2]];
                        $where['is_hexiao'] = ['<>', 1];
                    }
                }

            }
            !empty($input['exportExpressType']) && $where['express_type'] = $input['exportExpressType'];
            !empty($input['exportRefundStatus']) && $where['refund_status'] = ['in', explode(',', $input['exportRefundStatus'])];
            //不同数据导出
            OrderService::init('wid', session('wid'));
            //关联表
            $with = ['orderDetail'];
            if ($input['exportType'] == 'bill') {
                if (empty($where['status'])) {
                    //收入状态包括 1待发货；2已发货（待收货）；3已完成；5退款中
                    $where['status'] = [
                        'in',
                        [1, 2, 3, 5]
                    ];
                }
            } else if ($input['exportType'] == 'otherBill') {
                //导出代付对账单
                $where['type'] = 2;
            }
            //订单列表类型 0所有订单；1加星订单；2维权订单
            if (!empty($input['orderListType'])) {
                switch ($input['orderListType']) {
                    /* 加星订单 */
                    case '1':
                        $where['star_level'] = array('>', 0);
                        break;
                    /* 维权订单 */
                    case '2':
                        !isset($where['refund_status']) && $where['refund_status'] = array('>', 0);
                        break;
                    default:
                        # code...
                        break;
                }
            }
            //获取数据
            list($list, $pageHtml) = OrderService::with($with)->where($where)->getList(false);

            //导出发货订单
            if ($input['exportType'] == 'delivery') {
                //设置文件名
                $filename = '订单导出' . date('YmdHis') . '.xls';
                //导出
                OrderService::exportExcel($list['data'], 'sendProduct');
            } else {
                //OrderService::exportExcel(array_slice($list['data'], (($input['page'] - 1) * $perPage), $perPage), $input['exportType']);
                //导出所有数据 不分页

                /************add fuguowei 20180103*******/
                if (isset($list['data'])) {
                    foreach ($list['data'] as &$val) {
                        foreach ($val['orderDetail'] as &$v) {
                            $productData = ProductService::getDetail($v['product_id']);

                            $v['cost_price'] = $productData['cost_price'] ?? '';
                        }
                    }
                }

                /*********end**********/
                OrderService::exportExcel($list['data'], $input['exportType']);
            }
        }
    }

    //先当待发货用
    public function setOrderStatus(Request $request, $id)
    {
        $where['id'] = $id;
        $where['wid'] = session('wid');
        OrderService::init('wid', session('wid'))->where($where)->update(['status' => 2], false);
        return redirect('/merchants/order/orderDetail/' . $id);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20170505
     * @desc 取消订单
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function clearOrder(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = Array(
                'remark' => 'required',
            );
            $message = Array(
                'remark.required' => '请选择原因',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $order = OrderService::init()->getInfo($id);
            if (!$order || $order['status'] != 0) {
                return myerror('该订单买家已付款，暂时无法取消');
            }

            //退还积分
            if ($order['use_point'] > 0) {
                $point = $order['use_point'];
                $pointRecordData = [
                    'wid' => session('wid'),
                    'mid' => $order['mid'],
                    'point_type' => 6,
                    'is_add' => 1,
                    'score' => $point
                ];

                OPointRecordService::insertData($pointRecordData);
                $this->memberService->incrementScore($order['mid'], $point);
            }

            //退还优惠券
            $order['coupon_id'] && (new CouponLogService())->update($order['coupon_id'], ['status' => 0, 'oid' => 0]);

            //修改订单状态
            OrderService::init('wid', session('wid'))->where(['id' => $id])->update(['id' => $id, 'status' => 4], false);

            list($orderDetails) = OrderDetailService::init()->where(['oid' => $id])->getList(false);
            foreach ($orderDetails['data'] as $item) {
                $num = $item['num'];
                if (!empty($item['product_prop_id'])) {
                    //更改有规格商品 数据库库存
                    ProductSku::where('id', $item['product_prop_id'])->increment('stock', $num);
                    (new SkuRedis())->incr($item['product_prop_id'], 'stock', $num);
                }

                //更新商品
                Product::where('id', $item['product_id'])->increment('stock', $num);
                (new ProductRedis())->incr($item['product_id'], 'stock', $num);
            }

            $orderLog = [
                'oid' => $id,
                'wid' => session('wid'),
                'mid' => $order['mid'],
                'action' => 16,
                'remark' => $input['remark'],
            ];
            OrderLogService::init()->add($orderLog, false);
            OrderService::init('wid', session('wid'))->upOrderLog($id, session('wid'));

            //如果是秒杀订单 需要返还秒杀库存 Herry
            if (!empty($order['seckill_id'])) {
                (new SeckillModule())->returnSeckillStock($order['id'], $order['seckill_id']);
            }

            success();
        }
        return view('merchants.order.clearOrder', array(
            'id' => $id,
        ));
    }

    //修改订单价格
    public function upOrderPrice($id)
    {
        return view('merchants.order.upOrderPrice', array(
            'id' => $id,
        ));
    }

    /**
     * 核销节点详情
     * @param  $id  订单id
     * @return $notify_id 消息id
     * @return view 视图
     */
    public function stateMentDetail($id, $notify_id = 0)
    {
        $returnData = [];
        //区分消息已读未读，故引入nitify_id hsz 2018/6/25
        if ($notify_id) {
            (new NotificationModule())->setReadNotification($notify_id);
        }
        /* 查询订单详情 */
        $detail = OrderService::init('wid', session('wid'))->getInfo($id);
        foreach ($detail['orderDetail'] as &$pro) {
            $pro['coupon_price'] = $pro['price'] - $pro['after_discount_price'];
            $pro['goods_subtotal'] = $pro['after_discount_price'] * $pro['num'];
        }
        return view('merchants.order.stateMentDetail', array(
            'title' => '结算详情',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderDetail',
            'detail' => $detail
        ));
    }

    /**
     * 订单-改价
     * @param Request $request
     * @return json
     * @author Herry
     * @since 2018/6/20
     * @update 张永辉 2018年6月28日   修改改价逻辑，直接修改最终价格不需要计算
     */
    public function changePrice(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'id' => 'required|integer',
            'changePrice' => 'required|numeric',
            'freightPrice' => 'required|numeric',
        );
        $message = Array(
            'id.required' => '订单ID不能为空',
            'changePrice.required' => '修改价格不能为空',
            'freightPrice.required' => '运费价格不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        if ($input['changePrice'] < 0 || $input['freightPrice'] < 0) {
            return myerror('价格必须大于0');
        }

        $where = [
            'id' => $input['id'],
            'wid' => session('wid')
        ];

        //获取订单
        $order = OrderService::init('wid', session('wid'))->getInfo($input['id']);
        if (empty($order)) {
            return myerror('订单不存在');
        }
        if ($order['status'] != '0') {
            return myerror('只有待付款订单才能修改价格');
        }

        $reduce = bcsub($input['changePrice'], $order['pay_price'], 2);
        $pay_price = bcadd($input['changePrice'], $input['freightPrice'], 2);
        $update = [
            'change_price' => $reduce,
            'freight_price' => $input['freightPrice'],
            'pay_price' => $pay_price
        ];
        OrderService::init('wid', session('wid'))->where($where)->update($update, false);

        //新增一条改价日志
        $log = [
            'oid' => $order['id'],
            'wid' => $order['wid'],
            'mid' => $order['mid'],
            'action' => 15,
            'remark' => '[订单改价] 改价: ' . $reduce . ', 邮费: ' . $input['freightPrice']
        ];
        OrderLogService::init('wid', session('wid'))->add($log, false);
        OrderService::upOrderLog($order['id'], $order['wid']);

        return mysuccess('订单改价成功');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170504
     * @desc 订单发货
     * @param Request $request
     * @param ExpressService $expressService
     * @param LogisticsService $logisticsService
     * @update 何书哲 2018年6月28日 创建订单打单service实例；获取对应订单的打单列表；修改对应的打单记录的使用状态
     * @update 梅杰 2018年8月8日 卡密订单单独处理
     * @update 梅杰 2018年10月18日 发货提醒
     * @update 何书哲 2020年03月07日 补丁：如果订单商品全部已发货或退款成功,订单自动发货
     */
    public function delivery(Request $request, ExpressService $expressService, LogisticsService $logisticsService)
    {
        $input = $request->input();
        // 何书哲 2018年6月28日 创建订单打单service实例
        $orderLogisticsService = new OrderLogisticsService();
        if ($request->isMethod('post')) {
            $rule = Array(
                'oid' => 'required',
                'odid' => 'required',
            );
            // 是否选择 无需物流 change MayJay
            if ($input['no_express'] == 0) {
                $rule['logistic_no'] = 'required';
                $rule['express_id'] = 'required';
            }
            // END
            $message = Array(
                'oid.required' => '订单ID不能为空',
                'logistic_no.required' => '物流单号不能为空',
                'express_id.required' => '快递公司ID不能为空',
                'odid.required' => '订单详情ID不能为空',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $input['odid'] = json_decode($input['odid'], true);
            if (empty($input['odid']) || !is_array($input['odid'])) {
                error('请选择发货商品');
            }
            $orderData = OrderService::init()->getInfo($input['oid']);
            if ($request->input('type', 0) == 12) {
                // 检查库存是否充足
                $re = OrderDetailService::init()->where(['oid' => $input['oid'], 'is_delivery' => 0])->with(['product'])->getList(false);
                $detail = $re['0']['data'] ? $re[0]['data'][0] : error();
                // 获取可用的卡密
                $camService = new CamListService();
                if ($detail['num'] > $camService->leftStock($detail['product']['cam_id'])) {
                    error('库存不足');
                }
                $this->dispatch((new sendCodeKeyProduct($orderData['wid'], $orderData['mid'], $orderData['id']))->onQueue('sendCdKey'));
                success('系统正在发货处理中，请稍等');
            }
            // 需要物流 change Mayjay
            if ($input['no_express'] == 0) {
                if ($input['express_id'] <= 0) {
                    error('请选择物流公司');
                }
                $express = $expressService->getRowById($input['express_id']);
                if (!$express) {
                    error('物流公司不存在');
                }
                $logistics = [
                    'logistic_no' => $input['logistic_no'],
                    'express_id' => $input['express_id'],
                    'oid' => $input['oid'],
                    'odid' => implode(',', $input['odid']),
                    'express_name' => $express['title'],
                    'word' => $express['word'],
                ];
            } else {
                $logistics = [
                    'oid' => $input['oid'],
                    'odid' => implode(',', $input['odid']),
                    'no_express' => 1,
                ];
            }

            $res = $logisticsService->init()->model->where('oid', $input['oid'])->where('odid', implode(',', $input['odid']))->first();
            if ($res) {
                error('亲!该订单的该商品已经发货了!');
            }

            $logistics_id = $logisticsService->init()->add($logistics, false);

            if ($logistics_id) {
                foreach ($input['odid'] as $val) {
                    OrderDetailService::init()->where(['id' => $val])->update(['id' => $val, 'is_delivery' => 1, 'delivery_time' => time()], false);
                }
            }
            // 订单中每个商品的退款状态 Herry
            $ids = [];
            $query = OrderService::init('wid', session('wid'))->model->find($input['oid']);
            if (!$query) {
                error('订单号不存在');
            }
            $data['order'] = $query->load('orderDetail')->toArray();
            $refundService = new OrderRefundService();
            foreach ($data['order']['orderDetail'] as $k => $detail) {
                $refund = $refundService->init('oid', $input['oid'])->where(['oid' => $input['oid'], 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
                if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                    $ids[] = $detail['id'];
                }
            }
            $condition = ['oid' => $input['oid'], 'is_delivery' => 0];
            if ($ids) {
                $condition['id'] = ['not in', $ids];
            }
            list($data) = OrderDetailService::init()->where($condition)->getList();
            if (!$data['data']) {
                $where['id'] = $input['oid'];
                $where['wid'] = session('wid');
                OrderService::init('wid', session('wid'))->where($where)->update(['status' => 2], false);
                $orderLog = [
                    'oid' => $input['oid'],
                    'wid' => session('wid'),
                    'mid' => $orderData['mid'],
                    'action' => 3,
                    'remark' => '商家发货',
                ];
                OrderLogService::init()->add($orderLog, false);
            }
            OrderService::upOrderLog($input['oid'], session('wid'));
            // 何书哲 2018年6月28日 修改对应的打单记录（order_logistics数据表）的使用状态
            if ($orderLogistics = $orderLogisticsService->getRowByWhere(['oid' => $input['oid'], 'kuaidi_num' => $input['logistic_no']])) {
                $orderLogisticsService->update($orderLogistics['id'], ['is_used' => 1]);
            }
            // 发送发货通知
            $orderData['source'] == 0 && (new MessagePushModule(session('wid'), MessagesPushService::DeliverySuccess))->sendMsg(['oid' => $input['oid'], 'odid' => $logistics['odid']]);

            // 小程序发货通知改为发送订阅模板消息 吴晓平 2019年12月19日 14:35:44
            if ($orderData['source'] == 1) {
                // 模板发送的初步数据
                $data = [
                    'wid' => $orderData['wid'],
                    'openid' => '',
                    'param' => [
                        'flag' => 1,
                        'oid' => $input['oid'],
                    ]
                ];
                // 发送模板的相关内容
                $param = [
                    'mid' => $orderData['mid'],
                    'order_num' => $orderData['oid'],
                    'ship_time' => Carbon::now()->toDateTimeString(),
                    'express_company' => $input['no_express'] ? '无需物流' : $express['title'] ?? '',
                    'express_no' => $input['no_express'] ? 'no order number required' : $input['logistic_no'] ?? 0,
                ];
                $handData = app(MessagesPushService::class)->handDbData($orderData['wid'], MessagesPushService::DeliverySuccess);
                // 只有开启小程序模板发送才会发送订阅消息
                if (in_array(4, $handData['config'])) {
                    // 组装后的数据
                    $sendData = app(SubscribeMessagePushService::class)->packageSendData(7, $data);
                    $this->dispatch(new SubMsgPushJob(7, $orderData['wid'], $sendData, $param));
                }

            }
        }


        $query = OrderService::init('wid', session('wid'))->model->find($input['oid']);
        if (!$query) {
            error('订单号不存在');
        }
        $data['order'] = $query->load('orderDetail')->toArray();

        // 是否可以订单自动发货成功
        $isAutoComplete = true;
        // 订单中每个商品的退款状态 Herry
        $refundService = new OrderRefundService();
        foreach ($data['order']['orderDetail'] as $k => &$detail) {
            $refund = $refundService
                ->init('oid', $input['oid'])
                ->where([
                    'oid' => $input['oid'],
                    'pid' => $detail['product_id'],
                    'prop_id' => $detail['product_prop_id']
                ])
                ->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                unset($data['order']['orderDetail'][$k]);
                continue;
            }
            if ($detail['is_delivery'] == 0) {
                $isAutoComplete = false;
            }
        }
        // update 何书哲 2020年03月07日 补丁：如果订单商品全部已发货或退款成功,订单自动发货
        if ($isAutoComplete) {
            OrderService::init('wid', session('wid'))
                ->where(['id' => $input['oid'], 'wid' => session('wid')])
                ->update(['status' => 2], false);
            $orderLog = [
                'oid' => $input['oid'],
                'wid' => session('wid'),
                'mid' => $data['order']['mid'],
                'action' => 3,
                'remark' => '商家发货',
            ];
            OrderLogService::init()->add($orderLog, false);
            OrderDetailService::init()->model
                ->where(['oid' => $input['oid'], 'is_delivery' => 0])
                ->update(['is_delivery' => 1, 'delivery_time' => time()], false);
            error('订单已发货，请刷新后重试');
        }

        $data['order']['orderDetail'] = array_values($data['order']['orderDetail']);
        $data['express'] = array_values($expressService->getListWithoutPage());
        $logistics = (new LogisticsService())->getByOid($input['oid']);
        foreach ($data['order']['orderDetail'] as &$item) {
            $default = ['id' => '', 'logistic_no' => '', 'express_name' => '',];
            $item['logistics'] = $logistics[$item['id']] ?? $default;
            // add by 张国军 2018年08月10日 查询出卡密商品对应的库存和活动名称
            $item['carmStock'] = 0;
            $item['carmActivityName'] = "-";
            if (isset($data['order']['type']) && $data['order']['type'] == 12) {
                // 查询该商品对应的卡密
                $productData = ProductService::getDetail($item['product_id']);
                if (!empty($productData) && isset($productData['cam_id']) && $productData['cam_id'] > 0) {
                    // 查询该卡密id 对应的名称
                    $camActivity = (new CamActivityService())->getRowById($productData['cam_id']);
                    if (!empty($camActivity) && isset($camActivity['title'])) {
                        $item['carmActivityName'] = $camActivity['title'];
                    }
                    // 查询该卡密库存
                    $camList = (new CamListService())->countStock($productData['cam_id']);
                    if (!empty($camList) && isset($camList['leftTotal'])) {
                        $item['carmStock'] = $camList['leftTotal'];
                    }
                }
            }
        }
        // 何书哲 2018年6月28日 获取对应订单的打单列表（order_logistics数据表）
        $logistics_list = [];
        $logisticsRes = (new OrderLogisticsService())->model->where(['oid' => $input['oid'], 'is_used' => 0])->get(['express_id', 'kuaidi_num'])->toArray();
        foreach ($logisticsRes as $key => $val) {
            $flag = false;
            foreach ($logistics_list as $k => $v) {
                if ($val['express_id'] == $v['express_id']) {
                    $logistics_list[$k]['kuaidi_num'][] = $val['kuaidi_num'];
                    $flag = true;
                    continue;
                }
            }
            if ($flag == false) {
                $logistics_list[] = ['express_id' => $val['express_id'], 'kuaidi_num' => [$val['kuaidi_num']]];
            }
        }
        $data['logistics_list'] = $logistics_list;

        success('', '', $data);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170504
     * @desc 获取物流信息
     * @param LogisticsService $logisticsService
     * @param $id
     */
    public function getLogistics(LogisticsService $logisticsService, $id)
    {
        $res = $logisticsService->getLogistics($id);
        if ($res['success'] == 1) {
            success('', '', $res['data']);
        } else {
            error($res['message']);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170505
     * @desc 商家修改物流
     * @param Request $request
     * @param LogisticsService $logisticsService
     */
    public function modifyLogistics(Request $request, LogisticsService $logisticsService, ExpressService $expressService, $oid)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = Array(
                'data' => 'required',
            );
            $message = Array(
                'data.required' => '请传递修改信息',
            );

            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $data = $input['data'];
            foreach ($data as $val) {
                // 是无需要物流 change MayJay
                if ($val['no_express'] == 1) {
                    $tmp = [
                        'id' => $val['id'],
                        'logistic_no' => null,
                        'express_id' => null,
                        'express_name' => null,
                        'word' => null,
                        'logistic_log' => null,
                        'no_express' => 1
                    ];
                } else {
                    $expressTemp = $expressService->getRowById($val['express_id']);
                    if (!$expressTemp) {
                        return myerror();
                    }
                    if (!$val['logistic_no']) {
                        error('请填写订单号');
                    }
                    $tmp = [
                        'id' => $val['id'],
                        'logistic_no' => $val['logistic_no'],
                        'express_id' => $val['express_id'],
                        'express_name' => $expressTemp['title'],
                        'word' => $expressTemp['word'],
                        'no_express' => 0,
                    ];
                }
                $logisticsService->init()->where(['id' => $val['id']])->update($tmp, false);
            }

            //修改订单表的地址快照信息 Herry
            $update = [
                'address_province' => $input['address_province'],
                'address_city' => $input['address_city'],
                'address_area' => $input['address_area'],
                'address_name' => $input['address_name'],
                'address_phone' => $input['address_phone'],
                'address_detail' => $input['address_province'] . $input['address_city'] . $input['address_area'] . $input['address_detail'],
            ];

            $input['address_id'] && $update['address_id'] = $input['address_id'];

            OrderService::init('wid', session('wid'))
                ->where(['id' => $oid])
                ->update($update, false);

            success();
        }
        $logistics = $logisticsService->init()->model->where(['oid' => $oid])->get()->toArray();
        foreach ($logistics as &$val) {
            $val['num'] = count(explode(',', $val['odid']));
        }
        $data['logistics'] = $logistics;
        $data['express'] = array_values($expressService->getListWithoutPage());

        //返回收货信息 Herry
        $orderData = OrderService::init('wid', session('wid'))->getInfo($oid);
        if ($orderData) {

            //获取地区
            $regions = $this->regionService->getAll();
            foreach ($regions as $key => $item) {
                if ($item['status'] == -1) {
                    unset($regions[$key]);
                }
            }
            foreach ($regions as $value) {
                $regionList[$value['pid']][] = $value;
            }
            //对省份进行排序
            $provinceList = $regionList[-1];
            $orderData['address']['regions_data'] = json_encode($regionList);
            $orderData['address']['regionList'] = $regionList;
            $orderData['address']['provinceList'] = $provinceList;
        }
        $data['order'] = $orderData ?: [];

        success('', '', $data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170505
     * @desc 设置延期收货
     * @param $id
     */
    public function delay($oid)
    {
        $wid = session('wid');
        $orderData = OrderService::init('wid', $wid)->model->find($oid)->load('orderLog')->toArray();
        //判断该订单是否可以延期收货
        if ($orderData['status'] != 2) {
            error('该订单暂时无法延期收货');
        }
        //判断是否可以延期
        foreach ($orderData['orderLog'] as $val) {
            if ($val['action'] == 13) {
                error('亲！你已申请过延期了！');
            } elseif ($val['action'] == 3) {
                $day = (int)((time() - strtotime($val['created_at'])) / 86400);
                $autoReceiveDays = config('app.auto_confirm_receive_days');
                if (($autoReceiveDays - 3) > $day) {
                    error('亲！发货后' . ($autoReceiveDays - 3) . '天后才能延期收货哦');
                }
            }
        }
        $orderLog = [
            'oid' => $oid,
            'wid' => $wid,
            'mid' => session('mid'),
            'action' => 13,
            'remark' => '商家申请延期',
        ];
        OrderLogService::init('wid', session('wid'))->add($orderLog, false);
        OrderService::init('wid', session('wid'))->upOrderLog($oid, session('wid'));
        success();
    }

    /**
     * 订单-同意买家退货
     * @param Request $request
     * @param int $refundID 退款ID
     * @param int $oid 订单ID
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function refundAgreeReturn(Request $request, $refundID, $oid)
    {
        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->agreeReturn($request, $oid, session('wid'), $refundID);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                success('同意退货成功');
            } else {
                error($resultArr['errMsg']);
            }
        } else {
            error('只允许POST提交');
        }
    }

    /**
     * 订单-同意买家退款
     * @param Request $request
     * @param int $refundID 退款ID
     * @param int $oid 订单ID
     * @param int $pid 商品ID
     * @return json
     * @author Herry
     * @since 2018/6/21
     */
    public function refundAgree(Request $request, $refundID, $oid, $pid)
    {
        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->sellerAgree($oid, session('wid'), $refundID);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                success('同意退款成功');
            } else {
                error($resultArr['errMsg']);
            }
        } else {
            error('只允许POST提交');
        }
    }

    /**
     * 订单-拒绝买家退款
     * @param Request $request
     * @param int $refundID 退款ID
     * @param int $oid 订单ID
     * @param int $pid 商品ID
     * @return json
     * @author Herry
     * @since 2018/6/21
     */
    public function refundDisagree(Request $request, $refundID, $oid, $pid)
    {
        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->sellerDisagree($request, $oid, session('wid'), $refundID);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                success('拒绝退款成功');
            } else {
                error($resultArr['errMsg']);
            }
        } else {
            error('只允许POST提交');
        }
    }

    /**
     * 订单-商家同意退款后的打款操作(目前已弃用)
     * @param int $oid 订单ID
     * @param int $pid 商品ID
     * @return json
     * @author Herry
     * @since 2018/6/21
     */
    public function refundComplete($oid, $pid)
    {
        $wid = session('wid');
        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            error('订单不存在');
        }

        //判断订单状态是否是打款状态
        if ($orderData['refund_status'] != 3) {
            error('该订单不在可打款状态');
        }

        $orderRefundService = new OrderRefundService();
        $orderRefundService->completeRefund($wid, $oid, $pid);

        success('打款成功');
    }

    public function getDetail($id)
    {
        if (empty(session('wid'))) {
            error('请选择店铺');
        }
        if (empty($id)) {
            error('参数不完整');
        }

        $order = OrderService::orderDetail(session('wid'), $id);
        if (empty($order)) {
            error('订单异常');
        }

        success('', '', $order);
    }

    /**
     * 订单-维权详情
     * @param int $oid 订单ID
     * @param int $pid 商品ID
     * @param int $propID 规格ID
     * @return view
     * @author Herry
     * @since 2018/6/21
     */
    public function refundDetail($oid, $pid, $propID)
    {
        $wid = session('wid');
        //订单详情
        $orderData = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($orderData)) {
            error('订单不存在');
        }

        $orderRefundService = new OrderRefundService();
        $refund = $orderRefundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $pid, 'prop_id' => $propID])->getInfo();
        if (empty($refund)) {
            error('退款不存在');
        }

        $refund_first = $refund;

        //有修改则获取最新修改的申请
        $refund_last = (new RefundModule())->getLatestEditApply($refund);

        //商品详情
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            error('商品不存在');
        }

        //获取申请退款7天后时间
        $refundEndTimestamp = date('Y-m-d H:i:s', strtotime($refund['updated_at']) + 7 * 24 * 3600);

        //是否包含运费
        $detailCount = count($orderData['orderDetail']);
        $hasRefundCount = $orderRefundService->init('oid', $oid)->model->where(['oid' => $oid])->whereIn('status', [4, 8])->count();
        $refund_last['freight'] = 0;
        if ($hasRefundCount == $detailCount - 1) {
            //订单中所有商品都退款完成 且最后一个商品还未发货 则退运费
            foreach ($orderData['orderDetail'] as $detail) {
                $where = [
                    'oid' => $detail['oid'],
                    'product_id' => $pid,
                    'product_prop_id' => $propID
                ];
                $orderDetailData = OrderDetailService::init()->model->wheres($where)->get()->toArray();
                $orderDetailData = $orderDetailData ? $orderDetailData[0] : [];
                if ($orderDetailData && $orderDetailData['is_delivery'] == 0 && $orderDetailData['oid'] == $oid && $orderDetailData['product_id'] == $pid && $orderDetailData['product_prop_id'] == $propID) {
                    $refund_last['freight'] = $orderData['freight_price'];
//                    $refund['amount'] += $refund['freight'];
                    $refund_last['amount'] = sprintf('%.2f', $refund_last['amount']);
                    break;
                }
            }
        }

        return view('merchants.order.refundDetail', array(
            'title' => '维权详情',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderDetail',
            'order' => $orderData,
            'messages' => isset($refund['id']) ? (new RefundModule())->getMessages($wid, $refund['id']) : [],
            'refund' => $refund_last,
            'buyer' => $this->memberService->getRowById($orderData['mid']),
            'refundEndTimestamp' => $refundEndTimestamp,
            'product' => $product,
            'refund_first' => $refund_first
        ));
    }

    /**
     * 打印快递单
     */
    public function printExpress()
    {
        return view('merchants.order.printExpress', array(
            'title' => '维权详情',
            'leftNav' => $this->leftNav,
            'slidebar' => 'orderDetail'
        ));
    }


    /**
     * 打印快递单接口
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function printExpressApi(Request $request, RegionService $regionService, ShopService $shopService)
    {
        //获取要打印的订单（批量打印）
        $input = $request->input();
        $orderIds = $input['orderIds'];

        //查询订单的收货信息
        $where['oid'] = array('in', $orderIds);
        list($orderData) = OrderService::init('wid', session('wid'))->where($where)->getList();
        $result = [];
        //$weixinService = D('Weixin', 'uid', session('userInfo')['id']);
        $with = ['WeixinAddress'];
        foreach ($orderData['data'] as $key => $val) {
            if ($val['status'] != 1) {
                error('只能打印待发货订单');
            }
            //店铺信息
            //$weixinData = $weixinService->with($with)->getInfo($val['wid']);
            $weixinData = $shopService->getRowWithRelation($val['wid']);
            //地区信息
            $region = $regionService->getListById([$weixinData['province_id'], $weixinData['city_id'], $weixinData['area_id']]);
            $address = '';
            $city = '';
            $shipperAddress = (new WeixinRefundService())->getDefaultAddress(session('wid'), session('userInfo'), 2);//add fuguowei
            foreach ($region as $v) {
                $address .= $v['title'];
            }

            //寄件人
            $result[$key]['shipper-name'] = $weixinData['shop_name'];
            $result[$key]['shipper-city'] = $shipperAddress['province_title'] ?? '';  ////xiugai  fuguowei 20170116
            $result[$key]['shipper-company'] = $weixinData['company_name'];
            $result[$key]['shipper-address'] = $shipperAddress['address'] ?? '';   //xiugai  fuguowei 20170116
            $result[$key]['shipper-phone'] = session('userInfo')['mphone'];
            //寄件人签署时间 使用用户下单时间
            $payLog = OrderLogService::init()->model->where('oid', $val['id'])->where('action', 2)->first();
            if (empty($payLog)) {
                error('订单' . $val['oid'] . '没有支付记录');
            }
            $result[$key]['shipper-time'] = substr($payLog->created_at, 0, 10);

            //收件人
            $result[$key]['consignee-name'] = $val['address_name'];
            $result[$key]['consignee-city'] = $val['address_city'];
            $result[$key]['consignee-phone'] = $val['address_phone'];
            $result[$key]['consignee-address'] = $val['address_detail'];
        }
        success('', '', $result);
    }

    /**
     * 订单-退款-添加留言
     * @param Request $request
     * @param int $refundID 退款ID
     * @param int $oid 订单ID
     * @return json
     * @author Herry
     * @since 2018/6/21
     */
    public function refundAddMessage(Request $request, $refundID, $oid)
    {
        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->addMessage($request, $oid, session('wid'), $refundID, 1);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                success('添加留言成功');
            } else {
                error($resultArr['errMsg']);
            }
        } else {
            error('只允许POST提交');
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170531
     * @desc 获取订单分销详情
     * @param $oid
     */
    public function getDistribute($oid)
    {
        $wid = session('wid');
        $res = Income::where('wid', $wid)->where('oid', $oid)->get()->load('member')->toArray();
        return mysuccess('操作成功', '', $res);
    }


    /**
     * author: meijie
     * 批量发货打入文件接口
     */
    public function BatchDelivery(OrderModule $module)
    {
        $filePath = $_FILES['info']['tmp_name'];
        $content = file_get_contents($filePath);
        $fileType = mb_detect_encoding($content, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));//获取当前文本编码格式
        $rows = [];
        Excel::load($filePath, function ($reader) use (&$rows) {
            $rows = $reader->get()->toArray();
        }, $fileType);
        !$rows && error('上传数据为空');
        //存在数据
        $re = $module->BatchDelivery(session('wid'), $rows);
        success('操作成功', '', $re);
    }

    /**
     * 批量发货模板
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author: 梅杰 2018年8月27日
     */
    public function BatchDeliveryTemplate()
    {
        $filePath = './hsshop/exports/batch.csv';
        return response()->download($filePath, '批量发货模板' . '.csv');  //下载
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function addEvaluateClassify(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'title' => 'required',
        );
        $message = Array(
            'title.required' => '分类名称不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $data = [
            'wid' => session('wid'),
            'title' => $input['title'],
        ];

        $res = (new EvaluateClassifyService())->add($data);
        if ($res) {
            success();
        } else {
            error();
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171019
     * @desc 获取订单评价分类接口
     */
    public function getEvaluateClassify()
    {
        $res = (new EvaluateClassifyService())->getList(['wid' => session('wid')]);
        success('操作成功', '', $res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171019
     * @desc 添加订单分类
     */
    public function addProductEvaluateClassify(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'pid' => 'required',
            'eid' => 'required',
            'classify_name' => 'required',
        );
        $message = Array(
            'pid.required' => '产品id不能为空',
            'eid.required' => '评论id不能为空',
            'classify_name.required' => '分类名称不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $productEvaluateClassifyService = new ProductEvaluateClassifyService();
        foreach ($input['classify_name'] as $val) {
            $data = [
                'pid' => $input['pid'],
                'eid' => $input['eid'],
                'classify_name' => $val,
            ];
            $productEvaluateClassifyService->add($data);
        }
        success();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171020
     * @desc 使订单成团
     * @param $gid
     */
    public function makeCompleteGroups($oid)
    {
        $orderData = OrderService::init()->getInfo($oid);
        if (!$orderData) {
            error('订单不存在');
        }
        if ($orderData['groups_id'] == 0) {
            error('订单是非团购订单');
        }
        if ($orderData['groups_status'] == 2) {
            error('该订单已成团');
        }
        $data = [
            'groups_status' => 2
        ];
        list($orderList) = OrderService::init()->where(['groups_id' => $orderData['groups_id']])->getList();
        foreach ($orderList['data'] as $key => $value) {
            OrderService::init()->where(['id' => $value['id']])->update($data, false);
        }
        $groupsService = new GroupsService();
        $res = $groupsService->getRowById($orderData['groups_id']);

        if ($res) {
            $data = [
                'status' => 2,
                'complete_time' => date("Y-m-d H:i:s", time()),
            ];
            $groupsService->update($orderData['groups_id'], $data);
            //Add MayJay 成团提醒
            //获取所有参团人信息

            $groupsDetail = (new GroupsDetailService())->getListByWhere(['groups_id' => $orderData['groups_id']]);

            $mids = array_column($groupsDetail, 'member_id');
            $oids = array_column($groupsDetail, 'oid');

            // 订单详情
            $orderDetail = OrderDetail::query()->where('oid', $oid)->get(['oid', 'price']);
            if ($orderDetail->isEmpty()) {
                error('该订单数据异常');
            }

            // 拼团活动规则
            $groupsRule = GroupsRule::query()->find($res['rule_id'], ['id', 'title', 'groups_num']);
            if (empty($groupsRule)) {
                error('拼团活动规则不存在或已被删除');
            }

            foreach ($mids as $key => $mid) {
                // @update 吴晓平 2019年12月23日 17:21:47 把小程序拼团发送模板消息改为发送订阅模板消息
                // 模板发送的初步数据
                $data = [
                    'wid' => $orderData['wid'],
                    'openid' => '',
                    'param' => [
                        'oid' => $oids[$key],
                        'groups_id' => $orderData['groups_id'],
                    ]
                ];

                // 发送模板的相关内容
                $param = [
                    'mid' => $mid,                    // 拼团用户id
                    'title' => $groupsRule->title,  // 拼团活动名称
                    'oid' => $oids[$key],    // 对应拼团订单id
                    'groups_num' => $groupsRule->groups_num, // 参团人数
                    'notice' => '拼团成功，商家正在努力发货，请耐心等待'
                ];
                // 组装后的数据
                $sendData = app(SubscribeMessagePushService::class)->packageSendData(2, $data);
                $this->dispatch(new SubMsgPushJob(2, $orderData['wid'], $sendData, $param));
            }

            $res = (new GroupsDetailService())->getListByWhere(['groups_id' => $orderData['groups_id'], 'is_head' => '1']);
            $detailData = current($res);
            dispatch((new SendGroupsLog($detailData['id'], '3'))->onQueue('SendGroupsLog'));  //发送成团数据到数据中心队列
            dispatch((new SendTakeAway($oid)));//何书哲 2018年11月15日 外卖订单导入第三方

        }

        success();
    }


    /**
     * @author 付国维
     * @date 20171103
     * @desc 订单批量导出,导出格式.xls
     */
    public function orderExport(Request $request)
    {
        //接受数据
        $input = $request->input();

        //对传输的数据进行遍历转换
        foreach ($input as $k => $v) {

            $data = $v;
        }

        //转化为数组
        $id = explode(',', $data);
        //var_dump($id);die;
        //获取店铺名字
        $wid = session('wid');

        //查询数据
        $list = OrderService::orderDetail($wid, $id);
        //执行导出
        OrderService::exportExcel1($list);
    }


    /**
     * 打印销售单
     */
    public function salePrint()
    {
        return view('merchants.order.salePrint', array(
            'title' => '销售单'
        ));
    }


    /**
     * 打印销售单接口
     */
    public function salePrintApi(Request $request)
    {
        //接受数据
        $input = $request->input();

        //获取店铺名字
        $wid = session('wid');
        $id = $input["orderIds"];


        //查询数据
        $list = OrderService::orderDetail($wid, $id);

        //对查询的数据进行循环
        foreach ($list as $key => $val) {
            $memberInfo = (new MemberService())->getRowById($val['mid']);
            $result[$key]['create_at'] = $val['created_at'];
            $result[$key]['oid'] = $val['oid'];
            $result[$key]['truename'] = $memberInfo['truename'];   ////


            $result[$key]['products_price'] = $val['products_price'];
            $result[$key]['coupon_price'] = $val['coupon_price'];
            $result[$key]['freight_price'] = $val['freight_price'];
            $result[$key]['pay_price'] = $val['pay_price'];
            $result[$key]['address_detail'] = $val['address_detail'];
            $result[$key]['address_phone'] = $val['address_phone'];
            $result[$key]['buy_remark'] = $val['buy_remark'];
            $result[$key]['seller_remark'] = $val['seller_remark'];

            foreach ($val['orderDetail'] as $k => $v) {
                $result[$key]['detail'][$k]['title'] = $v['title'];
                $result[$key]['detail'][$k]['num'] = $v['num'];
                $result[$key]['detail'][$k]['spec'] = explode(',', $v['spec']) ?? [];
                $result[$key]['detail'][$k]['price'] = $v['price'];
                $result[$key]['detail'][$k]['money'] = $v['price'] * $v['num'];
                $result[$key]['detail'][$k]['jian'] = '件';
                $result[$key]['detail'][$k]['buy_message'] = $v['buy_message'];
                $result[$key]['detail'][$k]['product_id'] = "$key" + 1;
            }
        }

        success('', '', $result);
    }

    /**
     * @author 付国维
     * @date 20171103
     * @desc 订单批量导出,导出格式.csv
     * @update 何书哲 2019年10月10日 查询订单数据添加额外关联字段标志
     */
    public function orderExportCsv(Request $request)
    {
        // 接受数据
        $input = $request->input();

        // 对传输的数据进行遍历转换
        foreach ($input as $k => $v) {
            $data = $v;
        }

        // 转化为数组
        $id = explode(',', $data);

        // 获取店铺名字
        $wid = session('wid');

        // 查询数据
        $list = OrderService::orderDetail($wid, $id, true);

        // 设置文件名
        $filename = '订单导出' . date('YmdHis') . '.xls';
        // 导出
        OrderService::exportExcel($list, 'sendProduct');
    }

    /**
     * [getShareEventMember description]
     * @return [type] [description]
     */
    public function getShareEventMember(Request $request, ShareEventService $shareEventService, ShareEventRecordService $shareEventRecordService)
    {
        $oid = $request->input('oid') ?? 0;
        $page = $request->input('page') ?? 1;
        $wid = session('wid');
        if (!$oid) {
            error('请先选择相关订单');
        }
        $detail = OrderService::init('wid', $wid)->getInfo($oid);
        if (empty($detail)) {
            error('订单不存在或已被删除');
        }
        $where['wid'] = $wid;
        $where['share_event_id'] = $detail['share_event_id'];
        $where['source_id'] = $detail['mid'];
        $returnData = $shareEventRecordService->getAllListSort($where, $page);
        if ($returnData['list']) {
            $address = '';
            foreach ($returnData['list'] as $key => &$value) {
                $memberInfo = (new MemberService())->getRowById($value['actor_id']);
                $address .= $memberInfo['province'];
                $address .= $memberInfo['city'];
                $value['address'] = $address;
                $value['derate_time'] = date('Y-m-d H:i:s', $value['created_at']);
            }
        }

        return $returnData;

    }

    /**
     * 订单删除
     *
     * @author
     * @version
     */
    public function setAdminDel(Request $request)
    {
        $input = $request->input();
        $id = $input["orderIds"];
        $where['id'] = ['in', $id];
        $where['wid'] = session('wid');
        OrderService::init('wid', session('wid'))->wheres($where)->update(['admin_del' => 1]);
    }

    /**
     * 标记退款完成
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年7月2日
     */
    public function manuallyRefundSuccess(Request $request)
    {
        // 参数判断
        $input = $request->input();
        if (empty($input['oid']) || empty($input['pid'])) {
            error('参数不完整');
        }

        $return = (new RefundModule())->manuallySuccess($input);
        if ($return['err_code']) {
            error($return['err_msg']);
        } else {
            success();
        }
    }

    /**
     * 快速打单页面渲染
     * @param Request $request 请求参数
     * @return view
     * @create 何书哲 2018年6月26日 快速打单页面渲染
     */
    public function printOrder(Request $request)
    {
        return view('merchants.order.printOrder', array(
            'title' => '快速打单',
            'leftNav' => $this->leftNav,
            'slidebar' => 'printOrder',
        ));
    }

    /**
     * 获取/编辑快递100配置参数
     * @param Request $request 请求参数
     * @return json
     * @desc GET:获取快递100配置参数 POST:设置快递100配置参数
     * @create 何书哲 2018年6月28日 获取/编辑快递100配置参数
     */
    public function printOrderParams(Request $request, ShopService $shopService)
    {
        //从session获取店铺id
        $wid = session('wid');
        //如果是post方法，则设置快递100配置参数
        if ($request->isMethod('post')) {
            $inputs = $request->only(['print_type', 'kuaidi_app_id', 'kuaidi_app_secret', 'kuaidi_app_uid']);
            //去掉参数两边的空格
            if (isset($inputs['kuaidi_app_id'])) {
                $inputs['kuaidi_app_id'] = trim($inputs['kuaidi_app_id']);
            }
            if (isset($inputs['kuaidi_app_secret'])) {
                $inputs['kuaidi_app_secret'] = trim($inputs['kuaidi_app_secret']);
            }
            if (isset($inputs['kuaidi_app_uid'])) {
                $inputs['kuaidi_app_uid'] = trim($inputs['kuaidi_app_uid']);
            }
            $rules = array(
                'print_type' => 'required|in:1,2',
                'kuaidi_app_id' => 'required',
                'kuaidi_app_secret' => 'required',
                'kuaidi_app_uid' => 'required',
            );
            $messages = array(
                'print_type.required' => '打印类型不存在',
                'print_type.in' => '打印类型不正确',
                'kuaidi_app_id.required' => '快递100appid不能为空',
                'kuaidi_app_secret.required' => '快递100appsecret不能为空',
                'kuaidi_app_uid.required' => '快递100用户登录名不能为空',
            );
            $validator = Validator::make($inputs, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            //$res = WeixinService::init()->where(['id'=>$wid])->update($inputs, false);
            $res = $shopService->update($wid, $inputs);
            if ($res) {
                success('设置快递100参数成功');
            }
            error('设置快递100参数失败');
        }
        //获取快递100配置参数
        //$res = WeixinService::init()->getInfo($wid);
        $res = $shopService->getRowById($wid);
        if (!$res) {
            error('店铺不存在');
        }
        $data = [
            'print_type' => $res['print_type'],
            'kuaidi_app_id' => $res['kuaidi_app_id'],
            'kuaidi_app_secret' => $res['kuaidi_app_secret'],
            'kuaidi_app_uid' => $res['kuaidi_app_uid']
        ];
        success('获取快递100参数成功', '', ['data' => $data]);
    }

    /**
     * 快速打印快递单
     * @param Request $request 请求参数
     * @param array $orderIds 打印的订单id数组（例：[12345,23456,34567]）
     * @return json status: -3=>店铺不存在 -4=>打单参数未配置 -5=>商家发货地址不存在 -7：存在不能快速打单的订单 -8:未导入过快递管家 -9:已打单不能重复打单 -11:参数不能为空 -12:存在已关闭的订单 -13：存在退款中的订单 -14:已打单和退款中的订单 -15：已退款到账 -16：已完成订单
     * @create 何书哲 2018年6月28日 快速打印快递单
     */
    public function fastPrint(Request $request)
    {
        //从session获取店铺id
        $orderIds = $request->input('orderIds');
        $wid = session('wid');
        $module = new OrderLogisticsModule();
        if (empty($orderIds) || !is_array($orderIds)) {
            error('操作失败', '', ['status' => -11, 'message' => '参数不能为空']);
        }
        //店铺不存在或者店铺快递100打单参数未配置，直接返回
        $checkShopRes = $module->checkShopIfSend($wid);
        if ($checkShopRes['status'] != 0) {
            error('操作失败', '', $checkShopRes);
        }
        //检测订单id列表是否可以打印快递单
        $checkOrderIdsRes = $module->checkOrderIdsIfPrint($orderIds);
        if ($checkOrderIdsRes['status'] != 0) {
            error('操作失败', '', $checkOrderIdsRes);
        }
        $res = $module->fastPrint($wid, $orderIds);
        //订单已打印
        $isPrint = (new OrderLogisticsService())->getRowByWhere(['oid' => ['in', $orderIds]]);
        //存在退款中的订单
        $isRefund = $checkOrderIdsRes && isset($checkOrderIdsRes['data']) && isset($checkOrderIdsRes['data']['flag']) && $checkOrderIdsRes['data']['flag'] == 1;
        if ($isPrint && $isRefund) {
            $res['data']['status'] = -14;
        } elseif ($isPrint) {
            $res['data']['status'] = -9;
        } elseif ($isRefund) {
            $res['data']['status'] = -13;
        }
        success('操作成功', '', $res['data']);
    }

    /**
     * 订单异步导入快递100
     * @param Request $request 请求参数
     * @param array $orderIds 导入的订单id数组（例：[12345,23456,34567]）
     * @return json status: -1=>订单不存在 -2=>订单是自提订单 -3=>店铺不存在 -4=>打单参数未配置 -5=>商家发货地址不存在 -8=>已导入过快递管家，不能重复导入 -9=>不满足待发货条件，无法导入快递管家
     * @create 何书哲 2018年6月30日 订单异步导入快递100
     */
    public function importOrderLogistics(Request $request)
    {
        $orderIds = $request->input('orderIds');
        //从session获取店铺id
        $wid = session('wid');
        $module = new OrderLogisticsModule();
        if (empty($orderIds) || !is_array($orderIds)) {
            error('操作失败', '', ['status' => -11, 'message' => '参数不能为空']);
        }
        //检测店铺是否满足导入快递100条件
        $checkShopRes = $module->checkShopIfSend($wid);
        if ($checkShopRes['status'] != 0) {
            error('操作失败', '', $checkShopRes);
        }
        //检测订单是否满足导入快递100条件
        $checkOrderRes = $module->checkOrderIfSend($orderIds, 1);
        if ($checkOrderRes['status'] != 0) {
            error('操作失败', '', $checkOrderRes);
        }
        foreach ($orderIds as $oid) {
            $sendRes = $module->orderSend($wid, $oid);
            if ($sendRes['status'] != 200) {
                error('操作失败', '', $sendRes);
            }
        }
        success('操作成功', '', []);
    }


    /**
     * 批量发货视图
     * @param Request $request
     * @param BatchDeliveryLogService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年8月30日
     */
    public function batchDeliveryLog(Request $request, BatchDeliveryLogService $service)
    {
        $data = $service->getListPage(['wid' => $request->session()->get('wid')]);
        return view('merchants.order.batchDeliveryLog', array(
            'title' => '批量发货日志',
            'leftNav' => $this->leftNav,
            'slidebar' => 'batchDeliveryLog',
            'data' => $data
        ));

    }

    /**
     * 付款方式处理
     * @param $way
     * @return string
     * @author: 梅杰 2018年9月27日
     * @update 许立 2018年10月08日 增加小程序支付
     */
    public static function getPayWay($way)
    {

        switch ($way) {
            case 1:
                $payWay = '微信支付';
                break;
            case 2:
                $payWay = '支付宝支付';
                break;
            case 3:
                $payWay = '储值余额支付';
                break;
            case 4:
                $payWay = '货到付款/到店付款';
                break;
            case 5:
                $payWay = '找人代付';
                break;
            case 6:
                $payWay = '领取赠品';
                break;
            case 7:
                $payWay = '优惠兑换';
                break;
            case 8:
                $payWay = '银行卡支付';
                break;
            case 9:
                $payWay = '会员卡支付';
                break;
            case 10:
                $payWay = '小程序支付';
                break;
            default:
                $payWay = '未知支付';
                break;

        }
        return $payWay;
    }

    /**
     * 联系用户
     * @param Request $request
     * @author: 梅杰 2019/3/13 17:20
     */
    public function contactUser(Request $request)
    {
        $oid = $request->input('id', 0);
        if (!$order = Order::select(['id', 'wid', 'mid', 'source', 'xcx_config_id'])->find($oid)) {
            error('订单不存在');
        }
        $member = Member::select(['openid', 'xcx_openid'])->find($order->mid);
        if ($order->source == 0) { //发送公众号消息模板
            $data['touser'] = $member->openid;
            $data['url'] = $request->input('skip_url');
            $data['data']['first'] = ['value' => $request->input('content')];
            $data['data']['keyword1'] = ['value' => '商家消息'];
            $data['data']['keyword2'] = ['value' => date('Y-m-d H:i:s')];
            $data['data']['remark'] = ['value' => '感谢您对会搜云的支持'];
            $re = (new WechatBakModule())->sendTplNotify($order->wid, $data, WechatBakModule::COMMON_NOTIFY);
        } elseif ($order->source == 1) { //发送小程序
            $data['touser'] = $member->xcx_openid;
            $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($order->mid);
            if (!$data['form_id']) {
                error('该用户长时间未进入小程序，无法发送信息');
            }
            $data['data']['keyword1'] = [
                'value' => Weixin::select('shop_name')->find($order->wid)->shop_name
            ];
            $data['data']['keyword2'] = [
                'value' => $request->input('content')
            ];
            $data['data']['keyword3'] = [
                'value' => date('Y-m-d H:i:s'),
            ];
            $sendTplService = new WXXCXSendTplService($order->wid, $order->xcx_config_id);
            $re = $sendTplService->sendTplNotify($data, WXXCXSendTplService::MESSAGE_NOTIFY);
        } else {
            error();
        }
        if (empty($re['errcode']) || $re['errcode']) {
            error('发送失败(' . $re['errcode'] . ")" . $re['errmsg']);
        }
        $url = config('app.chat_url') . "/list/message/orderOfflineMsg?shopId=$order->wid&custId={$request->session()->get('userInfo')['id']}&weiUserId=$order->mid&message=$request->input('content')";
        $re = jsonCurl($url);
        success('发送成功');
    }

}

