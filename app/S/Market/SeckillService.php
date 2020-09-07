<?php

namespace App\S\Market;

use App\Model\Seckill;
use App\S\S;
use App\Lib\Redis\Seckill as SeckillRedis;

class SeckillService extends S
{
    public function __construct()
    {
        parent::__construct('Seckill');
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取秒杀活动详情
     */
    public function getDetail($id)
    {
        $redis = new SeckillRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            //redis不存在 取数据库
            $row = Seckill::where('id', $id)->first();
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

    /**
     * 根据主键id数组获取列表
     * @param array $idArr
     * @return mixed
     */
    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new SeckillRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 获取固定数据数组
     */
    public function getStaticList() {
        return ['all' => '所有活动', 'future' => '未开始', 'on' => '进行中', 'end' => '已结束'];
    }

    /**
     * 获取秒杀没有失效没有结束的商品
     * @return array
     */
    public function getSeckillingProductIDArr($wid)
    {
        $where = [
            'wid' => $wid,
            'invalidate_at' => '0000-00-00 00:00:00',
            'end_at' => ['>', date('Y-m-d H:i:s')]
        ];

        return Seckill::select('product_id')
            ->wheres($where)
            ->pluck('product_id')
            ->toArray();
    }

    /**
     * 检查活动是否 进行中且有效
     * @param $seckillID int 活动ID
     * @return bool 是否有效
     */
    public function checkValidity($seckillID)
    {
        $seckill = $this->getDetail($seckillID);
        if (empty($seckill)) {
            return false;
        }

        //检查有效性
        $now = date('Y-m-d H:i:s');
        if ($seckill['invalidate_at'] == '0000-00-00 00:00:00' && $seckill['start_at'] <= $now  && $seckill['end_at'] > $now) {
            return true;
        }

        return false;
    }

    /**
     * 检查商品是否正在参加秒杀活动
     * @param $ids array 商品ID数组
     */
    public function isProductSeckilling($ids)
    {
        //防止秒杀没保存商品ID
        if (in_array(0, $ids)) {
            return false;
        }
        $where = [
            'product_id' => ['in', $ids],
            'invalidate_at' => '0000-00-00 00:00:00',
            'end_at' => ['>', date('Y-m-d H:i:s')]
        ];

        $row = $this->model
            ->select('id')
            ->wheres($where)
            ->first();

        if (empty($row)) {
            return false;
        } else {
            return true;
        }
    }
}