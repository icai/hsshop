<?php 
namespace App\S;
use App\S\S;
use OrderCommon;

/**
 * 客户/会员
 */
class BalanceLogService extends S 
{
    public function __construct()
    {
        parent::__construct('BalanceLog');
    }

    //查看用户余额记录
    public function getWidLog($wid)
    {
        $where['wid']    = $wid;
        $where['status'] = 1;
        return $this->getListWithPage($where, 'created_at', 'DESC');
    }

    //type 0 全部  1充值成功
    public function getUserLog($wid, $mid, $type)
    {
        $where['wid'] = $wid;
        $where['mid'] = $mid;
        if ($type != 0 ) {
            $where['type'] = $type;
        }
        
        $where['status'] = 1;
        return $this->getListWithPage($where, 'created_at', 'DESC');
    }

    public function getAllRecharge($wid, $type = 1)
    {
        $where['wid']    = $wid;
        $where['type']   = $type;
        $where['status'] = 1;
        return $this->model->wheres($where)->sum('money');
    }

    public function getDistinct($wid)
    {
        $where['wid']    = $wid;
        $where['type']   = 1;
        $where['status'] = 1;
        return $this->model->wheres($where)->distinct('mid')->count('mid');
    }

    //pay_way 4 系统   5 退款
    public function insertLog($wid, $mid, $money, $pay_way = 1, $type = 1, $status = 0 , $msg = '',$xcxid=0)
    {
        $data['wid']   = $wid;
        $data['mid']   = $mid;
        $data['type']  = $type;
        $data['money'] = $money * 100;
        $data['status'] = $status;
        $data['pay_way'] = $pay_way;
        $data['trade_id'] = OrderCommon::createOrderNumber();
        $data['created_at'] = time();
        $data['pay_desc'] = $msg;
        return $this->model->insertGetId($data);
    }

    //todo redis
    public function getListById(array $idArr)
    {
        $data = $this->model->whereIn('id',$idArr)->get()->toArray();
        $data = array_column($data, null,'id');
        return sortArr($idArr, $data );
    }

    public function getRowById($id)
    {   
        $data = [];
        $obj = $this->model->wheres(['id' => $id])->first();
        if($obj){
            $data = $obj->toArray();
        }

        return $data;
    }

    public function updateDataStatusOk($id,$data)
    {
        return $this->model->where(['id'=>$id, 'status' => 0])->update($data);
    }


    /**
     * 更新数据
     * @param $id
     * @param $data
     * @return mixed
     * @author 张永辉 2018年7月12日
     */
    public function updateDataByid($id,$data)
    {
        return $this->model->where(['id'=>$id])->update($data);
    }


    /**
     * 批量更新
     * @param $data
     * @return bool
     * @author 张永辉 2018年9月18日
     */
    public function batchUpdate($data)
    {
        $ids = array_column($data,'id');
        $res = $this->model->whereIn('id',$ids)->update($data);
        return $res;
    }


}
