<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivityAttendLog;
use App\Services\Service;
use Carbon\Carbon;

/**
 * 中奖记录
 */
class AttendLogService extends Service{
    /**
     * @var array
     */
    public $field = ['id', 'rule_id', 'activity_id', 'award_id', 'award_num', 'created_at'];
    
    /**
     * @var int 用户ID
     */
    private $memberId;
    
    /**
     * @var array 活动参与记录
     */
    private $attendLogs = [];
    
    public function __construct($memberId)
    {
        $this->memberId = $memberId;
        $this->request = app('request');
    }
    
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new MarketingActivityAttendLog(), $uniqueKey, $uniqueValue, $idKey);
        
        return $this;
    }
    
    /**
     * 获取用户抽奖记录
     * @param int $activityId
     * @return mixed
     */
    public function getAttendLogs($activityId){
        if(!isset($this->attendLogs[$this->getMemberId()])){
            $attendLogs = $this->where([
                'activity_id'=>$activityId,
                'mid'=>$this->getMemberId(),
            ])
                ->order('id DESC')
                ->getList(false)
            ;
            $this->attendLogs[$this->getMemberId()] = $attendLogs[0]['data'];
        }
        
        return $this->attendLogs[$this->getMemberId()];
    }
    
    /**
     * 获取今天参与次数
     * @param int $activityId
     * @return int
     */
    public function getTodayAttendCount($activityId){
        $attendLogs = $this->getAttendLogs($activityId);
    
        $count = 0;
        foreach($attendLogs as $log){
            $time = new Carbon($log['created_at']);
            if($time->isToday()){
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * 获取今天中奖次数
     * @param int $activityId
     * @return int
     */
    public function getTodayWinCount($activityId){
        $attendLogs = $this->getAttendLogs($activityId);
    
        $count = 0;
        foreach($attendLogs as $log){
            if($log['award_num'] && (new Carbon($log['created_at']))->isToday()){
                $count++;
            }
        }
    
        return $count;
    }
    
    /**
     * 获取全程参与次数
     * @param int $activityId
     * @return int
     */
    public function getWholeAttendCount($activityId){
        return count($this->getAttendLogs($activityId));
    }
    
    /**
     * 获取全程中奖次数
     * @param int $activityId
     * @return int
     */
    public function getWholeWinCount($activityId){
        $attendLogs = $this->getAttendLogs($activityId);
    
        $count = 0;
        foreach($attendLogs as $log){
            if($log['award_num']){
                $count++;
            }
        }
    
        return $count;
    }
    
    public function addLog($activityId, $ruleId = 0, $awardId = 0, $awardNum = 0){
        $this->add([
            'activity_id'=>$activityId,
            'rule_id'=>$ruleId,
            'award_id'=>$awardId,
            'award_num'=>$awardNum,
            'mid'=>$this->getMemberId(),
        ], false);
    }
    
    /**
     * 判断用户是否参与过指定活动
     * @param int $activityId
     * @return bool
     */
    public function isAttend($activityId){
        return !!MarketingActivityAttendLog::where([
            'activity_id'=>$activityId,
            'mid'=>$this->getMemberId()
        ])
            ->select(['id'])
            ->get()->toArray();
    }
    
    /**
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->memberId;
    }
    
    /**
     * @param mixed $memberId
     * @return $this
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
        return $this;
    }
}