<?php

namespace App\Console\Commands;

use App\Module\LiShareEventModule;
use App\S\Foundation\VerifyCodeService;
use Illuminate\Console\Command;
use App\Module\StoreModule;
use App\Services\UserService;
use DB;
ini_set('max_execution_time', 0);

class SendRegisterSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendRegisterSMS';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '给注册免费领小程序用户发短信';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //sql:select DISTINCT phone from ds_li_register where created_at < '2018-03-07 00:00:00' ORDER BY id asc
        //20180307之前（id：200之前） 内网ds_li_register表的phone字段
        //20180307 只保留线上没有的手机号
        $phone_list = '13968230592';
        $phone_array = explode("\n", $phone_list);
        $sms_service = new VerifyCodeService();
        $success_array = [];
        $i = 0;

        foreach ($phone_array as $phone) {
            $i++;
            if ($i == 6) {
                sleep(1);
                $i = 1;
            }
            
            //注册新用户ds_user
            $user = DB::select("SELECT * FROM ds_user where mphone =". $phone);
            if (isset($user[0]->id) && !empty($user[0]->id)){
                continue;
            }

            (new LiShareEventModule())->registerByMobile([$phone]);
            $datas = [$phone, 12345678];
            $result = $sms_service->groupPurchaseNoitice($phone,$datas,14); //11 小程序   14 微商城

            (new StoreModule())->createShop($phone, 2, 365);  //6 小程序权限    1参会免费送

            if($result->statusCode!=0) {
                \Log::info('<SendRegisterSMS>发送短信失败<' . $phone . '>' . (string)$result->statusMsg);
            }else{
                $success_array[] = $phone;
            }
          
        }
        \Log::info('<SendRegisterSMS>验证码发送成功');
        \Log::info($success_array);
    }
}






