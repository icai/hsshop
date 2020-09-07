<?php

namespace App\Jobs\Delay;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use App\Module\OrderModule;

class CancelNonPaymentCommonOrder implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

	protected $commonOrderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($commonOrderId)
    {
        $this->commonOrderId = $commonOrderId;
		$this->delay(Carbon::now()->addMinutes(OrderModule::TIMEOUT_ORDER));
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

        (new OrderModule())->cancelTimeoutNonPaymentCommonOrder($this->commonOrderId);
    }
}
