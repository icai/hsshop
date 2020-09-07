<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/25
 * Time: 15:44
 */

namespace App\S\Store;
use App\S\S;

class MicroPageTypeRelationService extends S
{
    public function __construct()
    {
        parent::__construct('MicroPageTypeRelation');
    }
    /**
     * todo 统计使用微页面分类的微页面数
     * @param int $pageTypeID
     * @return int
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-16
     */
    public function statMicroPageNum($pageTypeID=0)
    {
        $microPageNum=0;
        if(!empty($pageTypeID)) {
            $microPageRelation =  $this->model->where(['micro_page_type_id' => $pageTypeID])->count();
            if (!empty($microPageRelation)) {
                $microPageNum =$microPageRelation;
            }
        }
        return $microPageNum;
    }

    /**
     * todo 查询微页面分类下的微页面
     * @param int $pageTypeID
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-16
     */
    public function selectMicroPage($pageTypeID=0)
    {
        $microPage=[];
        if(!empty($pageTypeID)) {
            //查询某个微页面分类下的微页面
            $typeRelationData =  $this->model->where(['micro_page_type_id' => $pageTypeID])->get()->load('belongsToMicroPage')->toArray();
            if (!empty($typeRelationData)) {
                foreach ($typeRelationData as $typeRelationItem) {
                    $pageData = [];
                    $pageData['id'] = $typeRelationItem['belongsToMicroPage']['id'];
                    $pageData['title'] = $typeRelationItem['belongsToMicroPage']['page_title'];
                    $pageData['sequence_number'] = $typeRelationItem['belongsToMicroPage']['sequence_number'];
                    $pageData['created_at'] = $typeRelationItem['belongsToMicroPage']['created_at'];
                    $pageData['pv_num']=0;
                    //查询出来的微页面数据存放到数组中
                    $microPage[] = $pageData;
                }
            }
        }
        return $microPage;
    }

    /**
     * todo 查询微页面与微页面分类关系数据[已使用]
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-13
     */
    public function getRelationData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='查询数据为空';
            return $returnData;
        }
        $result= $this->model->select(['micro_page_type_id','micro_page_id'])->where($data)->get()->toArray();
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * 查询微页面分类关系数据
     * @author 吴晓平 <2018年10月22日>
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function getRelationTypeData($data = [])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='查询数据为空';
            return $returnData;
        }
        $result= $this->model->select(['micro_page_type_id','micro_page_id'])->wheres($data)->with('belongsToMicroPageType')->get()->toArray();
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * todo 添加页面与页面分类关系数据
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-13
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $id= $this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 删除微页面分类与微页面关系数据
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-14
     */
    public function deleteData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result= $this->model->where($data)->delete();
        //如果没有要删除的数据 则返回0 删除失败返回false
        if($result===false)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='删除数据失败';
            return $returnData;
        }
        $returnData['data']=$result;
        return $returnData;
    }
}