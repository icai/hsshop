<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/4/26
 * Time: 18:05
 * Desc: 数据统计余额充值
 */
namespace App\Jobs;

use App\Lib\BLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;

class SendRechargeBalanceLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $wid;//店铺id
    protected $mid;//用户id
    protected $money;//余额充值金额
    protected $type;//1:充值 2：消耗
    protected $pay_way;//支付方式
    protected $createtime;//充值时间
    protected $fields = ['wid','mid','money','type','pay_way','createtime'];

    public $connection;
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid, $mid, $money, $type, $pay_way, $createtime)
    {
        $this->wid = $wid;
        $this->mid = $mid;
        $this->money = $money;
        $this->type = $type;
        $this->pay_way = $pay_way;
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

        $url = config('app.log_center_url').'/hsy_re_balance';
        $jobData = [
            'wid' => $this->wid,
            'mid' => $this->mid,
            'money' => $this->money,
            'type' => $this->type,
            'pay_way' => $this->pay_way,
            'createtime' => $this->createtime
        ];
        foreach ($jobData as $key => $val) {
            if (!in_array($key, $this->fields)) {
                \Log::info('余额充值数据不完整');
                \Log::Info(json_encode($jobData, JSON_UNESCAPED_UNICODE));
                return;
            }
        }
        $re = (new Bi())->request_post($url,json_encode($jobData, JSON_UNESCAPED_UNICODE));
        if ( $re !=200 ) {
            //将日志输出存放到其他文件中去
            \Log::error('余额充值日志发送失败,返回状态码：'.$re);
            BLogger::getDCLogger('error','hsy_re_balance')->error($jobData);
        }else {
            \Log::info('余额充值发送成功');
        }
    }

}