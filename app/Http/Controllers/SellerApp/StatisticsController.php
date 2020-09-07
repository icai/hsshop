<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/4/11
 * Time: 18:00
 */

namespace App\Http\Controllers\SellerApp;


use App\Http\Controllers\Controller;
use App\S\Order\OrderLogService;
use App\S\Product\ProductService;
use App\S\RelationService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Validator;

class StatisticsController extends Controller
{

    /**
     * @desc 营收统计
     * @param Request $request
     */
    public function shopStatistics(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $page = $input['page']??1;
        $size = $input['size']??15;
        $endTime = date('Y-m-d', strtotime('-1 day'));
        switch ($input['type']) {
            case 1://7 days
                $beginTime = date('Y-m-d', strtotime('-7 days'));
                break;
            case 2://30 days
                $beginTime = date('Y-m-d', strtotime('-30 days'));
                break;
            case 3://90 days
                $beginTime = date('Y-m-d', strtotime('-90 days'));
                break;
            default://7 days
                $beginTime = date('Y-m-d', strtotime('-7 days'));
        }
        $query = '/web/v1/shopStatistics?beginTime='.$beginTime.'&endTime='.$endTime.'&wid='.$wid;
        $dcUrl = config('app.dc_url');
        $shopStatisticsUrl = $dcUrl.$query;
        $res = jsonCurl($shopStatisticsUrl);
        $res['data']['detail'] = $this->dealData($res['data']['detail']);

        $data = (new OrderService())->getIncomeAndRefund($wid, $beginTime.' 00:00:00', $endTime.' 23:59:59', ($page-1)*$size, $size);
        foreach ($data[0] as $k => &$v) {
            $v['updated_at'] = date('n月j日', strtotime($v['updated_at']));
            if ($v['action'] == 2) {
                $v['action_title'] = '订单入账';
                $v['amount'] = $v['pay_price'];
            } elseif ($v['action'] == 8) {
                $v['action_title'] = '退款';
            }
            $v['pay_title'] = (new OrderService())->getPayWayString($v['pay_way']);
            unset($data[0][$k]['pay_price'], $data[0][$k]['pay_way'], $data[0][$k]['action']);
        }
        $res['data']['amount_list'] = $data[0];
        appsuccess('营收统计成功', $res['data']);
    }

    /**
     * @desc 收支明细
     * @param Request $request
     */
    public function getIncomeAndRefund(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $page = $input['page']??1;
        $size = $input['size']??15;
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $endTime = date('Y-m-d', strtotime('-1 day'));
        switch ($input['type']) {
            case 1://7 days
                $beginTime = date('Y-m-d', strtotime('-7 days'));
                break;
            case 2://30 days
                $beginTime = date('Y-m-d', strtotime('-30 days'));
                break;
            case 3://90 days
                $beginTime = date('Y-m-d', strtotime('-90 days'));
                break;
            default://7 days
                $beginTime = date('Y-m-d', strtotime('-7 days'));
        }
        $data = (new OrderService())->getIncomeAndRefund($wid, $beginTime.' 00:00:00', $endTime.' 23:59:59', ($page-1)*$size, $size);
        foreach ($data[0] as $key => &$val) {
            $val['updated_at'] = date('n月j日', strtotime($val['updated_at']));
            if ($val['action'] == 2) {
                $val['action_title'] = '订单入账';
                $val['amount'] = $val['pay_price'];
            } elseif ($val['action'] == 8) {
                $val['action_title'] = '退款';
            }
            $val['pay_title'] = (new OrderService())->getPayWayString($val['pay_way']);
            unset($data[0][$key]['pay_price'], $data[0][$key]['pay_way'], $data[0][$key]['action']);
        }
        //判断是否到最后一页
        appsuccess('获取收支明细成功', [
            'is_last' => ($size >= $data[1][0]['count'] || ($page*$size >= $data[1][0]['count'])) ? 1 : 0,
            'detail' => $data[0]
        ]);
    }


