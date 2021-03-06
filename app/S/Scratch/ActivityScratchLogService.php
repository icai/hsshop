<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/5/15
 * Time: 14:56
 */

namespace App\S\Scratch;

use App\Model\ActivityAwardAddress;
use App\Lib\Redis\ActivityScratchLogRedis;
use App\Lib\Redis\ActivityScratchRedis;
use App\S\Member\MemberService;
use App\S\S;
use App\Module\ExportModule;
use Illuminate\Support\Facades\DB;
use App\S\Market\ActivityAwardAddressService;
use App\Services\Shop\MemberAddressService;

class ActivityScratchLogService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        $this->request = app('request');
        parent::__construct('ActivityScratchLog');
    }

    /**
     * @author hsz
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new ActivityScratchLogRedis();
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
        $redis = new ActivityScratchLogRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null, 'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }

    /**
     * @author hsz
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        $res = $this->model->where('id', $id)->update($data);
        if ($res) {
            $storeRedis = new ActivityScratchLogRedis();
            return $storeRedis->update($id, $data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id', $id)->delete();
        if ($res) {
            $storeRedis = new ActivityScratchLogRedis();
            return $storeRedis->del($id);
        } else {
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where, $orderBy = '', $order = '')
    {
        return $this->getListWithPage($where, $orderBy, $order);
    }

    /**
     * 批量更新
     * @author 何书哲 2017年07月20日
     * @update 何书哲 2019年07月17日 修改redis批量更新
     */
    public function batchUpdate($ids, $data)
    {
        $res = $this->model->whereIn('id', $ids)->update($data);
        if ($res) {
            $redis = new ActivityScratchLogRedis();
            // update 何书哲 2019年07月17日 修改redis批量更新
            $redisUpData = [];
            foreach ($ids as $val) {
                $redisUpData[] = array_merge($data, ['id' => $val]);
            }
            return $redis->updateArr($redisUpData);
        } else {
            return false;
        }
    }

    /**
     * @author hsz
     * @desc获取统计数据
     * @param array $where
     * @return mixed
     */
    public function count($where = [])
    {
        return parent::count($where); // TODO: Change the autogenerated stub
    }

    /**
     * @author hsz
     * @desc 根据scratchid 获取记录
     * @param $id
     */
    public function getByScratchId($id)
    {
        $where = ['scratch_id' => $id];

        if ($this->request->input('status')) {
            if ($this->request->input('status') == 1) {
                $where['is_win'] = 0;
            } else {
                $where['is_win'] = 1;
            }
        }
        /************ 根据昵称搜索记录 20180122*********************/
        if ($this->request->input('name')) {
            $inputName = $this->request->input('name');
            $name['nickname'] = trim($inputName);
            $member = (new MemberService())->getListByConditionPage($name);
            $memberId = [];
            if ($member) {
                foreach ($member as $k => $v) {
                    $memberId[] = $v['id'];
                }
            }
            $where['mid'] = ['in', $memberId];
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
        foreach ($logData[0]['data'] as $val) {
            $mids[] = $val['mid'];
        }
        $res = (new MemberService())->getListById($mids);
        $memberData = [];
        foreach ($res as $val) {
            $memberData[$val['id']]['nickname'] = filterEmoji($val['nickname']);
            $memberData[$val['id']]['headimgurl'] = $val['headimgurl'];
            $memberData[$val['id']]['id'] = $val['id'];
        }
        foreach ($logData[0]['data'] as &$value) {
            $value['address'] = $address = [];
            if ($value['prize_type'] == 3) {
                // 是否有确认过地址
                $row = (new ActivityAwardAddressService())->model
                    ->where('activity_id', $value['scratch_id'])
                    ->where('mid', $value['mid'])
                    ->where('type', ActivityAwardAddress::ACTIVITY_TYPE_SCRATCH)
                    ->get()
                    ->toArray();
                if (!empty($row[0]['is_confirm'])) {
                    $address = (new MemberAddressService())->getAddressById($row[0]['address_id']);
                }
            }
            if (empty($is_export)) {
                $value['member'] = $memberData[$value['mid']];
                if (isset($address) && $address) {
                    $value['address'] = $address;
                }
            } else {
                $value['nickname'] = $memberData[$value['mid']]['nickname'];
                $value['prize_name'] = $value['is_win'] ? $value['prize'] : '未中奖';
                $value['address'] = isset($address) && $address ? $address['detail'] : '';
            }
        }

        if (empty($is_export)) {
            return $logData;
        } else {
            $data['data'] = $logData[0]['data'];
            $data['title'] = [
                'nickname' => '粉丝',
                'created_at' => '参与时间',
                'prize_name' => '奖品',
                'reduce_integra' => '消耗积分',
                'send_integra' => '获得积分',
                'address' => '收货地址'
            ];
            $width_array = [30, 20, 50, 20, 20, 50];
            (new ExportModule())->derive($data, '刮刮卡参与记录', 'xlsx', $width_array);
        }
    }

    /**
     * @author hsz
     * @desc 获取抽奖信息
     * @param $scratchId
     * @param $wid
     * @return array
     */
    public function getPrizeInfo($scratchId, $wid)
    {
        $return = [];
        //获取参与人数
        $sql = 'SELECT COUNT(DISTINCT mid) AS participate_user_num FROM ds_activity_scratch_log WHERE scratch_id=? AND wid=?';
        $res = DB::select($sql, [$scratchId, $wid]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            $return['participate_user_num'] = $res['participate_user_num'];
        } else {
            $return['participate_user_num'] = 0;
        }
        //获取参与次数
        $sql = 'SELECT COUNT(*) AS participate_total_num FROM ds_activity_scratch_log WHERE scratch_id=? AND wid=?';
        $res = DB::select($sql, [$scratchId, $wid]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            $return['participate_total_num'] = $res['participate_total_num'];
        } else {
            $return['participate_total_num'] = 0;
        }
        //获取领到人数
        $sql = 'SELECT COUNT(DISTINCT mid) AS receive_user_num FROM ds_activity_scratch_log WHERE scratch_id=? AND wid=? AND is_win=1';
        $res = DB::select($sql, [$scratchId, $wid]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            $return['receive_user_num'] = $res['receive_user_num'];
        } else {
            $return['receive_user_num'] = 0;
        }
        //获取未领到人数
        $sql = 'SELECT COUNT(DISTINCT mid) AS unreceive_user_num FROM ds_activity_scratch_log WHERE scratch_id=? AND wid=? AND is_win=0';
        $res = DB::select($sql, [$scratchId, $wid]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            $return['unreceive_user_num'] = $res['unreceive_user_num'];
        } else {
            $return['unreceive_user_num'] = 0;
        }
        return $return;
    }


}