<?php

namespace App\Console\Commands;

use App\Module\MallModule;
use Illuminate\Console\Command;

class ShopStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ShopStatistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计店铺销量等数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new MallModule())->shopStatistics();
    }
}