<?php
namespace App\Services\Marketing\Lottery;

use App\Model\MarketingActivityAttendLog;
use App\S\Member\MemberService;
use App\Services\Award\AwardFactory;
use App\Services\Marketing\ActivityAbstract;
use App\Services\Marketing\AttendLogService;
use App\Services\Marketing\Exception;

class LotteryActivity extends ActivityAbstract{
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
        
        //记录参与日志。
        //抽奖活动不会马上产生中奖结果，只记录一下参与记录，到时间点了再开奖
        $this->addAttendLog();
        
        return [];
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
        
        //抽奖活动只能参与有一次
        return (new AttendLogService($this->getMemberId()))
            ->init()->isAttend($this->activityId) ? 0 : 1;
    }
    
    /**
     * 开奖
     */
    public function draw(){
        $rules = $this->getRules();
        //把数组反转，先开低等级奖项
        $rules = array_reverse($rules);
        
        foreach($rules as $rule){
            if($rule['draw_at'] != null && $rule['draw_at'] != '0000-00-00 00:00:00'){
                //已经开过奖，跳过
                continue;
            }
            
            if($rule['remaining']){
                //随机出对应人数的参与者
                //应该不会有多人的。。懒得去分批处理量
                $attends = MarketingActivityAttendLog::where([
                    'activity_id'=>$this->activityId,
                    'rule_id'=>0,
                ])
                    ->limit(floor($rule['remaining'] / $rule['reward']))
                    ->inRandomOrder()
                    ->get(['mid'])
                    ->toArray();
                
                if($attends){
                    //挨个去发奖励
                    foreach($attends as $attend){
                        //扣除发掉的奖品
                        $this->getRuleService()->deduct($rule['id'], $rule['reward']);
                        
                        //发放奖品
                        AwardFactory::make($rule['award_id'])
                            ->setMemberId($this->getMemberId())
                            ->send($rule['reward']);
                        
                        //设置相关人员中奖信息
                        MarketingActivityAttendLog::where([
                            'activity_id' => $this->activityId,
                            'mid' => $attend['mid']
                        ])
                            ->update([
                                'rule_id' => $rule['id'],
                                'award_id' => $rule['award_id'],
                                'award_num' => $rule['reward'],
                            ]);
        
                    }
                }
            }
            
            //将规则设置为已开奖
            $this->getRuleService()->setDraw($rule['id']);
        }
    }
}