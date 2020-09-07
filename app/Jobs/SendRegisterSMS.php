<?php

namespace App\Jobs;

use App\S\Foundation\VerifyCodeService;
use App\S\ShareEvent\LiRegisterService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRegisterSMS implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 3;
    public $timeout = 60;
    protected $parameter;


    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(VerifyCodeService $verifyCodeService,LiRegisterService $liRegisterService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $datas = [1235,1,123456789];
        $result = $verifyCodeService->sendCode($this->parameter['phone'],$datas,2);
        if($result->statusCode!=0) {
            \Log::info(__FILE__.'发送短信错误');
            $liRegisterService->batchUpdate([$this->parameter['id']],['is_sms' => 0]);
        }else{
            $liRegisterService->batchUpdate([$this->parameter['id']],['is_sms' => 2]);
        }
    }
}
