<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/3/1
 * Time: 15:36
 */

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Module\XCXModule;
use App\Lib\BLogger;

class XCXRelease implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $wid=0;

    public function __construct($wid)
    {
        $this->wid = $wid;
    }

    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $storeId=$this->wid;
        //BLogger::getLogger('info')->info('小程序发布队列'.$storeId);
        if($storeId>0)
        {
            (new XCXModule())->autoRelease($storeId);
        }
    }
}