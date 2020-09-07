<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/7/31
 * Time: 15:05
 */

namespace App\Lib\Redis;


class openPermissionRedis extends RedisInterface
{
    protected $prefixKey = 'openPermission';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    public function addArr($data)
    {
        //主键
        $this->redis->LPUSH($this->key . "phone", $data['phone'] . "_" . $data['created_at']);
        $this->redis->HMSET($this->key . $data['phone'] . "_" . $data['created_at'], $data);
        return true;
    }


    /**
     * 获取所有内容
     * @return array
     * @author: 梅杰 20180731
     */
    public function getAll()
    {
        //先获取id list
        $idList = $this->redis->LRange($this->key . "phone", 0, -1);
        $data = [];
        foreach ($idList as $value) {
            $detail = $this->redis->HGETALL($this->key . $value);
            $data[] = [
                'phone'             => $detail['phone'],
                'smsLog'            => json_decode($detail['smsLog'], 1),
                'registerUserLog'   => json_decode($detail['registerUserLog'], 1),
                'createShopLog'     => json_decode($detail['createShopLog'], 1),
                'created_at'        => $detail['created_at']

            ];
        }
        return $data;
    }


}