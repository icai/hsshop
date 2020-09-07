<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 营销活动用户奖品收货地址设置表
 * @author 许立 2018年08月17日
 */
class ActivityAwardAddress extends Model
{
    /**
     * 活动类型-大转盘
     */
    const ACTIVITY_TYPE_WHEEL = 1;

    /**
     * 活动类型-砸金蛋
     */
    const ACTIVITY_TYPE_EGG = 2;

    /**
     * 活动类型-刮刮卡
     */
    const ACTIVITY_TYPE_SCRATCH = 3;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'activity_award_address';
    
    /**
     * 不更新时间字段
     */
    public $timestamps = false;
}