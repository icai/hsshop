<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/26
 * Time: 15:24
 */

namespace App\S\WXXCX;
use App\S\S;

class WXXCXAccessPageService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXAccessPage');
    }

    /**
     * todo 添加小程序访问统计数据
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-10-26
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $data['create_time']=time();
        $data['update_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改小程序访问统计
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-10-26
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $data['update_time']=time();
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if($updateReturnValue===false)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 查询小程序访问统计数据
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-10-26
     */
    public function getListByCondition($data=[],$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value)
        {
            switch ( $key )
            {
                //店铺id
                case 'wid':
                    $where['wid'] = $value;
                    break;
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                // app_id
                case 'app_id':
                    $where['app_id'] =$value;
                    break;
            }
        }
        //查询数据
        $select= $this->model->where($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        if(!empty($pageSize))
        {
            //$select->paginate($pageSize)返回的是:LengthAwarePaginator
            //$select->paginate($pageSize)->toArray() 返回的是数组 "total" => 7, "per_page" => 20,"current_page" => 1
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
            $returnData['total']=$select['total'];
            $returnData['currentPage']=$select['current_page'];
            $returnData['pageSize']=$select['per_page'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        $returnData['data'] = $idAttr;
        return $returnData;
    }
}