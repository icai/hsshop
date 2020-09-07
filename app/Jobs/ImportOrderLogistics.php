<?php

namespace App\Jobs;

use App\Module\OrderLogisticsModule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 订单打单导入队列
 * @create 何书哲 2018年6月27日
 */
class ImportOrderLogistics implements ShouldQueue {
    use InteractsWithQueue, Queueable, SerializesModels;

    //店铺id
    protected $wid;
    //订单id
    protected $oid;

    /**
     * 创建订单打单导入任务实例
     * @param $wid 店铺id
     * @param $oid 订单id
     * @return 任务实例
     * @create 何书哲 2018年6月27日 创建订单打单导入任务实例
     */
    public function __construct($wid, $oid) {
        $this->wid = $wid;
        $this->oid = $oid;
    }

    /**
     * 执行队列任务
     * @return void
     * @create 何书哲 2018年6月27日 执行队列任务
     */
    public function handle() {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $module = new OrderLogisticsModule();
        //检测订单是否满足导入快递100条件
        $checkOrderRes = $module->checkOrderIfSend([$this->oid]);
        if ($checkOrderRes['status'] != 0) {
            \Log::info($checkOrderRes);
            return;
        }
        //检测店铺是否满足导入快递100条件
        $checkShopRes = $module->checkShopIfSend($this->wid);
        if ($checkShopRes['status'] != 0) {
            \Log::info($checkShopRes);
            return;
        }
        $sendRes = $module->orderSend($this->wid, $this->oid);
        \Log::info($sendRes);
    }

}