<?php

namespace App\S\Product;

use App\Lib\Redis\ProductGroup as GroupRedis;
use App\Lib\Redis\ProductGroupTpl;
use App\Model\H5ComponentTempleteUse;
use App\Model\ProductGroup;
use App\S\S;
use DB;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;
use App\S\Product\ProductService;

class ProductGroupService extends S
{

    public function __construct()
    {
        parent::__construct('ProductGroup');
    }

    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new GroupRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /*
     * @todo: 删除单个商品分组
     * @params： id integer 分组id
     * @return： boolean 删除成功 或失败
     */
    public function delGroup($params = array(),$wid = 0){
        if(!isset($params['id']) || !$params['id'] || !is_numeric($params['id']) || !$wid){
            error('请选择分组！');
        }

        //先删除分组
        (new ProductGroup())->where('id', $params['id'])->delete();
        (new GroupRedis())->delete($params['id']);

        //删除模板
        $this->deleteTplsByGroupId($params['id']);

        //更新使用该分组的商品的分组ID字段
        $this->_updateProductGroupID($params['id']);

        return true;
    }

    /**
     * 根据商品分组ID删除模板
     */
    private function deleteTplsByGroupId($id)
    {
        $model = new H5ComponentTempleteUse();
        $redis = new ProductGroupTpl();
        //获取规格id列表
        $ids = $model->select('id')->where('type', 'group')->where('type_id', $id)->pluck('id')->toArray();
        //删除规格列表
        $redis->deleteArr($ids);
        $model->where('type', 'group')->where('type_id', $id)->delete();
    }

    /**
     * 更新使用该分组的商品的分组ID字段
     */
    private function _updateProductGroupID($group_id)
    {
        $product_service = new ProductService();
        $group_id = addslashes(strip_tags($group_id ));
        $products = $product_service->getList(['_string' => 'FIND_IN_SET(' . $group_id . ',group_id)']);
        foreach ($products as $product) {
            $group_id_array = explode(',', $product['group_id']);
            foreach ($group_id_array as $k => $id) {
                if ($id == $group_id) {
                    unset($group_id_array[$k]);
                    break;
                }
            }
            $new_group_id = implode(',', $group_id_array);
            $product_service->update($product['id'], ['group_id' => $new_group_id]);
        }
    }

    /*
     * @todo: 查询单个分组
     * @params： id integer 分组id
     * @return： 单个分组信息
     */
    public function getGroup($params = array(),$wid = 0){
        if(empty($params) || !$wid){
            error('参数错误！');
        }
        $group_id = isset($params['id']) && $params['id'] ? $params['id'] : 0;
        if(!$group_id){
            error('您要查询的分组信息不存在！');
        }

        //获取分组
        $group = $this->getDetail($group_id);
        if(empty($group)){
            error('您要查询的分组信息不存在！');
        }

        # 查询h5 下所有的模板信息
        $templates = (new H5ComponentTempleteUseService())->getListByGroup($group_id);
        $tpl = [];
        $tpl['data'] = [];
        $tpl['type'] = [];
        if(!empty($templates)) {
            foreach ($templates as $v){
                $tpl['type'][] = $v['mod_type'];
                $tpl['data'][] = json_decode($v['mod_config_json'], true);
            }
        }
        $group['tpl'] = $tpl;
        //拼团统计
        return  $group;
    }

    /**
     * 复制商品分组
     */
    public function copyGroup($wid_to, $group_from)
    {
        $now = date('Y-m-d H:i:s');

        //分组模板
        $group_template_service = new H5ComponentTempleteUseService();
        $templates_from = $group_template_service->getListByGroup($group_from['id']);
        $group_template_model = new H5ComponentTempleteUse();

        //新建一条分组
        unset($group_from['id']);
        $group_from['wid'] = $wid_to;
        $group_from['created_at'] = $group_from['updated_at'] = $now;
        empty($group_from['deleted_at']) && $group_from['deleted_at'] = null;
        $group_to_id = $this->model->insertGetId($group_from);

        //新建模板信息
        foreach ($templates_from as $template_from) {
            unset($template_from['id']);
            $template_from['created_at'] = $template_from['updated_at'] = $now;
            empty($template_from['deleted_at']) && $template_from['deleted_at'] = null;
            $template_from['wid'] = $wid_to;
            $template_from['type_id'] = $group_to_id;
            $group_template_model->insertGetId($template_from);
        }

        return $group_to_id;
    }

