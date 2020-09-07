<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/5/23
 * Time: 15:59
 */

namespace App\Services;


use App\Model\DistributeTemplate;

class DistributeTemplateService
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 添加分销模板
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        $id = DistributeTemplate::insertGetId($data);
        return $id;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 获取当前店铺的分销模板
     */
    public function getList()
    {
        $wid = session('wid');
        $templateData = DistributeTemplate::where('wid',$wid)->get()->toArray();
        return $templateData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 更新操作
     * @desc
     * @param $where
     * @param $data
     * @return mixed
     */
    public function up($where,$data)
    {
        $result = [
            'success'   => 0,
            'message'   => '',
        ];
        if (empty($where)){
            $result['message'] = '更新条件不能为空';
            return $result;
        }
        $result['success'] = DistributeTemplate::where($where)->update($data);
        return $result;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170524
     * @desc
     * @param $where
     * @return mixed
     */
    public function del($where)
    {
        $result = [
            'success'   => 0,
            'message'   => '',
        ];
        if (empty($where)){
            $result['message'] = '条件不能为空';
            return $result;
        }
        $result['success'] = DistributeTemplate::where($where)->delete();
        return $result;
    }


    public function count($where=[])
    {
        return DistributeTemplate::where($where)->count();
    }


    public function getRowById($id)
    {
        $result = DistributeTemplate::find($id);
        if ($result){
            return $result->toArray();
        }
        return [];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171121
     * @desc 获取店铺默认分销模板
     * @param $wid
     */
    public function getDefaultTemplate($wid)
    {
        $res = DistributeTemplate::where('wid',$wid)->orderBy('is_default','DESC')->first();
        if ($res){
            $res = $res->toArray();
        }
        return $res;

    }


    /**
     * 根据id获取分销模板
     * @param $ids
     * @return mixed
     * @author 张永辉 2018年10月8日
     */
    public function getListByIds($ids)
    {
        $ids = array_unique($ids);
        $data = DistributeTemplate::whereIn('id',$ids)->get()->load('gradeTemplate')->toArray();
        $result = [];
        foreach ($data as $val) {
            $temp[0] = [
                'id'         => $val['id'],
                'grade_id'      =>0,
                'wid'        => $val['wid'],
                'price'      => $val['price'],
                'cost'       => $val['cost'],
                'zero'       => $val['zero'],
                'one'        => $val['one'],
                'sec'        => $val['sec'],
                'three'      => $val['three'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at'],
            ];

            $result[$val['id']] = array_merge($temp,$val['gradeTemplate']);
            $result[$val['id']] = $this->handKey($result[$val['id']],'grade_id');
        }
        return $result;
    }

    /**
     * @param $data
     * @param string $key
     * @return array
     * @author 张永辉 2018年12月07日处理分销
     */
    public function handKey($data,$key='id')
    {
        $result = [];
        foreach ($data as $val){
            $result[$val[$key]] = $val;
        }
        return $result;
    }



}