<?php
/**
 * Created by wuxiaoping.
 * Date: 2017/10/09
 */

namespace App\S;

use WeixinService;
use App\S\Weixin\ShopService;

class PublicShareService
{

    /**
     * 通用的分享设置
     * 2017.10.09 wuxiaoping
     * @param  [int] $wid [所在店铺的id]
     * @return [type]      [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function publicShareSet($wid)
    {   
        $shopService = new ShopService();
        //$storeInfo = WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = $shopService->getRowById($wid);
        $store=['store_name'=>'','logo_url'=>config('app.source_url').'mctsource/images/m1logo.png'];
        if(!empty($storeInfo)) {
            $store['store_name'] = $storeInfo['shop_name'];
            if (!empty($storeInfo['logo'])) {
                $store['logo_url'] = imgUrl() . $storeInfo['logo'];
            }
        }
        $shareData['share_title'] = $storeInfo['share_title'] ? $storeInfo['share_title'] : $store['store_name'];
        $shareData['share_desc']  = $storeInfo['share_desc'] ? str_replace(PHP_EOL, '', $storeInfo['share_desc']) :''; //去掉换行符
        $shareData['share_img']  = $storeInfo['share_logo'] ? imgUrl() .$storeInfo['share_logo'] : $store['logo_url'];

        return $shareData;
    }

}