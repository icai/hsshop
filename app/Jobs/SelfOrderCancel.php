<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/24
 * Time: 10:21
 */

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Module\FeeModule;
use App\Lib\BLogger;

class SelfOrderCancel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $id=0;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $orderId=$this->id;
        //BLogger::getLogger('info')->info('取消订单:'.$storeId);
        if($orderId>0)
        {
            (new FeeModule())->cancelSelfOrderForExpire($orderId);
        }
    }
}