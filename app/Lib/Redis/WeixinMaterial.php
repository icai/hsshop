<?php
namespace App\Lib\Redis;
use Log;
use Redisx;

/**
 * 图文redis类（微信图文与高级图文）
 * @author 吴晓平 <2017.07.26>
 */
class WeixinMaterial extends RedisInterface
{

    protected $prefixKey = 'weixin_material';
    protected $timeOut   = 7200;

    public function __construct($key='') 
    {
        parent::__construct($key);
    }

    /**
     * [set 设置缓存]
     * @param [string] $value [设置的值]
     */
    public function set($value) 
    {
        $this->redis->SET($this->key, $value);
        $this->redis->EXPIRE($this->key, $this->timeOut);
    }

    public function get()
    {
        if(!$this->redis->EXISTS($this->key))
        {
            return false;
        }

        $value = $this->redis->GET($this->key);

        return $value;
    }

    public function exists()
    {
        if($this->redis->EXISTS($this->key))
        {
            return true;
        }
        return false;
    }

    public function del($id)
    {
        if ($this->redis->exists($this->key.$id)){
            return $this->redis->del($this->key.$id);
        }
        return true;
    }

    /**
     * [update 更新reids数据]
     * @param  [obj] $model [数据库相关表model类]
     * @param  [int] $wid   [店铺id]
     * 以字符串形式存到redis中
     * @return [type]        [description]
     */
    public function update($datas)
    {
        $value = json_encode($datas,JSON_UNESCAPED_UNICODE);
        $this->set($value);
    }

    /**
     * [更新hash类型redis]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateHashRow($id, $data)
    {
        if ( ! $this->redis->EXISTS($this->key . $id) ) {
            return true;
        }
        return $this->redis->hmset($this->key . $id, $data);
    }

}
?>
