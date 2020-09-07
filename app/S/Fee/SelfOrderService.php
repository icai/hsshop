<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/3
 * Time: 17:11
 */

namespace App\S\Fee;
use App\S\S;

class SelfOrderService extends S
{
    public function __construct()
    {
        parent::__construct('SelfOrder');
    }

    /**
     * todo 订购自营服务
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
        if(empty($data['order_no']))
        {
            $errMsg.='订单号为空';
        }
        if(empty($data['wid']))
        {
            $errMsg.='店铺id为空';
        }
        if(empty($data['pay_amount']))
        {
            $errMsg.='支付金额为空';
        }
        if(empty($data['products_amount']))
        {
            $errMsg.='商品金额为空';
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

    /*** todo 修改订购自营服务
     * @param int $id 更改数据id
     * @param array $data 要更改的数据
     * @return array
     * @author 张国军 2018-07-03
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
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 删除服务订购
     * @param int $id
     * @return array
     * @author 张国军 2018年07月05日
     */
    public function delete($id=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $deleteReturnValue=$this->model->where(['id'=>$id])->update(['current_status'=>-1,'update_time'=>time()]);
        if(!$deleteReturnValue)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='删除数据失败';
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 查询数据
     * @param array $data 查询条件
     * @param string $orderBy 要排序的字段
     * @param string $order 顺序/倒序
     * @param int $pageSize 页面数
     * @return array
     * @author 张国军 2018年07月03日
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
                //id
                case 'id':
                    $where['id'] =$value;
                    break;
                //订单号
                case 'order_no':
                    $where['order_no'] =$value;
                    break;
                //状态 0表示正常 -1表示删除
                case 'current_status':
                    $where['current_status'] =$value;
                    break;
                 //wids
                case 'wids':
                    $where['wid'] =['in',$value];
                    break;
                //状态
                case 'status':
                    $where['status']=$value;
                    break;
                    //wid
                case 'wid':
                    $where['wid']=$value;
                    break;
                case 'is_invoice':
                    $where['is_invoice']=$value;
                    break;
            }
        }
        //查询数据 使用in 需要把where 改成 wheres
        $select= $this->model->wheres($where);
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