<?php
/** 
 * 资产模块 
 * 
 * @package default 
 * @author  大王叫我来巡山 
 */
namespace App\Http\Controllers\Merchants;

//
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\Weixin;
use App\Module\MallModule;
use App\Services\CapitalWithdrawService;
use App\Services\Order\OrderRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\S\RelationService;
use App\Services\CashLogService;
use OrderService;
use App\S\Weixin\ShopService;

class CapitalController extends Controller
{
    
    private $weixinInfo;

    /**
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->leftNav = 'capital';

    }

    /**
	 * 我的收入
	 * @return [type] [description]
	 */
    public function index(Request $request,ShopService $shopService){
        $wid = session('wid');
        $relationservice = new RelationService();
        $cashLogService = new CashLogService();        
        //店铺信息
        /*$weixinService = D('Weixin', 'uid', session('userInfo')['id']);
        $this->weixinInfo = $weixinService->getInfo($wid);*/

        $this->weixinInfo = $shopService->getRowById($wid);
        $returnData = [];
        $orderListAll = [];
        //统计总的收入：从创建店铺的时间开始计算截止今日的0点
        $orderListAll = $relationservice->orderLogRelation([2,8],[strtotime($this->weixinInfo['created_at']),time()]);


        $orderListAll['total_income'] = sprintf('%.2f', $this->weixinInfo['sale_sum']);
        $orderListAll['seven_income'] = sprintf('%.2f', $this->weixinInfo['sale_sum_7days']);
        $orderListAll['month_income'] = sprintf('%.2f', $this->weixinInfo['sale_sum_30days']);
        $returnData = $orderListAll;
        /* 获取固定数据数组 */
        list($fieldList, $typeList, $expressList, $payWayList, $statusList, $refundStatusList) = OrderService::getStaticList();
        return view('merchants.capital.index',array(
            'title'      => '我的收入',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'index',
            'info'       => $this->weixinInfo,
            'return'     => $returnData,
            'payWayList' => $payWayList
        ));
    }

    /*
     * 提现
     */

    public function withdrawals(){
        return view('merchants.capital.withdrawals', array(
            'title' => '提现',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
        ));
    }

    /**
     * 设置提现账号
     */

    public function withdrawalSetting(){
        return view('merchants.capital.withdrawalSetting', array(
            'title' => '提现',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
        ));
    }

    /**
     *充值
     */

    public function rechargeMoney(Request $request){
        $userInfo = $request->session()->get('userInfo');
        $uid = $userInfo['id'];
        if($request->isMethod('post')){
            $input = $request->input();
            $balance = sprintf('%.2f',$input['balance']);
            /*
             * 中间会有扫码的付账的过程
             */
            DB::table('user_info')->where('uid',$uid)->increment('balance', $balance);
            return Redirect::to('merchants/capital');//进入收入
        }else {
            $balance = DB::table('user_info')->where('uid',$uid)->pluck('balance')->first();
            // $balance = $balance?$balance:'0.00';
            $balance = sprintf('%.2f',$balance);
            return view('merchants.capital.rechargeMoney', array(
                'title' => '充值',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'balance' => $balance
            ));
        }
    }

    /**
     * 交易记录
     * @return [type] [description]
     */
    public function transactionRecord(Request $request, $status = null)
    {
        // 订单状态
        $orderStatus = ['-1'=>'删除','0'=>'待付款','1'=>'待发货','2'=>'已发货','3'=>'已完成','4'=>'已关闭','5'=>'退款中'];

        // 查询条件
        $statusAdd = [];
        if ( $status ) {
            $whereStatus = [
                [0, 1, 2],
                [5],
                [3, 4],
                [-1]
            ];
            $statusAdd['status'] =  ['in', $whereStatus[$status - 1]];
        }

        // 查询数据
        $orderService = D('Order', 'wid', session('wid'));
        list($list, $pageHtml) = $orderService->where($statusAdd)->with($orderService->withAll)->getList();

        return view('merchants.capital.transactionRecord', [
            'title'       => '交易记录',
            'leftNav'     => $this->leftNav,
            'list'        => $list['data'],
            'pageHtml'    => $pageHtml,
            'status'      => $status,
            'slidebar'    => 'transactionRecord',
            'orderStatus' => $orderStatus,
        ]);
    }

