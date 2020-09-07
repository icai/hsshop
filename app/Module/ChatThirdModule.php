<?php
namespace App\Module;

use Log;
use MemberService;
use WeixinService;
use App\S\Foundation\RegionService;
use ProductService;
use App\Services\Permission\WeixinUserService;
use App\S\Order\OrderService;
use App\S\Weixin\ShopService;

class ChatThirdModule
{
    public function getChatData($userId, $shopId, $productId, $orderId)
    {
        $return = ['status' => 0];
        if ($userId > 0)
            $return['data']['userData'] = $this->getUserData($userId);

        if ($shopId > 0)
            list($return['data']['shopData'], $return['data']['manager']) = $this->getShopData($shopId);   

        if ($productId > 0)
            $return['data']['productData'] = $this->getProductData($productId); 

        if ($orderId > 0)
            $return['data']['orderData'] = $this->getOrderData($orderId); 

        return $return;
    }

    public function getUserOrderData($shopId, $userId)
    {
        $return = ['status' => 0];
        $return['data']['userOrderData'] = (new OrderService())->orderMidDetail($shopId, $userId); 
        if (isset($return['data']['userOrderData']) && !empty($return['data']['userOrderData'])) {
            foreach ($return['data']['userOrderData'] as $k => $value) {
                if (isset($value['orderDetail']) && !empty($value['orderDetail'])) {
                    foreach ($value['orderDetail'] as $k2 => $value2) {
                        if (isset($value2['img']) && !empty($value2['img'])) {
                            $return['data']['userOrderData'][$k]['orderDetail'][$k2]['img'] = config('app.source_img_url').$value2['img'];
                        }
                    }
                }
            }   
        }
        return $return;
    }

    public function getUserData($userId)
    {
        $memberData = MemberService::getRowById($userId);
        if (!empty($memberData) && !empty($memberData['province_id'])) {
            $info = (new RegionService())->getListById([$memberData['province_id'],$memberData['city_id'],$memberData['area_id']]);    
            $memberData['province'] = $info[$memberData['province_id']]['title'];
            $memberData['city'] = $info[$memberData['city_id']]['title'];
            $memberData['area'] = $info[$memberData['area_id']]['title'];
        }
        $memberData = !empty($memberData) ? $memberData : null;
        return $memberData;
    }

    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function getShopData($shopId)
    {
        $shopData = $manager = [];
        /*$shopRes =  WeixinService::getStore($shopId);
        if (isset($shopRes['data']) && !empty($shopRes['data'])) {
            $shopData = $shopRes['data'];
        }*/
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($shopId);
        if (!empty($shopData)) {
            $shopData['logo'] = $shopData['logo'] ? $shopData['logo'] : 'hsshop/image/static/m1logo.png';
            $shopData['logo'] = imgUrl().$shopData['logo'];
            $manager = (new WeixinUserService())->getUser($shopId);
        }
        return [$shopData, $manager];
    }

    public function getProductData($productId)
    {
        return ProductService::getDetail($productId);
    }

    public function getOrderData($orderId)
    {
        return (new OrderService())->getOrderDetail($orderId);
    }
}
