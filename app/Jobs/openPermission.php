<?php

namespace App\Jobs;

use App\Lib\Redis\openPermissionRedis;
use App\Module\StoreModule;
use App\S\Foundation\VerifyCodeService;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class openPermission implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input = [])
    {
        //
        $this->data = $input;
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

        $logData = [
            'phone'             => $this->data['phone'],
            'smsLog'            => [
                'isSendMsg' => $this->data['isSendMsg']
            ],
            'registerUserLog'   => [
                'isRegisterUserLog'=>$this->data['isRegisterUser']
            ],
            'createShopLog'     => [
                'isCreateShop'  => $this->data['isCreateShop']
            ]
        ];

        //判断是否注册账号
        if ($this->data['isRegisterUser']) {
            $logData['registerUserLog']['detail'] = (new UserService())->addUser(['mphone' => $this->data['phone']]);
        }

        //判断创建店铺
        if ($this->data['isCreateShop']) {
            $logData['createShopLog']['detail'] = (new StoreModule())->createShop($this->data['phone'], $this->data['permission'], 365);
        }


        //判断是否发送短信
        if ($this->data['isSendMsg']) {

            #todo 发送短信
            $sms_service = new VerifyCodeService();
            $smsRe = $sms_service->groupPurchaseNoitice($this->data['phone'], [$this->data['phone'], '12345678'], $this->data['msgTemplateId']);
            $logData['smsLog']['detail'] = $smsRe;
        }


        $logData['smsLog']          = json_encode($logData['smsLog']);
        $logData['registerUserLog'] = json_encode($logData['registerUserLog']);
        $logData['createShopLog']   = json_encode($logData['createShopLog']);
        $logData['created_at']      = time();
        //将信息存入缓存
        $re = (new openPermissionRedis())->addArr($logData);


    }

}
