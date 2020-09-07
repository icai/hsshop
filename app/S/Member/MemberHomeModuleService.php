<?php
/**
 * @author 吴晓平 <2018年08月28日>
 * @desc 主要功能是一次批量插入数据，查询所有的列表数据
 */
namespace App\S\Member;
use App\S\S;
use DB;

class MemberHomeModuleService extends S {
    
    
    /**
     * 构造方法
     * @return void
     */
    public function __construct()
    {
        $this->request = app('request');
        parent::__construct('MemberHomeModule');
    }

    /**
     * 批量插入数据
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function batchInsert($data)
    {
       $result = DB::table($this->model->getTable())->insert($data);
       return $result;
    }

    /**
     * 根据id获取模块列表
     * @param  [array] $ids [id数组]
     * @param  [int] $wid [店铺id]
     * @return [type]      [description]
     */
    public function getListByIds($ids,$wid)
    {
        $result = [];
        $where['id'] = ['in',$ids];
        $obj = $this->model->wheres($where)->get();
        if ($obj) {
            $result = $obj->toArray();
            foreach ($result as $key => &$value) {
                if ($value['urls']) {
                    $urls = json_decode($value['urls'],true);
                    if (strpos($urls['shop'], 'url')) {
                        $value['shop'] = sprintf($urls['shop'],$wid,$wid);
                    }else {
                        $value['shop'] = sprintf($urls['shop'],$wid);
                    }
                    $value['xcx'] = $urls['xcx'];
                }
            }
        }
        return $result;
    }
    
    /**
     * 查询数据库中的所有数据列表
     * @return [type] [description]
     */
    public function getAllList($wid)
    {
        $result = [];
        $obj = $this->model->get();
        if ($obj) {
            $result = $obj->toArray();
            foreach ($result as $key => $value) {
                if ($value['urls']) {
                    $urls = json_decode($value['urls'],true);
                    if (strpos($urls['shop'], 'url')) {
                        $value['shop'] = sprintf($urls['shop'],$wid,$wid);
                    }else {
                        $value['shop'] = sprintf($urls['shop'],$wid);
                    }
                    $value['xcx'] = $urls['xcx'];
                }
            }
        }
        return $result;
    }
    
}