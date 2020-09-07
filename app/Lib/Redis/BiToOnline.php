<?php
namespace App\Lib\Redis;

/**
 * 快递Redis
 */
class BiToOnline extends RedisInterface
{
    protected $prefixKey = 'bionline';
    protected $timeOut = 86400;

    public function __construct($key = "")
    {
        parent::__construct($key);
    }

    protected function setExpireTime()
    {
        $expireTime =  strtotime(date('Ymd')) + (25 * 3600) - time();
        return $expireTime; 
    }

    public function widToOnline($data)
    {
        $timeOut = $this->setExpireTime();

        return $this->redis->pipeline(function ($pipe) use ($data, $timeOut) {
            foreach ($data as $d) {
                $insert = [
                    'viewpv'      =>  $d->viewpv,
                    'viewuv'      =>  $d->viewuv,
                    'pagepv'      =>  $d->pagepv,
                    'pageuv'      =>  $d->pageuv,
                    'productpv'   =>  $d->productpv,
                    'productuv'   =>  $d->productuv,
                ];
                $key = $this->key .'wid'. '_' .$d->wid;
                $pipe->HMSET($key, $insert);
                $pipe->EXPIRE($key, $timeOut);

            }
        });
    }

    public function pageToOnline($data)
    {
        $timeOut = $this->setExpireTime();
        return $this->redis->pipeline(function ($pipe) use ($data, $timeOut) {
            foreach ($data as $d) {
                $insert = [
                    'viewpv'   =>  $d->viewpv,
                    'viewuv'   =>  $d->viewuv
                ];
                $key = $this->key .'page'. '_' .$d->type . '_' .$d->type_id . '_' .$d->wid;
                $pipe->HMSET($key, $insert);
                $pipe->EXPIRE($key, $timeOut);
            }
        });
    }

    public function getWidBi($wid)
    {
        $key = $this->key .'wid'. '_' .$wid;
        return $this->redis->HGETALL($key);
    }

    public function getPageBi($idArr, $wid, $type)
    {
        return $this->redis->pipeline(function ($pipe) use ($idArr, $wid, $type) {
            foreach ($idArr as $id) {
                $key = $this->key .'page'. '_' .$type . '_' .$id . '_' .$wid;
                $pipe->HGETALL($key);
            }
        });
    }
}