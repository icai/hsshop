<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\S\Wheel;

use App\Lib\Redis\ActivityWheelLogRedis;
use App\Lib\Redis\ActivityWheelRedis;
use App\Model\ActivityAwardAddress;
use App\Module\ExportModule;
use App\S\Market\ActivityAwardAddressService;
use App\S\Member\MemberService;
use App\S\S;
use App\Services\Shop\MemberAddressService;

class ActivityWheelLogService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        $this->request = app('request');
        parent::__construct('ActivityWheelLog');
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
        $redis = new ActivityWheelLogRedis();
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
        $redis = new ActivityWheelLogRedis();
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
            $storeRedis = new ActivityWheelLogRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new ActivityWheelLogRedis();
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
        return $this->getListWithPage($where, $orderBy, $order);
    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 批量更新
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redis = new ActivityWheelLogRedis();
            $redisUpData = [];
            foreach ($ids as $val){
                $redisUpData[] = array_merge($data,['id'=>$val]);
            }
            return $redis->updateArr($redisUpData);
        }else{
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170801
     * @desc 根绝wheelid 获取记录
     * @param $id
     * @update 梅杰 2018年7月27日 增加地址返回信息
     * @update 梅杰 2018年7月30日 地址返回信息数据格式
     * @update 许立 2018年08月17日 赠品且确认过收货信息才在列表显示收货地址
     * @update 许立 2018年08月30日 导出增加收货信息字段
     */
    public function getByWheelId($id)
    {
        $where = ['wheel_id'=>$id];

        if ($this->request->input('status')){
            if ($this->request->input('status') == 1){
                $where['is_win'] = 0;
            }else{
                $where['is_win'] = 1;
            }
        }
        /************fuguowei 根据昵称搜索记录 20180122*********************/
        if($this->request->input('name'))
        {
            $inputName = $this->request->input('name');
            $name['nickname'] = trim($inputName);
            $member = (new MemberService())->getListByConditionPage($name);
            $memberId = [];
            if($member)
            {
                foreach($member as $k=>$v)
                {
                    $memberId[] =$v['id'];
                }
            }
            $where['mid'] = ['in',$memberId];
        }
        /*********************end**************************************/

        $is_export = $this->request->input('is_export');
        if (!empty($is_export)) {
            //导出
            $logData = [
                [
                    'data' => $this->getList($where)
                ]
            ];
        } else {
            $logData = $this->getListWithPage($where);
        }
        $mids = [];
        foreach ($logData[0]['data'] as $val){
            $mids[] = $val['mid'];
        }
        $res= (new MemberService())->getListById($mids);
        $memberData = [];
        foreach ($res as $val){
            $memberData[$val['id']]['nickname'] = filterEmoji($val['nickname']);
            $memberData[$val['id']]['headimgurl'] = $val['headimgurl'];
            $memberData[$val['id']]['id'] = $val['id'];
        }
        foreach ($logData[0]['data'] as &$value){
            $value['address'] = [];
            $value['address_export'] = '未确认收货信息';
            if ($value['prize_type'] == 3) {
                // 是否有确认过地址
                $row = (new ActivityAwardAddressService())->model
                    ->where('activity_id', $value['wheel_id'])
                    ->where('mid', $value['mid'])
                    ->where('type', ActivityAwardAddress::ACTIVITY_TYPE_WHEEL)
                    ->get()
                    ->toArray();
                !empty($row[0]['is_confirm']) && $value['address'] = (new MemberAddressService())->getAddressById($row[0]['address_id']);
                if ($value['address']) {
                    $value['address_export'] = $value['address']['title'] . ', ' . $value['address']['phone'] . ', ' . $value['address']['detail'];
                }
            }
            if (empty($is_export)) {
                $value['member'] = $memberData[$value['mid']];
            } else {
                $value['nickname'] = $memberData[$value['mid']]['nickname'];
                $value['prize_name'] = $value['is_win'] ? $value['prize'] : '未中奖';
            }
        }

        if (empty($is_export)) {
            return $logData;
        } else {
            $data['data'] = $logData[0]['data'];
            $data['title'] = [
                'nickname'       => '粉丝',
                'created_at'     => '参与时间',
                'prize_name'     => '奖品',
                'reduce_integra' => '消耗积分',
                'send_integra'   => '获得积分',
                'address_export' => '收货信息'
            ];
            $width_array = [30,20,50,20,20,100];
            (new ExportModule())->derive($data, '大转盘参与记录', 'xlsx', $width_array);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170802
     * @desc获取统计数据
     * @param array $where
     * @return mixed
     */
    public function count($where = [])
    {
        return parent::count($where); // TODO: Change the autogenerated stub
    }


}























