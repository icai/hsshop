<?php

namespace App\Services;


use App\Model\MsgpushTotalStat;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;

class MsgpushTotalStatService extends Service
{
    public function __construct(){

    }

    /*
     * 查询当前店铺短信统计量
     */
    public function getMsgpushTotalStat($params = array(),$wid = 0){
        $perPage = config('database')['perPage'];  # 获取每页条数
        #从redis 里面取出数据
        $redisKey = 'msgpush_total_stat:wid:'.$wid; #从redis中取出
        $pageList = array();

        $list = Redisx::HGETALL($redisKey);
        $count = $list ? 1 : 0;

        # 多余一个参数 或者 没取到 任何东西时 使用 缓存
       if ((isset($params['page']) && count($params) > 1)  || !$count) {
            $fields = ['id','wid','total_send','total_achieve','total_fee','total_left','created_at','updated_at'];
            $where['wid'] = $wid;
            $query = MsgpushTotalStat::select($fields)->wheres($where)->order('updated_at desc');
            $list = $query->first()->toArray();
            /* 数据不为空则写入redis */
            if (!empty($list)) {
                /* 查询所有商品信息 */
                $where = array();
                $where['wid'] = $wid;
                $redisList = MsgpushTotalStat::select($fields)->wheres($where)->order('updated_at desc')->first()->toArray();
                /* 写入redis */
                # 在写如redis key 之前清空里面已经存在的 key
                Redisx::DEL($redisKey);
                Redisx::HMSET($redisKey , $redisList);
            }
        }
        return array('list'=>$list);
    }

}