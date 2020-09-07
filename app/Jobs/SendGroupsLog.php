<?php
/**
 * 拼团活动开团参团数据和已成团数据发送到数据中心日志服务器
 * create 何书哲 2018年7月6日
 */

namespace App\Jobs;

use App\Lib\BLogger;
use App\S\Foundation\Bi;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsService;
use App\Services\Order\OrderService;
use App\Services\Order\OrderDetailService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendGroupsLog implements ShouldQueue{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $type ;
    private $groupsDetailId;


    /**
     * 创建发送拼团任务实例
     * @param $type 方式：1=>开团 2参团 3=>成团
     * @param $data 传入日志中心的字段
     * @return 对象任务实例
     * @create 何书哲 2018年7月8日
     * @update 梅杰 队列链接设置
     */
    public function __construct($groupsDetailId,$type) {
        $this->groupsDetailId = $groupsDetailId;
        $this->type = $type;
        $this->connection = 'dc';
    }

    /**
     * 执行队列任务
     * @$param null
     * @return void
     * @create 何书哲 2018年7月8日
     * @update 张永辉 2018年7月16日
     */
    public function handle() {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $groupsDetailService = new GroupsDetailService();
        $groupsService = new GroupsService();
        $detailData = $groupsDetailService->getRowById($this->groupsDetailId);
        $groupsData = $groupsService->getRowById($detailData['groups_id']??'0');
        if (!$detailData  || !$groupsData){
            Log::info('团购详情id错误');
            return ;
        }

        if (in_array($this->type,['1','2'])) {
            $this->addGroups($groupsData,$detailData);
        } elseif ($this->type == '3') {
            $this->completeGroups($groupsData);
        }
    }


    /**
     * 开团参团发送日志
     * @param $groupsData
     * @param $detailData
     * @author 张永辉 2018年7月16日
     */
    public function addGroups($groupsData,$detailData)
    {
        $url = config('app.log_center_url').'/hsy_groups_meeting';
        $sendData = [
            'wid'               => $groupsData['wid'],
            'oid'               => $detailData['oid'],
            'rule_id'           => $groupsData['rule_id'],
            'group_id'          => $groupsData['id'],
            'mid'                => $detailData['member_id'],
            'discount_money'   => $this->computeDiscount($detailData['oid']),
            'type'              => $this->type,
            'created_at'       => strtotime($detailData['created_at']),
        ];

        $res = (new Bi())->request_post($url, json_encode($sendData, JSON_UNESCAPED_UNICODE));
        if ( $res != 200 ) {
            //将日志输出存放到其他文件中去
            Log::error('拼团日志发送失败,返回状态码：'.$res);
            BLogger::getDCLogger('error','hsy_groups_meeting')->error($sendData);
        }else {
            Log::info('拼团日志发送成功');
        }
    }


    /**
     * 成团发送日志
     * @param $groupsData
     * @param $detailData
     * @author 张永辉 2018年7月16日
     */
    public function completeGroups($groupsData){
        $url = config('app.log_center_url').'/hsy_groups_complete';
        $sendData = [
            'group_id'          => $groupsData['id'],
            'complete_time'    => strtotime($groupsData['complete_time']),
        ];
        $res = (new Bi())->request_post($url, json_encode($sendData, JSON_UNESCAPED_UNICODE));
        if ( $res != 200 ) {
            //将日志输出存放到其他文件中去
            Log::error('已成团日志发送失败,返回状态码：'.$res);
            BLogger::getDCLogger('error','hsy_groups_complete')->error($sendData);
        }else {
            Log::info('已成团日志发送成功');
        }
    }





    /**
     *计算优惠
     * @param $oid
     * @author 张永辉  2018年7月16日
     */
    public function computeDiscount($oid)
    {
        $orderService = new OrderService();
        $orderDetailService = new OrderDetailService();
        $orderData = $orderService->init()->model->select(['pay_price'])->find($oid);
        $detailData = $orderDetailService->init()->model->where(['oid'=>$oid])->get(['price','num'])->toArray();
        $sum = 0;
        foreach ($detailData as $val){
            $sum = $sum+($val['price']*$val['num']);
        }
        $discount = $sum-$orderData->pay_price??0;
        return $discount;
    }


}