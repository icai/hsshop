<?php
namespace App\S\Store;
use App\Lib\Redis\MemberHomeRedis;
use App\S\S;

/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/23
 * Time: 15:01
 */
class MemberHomeService extends S
{
    public function __construct()
    {
        parent::__construct('MemberHome');
    }

    /**
     * todo 添加会员主页信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-23
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
        if(empty($data['home_name']))
        {
            $errMsg.='页面名称为空';
        }
        if(empty($data['custom_info'])||$data['custom_info']=='[]')
        {
            $data['custom_info']=null;
        }
        else
        {
            if(is_array($data['custom_info']))
            {
                $data['custom_info'] = json_encode($data['custom_info']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['custom_info'],true);
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
     * todo 更改会员主页数据
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-23
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
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if($updateReturnValue===false)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new MemberHomeRedis();
        $updateData=$data;
        $updateData['updated_at']=date('Y-m-d H:i:s');
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
     * todo 获取会员主页信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-06-23
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
        $redis = new MemberHomeRedis();
        //使用缓存
        if($isCache)
        {
            $result = $redis->getOne($id);
        }
        if(empty($result))
        {
            $result = $this->model->where(['id' => $id])->first();
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
     * todo 通过店铺id来获取店铺信息
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2017-06-26
     */
    public function getRow($wid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($wid))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where(['wid' => $wid])->first();
        if(empty($result))
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
        return $this->getRowById($result['id']);
    }
}