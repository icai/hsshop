<?php

namespace App\Jobs;

use App\Module\MemberCardModule;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use App\Services\WeixinService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Weixin\ShopService;

class CheckGrantCard implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;
    protected $mid;
    protected $wid;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid,$wid)
    {
        $this->mid = $mid;
        $this->wid = $wid;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月18日 发放会员卡通知
     */
    public function handle(MemberCardModule $cardModule,ShopService $shopService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $shopData = $shopService->getRowById($this->wid);
        if (empty($shopData)){
            \Log::info('按规则发放会员卡店铺错误'.$this->wid);
            return false;
        }
        $res = $cardModule->grantRuleMemberCard($this->mid,$this->wid);
        foreach ($res as $val){
            $data['memberCard'] = $val;
            $data['shop'] = $shopData;
            $data['mid'] = $this->mid;
            $data['wid'] = $this->wid;
            (new MessagePushModule($this->wid, MessagesPushService::GetMemberCard))->sendMsg($data);
            (new MessagePushModule($this->wid, MessagesPushService::GetMemberCard, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($data);
        }
    }
}
