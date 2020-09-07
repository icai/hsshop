<?php

namespace App\Services;

use App\Model\MsgpushDayStat;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;

class MsgpushDayStatService extends Service
{
    public function __construct(){
        
    }

    /*
     * @todo: 查询商品模板列表
     */
    public function getMsgpushDayStats($params = array(),$wid = 0,$needPage = true){
        $perPage = config('database')['perPage'];  # 获取每页条数

        #从redis 里面取出数据
        $redisKey = 'msgpush_day_stat:wid:' . $wid; #从redis中取出 商品为店铺wid的 所有商品
        $pageList = array();
        if(!$needPage){  #不需要分页的时候 直接取出所有的 列表
            $list = array();
            $idList = Redisx::LRANGE($redisKey,0,-1);
            if ( !empty($idList) ) {
                foreach ($idList as $key => $value) {
                    $lists[] = Redisx::HGETALL($redisKey . '|id:' . $value);
                }
                $list['data'] = $lists;
            }
            $count = $list ? 1 : 0;
        }else{
            #list($list, $pageList, $count) = RedisPagination::page($redisKey, $perPage);
            $list = [];$count = 0;
        }

        # 多余一个参数 或者 没取到 任何东西时 使用 缓存
        if ((isset($params['page']) && count($params) > 1)  || !$count) {

            $fields = ['id','wid','day_total_send','day_total_achieve','day_total_fee','achieve_source','year','month','day','created_at','updated_at'];

            $where['wid'] = $wid;
            $day = isset($params['day']) && $params['day'] ? $params['day'] : 0;
            $startday = isset($params['startday']) && $params['startday'] ? $params['startday'] : 0;
            $endday = isset($params['endday']) && $params['endday'] ? $params['endday'] : 0;
            if ($day){
                $endday = date('Ymd');
                $startday = date('Ymd',strtotime(date('Y-m-d')) - $day * 24 * 3600);
            }
            $query = MsgpushDayStat::select($fields);
            if($startday){
                $query = $query->where('day','>=',$startday);
            }
            if($endday){
                $query = $query->where('day','<=',$endday);
            }
            if($needPage){
                $query = $query->paginate($perPage)->appends($params);
                $pageList = $query->links();
            }else{
                $query = $query->get();
            }
            $list = $query->toArray();
            /* 数据不为空则写入redis */
            if (!empty($list['data'])) {
                /* 查询所有商品信息 */
                $where = array();
                $where['wid'] = $wid;
                $redisList = MsgpushDayStat::select($fields)->wheres($where)->order('updated_at DESC,id DESC')->get()->toArray();
                /* 写入redis */
                # 在写如redis key 之前清空里面已经存在的 key
                Redisx::DEL($redisKey);
                foreach ($redisList as $key => $value) {
                    Redisx::HMSET($redisKey . '|id:' . $value['id'], $value);
                    Redisx::RPUSH($redisKey, $value['id']);
                }
            }
        }
        return array('list'=>$list,'pageLinks'=>$pageList);
    }
}