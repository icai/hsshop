<?php

namespace App\S\Product;

use App\Lib\Redis\ProductWholesale as WholesaleRedis;
use App\Model\ProductWholesale;
use App\S\S;

class ProductWholesaleService extends S
{
    public function __construct()
    {
        parent::__construct('ProductWholesale');
    }

    /**
     * @param int $productId 商品ID
     * @return array
     */
    public function getListByProduct($productId)
    {
        //获取规格id列表
        $wholesaleModel = new ProductWholesale();
        $wholesaleIds = $wholesaleModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        $wholesaleRedis = new WholesaleRedis();
        $redisArr = $wholesaleRedis->getArr($wholesaleIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($wholesaleIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $wholesaleModel->whereIn('id', $queryFromDB)->get()->toArray();
            $wholesaleRedis->setArr($dataNotInRedis);
        }
        
        return array_merge($redisArr, $dataNotInRedis);
    }

}