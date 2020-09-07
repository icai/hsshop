<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/4
 * Time: 10:18
 */

namespace App\S\Fee;
use App\S\S;

class SelfInvoiceService extends S
{
    public function __construct()
    {
        parent::__construct('SelfInvoice');
    }

    /**
     * todo 开具发票
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
        //发票抬头类型
        if(empty($data['title_type']))
        {
            $returnData['errCode']=-8;
            $returnData['errMsg']='发票抬头类型为空';
            return $returnData;
        }
        else
        {
            if(!in_array($data['title_type'],[1,2]))
            {
                $returnData['errCode']=-7;
                $returnData['errMsg']='发票抬头类型不对';
                return $returnData;
            }
        }
        //发票类型
        if(empty($data['type']))
        {
            $returnData['errCode']=-9;
            $returnData['errMsg']='发票类型为空';
            return $returnData;
        }
        else
        {
            if(!in_array($data['type'],[1,2]))
            {
                $returnData['errCode']=-4;
                $returnData['errMsg']='发票类型不对';
                return $returnData;
            }
            //增值税专业发票
            if($data['type']==2)
            {
                if(empty($data['tax_number']))
                {
                    $errMsg.='纳税人识别码不能为空';
                }
                if(empty($data['deposit_bank_address']))
                {
                    $errMsg.='开户行地址不能为空';
                }
                if(empty($data['deposit_bank_account']))
                {
                    $errMsg.='开户行账户不能为空';
                }
                if(empty($data['company_address']))
                {
                    $errMsg.='公司地址不能为空';
                }
                if(empty($data['company_telephone']))
                {
                    $errMsg.='公司联系方式不能为空';
                }
                if($data['title_type']==2)
                {
                    $errMsg.='增值税专业发票抬头类型不能为个人';
                }
                if($data['style']==2)
                {
                    $errMsg.='增值税专业发票不能为电子发票';
                }
            }//普通发票
            else if($data['type']==1)
            {
                //抬头类型为企业
                if($data['title_type']==1)
                {
                    if(empty($data['tax_number']))
                    {
                        $errMsg.='纳税人识别号不能够为空';
                    }
                }
            }

        }

        //发票性质 1纸质发票 2电子发票
        if(empty($data['style']))
        {
            $returnData['errCode']=-5;
            $returnData['errMsg']='发票性质不能够为空';
            return $returnData;
        }
        else
        {
            if(!in_array($data['style'],[1,2]))
            {
                $returnData['errCode']=-6;
                $returnData['errMsg']='发票性质不对';
                return $returnData;
            }
            //纸质发票
            if($data['style']==1)
            {
                //收件人
                if(empty($data['receiver']))
                {
                    $errMsg.='收件人不能够为空';
                }
                //联系电话
                if(empty($data['telephone']))
                {
                    $errMsg.='联系电话不能够为空';
                }
                //收件地址
                if(empty($data['province_id']))
                {
                    $errMsg.='省不能为空';
                }
                if(empty($data['city_id']))
                {
                    $errMsg.='市不能为空';
                }
                if(empty($data['area_id']))
                {
                    $errMsg.='区不能为空';
                }
                //详细地址
                if(empty($data['detail_address']))
                {
                    $errMsg.='详细地址不能为空';
                }
            }
        }

        if(empty($data['request_no']))
        {
            $errMsg.='申请编号不能够为空';
        }

        if(empty($data['amount']))
        {
            $errMsg.='发票金额不能够为空';
        }

        if(empty($data['title']))
        {
            $errMsg.='抬头不能为空';
        }

        if(empty($data['wid']))
        {
            $errMsg.='店铺id不能为空';
        }

        if(empty($data['order_id']))
        {
            $errMsg.='订单编号不能为空';
        }
        else
        {
            //检查订单编号是否符合要求
            $orderId=json_decode($data['order_id'],true);
            if(empty($orderId))
            {
                $returnData['errCode']=-4;
                $returnData['errMsg']="订单编号不符合要求";
                return $returnData;
            }
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

    /*** todo 修改发票一些信息
     * @param int $id 更改数据id
     * @param array $data 要更改的数据
     * @return array
     * @author 张国军 2018-07-04
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
        $errMsg='';
        if(empty($data['remark']))
        {
            $errMsg.='备注为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']=$errMsg;
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
                //wids
                case 'wids':
                    $where['wid'] =['in',$value];
                    break;
                //wid
                case 'wid':
                    $where['wid'] =$value;
                    break;
                //订单号
                case 'order_no':
                    $where['order_no'] =$value;
                    break;
                //数据是否有效 0有效 -1无效
                case 'current_status':
                    $where['current_status'] =$value;
                    break;
                case 'id':
                    $where['id']=$value;
                    break;
                case 'request_no':
                    $where['request_no']=$value;
                    break;
                case 'status':
                    $where['status']=$value;
                    break;
            }
        }
        //查询数据 有in 需要把where 改成 wheres
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