<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/15
 * Time: 11:51
 */

namespace App\Lib\Redis;


class FormIdRedis extends RedisInterface
{
    protected $prefixKey = 'UserFormId';
    protected $timeOut   = 604800; //七天有效期

    public function __construct($key = "")
    {
        parent::__construct($key);
    }







}