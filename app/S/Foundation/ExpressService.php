<?php
namespace App\S\Foundation;

use App\Lib\Redis\Express as ExpressRedis;
use App\S\S;

/**
 * 快递
 */
class ExpressService extends S
{
    public function __construct()
    {
        parent::__construct('Express');
    }

    //数据库所有字段
    public $field = ['id', 'title', 'word', 'sort', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * 获取非分页列表
     * @return array
     */
    public function getListWithoutPage()
    {
        return $this->getList([], '', '', 'id', 'asc');
    }

    /**
     * 根据id获取详情
     * @param $id int 主键
     * @return array
     */
    public function getRowById($id)
    {
        $allList = $this->getAll();
        $allList = array_column($allList,null,'id');
        return $allList[$id];
    }

    /**
     * 根据id数组获取列表
     * @return array
     */
    public function getListById(array $idArr)
    {
        $return = [];
        $allList = $this->getAll();
        $allList = array_column($allList,null,'id');
        foreach ($idArr as $id) {
            $return[$id] = $allList[$id];
        }
        return $return;
    }

    /**
     * 获取所有列表
     * @return array
     */
    public function getAll()
    {
        $redis = new ExpressRedis('all');
        $allList = $redis->get();
        if (empty($allList)) {
            $allList = $this->model->select('id','title','word', 'sort', 'status')->get()->toArray();
            $redis->set($allList);
        }
        return $allList;
    }
}