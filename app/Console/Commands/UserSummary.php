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

class UserSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UserSummary {wid?} {--begin_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信公众号粉丝统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //参数调用方法
        $wid = $this->argument('wid') ?? 0;
        $begin_date = $this->option('begin_date') ?? '';
        $end_date = $this->option('end_date') ?? '';
        (new Bi())->getWeixinFansData($wid,$begin_date,$end_date);
    }
}