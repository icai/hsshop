<?php
/**
 * create by 吴晓平2018.11.27
 * desc:主要用于总后台同步案例生成小程序二维码然后更新到案例表
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Weixin\ShopService;

class CreateCaseXcxQrocde implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $tries = 3; //失败连接次数
    protected $timeout = 60;
    protected $wid;  // 店铺id

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid)
    {
        $this->wid = $wid;

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

        (new ShopService())->updateXcxQrcode($this->wid);
    }
}
