<?php
namespace App\Services\Marketing\Lottery;

use App\Model\MarketingActivityRuleLottery;
use App\Services\Marketing\ActivityRuleAbstract;

class LotteryRulesService extends ActivityRuleAbstract{
    /**
     * 为活动添加规则
     * @param array $rules
     */
    public function addRules($rules = []){
        if($rules){
            $sort = 0;
            foreach($rules as $rule){
                $sort++;
                MarketingActivityRuleLottery::insert([
                    'activity_id'=>$this->getActivityId(),
                    'name'=>$rule['name'] ?? '',
                    'img'=>$rule['img'] ?? '',
                    'award_id'=>$rule['award_id'] ?? 0,
                    'reward'=>$rule['reward'] ?? 0,
                    'total'=>$rule['total'] ?? 0,
                    'remaining'=>$rule['remaining'] ?? $rule['total'] ?? 0,
                    'sort'=>$sort,
                ]);
            }
        }
    }
    
    /**
     * 获取活动规则
     * @return array
     */
    public function getRules(){
        return MarketingActivityRuleLottery::where('activity_id', '=', $this->getActivityId())
            ->select(['id', 'name', 'img', 'award_id', 'reward', 'remaining', 'draw_at'])
            ->order('sort ASC')
            ->get()->toArray();
    }
    
    /**
     * 扣除规则中的剩余奖品数
     */
    public function deduct($rule_id, $num){
        //递减规则中剩余奖品数
        MarketingActivityRuleLottery::where(['id'=>$rule_id])->decrement('remaining', $num);
    }
    
    /**
     * 设为已开奖
     */
    public function setDraw($rule_id){
        MarketingActivityRuleLottery::where(['id'=>$rule_id])->update([
            'draw_at'=>date('Y-m-d H:i:s'),
        ]);
    }
}