<?php

namespace App\Services;

use App\Model\Freight;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;

class FreightService extends Service
{
    public function __construct(){

    }

    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

        $this->initialize(new Freight(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 获取一条模板 @todo 注意 仅限后台使用该方法(因为带session) 如果小程序要调用 建议另写方法
     */
    public function getOne($id)
    {
        return $this->init('wid', session('wid'))->getInfo($id);
    }

    /**
     * 添加一条模板
     */
    public function addOne($data)
    {
        return $this->init('wid', $data['wid'])->add($data, false);
    }

    /**
     * 修改一条模板
     */
    public function updateOne($data)
    {
        return $this->init('wid', session('wid'))->where(['id' => $data['id']])->update($data, false);
    }

    /**
     * 删除一条模板
     */
    public function delOne($id)
    {
        return $this->init('wid', session('wid'))->delete($id, false);
    }

    /*
     * 获取所有运费模板
     */
    public function getFreights($params = array(),$wid = 0, $needPage = true){
        $perPage = config('database')['perPage'];  # 获取每页条数
        #从redis 里面取出数据
        $redisKey = 'freight:wid:'.$wid; #从redis中取出
        $pageList = array();
        if(!$needPage){  #不需要分页的时候 直接取出所有的 列表
            $list = array();
            $idList = Redisx::LRANGE($redisKey,0,-1);
            if ( !empty($idList) ) {
                foreach ($idList as $key => $value) {
                    $lists[] = Redisx::HGETALL($redisKey . ':id:' . $value);
                }
                $list['data'] = $lists;
            }
            $count = $list ? 1 : 0;
        }else{
            #list($list, $pageList, $count) = RedisPagination::page($redisKey, $perPage);
            $list = [];$count = 0;
        }
        #$count = 0;
        # 多余一个参数 或者 没取到 任何东西时 使用 缓存
       if ((isset($params['page']) && count($params) > 1)  || !$count) {
            $fields = ['id','title'];

            $title = isset($params['title']) && $params['title'] ? $params['title'] : '';
            $where['status'] = 1;
            $where['wid'] = $wid;
            if ($title) {
                $where['title'] = array('title', "'%" . $title . "%'");
            }
            $query = Freight::select($fields)->wheres($where)->order('updated_at desc');
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
                $where['status'] = 1;
                $where['wid'] = $wid;
                $redisList = Freight::select($fields)->wheres($where)->order('updated_at desc')->get()->toArray();
                /* 写入redis */
                # 在写如redis key 之前清空里面已经存在的 key
                Redisx::DEL($redisKey);
                foreach ($redisList as $key => $value) {
                    Redisx::HMSET($redisKey . ':id:' . $value['id'], $value);
                    Redisx::RPUSH($redisKey, $value['id']);
                }
            }
        }

        return array('list'=>$list,'pageLinks'=>$pageList);
    }

    /**
     * 验证运费模板格式
     * @param $data array 待验证的数据
     * @return array
     */
    public function verifyFormat($data)
    {
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        if (is_array($data)) {
            if (empty($data['title'])) {
                $resultArr['errMsg'] = '模板名称不能为空';
                return $resultArr;
            }

            // if (empty($data['delivery_id'])) {
            //     $resultArr['errMsg'] = '快递公司不能为空';
            //     return $resultArr;
            // }

            $rules = json_decode($data['delivery_rule'], true);
            if (!is_array($rules)) {
                $resultArr['errMsg'] = '运费模板数据格式不正确';
                return $resultArr;
            }

            //默认配置必须有
            if ($rules[0]['regions'] !== [0]) {
                $resultArr['errMsg'] = '默认区域必须设置';
                return $resultArr;
            }

            //检查每个规则
            foreach ($rules as $rule) {
                if (!is_array($rule)) {
                    $resultArr['errMsg'] = '运费模板数据格式不正确';
                    return $resultArr;
                }
                if (!is_array($rule['regions'])) {
                    $resultArr['errMsg'] = '地区配置不正确';
                    return $resultArr;
                } else {
                    foreach ($rule['regions'] as $v) {
                        if ($v < 0) {
                            $resultArr['errMsg'] = '地区不合法';
                            return $resultArr;
                        }
                    }
                }
                if ($rule['first_amount'] <= 0) {
                    $resultArr['errMsg'] = '首件或首重必须大于0';
                    return $resultArr;
                }
                /*if ($rule['first_fee'] <= 0) {
                    $resultArr['errMsg'] = '首件或首重运费必须大于0';
                    return $resultArr;
                }*/
            }

            $resultArr['errCode'] = 0;
        } else {
            $resultArr['errMsg'] = '数据格式不正确';
        }

        return $resultArr;
    }

}