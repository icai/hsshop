<?php

namespace App\S\Market;

use App\Model\ActivityBonus;
use App\S\S;

/**
 * 红包活动领取记录
 * @author 许立 2018年07月18日
 */
class BonusRecordService extends S
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年07月20日
     */
    public function __construct()
    {
        parent::__construct('ActivityBonusRecord');
    }

    /**
     * 获取用户某次红包活动的参与记录
     * @param int $bonus_id 活动id
     * @param int $mid 用户id
     * @return array
     * @author 许立 2018年07月17日
     */
    public function getRecord($bonus_id, $mid)
    {
        $row = $this->model->where('activity_bonus_id', $bonus_id)->where('mid', $mid)->first();
        return $row ? $row->toArray() : [];
    }
}