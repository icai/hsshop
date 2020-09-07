<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/26
 * Time: 18:05
 * Desc: 数据统计分销提现
 */
namespace App\Jobs;

use App\Lib\BLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;

class DataStatisticsDistributeWithdrawal implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $wid;//店铺id
    protected $mid;//提现人
    protected $money;//提现金额
    protected $type;//提现方式
    protected $source;//提现来源
    protected $createtime;//提现时间
    protected $fields = ['wid','mid','money','type','source','createtime'];

    public $connection;
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid, $mid, $money, $type, $source, $createtime)
    {
        $this->wid = $wid;
        $this->mid = $mid;
        $this->money = $money;
        $this->type = $type;
        $this->source = $source;
        $this->createtime = $createtime;
        $this->connection = 'dc';
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

        $url = config('app.log_center_url').'/hsy_ds_draw';
        $jobData = [
            'wid' => $this->wid,
            'mid' => $this->mid,
            'money' => $this->money,
            'type' => $this->type,
            'source' => $this->source,
            'createtime' => $this->createtime
        ];
        foreach ($jobData as $key => $val) {
            if (!in_array($key, $this->fields)) {
                \Log::info('分销提现数据不完整');
                \Log::Info(json_encode($jobData, JSON_UNESCAPED_UNICODE));
                return;
            }
        }
        $re = (new Bi())->request_post($url,json_encode($jobData, JSON_UNESCAPED_UNICODE));
        if ( $re !=200 ) {
            //将日志输出存放到其他文件中去
            \Log::error('分销提现日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error','hsy_ds_draw')->error($jobData);
        }else {
            \Log::info('分销提现发送成功');
        }
    }

}