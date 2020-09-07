<?php
namespace App\Console\Commands;
use DB;
use Illuminate\Console\Command;

class BiPageViewCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //php artisan BiPageViewCount --timeSet=1
    protected $signature = 'BiPageViewCount {--timeSet=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计汇总';

    /**
     * Execute the console command.
     *
     * todo  
     * @return mixed
     */
    public function handle()
    {
        //protected $signature = 'test {param1} {--param2=}';
        //$param1 = $this->argument('time');
        //$param2 = $this->option('param2');
        //$this->info($param2);
        //$this->info($param1);

        $now = time();
        $timeSet = $this->option('timeSet');
        
        $countTime = date('Ymd', $now - (24*3600) );

        if ($timeSet == 1)
            $countTime = date('Ymd', $now - (24*3600*2));

        if ($timeSet > 1)
            $countTime = $timeSet;

        //取店铺数量
        $dbconnect = DB::connection('mysql_bi');
        $widData = $dbconnect->select('SELECT  DISTINCT wid FROM bi_pageview_'.$countTime .' WHERE appid = 1');

        if (empty($widData)) {
            echo $timeSet."无数据";
            exit();
        }

        //店铺总浏览数  总访客数
        foreach ($widData as $wid) {
            $widpv = $widuv = $pagepv = $pageuv = $productpv = $productuv = 0;
            $widpvData = $dbconnect->select(
                'SELECT COUNT(*) as widpv 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid );

            $widpv = $widpvData[0]->widpv;

            $widuvData = $dbconnect->select(
                'SELECT DISTINCT bikey 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid );

            if (!empty($widuvData)) {
                $widuv = count($widuvData);
            }

            $pagepvData = $dbconnect->select(
                'SELECT COUNT(*) as pagepv 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid .' AND type = 1 ' );

            $pagepv = $pagepvData[0]->pagepv;

            $pageuvData = $dbconnect->select(
                'SELECT DISTINCT bikey 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid .' AND type = 1 ' );

            if (!empty($pageuvData)) {
                $pageuv = count($pageuvData);
            }

            $productpvData = $dbconnect->select(
                'SELECT COUNT(*) as productpv 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid .' AND type = 2 ' );

            $productpv = $productpvData[0]->productpv;

            $productuvData = $dbconnect->select(
                'SELECT DISTINCT bikey 
                 FROM bi_pageview_'. $countTime .' 
                 WHERE appid = 1 AND wid = '. $wid->wid .' AND type = 2 ' );

            if (!empty($productuvData)) {
                $productuv = count($productuvData);
            }

            $dbconnect->update(
                'REPLACE INTO bi_wid_pageview 
                (wid, viewpv, viewuv, pagepv, pageuv, productpv, productuv, counttime) 
                VALUES 
                ('. $wid->wid .',' .$widpv. ',' .$widuv. ','.$pagepv. ',' .$pageuv. ','. $productpv. ','. $productuv. ',' .$countTime. ')');

            echo $wid->wid.";";
        }

        //微页面 浏览UV / PV
        //微商品浏览UV / PV
        $findArr = [
            0   =>  [
                'type' => 1,
            ],
            1   =>  [
                'type' => 2,
            ]
        ];
        foreach ($findArr as $key => $value) {
            foreach ($widData as $wid) {
                $pageData = [];
                $pageData = $dbconnect->select(
                    'SELECT  DISTINCT p1 
                     FROM bi_pageview_'. $countTime .' 
                     WHERE appid = 1 AND wid = ' .$wid->wid. ' AND type =  '. $findArr[$key]['type']);

                if (empty($pageData)) continue;

                foreach ($pageData as  $page) {
                    $pagepv     = $pageuv = 0;

                    $pagepvData = $dbconnect->select(
                        'SELECT COUNT(*) as pagepv 
                         FROM bi_pageview_'. $countTime .' 
                         WHERE appid = 1 AND type = ' .$findArr[$key]['type']. ' AND  p1 = '. $page->p1 .' 
                         LIMIT 1' );

                    $pagepv = $pagepvData[0]->pagepv;

                    $pageuvData = $dbconnect->select(
                        'SELECT DISTINCT bikey 
                         FROM bi_pageview_'. $countTime .' 
                         WHERE appid = 1 AND wid = ' .$wid->wid. ' AND type = ' .$findArr[$key]['type']. ' AND  p1 = '. $page->p1 );

                    if (!empty($pageuvData)) {
                        $pageuv = count($pageuvData);
                    }
                    $dbconnect->update(
                        'REPLACE INTO bi_page_pageview 
                        (wid, viewpv, viewuv, counttime, type, type_id) 
                        VALUES 
                        ('. $wid->wid .',' .$pagepv. ',' .$pageuv. ',' .$countTime. ', ' .$findArr[$key]['type']. ', ' .$page->p1. ')');
                }
            }
        }
    }
}






