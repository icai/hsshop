<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/6/14
 * Time: 15:54
 */

namespace App\Lib\Redis;

class WXXCXCustomFooterBarRedis extends RedisInterface{

    protected $prefixKey = 'wxxcxcustomfooterbar';
    protected $timeOut   = 7200;

    public function __construct($key='')
    {
        parent::__construct($key);
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }


}