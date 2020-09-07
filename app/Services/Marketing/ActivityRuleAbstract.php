<?php
namespace App\Services\Marketing;

abstract class ActivityRuleAbstract{
    /**
     * @var int 活动ID
     */
    protected $activity_id;
    
    /**
     * @param int $activity_id 活动ID
     */
    public function __construct($activity_id)
    {
        $this->activity_id = $activity_id;
    }
    
    /**
     * 为活动添加规则
     * @param array $rules
     */
    abstract public function addRules($rules = []);
    
    /**
     * 获取活动规则
     * @return array
     */
    abstract public function getRules();
    
    /**
     * @return int
     */
    public function getActivityId()
    {
        return $this->activity_id;
    }
    
    /**
     * @param int $activity_id
     * @return $this
     */
    public function setActivityId(int $activity_id)
    {
        $this->activity_id = $activity_id;
        return $this;
    }
    
    /**
     * 一个比较流行的概率算法
     * @param array $proArr
     * @return int|string
     */
    protected function getRand($proArr) {
        $result = '';
        
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        if(!$proSum){
            return '';
        }
        
        //概率数组循环
        foreach($proArr as $key => $proCur){
            $randNum = mt_rand(1, $proSum);
            if($randNum <= $proCur){
                $result = $key;
                break;
            }else{
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        
        return $result;
    }
}