<?php
/**
 * Created by PhpStorm.
 * Author: wuxiaoping
 * Date: 2018/4/17
 * Time: 14:00
 */

namespace App\Console\Commands;

use App\Module\MallModule;
use Illuminate\Console\Command;
use App\S\Foundation\Bi;

class UserSummaryTiming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UserSummaryTiming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信公众号粉丝统计定时零晨2点运行';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //参数调用方法
        $wid = 0;
        $begin_date = date('Y-m-d',strtotime('-1 day'));
        $end_date = date('Y-m-d',strtotime('-1 day'));
        (new Bi())->getWeixinFansData($wid,$begin_date,$end_date);
    }
}