<?php

namespace App\Jobs;

use App\Lib\BLogger;
use App\S\Foundation\Bi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendDcLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $type; //日志类型 1:hsy_new_user
    private $log;  //接收到的数据


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$type)
    {
        //
        $this->type = $type;
        $this->log  = $data;
        $this->connection = 'dc';
        $this->queue = 'sendDcLog';
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

        switch ($this->type) {
            case 1:
                $this->HsyNewMember();
                break;
            default:
                return false;
                break;
        }

    }


    private function HsyNewMember()
    {
        $url = config('app.log_center_url').'/hsy_new_user';
        //替换下，数据中心那边切割有问题
        $postData = str_replace('/','-',$this->log);
        $re = (new Bi())->request_post($url,json_encode($postData));
        if ($re !=200 ) {
            //将日志输出存放到其他文件中去
            \Log::error('hsy_new_user日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error','hsy_new_user')->error($this->log);
        }else {
            //删除新用户标识
            \Log::info('hsy_new_user发送成功');
        }
    }



}
