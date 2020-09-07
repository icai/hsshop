<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/1/27
 * Time: 11:36
 */

namespace App\Module;


use App\Lib\Redis\RedisClient;
use CommonModule;
use Illuminate\Support\Str;

class CodeModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180127
     * @desc 存储code
     */
    public function setCode($value)
    {
        $request = app('request');
        $token = $request->input('token','1');
        if (!$token){
            return false;
        }
        $mid = CommonModule::getMidByToken($token);
        $wid = CommonModule::getWidByToken($token);
        if (empty($mid) || empty($wid)){
            return false;
        }
        $redis = (new RedisClient())->getRedisClient();
        $key = $this->getKey($wid,$mid);
        $redis->SET($key,$value);
        $redis->EXPIRE($key, 600);
        return true;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180127
     * @desc
     */

    public function getKey($wid,$mid)
    {
        return 'img_code:'.$wid.':'.$mid;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180127
     * @desc 检验验证码是否正确
     * @param $wid
     * @param $mid
     */
    public function check($wid,$mid,$code)
    {
        $key = $this->getKey($wid,$mid);
        $redis = (new RedisClient())->getRedisClient();
        $value = $redis->GET($key);
        $redis->DEL($key);
        $value = Str::lower($value);
        $code = Str::lower($code);

        if (empty($value) || empty($code)){
            return false;
        }
        if ($value == $code){
            return true;
        }else{
            return false;
        }
    }



}