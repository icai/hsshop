<?php

namespace App\S\Market;

use App\Lib\Redis\CouponLogRedis;
use App\Model\CouponLog;
use App\S\Member\MemberService;
use App\S\S;
use DB;

/**
 * 营销活动-优惠券领取记录
 * @author 许立 2018年09月13日
 */
class CouponLogService extends S
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年09月13日
     */
    public function __construct()
    {
        parent::__construct('CouponLog');
    }

    /**
     * 获取非分页列表
     * @param array $where 查询条件
     * @param array|string $orderBy 排序字段
     * @param string $order 顺序 ASC: 顺序, DESC: 倒序
     * @return array
     * @author 许立 2018年09月13日
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取带分页列表
     * @param array $where 查询条件
     * @param array|string $orderBy 排序字段
     * @param string $order 顺序 ASC: 顺序, DESC: 倒序
     * @param int $pageSize 每页数量
     * @return array
     * @author 许立 2018年09月13日
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 根据id数组获取列表
     * @param array $idArr 主键id
     * @return array
     * @author 许立 2018年09月13日
     */
    public function getListById($idArr)
    {
        $redisData = $mysqlData = $redisId = [];
        $redis = new CouponLogRedis();
        $idArr = array_values($idArr);
        // 优先获取redis
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key])) {
                // redis中不存在的数据 之后从数据库获取
                $redisId[] = $value;
            } else {
                $redisData[$value] = $result[$key];
            }
        }
        if ($redisId) {
            // 获取数据库数据并保存到redis
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }

    /**
     * 根据id获取详情
     * @param int $id 主键id
     * @return array
     * @author 许立 2018年09月13日
     */
    public function getDetail($id)
    {
        $redis = new CouponLogRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            // redis不存在 取数据库
            $row = $this->model->where('id', $id)->first();
            if (empty($row)) {
                return [];
            }
            $row = $row->toArray();
            // 保存redis
            $redis->add($row);
        }
        return $row;
    }

    /**
     * 获取用户优惠券列表
     * @param $status string 状态 valid:未使用(公用) | invalid:已失效(微商城使用 包括 失效优惠券 使用中 已使用 已过期) | used(小程序使用 已使用) | expired(小程序使用 已失效)
     * @param $wid int 店铺id
     * @param $pidArr array 商品id数组 可选
     * @param $type int 平台，0:微商城,1:小程序
     * @param $getFuture bool 是否取未开始的优惠券
     * @return array
     * @author 许立 2018年09月13日
     * @update 许立 2018年09月14日 优化代码
     */
    public function getCoupons($status, $wid, $mid, $pidArr = [], $pagination = true)
    {
        $now = date('Y-m-d H:i:s');
        $where = ['wid' => $wid, 'mid' => $mid];
        if ($status == 'valid') {
            //可用优惠券
            $where['status'] = 0;
            $where['end_at'] = ['>', $now];

            if (!empty($pidArr)) {
                //判断某商品是否满足优惠条件: 全部商品有效 或者 指定商品有效
                $where['_closure'] = function ($query) use ($pidArr) {
                    $query->where('range_type', 0)->orWhere(function ($query) use ($pidArr) {
                        $query->where('range_type', 1)->where(function ($query) use ($pidArr) {
                            //转化为数组
                            if (!is_array($pidArr)) {
                                $pidArr = [intval($pidArr)];
                            }
                            foreach ($pidArr as $v) {
                                $v = addslashes(strip_tags($v));
                                $query->orWhereRaw('FIND_IN_SET(' . $v . ', range_value)');
                            }
                        });
                    });
                };
            }
        } elseif ($status == 'invalid') {
            //失效优惠券 使用中 已使用 已过期
            $where['_closure'] = function ($query) {
                $query->where('status', '>', 0)->orWhere('end_at', '<=', date('Y-m-d H:i:s'));
            };
        } elseif ($status == 'used') {
            //使用中 已使用
            $where['_closure'] = function ($query) {
                $query->where('status', '>', 0);
            };
        } elseif ($status == 'expired') {
            //已过期 且未使用
            $where['_closure'] = function ($query) {
                $query->where('status', '=', 0)->where('end_at', '<=', date('Y-m-d H:i:s'));
            };
        }

        if ($pagination) {
            list($list) = $this->listWithPage($where);
        } else {
            list($list) = $this->listWithoutPage($where);
        }

        //有效列表 计算总金额
        $list['coupon_total_amount'] = 0;
        if ($status == 'valid') {
            list($couponSum) = $this->model
                ->select(DB::raw('sum(amount) as couponSum'))
                ->where('mid', $mid)
                ->where('wid', $wid)
                ->where('status', 0)
                ->where('end_at', '>', $now)
                ->get()
                ->toArray();
            $list['coupon_total_amount'] = $couponSum['couponSum'];
        }
        return $list;
    }

    /**
     * 新增一条记录
     * @param array $data 新增数据
     * @return int|false
     * @author 许立 2018年09月13日
     */
    public function createRow($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 更新一条记录
     * @param int $id 主键id
     * @param array $data 要更新的数据
     * @return bool
     * @author 许立 2018年09月13日
     */
    public function update($id, $data)
    {
        $this->model->where(['id' => $id])->update($data);
        $data['id'] = $id;
        (new CouponLogRedis())->updateRow($data);
        return true;
    }

    /**
     * 获取领取的优惠券张数
     * @param array $where 查询条件
     * @return int
     * @author 许立 2018年09月13日
     */
    public function getCount($where)
    {
        return $this->model->where($where)->count();
    }

    /**
     * 根据订单id获取优惠券领取记录
     * @param int $oid 订单id
     * @return array
     * @author 许立 2018年09月13日
     */
    public function getRowByOid($oid)
    {
        $row = $this->model->where('oid', $oid)->first();
        return $row ? $row->toArray() : [];
    }
}
