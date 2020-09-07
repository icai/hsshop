<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/4
 * Time: 10:11
 */

namespace App\S\Fee;
use App\S\S;

class SelfOrderDetailService extends S
{
    public function __construct()
    {
        parent::__construct('SelfOrderDetail');
    }

    /**
     * todo 添加订购自营服务明细
     * @param array $data 要添加的数据
     * @return array
     * @author 张国军 2018年07月04日
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
        if(empty($data['self_order_id']))
        {
            $errMsg.='订单编号为空';
        }
        if(empty($data['product_id']))
        {
            $errMsg.='商品id为空';
        }
        if(empty($data['product_name']))
        {
            $errMsg.='商品名称为空';
        }
        if(empty($data['product_version_no']))
        {
            $errMsg.='版本号为空';
        }
        if(empty($data['product_price']))
        {
            $errMsg.='金额为空';
        }
        if(empty($data['num']))
        {
            $errMsg.='年限为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $data['update_time']=time();
        $id=$this->model->insertGetId($data);
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
     * todo 查询数据
     * @param array $data 查询条件
     * @param string $orderBy 要排序的字段
     * @param string $order 顺序/倒序
     * @param int $pageSize 页面数
     * @return array
     * @author 张国军 2018年07月04日
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
            switch ($key)
            {
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                //订单号
                case 'self_order_id':
                    $where['self_order_id'] =$value;
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