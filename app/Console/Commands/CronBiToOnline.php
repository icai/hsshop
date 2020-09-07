<?php
namespace App\Console\Commands;
use DB;
use Illuminate\Console\Command;
use App\Lib\Redis\BiToOnline as BiToOnlineRedis;

class CronBiToOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronBiToOnline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '数据读取展示到线上';

    /**
     * Execute the console command.
     *
     * todo  
     * @return mixed
     */
    public function handle()
    {
        $now = time();
        $countTime = date('Ymd', ($now - (24*3600)) );

        $redis = new BiToOnlineRedis();

        $dbconnect = DB::connection('mysql_bi');
        $widData  = $dbconnect->select('SELECT  * FROM bi_wid_pageview WHERE counttime = '. $countTime);
        if (!empty($widData)) {
            $redis->widToOnline($widData);
        }

        $pageData =$dbconnect->select('SELECT  * FROM bi_page_pageview WHERE counttime = '. $countTime);
        if (!empty($pageData)) {
            $redis->pageToOnline($pageData);
        }
    }
}






