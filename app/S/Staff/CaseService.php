<?php
namespace App\S\Staff;
use App\S\S;
use App\Lib\Redis\CaseRedis;
use Illuminate\Support\Facades\Cookie;

class CaseService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('Cases');
		$this->redis = new CaseRedis();
	}

	/**
     * [获取案例列表并进行分页]
     * @param  [int]    $status   [状态]
     * 1-启用 0-禁用
     * @author WuXiaoPing
     * @date 2017-08-21
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($WhereData = [],$orderBy='',$pageSize=16)
    {
    	$where = [];
    	foreach ($WhereData as $key => $value) {
    		switch ($key) {
    			case 'goodsType':
    				$where['type'] = ['like','%'.$value.'%'];
    				break;
    			case 'industry':
    				$where['_string'] = ' find_in_set('.intval($value).',industry_ids)';
    				break;
    			case 'name':
    				$where['name'] = ['like','%'.$value.'%'];
    				break;
				case 'type':
    				$where['type'] = $value;
    				break;
    			default:
    				# code...
    				break;
    		}
    	}
    	$order = $orderBy ?? 'created_at';
		$list = $this->getListWithPage($where,[$order,'id'],'DESC',$pageSize);
    	
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
     * @update 何书哲 2018年7月30日 model返回为真时调用toArray
	 */
	public function getRowById($id)
	{
		if(empty($id)){
			error('数据异常');
		}
		$id = intval($id);
		$data = [];
		$data = $this->redis->getRow($id);
		if(empty($data)){
			$obj = $this->model->wheres(['id' => $id])->first();
			//何书哲 2018年7月30日 model返回为真时调用toArray
			if ($obj) {
			    $data = $obj->toArray();
                $this->redis->setRow($id,$data);
            }
		}
		return $data;

	}

	/**
	 * 统计相关案例详情页的访问人数
	 * @param  [string]  $ip        [访问用户ip]
	 * @param  [int]     $caseId    [案例id]
	 * @param  [int]     $num       [设置默认的浏览数]
	 * @return [type]               [description]
	 */
	public function statistics($ip,$caseId = 0,$num = 0)
	{
		//计算当时时间与24点之前还几个小时（多少秒）
		$seconds = strtotime(date('Y-m-d').' 23:59:59')-time();
		//计算小时（取整）
		$hours = floor($seconds/3600);

		//设置存储cookie的键值（key）
		$key = 'user:'.$ip.'news:'.$caseId;
		Cookie::queue($key, $num, $hours*3600);  //把参数保存到cookie,设置过期时间
		
	}
}