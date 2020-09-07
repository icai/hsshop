<?php
namespace App\Services\Marketing\Wheel;

use App\S\Member\MemberService;
use App\Services\Award\AwardFactory;
use App\Services\Marketing\ActivityAbstract;
use App\Services\Marketing\Exception;
use App\Services\Marketing\FrequencyService;

class WheelActivity extends ActivityAbstract{
    /**
     * 执行抽奖，返回中奖结果
     * @return array
     * @throws Exception
     */
    public function attend(){
        if(!$this->canAttend()){
            throw new Exception('您不符合活动参与条件');
        }
        
        if(!$this->isActive()){
            throw new Exception('活动未开始或已结束');
        }
        
        //积分扣减
        $this->costScoreBeforeDraw();
        
        $award = [];
        if($this->isWin()){//根据中奖概率计算中奖
            //返回奖品（并不一定中奖，当奖品已经发光时，依旧是未中奖）
            $award = $this->getAward();//获取奖品时，已经把奖品扣除了
            if($award){
                //发放奖品
                AwardFactory::make($award['award_id'])
                    ->setMemberId($this->getMemberId())
                    ->send($award['num']);
                
                //判断是否给中奖用户赠送积分
                if($this->data['is_give_winner_score']){
                    //积分赠送
                    $this->giveScoreAfterDraw();
                }
            }
        }else{//未中奖
            //积分赠送
            $this->giveScoreAfterDraw();
        }
        
        //记录参与日志
        $this->addAttendLog(
            $award['rule_id'] ?? 0,
            $award['award_id'] ?? 0,
            $award['num'] ?? 0
        );
        
        return $award;
    }
    
    /**
     * 获取用户剩余可参与次数（若为无限次，返回-1）
     * @return int
     * @update 梅杰 2018年7月27日 语法错误
     */
    public function getUserRemainingTimes()
    {
        if($this->is_need_mobile && !((new MemberService)->hasMobile($this->memberId)) ){
            //若要求必须绑定手机号，且没绑定，直接返回false
            return 0;
        }
        
        //根据频次规则返回剩余次数
        return (new FrequencyService($this->memberId))->init()->getRemainingTimes($this->activityId, $this->getMemberId());
    }
    
    /**
     * 返回中奖结果，同时扣除奖品数量。根据中奖概率，中奖后调用此算法。若没有剩余奖品，则返回空数组。
     * 返回格式如下：
     * [
     *   'award_id'=>1,
     *   'num'=>1,
     * ]
     * @return array
     */
    protected function getAward()
    {
        //随机奖品
        $award = $this->getRuleService()->randAward();
        if($award){
            //扣除奖品
            $this->getRuleService()->deduct($award['rule_id'], $award['num']);
        }
        
        return $award;
    }
}