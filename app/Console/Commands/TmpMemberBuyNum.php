<?php

namespace App\Console\Commands;

use App\S\Member\MemberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TmpMemberBuyNum extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TmpMemberBuyNum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计用户购买次数';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //筛选创建时间是昨天的（购买量不准确，理论上小于真实值，当天付款当天退款统计不到）
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $beginTime = $yesterday.' 00:00:00';
        $endTime = $yesterday.' 23:59:59';
        $sql = 'SELECT mid,COUNT(id) AS buy_num FROM ds_order 
                WHERE `status` IN (1,2,3,7) AND `created_at`>=? AND `created_at`<=?
                GROUP BY mid';
        $res = DB::select($sql, [$beginTime, $endTime]);
        if ($res) {
            try {
                $memberService = new MemberService();
                $res = json_decode(json_encode($res), true);
                foreach ($res as $val) {
                    $memberService->increment($val['mid'], 'buy_num', $val['buy_num']);
                }
                \Log::info($yesterday.'统计用户购买次数成功');
            } catch (\Exception $e) {
                \Log::info($yesterday.'统计用户购买次数失败：'.$e->getMessage());
            }
        } else {
            \Log::info($yesterday.'购买次数为0，不更新');
        }
    }

}