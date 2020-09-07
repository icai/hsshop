<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/3/8
 * Time: 13:44
 */

namespace App\S\WXXCX;
use App\S\S;

class WXXCXConfigRecordService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXConfigRecord');
    }
    /**
     * todo 添加微信小程序备注记录
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2018-03-08
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
        $errMsg='';
        if(empty($data['wid']))
        {
            $errMsg.='店铺id为空';
        }
        if(empty($data['operate_id']))
        {
            $errMsg.='operate_id为空';
        }
        if(empty($data['operator']))
        {
            $errMsg.='operator为空';
        }
        if(empty($data['content']))
        {
            $errMsg.='content为空';
        }
        if(empty($data['app_id']))
        {
            $errMsg.='app_id为空';
        }
        if(empty($data['app_name']))
        {
            $errMsg.='app_name为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 查询微信小程序备注
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2018-03-08
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
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                // app_id
                case 'app_id':
                    $where['app_id'] =$value;
                    break;
                case 'wid':
                    $where['wid'] =$value;
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