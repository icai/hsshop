<?php

namespace App\Jobs;

use App\Lib\BLogger;
use App\Model\CashLog;
use App\Model\CompanyPayOrder;
use App\Model\DistributeBank;
use App\Model\Income;
use App\S\BalanceLogService;
use App\S\Book\UsersBookService;
use App\S\Customer\PointRecordService;
use App\S\FavoriteService;
use App\S\Groups\GroupsDetailService;
use App\S\Market\ActivityAwardAddressService;
use App\S\Market\BonusRecordService;
use App\S\Market\EggMemberService;
use App\S\Market\ResearchRecordService;
use App\S\Market\CouponLogService;
use App\S\Member\MemberService;
use App\S\Order\OrderZitiService;
use App\S\Scratch\ActivityScratchLogService;
use App\S\Scratch\ActivityScratchService;
use App\S\Scratch\ActivityScratchWinService;
use App\S\ShareEvent\LiShareLogService;
use App\S\ShareEvent\ShareEventRecordService;
use App\S\ShareEvent\ShareEventShareService;
use App\S\Vote\EnrollInfoService;
use App\S\Wheel\ActivityWheelLogService;
use App\S\Wheel\ActivityWheelWinService;
use App\Services\MemberCardRecordService;
use App\Services\Order\OrderLogService;
use App\Services\Order\OrderRefundService;
use App\Services\Order\OrderService;
use App\Services\OrderRefundMessageService;
use App\Services\ProductEvaluateDetailService;
use App\Services\ProductEvaluateService;
use App\Services\Shop\MemberAddressService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BindMobile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public $smid;
    public $mid;
    public $umid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($smid, $mid, $umid = 0)
    {
        //
        $this->smid = $smid;
        $this->mid  = $mid;
        $this->umid = $umid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MemberService $memberService, OrderService $orderService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        BLogger::getBindLogger('info')->info('绑定手机号码原mid='.$this->smid.';最后mid='.$this->mid);
        //处理账号打通合并的分销关系数据
        $where = ['pid' => $this->smid];
        $res   = $memberService->getList($where);
        if ($res) {
            $ids = array_column($res, 'id');
            $memberService->batchUpdate($ids, ['pid' => $this->mid]);
            \Log::info('账号打通,分销关系合并:');
            $data = [
                'smid' => $this->smid,
                'mid'  => $this->mid,
                'ids'  => $ids,
            ];
            \Log::info($data);
        }

        //处理地址问题
        if ($this->umid != 0) {
            $this->dealMemberAddress();
        }

        try{
            //更新订单
            $this->dealOrder();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并订单出错');
            \Log::info($exception->getMessage());
        }

        try{
            //更新会员卡
            $this->dealCard();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码会员卡出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新优惠券
            $this->dealCoupon();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并优惠券出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新拼团信息
            $this->dealGroup();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并拼团出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新地址信息
            $this->dealMemberAddress();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并地址出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新积分日志
            $this->dealPoint();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并积分日志出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新充值余额日志
            $this->balanceLog();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并充值日志出错');
            \Log::info($exception->getMessage());
        }
        try{
            //更新大转盘信息
            $this->wheel();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并大转盘出错');
            \Log::info($exception->getMessage());
        }

        try{
            //合并砸金蛋记录
            $this->egg();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并砸金蛋出错');
            \Log::info($exception->getMessage());
        }

        try{
            //合并分销相关
            $this->distribute();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并分销提现等相关信息出错');
            \Log::info($exception->getMessage());
        }
        try{
            //合并享立减
            $this->share();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并分销享立减出错');
            \Log::info($exception->getMessage());
        }

        try{
            //合并收藏
            $this->favorite();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码合并收藏出错');
            \Log::info($exception->getMessage());
        }

        try{
            //合并调查留言
            $this->researchRecord();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码调查留言出错');
            \Log::info($exception->getMessage());
        }


        try{
            //拆红包
            $this->bonusRecord();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码拆红包出错');
            \Log::info($exception->getMessage());
        }

        try{
            //活动地址
            $this->awardAddress();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码活动地址出错');
            \Log::info($exception->getMessage());
        }
        try{
            //刮刮乐
            $this->scratch();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码刮刮乐出错');
            \Log::info($exception->getMessage());
        }
        try{
            //商品评价
            $this->productEvaluate();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码商品评价出错');
            \Log::info($exception->getMessage());
        }

        try{
            //合并预约表
            $this->usersBook();
        }catch (\Exception $exception){
            \Log::info('绑定手机号码预约出错');
            \Log::info($exception->getMessage());
        }




    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171218
     * @desc 处理小程序原来的地址
     * @param $mid
     */
    public function dealMemberAddress()
    {
        $memberAddressService = new MemberAddressService();
        list($data) = $memberAddressService->init()->where(['mid' => $this->smid])->getList(false);
        if ($data['data']) {
            foreach ($data['data'] as $val) {
                $memberAddressService->init()->where(['id' => $val['id']])->update(['umid' => $this->umid, 'mid' => $this->mid, 'type' => 0], false);
            }
        }

    }


    /**
     * 合并订单数据
     * @author 张永辉  2018年9月18日
     */
    public function dealOrder()
    {
        $orderService = new OrderService();
        $res          = $orderService->init()->model->where('mid', $this->smid)->get(['id'])->toArray();
        $odData       = [
            'mid'  => $this->mid,
            'umid' => $this->umid,
        ];
        foreach ($res as $val) {
            $odData['id'] = $val['id'];
            $orderService->init()->where(['id' => $val['id']])->update($odData, false);
        }
        //订单日志表
        $orderLogService = new OrderLogService();
        $logData = $orderService->init()->model->where('mid',$this->smid)->get(['id'])->toArray();
        foreach ($logData as $val){
            $orderLogService->init()->where(['id'=>$val['id']])->update(['mid'=>$this->mid],false);
        }
        //订单退款信息
        $orderRefundService = new OrderRefundService();
        $refundData = $orderRefundService->init()->model->where('mid',$this->smid)->get(['id'])->toArray();
        foreach ($refundData as $val){
            $orderRefundService->init()->where(['id'=>$val['id']])->update(['mid'=>$this->mid],false);
        }

        $refundMessageService = new OrderRefundMessageService();
        $messageData = $refundMessageService->init()->model->where('mid',$this->smid)->get(['id'])->toArray();
        foreach ($messageData as $val){
            $refundMessageService->init()->where(['id'=>$val['id']])->update(['mid'=>$this->mid],false);
        }

        //自提订单
        $orderZiTiService = new OrderZitiService();
        $orderZiTiService->model->where('mid',$this->smid)->update(['mid'=>$this->mid]);

    }


    /**
     * 合并会员卡
     * @author 张永辉  2018年9月18日
     */
    public function dealCard()
    {
        $recordService = new MemberCardRecordService();
        $res           = $recordService->init()->model->where('mid', $this->smid)->get(['id', 'card_id', 'in_card_at'])->toArray();

        $recordData = $this->dealKey($res, 'card_id');
        $cardIds    = array_column($recordData, 'card_id');
        if ($cardIds) {
            $nowMemberCard = $recordService->init()->model->where('mid', $this->mid)->whereIn('card_id', $cardIds)->get(['id', 'card_id', 'in_card_at'])->toArray();
            foreach ($nowMemberCard as $val) {
                if (strtotime($val['in_card_at']) < strtotime($recordData[$val['card_id']]['in_card_at'])) {
                    $recordService->init()->model->where('id', $val['id'])->delete();
                } else {
                    unset($recordData[$val['card_id']]['in_card_at']);
                }
            }
        }
        foreach ($recordData as $val) {
            $recordService->init()->where(['id' => $val['id']])->update(['id' => $val['id'], 'mid' => $this->mid], false);
        }
    }

    /**
     *  处理key值
     * @param $data
     * @param $key
     * @return array
     * @author 张永辉 2018年9月19日
     */
    function dealKey($data, $key)
    {
        $result = [];
        foreach ($data as $val) {
            $result[$val[$key]] = $val;
        }
        return $result;
    }


    /**
     * 合并优惠券
     * @author 张永辉  2018年9月18日
     */
    public function dealCoupon()
    {
        //更新优惠券信息
        $couponService = new CouponLogService();
        $couponData    = $couponService->model->where('mid', $this->smid)->get(['id'])->toArray();
        foreach ($couponData as $val) {
            $couponService->update($val['id'], ['mid' => $this->mid]);
        }
    }

    /**
     * 合并积分记录
     * @author 张永辉  2018年9月18日
     */
    public function dealPoint()
    {
        $pointService = new PointRecordService();
        $pointData    = $pointService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids          = array_column($pointData, 'id');
        if ($ids) {
            $pointService->batchUpdate($ids, ['mid' => $this->mid]);
        }
    }

    /**
     * 合并拼团信息
     * @author 张永辉  2018年9月18日
     */
    public function dealGroup()
    {
        $goupsDetailService = new GroupsDetailService();
        $detailData         = $goupsDetailService->model->where('member_id', $this->smid)->get(['id'])->toArray();
        $ids                = array_column($detailData, 'id');
        if ($ids) {
            $goupsDetailService->batchUpdate($ids, ['member_id' => $this->mid]);
        }
    }

    /**
     * 合并大转盘数据
     * @author 张永辉  2018年9月18日
     */
    public function wheel()
    {
        $wheelLogService = new ActivityWheelLogService();
        $logData         = $wheelLogService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids             = array_column($logData, 'id');
        if ($logData) {
            $wheelLogService->batchUpdate($ids, ['mid' => $this->mid]);
        }
        //中奖记录
        $wheelWinService = new ActivityWheelWinService();
        $winData         = $wheelWinService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids             = array_column($logData, 'id');
        if ($winData) {
            $wheelWinService->batchUpdate($ids, ['mid' => $this->mid]);
        }

    }

    /**
     * 同步充值余额日志
     * @author 张永辉  2018年9月18日
     */
    public function balanceLog()
    {
        $balanceLogService = new BalanceLogService();
        $balanceLogService->model->where('mid', $this->smid)->update(['mid'=>$this->mid]);
    }


    /**
     * 同步充值余额日志
     * @author 张永辉  2018年9月18日
     */
    public function egg()
    {
        $eggMemberService = new EggMemberService();
        $eggLogData       = $eggMemberService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids              = array_column($eggLogData, 'id');
        if ($eggLogData) {
            $eggMemberService->batchUpdate($ids, ['mid' => $this->mid]);
        }
    }


    /**
     * 分销信息合并
     * @author 张永辉 2018年9月18日
     */
    public function distribute()
    {
        //分销佣金收入信息
        $incomeModel = new Income();
        $incomeModel->where('mid', $this->smid)->update(['mid' => $this->mid]);
        $incomeModel->where('omid', $this->smid)->update(['omid' => $this->mid]);
        //提现信息
        $cashLogModel = new CashLog();
        $cashLogModel->where('mid', $this->smid)->update(['mid' => $this->mid]);
        //提现账号
        $distributeBank = new DistributeBank();
        $distributeBank->where('mid', $this->smid)->update(['mid' => $this->mid]);
        //合并提现订单
        CompanyPayOrder::where('mid',$this->smid)->update(['mid'=>$this->mid]);

    }


    /**
     * 合并享立减相关信息
     * @author 张永辉 2018年9月20日
     */
    public function share()
    {
        $liShareLogServce = new LiShareLogService();
        $logData          = $liShareLogServce->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids              = array_column($logData, 'id');
        if ($logData) {
            $liShareLogServce->batchUpdate($ids, ['mid' => $this->mid]);
        }
        //参与享立减记录表
        $shareRecodeServce = new ShareEventRecordService();
        $shareRecodeServce->model->where('source_id', $this->smid)->update(['source_id' => $this->mid]);
        $shareRecodeServce->model->where('actor_id', $this->smid)->update(['actor_id' => $this->mid]);
        //享立减分享记录
        $shareEventShareService = new ShareEventShareService();
        $shareData              = $shareEventShareService->model->where('share_id', $this->smid)->get(['id'])->toArray();
        $ids                    = array_column($shareData, 'id');
        if ($ids) {
            $shareEventShareService->batchUpdate($ids, ['share_id' => $this->mid]);
        }
    }


    /**
     * 合并收藏信息
     * @author 张永辉 2018年9月20日
     */
    public function favorite()
    {
        $favoriteServce = new FavoriteService();
        $favoriteData   = $favoriteServce->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids            = array_column($favoriteData, 'id');
        if ($ids) {
            $favoriteServce->batchUpdate($ids, ['share_id' => $this->mid]);
        }
    }


    /**
     * 合并调查留言
     * @author 张永辉 2018年9月20日
     */
    public function researchRecord()
    {
        $researchRecordServcie = new ResearchRecordService();
        $researchRecordServcie->model->where('mid', $this->smid)->update(['mid' => $this->mid]);
    }

    /**
     * 合并活动地址表
     * @author 张永辉 2018年9月20日
     */
    public function awardAddress()
    {
        $awardAddress = new ActivityAwardAddressService();
        $awardAddress->model->where('mid', $this->smid)->update(['mid' => $this->mid]);
    }

    /**
     * 合并猜红包
     * @author 张永辉 2018年9月20日
     */
    public function bonusRecord()
    {
        $bonusRecordService = new BonusRecordService();
        $bonusRecordService->model->where('mid', $this->smid)->update(['mid' => $this->mid]);
    }

    /**
     * 合并刮刮乐
     * @author 张永辉 2018年9月20日
     */
    public function scratch()
    {
        $scratchLogService = new ActivityScratchLogService();
        $logData           = $scratchLogService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids               = array_column($logData, 'id');
        if ($ids) {
            $scratchLogService->batchUpdate($ids, ['mid' => $this->mid]);
        }
        //合并中奖记录
        $scratchWinService = new ActivityScratchWinService();
        $winData           = $scratchWinService->model->where('mid', $this->smid)->get(['id'])->toArray();
        $ids               = array_column($winData, 'id');
        if ($ids) {
            $scratchWinService->batchUpdate($ids, ['mid' => $this->mid]);
        }
    }


    /**
     * 合并报名信息
     * @author 张永辉 2018年9月20日
     */
    public function enrollInfo()
    {
        $enrollInfoService = new EnrollInfoService();
        $enrollInfoData = $enrollInfoService->model->where('mid',$this->smid)->get(['id'])->toArray();
        $ids               = array_column($enrollInfoData, 'id');
        if ($ids) {
            $enrollInfoService->batchUpdate($ids, ['mid' => $this->mid]);
        }
    }

    /**
     * 商品评价相关
     * @author 张永辉
     */
    public function productEvaluate()
    {
        $evaluateService = new ProductEvaluateService();
        $evaluateService->init()->model->where('mid',$this->smid)->update(['mid'=>$this->mid]);
        $evaluateDetailService = new ProductEvaluateDetailService();
        $evaluateDetailService->init()->model->where('mid',$this->smid)->update(['mid'=>$this->mid]);
        $evaluateDetailService->init()->model->where('reply_id',$this->smid)->update(['reply_id'=>$this->mid]);
    }


    /**
     * 合并预约表
     * @author 张永辉 2018年9月20日
     */
    public function usersBook()
    {
        $usersBookService = new UsersBookService();
        $bookData = $usersBookService->model->where('mid',$this->smid)->get(['id'])->toArray();
        $ids               = array_column($bookData, 'id');
        if ($ids) {
            $usersBookService->batchUpdate($ids, ['mid' => $this->mid]);
        }
    }


}
