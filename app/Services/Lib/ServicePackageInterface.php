<?php 

namespace App\Services\Lib;

/**
 * 服务包接口
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月31日 01:53:25
 */
interface ServicePackageInterface
{
    /**
     * 设置特征
     * @param  instance $instance
     * @return $this
     */
    public function setTrait($instance);

    /**
     * 返回对应的数据表中的所有字段数组
     * 
     * @return array
     */
    public function getFieldAll();

    /**
     * 返回对应的ORM中的所有关联关系
     * 
     * @return array
     */
    public function getWithAll();
}
