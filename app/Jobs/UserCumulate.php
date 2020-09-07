<?php
/**
 * Created by PhpStorm.
 * Author: wuxiaoping
 * Date: 2018/4/17
 * Time: 14:00
 * 微信公众号粉丝统计队列任务
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;
use App\S\Wechat\WeixinConfigSubService;
use App\Services\Wechat\ApiService;

class UserCumulate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $dates;
    protected $wid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid=0,$dates=[])
    {
        $this->wid = $wid;
        $this->dates = $dates;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $bi = new Bi();
        $apiService = new ApiService();
        if ($this->wid) { //指定店铺的情况下
            $result = $apiService->getUserCumulate($this->wid,$this->dates);
            if (!isset($result['errcode'])) {
                $rs = $bi->storeUserCumulate($this->wid,$result);
                \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                \Log::info($rs);
            }
        }else { //未指定店铺的情况
            $weixinConfigSubService = new WeixinConfigSubService();
            $list = $weixinConfigSubService->getAllList();
            if ($list) {
                foreach ($list as $key => $value) {
                    $result = $apiService->getUserCumulate($value['wid'],$this->dates);
                    if (!isset($result['errcode'])) {
                        $rs = $bi->storeUserCumulate($this->wid,$result);
                        \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                        \Log::info($rs);
                    }
                }
            }
        }


    }
}