    /**
     * @desc 交易统计
     * @param Request $request
     */
    public function shopOrderStatistics(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        $senvenDay = date('Y-m-d', strtotime('-7 days'));
        $thirtyDay = date('Y-m-d', strtotime('-30 days'));
        $ninetyDay = date('Y-m-d', strtotime('-90 days'));
        switch ($input['type']) {
            case 1://7 days
                $query = '/web/v1/shopOrderStatistics?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            case 2://30 days
                $query = '/web/v1/shopOrderStatistics?beginTime='.$thirtyDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            case 3://90 days
                $query = '/web/v1/shopOrderStatistics?beginTime='.$ninetyDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            default://7 days
                $query = '/web/v1/shopOrderStatistics?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid;
        }
        $dcUrl = config('app.dc_url');
        $shopStatisticsUrl = $dcUrl.$query;
        $res = jsonCurl($shopStatisticsUrl);
        $res['data']['detail'] = $this->dealData($res['data']['detail']);
        $query = '/api/v1/order/index?beginTime='.$yestoday.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
        $queryUrl = $dcUrl.$query;
        $res2 = jsonCurl($queryUrl);
        $res['data']['yesterday'] = [
            'visitCount' => is_null($res2['data']['visitCount']) ? 0 : $res2['data']['visitCount'],
            'orderUserCnt' => is_null($res2['data']['orderUserCnt']) ? 0 : $res2['data']['orderUserCnt'],
            'orderCnt' => is_null($res2['data']['orderCnt']) ? 0 : $res2['data']['orderCnt'],
            'orderAmount' => is_null($res2['data']['orderAmount']) ? 0 : $res2['data']['orderAmount'],
            'payedOrderUserCnt' => is_null($res2['data']['payedOrderUserCnt']) ? 0 : $res2['data']['payedOrderUserCnt'],
            'payedOrderCnt' => is_null($res2['data']['payedOrderCnt']) ? 0 : $res2['data']['payedOrderCnt'],
            'payedAmount' => is_null($res2['data']['payedAmount']) ? 0 : $res2['data']['payedAmount'],
            'payedGoodsCnt' => is_null($res2['data']['payedGoodsCnt']) ? 0 : $res2['data']['payedGoodsCnt'],
            'payPerUser' => is_null($res2['data']['payPerUser']) ? 0 : $res2['data']['payPerUser'],

            'visitRate' => is_null($res2['data']['lastRate']['visitRate']) ? 0 : $res2['data']['lastRate']['visitRate'],
            'orderUserCnRate' => is_null($res2['data']['lastRate']['orderUserCnRate']) ? 0 : $res2['data']['lastRate']['orderUserCnRate'],
            'orderCnRate' => is_null($res2['data']['lastRate']['orderCnRate']) ? 0 : $res2['data']['lastRate']['orderCnRate'],
            'orderAmountRate' => is_null($res2['data']['lastRate']['orderAmountRate']) ? 0 : $res2['data']['lastRate']['orderAmountRate'],
            'payedOrderCnRate' => is_null($res2['data']['lastRate']['payedOrderCnRate']) ? 0 : $res2['data']['lastRate']['payedOrderCnRate'],
            'payedUserCnRate' => is_null($res2['data']['lastRate']['payedUserCnRate']) ? 0 : $res2['data']['lastRate']['payedUserCnRate'],
            'payAmountRate' => is_null($res2['data']['lastRate']['payAmountRate']) ? 0 : $res2['data']['lastRate']['payAmountRate'],
            'goodsRate' => is_null($res2['data']['lastRate']['goodsRate']) ? 0 : $res2['data']['lastRate']['goodsRate'],
            'payPerUserRate' => is_null($res2['data']['lastRate']['payPerUserRate']) ? 0 : $res2['data']['lastRate']['payPerUserRate'],
        ];
        appsuccess('交易统计成功', $res['data']);
    }

