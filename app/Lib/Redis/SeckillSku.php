<?php
namespace App\Lib\Redis;

/**
 * 秒杀活动-商品库存
 */
class SeckillSku extends RedisInterface
{
    protected $prefixKey = 'seckill_sku';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    /**
     * 设置秒杀库存(资格) 用于判断库存数
     * @param $seckillID int 秒杀活动ID
     * @param $stock int 秒杀商品库存
     */
    public function setStockQualification($seckillID, $skuID, $stock)
    {
        $this->redis->SET($this->key . 'stock:' . $seckillID . ':' . intval($skuID), $stock);
    }

    /**
     * 秒杀库存资格列表 用于执行秒杀并发pop原子性
     * @param $seckillID
     * @param $skuID
     * @param $stock
     */
    public function setStockList($seckillID, $skuID, $stock)
    {
        $listKey = $this->key . 'list:' . $seckillID . ':' . intval($skuID);
        for ($i = 1; $i <= $stock; $i++) {
            $this->redis->LPUSH($listKey, 1);
        }
    }

    /**
     * 获取秒杀库存(资格)
     * @param $seckillID int 秒杀活动ID
     * @param $stock int 秒杀商品库存
     */
    public function getStockQualification($seckillID, $skuID)
    {
        return $this->redis->GET($this->key . 'stock:' . $seckillID . ':' . intval($skuID));
    }

    /**
     * 判断秒杀库存(资格) key是否存在
     * @param $seckillID
     * @param $skuID
     * @return mixed
     */
    public function doesStockQualificationExists($seckillID, $skuID)
    {
        return $this->redis->EXISTS($this->key . 'stock:' . $seckillID . ':' . intval($skuID));
    }

    /**
     * 减少秒杀库存(资格)-秒杀动作
     */
    public function decrStockQualification($seckillID, $skuID, $num)
    {
        $listKey = $this->key . 'list:' . $seckillID . ':' . intval($skuID);
        $list = $this->redis->pipeline(function ($pipe) use ($listKey, $num) {
            for ($i = 1; $i <= $num; $i++) {
                $pipe->LPOP($listKey);
            }
        });

        //获取成功pop资格数量 库存不足的$list可能是 [1,1,1,1,null,null] (剩余4件 购买6件的情况)
        $qualificationNum = array_sum($list);

        //资格满足 正常抢购
        if ($qualificationNum == $num) {
            //有库存 正常返回
            $key = $this->key . 'stock:' . $seckillID . ':' . intval($skuID);
            if ($this->redis->EXISTS($key)) {
                return $this->redis->DECRBY($key, $num);
            } else {
                //todo 如果库存数redis不存在 可能非正常清除 则库存资格push回列表 此次返回不能秒杀 待优化
                $this->redis->pipeline(function ($pipe) use ($listKey, $qualificationNum) {
                    for ($i = 1; $i <= $qualificationNum; $i++) {
                        $pipe->LPUSH($listKey, 1);
                    }
                });

                return -1;
            }
        } else {
            //库存不足
            //如果pop出的资格数量少于购买数量 说明用户这次购买数量大于剩余库存
            //则此次购买失败 pop出的资格数量 push回资格列表
            $this->redis->pipeline(function ($pipe) use ($listKey, $qualificationNum) {
                for ($i = 1; $i <= $qualificationNum; $i++) {
                    $pipe->LPUSH($listKey, 1);
                }
            });

            return -1;
        }
    }

    public function returnStock($seckillID, $skuID, $stock)
    {
        //库存list
        $listKey = $this->key . 'list:' . $seckillID . ':' . intval($skuID);
        for ($i = 1; $i <= $stock; $i++) {
            $this->redis->LPUSH($listKey, 1);
        }

        //库存key
        $key = $this->key . 'stock:' . $seckillID . ':' . intval($skuID);
        $this->redis->INCRBY($key, $stock);
    }
}