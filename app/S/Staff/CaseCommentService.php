<?php
namespace App\S\Staff;
use App\S\S;
use App\Lib\Redis\CaseCommentRedis;

class CaseCommentService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('CaseComment');
		$this->redis = new CaseCommentRedis();
	}

	/**
     * [获取对应案例的列表并进行分页]
     * @param  [int]    $status   [状态]
     * 1-启用 0-禁用
     * @author WuXiaoPing
     * @date 2017-08-21
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($caseId)
    {
    	if(empty($caseId)){
    		error('数据异常');
    	}
    	$where['case_id'] = $caseId;
        $list = $this->getListWithPage($where,'created_at','DESC');
        return $list;

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

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}

	/**
	 * 处理编辑
	 * @param  [int] $id   [主键id]
	 * @param  [array] $data [要更新的数组数据]
	 * @return [type]       [description]
	 */
	public function update($id,$data)
	{
		$rs = $this->model->wheres(['id' => $id])->update($data);
		if($rs){
			$this->redis->updateHashRow($id,$data);
			return true;
		}

		return false;
	}

	public function del($id)
	{
		$rs = $this->model->wheres(['id' => $id])->delete();
		if($rs){
			return $this->redis->del($id);
		}

		return false;
	}

	/**
	 * [getRowById description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getRowById($id)
	{
		if(empty($id)){
			error('数据异常');
		}
		$data = [];
		$data = $this->redis->getRow($id);
		if(empty($data)){
			$data = $this->model->wheres(['id' => $id])->first()->toArray();
			$this->redis->setRow($id,$data);
		}
		return $data;

	}
}