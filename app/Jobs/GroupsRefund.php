<?php

namespace App\Jobs;

use App\Module\WeChatRefundModule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupsRefund implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries =3;
    protected $oid;

    public function __construct($oid)
    {
        //
        $this->oid = $oid;
    }

    /**
     * Execute the job.
     *
        * @return void
        */
    public function handle(WeChatRefundModule $weChatRefundModule)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        //获取订单和商品信息

    }
}
