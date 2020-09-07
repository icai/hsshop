<?php

namespace App\Services;

use App\Model\MemberCardRecord;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use Illuminate\Support\Facades\DB;
use MemberCardService as MCardService;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use UploadedFile;
use Validator;

class MemberCardRecordService extends Service
{
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
//        $this->field = ['id', 'wid', 'mid', 'member_title', 'card_id', 'balance', 'status',
//            'in_card_at', 'out_card_at', 'created_at'];
        /* 设置闭包标识 */
        //$this->closure('capital');
    }

    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new MemberCardRecord(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 订单构建筛选、搜索查询条件数组
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 11:39:43
     *
     * @param  array $input [需要搜索或筛选的参数字段数组]
     *
     * @return array $where 查询条件where数组
     */
    public function buildWhere(array $input = [])
    {
        /* 查询条件数组 */
        $where = [];

        /* 参数转换为查询条件数组 */
        if ($input) {
            foreach ($input as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                switch ($key) {
                    // 手机号
                    case 'card_id':
                        $where['card_id'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        $where[1] = 1;
        // 添加查询条件
        $this->whereAdd($where);

        return $this;
    }


    //重写添加条件方法
    public function whereAdd(array $where, $flag = false)
    {
        parent::whereAdd($where, $flag);
        return $this;
    }

    //重写关联关系方法
    public function with($with)
    {
        parent::with($with);
        return $this;
    }

    //重写查询字段方法
    public function select($columns)
    {
        parent::select($columns);
        return $this;
    }

    /**
     * [获取店铺下的所有会员信息]
     * @param  int $wid 店铺id
     * @return [type]      [description]
     */
    public function getMemberList($wid, $input = [], $card_id = 0)
    {
        $recordWhere['card_id'] = $card_id ?: [];

        if ($recordWhere['card_id']) {
            list($memberRecordList, $pageHtml) = $this->init('wid', $wid)->whereAdd($recordWhere)->buildWhere($input)->getList();
        } else {
            list($memberRecordList, $pageHtml) = $this->init('wid', $wid)->buildWhere($input)->getList();
        }

        $mids = []; //定义保存所有会员id
        $cardIds = []; //定义保存所有会员卡id
        $returnData = []; //定义返回的数据
        $relation = []; //定义用户id与会员卡id关系对应

        $member_card_time = [];//某会员领取会员卡时间
        $memberList = [];

        if ($memberRecordList['data']) {
            foreach ($memberRecordList['data'] as $memberRecord) {
                $mids[] = $memberRecord['mid'];
                $cardIds[] = $memberRecord['card_id'];
                $relation[$memberRecord['mid']] = $memberRecord['card_id'];
                if (!empty($card_id)) {//单张会员卡时，领取时间
                    $member_card_time[$memberRecord['mid']] = $memberRecord['in_card_at'];
                }
            }
            //重复的用户id (每个用户可以获得多张会员卡)
            $repeatMids = takeRepeatVal($mids);

            //去掉重复值 简化查询次数
            $mids = array_unique($mids);
            $cardIds = array_unique($cardIds);

            //改变重复数组键值 (从0开始)
            $uniqueValue = [];
            foreach ($repeatMids as $id) {
                $uniqueValue[] = $id;
            }

            $memberList = (new MemberService())->getListById($mids);
            $memberCardList['data'] = MCardService::getListById($cardIds);

            //用户与会员卡关系对应
            if ($memberCardList['data']) {
                $withShip = [];
                foreach ($memberCardList['data'] as $cardList) {
                    foreach ($relation as $lk => $la) {
                        if ($cardList['id'] == $la) {
                            $withShip[$lk]['title'] = $cardList['title'];
                            $withShip[$lk]['card_status'] = $cardList['card_status'];
                        }
                    }

                }
            }

            //组装数据
            if ($memberList) {
                foreach ($memberList as $mk => &$member) {

                    if (in_array($member['id'], $uniqueValue)) {
                        $member['is_more'] = true;  //说明该会员有多张会员卡
                    }
                    $member['title'] = $withShip[$member['id']]['title'];
                    $member['card_status'] = $withShip[$member['id']]['card_status'];
                    if (!empty($card_id) && isset($member_card_time[$member['id']])) {
                        $member['in_card_at'] = $member_card_time[$member['id']];
                    }
                }
            }

        }

        $returnData['member'] = $memberList;
        $returnData['card'] = $memberCardList['data'] ?? [];
        $returnData['pageHtml'] = $pageHtml;
        return $returnData;

    }

    /**
     * 会员卡列表
     * @param array $where
     * @param string $orderBy 排序字段
     * @param string $order 排序规则
     * @return mixed
     * @author: 梅杰 2018年8月24日
     * @update 何书哲 2019年06月20日 默认会员卡不存在会报错
     */
    public function getMemberCardList($where = [], $orderBy = 'buy_num', $order = 'desc')
    {
        $select = [
            'mid',
            'm.wid',
            'nickname',
            'mobile',
            'money',
            'score',
            'buy_num',
            'amount',
            'remark',
            'is_pull_black',
            'latest_access_time',
            'card_id',
            'in_card_at',
            'l.created_at',
            'source',
            DB::raw('COUNT(*) as count'),
        ];

        $query = DB::table('member as m')->select($select)->leftJoin('member_card_record as l', 'mid', '=', 'm.id')
            ->where(['l.status' => 1, 'is_member' => 1, 'm.status' => 0, 'l.wid' => $where['wid']])->whereNull('l.deleted_at')->whereNull('out_card_at')
            ->whereNull('m.deleted_at')->orderBy('is_default', 'DESC');
        if (isset($where['card_id'])) {
            $query = $query->where(['card_id' => $where['card_id']])->groupBy('mid');
            unset($where['card_id']);

        } else {
            $query = $query->groupBy('mid');
        }

        $tempTableQuery = DB::table(DB::raw("({$query->toSql()}) as temp "))->mergeBindings($query)
            ->wheres($where);
        $memberRecordList = $tempTableQuery->orderBy($orderBy, $order)->orderBy('mid', 'desc')->paginate();

        $memberCardService = new MemberCardService();

        foreach ($memberRecordList as &$value) {
            $cardInfo = $memberCardService->getCard($value->card_id);
            $value->in_card_at = date('Y-m-d', strtotime($value->in_card_at));
            $value->created_at = date('Y-m-d', strtotime($value->created_at));
            $value->title = $cardInfo['title'];
            //获取会员的所有会员卡信息
            $card = $this->getMenberCart($value->mid, $where['wid']);

            $value->card = array_column($card, 'memberCard');
            $default = array_filter($value->card, function ($re) use ($value) {
                return $re['id'] == $value->card_id;
            });
            //update 何书哲 2019年06月20日 默认会员卡不存在会报错
            if ($default) {
                $value->default = array_column(array_values($default), null)[0];
            } else {
                $value->default = ['expire_time' => '异常'];
            }
        }

        return $memberRecordList;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170512
     * @desc 获取会员会员卡列表
     * @param $mid
     */
    public function getMenberCart($mid, $wid)
    {
        $cartList = $this->init('wid', $wid)->model->where(['mid' => $mid, 'wid' => $wid, 'status' => 1, 'out_card_at' => null])
            ->orderBy('is_default', 'desc')->orderBy('is_new', 'desc')->orderBy('id', 'asc')->get()->load('memberCard')->toArray();
        $this->dealCart($cartList);
        return $cartList;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170512
     * @desc 处理会员会员卡数据
     * @param $data
     * @state 1:正常，2：未激活,3:已过期,4:未开始,-1：已删除,5: 已禁用
     * @update 增加会员卡过期时间
     */
    public function dealCart(&$data)
    {
        foreach ($data as $key => &$val) {
            if (empty($val['memberCard'])) {
                $val['state'] = -1;
                continue;
            }
            $val['memberCard']['power_desc'] = $this->replace($val['memberCard']['member_power']);
            if ($val['active_status'] == 0) {
                $val['memberCard']['expire_time'] = '未激活';
                $val['state'] = 2;
                continue;
            }

            switch ($val['memberCard']['limit_type']) {
                case 0: //无期限
                    $val['memberCard']['expire_time'] = '无期限';
                    break;
                case 1:
                    $day = (time() - strtotime($val['created_at'])) / 86400;
                    $val['memberCard']['expire_time'] = date('Y-m-d H:i:s', $val['memberCard']['limit_days'] * 86400 + strtotime($val['created_at']));
                    if ($val['memberCard']['limit_days'] < $day) {
                        $val['state'] = 3;
                        continue;
                    }
                    $val['leftDays'] = ceil($val['memberCard']['limit_days'] - $day);
                    break;
                case 2:
                    $val['memberCard']['expire_time'] = $val['memberCard']['limit_end'];
                    if (strtotime($val['memberCard']['limit_start']) > time()) {
                        $val['state'] = 4;
                        continue;
                    }
                    if (strtotime($val['memberCard']['limit_end']) < time()) {
                        $val['state'] = 3;
                        continue;
                    }
                    $day = (strtotime($val['memberCard']['limit_end']) - time()) / 86400;
                    $val['leftDays'] = ceil($day);
                    break;
                default:
                    continue;
                    break;

            }
            if ($val['memberCard']['state'] == -1) {
                $val['memberCard']['expire_time'] = '已删除';
                $val['state'] = -1;
                continue;
            } elseif ($val['memberCard']['state'] == 0) {
                $val['memberCard']['expire_time'] = '已禁用';
                $val['state'] = 5;
                continue;
            }


            $val['state'] = 1;
        }
        $this->sort($data);

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170512
     * @desc 替换数据
     * @param $str
     */
    public function replace($str)
    {
        $temp = [
            1 => '包邮',
            2 => '会员折扣',
            3 => '赠送优惠券',
            4 => '赠送积分',
        ];
        $tmpArray = [];
        foreach (explode(',', $str) as $val) {
            $tmpArray[] = $temp[$val];
        }
        return implode(',', $tmpArray);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 对会员卡进行排序，有效的会员卡拍上面
     * @desc
     * @param $data
     */
    public function sort(&$data)
    {
        $tempValid = [];
        $tempInvalid = [];
        $isDefault = false;
        foreach ($data as $key => $val) {
            if ($val['state'] == 1) {
                if ($val['is_default'] == 1) {
                    $isDefault = true;
                }
                $tempValid[] = $val;
            } else {
                if ($val['is_default'] == 1) {
                    $val['is_default'] = 0;
                }
                $tempInvalid[] = $val;
            }
        }
        if ($tempValid && !$isDefault) {
            $tempValid[0]['is_default'] = 1;
        }
        $data = array_merge($tempValid, $tempInvalid);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170517
     * @desc 获取会员卡号码
     */
    public function getCardNo()
    {
        $no = '';
        for ($i = 0; $i < 18; $i++) {
            $no = $no . rand(0, 9);
        }
        $card = $this->init()->model->where(['card_num' => $no])->first();
        if ($card) {
            return $this->getCardNo();
        } else {
            return $no;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170518
     * @desc 订单使用会员卡
     * @param $id
     */
    public function useCard($mid, $wid)
    {
        $result = [
            'errCode' => 0,
            'errMsg' => '',
            'data' => [
                'isOwn' => 0,
                'info' => [
                    'isDelivery' => 0,
                    'isDiscount' => 0,
                    'discount' => 10
                ]
            ]
        ];

        $data = $this->getMenberCart($mid, $wid);

        $card = [];
        foreach ($data as $value) {
            if ($value['state'] == 1 && $value['is_default'] == 1) {
                $card = $value;
            }
        }
        if (!$card) {
            return $result;
        }
        /*添加会员卡Id返回值*/
        $result['data']['info']['card_id'] = $card['card_id'];
        $result['data']['isOwn'] = 1;
        $power = explode(',', $card['memberCard']['member_power']);
        if (in_array(1, $power)) {
            $result['data']['info']['isDelivery'] = 1;
        }
        if (in_array(2, $power)) {
            $result['data']['info']['isDiscount'] = 1;
            $result['data']['info']['discount'] = $card['memberCard']['discount'];
        }
        return $result;
    }


}