<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HandleCert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handleCert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @author 梅杰 20180724 小程序证书兼容处理脚本
     * @update 梅杰 20180725 修改小程序商户证书获取路径
     * @return mixed
     */
    public function handle()
    {
        //
        DB::table('wxxcx_config')->select('wid','id')->where(['current_status' => 0])->chunk(100,function ($config) {
            foreach ($config as $value) {
                //如果商户证书之前是否有上传
                $id = $value->id;
                $wid = $value->wid;
                $certPath = "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem";
                $keyPath = "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem";

                $newCertPath = "./hsshop/cert/{$wid}_cert/mini_cert_".$id."/apiclient_cert.pem";
                $newKeyPath = "./hsshop/cert/{$wid}_cert/mini_cert_".$id."/apiclient_key.pem";
                if ( Storage::exists($certPath) && Storage::exists($certPath) && !Storage::exists($newCertPath) && !Storage::exists($newKeyPath) ) {
                    //移动
                    Storage::copy($certPath,$newCertPath) && Storage::copy($keyPath,$newKeyPath);
                }

                $certPath = "./hsshop/cert/". $wid.'-'.$id."_cert/mini_cert/apiclient_cert.pem";
                $keyPath = "./hsshop/cert/".$wid.'-'.$id."_cert/mini_cert/apiclient_key.pem";

                if ( Storage::exists($certPath) && Storage::exists($certPath) && !Storage::exists($newCertPath) && !Storage::exists($newKeyPath)  ) {
                    //移动
                    if (Storage::copy($certPath,$newCertPath) && Storage::copy($keyPath,$newKeyPath)) {
                        Storage::deleteDirectory("./hsshop/cert/". $wid.'-'.$id."_cert");
                    }
                }



            }
        });

    }
}
