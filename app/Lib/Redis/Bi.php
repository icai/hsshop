<?php
namespace App\Lib\Redis;

/**
 * 快递Redis
 * @update 吴晓平 2018年6月28日 添加保存设置hash类型redis
 */
class Bi extends RedisInterface
{
    protected $prefixKey = 'bi';
    protected $timeOut = 2592000;

    public function __construct($key = "")
    {
        parent::__construct($key, 'bi');
    }

    public function insert($val)
    {
        return $this->redis->RPUSH($this->key, $val);
    }

    public function getBiData()
    {
        return $this->redis->RPOP($this->key);
    }

    /**
     * 数据统计根据wid保存redis数据
     * @param array $data [description]
     * @author 吴晓平 2018年6月28日
     */
    public function setXcxArrJson($data=[])
    {
        return $this->redis->SET('xcxstc--'.date('Ymd',strtotime('-1 days')),$data);
    }

    /**
     * 数据统计根据wid获取redis数据
     * @param array $widArr [description]
     * @author 吴晓平 2018年6月28日
     */
    public function getXcxArr()
    {   
        return $this->redis->GET('xcxstc--'.date('Ymd',strtotime('-1 days')));
    }

    public function delXcxData()
    {
        if ($this->redis->EXISTS('xcxstc--'.date('Ymd',strtotime('-1 days')))) {
            return $this->redis->Del('xcxstc--'.date('Ymd',strtotime('-1 days')));
        }
        
    }
}