<?php

namespace App\Jobs;

use App\Model\DistributeGrade;
use App\Model\Income;
use App\Model\Member;
use App\Model\OrderDetail;
use App\Module\DistributeModule;
use App\S\Member\MemberService;
use App\Services\Order\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class Distribution implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;

    public $order;
    public $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order, $type = '1')
    {
        //
        $this->order = $order;
        $this->type  = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DistributeModule $distributeModule, OrderService $orderService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        if ($this->type == '2') {
            Log::info('生成订单时执行队列Distribution，在' . date('Y-m-d H:i:s', time()) . '时执行，订单数据：');
        } else {
            Log::info('队列Distribution，在' . date('Y-m-d H:i:s', time()) . '时执行，订单数据：');
            $this->_delTempIncome();
            $this->upDistributeGrade();
        }
        Log::info($this->order);
        if ($this->type == '1') {
            $this->_upDistributeMember();
        }
//        if ($this->order['seckill_id'] != 0) {
//            Log::info('秒杀订单不参加分销');
//            return;
//        }
        if ($this->order['groups_id'] == 0) {
            $res = $orderService->distribute($this->order, $this->type);
        } else {
            $res = $distributeModule->groupsDistribute($this->order, $this->type);
        }
        Log::info('队列运行结束' . date('Y-m-d H:i:s', time()) . '返回结果:');
        Log::info($res);
    }


    /**
     * 更新分销相关信息
     * @author 张永辉
     */
    private function _upDistributeMember()
    {
        $memberService = new MemberService();
        $memberData    = $memberService->getRowById($this->order['mid']);
        if (empty($memberData['pid'])) {
            return true;
        }
        return $memberService->increment($memberData['pid'], 'trade_amount', $this->order['pay_price']);

    }

    /**
     * 删除生成订单是的到账信息
     * @author 张永辉 2018年10月10日
     */
    private function _delTempIncome()
    {
        Income::where('oid', $this->order['id'])->where('status', '-3')->delete();
    }


    /**
     * 升级分销等级
     * @author 张永辉 2018年12月10日
     */
    public function upDistributeGrade()
    {
        $res = DistributeGrade::where('wid', $this->order['wid'])->orderBy('id', 'desc')->get()->toArray();
        if (!$res) {
            return true;
        }
        $orderDetail = OrderDetail::where('oid', $this->order['id'])->get(['id', 'product_id'])->toArray();
        $memberGradeId = Member::where('id',$this->order['mid'])->value('distribute_grade_id');
        $orderPids   = array_column($orderDetail, 'product_id');
        $gradeId     = 0;
        foreach ($res as $val) {
            if ($val['id'] == $memberGradeId){
                break;
            }
            if ($val['pids'] && array_intersect($orderPids, explode(',', $val['pids']))) {
                $gradeId = $val['id'];
                break;
            }
        }
        if ($gradeId) {
            (new MemberService())->updateData($this->order['mid'], ['distribute_grade_id' => $gradeId,'is_distribute'=>1]);
        }

        return true;


    }


}