    /**
     * 对账单 - 账单汇总
     * @return [type] [description]
     */
    public function billSummary(Request $request)
    {
        $input = $request->input();
        $relationservice = new RelationService();
        $wid = session('wid');
        $type = !empty($input['type']) ? $input['type'] : 1;
        $year = $input['year'] ?? date('Y',time());
        $income = $paid = 0;  //定义收入，支出的基础值
        $returnData = []; //保存返回的数组
        $month = !empty($input['month']) ? $input['month'] : date('m',time());  //该月份下的所有的天数收入，支出汇总
        $day = 0;
        if( $type == 1 ){ //表示按日汇总
            $startTime = strtotime($year.'-'.$month.'-01 '.'00:00:00');  // 统计的开始时间
            $currentMonth = date('m',time());  //获取当前月份

            //筛选的是以前的月份（不是当前月）
            if($month == $currentMonth){
                $endTime = time();
                $day = date('d',time());
            }else{
                $day = $this->getDaysFromMonth($year,$month);
                $endTime = strtotime($year.'-'.$month.'-'.$day.' 11:59:59');
            }
            $returnData = $this->books($startTime,$endTime,$year,$month,$day);
            
        }else if( $type == 2 ){ //表示按月汇总
            /**如果year = 2017 则统计 2017-01-01 00:00:00 -- 2017-12-31 11:59:59之间的数据**/
            $startTime = strtotime($year.'-01-01 00:00:00');
            $endTime   = strtotime($year.'-12-31 11:59:59');
            $returnData = $this->books($startTime,$endTime,$year);
        }

        //点击详情时把日期作为参数传递使用
        foreach($returnData as $rek => $ret){  
            $returnData[$rek]['param'] =  str_replace('-', '/', $rek);
        }

        //填充有日期数据，但没有收入或支出时的初始值
        foreach($returnData as $sdk => $red){
            if(!isset($red['income'])){
                $returnData[$sdk]['income'] = 0;
            }

            if(!isset($red['paid'])){
                $returnData[$sdk]['paid'] = 0;
            }
        }

        
        //跟前端交互返回的数据
        if($request->method() == 'POST'){
            success('','',$returnData);
        }

        return view('merchants.capital.billSummary',array(
            'title'    => '对账单 - 账单汇总',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'billSummary',
            'pageHtml' => '',
            'list'     => $returnData,
            'type'     => $type,
            'year'     => $year,
            'month'    => $month,
            'day'      => $day
        ));
    }

    /**
     * 收支统计
     * @return [type] [description]
     */
    public function books($startTime,$endTime,$year,$month=0,$day=0)
    {
        $relationservice = new RelationService();
        $wid = session('wid');
        $orderListAll = $relationservice->orderLogRelation([2,8],[$startTime,$endTime]);
        $result = [];  //保存收入，支出的数组
        $returnData = [];
        

        //处理统计该月份下对应日期的收入与支出
        if($day){
            if(isset($orderListAll['datas']) && $orderListAll['datas']){
                foreach($orderListAll['datas'] as $val){
                    $dateKey = substr($val['updated_at'],0,10);
                    if($val['action'] == 2){ //表示有订单收入    
                        $result['income'][$dateKey][] = $val;
                    }else if($val['action'] == 8 || $val['action'] == -1){
                        $result['paid'][$dateKey][] = $val;
                    }
                }
            }
            for($i=1; $i<$day; $i++){
                if($i < 10){
                    $i = '0'.$i;
                }
                if(isset($result['income']) && $result['income']){
                    if(!empty($result['income'][$year.'-'.$month.'-'.$i])){  //是否存在该日期下的订单入账
                        $returnData[$year.'-'.$month.'-'.$i]['income'] = 0;
                        foreach($result['income'][$year.'-'.$month.'-'.$i] as $res){
                            $returnData[$year.'-'.$month.'-'.$i]['income'] += $res['pay_price']; 
                            //$returnData[$year.'-'.$month.'-'.$i]['income'] = sprintf('%.2f',$returnData[$year.'-'.$month.'-'.$i]['income']);
                        }
                        $returnData[$year.'-'.$month.'-'.$i]['income'] = sprintf('%.2f',$returnData[$year.'-'.$month.'-'.$i]['income']);
                    }
                }
                if(isset($result['paid']) && $result['paid']){
                    if(!empty($result['paid'][$year.'-'.$month.'-'.$i])){  //是否存在该日期下的退款支出
                        $returnData[$year.'-'.$month.'-'.$i]['paid'] = 0;
                        foreach($result['paid'][$year.'-'.$month.'-'.$i] as $res){
                            $returnData[$year.'-'.$month.'-'.$i]['paid'] += $res['pay_price']; 
                            //$returnData[$year.'-'.$month.'-'.$i]['paid'] = sprintf('&.2f',$returnData[$year.'-'.$month.'-'.$i]['paid']);
                        }
                        $returnData[$year.'-'.$month.'-'.$i]['paid'] = sprintf('&.2f',$returnData[$year.'-'.$month.'-'.$i]['paid']);
                    }
                }
                
            }
        }else{
            if(isset($orderListAll['datas']) && $orderListAll['datas']){
                foreach($orderListAll['datas'] as $val){
                    $dateKey = substr($val['updated_at'],0,7);
                    if($val['action'] == 2){ //表示有订单收入    
                        $result['income'][$dateKey][] = $val;
                    }else if($val['action'] == 8 || $val['action'] == -1){
                        $result['paid'][$dateKey][] = $val;
                    }
                }
            }

            for($i=1;$i<=12;$i++){
                if($i < 10){
                    $i = '0'.$i;
                }
                if(isset($result['income']) && $result['income']){
                    if(!empty($result['income'][$year.'-'.$i])){
                        $returnData[$year.'-'.$i]['income'] = 0;
                        foreach($result['income'][$year.'-'.$i] as $res){
                            $returnData[$year.'-'.$i]['income'] += $res['pay_price']; 
                        }
                        $returnData[$year.'-'.$i]['income'] = sprintf('%.2f', $returnData[$year.'-'.$i]['income']);
                    }

                } 

                if(isset($result['paid']) && $result['paid']){
                    if(!empty($result['paid'][$year.'-'.$i])){  //是否存在该日期下的退款支出
                        $returnData[$year.'-'.$i]['paid'] = 0;
                        foreach($result['paid'][$year.'-'.$i] as $res){
                            $returnData[$year.'-'.$i]['paid'] += $res['pay_price']; 
                        }
                        $returnData[$year.'-'.$i]['paid'] = sprintf('%.2f', $returnData[$year.'-'.$i]['paid']);
                    }
                }
            }

        }
        return $returnData;
    }

