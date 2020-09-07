<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 下午1:33
 */

namespace App\S\File;


use App\Http\Requests\Request;
use App\Lib\Redis\UserFileRedis;
use App\Model\UserFile;
use App\S\S;
use RedisPagination;
use Session;
use Validator;

class UserFileService extends S
{

    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');
        parent::__construct('UserFile');

    }




    /**
     * @auth zhangyh
     * @desc 修改用户文件分组
     * @param $data
     *
     */
    function modifyClassify($data,$classify_id,$user_id)
    {
        $userFileData = Array();
        //根据用户文件id更新
        foreach ($data as $key=>$value)
        {
            $userFileData['file_classify_id'] = $classify_id;
            $this->update($value,$userFileData);
        }
    }

    /**
     * @auth zhangyh 201703051400
     * @desc 删除用户与文件的关联关系，真实文件不进行删除
     * @param Request $request
     * @param FileInfoService $fileInfoService
     */
    function delUserFile($userFileIds)
    {
        $wid = session('wid');
        //删除用户文件信息
        foreach ($userFileIds as $key=>$val){
            $userFileData = $this->getRowById($val);
            if (!empty($userFileData) && $userFileData['weixin_id'] == $wid) {
                $res = $this->del($val);
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     * @auth zhangyh
     * @version 201703051543
     * @desc 根据用户分组获取用户文件
     * @param $classifyId
     */
    function getUserFileByClassify($classifyId,$file_mine=1)
    {
        $userId = session('userInfo')['id'];
        $wid = session('wid');
        $where = Array();
        $where['file_classify_id'] = $classifyId;
        $where['file_mine'] = $file_mine;
        $where['weixin_id'] = $wid;

        $page = 15;
        if ($file_mine == 2) {
            $page = 10;
        }
        $res = $this->getlistPage($where, $page);
        return $res;
    }

   function getUserFileByClassifyForDel($classifyId)
    {
        $userId = session('userInfo')['id'];
        $wid = session('wid');
        $where = Array();
        $where['file_classify_id'] = $classifyId;
        $where['weixin_id'] = $wid;

        $res = $this->getlistPage($where, 1000);//删1000个
        return $res;
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
        $redis = new UserFileRedis();
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
        $redis = new UserFileRedis();
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
            $storeRedis = new UserFileRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new UserFileRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where, $page = 15)
    {
        $input = $this->request->input();
        $perPage= $page;
        $list = $this->model->wheres($where)->orderBy('id','desc');
        $list = $list->paginate($perPage)->appends($input);
        $pageHtml = $list->links();
        $list->load('FileInfo');
        $list = $list->toArray();
        return [ $list, $pageHtml ];
    }

    public function getListWithPage($where = [], $orderBy = '', $order = '', $pageSize = 0)
    {
        return parent::getListWithPage($where, $orderBy, $order, $pageSize); // TODO: Change the autogenerated stub
    }


}