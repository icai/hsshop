<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/2
 * Time: 9:06
 */

namespace App\S\Market;


use App\Lib\Redis\MarketingActivityScoreRedis;
use App\S\S;

class ScoreService extends S
{

    /**
     * ScoreService constructor.
     */
    public function __construct()
    {
        parent::__construct('MarketingActivityScore');
    }

    /**
     * 创建积分奖项
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
        //加入redis
        $data = $re->toArray();
        $redis = new MarketingActivityScoreRedis();
        $redis->addArr($data);
        return $data['id'];
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 4)
    {
        $where['wid'] = session('wid');
        $where['left'] = ['>','0'];
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
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
        $redis = new MarketingActivityScoreRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null, 'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }


    /**
     * 通过主键获取积分奖项详情
     * author: meijie
     * @param $id
     * @return mixed
     */
    public function getInfoById($id)
    {
        $redis = new MarketingActivityScoreRedis();
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


    //修改积分库中的库存
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
        $redis = new MarketingActivityScoreRedis();
        $redis->incr($id,'left',-1);
        return true;
    }
}