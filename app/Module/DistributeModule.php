<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/11/14
 * Time: 14:20
 */

namespace App\Module;

use App\Jobs\DataStatisticsDistributeWithdrawal;
use App\Lib\Redis\RedisClient;
use App\Model\Bank;
use App\Model\CashLog;
use App\Model\CompanyPayOrder;
use App\Model\DistributeBank;
use App\Model\DistributeGrade;
use App\Model\DistributeGradeTemplate;
use App\Model\DistributeTemplate;
use App\Model\Income;
use App\Model\Member;
use App\Model\Product;
use App\S\Distribute\DistributeGradeService;
use App\S\Groups\GroupsService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Order\CompanyPayOrderService;
use App\Services\CashLogService;
use App\Services\IncomeService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderService;
use App\Services\WeixinService;
use DB;
use Illuminate\Http\Request;
use Log;
use OrderCommon;
use App\S\Weixin\ShopService;

class DistributeModule
{

    public $request;

    public function __construct()
    {
        $this->request = app('request');
    }

    public function HandleOldData($wid)
    {
        $memberService = new MemberService();
        $where         = [
            'cash'          => ['>', 0],
            'wid'           => $wid,
            'is_distribute' => 0,
        ];
        $res           = $memberService->getDistributeOldData($wid);
        $res1          = $memberService->getMemberIds($where);
        $result        = array_merge($res, $res1);
        if (count($result) > 0) {
            $ids = [];
            foreach ($res as $val) {
                $ids[] = $val['id'];
            }
            $memberService->batchUpdate($ids, ['is_distribute' => 1]);
        }
        return true;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171115
     * @desc 个人中心是否显示成为分销可按钮
     * @param $memebr
     * @param $wid
     * @return int 0 ，不显示，1：显示
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isShowDistibute($memebr, $wid)
    {
        $result = 0;
        if ($memebr['is_distribute'] == 1) {
            return $result;
        }
        /*$shopData = (new WeixinService())->init()->model->where('id',$wid)->get(['id','is_distribute','distribute_grade','demand'])->toArray();
        if (!$shopData){
            return $result;
        }
        $shopData = $shopData[0];*/
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        if (empty($shopData)) {
            return $result;
        }
        if ($shopData['is_distribute'] == 0 || ($shopData['is_distribute'] == 1 && $shopData['distribute_grade'] == 0)){
            return $result;
        }
        $redisClient = (new RedisClient())->getRedisClient();
        $res         = $redisClient->get($this->getKey($memebr['id'], $wid));
        if ($res) {
            return $result;
        }

        if ($shopData['is_distribute'] == 1 && $shopData['distribute_grade'] == 1) {
            if ($shopData['demand']) {
                $demand  = json_decode($shopData['demand'], true);
                $buyData = (new OrderService())->getMemberBuyData($memebr['id']);
                if ($demand){
                    foreach ($demand as $key => $val) {
                        switch ($key) {
                            case 'pay_num':
                                if ($val <= $buyData['num']) {
                                    $result = 1;
                                    break 2;
                                }
                                break;
                            case 'pay_amount':
                                if ($val <= $buyData['amount']) {
                                    $result = 1;
                                    break 2;
                                }
                                break;
                            case 'score':
                                if ($val <= $memebr['score']) {
                                    $result = 1;
                                    break 2;
                                }
                                break;
                        }
                    }
                }
            } else {
                $result = 0;
            }
        }
        return $result;


    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171115
     * @desc 获取key
     * @param $mid
     * @param $wid
     */
    public function getKey($mid, $wid)
    {
        return 'is_distribute_grade:' . $wid . ':' . $mid;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取分销佣金记录
     * @param $mid
     * @param $wid
     */
    public function getCash($mid, $wid)
    {
        $page = $this->request->input('page')??1;
        return (new CashLogService())->getMemberCash($mid, $wid, $page);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 待到账金额
     * @param $mid
     * @param $wid
     * @return mixed
     */
    public function getStayAmount($mid, $wid)
    {
        return Income::where('mid', $mid)->where('wid', $wid)->where('status', 0)->sum('money');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 已提现金额
     */
    public function extractAmount($mid, $wid)
    {
        return CashLog::where('wid', $wid)->where('mid', $mid)->where('status', 2)->sum('money');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171116
     * @desc 获取收入记录
     */
    public function getIncome($mid, $wid)
    {
        $page     = $this->request->input('page')??1;
        $pagesize = config('database.perPage');
        $offset   = ($page - 1) * $pagesize;
        $income   = Income::where('mid', $mid)->where('wid', $wid)->where('status','!=','-3')->skip($offset)->take($pagesize)->get()->load('order')->toArray();
        return $income;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171118
     * @desc 获取用户提现数据
     * @param $mid
     * @param $wid
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getWithdrawals($mid, $wid)
    {
        $memberData = (new MemberService())->model->find($mid);
        if ($memberData){
            $memberData = $memberData->toArray();
        }
        $bankInfo   = DistributeBank::where('mid', $mid)->orderBy('is_default', 'DESC')->first();
        if ($bankInfo) {
            $bankInfo = $bankInfo->toArray();
        } else {
            $bankInfo = array();
        }
        //$result = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $result = $shopService->getRowById($wid);
        $company_pay = $result['company_pay']??0;
        return [
            'cash'        => round($memberData['cash'], 2),
            'bank'        => $bankInfo,
            'company_pay' => $company_pay?1:0,
        ];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171116
     * @desc 提现
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function withdrawals($mid, $wid, $source = 0)
    {
        $input         = $this->request->input();
        $memberService = new MemberService();
        $memberData    = $memberService->model->find($mid)->toArray();
        if ($input['money'] < 0) {
            $result['errCode'] = -3;
            $result['errMsg']  = '提取金额必须大于0';
            return $result;
        }
        if ($input['money'] > $memberData['cash']) {
            $result['errCode'] = -1;
            $result['errMsg']  = '最大可提取' . $memberData['cash'];
            return $result;
        }
        //判断账户是否存在
        $bank = [];
        if ($input['bank_id'] != 0) {
            $bank = DistributeBank::find($input['bank_id']);
            if (!$bank) {
                $result['errCode'] = -2;
                $result['errMsg']  = '账号不存在';
                return $result;
            }
            $bank = $bank->toArray();
            if ($bank['is_default'] != 1) {
                DistributeBank::where('id', $input['bank_id'])->update(['is_default' => 1]);
            }
        }
        if ($input['bank_id'] == 0 && $input['money'] < 1) {
            $result['errCode'] = -2;
            $result['errMsg']  = '提现金额必须大于1元';
            return $result;
        }
        //$shopData = (new WeixinService())->getStore($wid);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($wid);
        $withdraw_grade = $shopData['withdraw_grade']??0;
        if ($withdraw_grade>0 && $withdraw_grade>$input['money']){
            $result['errCode'] = -2;
            $result['errMsg']  = '提现金额必须大于' . $withdraw_grade . '元';
            return $result;
        }
        if ($shopData['company_pay'] == '0' && !$bank){
            $result['errCode'] = -3;
            $result['errMsg']  = '提现参数有误！请联系商家';
            return $result;
        }

        $cashLog        = [
            'mid'       => $mid,
            'money'     => $input['money'],
            'type'      => $bank['type']??'3',
            'name'      => $bank['name']??'',
            'type_name' => $bank['bank_name']??'',
            'account'   => $bank['account']??'',
            'wid'       => $wid,
            'source'    => $source,
        ];
        $cashLogService = new CashLogService();
        DB::beginTransaction();
        if ($shopData['company_pay'] == '2'){
            $cashLog['status'] = 2;
            $res1 = $memberService->decrement($mid, 'cash', $input['money']);
            $res2 = $cashLog['id'] = $cashLogService->add($cashLog);
            $result = $this->autoCompanyPay($cashLog);
            if (!$res1 || !$res2 || $result['errCode']) {
                \Log::info(['cashLog'=>$cashLog,'result'=>$result]);
                DB::rollBack();
                $result['errCode'] = -1;
                $result['errMsg']  = '提现失败请联系商家';
                return $result;
            }
        }else{
            $res1 = $memberService->decrement($mid, 'cash', $input['money']);
            $res2 = $cashLogService->add($cashLog);
            if (!$res1 || !$res2) {
                DB::rollBack();
                $result['errCode'] = -1;
                $result['errMsg']  = '操作失败';
                return $result;
            }
        }


        DB::commit();
        // 发送提现信息到日志服务器(数据中心) hsz
//        $job = new DataStatisticsDistributeWithdrawal($wid, $mid, $input['money'], $bank['type'], $source);
//        dispatch($job->onQueue('DataStatisticsDistributeWithdrawal'));

        $result['errCode'] = 0;
        $result['errMsg']  = '操作成功';
        return $result;
    }





    /**
     *
     * @param $cashLog
     * @author 张永辉 2018年11月21日
     */
    public function autoCompanyPay($cashData)
    {
        $result         = ['errCode' => 0, 'errMsg' => ''];
        $memberData = (new MemberService())->getRowById($cashData['mid']);
        if (!$memberData) {
            $result['errCode'] = -2;
            $result['errMsg']  = '用户不存在';
            return $result;
        }
        $orderData = [
            'order_num'   => 'C' . OrderCommon::createOrderNumber(),
            'relation_id' => $cashData['id'],
            'amount'      => intval(strval($cashData['money'] * 100)),
            'mid'         => $cashData['mid'],
            'name'        => $cashData['name'],
            'type'        => '1',
            'wid'         => $cashData['wid'],
        ];
        if ($cashData['source'] == 0) {
            $orderData['openid'] = $memberData['openid'];
        } else {
            $orderData['openid'] = $memberData['xcx_openid'];
        }
        $res = (new CompanyPayOrderService())->add($orderData);

        return (new WeChatRefundModule())->mmpaymkttransfers($orderData, $this, $cashData['source']);
    }



    public function getMyAccount($mid)
    {
        return DistributeBank::where('mid', $mid)->get()->toArray();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取银行账户
     */
    public function getBank()
    {
        return Bank::get()->toArray();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 取消成为分销客
     * @param $mid
     * @param $wid
     * @param int $tag
     */
    public function cancelDistribute($mid, $wid, $tag = 1)
    {
        $key         = $this->getKey($mid, $wid);
        $redisClient = (new RedisClient())->getRedisClient();
        $redisClient->SET($key, 1);
        if ($tag == 1) {
            $timeOut = 7 * 86400;
            $redisClient->EXPIRE($key, $timeOut);
        }
        return true;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171117
     * @desc 添加下级
     * @param $mid
     * @param $ids
     */
    public function addJunior($mid, $ids, $wid)
    {
        $memberService = new MemberService();
        $where         = [
            'id'  => ['in', $ids],
            'wid' => $wid,
            'pid' => 0,
        ];
        $res           = $memberService->getMemberIds($where);
        $memberIds     = [];
        foreach ($res as $val) {
            $memberIds[] = $val['id'];
        }
        if ($memberIds) {
            return (new MemberService())->batchUpdate($ids, ['pid' => $mid]);
        } else {
            return false;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171120
     * @desc 添加佣金
     * @param $id
     * @param $cash
     * @param $wid
     */
    public function addCash($id, $cash, $wid)
    {

        $memberService = new MemberService();
        if (!($memberService->getList(['id' => $id, 'wid' => $wid]))) {
            $result['errCode'] = -3;
            $result['errMsg']  = '该用户不属于该店铺';
            return $result;
        }

        $incomeData = [
            'mid'    => $id,
            'wid'    => $wid,
            'omid'   => 0,
            'money'  => $cash,
            'oid'    => 0,
            'status' => 1,
        ];
        DB::beginTransaction();
        $res = Income::insertGetId($incomeData);
        if (!$res) {
            DB::rollBack();
            $result['errCode'] = -1;
            $result['errMsg']  = '操作失败';
            return $result;
        }
        $res = $memberService->increment($id, 'cash', $cash);
        if (!$res) {
            DB::rollBack();
            $result['errCode'] = -2;
            $result['errMsg']  = '添加失败';
            return $result;
        }
        DB::commit();
        $result['errCode'] = 0;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171121
     * @desc 是否能够绑定上下级关系
     * @param $wid
     * @param $mid
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isBind($memebr)
    {
        /*$shopData = (new WeixinService())->getStore($memebr['wid']);
        if ($shopData['errCode'] != 0){
            return false;
        }
        $shopData = $shopData['data'];*/
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($memebr['wid']);
        if (empty($shopData)) {
            return false;
        }
        if ($shopData['is_distribute'] == 0){
            return false;
        }
        if ($shopData['distribute_grade'] == 0 || ($shopData['distribute_grade'] == 1 && $memebr['is_distribute'] == 1)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171122
     * @desc 团购分销分钱
     * @param $order
     * @param $wid
     * @return array
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function groupsDistribute($order,$type='1')
    {
        $result   = [
            'success' => 0,
            'message' => '',
        ];
        //$shopData = (new WeixinService())->getStore($order['wid']);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($order['wid']);
        if (empty($shopData)|| $shopData['is_distribute'] != 1){
            $result['message'] = '店铺未开启分销';
            return $result;
        }

        if ($order['pay_price'] <= 0) {
            $result['message'] = '订单金额为0';
            return $result;
        }

        $count = Income::where('oid', $order['id'])->count();
        if ($count > 0) {
            $result['message'] = '该订单已分配过佣金';
            return $result;
        }

        $template = $this->getGroupsTemplate($order['groups_id']);
        if ($template['success'] == 0) {
            return $template;
        }
        $template = $template['data'];

        $orderDetail = (new OrderDetailService())->init()->model->where('oid', $order['id'])->get(['id', 'oid', 'price', 'num'])->toArray();
        if (!$orderDetail) {
            $result['message'] = '订单详情不存在';
            return $result;
        }
        $orderDetail = $orderDetail[0];
        foreach ($template as $key=>$item){
            $allPay      = bcsub($order['pay_price'], $order['freight_price'], 2);
            $cost        = $orderDetail['price'] * $orderDetail['num'] * $item['cost'] / $item['price'];
            $remainder   = bcsub($allPay, $cost, 2);
            if ($remainder <= 0) {
                $result['message'] = '分销金额为0';
                return $result;
            }
            $all           = $item['zero'] + $item['one'] + $item['sec'] + $item['three'];
            $data[$key]['zero']  = $remainder * $item['zero'] / $all;
            $data[$key]['one']   = $remainder * $item['one'] / $all;
            $data[$key]['two']   = $remainder * $item['sec'] / $all;
            $data[$key]['three'] = $remainder * $item['three'] / $all;
            $logData       = [
                'oid'       => $order['id'],
                'allPay'    => $allPay,
                'cost'      => $cost,
                'remainder' => $remainder,
                'template'  => $item,
                'data'      => $data,
            ];
            Log::info('分销佣金计算数据：');
            Log::info($logData);
        }
        $mid = $order['mid'];
        $source  = ['zero','one','two','three'];
        foreach ($source as $key => $val) {
            if ($mid == 0) {
                break;
            }
            $member = Member::select(['id', 'pid', 'distribute_grade_id', 'is_distribute'])->find($mid)->toArray();
            if (!empty($shopData['distribute_grade']) && $shopData['distribute_grade'] == 1 && $key > 0 && $member->is_distribute == 0) {
                break;
            }
            if ($data[$member['distribute_grade_id']][$val] >= 0.01) {
                $incomeData = [
                    'mid'   => $mid,
                    'wid'   => $order['wid'],
                    'omid'  => $order['mid'],
                    'money' => round($data[$member['distribute_grade_id']][$val],2),
                    'oid'   => $order['id'],
                    'level' => $key,
                ];
                if ($type == '2') {
                    $incomeData['status'] = '-3';
                }
                Income::insert($incomeData);
                //todo 何书哲 2018年11月05日 下级下单的佣金提醒
                $commissionData = [
                    'mid' => $mid,
                    'oid' => $order['oid'],
                    'money' => round($data[$member['distribute_grade_id']][$val],2),
                    'commission_type' => 'commission_order'
                ];
                $order['source'] == 0 && (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant))->sendMsg($commissionData);
                $order['source'] == 1 && (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($commissionData, $order['xcx_config_id']);
            }
            $mid = $member['pid'];
        }
        (new OrderService())->init()->where(['id' => $order['id']])->update(['distribute_type' => 1], false);
        $result['success'] = 1;
        return $result;
    }



    /**
     * @param $data
     * @param string $key
     * @return array
     * @author 张永辉 2018年12月07日处理分销
     */
    public function handKey($data,$key='id')
    {
        $result = [];
        foreach ($data as $val){
            $result[$val[$key]] = $val;
        }
        return $result;
    }




    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171122
     * @desc 获取团末班
     * @param $gid
     */
    public function getGroupsTemplate($gid)
    {
        $result     = ['success' => 0, 'message' => ''];
        $groupsData = (new GroupsService())->getGroupsInfo($gid);
        if (!$groupsData) {
            $result['message'] = '团不存在';
            return $result;
        }
        $groupsData = current($groupsData);
        $tid        = $groupsData['distribute_template_id'];
        if ($tid == 0) {
            $result['message'] = '该团不存在分销';
            return $result;
        }
        $template = DistributeTemplate::where('id', $tid)->get()->load('gradeTemplate')->toArray();
        if (!$template) {
            $result['message'] = '团不存在';
            return $result;
        } else {
            $res = [];
            foreach ($template as $val) {
                $temp[0] = [
                    'id'         => $val['id'],
                    'grade_id'      =>0,
                    'wid'        => $val['wid'],
                    'price'      => $val['price'],
                    'cost'       => $val['cost'],
                    'zero'       => $val['zero'],
                    'one'        => $val['one'],
                    'sec'        => $val['sec'],
                    'three'      => $val['three'],
                    'created_at' => $val['created_at'],
                    'updated_at' => $val['updated_at'],
                ];

                $res[$val['id']] = array_merge($temp,$val['gradeTemplate']);
                $res[$val['id']] = $this->handKey($res[$val['id']],'grade_id');
            }
            $result['success'] = 1;
            $result['data']    = current($res);
            return $result;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171130
     * @desc 小程序个人中心是否显示我的财富
     * @param $mid
     * @param $wid
     * @return int
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isShowWealth($mid, $wid)
    {
        $member = (new MemberService())->getRowById($mid);
        if ($member['cash'] > 0) {
            return 1;
        }
        /*$weixin = (new WeixinService())->init()->model->where('id',$wid)->get()->toArray();
        if (!$weixin){
            return 0;
        }
        $weixin = current($weixin);*/
        $shopService = new ShopService();
        $weixin = $shopService->getRowById($wid);
        if (empty($weixin)) {
            return 0;
        }
        if($weixin['is_distribute']==0) {
            return 0;
        } else {
            if ($weixin['distribute_grade'] == 0 || ($weixin['distribute_grade'] == 1 && $member['is_distribute'] == 1)) {
                return 1;
            } else {
                return 0;
            }

        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171220
     * @desc 获取商品分享
     * @param $shopData
     * @param $max
     * @param $mid
     * @param $tid
     * @return float|int
     */
    public function getProductDistributePrice($shopData, $max, $mid, $tid)
    {
       $member = (new MemberService())->getRowById($mid);
        $price = $rate= $rateSec = 0;
      if ($shopData['is_open_weath']==1 && $shopData['is_distribute'] ==1 && ($shopData['distribute_grade'] == 0 || ($shopData['distribute_grade'] == 1 && $member['is_distribute'] == 1)) && $tid !=0){
          $res = DistributeTemplate::find($tid);
          if (!$res){
              return 0;
          }
          $res = $res->load('gradeTemplate')->toArray();
          $res = $this->handDistribute([$res]);
          $res = array_pop($res);
          $res = $res[$member['distribute_grade_id']];
          $rate = $res['one'];
          $rateSec = $res['sec'];
          $price = round(($max*$rate)/100,2);
      }
        return [$price,$rate,$rateSec];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180408
     * @desc 创建打款订单
     */
    public function createCompanyPayOrder($cashId)
    {
        $result         = ['errCode' => 0, 'errMsg' => ''];
        $cashLogService = new CashLogService();
        $cashData       = $cashLogService->getRowById($cashId);
        if (!$cashData) {
            $result['errCode'] = -1;
            $result['errMsg']  = '提现记录不存在';
            return $result;
        }
        $memberData = (new MemberService())->getRowById($cashData['mid']);
        if (!$memberData) {
            $result['errCode'] = -2;
            $result['errMsg']  = '用户不存在';
            return $result;
        }
        $orderData = [
            'order_num'   => 'C' . OrderCommon::createOrderNumber(),
            'relation_id' => $cashId,
            'amount'      => intval(strval($cashData['money'] * 100)),
            'mid'         => $cashData['mid'],
            'name'        => $cashData['name'],
            'type'        => '1',
            'wid'         => $cashData['wid'],
        ];
        if ($cashData['source'] == 0) {
            $orderData['openid'] = $memberData['openid'];
        } else {
            $orderData['openid'] = $memberData['xcx_openid'];
        }
        $res = (new CompanyPayOrderService())->add($orderData);
        return $res;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180411
     * @desc 分销企业付款
     */
    public function distributePay($id)
    {
        $companyPayService = new CompanyPayOrderService();
        $orderData         = $companyPayService->getRowById($id);
        $cashData          = (new CashLogService())->getRowById($orderData['relation_id']);
        return (new WeChatRefundModule())->mmpaymkttransfers($orderData, $this, $cashData['source']);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180411
     * @desc 支付回调页面
     * @param $res
     */
    public function companyPayCallBack($res)
    {
        $companyPayService = new CompanyPayOrderService();
        $data              = [
            'payment_no'   => $res['payment_no'],
            'payment_time' => $res['payment_time'],
            'pay_status'   => 1
        ];
        $partner_trade_no  = $res['partner_trade_no'];
        $res               = $companyPayService->update(['order_num' => $partner_trade_no], $data);
        //将微信分销数据写入日志服务器 hsz
        if (($company = CompanyPayOrder::where('order_num', $partner_trade_no)->first()) && ($cash = CashLog::find($company->relation_id))) {

            $job = new DataStatisticsDistributeWithdrawal($cash->wid, $cash->mid, $cash->money, $cash->type, $cash->source, time());
            dispatch($job->onQueue('dsDraw'));
        }

        if ($res) {
            $result['errCode'] = 0;
            $result['errMsg']  = '付款成功';
            return $result;
        } else {
            \Log::info('企业付款成功，更新订单失败,返回信息：' . $res);
            $result['errCode'] = -32;
            $result['errMsg']  = '更新订单失败';
            return $result;
        }
    }

    /**
     *
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180411
     * @desc 同意提现操作
     */
    public function dealAgree($id)
    {
        $result = ['errCode' => 0, 'errMsg' => ''];
        $res    = (new CompanyPayOrderService())->isExist($id, 1);
        if ($res) {
            $result = $this->distributePay($res['id']);
        } else {
            $res = $this->createCompanyPayOrder($id);
            if ($res['errCode'] == 0) {
                $result = $this->distributePay($res);
            } else {
                $result['errCode'] = -21;
                $result['errMsg']  = '创建支付订单失败';
            }
        }
        return $result;
    }



    /**
     * 处理申请用户数据
     * @author 张永辉 2018年9月29日
     */
    public function applyMember($data)
    {
        $memberService = new MemberService();
        $memberids     = array_column($data, 'mid');
        $res           = $memberService->getListById($memberids);
        $memberData    = [];
        foreach ($res as $val) {
            $memberData[$val['id']] = $val;
        }
        foreach ($data as &$item) {
            $item['memberData'] = $memberData[$item['mid']]??[];
        }
        return $data;
    }


    /**
     * 是否显示分销相关
     * @param $wid
     * @param $mid
     * @return bool
     * @author 张永辉 2018年10月17日
     */
    public function distributePermission($wid, $mid)
    {
        $shopData = (new ShopService())->getRowById($wid);

        if ($shopData['is_distribute'] == 0) {
            return false;
        }
        $memberData = (new MemberService())->getRowById($mid);
        if ($shopData['distribute_grade'] == 0 || ($shopData['distribute_grade'] == 1 && $memberData['is_distribute'] == 1)) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * 添加分销等级
     * @author 张永辉 2018年12月06日
     */
    public function addStoreDistributeGrade($input)
    {
        $result = ['errCode'=> 0,'errMsg'=>'','data'=>''];
        if (!empty($input['pids']) && !is_array($input['pids'])){
            $result['errCode'] = 10032;
            $result['errMsg'] = '商品必须是数组';
            return $result;
        }
        $distributeGradeService = new DistributeGradeService();
        if ($distributeGradeService->count(['wid' => $input['wid']]) >= 2 && empty($input['id'])) {
            $result['errCode'] = 10032;
            $result['errMsg'] = '最多只能有三个等级';
            return $result;
        }

        $insertData = [
            'title' => $input['title'],
            'wid'   => $input['wid'],
            'total_amount' => $input['total_amount']??0,
            'extension_amount'=> $input['extension_amount']??0,
            'pids'      => implode(',',$input['pids']),
            'grade'    => $input['grade']??0,
        ];

        if (empty($input['id'])){
            $result['data'] = $distributeGradeService->add($insertData);
            $this->handTemplate($result['data'],$input['wid']);
        }else{
            $distributeGradeService->updata($input['id'],$insertData);
            $result['data'] = $input['id'];
        }
        return $result;
    }


    /**
     * 处理分销模板
     * @author 张永辉 2018年12月06日
     */
    public function handTemplate($id,$wid)
    {
        $res = DistributeGrade::where('id', '!=', $id)->where('wid', $wid)->select(['id'])->first();

        if ($res) {
            $template = DistributeGradeTemplate::where('grade_id', $res->id)->get(['id', 'template_id', 'wid', 'price', 'cost', 'zero', 'one', 'sec', 'three'])->toArray();
        } else {
            $template = DistributeTemplate::where('wid', $wid)->get(['id', 'wid', 'price', 'cost', 'zero', 'one', 'sec', 'three'])->toArray();
        }
        $insertData = [];
        foreach ($template as $val) {
            isset($val['template_id']) ? '' : $val['template_id'] = $val['id'];
            $val['grade_id'] = $id;
            unset($val['id']);
            $insertData[] = $val;
        }
        if ($insertData) {
            DistributeGradeTemplate::insert($insertData);
        }
        return true;

    }


    /**
     * 删除店铺分销等级
     * @author 张永辉 2018年12月06日
     */
    public function delStoreDistributeGrade($id)
    {
        $result = ['errCode'=> 0,'errMsg'=>'','data'=>''];
        $distributeGradeService = new DistributeGradeService();
        $distributeGradeService->del($id);
        DistributeGradeTemplate::where('grade_id',$id)->delete();
        $memberData = Member::where('distribute_grade_id',$id)->get(['id'])->toArray();
        if ($memberData){
            $mids = array_column($memberData,'id');
            (new MemberService())->batchUpdate($mids,['distribute_grade_id'=>0]);
        }
        return $result;
    }


    /**
     * 获取
     * @author 张永辉 2018年12月06日
     */
    public function getStoreDistributeGrade($wid)
    {
        $distributeGradeService = new DistributeGradeService();
        $result = $distributeGradeService->getByWhere(['wid'=>$wid],'id','asc');
        $pids = [];
        foreach ($result as &$val){
            $val['pids'] = explode(',',$val['pids']);
            $val['product_title'] = Product::where('id',$val['pids'][0])->value('title');
        }
        return $result;
    }


    /**
     * 分销商品列表
     * @param $request
     * @param $productService
     * @param $productSkuService
     * @param $distributeTemplateService
     * @param $memberService
     * @param $wid
     * @param $mid
     * @return mixed
     * @author 张永辉 2018年12月11日
     */
    public function productList($request, $productService, $productSkuService, $distributeTemplateService, $memberService,$wid,$mid)
    {
        $where        = [
            'wid'                    => $wid,
            'distribute_template_id' => ['!=', '0'],
            'is_distribution'        => '1',
            'status'                 => '1'
        ];
        $product      = $productService->listWithPage($where);
        $pids         = array_column($product[0]['data'], 'id');
        $templateIds  = array_column($product[0]['data'], 'distribute_template_id');
        $templateData          = $distributeTemplateService->getListByIds($templateIds);
        $memberData = $memberService->getRowById($mid);
        $grade_id = $memberData['distribute_grade_id'];
        $skuData = $productSkuService->getSkuListByPids($pids);
        foreach ($product[0]['data'] as &$item) {
            if (empty($skuData[$item['id']])) {
                $item['distribute_amount'] = round($item['price'] * $templateData[$item['distribute_template_id']][$grade_id]['one'] * 0.01, 2);
                $item['distribute_amount_sec'] = round($item['price'] * $templateData[$item['distribute_template_id']][$grade_id]['sec'] * 0.01, 2);
            } else {
                foreach ($skuData[$item['id']] as &$val){
                    $val['distribute_amount'] = round($val['price'] * $templateData[$item['distribute_template_id']][$grade_id]['one'] * 0.01, 2);
                    $val['distribute_amount_sec'] = round($val['price'] * $templateData[$item['distribute_template_id']][$grade_id]['sec'] * 0.01, 2);
                }
                $item['skuData'] = $skuData[$item['id']];
                $item['distribute_amount'] =$skuData[$item['id']][0]['distribute_amount'] ;
                $item['distribute_amount_sec'] =$skuData[$item['id']][0]['distribute_amount_sec'] ;
            }
        }
        return $product;
    }


    /**
     * 获取分销升级信息
     * @param $wid
     * @author 张永辉 2018年12月11日
     */
    public function getGradeInfo($wid,$grade_id)
    {
        $data = [];
        $gradeData = DistributeGrade::where('wid',$wid)->orderBy('id','desc')->get()->toArray();
        if (!$gradeData){
            return $data;
        }
        $data['title'] = (new ShopService())->getRowById($wid)['distribute_default_grade_title'] ?? '普通分销员';
        $data['grade'] = [];
        foreach ($gradeData as $val){
            if ($val['id'] == $grade_id){
                $data['title'] = $val['title'];
                break;
            }
            $data['grade'][] = $val;
        }
        return $data;
    }


    /**
     * 处理分销模板数据
     * @author 张永辉 2018年12月13日
     */
    public function handDistribute($data)
    {
        $result = [];
        foreach ($data as $val) {
            $temp[0] = [
                'id'         => $val['id'],
                'grade_id'      =>0,
                'wid'        => $val['wid'],
                'price'      => $val['price'],
                'cost'       => $val['cost'],
                'zero'       => $val['zero'],
                'one'        => $val['one'],
                'sec'        => $val['sec'],
                'three'      => $val['three'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at'],
            ];

            $result[$val['id']] = array_merge($temp,$val['gradeTemplate']);
            $result[$val['id']] = $this->handKey($result[$val['id']],'grade_id');
        }
        return $result;
    }







}






