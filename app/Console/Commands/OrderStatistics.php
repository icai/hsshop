<?php

namespace App\Console\Commands;

use App\Module\OrderModule;
use Illuminate\Console\Command;

class OrderStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrderStatistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计店铺订单数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new OrderModule())->orderStatistics();
    }
}