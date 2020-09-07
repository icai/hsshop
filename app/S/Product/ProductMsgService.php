<?php

namespace App\S\Product;

use App\Lib\Redis\ProductMsg as MsgRedis;
use App\Model\ProductMsg;
use App\S\S;

class ProductMsgService extends S
{
    public function __construct()
    {
        parent::__construct('ProductMsg');
    }

    /**
     * @param int $productId 商品ID
     * @return array
     */
    public function getListByProduct($productId)
    {
        //获取规格id列表
        $msgModel = new ProductMsg();
        $msgIds = $msgModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        $msgRedis = new MsgRedis();
        $redisArr = $msgRedis->getArr($msgIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($msgIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $msgModel->whereIn('id', $queryFromDB)->get()->toArray();
            $msgRedis->setArr($dataNotInRedis);
        }

        return array_merge($redisArr, $dataNotInRedis);
    }
    
}