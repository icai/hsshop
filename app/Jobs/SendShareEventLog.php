<?php
/**
 * 享立减活动数据发送到数据中心日志服务器
 * auhtor 何书哲 2018年8月6日
 */

namespace App\Jobs;

use App\Lib\BLogger;
use App\S\Foundation\Bi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendShareEventLog implements ShouldQueue{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $fields = ['wid','actor_id','source_id','share_event_id','created_at','source'];


    /**
     * 创建发送享立减任务实例
     * @param $data 传入日志中心的字段
     * @author 何书哲 2018年8月6日
     */
    public function __construct($data) {
        $this->_dealData($data);
        $this->connection = 'dc';
    }

    private function _dealData($data) {
        foreach ($this->fields as $v) {
            $this->data[$v] = $data[$v];
        }
    }

    /**
     * 执行队列任务
     * @$param null
     * @return void
     * @author 何书哲 2018年8月6日
     */
    public function handle() {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $url = config('app.log_center_url').'/hsy_share_event';
        $res = (new Bi())->request_post($url, json_encode($this->data, JSON_UNESCAPED_UNICODE));
        if ( $res != 200 ) {
            //将日志输出存放到其他文件中去
            Log::error('享立减日志发送失败,返回状态码：'.$res);
            BLogger::getDCLogger('error','hsy_share_event')->error($this->data);
        }else {
            Log::info('享立减日志发送成功');
        }
    }

}