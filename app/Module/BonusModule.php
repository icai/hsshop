<?php

namespace App\Module;
use App\Model\ActivityBonus;
use App\Model\ActivityBonusRecord;
use App\S\Market\BonusRecordService;
use App\S\Market\BonusService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Member\MemberService;
use App\S\Store\MicroPageService;
use App\S\WXXCX\WXXCXMicroPageService;
use WeixinService;
use App\S\Weixin\ShopService;

/**
 * 红包活动
 * @author 许立 2018年07月17日
 */
class BonusModule
{
    /**
     * 新建红包活动
     * @param array $array 待新建的数据
     * @return bool
     * @author 许立 2018年07月20日
     * @update 许立 2018年08月06日 增加微商城微页面id字段
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addBonus($array)
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'wid' => $array['wid'],
            'title' => $array['title'],
            'start_at' => $array['start_at'],
            'end_at' => $array['end_at'],
            'coupon_ids' => implode(',', $array['coupon_ids']),
            'image' => $array['image'],
            'micro_page_id' => $array['micro_page_id'],
            'shop_micro_page_id' => $array['shop_micro_page_id'],
            'show_interval' => $array['show_interval'],
            'created_at' => $now,
            'updated_at' => $now
        ];
        $bonus_service = new BonusService();
        $data['status'] = ActivityBonus::BONUS_STATUS_ON;
        $result_update = true;
        if (!empty($array['use_this'])) {
            // 停止其他红包活动
            $result_update = $bonus_service->model->where('status', ActivityBonus::BONUS_STATUS_ON)->update(['status' => ActivityBonus::BONUS_STATUS_STOP]);
        }
        $result_insert = $bonus_service->model->insertGetId($data);

        if ($result_insert && $result_update) {
            return true;
        }

        return false;
    }

    /**
     * 获取红包详情
     * @param int $bonus_id 红包活动id
     * @return array
     * @author 许立 2018年07月20日
     * @update 许立 2018年08月06日 增加微商城微页面标题, 获取标题
     */
    public function getDetail($bonus_id)
    {
        // 活动详情
        $bonus = (new BonusService())->getDetail($bonus_id);
        if ($bonus) {
            // 优惠券列表
            $bonus['coupons'] = (new CouponService())->getListById(explode(',', $bonus['coupon_ids']));
            // 小程序微页标题
            $micro_page = (new WXXCXMicroPageService())->getRowById($bonus['micro_page_id']);
            $bonus['micro_page_title'] = $micro_page['data']['title'] ?? '';
            // 微商城微页标题
            $shop_micro_page = (new MicroPageService())->getRowById($bonus['shop_micro_page_id']);
            $bonus['shop_micro_page_title'] = $shop_micro_page['data']['page_title'] ?? '';
        }

        return $bonus;
    }

