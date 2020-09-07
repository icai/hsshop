<?php
/**
 * @author wuxiaoping <2018.03.20>
 * @desc  数据统计队列任务
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;

class RepeatStatisticsXCXData implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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

        if ($this->data) {
            $bi = new Bi();
            $res = $bi->commitStatisticsXCXData($this->data);
            \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
            \Log::info($res);
        }
    }
}
