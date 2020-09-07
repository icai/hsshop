<?php
namespace App\Services;

use App\Model\ServicePartner;
use RedisPagination;
use Validator;

/**
 * 权限设置
 */
class ServicePartnerService extends Service
{
    /**
     * 构造方法
     *
     *
     * @return void
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id' , 'wid', 'service_title', 'telephone', 'service_addr', 'service_aptitude','created_at','delete_at','is_delete'];

        /* 设置闭包标识 */
       // $this->closure('capital');
        // 所有关联关系
        //$this->withAll = ['userInfo'];
    }

    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        $this->initialize(new ServicePartner(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /* 添加 管理员 */

    public function insert($data){
        /*插入数据 */
        $insertGetId= $this->model->insertGetId($data);
        $insertDatas = array();
        /*redis插入 */
        if($insertGetId) {
            $data['id'] = $insertGetId;
            foreach ($data as $k=>$d) {
                $insertDatas[$insertGetId][$k] = $d;
            }
            $insertDatas[$insertGetId] = $data;
            /*redis插入 */
            RedisPagination::save($insertDatas);
            return true;
        }
        return false;
    }

}