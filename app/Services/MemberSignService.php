<?php
namespace App\Services;

use App\Model\MemberSign;
use App\Model\MemberSignAward;
use App\Model\MemberSignRule;
use App\Services\Award\AwardFactory;

class MemberSignService extends Service{
    /**
     * 所有关联关系
     * @var array
     */
    public $withAll = ['signAwards'];
    
    public function __construct()
    {
        
    }
    
    /**
     * @param string $uniqueKey
     * @param string $uniqueValue
     * @param string $idKey
     * @return $this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new MemberSign(), $uniqueKey, $uniqueValue, $idKey);
        
        return $this;
    }
    
    /**
     * 执行签到
     * @param null|int $memberId
     * @return int 连续签到天数
     */
    public function sign($memberId = null){
        $memberId || $memberId = session('mid');
        
        if($this->isSignDay($memberId)){
            //若当日已签到，返回0
            return 0;
        }
        
        //获取最后一条签到记录
        $lastSign = $this->getLastSign($memberId);
        if($lastSign && $lastSign['date'] == date('Y-m-d', strtotime('-1 day'))){
            //连续签到
            $continuousDays = ++$lastSign['continuous_days'];
        }else{
            //断签或第一次签到
            $continuousDays = 1;
        }
    
        //插入签到记录
        $signId = $this->addSign($continuousDays, $memberId);
        
        //根据连续签到天数，发放奖励
        $this->sendAward($signId, $continuousDays, $memberId);
        
        return $continuousDays;
    }
    
    /**
     * 判断用户今天是否签到
     * @param int $memberId
     * @return bool
     */
    private function isSignDay($memberId){
        return !!MemberSign::where([
            'mid'=>$memberId,
            'date'=>date('Y-m-d'),
        ])->get()->toArray();
    }
    
    /**
     * 获取最后一条签到记录
     * @param int $memberId
     */
    private function getLastSign($memberId){
        return MemberSign::where([
            'mid'=>$memberId,
        ])
            ->order('date DESC')
            ->limit(1)
            ->first()->toArray();
    }
    
    /**
     * 获取签到规则周期
     * 取最大连续签到天数作为规则周期
     */
    private function getRuleCircle(){
        return MemberSignRule::max('day');
    }
    
    /**
     * 根据连续签到天数获取签到规则
     * @param int $day
     */
    private function getRule($day){
        return MemberSignRule::where([
            'day'=>$day,
        ])->get()->toArray();
    }
    
    /**
     * 根据签到天数，发放奖励
     * @param int $signId 签到日志ID
     * @param int $continuousDays 连续签到天数
     * @param int $memberId 用户ID
     */
    private function sendAward($signId, $continuousDays, $memberId){
        $circle = $this->getRuleCircle();
        $day = $continuousDays % $circle;
        
        //获取签到规则
        $rules = $this->getRule($day);
        
        //循环发放奖励
        foreach($rules as $rule){
            //记录签到中奖记录
            MemberSignAward::insert([
                'sign_id'=>$signId,
                'award_id'=>$rule['award_id'],
                'award_num'=>$rule['award_num'],
            ]);
            
            //发放奖励
            AwardFactory::make($rule['award_id'])
                ->setMemberId($memberId)
                ->send($rule['award_num']);
        }
    }
    
    /**
     * 添加签到记录
     * @param $continuousDays
     * @param $memberId
     * @return int 签到日志ID
     */
    private function addSign($continuousDays, $memberId){
        return MemberSign::insert([
            'mid'=>$memberId,
            'date'=>date('Y-m-d'),
            'continuous_days'=>$continuousDays,
        ]);
    }
}