<?php

namespace App\Jobs\Delay;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\Seckill;
use Carbon\Carbon;
use App\Module\SeckillModule;

class CancelNonPaymentSeckillOrder implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

	protected $seckillOrderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($seckillOrderId, $seckillId)
    {
        $this->seckillOrderId = $seckillOrderId;
		$seckillInfo =Seckill::find($seckillId);
		if (!is_null($seckillInfo)) {
			$this->delay(Carbon::now()->addMinutes($seckillInfo->cancel_minutes));
		}
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

		(new SeckillModule())->cancelTimeoutNonPaymentSeckillOrder($this->seckillOrderId);
    }
}
