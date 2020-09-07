<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Module\StatisticModule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * 构造函数
     * @param Request $request 公共请求类
     * @param StatisticModule $module 自定义统计module类
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 商家活跃度-微商城数据统计
     * @author 吴晓平 [wuxiaoping1559@dingtalk.com] at 2019年08月14日
     * @return [type] [description]
     */
    public function getWeixinStatistic()
    {
        // 设置过期时间为距离第二天的凌晨3点的时间
        $times = Carbon::parse((new Carbon)->addDay()->toDateString() . ' 03:00:00')->diffInSeconds(Carbon::now());
        $module = app(StatisticModule::class);
        $yestoday = Carbon::yesterday()->toDateString();
        $today = Carbon::now()->toDateString();
        $incomeData = \Cache::remember('shopStatistic', $times, function () use ($module) {
            $shopCount = $module->getShopCount();
            $orderData = $module->getOrderCount();
            $userCount = $module->getUserTotal();
            $incomeData = ['shopCount' => $shopCount, 'orderData' => $orderData, 'userCount' => $userCount];
            return $incomeData;
        });

        $activeData = $module->getActiveStatisticData(['start' => $yestoday, 'end' => $today]);
        $rankData = $module->getSalesIncomeRank(['start' => $yestoday, 'end' => $today]);
        return view('staff.weixin.statistic', [
            'title' => '商家活跃度',
            'incomeData' => $incomeData,
            'activeData' => $activeData,
            'rankData' => $rankData,
            'start' => Carbon::yesterday()->toDateString(),
            'end' => Carbon::now()->toDateString()
        ]);
    }

    /**
     * 商家活跃度-获取活跃数据api
     * @author 吴晓平 [wuxiaoping1559@dingtalk.com] at 2019年08月14日
     * @return [type] [description]
     */
    public function getActiveApi()
    {
        $start = $this->request->input('start', '');
        $end = $this->request->input('end', '');
        if (!strtotime($start) || !strtotime($end)) {
            error('请选择日期');
        }
        $module = app(StatisticModule::class);
        $activeData = $module->getActiveStatisticData(['start' => $start, 'end' => $end]);
        success('操作成功', '', $activeData);
    }

    /**
     * @description：获取根据销售额对店铺的倒序排序列表api
     * @return bool
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月19日 15:26:17
     */
    public function getRankingListApi()
    {
        $start = $this->request->input('start', '');
        $end = $this->request->input('end', '');
        $keywords = $this->request->input('keywords', '');
        $order = $this->request->input('order', '');
        if (!strtotime($start) || !strtotime($end)) {
            // 如果没有日期的筛选则默认为筛选昨天的数据
            $start = Carbon::yesterday()->toDateString();
            $end = Carbon::now()->toDateString();
        }
        // 只针对销售额，订单数进行排序
        if ($order) {
            $orders = explode('_', $order);
            if (!in_array($orders[0], ['income', 'nums'])) {
                error('只针对销售额，订单数进行排序');
            }
        }
        $module = app(StatisticModule::class);
        $rankData = $module->getSalesIncomeRank(['start' => $start, 'end' => $end], $keywords, $order);
        success('操作成功', '', $rankData);
    }
}
