<?php

namespace App\S\Market;

use App\Model\SeckillSku;
use App\S\S;
use App\Lib\Redis\SeckillSku as SkuRedis;

class SeckillSkuService extends S
{
    public function __construct()
    {
        parent::__construct('SeckillSku');
    }

    /**
     * 获取某次秒杀活动的商品库存列表
     * @param $id int 秒杀活动ID
     * @return array 库存列表
     */
    public function getListBySeckillID($id)
    {
        $model = new SeckillSku();
        $ids = $model->select('id')->where('seckill_id', $id)->pluck('id')->toArray();
        $redis = new SkuRedis();
        $redisArr = $redis->getArr($ids);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($ids as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $model->whereIn('id', $queryFromDB)->get()->toArray();
            $redis->setArr($dataNotInRedis);
        }

        return array_merge($redisArr, $dataNotInRedis);
    }

    /**
     * 根据秒杀ID删除秒杀商品库存
     */
    public function deleteSkuBySeckillID($id)
    {
        //获取id
        $model = new SeckillSku();
        $ids = $model->select('id')->where('seckill_id', $id)->pluck('id')->toArray();

        //删除sku
        $model->where('seckill_id', $id)->delete();
        (new SkuRedis())->deleteArr($ids);
    }

    /**
     * 根据条件获取详情
     */
    public function getRowByWhere($where)
    {
        //获取
        $row = $this->model->wheres($where)->first();
        if (empty($row)) {
            return [];
        }

        return $row->toArray();
    }

    /**
     * 获取秒杀商品某规格的库存
     * @param int $seckillID 秒杀id
     * @param int $skuID 商品规格id
     * @return int
     * @author 许立 2018年07月12日 直接读取数据库的库存
     */
    public function getStock($seckillID, $skuID)
    {
        $seckillSku = SeckillSku::select('seckill_stock')
            ->where('seckill_id', $seckillID)
            ->where('sku_id', $skuID)
            ->first();
        if (empty($seckillSku)) {
            $stock = 0;
        } else {
            $seckillSku = $seckillSku->toArray();
            $stock = $seckillSku['seckill_stock'];
        }

        return $stock;
    }

    /**
     * 更新数据库和redis
     */
    public function update($id, $data)
    {
        SeckillSku::where('id', $id)->update($data);
        $data['id'] = $id;
        (new SkuRedis())->updateRow($data);
    }


    public function updateReduce($id, $data,$num)
    {
        $res = SeckillSku::where('id', $id)->where('seckill_stock','>=',$num)->update($data);
        if (!$res){
            return false;
        }
        $data['id'] = $id;
        (new SkuRedis())->updateRow($data);
        return true;
    }
}