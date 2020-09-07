<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/3
 * Time: 13:51
 */

namespace App\S\Store;
use App\Lib\Redis\MicroPageRedis;
use App\S\Product\ProductService;
use App\S\S;

class MicroPageService extends S
{
    public function __construct()
    {
        parent::__construct('MicroPage');
    }

    /**
     * todo 添加微页面信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
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
        if(empty($data['page_title']))
        {
            $errMsg.='页面名称为空';
        }
        if (empty($data['page_template_info'])||$data['page_template_info']=='[]')
        {
            $data['page_template_info']=null;
        }
        else
        {
            if(is_array($data['page_template_info']))
            {
                $data['page_template_info'] = json_encode($data['page_template_info']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['page_template_info'],true);
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
     * todo 更改微页面数据信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
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
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new MicroPageRedis();
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
     * todo 通过id来删除微页面信息
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
        $deleteReturnValue=$this->model->where(['id'=>$id])->delete();
        if(!$deleteReturnValue)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new MicroPageRedis();
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
     * todo 获取微页面信息
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
        $redis = new MicroPageRedis();
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
     * todo 通过查询条件来查询id
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2017-06-28
     */
    public function getRowByCondition($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where($data)->first();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            //如果没有查询到符合要求的数据$result为null
            return $returnData;
        }
        $result=$result->toArray();
        return $this->getRowById($result['id']);
    }

    /**
     * todo 查询微页面信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function getListByCondition($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        $result = $this->model->where($data)->get();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            //如果没有查询到符合要求的数据$result为null
            return $returnData;
        }
        $returnData['data']=$result->toArray();
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
        if ($data) {
            foreach ($data as $key => $value) {
                switch ( $key ) {
                    //店铺id
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    // 是否显示
                    case 'is_show':
                        $where['is_show'] = $value;
                        break;
                    // id
                    case 'id':
                        $where['id'] = ['in',$value];
                        break;
                    // 标题
                    case 'page_title':
                        $where['page_title'] = ['LIKE','%'.$value.'%'];
                        break;
                }
            }
        }
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new MicroPageRedis();

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
     * todo 统计店铺下微页面数
     * @param int $wid
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-25
     */
    public function statMicroPage($wid=0)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '','data'=>0);
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        $result=$this->model->where(['wid'=>$wid])->count();
        if($result===false)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='统计数据失败';
            return $returnData;
        }
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * todo 统计微页面下商品数
     * @param string $templateData
     * @return int
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-16
     */
    public function statProductNum($templateData='')
    {
        //$productNum存放商品个数
        $productNum=0;
        if(!empty($templateData)&&$templateData!='[]')
        {
            //把json字符串转化为数组
            $templateArrayData=json_decode($templateData,true);
            //json字符串转化成功
            if(is_array($templateArrayData)&&!empty($templateArrayData))
            {
                foreach($templateArrayData as $templateItem)
                {
                    //查找商品
                    if (isset($templateItem['type']) && $templateItem['type'] == 'goods' && !empty($templateItem['products_id']) && $templateItem['products_id'] != '[]')
                    {
                        //$productsID存放商品id
                        $productsID=$templateItem['products_id'];
                        $productService=new ProductService();
                        foreach($productsID as $productID)
                        {
                            //判断该商品是否存在
                            $list=$productService->getList(['id'=>$productID,'status'=>1]);
                            if(!empty($list))
                            {
                                $productNum=$productNum+1;
                            }
                        }
                    }
                }
            }
        }
        return $productNum;
    }


    /**
     * todo 通过查询条件 查询出id
     * @param array $whereData
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function selectIDByCondition($whereData=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($whereData))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where($whereData)->first();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            //如果没有查询到符合要求的数据$result为null
            return $returnData;
        }
        $returnData['data']=$result->toArray();
        return $returnData;
    }

	/**
	 *
	 * @param array $where
	 * @param \Closure $callback
	 * @return \Generators
	 */
	public function cursor(array $where = [], \Closure $callback)
	{
		foreach ($this->model->wheres($where)->cursor() as $record)	{
			yield $callback($record);
		}
	}
}