    /*
     * @todo: 新建或者编辑单个商品分组
     * @params: title string  商品分组标题
     * @params：show_tag_title  integer 页面上是否显示分组名称 1 是 0 否
                first_priority  integer  第一优先级
                second_priority integer  第二优先级
                default_config_json string 默认配置
                introduce string 分组简介
     */
    public function setGroup($params = array(),$wid = 0){
        if(empty($params) || !$wid){
            error('参数错误！');
        }
        $params_data = isset($params['data'])&&$params['data']?$params['data']:'';
        $params_data = json_decode($params_data, true);
        if(!$params_data){
            error('参数错误！');
        }
		/*
		$returnCode = $this->validateComponents($params_data);
		if ($returnCode['errno'] !== 0) {
			error($returnCode['errmsg']);
		}
		 */
        $group_id = isset($params['id']) && $params['id']?$params['id']: ''; # 分组id
        $config_params = $params_data[0];
        $insert_data['title'] = isset($config_params['groupTitle']) && $config_params['groupTitle']?$config_params['groupTitle']: ''; # 分组名称
        if(!$insert_data['title']){
            error('请填写分组名称！');
        }
        $insert_data['show_tag_title'] = isset($config_params['show_tag_title']) ? intval($config_params['show_tag_title']): 0;    # 是否显示分组标题
        $insert_data['first_priority'] = isset($config_params['first_priority']) ? $config_params['first_priority']: 1;    # 第一优先级排序
        $insert_data['second_priority'] = isset($config_params['second_priority']) ? $config_params['second_priority']: 3; # 第二优先级排序
        $insert_data['introduce'] = isset($config_params['signInfo']) ? $config_params['signInfo']: ''; # 分组简介

        $insert_data['is_default'] = $config_params['is_default'] ?? 0;

        $insert_data['wid'] = $wid;

        //状态默认值
        //$insert_data['status'] = isset($config_params['status']) ? intval($config_params['status']): 1;
        $insert_data['created_at'] = date('Y-m-d H:i:s');
        

        $isEdit = false;
        if ($group_id) {
            $isEdit = true;
            //编辑
            ProductGroup::where('id', $group_id)->update($insert_data);
            $insert_data['id'] = $group_id;
            (new GroupRedis())->updateRow($insert_data);
        } else {
            //添加
            //$group_id = $this->init('wid', $wid)->add($insert_data, false);
            $group_id = ProductGroup::insertGetId($insert_data);
        }

        if ($group_id) {
            # 存入对应的 模板表
            $tpl_data['type'] = 'group';
            $tpl_data['type_id'] = $group_id;
            $tpl_data['title'] = $insert_data['title'];
            $tpl_data['data'] = $params_data;
            #设置模板信息
            
            $templateUseService = new H5ComponentTempleteUseService();
            // foreach ($tpl_data['data'] as $k => $tData) {
            //     $tpl_data['data'][$k] = $this->_orderByGoods($tData);    
            // }
            $flag = $templateUseService->setGroupTemplete($tpl_data,$wid, $isEdit);
            if(!$flag){
                error('分组模板设置失败');
            }
            return true;
        }
        return false;
    }

    private function _orderByGoods($jsonData)
    {
        if (empty($jsonData['goods'])) {
            return $jsonData;
        }
        $goodsArr = $jsonData['goods'];

        $productId = [];
        foreach ($goodsArr as $v) {
            if (!isset($v['id'])) {
                return $jsonData;
            }
            $productId[] = $v['id'];
        }
        $sortId = (new ProductService())->sortProductIDArr($productId, $jsonData['first_priority'], $jsonData['second_priority']);
        $goods   = $jsonData['goods'];
        $goodsReturn = [];
        foreach ($sortId as $s) {
            foreach ($goods as $g) {
                if ($g["id"] == $s) {
                    $goodsReturn[] = $g;
                    break;
                }
            }
        }
        $jsonData['goods'] = $goodsReturn;

        $thGoodsReturn = [];
        $n = 0;
        for ($i=0; $i < count($goodsReturn); $i++) { 
            if ($i != 0  && ($i % 3) == 0) {
                $n = $n + 1;
            }
            $thGoodsReturn[$n][] = $goodsReturn[$i];
        }
        $jsonData['thGoods'] = $thGoodsReturn;
        return $jsonData;
    }

    /**
     * 获取商品分组信息 默认取redis redis不存在则取数据库
     */
    public function getDetail($groupId)
    {
        $groupRedis = new GroupRedis();
        $group = $groupRedis->getRow($groupId);
        if (empty($group)) {
            //redis不存在 取数据库
            $group = ProductGroup::where('id', $groupId)->first();
            if (empty($group)) {
                $group = [];
                $groupRedis->delete($groupId);
            } else {
                $group = $group->toArray();
                //保存redis
                $groupRedis->add($group);
            }
        }
        return $group;
    }

