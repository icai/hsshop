<?php 
namespace App\S\WXXCX;
use App\S\S;
use App\Lib\Redis\WXXCXSyncFooterBarRedis;

class WXXCXSyncFooterBarService extends S{

	public function __construct()
	{
		parent::__construct('WXXCXSyncFooterBar');
		$this->redis = new WXXCXSyncFooterBarRedis();
	}

	/**
	 * 获取全部的导航信息
	 * @param  [int] $wid [店铺的主键id]
	 * @return [type]      [description]
	 */
	public function getAllList($wid,$whereData=[],$orderBy='',$is_page=true,$pageSize=15)
	{
		$where['wid'] = $wid;
		$order = $orderBy ?? 'created_at';
		foreach ($whereData as $key => $value) {
			switch ($key) {
				case 'page_path':
					$where['page_path'] = $value;
					break;
				default:
					# code...
					break;
			}
		}
		if ($is_page) {
			$list = $this->getListWithPage($where,$order,'ASC',$pageSize);
		}else{
			$list = $this->getList($where);
		}
		return $list;
		
	}

	/**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-12-11
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

	/**
	 * 删除数据
	 * @param  [int] $id   [主键id]
	 * @return [type]     [description]
	 */
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
	 * 获取单条数据
	 * @param  [int] $id   [主键id]
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
			$obj = $this->model->wheres(['id' => $id])->first();
			if ($obj) {
				$data = $obj->toArray();
				$this->redis->setRow($id,$data);
			}		
		}
		return $data;

	}

	/**
	 * 把修改标题或图标的数据以字符串形式保存到redis
	 * @param  [int] $wid  [店铺id]
	 * @param  [mix] $data [要保存的数据]
	 * @return [type]       [description]
	 */
	public function saveSyncBarByRedis($wid,$data)
	{
		$key = 'footerBar:'.$wid;
		return $this->redis->setStringData($key,$data);
	}

	/**
	 * 获取保存的redis字符串数据
	 * @param  [int] $wid  [店铺id]
	 * @return [type] [description]
	 */
	public function getSyncBarData($wid)
	{
		$key = 'footerBar:'.$wid;
		return $this->redis->getStringData($key);
	}

	/**
	 * 刷新删除redis字符串数据
	 * @param  [int] $wid  [店铺id]
	 * @return [type] [description]
	 */
	public function delSyncBarData($wid)
	{
		$key = 'footerBar:'.$wid;
		return $this->redis->delStringData($key);
	}
}





















