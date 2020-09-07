<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivity;

class ActivityFactory{
    private static $activityMap = [
        MarketingActivity::TYPE_EGG=>'App\Services\Marketing\Egg\EggActivity',//砸金蛋
        MarketingActivity::TYPE_WHEEL=>'App\Services\Marketing\Wheel\WheelActivity',//大转盘
        MarketingActivity::TYPE_CARD=>'App\Services\Marketing\Card\CardActivity',//刮刮卡
        MarketingActivity::TYPE_LOTTERY=>'App\Services\Marketing\Lottery\LotteryActivity',//幸运大抽奖
    ];
    
    private static $ruleMap = [
        MarketingActivity::TYPE_EGG=>'App\Services\Marketing\Egg\EggRulesService',//砸金蛋
        MarketingActivity::TYPE_WHEEL=>'App\Services\Marketing\Wheel\WheelRulesService',//大转盘
        MarketingActivity::TYPE_CARD=>'App\Services\Marketing\Card\CardRulesService',//刮刮卡
        MarketingActivity::TYPE_LOTTERY=>'App\Services\Marketing\Lottery\LotteryRulesService',//幸运大抽奖
    ];
    
    /**
     * 获取一个活动实例
     * @param $activity_id
     * @return ActivityAbstract
     * @throws ErrorException
     */
    public static function make($activity_id){
        $activity = (new ActivityService)->init()->getInfo($activity_id);
        
        if(!$activity){
            throw new ErrorException('指定活动ID不存在');
        }
        
        if(isset(self::$activityMap[$activity['type']])){
            return new self::$activityMap[$activity['type']]($activity);
        }else{
            throw new ErrorException('不支持的活动类型');
        }
    }
    
    /**
     * 获取活动规则服务
     * @param $type
     * @param int $activity_id
     * @return ActivityRuleAbstract
     * @throws ErrorException
     */
    public static function getRuleService($type, $activity_id){
        if(isset(self::$ruleMap[$type])){
            return new self::$ruleMap[$type]($activity_id);
        }else{
            throw new ErrorException('不支持的活动类型');
        }
    }
}