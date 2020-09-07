<?php

namespace App\Console\Commands;

use App\Module\SeckillModule;
use Illuminate\Console\Command;

class AutoCancelSeckill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoCancelSeckill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '秒杀超时未支付自动取消订单';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new SeckillModule())->autoCancel();
    }
}






