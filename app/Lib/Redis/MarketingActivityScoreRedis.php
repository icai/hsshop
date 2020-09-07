<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/2
 * Time: 9:13
 */

namespace App\Lib\Redis;


class MarketingActivityScoreRedis extends RedisInterface
{
    protected $prefixKey = 'marketing_activity_score';
    protected $timeOut = 7200;

    public function __construct($key = '')
    {
        parent::__construct($key);
    }
}