<?php
/**
 * @ 微商城，小程序相关的数据统计
 * @Author:      吴晓平 [wuxiaoping1559@dingtalk.com] at  2019年08月14日
 */

namespace App\Module;
use App\Model\Member;
use App\Model\Order;
use App\Model\Weixin;
use App\Model\WeixinRole;
use App\Model\WeixinConfigSub;
use App\Model\WXXCXConfig;
use Carbon\Carbon;

class StatisticModule
{
    private $endDay; // 定义一个要统计结束的时间变量

    // 构造函数 给要统计结束的时间变量赋值
    public function __construct()
    {
        $this->endDay = (new Carbon)->subDay()->toDateString() . ' 23:59:59';
    }

    /**
     * @description：商家活跃总商家数统计
     *
     * @return int
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月14日 15:28:36
     */
    public function getShopCount()
    {
        $count = Weixin::query()
            ->where('created_at', '<=', $this->endDay)
            ->where('shop_expire_at', '>=', Carbon::now()->toDateTimeString())
            ->count();
        return number_format($count);
    }

    /**
     * @description：商家活跃总订单数，总销售额
     *
     * @return array
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月14日 15:28:42
     */
    public function getOrderCount()
    {
        $dbOrderBuilder = \DB::connection('mysql_dc_log')->table('order');
        $dbOrderBuilder->where('created_at', '<=', Carbon::yesterday()->timestamp);
        // 截止到昨日的所有店铺的支付总额
        $priceTotal = $dbOrderBuilder->sum('order_payed_amount');
        // 截止到昨日的所有店铺的退款总额
        $refundPayTotal = $dbOrderBuilder->sum('order_pay');
        // 截止到昨日的所有店铺的支付订单总数
        $count = $dbOrderBuilder->sum('order_payed_count');
        // 截止到昨日的所有店铺的退款订单总数
        $dbOrderRefundBuilder = \DB::connection('mysql_dc_order_log')->table('order_refund');
        $dbOrderRefundBuilder->where('created_at', '<=', Carbon::yesterday()->timestamp);
        $refundCount = $dbOrderRefundBuilder->count();
        // 销售总额
        $totalIncome = bcsub($priceTotal, $refundPayTotal, 2);
        // 扣除退款的总订单数
        $totalCount = intval($count-$refundCount);
        return ['order_count' => number_format($totalCount), 'price_total' => number_format($totalIncome, 2)];
    }

    /**
     * @description：商家活跃总用户数
     *
     * @return int
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月14日 15:28:46
     */
    public function getUserTotal()
    {
        $count = Member::query()
            ->where('created_at', '<=', $this->endDay)
            ->where('status', 0)
            ->where(function ($query) {
                $query->where('openid', '!=', '')->orWhere('xcx_openid', '!=', '');
            })->count();
        return number_format($count);
    }

