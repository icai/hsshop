<?php

namespace App\Jobs;

use App\Module\LiShareEventModule;
use App\Module\MeetingGroupsRuleModule;
use App\S\Foundation\VerifyCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OrderService;

class SendMeetingGroupSMS implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;
    protected $phone;
    protected $groupsDetai;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone,$groupsDetail)
    {
        $this->phone = $phone;
        $this->groupsDetai = $groupsDetail;
        //
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

        $result  = (new LiShareEventModule())->registerByMobile([$this->phone]);
        if ($result){
            $smsNo = 12;
            $order = OrderService::init()->model->find($this->groupsDetai['oid']);
            if ($order){
                $order = $order->toArray();
                if ($order['wid'] == '661') {
                    $smsNo = 13;
                }
            }
            \Log::info('拼团发送短信:phone='.$this->phone);
            (new VerifyCodeService())->groupPurchaseNoitice($this->phone,[$this->phone,'12345678'],$smsNo);
        }
    }
}
