<?php
namespace App\S\Wechat;
use App\S\S;
use App\Lib\Redis\WeixinReplyRule;

class WeixinReplyRuleService Extends S{

	//设置model
	public function __construct()
	{
		parent::__construct('WeixinReplyRule');
	}

	/**
	 * [获取某个店铺下的全部微信图文数据，可以进行分页]
	 * @param  [int]    $wid   [店铺id]
	 * @param  [array]  $whereData [条件关联数组]
	 * @param  $is_page 是否进行分页
	 * @return [array]  $list  [微信图文数据]
	 */
	public function getAllList($wid,$whereData = [],$is_page = true)
	{	
		$where = [];
		if(!empty($whereData)){
			$where = ($where + $whereData);
		}
		$where['wid'] = $wid;
		if($is_page){
			$list = $this->getListWithPage($where);
		}else{
			$list = $this->getList($where);
		}
		
		return $list;
	}

	/**
	 * [根据主键id，获取单条微信图文数据]
	 * @param  [int]   $id      [主键id]
	 * @return [type]  $data    [微信图文数据]
	 */
	public function getRowById($id)
	{	
		$data = [];
		$obj = $this->model->wheres(['id' => $id])->first();
		if($obj){
			$data = $obj->toArray();
		}

		return $data;
	}

	/**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-07-25
     */
	public function getListById($idArr = [])
	{
		$weixinReplyRuleRedis = new WeixinReplyRule();
		$redisData = $mysqlData = [];
        $redisId = [];

        $result = $weixinReplyRuleRedis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->with(['weixinReplyKeyword','weixinReplyContent'])->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');

			//子表列表需要转化成json才能存redis hash Herry
			foreach ($mysqlData as &$v) {
				$v['weixinReplyKeyword'] = json_encode($v['weixinReplyKeyword']);
				$v['weixinReplyContent'] = json_encode($v['weixinReplyContent']);
			}

            $weixinReplyRuleRedis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
	}


    /**
     * 添加单条微信单图文
     * @param [int] 插入数据库的主键id值
     */
    public function add($data)
    {
    	return $this->model->insertGetId($data);
    }

	/**
	 * [编辑单条微信图文]
	 * @param  [type]  $id   [要更新对应的主键id]
	 * @param  [array] $data [要更新键值对数据]
	 * @return [int]         [影响的条数]
	 */
    public function update($id,$data)
    {
    	$returnData = ['errcode' => 0, 'errmsg' => ''];
    	if(empty($id)){
    		$returnData['errcode'] = -1;
    		$returnData['errmsg'] = 'id不能为空';
    		return $returnData;
    	}

    	if(empty($data)){
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }

    	$row = $this->model->wheres(['id' => $id])->update($data);
    	$weixinReplyRuleRedis = new WeixinReplyRule();

        //redis永远不更新的bug fixed by Herry
        /*if(!$row){
        	$result = $this->getRowById($id);
        	$weixinReplyRuleRedis->updateHashRow($id, $result);
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }*/

        $result = $this->getRowById($id);
        $weixinReplyRuleRedis->updateHashRow($id, $result);

        return $returnData;
    }

    /**
     * 根据图文id删除信息
     * @param  [int] $id [description]
     * @return [type]     [description]
     */
    public function del($id)
    {
    	$rs = $this->model->wheres(['id' => $id])->delete();
    	if($rs){
    		$weixinReplyRuleRedis = new WeixinReplyRule();
			return $weixinReplyRuleRedis->del($id);	
    	}
    	return false;
    	
    }

    /**
     * [relationSave description]
     * @param  [int]  $rule_id  [回复规则主键id]
     * @param  array   $data    [description]
     * @param  integer $type    [1、表示关键词，2、表示回复内容]
     * @return [type]           [description]
     */
    public function relationSave($rule_id,$data = [],$type = 1)
    {
        $list = $this->getRowById($rule_id);
        //把相关回复规则的关键词或回复内容保存到对应规则的redis中
        if($type == 1){
            $list['weixinReplyKeyword'] = json_encode($data);
        }else if($type == 2){
            $list['weixinReplyContent'] = json_encode($data);
        }

        $weixinReplyRuleRedis = new WeixinReplyRule();
        return $weixinReplyRuleRedis->updateHashRow($rule_id, $list);
    }

    /**
     * 数据处理
     * 
     * @param  array $list [需要被处理的数据]
     * @return array       [处理后的数据]
     */
    public function makeDatas($list)
    {
        $returnData = $this->model->dealDatas($list);

        return $returnData;
    }



}



 ?>