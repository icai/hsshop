<?php

namespace App\Console\Commands;

use App\Http\Middleware\AliApp;
use App\Module\AliApp\AliAppModule;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayOpenAuthTokenAppRequest;
use App\Module\AliApp\VersionManageModule;
use App\S\AliApp\AliappConfigService;
use Illuminate\Console\Command;

class RefreshAliappToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RefreshAliAppToken';

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
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $aliappConfigService = new AliappConfigService();
        $aliClientModule = new AliClientModule();
        $requestParam = new AlipayOpenAuthTokenAppRequest();
        $nowtime = time();
        $where = [
            'expires_in'=> ['<',$nowtime],
        ];
        $configData = $aliappConfigService->getList($where);
        foreach ($configData as $val){
            $param = [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $val['app_refresh_token'],
            ];
            $requestParam->setBizContent(json_encode($param));
            $result = $aliClientModule->execute($requestParam);
            $responseNode = str_replace(".", "_", $requestParam->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            dump($result);
            if(!empty($resultCode) && $resultCode == 10000){
                $res = $this->refreshAliAppConfig($result->$responseNode,$val['wid'],$val['id']);
                if (!$res){
                    \Log::info('刷新令牌失败');
                }
            } else {
                \Log::info('刷新令牌失败');
                \Log::info(json_decode(json_encode($result),true));
            }
        }

    }


    /**
     * 更新token
     * @author 张永辉
     */
    public function refreshAliAppConfig($data,$wid,$id)
    {
        $data = json_decode(json_encode($data),true);
        $aliappConfigService = new AliappConfigService();
        $nowTime = time();
        $expiresIn = $nowTime+$data['expires_in']-1728000;   //提前20天重新获取令牌
        $reExpiresIn = $nowTime+$data['re_expires_in']-3600; //提前一个小时失效刷新令牌
        $param = [
            'wid'                => $wid,
            'user_id'           => $data['user_id'],
            'auth_app_id'       => $data['auth_app_id'],
            'app_auth_token'    => $data['app_auth_token'],
            'app_refresh_token' => $data['app_refresh_token'],
            'expires_in'         => $expiresIn,
            're_expires_in'      => $reExpiresIn,
        ];
        $aliappConfigService->update($id,$param);
//        $param['id'] = $id;
//        $versionManageModule = new VersionManageModule();
//        $versionManageModule->getAliappInfo($param);
        return true;
    }




}
