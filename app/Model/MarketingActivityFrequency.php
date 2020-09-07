<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MarketingActivityFrequency extends Model
{
    /**
     * 频次单位 - 每天
     */
    const UNIT_EVERY_DAY = 1;
    
    /**
     * 频次单位 - 全程
     */
    const UNIT_WHOLE = 2;
    
    /**
     * 频次类型 - 可抽奖次数
     */
    const TYPE_CAN_BE = 1;
    
    /**
     * 频次类型 - 最多可中奖次数
     */
    const TYPE_MAX_WINS = 2;
    
    protected $table = 'marketing_activity_frequency';
    public $timestamps = false;

}