<?php

namespace App\Console\Commands;
use App\Module\RefundModule;
use App\S\Member\MemberService;
use OrderLogService;
use DB;
use Illuminate\Console\Command;
use OrderPointExtraRuleService as OOrderPointExtraRuleService;
use OrderPointRuleService as OOrderPointRuleService;
use OrderService;
use PointRecordService as OPointRecordService;

class AutoFinishOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoFinishOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单自动完成';

    /**
     * Execute the console command.
     *
     * todo  
     * @return mixed
     * @update 何书哲 2018年11月19日 添加外卖订单（连接delivery_config，按设置发货后自动确认收货时间来走）
     */
    public function handle()
    {
        $checkTime = (time() - (7*24*3600));
        DB::table('order as o')
        ->leftJoin('delivery_config as dc', 'o.wid', '=', 'dc.wid')
        ->select('o.id','o.oid','o.wid','o.mid','o.use_point','o.coupon_id','dc.delivery_hour')
        ->where('o.status' ,2)
        ->whereRaw('UNIX_TIMESTAMP(ds_o.created_at) < IF(ds_o.is_takeaway = 1, ?-ds_dc.delivery_hour*3600, ?)', [time(), $checkTime])
        ->chunk(100, function($orders) {
            foreach ($orders as $order) {
                $mid = $order->mid;
                $wid = $order->wid;
                $oid = $order->oid;

                $orderData = OrderService::init('wid',$wid)->model->find($order->id)->load('orderLog')->load('orderDetail')->toArray();
                //判断该订单是否可以取消退款
                if ($orderData['status'] != 2)
                {
                    continue;
                }


                //todo 优化 不应该查询所有日志出来  15天没有读配置 写死在这
                $delay = false;
                $sendTime = 0;
                foreach ($orderData['orderLog'] as $val)
                {
                    if ($val['action'] == 13){
                        $delay = true;
                    }elseif ($val['action'] == 3){
                        $sendTime = strtotime($val['created_at']);
                    }
                }
                $sendTime = $delay === true ? ($sendTime + 259200) : $sendTime;

                $myTime = (time() - (7 * 86400));
                if ($sendTime > $myTime) {
                    continue;
                }
                echo $oid.";";
                //确认收货 如果有退款 关闭退款 Herry 20180314
                (new RefundModule())->closeAfterReceive($order->id);

                $res = OrderService::init('wid',$wid)->where(['id'=>$order->id,'status'=>2])->update(['id'=>$order->id,'status'=>3],false);
                $orderLogData = [
                    'oid'           => $order->id,
                    'wid'           => $wid,
                    'mid'           => $mid,
                    'action'        => 11,
                    'remark'        => '系统自动确认收货'
                ];
                OrderLogService::init('wid',$wid)->add($orderLogData,false);
                OrderService::upOrderLog($order->id, $wid);

                if ($res){
                    //确认收货分销佣金到账
//                    OrderService::getMoney($orderData['id']);

                    $point=0;
                    if($orderData['pay_price']>0) {
                        //当店铺开启订单赠送积分
                        $orderPointData = OOrderPointRuleService::getRowByCondition(['wid' => $wid, 'is_on' => 1]);
                        if ($orderPointData['errCode'] == 0 && !empty($orderPointData['data'])) {
                            //积分规则id
                            $id=$orderPointData['data']['id'];
                            //订单对应的积分
                            $orderPoint=intval($orderData['pay_price']*$orderPointData['data']['basic_rule']/100);

                            $whereData=[];
                            $whereData['p_id']=$id;
                            $whereData['used_money']=['<=',$orderData['pay_price']];
                            //查询订单积分对应的额外规则
                            $orderExtraRuleData=OOrderPointExtraRuleService::getListByConditionWithPage($whereData,'used_money','desc');

                            $orderExtraPoint=0;
                            if($orderExtraRuleData['errCode']==0&&!empty($orderExtraRuleData['data']))
                            {
                                //查询该订单对应的金额 额外积分
                                $orderExtraPoint=$orderExtraRuleData['data'][0]['reward_point'];
                            }
                            // 该订单总共积分
                            $point=intval($orderPoint+$orderExtraPoint);
                            if($point>0)
                            {
                                $pointRecordData=['wid'=>$wid,'mid'=>$mid,'point_type'=>1,'is_add'=>1,'score'=>$point];
                                //消费积分记录
                                OPointRecordService::insertData($pointRecordData);
                                //查询改用户当前积分
                                (new MemberService())->incrementScore($mid, $point);
                            }
                        }
                    }
                }
                echo $oid.";";
            }
        });
    }
}






