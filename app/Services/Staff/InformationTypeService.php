<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  11:39
 * DESC
 */

namespace App\Services\Staff;


use App\Model\InformationType;
use App\Services\Service;

class InformationTypeService extends Service
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id','parent_id','type_path','name','status','sort','created_at'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new InformationType(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132026
     * @desc 获取分类
     * @param $parentId
     */
    public function  get($parentId=null)
    {
        if (is_null($parentId)){
            $where = [];
        }else{
            $where = [
                'parent_id'     => $parentId
            ];
        }
        list($data) = $this->init()->getList(false);
        $typeTmp = [];
        foreach ($data['data'] as $val)
        {
            $typeTmp[$val['id']] = $val['name'];
        }
        $infoTypeData = $this->init()->where($where)->getList();
        foreach ($infoTypeData[0]['data'] as &$val)
        {
            if ($val['type_path']){
                foreach (explode(',',$val['type_path']) as $v)
                {
                    $val['name_path'][] = $typeTmp[$v];
                }
                $val['name_path'] = implode('->',$val['name_path']);
            }else{
                $val['name_path']=$val['name'];
            }
        }

        return $infoTypeData;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703240909
     * @desc 删除分类
     * @param $id
     */
    public  function delInfoType($id)
    {
        $data = $this->init()->getInfo($id);
        //查看是几级类目
        if (count(explode(',',$data['type_path'])) == 3){
            //查看是否有资讯属于该分类
            $informationService = new InformationService();
            $res = $informationService->init()->model->where(['info_type'=>$id])->get()->toArray();
            if ($res){
                error('已有资讯属于该分类，请先删除资讯');
            }else{
                $this->init()->where(['id'=>$id])->delete($id);
            }
        }else{
            $res = $this->init()->model->where(['parent_id'=>$id])->get()->toArray();
            if ($res){
                error('该分类存在子分类，请先删除子分类');
            }else{
                $this->init()->where(['id'=>$id])->delete($id);
            }
        }
    }




}









