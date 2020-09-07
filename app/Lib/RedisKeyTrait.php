<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 8/6/17
 * Time: 9:03 PM
 */

namespace App\Lib;


trait RedisKeyTrait
{
    public function getRedisKey()
    {
        return $this->redisKey;
    }
}