<?php
namespace App\S\Weixin;

use App\S\S;
use App\Lib\Redis\WeixinBusinessRedis;
/**
 * 
 */
class WeixinBusinessService extends S {

	public function __construct()
    {
    	$this->redis = new WeixinBusinessRedis();
        parent::__construct('WeixinBusiness');
    } 

    public function getAllCategory()
    {
        return $this->getList();
    }

    /**
     * 获取所有包含二级的一级分类
     * @return [type] [description]
     */
    public function getFirstCategory()
    {
        $data = $this->model->where('pid',0)->where('status',1)->orderBy('sort','desc')->withCount('hasManyChilds')->get();
    	return $data;
    }

    /**
     * 根据条件获取二级分类列表信息
     * @param  integer $pid       [一级分类id]
     * @param  array   $whereData [搜索的数组条件]
     * @return [type]             [description]
     */
    public function getSecondCategory($pid = 0,$whereData=[])
    {
    	$where['status'] = 1;
    	$where['pid'] = ['<>',0];
    	if (!empty($pid)) {
    		$where['pid'] = $pid;
    	}
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
    				case 'keyword':
    					if ($value) {
    						$where['title'] = ['LIKE',"%".$value."%"];;
    					}
    					break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
        $list = $this->model->wheres($where)->orderBy('sort','desc')->orderBy('id','desc')->get()->toArray();
        //dd($list);
    	//$list = $this->getList($where,'','',['sort','created_at'],'DESC');
        return $list;
    }

    /**
     * 根据条件获取所有的二级分类数
     * @param  [array] $where [数组条件]
     * @return [type]        [description]
     */
    public function getCatesCount($where)
    {   
        return $this->model->wheres($where)->count();
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     */
    public function getListById($idArr = [])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
       
        $result = $this->redis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 根据id获取单条记录
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getRowById($id)
    {
        $data = $this->redis->getRow($id);
        if (empty($data)) {
            $obj = $this->model->where('id',$id)->first();
            if ($obj) {
                $data = $obj->toArray();
                $this->redis->updateHashRow($id,$data);
            }
        }
        return $data;
    }

    /**
     * 获取分类下的商家案例列表
     * @return [type] [description]
     */
    public function cateCaseList($cateId)
    {
    	$data = $this->redis->getRow($cateId);
    	if (empty($data)) {
    		$where['id'] = $cateId;
    		$where['status'] = 1;
    		$obj = $this->model->where($where)->with(['shop'=>function($query){
    			$query->where('is_recommend',1)->with(['weixinConfigSub','wxxcxConfig']);
    		}])->first();
    		if ($obj) {
    			$data = $obj->toArray();
    			$data['shop'] = json_encode($data['shop']);
    			$this->redis->add($data);
    		}
    	}
    	return $data;	
    }

}