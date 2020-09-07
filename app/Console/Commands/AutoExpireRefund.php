<?php

namespace App\Console\Commands;

use App\Module\RefundModule;
use Illuminate\Console\Command;

class AutoExpireRefund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoExpireRefund';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '买家商家退款处理逾期';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new RefundModule())->autoExpireRefund();
    }
}