    /**
     * todo 查询商品分组信息
     * @param int $wid 店铺id
     * @param int $id 商品分组id
     * @return array  商品分组信息
     * @author jonzhang
     * @date 2017-05-25
     */
    public function selectProductGroup($wid=0,$id=0)
    {
        //定义返回数据格式
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        //参数判断
        $errMsg='';
        if(empty($id))
        {
            $errMsg.='id为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        //查询商品分组名称信息
        $result=ProductGroup::select(['id','title'])->where(['id'=>$id])->get()->toArray();
        //dd($result);
        $returnData['data']=$result;
        //dd($returnData);
        return $returnData;
    }


    /**
     * desc 获取多个商品的分组信息
     * $ids  array
     * by  fuguowei
     * date 20180105
     */
    public function productGroup($ids)
    {
        $data = [];
        if (is_array($ids)){
            foreach($ids as $id){
                $data[] = $this->getDetail($id);
            }
        }else{
            $data[] = $this->getDetail($ids);
        }
        return $data;
    }

    /**
     * 获取商品分组包括模板信息
     */
    public function getGroupAndTemplates($wid, $group_id)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        if(empty($wid) || empty($group_id)){
            $resultArr['errMsg'] = '参数不完整';
            return $resultArr;
        }

        //获取分组
        $group = $this->getDetail($group_id);
        if(empty($group)){
            error('您要查询的分组信息不存在！');
            $resultArr['errMsg'] = '商品分组信息不存在';
            return $resultArr;
        }

        # 查询h5 下所有的模板信息
        $templates = (new H5ComponentTempleteUseService())->getListByGroup($group_id);
        $tpl = [];
        $tpl['data'] = [];
        $tpl['type'] = [];
        if(!empty($templates)) {
            foreach ($templates as $v){
                $tpl['type'][] = $v['mod_type'];
                $tpl['data'][] = json_decode($v['mod_config_json'], true);
            }
        }
        $group['tpl'] = $tpl;
        return  $group;
    }

    /**
     * 获取商品分组列表
     * @param array $products 商品列表
     * @return array 带商品分组信息的商品列表
     * @author 许立 2018年6月26日
     * @update 何书哲 2019年03月11日 获取商品编码
     */
    public function listWithGroupNames($products)
    {
        $productIds = array_column($products, 'id');

        //获取有规格商品编码
        $skuData = (new ProductSkuService())->model->whereIn('pid', $productIds)->where('code', '<>', ' ')->select('pid', 'code')->get()->toArray();
        foreach ($skuData as $item) {
             $skuData[$item['pid']][] = $item['code'];
        }

        //获取无规格商品编码
        $unSkuData = (new ProductService())->model->whereIn('id', $productIds)->where('goods_no', '<>', ' ')->select('id', 'goods_no')->get()->toArray();
        foreach ($unSkuData as $item) {
            $unSkuData[$item['id']][] = $item['goods_no'];
        }

        foreach ($products as $k => $product) {
            $products[$k]['group_name_array'] = array_column($this->getListById(explode(',', $product['group_id'])), 'title');
            $products[$k]['goods_no'] = array_unique(array_collapse([(isset($skuData[$product['id']]) ? $skuData[$product['id']] : []), (isset($unSkuData[$product['id']]) ? $unSkuData[$product['id']] : [])]));
        }

        return $products;
    }

    /**
     * 查询两个默认分组的信息（未分组，卡密商品）
     * @author 吴晓平 <2018年08月23日>
     * @param  [int] $wid [店铺id]
     * @return [type]      [description]
     */
    public function getGroupIdByDefault($wid)
    {
        $redis = new GroupRedis();
        $key = '-'.$wid;
        $returnData = $redis->getDataFromKey($key);
        $returnData = json_decode($returnData,true);
        if (empty($returnData)) {
            $obj = $this->model->select('id','title')->where(['wid' => $wid])->where(function($query){
                $query->where(['is_default' => 3])->orWhere(['is_default' => 4])->orderBy('is_default','ASC');
            })->get();
            if ($obj) {
                $rs = $obj->toArray();
                $returnData = json_encode($rs,JSON_UNESCAPED_UNICODE);
                $redis->setDataByKey($key,$returnData);
            }
        }
        return $returnData;
    }


}
