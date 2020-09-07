<?php

namespace App\Jobs;

use App\Services\Order\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LossDistributeIncome implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        //
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderService $orderService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        \Log::info('退款佣金流失'.$this->id);

        $res = $orderService->lossIncome($this->id);

        \Log::info('执行结果:'.$res);

    }
}
