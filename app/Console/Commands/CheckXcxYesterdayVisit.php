<?php

/**
 * 检查小程序统计是否有错误
 * @author 何书哲 2019年01月25日
 */

namespace App\Console\Commands;

use App\S\CorpMsgService;
use Illuminate\Console\Command;
use DB;

class CheckXcxYesterdayVisit extends Command
{
    protected $signature = 'CheckXcxYesterdayVisit';

    protected $description = '检查统计小程序昨日访问数据是否成功，否则发送企业微信通知';

    public function handle()
    {
        $date = date('Ymd', strtotime("-1 day"));
        $sql = 'SELECT COUNT(*) as count FROM ds_xcx_statistics_log WHERE start_date=? AND log LIKE ? ';
        $res = DB::select($sql, [$date, '%错误码:-1错误信息%']);
        if ($res[0]->count) {
            (new CorpMsgService())->sendMsg(['content' => '统计小程序昨日('.$date.')访问数据失败'], 1);
        }
    }
}