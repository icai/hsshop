<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivityFrequency;
use App\Services\Service;

/**
 * 活动参与频次检查类
 */
class FrequencyService extends Service{
    /**
     * @var int 用户ID
     */
    private $memberId;
    
    /**
     * @var array
     */
    public $field = ['id', 'type', 'limit', 'unit', 'activity_id'];
    
    public function __construct()
    {
        $this->request = app('request');
    }
    
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new MarketingActivityFrequency(), $uniqueKey, $uniqueValue, $idKey);
        
        return $this;
    }
    
    /**
     * 逐条检查频率规则是否符合
     * @param int $activityId 活动ID
     * @param null|int $memberId
     * @return bool
     */
    public function check($activityId, $memberId = null){
        $remainingTimes = $this->getRemainingTimes($activityId, $memberId);
        
        return $remainingTimes != 0;
    }
    
    /**
     * 获取指定活动剩余可参与次数
     * @param int $activityId
     * @param null|int $memberId
     * @return int
     */
    public function getRemainingTimes($activityId, $memberId = null){
        $memberId || $memberId = $this->getMemberId();
        
        list($list) = $this->where(['activity_id'=>$activityId])->getList(false);
        
        $times = -1;//默认为无限次
        foreach($list['data'] as $frequency){
            $singleRemainingTimes = $this->getSingleRemainingTimes($frequency, $memberId);
            if($singleRemainingTimes == 0){
                //若有一条规则不符合，直接返回0
                return 0;
            }else if($singleRemainingTimes > 0){
                //若此条规则返回的不是无限次，与现有剩余次数比较，取数值小的
                if($times == -1){
                    //若目前是无限次，直接赋值为单条规则剩余次数
                    $times = $singleRemainingTimes;
                }else{
                    //若目前不是无限次，取少的剩余次数为准
                    $times = min($times, $singleRemainingTimes);
                }
            }
        }
        
        return $times;
    }
    
    /**
     * 获取指定活动剩余可参与次数
     * @param int $activityId
     * @param null|int $memberId
     * @return int
     */
    public function getRemainingWinTimes($activityId, $memberId = null){
        $memberId || $memberId = $this->getMemberId();
    
        list($list) = $this->where(['activity_id'=>$activityId])->getList(false);
    
        $times = -1;//默认为无限次
        foreach($list['data'] as $frequency){
            $singleRemainingTimes = $this->getSingleRemainingWinTimes($frequency, $memberId);
            if($singleRemainingTimes == 0){
                //若有一条规则不符合，直接返回0
                return 0;
            }else if($singleRemainingTimes > 0){
                //若此条规则返回的不是无限次，与现有剩余次数比较，取数值小的
                if($times == -1){
                    //若目前是无限次，直接赋值为单条规则剩余次数
                    $times = $singleRemainingTimes;
                }else{
                    //若目前不是无限次，取少的剩余次数为准
                    $times = min($times, $singleRemainingTimes);
                }
            }
        }
    
        return $times;
    }
    
    /**
     * 根据频次规则，获取剩余参与次数
     * （-1 代表无限次）
     * @param array $frequency
     * @param null|int $memberId
     * @return int
     * @throws ErrorException
     */
    protected function getSingleRemainingTimes(array $frequency, $memberId = null){
        $memberId || $memberId = $this->getMemberId();
        
        if(!$frequency['limit']){
            //若limit为0，则不限制次数
            return -1;
        }
    
        if($frequency['type'] == MarketingActivityFrequency::TYPE_MAX_WINS){
            //此方法不判断可中奖次数，是返回给用户看的可参与次数
            return -1;
        }
        
        $attendLogService = (new AttendLogService($memberId))
            ->init();
    
        //判断参与次数
        if($frequency['unit'] == MarketingActivityFrequency::UNIT_EVERY_DAY){
            //按天计算
            $attendCount = $attendLogService->getTodayAttendCount($frequency['activity_id']);
        }else if($frequency['unit'] == MarketingActivityFrequency::UNIT_WHOLE){
            //按全程计算
            $attendCount = $attendLogService->getWholeAttendCount($frequency['activity_id']);
        }else{
            throw new ErrorException('频率单位取值异常');
        }
        
        if($attendCount >= $frequency['limit']){
            return 0;
        }else{
            return $frequency['limit'] - $attendCount;
        }
    }
    
    /**
     * 根据频次规则，获取剩余可中奖次数
     * （-1 代表无限次）
     * @param array $frequency
     * @param null|int $memberId
     * @return int
     * @throws ErrorException
     */
    protected function getSingleRemainingWinTimes(array $frequency, $memberId = null)
    {
        $memberId || $memberId = $this->getMemberId();
    
        if(!$frequency['limit']){
            //若limit为0，则不限制次数
            return -1;
        }
    
        $attendLogService = (new AttendLogService($memberId))
            ->init();
    
        if($frequency['type'] == MarketingActivityFrequency::TYPE_CAN_BE){
            //判断参与次数
            if($frequency['unit'] == MarketingActivityFrequency::UNIT_EVERY_DAY){
                //按天计算
                $attendCount = $attendLogService->getTodayAttendCount($frequency['activity_id']);
            }else if($frequency['unit'] == MarketingActivityFrequency::UNIT_WHOLE){
                //按全程计算
                $attendCount = $attendLogService->getWholeAttendCount($frequency['activity_id']);
            }else{
                throw new ErrorException('频率单位取值异常');
            }
        }else if($frequency['type'] == MarketingActivityFrequency::TYPE_MAX_WINS){
            //判断最大中奖次数
            if($frequency['unit'] == MarketingActivityFrequency::UNIT_EVERY_DAY){
                //按天计算
                $attendCount = $attendLogService->getTodayWinCount($frequency['activity_id']);
            }else if($frequency['unit'] == MarketingActivityFrequency::UNIT_WHOLE){
                //按全程计算
                $attendCount = $attendLogService->getWholeWinCount($frequency['activity_id']);
            }else{
                throw new ErrorException('频率单位取值异常');
            }
        }else{
            throw new ErrorException('频率类型取值异常');
        }
    
        if($attendCount >= $frequency['limit']){
            return 0;
        }else{
            return $frequency['limit'] - $attendCount;
        }
    }
    
    /**
     * 添加频次规则
     * @param int $activityId
     * @param array $frequencies
     * @internal param $activity_id
     */
    public function addFrequencies($activityId, $frequencies){
        foreach($frequencies as $frequency){
            $this->add([
                'activity_id'=>$activityId,
                'type'=>$frequency['type'],
                'limit'=>$frequency['limit'],
                'unit'=>$frequency['unit'],
            ], false);
        }
    }
    
    /**
     * 获取memberId，默认为当前用户mid
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->memberId ? $this->memberId : session('mid');
    }
    
    /**
     * @param mixed $memberId
     * @return FrequencyService
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
        return $this;
    }
}