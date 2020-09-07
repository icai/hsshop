<?php
namespace App\Services\Award;

use App\Model\MarketingActivityAward;
use App\Services\Award\IntegralAward;

class AwardFactory{
    /**
     * 获取一个活动实例
     * @param $award_id
     * @return AwardAbstract
     * @throws ErrorException
     */
    public static function make($award_id){
        $award = MarketingActivityAward::find($award_id);
        if(!$award){
            throw new ErrorException('指定奖品ID不存在');
        }
        
        switch($award->type){
            case MarketingActivityAward::TYPE_INTEGRAL:
                //送积分
                return new ScoreAward($award);
            default:
                throw new ErrorException('不支持的奖品类型');
        }
    }
}