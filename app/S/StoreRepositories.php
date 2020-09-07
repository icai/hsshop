<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/27
 * Time: 11:22
 */

namespace App\S;



use App\Model\FileInfo;
use App\Model\Region;
use App\S\Foundation\RegionService;
use App\S\Store\StoreService;
use App\S\Lift\ReceptionService;

class StoreRepositories
{

    public $request;
    public function __construct()
    {
        $this->request = app('request');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170628
     * @desc 按门店名称，所在 省 市 区 搜索
     * @param string $word
     * @update 陈文豪 2018年7月23日 修改搜索条件
     */
    public function search($word='',$type=0)
    {
        $wid = session('wid');
        if ($type == 1) {
            $query = (new ReceptionService())->model->where('wid',$wid);
        }else {
            $query = (new StoreService())->model->where('wid',$wid);
        }
        if ($word){
            //$query = $query->where('title','like','%'.$word.'%');
            $region = Region::where('title','like','%'.$word.'%')->get(['id','title'])->toArray();
            $ids = [];
            foreach ($region as $val){
                $ids[] = $val['id'];
            }
            if ($ids){
                //$query = $query->orWhereIn('province_id',$ids)->orWhereIn('city_id',$ids)->orWhereIn('area_id',$ids);
                $query->where(
                   function($q1) use($ids,$word){
                        $q1->orWhereIn('province_id',$ids)
                        ->orWhereIn('city_id',$ids)
                        ->orWhereIn('area_id',$ids)
                        ->orWhere('title','like','%'.$word.'%');
                    }
                );
            } else {
               $query = $query->where('title','like','%'.$word.'%');
           }
        }
        $page = $this->request->input('page')??1;
        $pagesize = config('database.perPage');
        $tag = $this->request->input('tag')??1;
        if ($tag == 2) {
            # code...
            $pagesize = 1000;
        }
        $offset = ($page-1)*$pagesize;
        $count = $query->count();
        $pageNum = ceil($count/$pagesize);
        $storeData = $query->skip($offset)->take($pagesize)->orderBy('id','desc')->get()->toArray();
        $pageInfo = [
            'pageNow' => $page,
            'pageSize'=> $pagesize,
            'count'    => $count,
            'pageNum'  => $pageNum,
        ];
        $data['pageInfo'] = $pageInfo;
        $data['data'] = $storeData;
        $this->dealStore($data['data']);
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170628
     * @desc 处理店铺数据
     * @param $data
     * @update 陈文豪 2018年7月18号 修复电话号码
     */
    public function dealStore(&$data)
    {
        $regionService = new RegionService();
        $regions = $regionService->getAll();
        foreach($regions as $value){
            $regionList[$value['id']] = $value;
        }
        //获取资源信息
        $ids = [];
        $fileData = [];
        foreach ($data as $val){
            if (!isset($val['imgs']) && empty($val['imgs'])) {
                $val['imgs'] = $val['images'];
            }
            $ids = array_merge($ids,explode(',',$val['imgs']));
        }
        if ($ids){
            $res = FileInfo::whereIn('id',array_unique($ids))->get(['id','path','s_path','m_path','l_path'])->toArray();
            foreach ($res as $val){
                $fileData[$val['id']] = $val;
            }
        }

        foreach ($data as &$val){
            $val['province'] = $regionList[$val['province_id']]['title'];
            $val['city'] = $regionList[$val['city_id']]['title'];
            $val['area'] = $regionList[$val['area_id']]['title'];
            if (!isset($val['imgs']) && empty($val['imgs'])) {
                $val['imgs'] = $val['images'];
            }
            if ($val['imgs']){
                foreach (explode(',',$val['imgs']) as $v){
                    $val['file'][] = $fileData[$v]??[];
                }
            }
            //修复电话号码
            if(!empty($val['phone']) && substr($val['phone'],0,1) == '-') {
                $val['phone'] =  substr($val['phone'],1);   
            }
        }
    }

}