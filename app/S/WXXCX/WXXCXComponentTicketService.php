<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/13
 * Time: 14:26
 */

namespace App\S\WXXCX;
use App\S\S;

class WXXCXComponentTicketService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXComponentTicket');
    }
    /**
     * todo 添加微信小程序第三方平台ticket信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-10-13
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
        $data['create_time']=time();
        $data['update_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改微信小程序第三方ticket信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-10-13
     */
    public function updateData($id=0,$data=[])
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        if (empty($id))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        if (empty($data))
        {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '更新的数据为空';
            return $returnData;
        }
        $data['update_time']=time();
        $updateReturnValue = $this->model->where(['id' => $id])->update($data);
        if ($updateReturnValue === false)
        {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '更新数据失败';
            return $returnData;
        }
        else if($updateReturnValue>0)
        {
            $returnData['errCode'] = 0;
            $returnData['errMsg'] = '更新数据成功';
            return $returnData;
        }
        $returnData['errCode']=1;
        $returnData['errMsg']='没有要更改的数据';
        return  $returnData;
    }

    /**
     * todo 通过id来获取小程序第三方平台ticket信息
     * @param $wid
     * @return array
     * @author add by jonzhang
     * @date 2017-10-13
     */
    public function getRowById($id)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($id))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为null';
            return $returnData;
        }
        $result = $this->model->where(['id' =>$id])->first();
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
        $returnData['data']=$result->toArray();
        return  $returnData;
    }

}