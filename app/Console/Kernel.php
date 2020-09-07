<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AutoExpireOrder::class,
        Commands\AutoFinishOrder::class,
        Commands\AutoFinishGroups::class,
        Commands\AutoCancelSeckill::class,
        Commands\HandleWXConfig::class,
        Commands\BiPageViewCount::class,
        Commands\CronBiToOnline::class,
        Commands\ValidateMicroPageComponentInfo::class,
        Commands\SwooleStart::class,
        Commands\AutoExpireRefund::class,
        Commands\DistributeInCome::class,
        Commands\OrderReview::class,
        Commands\SendRegisterSMS::class,
        Commands\CreateDataCenterTables::class,
        Commands\ShopStatistics::class,
        Commands\OrderStatistics::class,
        Commands\XCXStatistics::class,
        Commands\XcxYesterdayVisit::class,
        Commands\UserSummary::class,
        Commands\UserCumulate::class,
        Commands\TmpMemberBuyNum::class,
        Commands\TmpDistributeWithdrawal::class,
        Commands\TmpRechargeBalance::class,
        Commands\AdStatistics::class,
        Commands\OldOrderPayed::class,
        Commands\UserSummary::class,
        Commands\UserCumulate::class,
        Commands\OldRefundLog::class,
        Commands\FixProduct::class,
        Commands\RepeatXCXStatistics::class,
        Commands\OldOrderGroups::class,
        Commands\HandleCert::class,
        Commands\HandleCdn::class,
        Commands\createBiTable::class,
        Commands\OldShareEvent::class,
        Commands\AliAppDetailQuery::class, // 许立 2018年08月01日 小程序版本详情查询
        Commands\RefreshAliappToken::class, //张永辉 2018年8月02日 定时刷新过期令牌
        Commands\IsMember::class, //梅杰 2018年9月4日 是否是会员
        Commands\MemberPayAmount::class, //梅杰 2018年9月10日 用户消费金额统计脚本
        Commands\OldDistributeData::class, //张永辉 2018年9月6日 分销老数据
        Commands\CreateLoginTables::class, //何书哲 2018年9月19日 创建登录表（按天）
        Commands\AutoCheckDistributeApplay::class, //张永辉 2018年10月11日
        Commands\CheckXcxYesterdayVisit::class, //何书哲 2019年01月25日
        Commands\ShopExpireRemind::class, //吴晓平 2019年05月05日
        Commands\SyncShopLastLoginData::class, // 梅杰 2019年08月19日 11:59:57 每天同步店铺最后访问时间记录
        Commands\SyncOrderRefundData::class, // 梅杰 2019年08月19日 11:59:57 更新订单统计中的退款订单数
        Commands\RsyncCrm::class, // 陈文豪 2020年07月02日09:21:29 crm数据导入
        Commands\RsyncCrmHour::class, // 陈文豪 2020年07月02日09:21:29 crm数据导入
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('AutoExpireOrder')
                 ->everyMinute();
        $schedule->command('AutoFinishOrder')
                 ->everyMinute();
        $schedule->command('AutoFinishGroups')
            ->everyMinute();
        $schedule->command('AutoCancelSeckill')
            ->everyMinute();
        $schedule->command('AutoExpireRefund')
            ->everyMinute();
        $schedule->command('DistributeInCome')
            ->everyMinute();
        $schedule->command('AdStatistics')
            ->everyMinute();
        // 每小时插入crm数据
        $schedule->command('RsyncCrmHour')
            ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}