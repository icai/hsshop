<?php
namespace App\S\Foundation;

use App\Lib\Redis\Region as RegionRedis;
use App\S\S;

/**
 * 省市区
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年1月14日 10:32:35
 */
class RegionService  extends S
{
    public function __construct()
    {
        parent::__construct('Region');
    }

    /**
     * 设置所有字段名称
     *
     * @var array
     * @update : 吴晓平 增加判断id是否存在于列表数组中 2019年09月18日 09:51:26
     */
    public $field = ['id', 'title', 'pid', 'level', 'created_at', 'updated_at', 'deleted_at'];

    public function getListById(array $idArr)
    {
        $return = [];
        $allList = $this->getAll();
        $allList = array_column($allList,null,'id');
        foreach ($idArr as $id) {
            // update by 吴晓平 增加判断id是否存在于列表数组中 2019年09月18日 09:51:26
            if (isset($allList[$id])) {
                $return[$id] = $allList[$id];
            }
        }
        return $return;
    }

    public function getRowById($id)
    {
        $allList = $this->getAll();
        $allList = array_column($allList,null,'id');
        return $allList[$id];
    }

    /**
     * 获取地址库所有地址
     * @author cwh
     * @desc   不包含已删除的
     * @return [type]         [description]
     */
    public function getAllWithoutDel()
    {
        $redis = new RegionRedis('all_no_del');
        $allList = $redis->get();
        if (empty($allList)) {
            $allList = $this->model->wheres(['status' => 0])->select('id','title','pid','level','status')->get()->toArray();
            $redis->set($allList);
        }
        return $allList;
    }

    public function getListByIdWithoutDel(array $idArr)
    {
        $return = [];
        $allList = $this->getAllWithoutDel();
        $allList = array_column($allList,null,'id');
        foreach ($idArr as $id) {
            $return[$id] = $allList[$id];
        }
        return $return;
    }

    /**
     * 获取地址库所有地址
     * @author cwh
     * @desc   包括已删除的（当初删除为了修复有重复的问题，兼容用这个额方法）
     * @return [type]         [description]
     * @update 何书哲 2019年09月30日 获取全部的地址库不使用redis，避免redis造成的数据问题
     */
    public function getAll()
    {
        $allList = $this->model->select('id','title','pid','level','status')->get()->toArray();
        return $allList;
    }

    public function getProvinceList()
    {
        $redis = new RegionRedis('province');
        $provinceList = $redis->get();
        if (empty($provinceList)) {
            $allList = $this->getAll();

            foreach ($allList as $value) {
                $regionList[$value['pid']][] = $value;
            }
            $provinceList = $regionList[-1];
            $redis->set($provinceList);
        }
        return $provinceList;
    }

    /**
     * 根据地区名称获取数据
     * @author wuxiaoping 2018.1.04 主要用于同步微信地址
     * @param  [string]  $title [省，市，区名称]
     * @param  integer $level [description]
     * @return [type]         [description]
     */
    public function getRowByTitle($title,$level=0)
    {
        $obj = $this->model->wheres(['title' => $title,'level' => $level])->first();
        $data = [];
        if ($obj) {
            $data = $obj->toArray();
        }
        return $data;
    }

    public function getListWithPage($where = [], $orderBy = '', $order = '', $pageSize = 0)
    {
        return parent::getListWithPage($where, $orderBy, $order, $pageSize); // TODO: Change the autogenerated stub
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }


    public function delRedisAll()
    {
        $redis = new RegionRedis('all');
        $redis->del('all');
        $redis->del('all_no_del');

    }

    /**
     * 更新地址
     * @param $id
     * @param $data
     * @author 张永辉 20180823
      */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        return $res;
    }




}
