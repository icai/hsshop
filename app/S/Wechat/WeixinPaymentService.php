<?php
namespace App\S\Wechat;

use App\S\S;

class WeixinPaymentService Extends S{

	//设置model
	public function __construct()
	{
		parent::__construct('WeixinPayment');
	}

	public function getRowByWid($wid)
	{
		$row = $this->model->wheres(['wid'=>$wid])->first();
        if (empty($row)) 
        	return [];

        return $row->toArray();
	}

	public function getAppDataByWid($wid)
	{
		$weixinData = $this->getRowByWid($wid);
        if (empty($weixinData) || !isset($weixinData['config'])) 
            return false;

        $weiXinCfg = json_decode($weixinData['config'], true);
        return $weiXinCfg;
	}



	public function getConfData($wid)
    {
        $data = $this->getRowByWid($wid);
        if ($data) {
            $conf   = json_decode($data['config'], true);
            $verify = ['payee', 'app_id', 'app_secret', 'mch_id', 'mch_key'];
            foreach ($verify as $value) {
                if ( !isset($conf[$value]) || empty($conf[$value]) ) {
                    return false;
                }
            }
            return $conf;
        }else{
            return false;
        }
    }









}