    /*
     * 对账单 - 账单汇总详情
     * @param $type  汇总类型  1-日汇总  2-月汇总
     * @param $year 统计对应的年份
     * @param $month 统计对应的月份
     * @param $day 统计对应的日期
     */
    public function billSummaryContent($type,$year,$month,$day=0){
        $relationservice = new RelationService();
        $cashLogService = new CashLogService();
        $wid = session('wid');
        /*$weixinService = D('Weixin', 'uid', session('userInfo')['id']);
        $this->weixinInfo = $weixinService->getInfo($wid);*/
        $this->weixinInfo = (new ShopService())->getRowById($wid);
        $days = $this->getDaysFromMonth($year,$month); //每个月对应的天数
        $createTime = $this->weixinInfo['created_at'];

        //设置汇总账单的起止时间
        $startTime = $endTime = $periodEndTime = 0;
        if($type == 1){ //日汇总
            $startTime = $year.'-'.$month.'-'.$day.' 00:00:00';
            //统计日期为当前日
            if(date('d',time()) == $day){
                $endTime = date('Y-m-d H:i:s',time());
            }else{
                $endTime = $year.'-'.$month.'-'.$day.' 23:59:59';
            }

            //设置统计上期营收的结束时间
            if((int)$day-1 == 0){ //表示当前日期是1号
                if((int)$month-1 == 0){
                    $periodEndTime = ((int)$year-1).'-12-31 23:59:59';
                }else{
                    $beforMonth = (int)$month-1;
                    $periodEndTime = $year.'-'.$beforMonth.'-'.$this->getDaysFromMonth($year,$beforMonth).' 23:59:59';
                }
            }else{
                $beforDay = (int)$day-1;
                $periodEndTime = $year.'-'.$month.'-'.$beforDay.' 23:59:59';
            }   

        }else{ //月汇总
            $startTime = $year.'-'.$month.'-01 00:00:00';
            //统计的月份为当前月
            if(date('m',time()) == $month){
                $endTime = date('Y-m-d H:i:s',time());
            }else{
                $endTime = $year.'-'.$month.'-'.$days.' 23:59:59';
            }
            //设置统计上期营收的结束时间
            if((int)$month-1 == 1){ //表示当前月份是1月
                $periodEndTime = ((int)$year-1).'-12-31 23:59:59';
            }else{
                $beforMonth = (int)$month-1;
                $periodEndTime = $year.'-'.$beforMonth.'-'.$this->getDaysFromMonth($year,$beforMonth).' 23:59:59';
            }

        }


        //上期营收汇总（收入+支出）
        $periodOrderListAll = $relationservice->orderLogRelation([2,8],[strtotime($createTime),strtotime($periodEndTime)]);
        $periodIncome = 0; //上期汇总初起值
        if($periodOrderListAll){
            $sale_sum = (new MallModule())->getSaleSum((new OrderRefundService()), $wid, 3, $createTime, $periodEndTime);
            $return['periodSum'] = $sale_sum;
            $periodIncome = $sale_sum;
        }
        
        //本期营收汇总（收入+支出）
        $thisOrderListAll = $relationservice->orderLogRelation([2,8],[strtotime($startTime),strtotime($endTime)]);
        $thisIncome = 0; //本期收入
        $thisPaid = 0;   // 本期支出
        $thisRefund = $thisDistribute = 0;
        if($thisOrderListAll['datas']){

            foreach($thisOrderListAll['datas'] as $item){
                if($item['action'] == 2){ //营收收入
                    $thisIncome += $item['pay_price'];
                }else if($item['action'] == 8){ //营收支出
                    $thisRefund += $item['pay_price'];  //本期的退款汇总
                    $thisPaid -= $item['pay_price'];
                }else if($item['action'] == -1){
                    $thisDistribute += $item['pay_price']; // 本期的分销打款汇总
                    $thisPaid -= $item['pay_price'];
                }   
            }
        }
        
        $return['thisIncome'] = $thisIncome;
        $return['thisPaid']   = $thisPaid;
        $reutn['refund']      = $thisRefund;
        $return['distribute'] = $thisDistribute;
        $return['thisSum']    = ($periodIncome + $thisIncome) - $thisPaid;
        return view('merchants.capital.billSummaryContent',array(
            'title'    => '对账单 - 账单汇总',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'billSummary',
            'weixinInfo'=> $this->weixinInfo,
            'type'      => $type,
            'year'      => $year,
            'month'     => $month,
            'day'       => $day,
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'return'    => $return

        ));
    }

