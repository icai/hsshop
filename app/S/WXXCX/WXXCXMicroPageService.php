<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/14
 * Time: 9:15
 */

namespace App\S\WXXCX;
use App\Lib\Redis\WXXCXMicroPageRedis;
use App\S\S;

class WXXCXMicroPageService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXMicroPage');
    }

    /**
     * todo 添加小程序微页面信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $errMsg='';
        if(empty($data['wid']))
        {
            $errMsg.='店铺id为空';
        }
        if(empty($data['title']))
        {
            $errMsg.='页面名称为空';
        }
        if (empty($data['template_info'])||$data['template_info']=='[]')
        {
            $data['template_info']=null;
        }
        else
        {
            if(is_array($data['template_info']))
            {
                $data['template_info'] = json_encode($data['template_info']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['template_info'],true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $data['update_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改小程序微页面数据信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        if(isset($data['template_info']))
        {
            if(!empty($data['template_info']))
            {
                $compareData=json_decode($data['template_info'],true);
                if(empty($compareData))
                {
                    $returnData['errCode']=-4;
                    $returnData['errMsg']='数据格式不正确';
                    return $returnData;
                }
            }
        }
        $data['update_time']=time();
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new WXXCXMicroPageRedis();
        $updateData=$data;
        $updateData['update_time']=time();
        $i=0;
        $status=false;
        while($i<3&&!$status)
        {
            if($redis->updateRedis($id,$updateData))
            {
                $status=true;
            }
            $i++;
        }
        if(!$status)
        {
            $deleteReturnValue=$redis->deleteRedis($id);
            if(!$deleteReturnValue)
            {
                $returnData['errCode']=-4;
                $returnData['errMsg']='处理缓存失败';
                return $returnData;
            }
        }
        return $returnData;
    }

    /**
     * todo 通过id来删除小程序微页面信息
     * @param int $id
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function delete($id=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $deleteReturnValue=$this->model->where(['id'=>$id])->update(['current_status'=>-1,'update_time'=>time()]);
        if(!$deleteReturnValue)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='删除数据失败';
            return $returnData;
        }
        $redis=new WXXCXMicroPageRedis();
        $i=0;
        $status=false;
        while($i<3&&!$status)
        {
            if($redis->deleteRedis($id))
            {
                $status=true;
            }
            $i++;
        }
        return $returnData;
    }
    /**
     * todo 获取小程序微页面信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-07-03
     */
    public function getRowById($id,$isCache=true)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result=[];
        $redis = new WXXCXMicroPageRedis();
        //使用缓存
        if($isCache)
        {
            $result = $redis->getOne($id);
        }
        if(empty($result))
        {
            $result = $this->model->where(['id' => $id,'current_status'=>0])->first();
            if (empty($result))
            {
                if($result===false)
                {
                    $returnData['errCode'] = -2;
                    $returnData['errMsg'] = '查询数据出现错误';
                    return $returnData;
                }
                return $returnData;
            }
            $result=$result->toArray();
            $redis->addArr($result);
        }
        $returnData['data']=$result;
        return $returnData;
    }


    /**
     * todo 查询微页面数据信息[分页]
     * @param $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function getListByConditionWithPage($data=[],$orderBy='',$order='',$pageSize=0)
    {
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value) {
            switch ( $key ) {
                //店铺id
                case 'wid':
                    $where['wid'] = $value;
                    break;
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                // 标题
                case 'title':
                    $where['title'] = ['LIKE','%'.$value.'%'];
                    break;
                // 是否为主页
                case 'is_home':
                    $where['is_home'] =$value;
                    break;
                case 'current_status':
                    $where['current_status'] =$value;
                    break;
                case 'ids':
                    $where['id'] = ['in',$value];
                    break;
            }
        }
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询小程序微页面信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new WXXCXMicroPageRedis();

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

}