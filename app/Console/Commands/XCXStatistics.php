<?php

namespace App\Console\Commands;

use App\Module\MallModule;
use Illuminate\Console\Command;
use App\S\Foundation\Bi;

class XCXStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'XCXStatistics {wid?} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '店铺小程序指定日期数据统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //参数调用方法
        $wid = $this->argument('wid') ?? 0;
        $date = $this->option('date') ?? '';
        (new Bi())->statisticsXCXData($wid,$date);
    }
}