    /**
     * @desc 流量统计
     * @param Request $request
     * @update 何书哲 2018年10月12日 调用接口返回数据有pid改为id
     */
    public function shopPageStatistics(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        switch ($input['type']) {
            case 1://按日 -7 days
                $senvenDay = date('Y-m-d', strtotime('-7 days'));
                $query = '/web/v1/shopPageStatistics?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 2://按日 -30 days
                $thirtyDay = date('Y-m-d', strtotime('-30 days'));
                $query = '/web/v1/shopPageStatistics?beginTime='.$thirtyDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 3://按日 -90 days
                $ninetyDay = date('Y-m-d', strtotime('-90 days'));
                $query = '/web/v1/shopPageStatistics?beginTime='.$ninetyDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 4://按月
                if (date('d') == 1) {
                    $lastMonth = date('Y-m', strtotime('-1 month'));
                    $beginMonth = date('Y-m', strtotime('-12 month'));
                } else {
                    $lastMonth = date('Y-m');
                    $beginMonth = date('Y-m', strtotime('-11 month'));
                }
                $query = '/web/v1/shopPageStatistics?beginTime='.$beginMonth.'&endTime='.$lastMonth.'&wid='.$wid.'&type=2';
                break;
            default:
        }
        $dcUrl = config('app.dc_url');
        $shopStatisticsUrl = $dcUrl.$query;
        $res = jsonCurl($shopStatisticsUrl);
        $res['data']['detail'] = $this->dealData($res['data']['detail']);
        if (isset($res['data']['productPage'])) {
            foreach ($res['data']['productPage'] as &$val) {
                $detail = (new ProductService())->getDetail($val['id']);
                $val['title'] = isset($detail['title']) ? $detail['title'] : '';
                $val['img'] = isset($detail['img']) ? $detail['img'] : '';
            }
        }
        $query = '/api/v1/merchantsAnalysisPageData?beginTime='.$yestoday.'&endTime='.$yestoday.'&wid='.$wid;
        $queryUrl = $dcUrl.$query;
        $res2 = jsonCurl($queryUrl);
        $res['data']['yesterday'] = [
            'uv' => is_null($res2['data']['overview']['uv']) ? 0 : $res2['data']['overview']['uv'],
            'pv' => is_null($res2['data']['overview']['pv']) ? 0 : $res2['data']['overview']['pv'],
            'visitProductUv' => is_null($res2['data']['overview']['visitProductUv']) ? 0 : $res2['data']['overview']['visitProductUv'],
            'visitProductPv' => is_null($res2['data']['overview']['visitProductPv']) ? 0 : $res2['data']['overview']['visitProductPv'],
        ];
        appsuccess('流量统计成功', $res['data']);
    }

    /**
     * @desc 商品排名
     * @param Request $request
     * @update 何书哲 2018年10月12日 调用接口返回数据有pid改为id
     */
    public function getRankProductV(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        switch ($input['type']) {
            case 1://按日 -7 days
                $senvenDay = date('Y-m-d', strtotime('-7 days'));
                $query = '/web/v1/getRankProductV?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 2://按日 -30 days
                $thirtyDay = date('Y-m-d', strtotime('-30 days'));
                $query = '/web/v1/getRankProductV?beginTime='.$thirtyDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 3://按日 -90 days
                $ninetyDay = date('Y-m-d', strtotime('-90 days'));
                $query = '/web/v1/getRankProductV?beginTime='.$ninetyDay.'&endTime='.$yestoday.'&wid='.$wid.'&type=1';
                break;
            case 4://按月
                if (date('d') == 1) {
                    $lastMonth = date('Y-m', strtotime('-1 month'));
                    $beginMonth = date('Y-m', strtotime('-12 month'));
                } else {
                    $lastMonth = date('Y-m');
                    $beginMonth = date('Y-m', strtotime('-11 month'));
                }
                $query = '/web/v1/getRankProductV?beginTime='.$beginMonth.'&endTime='.$lastMonth.'&wid='.$wid.'&type=2';
                break;
            default:
        }
        $dcUrl = config('app.dc_url');
        $shopStatisticsUrl = $dcUrl.$query;
        $res = jsonCurl($shopStatisticsUrl);
        if (isset($res['data'])) {
            foreach ($res['data'] as &$val) {
                $detail = (new ProductService())->getDetail($val['id']);
                $val['title'] = isset($detail['title']) ? $detail['title'] : '';
                $val['img'] = isset($detail['img']) ? $detail['img'] : '';
            }
        }
        appsuccess('商品浏览排行成功', ['detail'=>$res['data']]);
    }

