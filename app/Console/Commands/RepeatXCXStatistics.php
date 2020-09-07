<?php

namespace App\Console\Commands;

use App\Module\MallModule;
use Illuminate\Console\Command;
use App\S\Foundation\Bi;

class RepeatXCXStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RepeatXCXStatistics';

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
        (new Bi())->repeatCommitXcxData();
    }
}