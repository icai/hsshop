<?php
/**
 * User: hsz
 * Date: 2018/5/15
 * Time: 17:00
 */

namespace App\Module;

use App\Jobs\CheckGrantCard;
use App\S\Customer\PointRecordService;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use App\S\Scratch\ActivityScratchLogService;
use App\S\Scratch\ActivityScratchPrizeService;
use App\S\Scratch\ActivityScratchService;
use App\S\Scratch\ActivityScratchWinService;
use App\S\Product\ProductService;
use App\Services\MemberCardRecordService;

class ScratchModule
{
    /**
     * @author hsz
     * @date ${DATE}
     * @desc 检查抽奖频次
     */
    public function checkTimes($scratchData, $mid, $wid)
    {
        $result = ['success' => 0, 'message' => ''];
        $logService = new ActivityScratchLogService();
        $where = [
            'wid' => $wid,
            'mid' => $mid,
            'scratch_id' => $scratchData['id'],
        ];
        /*1：一人一次，2：一天一次，3：一天两次*/
        if ($scratchData['rule'] == 1) {
            $num = $logService->count($where);
            if ($num >= 1) {
                $result['message'] = '亲,你已经刮过这张啦！不能重复参与哦 ～';
                return $result;
            }
        } elseif ($scratchData['rule'] == 2) {
            $dayStart = strtotime(date("Y-m-d", time()));
            $dayStart = date("Y-m-d H:i:s", $dayStart);
            $now = date("Y-m-d H:i:s", time());
            $where['created_at'] = ['between', [$dayStart, $now]];
            $num = $logService->count($where);
            if ($num >= 1) {
                $result['message'] = '亲,今天已经刮完啦！明天再来吧 ～';
                return $result;
            }
        } else {
            $dayStart = strtotime(date("Y-m-d", time()));
            $dayStart = date("Y-m-d H:i:s", $dayStart);
            $now = date("Y-m-d H:i:s", time());
            $where['created_at'] = ['between', [$dayStart, $now]];
            $num = $logService->count($where);
            if ($num >= 2) {
                $result['message'] = '亲,今天已经刮完啦！明天再来吧 ～';
                return $result;
            }
        }
        $result['success'] = 1;
        return $result;
    }

    /**
     * @author hsz
     * @desc 判断用户是否有权限参加
     */
    public function checkPermission($scratchData,$mid,$wid)
    {
        $result = ['success'=>0,'message'=>''];
        //查看是否是否指定会员
        if ($scratchData['condit'] == 1){
            if ($scratchData['card_id']){
                $memberCardService = new MemberCardRecordService();
                $cardList = $memberCardService->getMenberCart($mid,$wid);
                $cardIds = [];
                foreach ($cardList as $val){
                    if ($val['state'] == 1){
                        $cardIds[] = $val['memberCard']['id'];
                    }
                }
                if (!$cardIds){
                    $result['message'] = '亲,该活动只有指定会员才能参加哦 ～';
                    return $result;
                }
                $res = array_intersect(explode(',',$scratchData['card_id']),$cardIds);
                if (!$res){
                    $result['message'] = '亲,该活动只有指定会员才能参加哦 ～';
                    return $result;
                }
            }
        }
        if ($scratchData['reduce_integra']>0){
            $memberData = (new MemberService())->getRowById($mid);
            if ($scratchData['reduce_integra']>$memberData['score']){
                $result['message'] = 'OMG,积分居然不够 ～';
                return $result;
            }
        }
        $result['success'] = 1;
        return $result;
    }

