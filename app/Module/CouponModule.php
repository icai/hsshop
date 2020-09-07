<?php

namespace App\Module;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Member\MemberService;

/**
 * 优惠券模块
 * @author 许立 2018年07月18日
 */
class CouponModule
{
    /**
     * 统计某红包活动下的优惠券数据
     * @param int $coupon_ids 优惠券id串
     * @param int $bonus_id 红包活动id
     * @return array
     * @author 许立 2018年07月20日
     * @update 许立 2018年07月23日 已领取个数和剩余金额修改
     */
    public function statistics($coupon_ids, $bonus_id)
    {
        $return = [];
        $coupon_log_service = new CouponLogService();
        // 获取优惠券规则
        $coupons = (new CouponService())->getListById(explode(',', $coupon_ids));
        foreach ($coupons as $coupon) {
            // 获取某优惠券的领取记录
            $where = [
                'coupon_id' => $coupon['id'],
                'activity_bonus_id' => $bonus_id
            ];
            $coupon_logs = $coupon_log_service->listWithoutPage($where)[0]['data'];
            // 已领取个数
            $received_count = count($coupon_logs);
            $return[$coupon['id']]['received_count'] = $received_count;
            // 已使用个数
            $return[$coupon['id']]['used_count'] = $received_count - (array_count_values(array_column($coupon_logs, 'status'))[0] ?? 0);
            // 剩余个数
            $return[$coupon['id']]['left'] = $coupon['left'];
            // 已领取金额
            $received_sum_amount = array_sum(array_column($coupon_logs, 'amount'));
            $return[$coupon['id']]['received_amount'] = $received_sum_amount;
            // 总金额 随机金额则使用区间
            $return[$coupon['id']]['is_random'] = 0;
            $return[$coupon['id']]['left_amount'] = 0;
            $return[$coupon['id']]['left_amount_min'] = 0;
            $return[$coupon['id']]['left_amount_max'] = 0;
            $left_amount = $coupon['left'] * $coupon['amount'];
            if ($coupon['is_random']) {
                $return[$coupon['id']]['is_random'] = 1;
                // 剩余金额
                $return[$coupon['id']]['left_amount_min'] = $left_amount;
                $return[$coupon['id']]['left_amount_max'] = $coupon['left'] * $coupon['amount_random_max'];
            } else {
                $return[$coupon['id']]['left_amount'] = $left_amount;
            }
        }

        return $return;
    }

    /**
     * 优惠券领取列表赋值用户信息
     * @param array $couponLogArray
     * @return array
     * @author 许立 2018年09月12日
     */
    public function handleCouponLogMember($couponLogArray)
    {
        //获取用户信息
        $members = (new MemberService())->getListById(array_unique(array_column($couponLogArray, 'mid')));
        $members = array_column($members, null, 'id');

        //优惠券列表返回用户信息
        foreach ($couponLogArray as $k => $v) {
            $member = $members[$v['mid']];
            $v['nickname'] = $member['nickname'];
            $v['avatar'] = $member['headimgurl'];
            $v['gender'] = $member['sex'];
            $v['mobile'] = $member['mobile'];
            $v['created_at_new'] = substr($v['created_at'], 5);
            $couponLogArray[$k] = $v;
        }

        return $couponLogArray;
    }

    /**
     * 领取会员卡时插入优惠券日志
     * @param int $mid 用户id
     * @param int $couponId 优惠券Id
     * @param int $count 会员卡中优惠券赠送的数目
     * @param int $wid 店铺id
     * @return bool
     * @update 许立 2018年09月12日 优化代码
     */
    public function createCouponLog($mid, $couponId, $count, $wid)
    {
        //获取该优惠券的具体信息
        $couponInfo = (new CouponService())->getDetail($couponId);
        if (empty($couponInfo) || $couponInfo['left'] <= 0) {
            return false;
        }
        //拼接数据
        $input = [
            'wid' => $wid,
            'mid'=>$mid,
            'coupon_id'=>$couponInfo['id'],
            'title'=>$couponInfo['title'],
            'limit_amount' => $couponInfo['limit_amount'],
            'range_type' => $couponInfo['range_type'],
            'range_value' => $couponInfo['range_value'],
            'start_at' => $couponInfo['start_at'],
            'end_at' => $couponInfo['end_at'],
            'amount'=>$couponInfo['amount'],
        ];

        $couponLogService = new CouponLogService();
        for ($i = 0; $i < $count; $i++) {
            $re = $couponLogService->createRow($input);
            if (!$re) {
                return false;
            }
        }

        return true;
    }