    /**
     * 编辑红包活动
     * @param int $bonus_id 红包活动id
     * @param array $array 待更新的数据
     * @return bool
     * @author 许立 2018年07月20日
     * @update 许立 2018年08月06日 增加微商城微页面id字段
     */
    public function editBonus($bonus_id, $array)
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'title' => $array['title'],
            'start_at' => $array['start_at'],
            'end_at' => $array['end_at'],
            'coupon_ids' => implode(',', $array['coupon_ids']),
            'image' => $array['image'],
            'micro_page_id' => $array['micro_page_id'],
            'shop_micro_page_id' => $array['shop_micro_page_id'],
            'show_interval' => $array['show_interval'],
            'created_at' => $now,
            'updated_at' => $now
        ];
        return !!(new BonusService())->model->where('id', $bonus_id)->update($data);
    }

    /**
     * 删除红包活动
     * @param int $bonus_id 红包活动id
     * @return bool
     * @author 许立 2018年07月20日
     */
    public function deleteBonus($bonus_id)
    {
        return !!(new BonusService())->model->where('id', $bonus_id)->update(['status' => ActivityBonus::BONUS_STATUS_DELETE]);
    }

    /**
     * 停止红包活动
     * @param int $bonus_id 红包活动id
     * @return bool
     * @author 许立 2018年07月20日
     */
    public function stopBonus($bonus_id)
    {
        return !!(new BonusService())->model->where('id', $bonus_id)->update(['status' => ActivityBonus::BONUS_STATUS_STOP]);
    }

    /**
     * 处理红包活动的优惠券金额统计数据
     * @param array $list 待处理的红包活动列表
     * @return array
     * @author 许立 2018年07月20日
     */
    public function dealWithListData($list)
    {
        $now = date('Y-m-d H:i:s');
        $coupon_module = new CouponModule();
        foreach ($list as $k => $v) {
            // 处理状态
            if ($v['status'] == 2) {
                $v['status_string'] = '已停止';
            } elseif ($v['start_at'] > $now) {
                $v['status_string'] = '未开始';
            } elseif ($v['end_at'] <= $now) {
                $v['status_string'] = '已结束';
            } else {
                $v['status_string'] = '进行中';
            }
            // 处理优惠券
            $count_data = $coupon_module->statistics($v['coupon_ids'], $v['id']);
            // 已拆金额
            $v['received_amount'] = array_sum(array_column($count_data, 'received_amount'));
            // 剩余金额
            $v['left_amount_string'] = array_sum(array_column($count_data, 'left_amount'));
            if (array_sum(array_column($count_data, 'is_random'))) {
                // 包含随机优惠券
                $left_amount_min = array_sum(array_column($count_data, 'left_amount_min'));
                $left_amount_max = array_sum(array_column($count_data, 'left_amount_max'));
                $v['left_amount_string'] = ($v['received_amount'] + $left_amount_min) . '~' . ($v['received_amount'] + $left_amount_max);
            }
            // 拆分成功
            $v['received_count'] = array_sum(array_column($count_data, 'received_count'));
            // 剩余库存
            $v['left'] = array_sum(array_column($count_data, 'left'));
            // 已使用
            $v['used_count'] = array_sum(array_column($count_data, 'used_count'));
            $list[$k] = $v;
        }
        return $list;
    }

    /**
     * 获取前端红包活动的弹窗显示类型
     * @param int $mid 用户id
     * @param int $wid 店铺id 许立 2018年07月27日
     * @return int 0: 弹窗展示, 1: 右下角图标展示, 2: 不展示
     * @author 许立 2018年07月20日
     */
    public function getShowType($mid, $wid)
    {
        // 获取唯一进行中的活动
        $bonus = (new BonusService())->getOn($wid);
        if (empty($bonus)) {
            return ActivityBonus::BONUS_WINDOW_STATUS_HIDE;
        }

        // 判断最新活动用户领取状态
        $record = (new BonusRecordService())->getRecord($bonus['id'], $mid);
        if ($record) {
            $window_status = ActivityBonus::BONUS_WINDOW_STATUS_HIDE;
            if ($record['status']  == ActivityBonusRecord::RECORD_STATUS_CLOSE) {
                $window_status = ActivityBonus::BONUS_WINDOW_STATUS_CORNER;
            }
            return $window_status;
        }

        return ActivityBonus::BONUS_WINDOW_STATUS_SHOW;
    }

    /**
     * 用户关闭红包弹窗
     * @param int $mid 用户id
     * @param int $wid 店铺id 许立 2018年07月27日
     * @return array 格式: ['err_code' => 0, 'err_msg' => '', 'data' => []]
     * @author 许立 2018年07月20日
     */
    public function closeWindow($mid, $wid)
    {
        // 返回格式
        $return = [
            'err_code' => 1,
            'err_msg' => '',
            'data' => []
        ];

        // 获取唯一进行中的活动
        $bonus = (new BonusService())->getOn($wid);
        if (empty($bonus)) {
            $return['err_msg'] = '商家已停止活动';
            return $return;
        }

        // 获取记录
        $record_service = new BonusRecordService();
        $record = $record_service->model
            ->where('activity_bonus_id', $bonus['id'])
            ->where('mid', $mid)
            ->first();
        if (empty($record)) {
            // 关闭
            $insert = [
                'activity_bonus_id' => $bonus['id'],
                'mid' => $mid
            ];
            $result = $record_service->model->insertGetId($insert);
            if (!$result) {
                $return['err_msg'] = '关闭活动出错';
                return $return;
            }
        }

        $return['err_code'] = 0;
        return $return;
    }

    /**
     * 用户拆红包
     * @param int $mid 用户id
     * @param int $wid 店铺id 许立 2018年07月27日
     * @return array 格式: ['err_code' => 0, 'err_msg' => '', 'data' => []]
     * @author 许立 2018年07月20日
     */
    public function unpack($mid, $wid)
    {
        // 返回格式
        $return = [
            'err_code' => 1,
            'err_msg' => '',
            'data' => []
        ];

        // 获取唯一进行中的活动
        $bonus = (new BonusService())->getOn($wid);
        if (empty($bonus)) {
            $return['err_msg'] = '商家已停止活动';
            return $return;
        }

        // 获取记录
        $record_service = new BonusRecordService();
        $record = $record_service->model
            ->where('activity_bonus_id', $bonus['id'])
            ->where('mid', $mid)
            ->first();
        if (empty($record)) {
            // 拆红包
            $insert = [
                'activity_bonus_id' => $bonus['id'],
                'mid' => $mid,
                'status' => ActivityBonusRecord::RECORD_STATUS_UNPACK
            ];
            $record_service->model->insertGetId($insert);
        } else {
            $record_service->model->where('id', $record->id)->update(['status' => ActivityBonusRecord::RECORD_STATUS_UNPACK]);
        }

        if (empty($record) || $record->status == ActivityBonusRecord::RECORD_STATUS_CLOSE) {
            // 第一次弹窗 或者 只关闭过弹窗 没拆过红包 则发优惠券
            $couponModule = new CouponModule();
            foreach (explode(',', $bonus['coupon_ids']) as $coupon_id) {
                $couponModule->sendMemberCoupon($bonus['wid'], $mid, $coupon_id, $bonus['id']);
            }
        }

        $return['err_code'] = 0;
        return $return;
    }

    /**
     * 用户拆红包后的红包详情页数据
     * @param int $mid 用户id
     * @param int $wid 店铺id 许立 2018年07月27日
     * @return array 格式: ['err_code' => 0, 'err_msg' => '', 'data' => []]
     * @author 许立 2018年07月20日
     * @update 许立 2018年08月06日 增加微商城微页面id字段
     * @update 许立 2018年08月08日 返回优惠券生效时间时分秒
     */
    public function unpackDetail($mid, $wid)
    {
        // 返回格式
        $return = [
            'err_code' => 1,
            'err_msg' => '',
            'data' => []
        ];

        // 获取唯一进行中的活动
        $bonus = (new BonusService())->getOn($wid);
        if (empty($bonus)) {
            $return['err_msg'] = '商家已停止活动';
            return $return;
        }

        // 获取用户信息
        $member = (new MemberService())->getRowById($mid);
        $phone = $member['mobile'] ?? '';
        if ($phone) {
            $phone_first_string = mb_substr($phone, 0, 3, 'utf-8');
            $phone_last_string = mb_substr($phone, -4, 4, 'utf-8');
            $phone = $phone_first_string . str_repeat('*', 4) . $phone_last_string;
        }

        // 拆红包所得的优惠券
        $where = [
            'coupon_id' => ['in', explode(',', $bonus['coupon_ids'])],
            'activity_bonus_id' => $bonus['id'],
            'mid' => $mid
        ];
        $coupons = (new CouponLogService())->listWithoutPage($where)[0]['data'];

        $return['err_code'] = 0;
        $return['data'] = [
            'image' => $bonus['image'],
            'micro_page_id' => $bonus['micro_page_id'],
            'shop_micro_page_id' => $bonus['shop_micro_page_id'],
            'phone' => $phone,
            'coupons' => $coupons
        ];
        return $return;
    }

    /**
     * 获取红包活动弹窗
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @return array
     * @author 许立 2018年08月08日
     */
    public function showWindow($wid, $mid)
    {
        // 店铺
        //$shop = WeixinService::init()->where(['id' => $wid])->getInfo($wid);
        $shopService = new ShopService();
        $shop = $shopService->getRowById($wid);
        // 获取唯一进行中的活动
        $bonus = (new BonusService())->getOn($wid);
        $data = [
            'is_show' => (new BonusModule())->getShowType($mid, $wid),
            'shop_name' => $shop['shop_name'] ?? '',
            'activity_title' => $bonus['title'] ?? ''
        ];

        return $data;
    }
}