<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 下午1:45
 */

namespace App\S\File;


use App\Lib\Redis\FileClassifyRedis;
use App\Model\FileClassify;
use App\S\S;
use DB;
use RedisPagination;
use App\S\File\UserFileService;

class FileClassifyService extends S
{

    /**
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');
        parent::__construct('FileClassify');

    }



    /**
     * @auth zhangyh
     * @date 201703031250
     * @param $data
     * @return bool
     */
    function insert(&$data)
    {
        /*插入数据 */
        try{
            $insertGetId= $this->model->insertGetId($data);
        }catch (exception $e)
        {
            return false;
        }
        $insertDatas = array();
        /*redis插入 */
        if($insertGetId) {
            $data['id'] = $insertGetId;
            $insertDatas[$insertGetId] = $data;
            /*redis插入 */
            RedisPagination::save($insertDatas);
            return true;
        }
        return false;
    }


    /**
     * @auth zhangyh
     * @desc 获取分组信息
     * @date 201703031651
     */

    function getMyClassify($user_id, $file_mine)
    {
        $wid = session('wid');
        $where = Array();
        $where['user_id'] = $user_id;
        $where['weixin_id'] = $wid;
        $where['file_mine'] = $file_mine;
		$userFileService = new UserFileService();
        $classify =$this->model->where($where)->order('id asc')->get()->toArray();
        //获取用户图片数量
        $res = $userFileService->model->select(DB::raw('count(*) as number,file_classify_id'))->where('weixin_id',$wid)->where('file_mine',$file_mine)

        ->groupBy('file_classify_id')->get()->toArray();
        return $this->dealData($classify,$res);
    }

    /**
     * @auth zhangyh
     * @date 201703031708
     * @todo 处理分类数据
     */
    function dealData($classify,$data)
    {
        $classify_number = Array();
        $tmp = Array();
        $result = Array();

        foreach ($data as $key=>$val)
        {
            $classify_number[$val['file_classify_id']] = $val['number'];
        }
        //添加未分组数据
        array_push($result,Array(
            'name'      => '未分组',
            'id'        => 0,
            'number'    => isset($classify_number[0])?$classify_number[0]:0,
        ));

        foreach ($classify as $key=>$value)
        {
            $tmp['name'] = $value['name'];
            $tmp['id']   = $value['id'];
            $tmp['number'] = isset($classify_number[$value['id']])?$classify_number[$value['id']]:0;
            array_push($result,$tmp);
        }
        return $result;
    }


    /**
     * @auth zhangyh 201703061723
     * @param $classifyId
     */
    function delClassify($classifyId)
    {
        $this->del($classifyId);
        //修改用户文件分组为默认分组
        $userFileService = new UserFileService();
        $userFileData = $userFileService->getUserFileByClassifyForDel($classifyId);
        foreach ($userFileData[0]['data'] as $val){
            $res = $userFileService->update($val['id'],['file_classify_id' => 0]);
            if (!$res){
                return false;
            }
        }
        return true;
    }





//新service方法
    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new FileClassifyRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new FileClassifyRedis();
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

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new FileClassifyRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new FileClassifyRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where)
    {
        return $this->getListWithPage($where, '', '');
    }

}