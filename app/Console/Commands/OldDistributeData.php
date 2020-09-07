<?php

namespace App\Console\Commands;

use App\S\Member\MemberService;
use App\Services\Order\OrderService;
use Illuminate\Console\Command;
use DB;

class OldDistributeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OldDistributeData {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'OldDistributeData {type}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $type = $this->argument('type');
        switch ($type) {
            case '1' :
                $this->dealTotalCash();
                break;
            case '2':
                $this->dealSonNum();
                break;
            case '3':
                $this->dealTradeAmount();
                break;
            default:
                $this->info('参数有误');
                break;
        }
    }


    /**
     * 获取总佣金
     * @author 张永辉
     */
    public function dealTotalCash()
    {
        $sql           = 'SELECT id,mid,SUM(money) as amount FROM ds_income WHERE `status`=1 GROUP BY mid';
        $result        = DB::select($sql);
        $result        = json_decode(json_encode($result), true);
        $memberService = new MemberService();
        foreach ($result as $item) {
            $this->info('mid=' . $item['mid'] . ';amount=' . $item['amount']);
            $memberService->updateData($item['mid'], ['total_cash' => $item['amount']]);
        }
    }


    /**
     * 统计下级用户数量
     * @author 张永辉
     */
    public function dealSonNum()
    {
        $sql           = 'SELECT id,pid,COUNT(id) as num FROM ds_member WHERE pid != 0 GROUP BY pid';
        $result        = DB::select($sql);
        $result        = json_decode(json_encode($result), true);
        $memberService = new MemberService();
        foreach ($result as $item) {
            $this->info('mid=' . $item['pid'] . ';num=' . $item['num']);
            $memberService->updateData($item['pid'], ['son_num' => $item['num']]);
        }
    }


    public function dealTradeAmount()
    {
        $orderService = new OrderService();
        $sql          = 'SELECT id,pid FROM ds_member WHERE pid !=0';
        $result       = DB::select($sql);
        $sourceData   = [];
        foreach ($result as $item) {
            $sourceData[$item->pid][] = $item->id;
        }
        $memberService = new MemberService();
        foreach ($sourceData as $key => $val) {
            $amount = $orderService->init()->whereIn('mid', $val)->sum('pay_price');
            $this->info('mid=' . $key . ';amount=' . $amount);
            if ($amount != 0) {
                $memberService->updateData($key, ['trade_amount' => $amount]);
            }
        }
    }



}
