<?php
namespace App\S\Wechat;

use App\S\Foundation\RegionService;
use App\S\S;
use App\Model\WeixinAddress;

class WeixinRefundService Extends S
{
    //设置model
    public function __construct()
    {
        parent::__construct('WeixinRefund');
    }



    /**
     * 获取退货地址
     * @param  $wid 店铺id
     * @param  $userInfo 用户信息  默认值获取session('userInfo');
     * @param [type] $type 地址类型
     * type = 1 获取退货地址  type=2 获取发货地址 type=3 同时获取退货与发货地址
     * @return [type] [description]
     */
    public function getDefaultAddress($wid,$userInfo,$type=1)
    {
    	if (empty($wid)) {
    		$returnData = ['error' => 100,'msg' => '请先选择相应的店铺'];
    		return $returnData;	
    	}

    	if(empty($userInfo)) {
    		$returnData = ['error' => 101,'msg' => '用户信息不正确'];
    		return $returnData;
    	}
        $addressInfo = $addressData = [];
        switch ($type) {
            case '1':
                //先获取默认的退货地址
                $obj = WeixinAddress::where(['wid'=>$wid,'is_default'=>1])->orWhere(['wid'=>$wid,'is_default'=>0,'type'=>1])->get();
                if($obj) {
                    $data = $obj->toArray();
                    foreach ($data as $key => $value) {
                        if($value['is_default'] == 1){
                            $addressInfo = $value;
                        }else{
                            $addressInfo = $data[0];
                        }
                    }
                }
                break;

            case '2':
                //先获取默认的发货地址
                $obj = WeixinAddress::where(['wid'=>$wid,'is_send_default'=>1])->orWhere(['wid'=>$wid,'is_send_default'=>0,'type'=>2])->get();
                if($obj) {
                    $data = $obj->toArray();
                    foreach ($data as $key => $value) {
                        if($value['is_send_default'] == 1){
                            $addressInfo = $value;
                        }else{
                            $addressInfo = $data[0];
                        }
                    }
                }
                break;
            case '3':
                //同时获取默认的发货地址与退货地址
                $obj = WeixinAddress::where(['wid'=>$wid,'type'=>3,'is_send_default'=>1,'is_default'=>1])->first();
                if($obj) {
                    $addressInfo = $obj->toArray();
                }
                break;
            
            default:
                # code...
                break;
        }
        //如果没设置默认的地址则按顺序获取第一条
        if(empty($addressInfo)) { 
            $res = WeixinAddress::where(['wid'=>$wid])->order('created_at DESC')->first();
            if($res){
                $addressInfo = $res->toArray();
            }else{  //如果后台没添加地址则读取创建店铺时所填的地址
                $weixinInfo = D('Weixin', 'uid', $userInfo['id'])->getInfo($wid);
                $addressInfo['name']        = $userInfo['name'];
                $addressInfo['mobile']      = $userInfo['mphone'];
                $addressInfo['province_id'] = $weixinInfo['province_id'];
                $addressInfo['city_id']     = $weixinInfo['city_id'];
                $addressInfo['area_id']     = $weixinInfo['area_id'];
                $addressInfo['address']     = $weixinInfo['address'];
            }
        }
        /*获取省市县地址*/
        $temp[] = $addressInfo['province_id'];
        $temp[] = $addressInfo['city_id'];
        $temp[] = $addressInfo['area_id'];
        $region = (new RegionService())->getListById($temp);
        /*返回具体数据*/
        $addressData['name'] = $addressInfo['name'] ?? '';
        $addressData['mobile'] = $addressInfo['mobile'] ?? '';
        $addressData['address'] = $region[$addressInfo['province_id']]['title'].$region[$addressInfo['city_id']]['title'].$region[$addressInfo['area_id']]['title'].$addressInfo['address'];
        $addressData['zip_code'] = $addressInfo['zip_code'] ?? '';
        $addressData['province_title'] = $region[$addressInfo['province_id']]['title'];//tainjia  fuguowei 20180116
        return $addressData;

    }
}