<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MarketingActivityAward extends Model
{
    /**
     * 奖品类型 - 积分
     */
    const TYPE_INTEGRAL = 1;
    
    
    
    protected $table = 'marketing_activity_award';
    public $timestamps = false;

}