    /**
     * @author hsz
     * @desc 验证刮刮卡是否可用
     * @param $scratchId
     */
    public function check($scratchData,$mid, $wid)
    {
        $result = ['success'=>0,'message'=>''];
        if (!$scratchData){
            $result['message'] = '活动不存在';
            return $result;
        }
        //判断活动是否开始,，或结束
        $now = time();
        if (strtotime($scratchData['start_time'])>$now){
            $result['message'] = '亲,来的太早了,活动还没开始呢！';
            return $result;
        }
        if (strtotime($scratchData['end_time'])<$now){
            $result['message'] = '哎,居然错过了！活动已结束,下次早点来哦！';
            return $result;
        }
        //判断用户是否有权限
        $result = $this->checkPermission($scratchData,$mid, $wid);
        if ($result['success'] == 0){
            return $result;
        }
        $result = $this->checkTimes($scratchData,$mid,$wid);
        if ($result['success'] == 0){
            return $result;
        }
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author hsz
     * @desc 减积分
     * @param $num
     * @return bool
     */
    public function reduceIntegra($num,$wid,$mid)
    {
        $pointData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'point_type'=> 11,
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
     * @author hsz
     * @date 送积分
     * @desc
     * @param $num
     */
    public function sendIntegra($num,$wid,$mid)
    {
        $pointData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'point_type'=> 11,
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
     * @author hsz
     * @desc 写日志
     */
    public function addScratchLog($data)
    {
        $logData = [
            'wid'               => $data['wid'],
            'mid'               => $data['mid'],
            'scratch_id'          => $data['scratch_id'],
            'prize'             => $data['prize']??'',
            'reduce_integra'   => $data['reduce_integra']??0,
            'send_integra'     => $data['send_integra']??0,
            'is_win'            => $data['is_win']??0,
            'prize_type'        => $data['prize_type']??0
        ];
        $logService = new ActivityScratchLogService();
        $logService->add($logData);
    }

    /**
     * @author hsz
     * @desc 未中奖提示信息
     * @param $scratchData
     */
    public function info($scratchData,$mid)
    {
        $result = ['success'=>1,'message'=>'','data'=>[]];
        $logData['scratch_id'] = $scratchData['id'];
        if ($scratchData['send_integra']>0){
            $this->otherSendPoint($scratchData['wid'],$mid,$scratchData['send_integra'],$scratchData['id']);
            $result['message'] = '哎呀，大奖和你擦肩而过！送你'.$scratchData['send_integra'].'积分';
            $this->sendIntegra($scratchData['send_integra'],$scratchData['wid'],$mid);
            $logData['send_integra'] = $scratchData['send_integra'];
        }else{
            $result['message'] = '哎呀，真可惜，大奖和你擦肩而过！';
        }
        if ($scratchData['reduce_integra']>0){
            $logData['reduce_integra'] = $scratchData['reduce_integra'];
        }
        $logData['wid'] = $scratchData['wid'];
        $logData['mid'] = $mid;
        $this->addScratchLog($logData);
        return $result;
    }

    /**
     * @author hsz
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
     * @author hsz
     * @desc 赠送奖品
     * @param $winData
     */
    public function sendPrize($winData,$scratchData,$wid,$mid){

        //写中奖记录
        $winLogData = [
            'wid'       => $wid,
            'mid'       => $mid,
            'type'      => $winData['type'],
            'scratch_id'  => $winData['scratch_id']
        ];
        //坑爹的补丁
        if ($winLogData['type']==1){
            $winLogData['type']=6;
        }
        $logData['scratch_id'] = $winData['scratch_id'];
        $logData['send_integra'] = 0;
        $logData['is_win'] = 1;

        switch ($winData['type']){
            case 1:
                //送积分
                $this->sendIntegra($winData['content'],$wid,$mid);
                $logData['prize'] = $winLogData['title'] = '获取'.$winData['content'].'积分';
                $winLogData['content'] = $winData['content'];
                $logData['send_integra'] = $winData['content'];
                break;
            case 2:
                //送优惠券
                $coupon = (new CouponLogService())->getDetail($winData['content']);
                $winLogData['title'] = $coupon['title'] ?? '';
                $winLogData['content'] = $winData['content'];
                $logData['prize'] = '获取' . ($coupon['title'] ?? '');
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

        if($scratchData['is_send_all'] == 1 && $scratchData['send_integra']>0) {
            $logData['send_integra'] = $logData['send_integra']+$scratchData['send_integra'];
        }
        if($scratchData['reduce_integra']>0){
            $logData['reduce_integra'] = $scratchData['reduce_integra'];
        }
        $logData['wid'] = $wid;
        $logData['mid'] = $mid;
        $logData['prize_type'] = $winData['type'];
        $this->addScratchLog($logData);
        $winLogData['img'] = $winData['img'];
        (new ActivityScratchWinService())->add($winLogData);
    }

    /**
     * @author hsz
     * @desc 中奖处理
     * @param $scratchId
     * @return  0:未中奖，1：一等奖，2：二等奖，3：三等奖
     */
    public function compute($scratchData,$wid,$mid)
    {
        $result = ['success'=>0,'message'=>'','data'=>[]];
        //查看是否消耗积分
        if ($scratchData['reduce_integra']>0){
            $res = $this->reduceIntegra($scratchData['reduce_integra'],$wid,$mid);
            if (!$res){
                $result['message'] = 'OMG,积分居然不够 ～';
                return $result;
            }
        }
        //增加参与日志
        (new ActivityScratchService())->increment($scratchData['id'],'num',1);
        $prizeService = new ActivityScratchPrizeService();
        $res = $prizeService->getByScratchId($scratchData['id']);
        if (!$res){
            return $this->info($scratchData,$mid);
        }
        $grade = [];
        $prizeData = [];
        foreach ($res as $val){
            $grade[$val['grade']] = $val['num'];
            $prizeData[$val['grade']] = $val;
        }
        $res = $this->computeRate($scratchData['rate'],$grade);
        if ($res == 0){
            return $this->info($scratchData,$mid);
        }
        //中奖操作
        $winData = $prizeData[$res];
        //如果是优惠券，判断优惠券是否存在，如果不存在则不中奖
        if ($winData['type'] == 2) {
            $res = (new CouponModule())->sendMemberCoupon($wid,$mid,$winData['content']);
            if ($res['error_code'] != 0){
                return $this->info($scratchData,$mid);
            }else{
                $winData['content'] = $res['data'];
            }
        }
        //end
        $res = $prizeService->reduce($winData['id'],1);
        if (!$res) {
            return $this->info($scratchData,$mid);
        }
        //送奖品,中奖纪录，参与记录
        $this->sendPrize($winData,$scratchData,$wid,$mid);
        //是否参与送积分
        $winData['send_integra'] = 0;
        if ($scratchData['is_send_all'] == 1 && $scratchData['send_integra']>0){
            $this->otherSendPoint($wid, $mid, $scratchData['send_integra'],$scratchData['id']);
            $this->sendIntegra($scratchData['send_integra'],$wid,$mid);
            $winData['send_integra'] = $scratchData['send_integra'];
        }
        (new ActivityScratchService())->increment($scratchData['id'],'win_num',1);
        $result['success'] = 1;
        $result['data'] = $winData;
        return $result;
    }

    /**
     * @author hsz
     * @desc 奖品处理
     * @param $scratchData
     * @return array
     */
    public function dealPrizeInfo($scratchData)
    {
        $couponService = new CouponService();
        foreach ($scratchData as &$val) {
            if ($val['type'] == 1) {
                $val['descr'] = '积分：'.$val['content'];
            } elseif ($val['type'] == 2) {
                $coupon = $couponService->getDetail($val['content']);
                if ($coupon['is_limited']) {
                    if ($coupon['is_random']) {
                        if ($coupon['amount'] == $coupon['amount_random_max']) {
                            $val['descr'] = '优惠：满'.$coupon['limit_amount'].'减'.$coupon['amount'];
                        } else {
                            $val['descr'] = '优惠：满'.$coupon['limit_amount'].'减'.$coupon['amount'].'~'.$coupon['amount_random_max'];
                        }
                    } else {
                        $val['descr'] = '优惠：满'.$coupon['limit_amount'].'减'.$coupon['amount'];
                    }
                } else {
                    if ($coupon['is_random']) {
                        if ($coupon['amount'] == $coupon['amount_random_max']) {
                            $val['descr'] = '优惠：减'.$coupon['amount'];
                        } else {
                            $val['descr'] = '优惠：减'.$coupon['amount'].'~'.$coupon['amount_random_max'];
                        }
                    } else {
                        $val['descr'] = '优惠：减'.$coupon['amount'];
                    }
                }
            }
        }
        return $scratchData;
    }

    /**
     * @author hsz
     * @desc 额外赠送给几分
     */
    public function otherSendPoint($wid,$mid,$send_integra,$scratch_id=0)
    {
        //送积分也写入中奖纪录
        $insertWinLog = [
            'wid'       => $wid,
            'mid'       => $mid,
            'type'      => '5',
            'title'    => '额外赠送积分',
            'content' => $send_integra,
            'scratch_id' => $scratch_id
        ];
        (new ActivityScratchWinService())->add($insertWinLog);
    }
}