    /**
     * @description：获取活跃数据
     *
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月14日 19:47:01
     */
    public function getActiveStatisticData($sectionDates = [])
    {
        // 设置连接dc库
        $dbOrderBuilder = \DB::connection('mysql_dc_log')->table('order');
        if (empty($sectionDates['start']) || empty($sectionDates['end'])) {
            return [];
        }
        if (Carbon::parse($sectionDates['end'])->lt(Carbon::parse($sectionDates['start'])))  {
            return [];
        }
        // 指定时间区间内的新增店铺数
        $shopCount = Weixin::query()
            ->whereDate('created_at', '>=', $sectionDates['start'])
            ->whereDate('created_at', '<', $sectionDates['end'])
            ->count();

        // 指定时间区间内的新增订单数
        $startDate = Carbon::parse($sectionDates['start'])->timestamp;
        $endDate = Carbon::parse($sectionDates['end'])->timestamp;
        $dbOrderBuilder->where('created_at', '>=', $startDate);
        $dbOrderBuilder->where('created_at', '<', $endDate);
        $orderPayedCount = $dbOrderBuilder->count();

        // 指定时间区间内的活跃客户数（uv）,微商城数据
        $dbViewBuilder = \DB::connection('mysql_dc_log')->table('totalview_count');
        $dbViewBuilder->where('countTime', '>=', $startDate);
        $dbViewBuilder->where('countTime', '<', $endDate);
        $viewCount = $dbViewBuilder->sum('viewuv');
        // 小程序的数据
        $dbViewXcxBuilder = \DB::connection('mysql_dc_log')->table('viewxcx_count');
        $dbViewXcxBuilder->where('createTime', '>=', $startDate);
        $dbViewXcxBuilder->where('createTime', '<', $endDate);
        $viewXcxCount = $dbViewXcxBuilder->sum('xcx_uv');
        // 两边数据相加求总数据
        $viewTotalCount = $viewCount + $viewXcxCount;

        // 活跃商家数
        $dbActiveShopBuilder = \DB::connection('mysql_dc_log')->table('shop_login_log');
        $dbActiveShopBuilder->where('last_login_at', '>=', $startDate);
        $dbActiveShopBuilder->where('last_login_at', '<', $endDate);
        $activeShop = $dbActiveShopBuilder->select(['id', 'wid'])->get();
        // 先根据wid分组然后进行统计（防止同一店铺多次统计）
        $activeShopCount = $activeShop->groupBy('wid')->count();
        return ['newShopCount' => number_format($shopCount), 'orderPayedCount' => number_format($orderPayedCount), 'viewTotalCount' => $viewTotalCount, 'activeShopCount' => $activeShopCount];

    }

    /**
     * @description：获取根据销售额对店铺的倒序排序列表
     * @param array $sectionDates 筛选的时间区间数组
     * @param string $keywords    筛选店铺的标题名称
     *
     * @return array|bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月19日 14:56:49
     */
    public function getSalesIncomeRank($sectionDates = [], $keywords = '', $order = '')
    {
        if (empty($sectionDates['start']) || empty($sectionDates['end'])) {
            return [];
        }
        if (Carbon::parse($sectionDates['end'])->lt(Carbon::parse($sectionDates['start'])))  {
            return [];
        }
        $dbOrderBuilder = \DB::connection('mysql_dc_log')->table('order');
        // 根据店铺的名称关键词搜索
        if ($keywords) {
            $ids = Weixin::query()
                ->where('shop_name', 'like', '%' . $keywords . '%')
                ->where('shop_expire_at', '>=', Carbon::now()->toDateTimeString())
                ->pluck('id')->all();
            if ($ids) {
                $dbOrderBuilder->whereIn('wid', $ids);
            }
        }
        $page = app('request')->input('page', 1);
        $startDate = Carbon::parse($sectionDates['start'])->timestamp;
        $endDate = Carbon::parse($sectionDates['end'])->timestamp;
        $dbOrderBuilder->where('created_at', '>=', $startDate);
        $dbOrderBuilder->where('created_at', '<', $endDate);
        $dbOrderBuilder->select([\DB::raw('sum(order_payed_amount) as income'), \DB::raw('sum(order_payed_count) as nums'), 'wid']);
        $dbOrderBuilder->groupBy('wid');
        // 根据筛选排序
        if ($order) {
            $orders = explode('_', $order);
            $dbOrderBuilder->orderBy($orders[0], $orders[1]);
        } else {
            $dbOrderBuilder->orderBy('income', 'desc');
        }
        $list = $dbOrderBuilder->paginate(8);
        if (!$list->isEmpty()) {
            foreach ($list as $key => $item) {
                $item->shop_name = '';
                $item->weixin_logo_url = '';
                $item->rank = ($key + 1) + ($page - 1) * 8;
                $wids[] = $item->wid;
            }
            // 根据wid字段获取对应的店铺标题名称
            $shops = Weixin::query()->whereIn('id', $wids)->get(['id', 'shop_name', 'weixin_logo_url']);
            if (!$shops->isEmpty()) {
                foreach ($shops as $shop) {
                    foreach ($list as $item) {
                        if ($shop->id == $item->wid) {
                            $item->shop_name = $shop->shop_name;
                            $item->weixin_logo_url = $shop->weixin_logo_url;
                        }
                    }
                }
            }
        }
        return $list;
    }
}
