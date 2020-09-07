<?php
namespace App\S\Foundation;

use App\Services\Lib\RestService;

class VerifyCodeService
{

	//云通讯短信配置
	private $accountToken;
	private $accountSid;
	private $appId;

	/**
	 * [定义短信模板ID]
	 * 1：表示注册发送短信验证码
	 * 2：表示删除店铺发送短信验证码
	 * 3：表示订单支付成功后发送短信
	 * 4: 表示忘记密码发送验证码
	 * @var [type]
     * @update 张永辉 2018年6月27日 626 店铺发送总裁班课程短信
     * @update 何书哲 2018年9月18日 tempList 1,2,3,4由模板180461更改为335720，5由模板189428更改为328343，6由模板189833更改为328344
	 */
	private $tempList = [
            1 => '335720', //注册发送短信验证码
            2 => '335720', //删除店铺发送短信验证码
            3 => '335720', //订单支付成功后发送短信
            4 => '335720', //忘记密码发送验证码
            5 => '328343', //运营订单（您有新的订单，下单时间为{1}，请尽快处理。）
            6 => '328344', //详细运营订单（姓名：{1}，电话：{2}，行业：{3}，请及时处理。）
		    7 => '237377', //领取小程序通知
		    8 => '244519', //小程序拼团支付成功
		    9 => '244513', //小程序拼团进度提醒
		    10 => '244632', //小程序拼团成功提醒
		    11 => '244511', //小程序领取通知
		    12 => '246688', //最新团长通知 领取小程序模板
		    13 => '246686', //微商城
		    14 => '246523', //领取微商城
            15 => '258033', // 626 店铺发送总裁班课程短信
			16 => '261817', // 许立 2018年7月12日 7月9日活动会搜云系统领取通知
			17 => '261794', // 许立 2018年7月12日 7月9日活动汇推荐两人成功通知
	];

    public function __construct()
    {
		$this->accountToken = config('app.sms_account_token');
		$this->accountSid 	= config('app.sms_account_sid');
		$this->appId 		= config('app.sms_appid');   
    }

	/**
	 * [sendCode 短信模板/验证码 发送]
	 * @param  [int]   $phone       [接收短信的手机号]
	 * @param  [array] $datas       [发送的短信中所带的参数信息]
	 * @param  [int]   $type_code   [云通讯的短信对应的模板id]
	 * @return [obj]   $result      [返回结果集，包括状态码，状态信息]
	 */
	public function sendCode($phone,$datas,$type_code)
	{
        $restService  = new RestService('app.cloopen.com',8883,'2013-12-26'); 
        $restService->setAccount($this->accountSid,$this->accountToken); 
        $restService->setAppId($this->appId); 
        $result = $restService->sendTemplateSMS($phone,$datas,$this->tempList[$type_code]); 

        return $result;
	}

	/**
	 * 领取小程序通知
	 */
	public function getMiniAppNotice($phone,$datas,$type_code)
	{
		$restService  = new RestService('app.cloopen.com',8883,'2013-12-26');
		$restService->setAccount($this->accountSid,$this->accountToken);
		$restService->setAppId('8aaf070861f56d5c0161ffa85f8504c9');
		$result = $restService->sendTemplateSMS($phone,$datas,$this->tempList[$type_code]);

		return $result;
	}

	/**
	 * 拼团状态短信提示
	 * @param  [type] $phone     [发送的手机号]
	 * @param  [array] $datas     [模板内容参数]
	 * $type_code:8,9,10时，$datas为['拼团标题'];
	 * $type_code:11时，$datas为['账号'，'密码'];
	 * @param  [type] $type_code [模板类型]
	 * $type_code为8：小程序拼团支付成功
	 * $type_code为9：小程序拼团进度提醒
	 * $type_code为10：小程序拼团成功提醒
	 * $type_code为11：小程序领取通知
	 * @return [type]            [description]
	 */
	public function groupPurchaseNoitice($phone,$datas,$type_code)
	{
		\Log::info(['phone' => $phone,'data' => $datas,'type_code' => $type_code]);
		$restService  = new RestService('app.cloopen.com',8883,'2013-12-26');
		$restService->setAccount($this->accountSid,$this->accountToken);
		$restService->setAppId('8a216da862b935db0162cc4fe9000549');
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