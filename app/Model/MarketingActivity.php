<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MarketingActivity extends Model
{
    /**
     * 活动类型 - 砸金蛋
     */
    const TYPE_EGG = 1;
    
    /**
     * 活动类型 - 转盘
     */
    const TYPE_WHEEL = 2;
    
    /**
     * 活动类型 - 刮刮卡
     */
    const TYPE_CARD = 3;
    
    /**
     * 活动类型 - 幸运大抽奖
     */
    const TYPE_LOTTERY = 4;
    
    
    protected $table = 'marketing_activity';
    public $timestamps = false;
    
    
    public function marketingActivityExtra() {
        return $this->hasOne('App\Model\MarketingActivityExtra', 'activity_id');
    }
    
    public function shareInfo(){
        return $this->hasOne('App\Model\MarketingActivityShare', 'activity_id');
    }
}