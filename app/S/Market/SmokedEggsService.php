<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 8:44
 */

namespace App\S\Market;


use App\Lib\Redis\EggsRedis;
use App\S\S;

class SmokedEggsService extends S
{

    /**
     * SmokedEggsService constructor.
     */
    public function __construct()
    {
        parent::__construct('MarketingActivityEgg');
    }


    /**
     * 添加活动
     * author: meijie
     * @param $data
     * @return 返回Id
     */
    public function addActivity($data)
    {
        $data['created_at'] = $input['updated_at'] = date('Y-m-d H:i:s');
        $data['deleted_at'] = null;
        $data['limit_json'] = \GuzzleHttp\json_encode($data['limit_json']);
        if(!empty($data['share_json'])){
            $data['share_json'] = \GuzzleHttp\json_encode($data['share_json']);
        }
        return $this->model->insertGetId($data);

    }

    /**
     * 通过主键获取砸金蛋活动详情
     * author: meijie
     * @param $id
     * @return mixed
     */
    public function getInfoById($id)
    {
        $redis = new EggsRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            //redis不存在 取数据库
            $row = $this->model->where('id', $id)->first();
            if ($row) {
                $row = $row->toArray();
                //保存redis
                $redis->add($row);
            }
        }
        return $row;
    }


    #todo 修改砸金蛋活动
    /**
     * 修改活动
     * author: meijie
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateActivity($id,$data)
    {
         if ($this->model->where(['id'=>$id])->update($data)) {
             $data['id'] = $id;
             $data['update_at'] = date('Y-m-d H:i:s');
             $redis = new EggsRedis();
             $redis->updateRow($data);
             return true;
         }
        return false;
    }


    #todo 删除砸金蛋活动
    /**
     * 按主键删除砸金蛋活动
     * author: meijie
     * @param $id
     * @return bool
     */
    public function delActivity($id,$wid)
    {
        $re = $this->model->where(['id'=> $id ,'wid'=>$wid])->delete();
        if($re === false) {
            return false;
        }
        //删除redis
        $redis = new EggsRedis();
        $redis->delete($id);
        return true;
    }


    #todo 手动终止活动
    /**
     * 手动终止活动
     * author: meijie
     * @param $id
     * @return bool
     */
    public function stopActivity($id,$wid)
    {
        $condition = [
            'id' =>$id ,
            'wid' => $wid
        ];
        $re = $this->model->where($condition)->update(['status'=>1]);
        if($re === false) {
            return false;
        }
        $data = $this->getInfoById($id);
        //更新redis
        $data['status'] = 1;
        $redis = new EggsRedis();
        $redis->updateRow($data);
        return true;
    }
    
    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @param int $pageSize
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        $where['wid'] = session('wid');  
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
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
        $redis = new EggsRedis();
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

 
}