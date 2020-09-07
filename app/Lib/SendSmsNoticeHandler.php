<?php
/**
 * @author 吴晓平 <2019.02.18>
 * 官网申请发送短信通知
 */
namespace App\Lib;

use App\Services\Lib\RestService;

class SendSmsNoticeHandler
{

	//云通讯短信配置
	private $accountToken;
	private $accountSid;
	private $appId;

	/**
	 * [定义短信模板ID]
	 * 1：店铺到期提醒
	 */
	private $tempList = [
        1 => '427524', // 店铺到期提醒
	];

    public function __construct()
    {
		$this->accountToken = '9e4008a1f862450fa9bb4b09a7693465';   
		$this->accountSid 	= '8a48b5514f4fc588014f67a8f5182ea2';
		$this->appId 		= '8aaf07085c346c5a015c5c763fac0aeb';   
    }

	/**
	 * [sendNotice 短信模板/验证码 发送]
	 * @param  [int]   $phone       [接收短信的手机号]
	 * @param  [array] $datas       [发送的短信中所带的参数信息]
	 * @param  [int]   $type_code   [云通讯的短信对应的模板id]
	 * @return [obj]   $result      [返回结果集，包括状态码，状态信息]
	 */
	public function sendNotice($phone,$datas,$type_code)
	{
        $restService  = new RestService('app.cloopen.com',8883,'2013-12-26'); 
        $restService->setAccount($this->accountSid,$this->accountToken); 
        $restService->setAppId($this->appId); 
        $result = $restService->sendTemplateSMS($phone,$datas,$this->tempList[$type_code]); 

        return $result;
	}

    /**
     * 获取短信所有模板
     * @return array
     * @author: 梅杰 2018年07月30日
     */
	public function getTemplatesList()
    {
        return $this->tempList;
    }
}