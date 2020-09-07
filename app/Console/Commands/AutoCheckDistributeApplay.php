<?php

namespace App\Console\Commands;

use App\Module\MessagePushModule;
use App\S\Distribute\DistributeApplayLogService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Weixin\ShopService;
use Illuminate\Console\Command;

class AutoCheckDistributeApplay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoCheckDistributeApplay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动审核分销申请';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(DistributeApplayLogService $distributeApplayLogService, ShopService $shopService,MemberService $memberService)
    {
        //
        $time = date('Y-m-d H:i:s', time() - 1800);
        $data = $distributeApplayLogService->getList(['created_at' => ['<', $time], 'status' => '0']);
        if (!$data) {
            return true;
        }
        $logData = [];
        foreach ($data as $val) {
            $logData[$val['wid']][] = $val;
        }
        $wids     = array_keys($logData);
        $res      = $shopService->getListById($wids);
        $shopData = [];
        foreach ($res as $val) {
            $shopData[$val['id']] = $val;
        }
        foreach ($logData as $key => $val) {
            if (empty($shopData[$key]) || $shopData[$key]['is_auto_check'] != '1') {
                continue;
            }
            $ids = array_column($val, 'id');
            $mids = array_column($val,'mid');
            $distributeApplayLogService->batchUpdate($ids, ['status' => 1]);
            $memberService->batchUpdate($mids,['is_distribute'=>1]);

            //何书哲 2018年11月09日 自动成为分销客发送消息通知
            foreach ($mids as $mid) {
                (new MessagePushModule($key, MessagesPushService::BecomePromoter))->sendMsg(['mid'=>$mid]);
                (new MessagePushModule($key, MessagesPushService::BecomePromoter, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid'=>$mid]);

            }

        }

    }
}
