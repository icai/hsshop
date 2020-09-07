<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivityMeta;
use App\Model\MarketingActivityShare;
use App\Services\ScoreServices;
use Illuminate\Support\Facades\DB;

abstract class ActivityAbstract implements \ArrayAccess{
    /**
     * 活动基础信息
     * @var array
     */
    protected $data = [];
    
    /**
     * 用户ID
     * @var int
     */
    protected $memberId = 0;
    
    /**
     * @var ActivityRuleAbstract
     */
    protected $ruleService;
    
    /**
     * @var int 活动ID
     * 这个属性只是为了调用方便，其值与$this->data['id']一致
     */
    protected $activityId;
    
    /**
     * @param array $activity
     */
    public function __construct($activity){
        $this->activityId = $activity['id'];
        
        if(is_object($activity)){
            $activity = $activity->toArray();
        }
        $activity_extra = $activity['marketingActivityExtra'];
        unset($activity['marketingActivityExtra']);
        $this->data = array_merge($activity, $activity_extra);
        
        //初始化用户ID
        $this->memberId = session('mid');
    }
    
    /**
     * 参与活动入口
     * @return array
     */
    abstract public function attend();
    
    /**
     * 能否参与活动
     * @return bool
     */
    public function canAttend(){
        //剩余参与次数为0，则不可再参与活动，否则可以参与活动
        return $this->getUserRemainingTimes() != 0;
    }
    
    /**
     * 获取用户剩余可参与次数
     *  - >0: 表示剩余次数
     *  - =0: 不能参与活动
     *  - <0: 不限制参与次数
     * @return int
     */
    abstract public function getUserRemainingTimes();
    
    /**
     * 获取活动规则
     * @return array
     */
    public function getRules(){
        return $this->getRuleService()->getRules();
    }
    
    /**
     * 获取规则服务
     * @return ActivityRuleAbstract
     */
    protected function getRuleService(){
        $this->ruleService || $this->ruleService = ActivityFactory::getRuleService(
            $this->data['type'],
            $this->activityId
        );
        
        return $this->ruleService;
    }
    
    /**
     * 检查参与频率
     * @return bool
     */
    public function checkFrequency(){
        return (new FrequencyService())->init()->check($this->activityId, $this->getMemberId());
    }
    
    /**
     * 插入参与日志
     * @param int $ruleId 中奖规则（若未中奖则为0）
     * @param int $awardId 奖品ID（若未中奖则为0）
     * @param int $awardNum 奖品数量（若未中奖则为0）
     */
    protected function addAttendLog($ruleId = 0, $awardId = 0, $awardNum = 0){
        (new AttendLogService($this->getMemberId()))
            ->init()->addLog(
            $this->activityId,
            $ruleId,
            $awardId,
            $awardNum
        );
        
        //activity_meta表attends自增
        MarketingActivityMeta::where([
            'activity_id'=>$this->activityId,
        ])->update([
            'visits'=>DB::raw('visits + 1')
        ]);
    }
    
    /**
     * 当前活动是否进行中
     * @return bool
     */
    public function isActive(){
        $now = time();
        if($now < strtotime($this->start_time) || $now > strtotime($this->end_time)){
            //活动未开始或已结束
            return false;
        }
        
        if($this->invalid_time && $this->invalid_time != '0000-00-00 00:00:00' && $now > strtotime($this->invalid_time)){
            //活动提前结束
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取分享信息
     * @return array
     */
    public function getShareInfo(){
        $shareInfo = MarketingActivityShare::find($this->data['activityId']);
        return $shareInfo ? $shareInfo->toArray() : [];
    }
    
    /**
     * 计算是否中奖
     *  - 若概率为0，则永远不会中奖
     *  - 若要100%中奖，则percent >= 100即可
     */
    protected function isWin(){
        if(!$this->data['percent']){
            return false;
        }
        
        if($this->data['percent'] >= 100){
            return true;
        }
        
        //概率是4位小数，转化为整数后计算概率
        $percent = $this->data['percent'] * 10000;
        $rand = mt_rand(0, 1000000);
        
        $percentResult = $rand <= $percent;
        if(!$percentResult){
            //若概率没符合，直接返回false
            return false;
        }
        
        //若概率符合，判断剩余可中奖次数是否为0
        return (new FrequencyService())->init()->getRemainingWinTimes($this->activityId, $this->getMemberId()) != 0;
    }
    
    /**
     * 抽奖前消耗积分
     * @return bool
     */
    protected function costScoreBeforeDraw(){
        if($this->data['cost_score']){
            $scoreService = new ScoreServices($this->getMemberId());
            if($scoreService->hasEnoughScore($this->data['cost_score'])){
                $scoreService->decr($this->data['cost_score']);
            }
        }
        
        return true;
    }
    
    /**
     * 抽奖后赠送积分
     * @return bool
     */
    protected function giveScoreAfterDraw(){
        if($this->data['give_score']){
            //赠送积分
            (new ScoreServices($this->getMemberId()))->incr($this->data['give_score']);
        }
        
        return true;
    }
    
    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }
    
    /**
     * @param int $memberId
     * @return ActivityAbstract
     */
    public function setMemberId(int $memberId)
    {
        $this->memberId = $memberId;
        return $this;
    }
    
    /**
     * 获取活动表所有参数
     * @return array
     */
    public function getData(){
        return $this->data;
    }
    
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
    
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    
    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    
    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @throws ErrorException
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        throw new ErrorException('只读属性，不可修改');
    }
    
    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @throws ErrorException
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new ErrorException('只读属性，不可修改');
    }
}