<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 10:52
 */

namespace App\S\Market;


use App\Lib\Redis\EggsSkuRedis;
use App\S\S;

class SmokedEggSkuService extends S
{

    /**
     * SmokedEggSkuService constructor.
     */
    public function __construct()
    {
        parent::__construct('MarketingActivityRuleEggs');
    }


    /**
     * 添加砸金蛋活动的奖品信息
     * author: meijie
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $re = $this->model->create($data);
        if (!$re) {
            return false;
        }
        $temp = $re->toArray();
        //存入Redis
        $eggsSkuRedis = new EggsSkuRedis();
        if ($eggsSkuRedis->addArr($temp)) {
            return true;
        }
        return false;
    }

    /**
     * author: meijie
     * @param $id
     * @return array
     */
    public function getInfoById($id)
    {
        $redis = new EggsSkuRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            //redis不存在 取数据库
            $row = $this->model->where('id', $id)->first();
            if (empty($row)) {
                return [];
            } else {
                $row = $row->toArray();
            }
            //保存redis
            $redis->add($row);
        }
        return $row;
    }


    public function update($id, $data = [])
    {
        $re = $this->model->where(['id' => $id])->update($data);
        if ($re === false) {
            return false;
        }
        //更新redis
        $data['id'] = $id;
        $data['update_at'] = date('Y-m-d H:i:s');
        $redis = new EggsSkuRedis();
        $redis->updateRow($data);
        return true;
    }

    // 更改库存
    public function updateStock($id)
    {
        $re = $this->model->where(['id'=>$id])->decrement('left');
        if($re === false)
        {
            return false;
        }
        //更新redis
        $data['id'] = $id;
        $data['update_at'] = date('Y-m-d H:i:s');
        $redis = new EggsSkuRedis();
        $redis->incr($id,'left',-1);
        return true;
    }
    
}