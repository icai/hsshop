<?php

namespace App\S\Product;


use App\Lib\Redis\ProductGroupTpl;
use App\Model\H5ComponentTempleteUse;
use Carbon\Carbon;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;

class H5ComponentTempleteUseService
{

    /*
     * @todo: 查询商品模板列表
     */
    public function getTempletes($params = array(),$wid = 0,$needPage = true){
        $perPage = config('database')['perPage'];  # 获取每页条数

        $type = isset($params['type']) && $params['type'] ? $params['type'] : '';
        if(!$type){
            error('请选择模板类型');
        }
        #从redis 里面取出数据
        $redisKey = 'h5component_templete_use:wid:' . $wid.'|type:'.$type; #从redis中取出 商品为店铺wid的 所有商品
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

            $fields = ['id','h5component_templete_id','title','wid','type','mod_type','mod_config_json','sort','status','created_at','updated_at'];

            $title = isset($params['title']) && $params['title'] ? $params['title'] : '';
            $type_id = isset($params['type_id'])?$params['type_id']:0;
            $where['wid'] = $wid;
            $where['status'] = 1;
            $where['type'] = $type;
            if ($title){
                $where['title'] = array('like', "'%" . $title . "%'");
            }
            if($type_id){
                $where['type_id'] = $type_id;
            }
            $query = H5ComponentTempleteUse::select($fields)->wheres($where)->order('id asc,sort desc');
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
                $where['status'] = 1;
                $where['type'] = $type;
                $redisList = H5ComponentTempleteUse::select($fields)->wheres($where)->order('id asc,sort desc')->get()->toArray();
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

    /*
     * @todo: 删除模板 页面接口
     * @params： id integer 模板id
     * @return： boolean 删除成功 或失败
     */
    public function delTpl($params = array(),$wid = 0){
        if(!isset($params['id']) || !$params['id'] || !is_numeric($params['id']) || !$wid){
            error('请选择模板！');
        }
        $id = $params['id'];
        #先删除数据库里面的数据
        $where['wid'] = $wid;
        $where['id'] = $id;
        $where['type'] = isset($params['type']) && $params['type'] ? $params['type'] :'';
        if(!$where['type']){
            error('未知模板类型，无法执行删除操作');
        }
        $flag = H5ComponentTempleteUse::wheres($where)->update(array('status' => 0,'deleted_at'=> Carbon::now()));
        if($flag){
            #从redis 里面删除数据
            $redisKey = 'h5component_templete_use:wid:' . $wid.'|type:'.$where['type']; #从redis中取出 商品为店铺wid的 所有与产品相关的模板
            Redisx::DEL($redisKey . '|id:' . $id);
            Redisx::LREM($redisKey ,0 , $id);
            return true;
        }
        return false;
    }

    /*
    * @todo: 增加或修改分组模板
    * @params： type string 模板类型
    *           type_id 模板类型对应的id  当type 为 goods 时 传递 0
    *           wid  店铺 id
    *           title 不是必传字段
    *           data  一个配置文件大数组
    */
    public function setGroupTemplete($params = array(),$wid = 0, $isEdit = false){
        if(empty($params) || !$wid){
            error('参数错误');
        }
        $data_config = isset($params['data']) && $params['data'] ? $params['data'] : [];
        if(!$data_config){
            error('模板数据为空');
        }
        if(!$params['type']){
            error('请选择模板类型');
        }

        //先查询模板
        //list($templates) = $this->init('wid', $wid)->where($where)->getList(false);
        $tplModel = new H5ComponentTempleteUse();
        $tplRedis = new ProductGroupTpl();
        if ($isEdit) {
            //获取id列表
            $tplIds = $tplModel->select('id')->where('type', $params['type'])->where('type_id', $params['type_id'])->pluck('id')->toArray();
            //删除列表
            $tplRedis->deleteArr($tplIds);
            $tplModel->where('type', $params['type'])->where('type_id', $params['type_id'])->delete();
        }

        //再添加新模板
        $sort = 1;
        foreach($data_config as $data){
            $insert_data = array();
            $insert_data['type'] = $params['type'];
            $insert_data['wid']  = $wid;

            #商品初始化时 模板id 也不是必填的
            $insert_data['type_id'] = $params['type_id'];
            $insert_data['title'] = $params['title'];
            $insert_data['mod_type'] = $data['type'];
            $insert_data['mod_config_json'] = json_encode($data);
            $insert_data['sort'] = $sort;

            $id = $tplModel->insertGetId($insert_data);
            if ($isEdit) {
                $insert_data['id'] = $id;
                $insert_data['created_at'] = date('Y-m-d H:i:s');
                $insert_data['updated_at'] = date('Y-m-d H:i:s');
                $insert_data['deleted_at'] = null;
                $tplRedis->add($insert_data);
            }

            $sort++;
        }

        return true;
    }

    public function getListByGroup($groupId)
    {
        //获取规格id列表
        $tplModel = new H5ComponentTempleteUse();
        $tplRedis = new ProductGroupTpl();
        $tplIds = $tplModel->select('id')->where('type', 'group')->where('type_id', $groupId)->pluck('id')->toArray();
        $redisArr = $tplRedis->getArr($tplIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($tplIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $tplModel->whereIn('id', $queryFromDB)->get()->toArray();
            $tplRedis->setArr($dataNotInRedis);
        }

        return array_merge($redisArr, $dataNotInRedis);
    }
}