    /**
     * 给用户发放优惠券 不考虑会员等级要求 每人领取限额等限制
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param int $coupon_id 优惠券id
     * @param int $bonus_id 红包活动id
     * @return array
     * @author 许立 2018年07月19日 新增参数$bonus_id
     * @update 许立 2018年09月12日 优化代码
     */
    public function sendMemberCoupon($wid, $mid, $coupon_id, $bonus_id = 0)
    {
        $return = [
            'error_code' => 1,
            'error_msg' => '',
            'data' => 0
        ];

        if (empty($wid) || empty($mid) || empty($coupon_id)) {
            $return['error_msg'] = '参数不完整';
            return $return;
        }

        //获取优惠券详情
        $couponService = new CouponService();
        $coupon = $couponService->getDetail($coupon_id);
        if (empty($coupon)) {
            $return['error_msg'] = '优惠券不存在';
            return $return;
        }

        //用户信息
        $member = (new MemberService())->getRowById($mid);
        if (empty($member)) {
            $return['error_msg'] = '用户不存在';
            return $return;
        }

        //判断领取资格
        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['invalid_at'])) {
            $return['error_msg'] = '该优惠券已经失效';
            return $return;
        } elseif ($coupon['left'] < 1) {
            $return['error_msg'] = '该优惠券领完';
            return $return;
        } elseif ($coupon['expire_type'] == 0 && $now > $coupon['end_at']) {
            $return['error_msg'] = '该优惠券已经过期';
            return $return;
        }

        //随机领取备注
        $list = $couponService->getStaticList();

        //生效时间过期时间新需求 20171127 Herry
        $start = $coupon['start_at'];
        $end = $coupon['end_at'];
        if ($coupon['expire_type'] == 1) {
            //领到券当日零点开始N天内有效
            $start = date('Y-m-d') . ' 00:00:00';
            $days = $coupon['expire_days'] - 1;
            $end = date('Y-m-d', strtotime("+" . $days . " days")) . ' 23:59:59';
        } elseif ($coupon['expire_type'] == 2) {
            //领到券次日零点开始N天内有效
            $start = date('Y-m-d', strtotime("+1 day")) . ' 00:00:00';
            $days = $coupon['expire_days'];
            $end = date('Y-m-d', strtotime("+" . $days . " days")) . ' 23:59:59';
        }

        //成功领取
        $amount = $coupon['is_random'] ? rand($coupon['amount'] * 100, $coupon['amount_random_max'] * 100) / 100 : $coupon['amount'];
        $data = [
            'wid'          => $wid,
            'mid'          => $mid,
            'coupon_id'    => $coupon_id,
            'title'        => $coupon['title'],
            'amount'       => $amount,
            'limit_amount' => $coupon['limit_amount'],
            'start_at'     => $start,
            'end_at'       => $end,
            'remark'       => $list[1][array_rand($list[1])],
            'range_type'   => $coupon['range_type'],
            'range_value'  => $coupon['range_value'],
            'only_original_price' => $coupon['only_original_price'],
            'activity_bonus_id' => $bonus_id
        ];
        $couponReceiveID = (new CouponLogService())->createRow($data);
        if ($couponReceiveID) {
            //优惠券规则表 库存减1
            $couponService->increment($coupon_id, 'left', -1);
        } else {
            $return['error_msg'] = '领取优惠券失败';
            return $return;
        }

        $return['error_code'] = 0;
        $return['data'] = $couponReceiveID;
        return $return;
    }
}