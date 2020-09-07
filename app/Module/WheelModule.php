<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/8/2
 * Time: 13:45
 */

namespace App\Module;


use App\Jobs\CheckGrantCard;
use App\S\Customer\PointRecordService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Product\ProductService;
use App\S\Wheel\ActivityWheelLogService;
use App\S\Wheel\ActivityWheelPrizeService;
use App\S\Wheel\ActivityWheelService;
use App\S\Wheel\ActivityWheelTimeService;
use App\S\Wheel\ActivityWheelWinService;
use App\Services\MemberCardRecordService;
use OrderDetailService;
use DB;

class WheelModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170802
     * @desc 验证大转盘是否可用
     * @param $wheelId
     */
    public function check($wheelData,$mid)
    {
        $result = ['success'=>0,'message'=>''];
        if (!$wheelData){
            $result['message'] = '活动不存在';
            return $result;
        }
        //判断活动是否开始,，或结束
        $now = time();
        if (strtotime($wheelData['start_time'])>$now){
            $result['message'] = '您来的太早啦！活动还没开始呢!';
            return $result;
        }
        if (strtotime($wheelData['end_time'])<$now){
            $result['message'] = '活动已结束,下次早点来哦！';
            return $result;
        }
        //判断用户是否有权限
        $result = $this->checkPermission($wheelData,$mid);
        if ($result['success'] == 0){
            return $result;
        }
        $result = $this->checkTimes($wheelData,$mid);
        if ($result['success'] == 0){
            return $result;
        }
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170802
     * @desc 判断用户是否有权限参加
     */
    public function checkPermission($wheelData,$mid)
    {
        $result = ['success'=>0,'message'=>''];
        //查看是否是否指定会员
        if ($wheelData['condit'] == 1){
            if ($wheelData['card_id']){
                $memberCardService = new MemberCardRecordService();
                $cardList = $memberCardService->getMenberCart($mid,$wheelData['wid']);
                $cardIds = [];
                foreach ($cardList as $val){
                    if ($val['state'] == 1){
                        $cardIds[] = $val['memberCard']['id'];
                    }
                }
                if (!$cardIds){
                    $result['message'] = '很抱歉，您的资格未达到!';
                    return $result;
                }
                $res = array_intersect(explode(',',$wheelData['card_id']),$cardIds);
                if (!$res){
                    $result['message'] = '很抱歉，您的资格未达到!';
                    return $result;
                }
            }
        }
        if ($wheelData['reduce_integra']>0){
            $memberData = (new MemberService())->getRowById($mid);
            if ($wheelData['reduce_integra']>$memberData['score']){
                $result['message'] = '您的积分不够啦 ～';
                return $result;
            }
        }
        $result['success'] = 1;
        return $result;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc 检查抽奖频次
     */
    public function checkTimes($wheelData,$mid)
    {
        $result = ['success'=>0,'message'=>''];
        $logService = new ActivityWheelLogService();
        //获取当前用户另外增加的次数

        $timeData = (new ActivityWheelTimeService())->getList(['mid'=>$mid,'wheel_id'=>$wheelData['id']]);
        if ($timeData){
            $addedNum = $timeData[0]['num'];
        }else{
            $addedNum = 0;
        }

        /*1：一天一次，2：每人一次*/
        if ($wheelData['rule'] == 1){
            $dayStart =  strtotime(date("Y-m-d",time()));
            $dayStart = date("Y-m-d H:i:s",$dayStart);
            $now = date("Y-m-d H:i:s",time());
            $where = [
                'mid'       => $mid,
                'wheel_id'   =>$wheelData['id'],
                'created_at' => ['between', [$dayStart, $now]],
            ];
            $num = $logService->count($where);
            if ($num >= ($wheelData['times'] + $addedNum)){
                $result['message'] = '您今日的抽奖次数已用完，请明日再来';
                return $result;
            }
        }else{
            $where = [
                'mid'       => $mid,
                'wheel_id'   =>$wheelData['id'],
            ];
            $num = $logService->count($where);
            if ($num >= ($wheelData['times'] + $addedNum)){
                $result['message'] = '您的抽奖次数已用完';
                return $result;
            }
        }
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170803
     * @desc 计算是否中奖
     * @return  0:未中奖，1：一等奖，2：二等奖，3：三等奖
     */
    public function computeRate($rate,$grade)
    {
        $rand = rand(0,100);
        if ($rand>$rate){
            return 0;
        }
        $all = 0;
        $rate = [];
        foreach ($grade as $key=>$val){
            $rate[$key] = $all = $all+$val;
        }
        //奖品数量如果奖品数量为零则未中奖
        if ($all<=0){
            return 0;
        }
        $rand = mt_rand(0,$all-1);

        foreach ($rate as $key=>$value){
            if ($value>$rand){
                return $key;
            }
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170803
     * @desc 中奖处理
     * @param $wheelId
     * @return  0:未中奖，1：一等奖，2：二等奖，3：三等奖
     * @update 许立 2018年08月17日 中奖表增加活动id字段
     */
    public function compute($wheelData,$wid,$mid)
    {
        $result = ['success'=>0,'message'=>'','data'=>[]];
        //查看是否消耗积分
        if ($wheelData['reduce_integra']>0){
            $res = $this->reduceIntegra($wheelData['reduce_integra'],$wid,$mid);
            if (!$res){
                $result['message'] = '亲！积分不足哦！';
                return $result;
            }
        }
        //增加参与日志
        (new ActivityWheelService())->increment($wheelData['id'],'num',1);

        $prizeService = new ActivityWheelPrizeService();
        $res = $prizeService->getByWheelId($wheelData['id']);
        if (!$res){
            return $this->info($wheelData,$mid);
        }
        $grade = [];
        $prizeData = [];
        foreach ($res as $val){
            $grade[$val['grade']] = $val['num'];
            $prizeData[$val['grade']] = $val;
        }
        $res = $this->computeRate($wheelData['rate'],$grade);
        if ($res == 0){
            return $this->info($wheelData,$mid);
        }
        //中奖操作
        $winData = $prizeData[$res];
        //如果是优惠券，判断优惠券是否存在，如果不存在则不中奖
            if ($winData['type'] == 2) {
                $res = (new CouponModule())->sendMemberCoupon($wid,$mid,$winData['content']);
                if ($res['error_code'] != 0){
                    return $this->info($wheelData,$mid);
                }else{
                    $winData['content'] = $res['data'];
                }
            }
        //end
        $res = $prizeService->reduce($winData['id'],1);
        if (!$res) {
            return $this->info($wheelData,$mid);
        }
        //送奖品,中奖纪录，参与记录
        $this->sendPrize($winData,$wheelData,$wid,$mid);
        //是否参与送积分
        $winData['send_integra'] = 0;
        if ($wheelData['is_send_all'] == 1 && $wheelData['send_integra']>0){
            $this->otherSendPoint($wid,$mid,$wheelData['send_integra'], $wheelData['id']);
            $this->sendIntegra($wheelData['send_integra'],$wid,$mid);
            $winData['send_integra'] = $wheelData['send_integra'];
        }
        (new ActivityWheelService())->increment($wheelData['id'],'win_num',1);
        $result['success'] = 1;
        $result['data'] = $winData;
        return $result;
    }




    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170816
     * @desc 判断用户是否
     * @param $coupon_id
     */
    public function checkCoupon($coupon_id,$wid,$mid)
    {
        //获取优惠券详情
        $coupon = (new CouponService())->getDetail($coupon_id);
        $coupon || error('优惠券不存在');
        //判断领取资格
        //何书哲 2018/3/15 大转盘送优惠券多长时间失效状态的判断
        $receiveFlag = true;

        if (!empty($coupon['invalid_at'])) { //该优惠券已经失效
            $receiveFlag = false;
        } elseif ($coupon['left'] < 1) { //优惠券领光
            $receiveFlag = false;
        } elseif ($coupon['member_card_id']) { //指定会员卡才能领取
            $res = (new MemberCardRecordService())->init('wid', $wid)
                ->where(['mid' => $mid, 'card_id' => $coupon['member_card_id']])
                ->getInfo();
            if($receiveFlag && empty($res)) {
                $receiveFlag = false;
            }
        } elseif ($coupon['expire_type'] == 0 && time() < strtotime($coupon['start_at'])) { //优惠券领取未开始
            $receiveFlag = false;
        } elseif ($coupon['expire_type'] == 0  && time() > strtotime($coupon['end_at'])) { //优惠券领取已结束
            $receiveFlag = false;
        }
        //end

        /**
        if (!empty($coupon['invalid_at'])) {
        $receiveFlag = false;
        } elseif ($now < $coupon['start_at']) {
        $receiveFlag = false;
        } elseif ($now > $coupon['end_at']) {
        $receiveFlag = false;
        } elseif ($coupon['left'] < 1) {
        $receiveFlag = false;
        } elseif ($coupon['member_card_id']) {
        //指定会员卡才能领取
        $res = (new MemberCardRecordService())->init('wid', $wid)
        ->where(['mid' => $mid, 'card_id' => $coupon['member_card_id']])
        ->getInfo();
        if ($receiveFlag && empty($res)) {
        $receiveFlag = false;
        }
        }
         **/

        if ($receiveFlag) {
            //查询领取记录 是否超过定额
            $couponLogCount = (new CouponLogService())->getCount(['mid' => $mid, 'coupon_id' => $coupon['id']]);
            if ($receiveFlag && $coupon['quota'] && $couponLogCount >= $coupon['quota']) {
                $receiveFlag = false;
            }
        }

        return $receiveFlag;
    }
    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170803
     * @desc 未中奖提示信息
     * @param $wheelData
     * @update 许立 2018年08月17日 中奖表增加活动id字段, 中奖日志表增加奖品类型字段
     */
    public function info($wheelData,$mid)
    {
        $result = ['success'=>1,'message'=>'','data'=>[]];
        $logData['wheel_id'] = $wheelData['id'];
        if ($wheelData['send_integra']>0){
            $this->otherSendPoint($wheelData['wid'],$mid,$wheelData['send_integra'], $wheelData['id']);
            $result['message'] = '哎呀，大奖和您擦肩而过！送你'.$wheelData['send_integra'].'积分';
            $this->sendIntegra($wheelData['send_integra'],$wheelData['wid'],$mid);
            $logData['send_integra'] = $wheelData['send_integra'];
        }else{
            $result['message'] = '大奖和您擦肩而过，再接再厉哦!';
        }
        if ($wheelData['reduce_integra']>0){
            $logData['reduce_integra'] = $wheelData['reduce_integra'];
        }
        $logData['wid'] = $wheelData['wid'];
        $logData['mid'] = $mid;
        $logData['prize_type'] = 0;
        $this->addWheelLog($logData);
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 送积分
     * @desc
     * @param $num
     */
    public function sendIntegra($num,$wid,$mid)
    {
        $pointData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'point_type'=> 8,
            'is_add'    => 1,
            'score'     => $num,
        ];
        (new PointRecordService())->insertData($pointData);
        $res = (new MemberService())->increment($mid,'score',$num);
        //按规则发放会员卡队列
        dispatch((new CheckGrantCard($mid,$wid))->onQueue('CheckGrantCard'));
        return $res;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 减积分
     * @param $num
     * @return bool
     */
    public function reduceIntegra($num,$wid,$mid)
    {
        $pointData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'point_type'=> 8,
            'is_add'    => 0,
            'score'     => $num,
        ];
        $res =  (new MemberService())->decrement($mid,'score',$num);
        if($res){
            (new PointRecordService())->insertData($pointData);
            return true;
        }else{
            return false;
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170803
     * @desc 赠送奖品
     * @param $winData
     * @update 许立 2018年08月17日 中奖表增加活动id字段, 中奖日志表增加奖品类型字段
     */
    public function sendPrize($winData,$wheelData,$wid,$mid){

        //写中奖记录
        $winLogData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'type'      => $winData['type'],
            'wheel_id'  => $winData['wheel_id']
        ];
        //坑爹的补丁
        if ($winLogData['type']==1){
            $winLogData['type']=6;
        }

        $logData['wheel_id'] = $winData['wheel_id'];
        $logData['send_integra'] = 0;
        $logData['is_win'] = 1;

        switch ($winData['type']){
            case 1:
                $this->sendIntegra($winData['content'],$wid,$mid);
                $logData['prize'] = $winLogData['title'] = '大转盘赠送积分';
                $winLogData['content'] = $winData['content'];
                $logData['send_integra'] = $winData['content'];
                break;
            case 2:
                $coupon = (new CouponLogService())->getDetail($winData['content']);
                $winLogData['title'] = $coupon['title'] ?? '';
                $winLogData['content'] = $winData['content'];
                $logData['prize'] = '获取'.($coupon['title'] ?? '');
                break;
            case 3:
                $winLogData['title'] = $winData['content'];
                $winLogData['content'] = $winData['method'];
                $logData['prize'] = $winData['content'];
                break;
            case 4:
                $productData = (new ProductService())->model->where('id',$winData['content'])->get(['id','title'])->toArray();
                $productData = array_pop($productData);
                $winLogData['title'] = $productData['title'];
                $winLogData['content'] = $productData['id'];
                $logData['prize'] = $productData['title'];
                break;
        }

        if($wheelData['is_send_all'] == 1 && $wheelData['send_integra']>0) {
            $logData['send_integra'] = $logData['send_integra']+$wheelData['send_integra'];
        }
        if($wheelData['reduce_integra']>0){
            $logData['reduce_integra'] = $wheelData['reduce_integra'];
        }
        $logData['wid'] = $wid;
        $logData['mid'] = $mid;
        $logData['prize_type'] = $winData['type'];
        $this->addWheelLog($logData);
        $winLogData['img'] = $winData['img'];
        (new ActivityWheelWinService())->add($winLogData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170804
     * @desc 写日志
     * @update 许立 2018年08月17日 中奖日志表增加奖品类型字段
     */
    public function addWheelLog($data)
    {
        $logData = [
            'wid'               => $data['wid'],
            'mid'               => $data['mid'],
            'wheel_id'          => $data['wheel_id'],
            'prize'             => $data['prize']??'',
            'prize_type'        => $data['prize_type'] ?? 0,
            'reduce_integra'   => $data['reduce_integra']??0,
            'send_integra'     => $data['send_integra']??0,
            'is_win'            => $data['is_win']??0,
        ];

        $logService = new ActivityWheelLogService();
        $logService->add($logData);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170913
     * @desc 增加抽奖次数
     */
    public function addTime($oid,$mid)
    {
        $odData = OrderDetailService::init()->model->where('oid',$oid)->get(['id','product_id'])->toArray();
        if (!$odData){
            return true;
        }
        foreach ($odData as $val){
            $wheelService = new ActivityWheelService();
            $wheelTimeService = new ActivityWheelTimeService();
            $now = date('Y-m-d H:i:s',time());
            $where = [
                'pids'  => ['like','%,'.$val['product_id'].',%'],
                'start_time'    => ['<',$now],
                'end_time'      => ['>',$now],
            ];
            $wheelData = $wheelService->getList($where);
            foreach ($wheelData as $val){
                $timeData = $wheelTimeService->getList(['mid'=>$mid,'wheel_id'=>$val['id']]);
                if ($timeData){
                    $timeData = array_pop($timeData);
                    $wheelTimeService->increment($timeData['id'],'num',1);
                }else{
                    $timeData = [
                        'mid'       => $mid,
                        'wheel_id'  => $val['id'],
                        'num'       => 1,
                    ];
                    $wheelTimeService->add($timeData);
                }
            }
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180517
     * @desc 额外赠送给几分
     * @param $wid
     * @param $mid
     * @param $send_integra
     * @param int $wheelId 活动id
     * @update 许立 2018年08月17日 中奖表增加活动id字段
     */
    public function otherSendPoint($wid,$mid,$send_integra, $wheelId = 0)
    {
        //送积分也写入中奖纪录
        $insertWinLog = [
            'wid'       => $wid,
            'mid'       => $mid,
            'type'      => '5',
            'title'    => '额外赠送积分',
            'content' => $send_integra,
            'wheel_id' => $wheelId
        ];
        (new ActivityWheelWinService())->add($insertWinLog);
    }

    /**
     * 获取参与人数
     * @param array $activityIdArr 活动id数组
     * @return array
     * @author 许立 2018年08月16日
     */
    public function getMemberCount($activityIdArr)
    {
        return (new ActivityWheelLogService())->model
            ->select(DB::raw('wheel_id, COUNT(DISTINCT mid) AS memberCount'))
            ->whereIn('wheel_id', $activityIdArr)
            ->groupBy('wheel_id')
            ->get()
            ->toArray();
    }
}