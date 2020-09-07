<?php 

namespace App\Services\Lib;

/**
 * 服务插件接口
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月31日 01:53:25
 */
interface ServicePlugInterface
{
    /**
     * 设置特征
     * 
     * @param  instance $instance
     * @return $this
     */
    public function trait($instance);
}
