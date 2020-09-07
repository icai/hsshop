<?php
namespace App\S\Staff;
use App\S\S;
use App\Lib\Redis\AfficheRedis;

class AfficheService extends S{

    protected $redis;

    public function __construct()
    {
        parent::__construct('Affiche');
        $this->redis = new AfficheRedis();
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     */
    public function getListById($idArr = [])
    {
        $redisData = $mysqlData = [];
        $redisId = [];

        $result = $this->redis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    //添加数据
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 处理编辑
     * @param  [int] $id   [主键id]
     * @param  [array] $data [要更新的数组数据]
     * @return [type]       [description]
     */
    public function update($id,$data)
    {
        $rs = $this->model->wheres(['id' => $id])->update($data);
        if($rs){
            $this->redis->updateHashRow($id,$data);
            return true;
        }

        return false;
    }

    /**
     * 删除数据
     * @param  [int] $id   [主键id]
     * @return [type]     [description]
     */
    public function del($id)
    {
        $rs = $this->model->wheres(['id' => $id])->delete();
        if($rs){
            $this->redis->del($id);
            return true;
        }

        return false;
    }

    /**
     * 获取单条数据
     * @param  [int] $id   [主键id]
     * @return [type]     [description]
     */
    public function getRowById($id)
    {
        if(empty($id)){
            error('数据异常');
        }
        $data = [];
        $data = $this->redis->getRow($id);
        if(empty($data)){
            $obj = $this->model->wheres(['id' => $id])->first();
            if ($obj) {
                $data = $obj->toArray();
                $this->redis->setRow($id,$data);
            }
        }
        return $data;

    }



}