    /**
     * 计算每月的天数（2月是否为闰月，其他月份是否有31号）
     * @param  [int] $year  [年份]
     * @param  [int] $month [月份]
     * @return [int] [返回的天数]
     */
    public function getDaysFromMonth($year,$month)
    {   
        $day = 0;
        $bigMonth = [1,3,5,7,8,10,12];
        if( $month == 2){  // 当月份为2月的时，判断年份是否为闰年
            if(( $year % 4 == 0 && $year % 100 !=0 )||($year % 400 == 0 )){
                $day = 28;
            }else{
                $day = 29;
            }
        }else{
            if(in_array($month,$bigMonth)){  //表示大月，31天
                $day = 31;
            }else{
                $day = 30;
            }
        }
        return $day;
    }

    /**
     * 对账单 - 账单明细
     * @return [type] [description]
     */
    public function billDetail(Request $request)
    {
        $relationservice = new RelationService();
        $input = $request->input();

        $startTime = $input['start_time'] ?? '';
        $endTime = $input['end_time'] ?? '';
        $wid = session('wid');

        //当没有查询时间范围时，给一个默认的起始时间
        if(empty($startTime)){
            $startTime = strtotime(('-7 days'));
        }else{
            $startTime = strtotime($startTime);
        }
        // 默认结束时间
        if(empty($endTime)){
            $endTime = time();
        }else{
            $endTime = strtotime($endTime);
        }

        $data = [];
        if(isset($input['order_sn']) && $input['order_sn']){
            $data['order_sn'] = $input['order_sn'];
        }

        /* 获取固定数据数组 */
        list($fieldList, $typeList, $expressList, $payWayList, $statusList, $refundStatusList) = OrderService::getStaticList();
        if (isset($input['order_sn']) && $input['order_sn']) {
            $orderData = OrderService::init()->where(['oid' => $input['order_sn']])->getList(false);
            if ($orderData[0]['data']) {
                $input['oid'] = $orderData[0]['data'][0]['id'];
            }
        }
        //获取统计数据
        $orderListAll = $relationservice->orderLogRelation([2,8],[$startTime,$endTime],$input);
        $returnData = $lastData = [];
        if(isset($orderListAll['datas']) && $orderListAll['datas']){
            foreach($orderListAll['datas'] as $val){
                $returnData[$val['action']][] = $val;
            }
        }

        //根据搜索条件返回相应的数据
        if(!empty($input['type'])){
            if(isset($input['type']) && $input['type'] == 0){
                $lastData = isset($orderListAll['datas']) ? $orderListAll['datas'] : [];
            }else if(isset($input['type']) && $input['type'] == 1){  //订单入账
                $lastData = isset($returnData[2]) ? $returnData[2] : [];
                
            }else if(isset($input['type']) && $input['type'] == 2){  //订单退款
                $lastData = isset($returnData[8]) ? $returnData[8] : [];
               
            }else if(isset($input['type']) && $input['type'] == 3){  //分销打款
                $lastData = isset($returnData[-1]) ? $returnData[-1] : [];
            }
        }else{
            $lastData = isset($orderListAll['datas']) ? $orderListAll['datas'] : [];
        }

        return view('merchants.capital.billDetail',array(
            'title'      => '对账单 - 账单明细',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'billDetail',
            'start_time' => date('Y-m-d H:i:s',$startTime),
            'end_time'   => date('Y-m-d H:i:s',$endTime),
            'result'     => $lastData,
            'pageHtml'   => $orderListAll['pageHtml'],
            'input'      => $input,
            'payWayList' => $payWayList
        ));
    }

