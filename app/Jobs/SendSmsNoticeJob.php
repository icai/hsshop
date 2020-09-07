<?php
/**
 * @author 吴晓平 <2019.05.05>
 * 官网申请体验或代理发送短信通知
 */
namespace App\Jobs;

use App\Lib\SendSmsNoticeHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsNoticeJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;



    private $mobile;
    private $data;
    private $type;


    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($mobile, $data, $type = 1)
    {
        // 修改参数为单个手机号
        $this->mobile = $mobile;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(SendSmsNoticeHandler $sender)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }
        $sender->sendNotice($this->mobile, $this->data, $this->type);
    }


}
