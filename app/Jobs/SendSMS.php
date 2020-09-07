<?php

namespace App\Jobs;

use App\S\Wechat\WeixinSmsConfService;
use App\Services\Lib\RestService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;



    private $to; //接收方号码
    private $wid;
    private $flag;
    private $tempId;
    private $restService;
    private $oid;



    public $tries = 3;
    public $timeout = 60;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid,$oid = 0)
    {
        //
        $this->wid = $wid;
        $this->flag = 0;
        $this->oid = $oid;
        $re = (new WeixinSmsConfService())->getList(['wid'=>$wid]);
        if($re){
            $this->flag = 1;
            $conf  = $re[0];
            $this->restService = new RestService('app.cloopen.com', 8883, '2013-12-26');
            $this->restService->setAccount($conf['account_sid'], $conf['account_token']);
            $this->restService->setAppId($conf['app_id']);
            $this->tempId = $conf['code'];
            $this->to = $conf['phone'];
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

        if($this->flag){
            $data = [$this->oid];
            $xml = $this->restService->sendTemplateSMS($this->to, $data, $this->tempId);
            $re = json_decode(json_encode($xml),1);
            if($re['statusCode'] != 000000){
                Log::info("订单号：{$this->oid}的短信发送失败：".$re['statusMsg']);
            }
        }
    }


}
