<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivity;
use App\Model\MarketingActivityExtra;
use App\Model\MarketingActivityMeta;
use App\Model\MarketingActivityShare;
use App\Model\MarketingActivityVisitLog;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 活动
 */
class ActivityService extends Service{
    public $withAll = ['marketingActivityExtra', 'shareInfo'];
    
    /**
     * 初始化 设置唯一标识和redis键名
     *
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     *
     * @return $this
     *
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new MarketingActivity(), $uniqueKey, $uniqueValue, $idKey);
        
        return $this;
    }
    
    /**
     * 创建活动
     * @param string $name
     * @param int $type 活动类型
     * @param array $extra 对应marketing_activity和marketing_activity_extra表字段
     * @param array $rules 活动规则，根据活动类型对应不同的表
     * @param array $frequencies 参与频次
     * @param array $share 分享信息
     */
    public function create($name, $type, $extra = [], $rules = [], $frequencies = [], $share = []){
        $activityId = $this->addD([
            'name'=>$name,
            'wid'=>$extra['wid'] ?? session('wid', 0),
            'type'=>$type,
            'percent'=>$extra['percent'] ?? 0,
            'is_need_mobile'=>empty($extra['is_need_mobile']) ? 0 : 1,
            'start_time'=>$extra['start_time'] ?? Carbon::now(),
            'end_time'=>$extra['end_time'] ?? date('Y-m-d H:i:s', strtotime('+3 years')),//默认为3年后，基本也就是相当于不过期
        ], false);
        
        //插extra表
        MarketingActivityExtra::insert([
            'activity_id'=>$activityId,
            'description'=>'asdf',
        ]);
        
        //中奖规则信息
        if($rules){
            $ruleService = ActivityFactory::getRuleService($type, $activityId);
            $ruleService->addRules($rules);
        }
        
        //频次信息
        if($frequencies){
            (new FrequencyService())->init()->addFrequencies($activityId, $frequencies);
        }
        
        //分享信息
        MarketingActivityShare::insert([
            'title'=>$share['title'] ?? $share['title'],
            'cover'=>$share['title'] ?? $share['cover'],
            'description'=>$share['title'] ?? $share['description'],
            'link'=>$share['title'] ?? $share['link'],
        ]);
        
        //初始化活动数据表
        MarketingActivityMeta::insert([
            'activity_id'=>$activityId,
        ]);
        
        $activity = $this->getInfo($activityId);
        $this->updateR($activityId, $activity, false);
    }
    
    /**
     * 获取一个活动实例
     * @param int $activityId
     * @return ActivityAbstract
     */
    public function get($activityId){
        return ActivityFactory::make($activityId);
    }
    
    /**
     * 将指定活动置为失效
     * @param int $activityId
     * @return bool
     * @throws ErrorException
     */
    public function invalid($activityId){
        if(!$this->get($activityId)->isActive()){
            return false;
        }
        
        $this->where([
            'id'=>$activityId,
        ])->update(array(
            'invalid_time'=>date('Y-m-d H:i:s', time()),
        ));
    
        return true;
    }
    
    /**
     * 添加访问日志
     * @param $activityId
     * @param null|int $memberId
     */
    public function addViewLog($activityId, $memberId = null){
        $memberId || $memberId = session('mid', 0);
        
        MarketingActivityVisitLog::insert([
            'activity_id'=>$activityId,
            'mid'=>$memberId,
            'ip'=>DB::raw("INET_ATON('".request()->ip()."')"),
        ]);
        
        //activity_meta表visits自增
        MarketingActivityMeta::where([
            'activity_id'=>$activityId,
        ])->update([
            'visits'=>DB::raw('visits + 1')
        ]);
    }
}