    /**
     * @desc 页面浏览排行
     * @param Request $request
     */
    public function getPage(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        $senvenDay = date('Y-m-d', strtotime('-7 days'));
        $thirtyDay = date('Y-m-d', strtotime('-30 days'));
        $ninetyDay = date('Y-m-d', strtotime('-90 days'));
        switch (isset($input['type']) ? $input['type'] : 1) {
            case 1://7 days
                $query = '/web/v1/getPage?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            case 2://30 days
                $query = '/web/v1/getPage?beginTime='.$thirtyDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            case 3://90 days
                $query = '/web/v1/getPage?beginTime='.$ninetyDay.'&endTime='.$yestoday.'&wid='.$wid;
                break;
            default://7 days
                $query = '/web/v1/getPage?beginTime='.$senvenDay.'&endTime='.$yestoday.'&wid='.$wid;
        }
        $dcUrl = config('app.dc_url');
        $shopStatisticsUrl = $dcUrl.$query;
        $res = jsonCurl($shopStatisticsUrl);
        $total_pv = array_sum(array_column($res['data'], 'pv'));
        $total_uv = array_sum(array_column($res['data'], 'uv'));;
        appsuccess('页面排行成功', ['total_pv'=>$total_pv, 'total_uv'=>$total_uv, 'detail'=>$res['data']]);
    }

    /**
     * @desc 客户统计
     * @param Request $request
     */
    public function memberStatistics(Request $request){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $rules = array(
            'type' => 'required|integer'
        );
        $messages = array(
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须是整数'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $wid = $tokenData['wid'];
        $query = '/api/v1/getWeixinFansCount/'.$wid;
        $dcUrl = config('app.dc_url');
        $memeberStatisticsUrl = $dcUrl.$query;
        $memberRes = jsonCurl($memeberStatisticsUrl);
        if ($memberRes['err_code'] != 0) {
            apperror('客户统计失败');
        }
        $endDate = strtotime('-1 day');
        switch ($input['type']) {
            case 1://7 days
                $beginDate = strtotime('-7 days');
                $data = $memberRes['data']['sevenDays'];
                break;
            case 2://30 days
                $beginDate = strtotime('-30 days');
                $data = $memberRes['data']['thirtyDays'];
                break;
            case 3://90 days
                $beginDate = strtotime('-90 days');
                $data = $memberRes['data']['ninetyDays'];
                break;
            default://7 days
                $beginDate = strtotime('-7 days');
                $data = $memberRes['data']['sevenDays'];
        }
        $days = getDateFromRange($beginDate, $endDate);
        $list = [];
        foreach ($days as $date) {
            $tmp = [];
            if ($data && $data['list'] && array_filter($data['list'], function ($item) use (&$tmp, $date) {
                $item['createtime'] = date('Y-m-d', $item['createtime']);
                return $date == $item['createtime'] ? ($tmp = $item) : false;
                })) {
                $tmp['createtime'] = date('n月j日', strtotime($date));
                $tmp['growthUserNum'] = $tmp['newUsersNum']-$tmp['cancelUsersNum'];
                $list[] = $tmp;
            } else {
                $list[] = [
                    'createtime' => date('n月j日', strtotime($date)),
                    'newUsersNum' => 0,
                    'cancelUsersNum' => 0,
                    'growthUserNum' => 0
                ];
            }
        }
        appsuccess('客户统计成功', [
            'totalFans' => $memberRes['data']['totalFans'],
            'newUsersNum' => $data['newUsersNum'],
            'cancelUsersNum' => $data['cancelUsersNum'],
            'growthUserNum' => $data['growthUserNum'],
            'list' => $list
        ]);
    }

    public function dealData($data){
        foreach ($data as &$val ) {
            if (array_key_exists('created_at', $val)) {
                $val['created_at'] = date('n月j日', strtotime($val['created_at']));
            } elseif (array_key_exists('date', $val)) {
                $val['date'] = date('n月j日', strtotime($val['date']));
            }
        }
        return $data;
    }

}