    /*
     * 对账单 - 账单明细详情
     */
    public function billDetailContent(Request $request){
        return view('merchants.capital.billDetailContent',array(
            'title'    => '对账单 - 账单明细',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'billSummary'
        ));
    }

    /**
     * 提现记录
     * @return [type] [description]
     */
    public function withdrawalRecord( Request $request, CapitalWithdrawService $capitalWithdrawService ) {
        $wid =session('wid');
        $with = ['bankInfo'];
        $where = array();
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $status = $request->input('status');
        if($startTime){
            $where = array('create_at','>',$startTime);
        }
        if($endTime){
            $where = array_merge(array('create_at','<',$endTime),$where);
        }
        if($status){
            $where = array_merge(array('status'=>$status),$where);
        }

        list($list, $pageHtml) = $capitalWithdrawService->init('wid',$wid)->with($with)->where($where)->getList();

        return view('merchants.capital.withdrawalRecord',array(
            'title'    => '提现记录',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'withdrawalRecord',
            'withdrawl' => $list['data'],
            'userInfo'  => $request->session()->get('userInfo'),
            'input' =>$request->input(),
            '$pageHtml'=> $pageHtml

        ));
    }

    /**
     * 保证金记录
     * @return [type] [description]
     */
    public function bailRecord(){
        return view('merchants.capital.bailRecord',array(
            'title'    => '保证金记录',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'bailRecord'
        ));
    }

    /**
     * 不可用余额
     * @return [type] [description]
     */
    public function disabledBalance(){
        return view('merchants.capital.disabledBalance',array(
            'title'    => '不可用余额',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'disabledBalance'
        ));
    }

    /**
     * 服务市场 - 服务订购
     * @return [type] [description]
     */
    public function serviceOrdering(){
        return view('merchants.capital.serviceOrdering',array(
            'title'    => '服务市场 - 服务订购',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'serviceOrdering'
        ));
    }

    /**
     * 服务市场 - 批量采购
     * @return [type] [description]
     */
    public function bulkPurchase(){
        return view('merchants.capital.bulkPurchase',array(
            'title'    => '服务市场 - 批量采购',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'serviceOrdering',

        ));
    }

    /**
     * 服务市场 - 激活码兑换
     * @return [type] [description]
     */
    public function cdkeyExchange(){
        return view('merchants.capital.cdkeyExchange',array(
            'title'    => '服务市场 - 激活码兑换',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'serviceOrdering'
        ));
    }

    /**
     * 订购关系 - 我的服务
     * @return [type] [description]
     */
    public function myService(){
        return view('merchants.capital.myService',array(
            'title'    => '订购关系 - 我的服务',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'myService'
        ));
    }

    /**
     * 订购关系 - 订购记录
     * @return [type] [description]
     */
    public function orderRecord(){
        return view('merchants.capital.orderRecord',array(
            'title'    => '订购关系 - 订购记录',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'myService'
        ));
    }

    /**
     * 会搜币
     * @return [type] [description]
     */
    public function virtualCurrency(){
        return view('merchants.capital.virtualCurrency',array(
            'title'    => '会搜币',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'virtualCurrency'
        ));
    }

    /**
     * 邀请奖励
     * @return [type] [description]
     */
    public function inviteRewards(){
        return view('merchants.capital.inviteRewards',array(
            'title'    => '邀请奖励',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'inviteRewards'
        ));
    }

    /**
     * 发票管理
     * @return [type] [description]
     */
    public function invoiceManagement(){
        return view('merchants.capital.invoiceManagement',array(
            'title'    => '发票管理',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'invoiceManagement'
        ));
    }
}
