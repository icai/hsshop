<?php
/**
 * @author 吴晓平 <2018年07月27日>
 * @desc 支付宝小程序发送模板消息（暂时只支持订单支付成功后发送消息）
 */
namespace App\Module\AliApp;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayOpenAppMiniTemplatemessageSendRequest;

class SendMessageModule
{

	const PAY_SUCCESS_TEPM  = 'MmI1OWEwMmU5NDQ5ZDNiMTRiYzhiYmZkMzM0OTNiNWU='; //订单支付成功发送模板id
	/**
     * 构造函数
     * @return $this
     */
    public function __construct()
    {
        $this->return = [
            'errCode' => 1,
            'errMsg' => ''
        ];
    }
	/**
	 * [发送模板消息]
	 * @author 吴晓平 <2018年07月27日>
	 * @param  [int]     $toUserId       [发送的支付宝帐户]
	 * @param  [string]  $tradeNo         [用户发生的交易行为的交易号，或者用户在小程序产生表单提交的表单号，用于信息发送的校验。注：支付时则为trade_no的值]
	 * @param  [string]  $page           [小程序的跳转页面，用于消息中心用户点击之后详细跳转的小程序页面]
	 * @param  [array]   $data           [开发者需要发送模板消息中的自定义部分来替换模板的占位符（注意，占位符必须和申请模板时的关键词一一匹配]
	 * @return [type]                    [状态，状态说明]
	 */
	public function sendPayMessage($toUserId,$tradeNo,$page,$data)
    {
    	if (empty($toUserId)) {
    		$this->return['errCode'] = 1;
    		$this->return['errMsg'] = "发送的用户支付宝帐户为空";
    		\Log::info('发送模板消息的用户支付宝帐户为空');
    		return $this->return;
    	}
    	$param = [
			'to_user_id'       => $toUserId,
			'form_id'          => $tradeNo,
			'user_template_id' => self::PAY_SUCCESS_TEPM,
			'page'             => $page,
			'data'             => json_encode($data,JSON_UNESCAPED_UNICODE)
    	];
    	try {
    		$request = new AlipayOpenAppMiniTemplatemessageSendRequest ();
			$request->setBizContent(json_encode($param,JSON_UNESCAPED_UNICODE));
			$aliClientModule = new AliClientModule();
			$aliClientModule->appId = "2018070260569160";
			$result = $aliClientModule->execute($request); 
			 
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$resultCode = $result->$responseNode->code;
			if(!empty($resultCode)&&$resultCode == 10000){
				$this->return['errCode'] = 0;
				\Log::info("给用户 ".$toUserId.' 发送模板成功');
			} else {
				$this->return['errCode'] = $resultCode ?? 1;
				$this->return['errMsg']  = $result->$responseNode->sub_msg ?? '模板消息发送失败';
				\Log::info("给用户 ".$toUserId.' 发送模板失败');
			}
    	} catch (\Exception $e) {
    		$this->return['errCode'] = 1;
			$this->return['errMsg']  = '模板消息发送失败';
    		\Log::info('模板发送失败'.$e->getMessage());

    	}
		return $this->return;
    }
}