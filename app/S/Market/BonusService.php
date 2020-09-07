<?php

namespace App\S\Market;

use App\Model\ActivityBonus;
use App\S\S;

/**
 * 红包活动
 * @author 许立 2018年07月17日
 */
class BonusService extends S
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年07月20日
     */
    public function __construct()
    {
        parent::__construct('ActivityBonus');
    }

    /**
     * 列表页面
     * @param array $where 查询条件
     * @param array|string $orderBy 排序字段
     * @param string $order 顺序 ASC: 顺序, DESC: 倒序
     * @param int $pageSize 每页数量
     * @return array
     * @author 许立 2018年07月20日
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        //直接查询列表所有字段 不缓存redis
        $this->setParameter('all');
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 获取活动详情
     * @param int $bonus_id 活动id
     * @return array
     * @author 许立 2018年07月17日
     */
    public function getDetail($bonus_id)
    {
        $detail = $this->model->where('id', $bonus_id)->first();
        return $detail ? $detail->toArray() : [];
    }

    /**
     * 获取状态列表
     * @return array
     * @author 许立 2018年07月17日
     */
    public function getStatusList()
    {
        return ['all' => '所有活动', 'future' => '未开始', 'on' => '进行中', 'end' => '已结束'];
    }

    /**
     * 获取唯一进行中且没停用没删除的活动
     * @param int $wid 店铺id 许立 2018年07月27日
     * @return array
     * @author 许立 2018年07月18日
     */
    public function getOn($wid)
    {
        $now = date('Y-m-d H:i:s');
        // 启用且正在进行中的活动
        $bonus = $this->model
            ->where('wid', $wid)
            ->where('status', ActivityBonus::BONUS_STATUS_ON)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->first();
        return $bonus ? $bonus->toArray() : [];
    }

    /**
     * 设置的时间是否不跟现有活动冲突
     * @param int $wid 店铺id 许立 2018年07月27日
     * @param string $start_at 开始时间
     * @param string $end_at 结束时间
     * @param int $ignore_bonus_id [可选] 需要剔除的红包活动id
     * @return bool
     * @author 许立 2018年07月20日
     * @update 许立 2018年08月10日 设置的活动时间允许和过期活动时间冲突
     */
    public function isTimeValid($wid, $start_at, $end_at, $ignore_bonus_id = 0)
    {
        // 获取进行中和未来的活动
        $select = $this->model
            ->where('wid', $wid)
            ->where('status', ActivityBonus::BONUS_STATUS_ON)
            ->where('end_at', '>', date('Y-m-d H:i:s'));
        $ignore_bonus_id && $select = $select->where('id', '<>', $ignore_bonus_id);
        $bonus_list = $select->get()->toArray();
        foreach ($bonus_list as $bonus) {
            if (($bonus['start_at'] <= $start_at && $start_at <= $bonus['end_at'])
                || ($bonus['start_at'] <= $end_at && $end_at <= $bonus['end_at'])
                || ($start_at <= $bonus['start_at'] && $bonus['end_at'] <= $end_at)) {
                return false;
            }
        }

        return true;
    }
}