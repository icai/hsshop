<?php

namespace App\S\Product;

use App\Lib\Redis\ProductImg as ImgRedis;
use App\Model\ProductImg;
use App\S\S;

class ProductImgService extends S
{
    public function __construct()
    {
        parent::__construct('ProductImg');
    }

    /**
     * @param int $productId 商品ID
     * @return array
     */
    public function getListByProduct($productId)
    {
        //获取规格id列表
        $imgModel = new ProductImg();
        $imgIds = $imgModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        $imgRedis = new ImgRedis();
        $redisArr = $imgRedis->getArr($imgIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($imgIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $imgModel->whereIn('id', $queryFromDB)->get()->toArray();
            $imgRedis->setArr($dataNotInRedis);
        }
        
        return array_merge($redisArr, $dataNotInRedis);
    }

}