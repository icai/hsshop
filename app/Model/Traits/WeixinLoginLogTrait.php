<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/8/19
 * Time: 10:18
 * Desc: 店铺登录时间
 */

namespace App\Model\Traits;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


trait WeixinLoginLogTrait
{
    /**
     * cache 前缀
     * @var string
     */
    protected $cacheName = "shopLastLogin";


    /**
     *
     * 获取缓存key
     *
     * @param string $date 时间
     *
     * @return string
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年08月19日 10:52:02
     */
    protected function getCacheName(string $date)
    {

        return $this->cacheName . ':' . $date;
    }

    /**
     * 记录商家后台店铺最近访问时间
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年08月19日 10:34:01
     */
    public function recordLog()
    {
        // 要保存到redis的登录的值
        $now = Carbon::now()->timestamp;

        $date = Carbon::now()->toDateString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::HSET($this->getCacheName($date), $this->id, $now);

    }


    /**
     * 将redis中的数据同步到数据库
     *
     * @return bool
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年08月19日 11:45:02
     */
    public function syncLastAtToDataBase()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $key = $this->getCacheName($yesterday);
        $data = Redis::HGETALL($key);

        $insert = [];
        foreach ($data as $wid => $lastLoginAt) {
            $insert[] = [
                'wid' => $wid,
                'last_login_at' => $lastLoginAt
            ];
        }
        try {
            if ($insert && DB::connection("mysql_dc_log")->table("shop_login_log")->insert($insert)) {
                Redis::del($key);
            }

        } catch (\Exception $exception) {
            Log::info("同步店铺最近访问时间错误：" . $exception->getMessage());
            return false;
        }

        return true;

    }
}