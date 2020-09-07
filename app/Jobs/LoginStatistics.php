<?php
/**
 * Created by PhpStorm.
 * User: 何书哲
 * Date: 2018/9/19
 * Desc: 登录统计
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;
use App\Lib\BLogger;

class LoginStatistics implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;       //用户id
    protected $ip;        //IP地址
    protected $type;      //方式
    protected $createtime;//创建时间

    public $connection;
    public $tries = 3;
    public $timeout = 60;

    /**
     * 创建任务实例
     * @param $uid 用户id
     * @param $ip IP地址
     * @param $type 类型
     * @return void
     */
    public function __construct($uid, $ip, $type)
    {
        $this->uid = $uid;
        $this->ip = $ip;
        $this->type = $type;
        $this->createtime = time();
        $this->connection = 'dc';
    }

    /**
     * 执行任务
     * @param null
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $url = config('app.log_center_url').'/hsy_user_login';
        $jobData = [
            'uid'          => $this->uid,
            'ip'           => $this->ip,
            'type'         => $this->type,
            'created_time' => $this->createtime
        ];
        $re = (new Bi())->request_post($url, json_encode($jobData, JSON_UNESCAPED_UNICODE));
        if ($re != 200 ) {
            \Log::error('登录日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error', 'hsy_user_login')->error($jobData);
        } else {
            \Log::info('登录日志发送成功');
        }
    }

}