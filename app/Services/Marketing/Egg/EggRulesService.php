<?php
namespace App\Services\Marketing\Egg;

use App\Model\MarketingActivityRuleEgg;
use App\Services\Marketing\ActivityRuleAbstract;

class EggRulesService extends ActivityRuleAbstract{
    /**
     * 为活动添加规则
     * @param array $rules
     */
    public function addRules($rules = []){
        if($rules){
            $sort = 0;
            foreach($rules as $rule){
                $sort++;
                MarketingActivityRuleEgg::insert([
                    'activity_id'=>$this->getActivityId(),
                    'name'=>$rule['name'] ?? '',
                    'img'=>$rule['img'] ?? '',
                    'award_id'=>$rule['award_id'] ?? 0,
                    'percent'=>$rule['percent'] ?? 0,
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
        return MarketingActivityRuleEgg::where('activity_id', '=', $this->getActivityId())
            ->select(['id', 'name', 'img', 'award_id', 'reward', 'remaining', 'percent'])
            ->order('sort ASC')
            ->get()->toArray();
    }
    
    /**
     * 随机中奖结果。根据中奖概率，中奖后调用此算法。若没有剩余奖品，则返回空数组。
     * 返回格式如下：
     * [
     *   'award_id'=>1,
     *   'num'=>1,
     * ]
     * @return array
     */
    public function randAward()
    {
        $rules = $this->getRules();
        $ruleMap = [];
        foreach($rules as $k => $r){
            if($r['percent'] > 0 && $r['remaining'] > $r['reward']){
                //剩余奖品数大于单次奖品数才能参与抽奖
                $ruleMap[$k] = $r['percent'] * 100;
            }
        }
        if(!$ruleMap){
            //没有可抽的奖品，直接返回
            return [];
        }
        
        //根据中奖比例，计算出未中奖比例
        $proSum = array_sum($ruleMap);
        $ruleMap[''] = 10000 - $proSum;
        
        $key = $this->getRand($ruleMap);
        
        if($key === ''){
            return [];
        }else{
            $selectRule = $rules[$key];
            
            return [
                'rule_id'=>$selectRule['id'],
                'award_id'=>$selectRule['award_id'],
                'num'=>$selectRule['reward'],
            ];
        }
    }
    
    /**
     * 扣除规则中的剩余奖品数
     */
    public function deduct($rule_id, $num){
        //递减规则中剩余奖品数
        MarketingActivityRuleEgg::where(['id'=>$rule_id])->decrement('remaining', $num);
    }
}