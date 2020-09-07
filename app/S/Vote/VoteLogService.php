<?php
namespace App\S\Vote;
use App\S\S;
use App\Lib\Redis\VoteLogRedis;
use App\S\Member\MemberService;

class VoteLogService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('VoteLog');
		$this->redis = new VoteLogRedis();
	}

	/**
     * [获取全部数据信息]
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($wid,$orderBy='')
    {
    	$where['wid'] = $wid;
    	$order = $orderBy ?? 'created_at';
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
			$this->redis->del($id);
			return true;
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

	/**
	 * 根据相关条件获取对应的列表信息
	 * @param array $condition 条件数组
	 * @return [type] [description]
	 */
	public function getListByWhereData($wid,$condition=[],$groupBy='',$input=[],$type='member')
	{
		$where['wid'] = $wid;
		foreach ($condition as $key => $value) {
			switch ($key) {
				case 'enroll_id':
					$where['enroll_id'] = $value;
					break;
				case 'vote_id':
					$where['vote_id'] = $value;
					break;
				case 'mid':
					$where['mid'] = $value;
					break;
				case 'created_at':
					$where['created_at'] = $value;
					break;
				
				default:
					# code...
					break;
			}
		}
		$idArr = $data = [];
		if ($groupBy) {
			$obj = $this->model->wheres($where)->select(['mid','enroll_id'])->groupBy($groupBy)->get();
		}else{
			$obj = $this->model->wheres($where)->select(['mid','enroll_id'])->get();
		}
		if (!$obj) {
			return [];
		}

		$data = $obj->toArray();
		if ($type == 'statistics') {
			return $data;
		}
		//获取id数组
		foreach ($data as $key => $value) {
			$idArr[] = $value['mid'];
		}

		if ($idArr) {
			$memberWhere['id'] = $idArr;
			$condition = ($memberWhere + $input);
			return (new MemberService())->getListByConditionWithPage($condition);
		}
		//还未有用户信息时返回空数据
		$list = [];
		$pageHtml = '';
		return [$list, $pageHtml];
		
		
	}

	/**
	 * 统计获取投票人次
	 * @param  int $wid       店铺id
	 * @param  int $enroll_id 活动id
	 * @return [type]            [description]
	 */
	public function getPersonTime($wid,$enroll_id)
	{
		$num = 0;
		$where['wid'] = $wid;
		$where['vote_id'] = $enroll_id;
		$obj = $this->model->wheres($where)->groupBy('mid')->get();
		if ($obj) {
			$data = $obj->toArray();
			$num = count($data);
		}
		return $num;
	}

}