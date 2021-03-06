<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\S\Groups;
use App\Lib\Redis\GroupsRuleRedis;
use App\S\S;
use StaffOperLogService;
use DB;

class GroupsRuleService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('GroupsRule');
    }





    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new GroupsRuleRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new GroupsRuleRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new GroupsRuleRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new GroupsRuleRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where,$orderBy = '', $order = '')
    {
        $pageSize = app('request')->input('pageSize')??0;
        return $this->getListWithPage($where, $orderBy, $order,$pageSize);
    }

    /**
     * 获取所有参加拼团的商品
     * @author Herry
     * @return array 商品ID数组
     */
    public function getGroupingProductIDArr()
    {
        //未失效且未结束
        $where = [
            'end_time' => ['>', date('Y-m-d H:i:s')],
            'status' => 0
        ];

        $list = $this->getList($where);

        //取出所有商品ID
        return array_column($list, 'pid');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201701011
     * @desc 获取团购列表
     * @param $wid
     * @param int $status
     * @param string $orderBy
     * @param string $order
     * @return array
     * status=0 全部列表，1、未开始，2、正在进行中，3、已结束，4，未开始和正在进行中
     */
    public function getGroupRuleList($wid,$status=0,$orderBy='', $order='')
    {
        $time = date('Y-m-d H:i:s',time());
        $where = [
            'wid'   => $wid,
        ];
        // 许立 2018年08月15日 拼团活动标题搜索
        if (!empty(app('request')->input('title'))) {
            $where['title'] = ['like', '%' . app('request')->input('title') . '%'];
        }
        if ($status){
            switch ($status){
                case 1:
                    $where['start_time'] = ['>=',$time];
                    break;
                case 2:
                    $where['start_time'] = ['<=',$time];
                    $where['end_time'] = ['>=',$time];
                    $where['status'] = 0;
                    break;
                case 3:
                    $where['end_time'] = ['<=',$time];
                    break;
                case 4:
                    $where['end_time'] = ['>=',$time];
                    $where['status'] = 0;
                    break;
                default:
            }
        }

        $data = $this->getlistPage($where,$orderBy, $order);
        return $data;
    }

    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order); // TODO: Change the autogenerated stub
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171123
     * @desc 获取商品已售数量
     * @param $ruleId
     */
    public function getProductSoldNum($ruleId)
    {
        $sql = 'SELECT gr.id,p.sold_num FROM ds_groups_rule as gr LEFT JOIN ds_product as p ON gr.pid=p.id WHERE gr.id='.$ruleId;
        try{
            $res = DB::select($sql);
        }catch (\Exception $exception){
            \Log::info('sql执行报错:'.$sql);
            \Log::info('报错信息'.$exception->getMessage());
            return 0;
        }
        if ($res){
            $res = json_decode(json_encode($res),true);
            return $res[0]['sold_num']??0;
        }else{
            return 0;
        }
    }

    /**
     * todo 查询团购数据
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2018-04-09
     */
    public function getListByCondition($data=[],$status,$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        $time = date('Y-m-d H:i:s',time());
        /* 查询条件数组 */
        $where = [];
        if ($status){
            switch ($status){
                case 1:
                    $where['start_time'] = ['>=',$time];
                    break;
                case 2:
                    $where['start_time'] = ['<=',$time];
                    $where['end_time'] = ['>=',$time];
                    $where['status'] = 0;
                    break;
                case 3:
                    $where['end_time'] = ['<=',$time];
                    break;
                case 4:
                    $where['end_time'] = ['>=',$time];
                    $where['status'] = 0;
                    break;
            }
        }
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
                case 'title':
                    $where['title']=['like',  $value. '%'];
            }
        }

        //查询数据
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

    /**
     * 获取团规则下的实际支付金额、成团订单数、拼团成功的客户数
     * @param $ruleId
     * @return array
     * @author 何书哲 2019年01月10日
     */
    public function getTotalGroup($ruleId) {
        $return = [0, 0, 0];

        $sql = 'SELECT SUM(pay_price) AS total_pay_price FROM ds_order WHERE id IN (SELECT oid FROM ds_groups_detail WHERE groups_id IN (SELECT id FROM ds_groups WHERE rule_id=?))';
        try{
            $res = DB::select($sql, [$ruleId]);
        }catch (\Exception $exception){
            \Log::info('sql执行报错:'.$sql);
            \Log::info('报错信息'.$exception->getMessage());
            return $return;
        }
        if ($res){
            $res = json_decode(json_encode($res),true);
            $return[0] = $res[0]['total_pay_price']??0;
        }else{
            return $return;
        }

        $sql = 'SELECT COUNT(*) AS group_order_num,COUNT(mid) AS group_member_num FROM ds_order WHERE groups_id IN (SELECT id FROM ds_groups WHERE rule_id=? and `status`=2)';
        try{
            $res = DB::select($sql, [$ruleId]);
        }catch (\Exception $exception){
            \Log::info('sql执行报错:'.$sql);
            \Log::info('报错信息'.$exception->getMessage());
            return $return;
        }
        if ($res){
            $res = json_decode(json_encode($res),true);
            $return[1] = $res[0]['group_order_num']??0;
            $return[2] = $res[0]['group_member_num']??0;
        }else{
            return $return;
        }

        return $return;
    }


}























