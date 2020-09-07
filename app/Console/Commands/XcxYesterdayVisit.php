<?php

namespace App\Console\Commands;

use App\S\Foundation\Bi;
use Illuminate\Console\Command;

class XcxYesterdayVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'XcxYesterdayVisit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计小程序昨日访问数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new Bi())->statisticsXCXData(0, date('Ymd', strtotime("-1 day")));
    }
}