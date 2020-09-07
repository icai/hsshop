<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/28
 * Time: 17:04
 */

namespace App\S\Product;
use App\S\S;
use App\Lib\Redis\ProductTemplateRedis;

class ProductTemplateService extends S
{
    public function __construct()
    {
        parent::__construct('ProductTemplate');
    }

    /**
     * todo 查询商品模板信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-28
     */
    public function getListByCondition($data=[],$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value)
        {
            switch ( $key )
            {
                //店铺id
                case 'wid':
                    $where['wid'] = $value;
                    break;
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                // 标题
                case 'template_name':
                    $where['template_name'] = ['LIKE','%'.$value.'%'];
                    break;
                case 'template_style':
                    $where['template_style'] = $value;
                    break;
            }
        }
        //查询数据
        $select= $this->model->select(['id'])->where($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        $idAttr=[];
        if(!empty($pageSize))
        {
            //$select->paginate($pageSize)返回的是:LengthAwarePaginator
            //$select->paginate($pageSize)->toArray() 返回的是数组 "total" => 7, "per_page" => 20,"current_page" => 1
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        if(!empty($idAttr))
        {
            $ids=[];
            foreach ($idAttr as $item)
            {
                array_push($ids,$item['id']);
            }
            //通过id来查询对应的信息
            $returnData['data'] = $this->getListById($ids);
        }
        return $returnData;
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询商品模板信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-28
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new ProductTemplateRedis();

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
     * todo 查询商品模板数据信息[分页]
     * @param $data
     * @return array
     * @author jonzhang
     * @date 2017-07-28
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
                    // id
                    case 'id':
                        $where['id'] = $value;
                        break;
                    // 标题
                    case 'template_name':
                        $where['template_name'] = ['LIKE','%'.$value.'%'];
                        break;
                }
            }
        }
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }

    /**
     * todo 添加商品模板信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-28
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
        if(empty($data['template_name']))
        {
            $errMsg.='标题为空';
        }
        if(empty($data['template_style']))
        {
            $errMsg.='模板样式为空';
        }
        if (empty($data['product_template_info'])||$data['product_template_info']=='[]')
        {
            $data['product_template_info']=null;
        }
        else
        {
            if(is_array($data['product_template_info']))
            {
                $data['product_template_info'] = json_encode($data['product_template_info']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['product_template_info'],true);
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
     * todo 更改商品模板信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-28
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
        if (empty($data['product_template_info'])||$data['product_template_info']=='[]')
        {
            $data['product_template_info']=null;
        }
        else
        {
            if(is_array($data['product_template_info']))
            {
                $data['product_template_info'] = json_encode($data['product_template_info']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['product_template_info'],true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if($updateReturnValue===false)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new ProductTemplateRedis();
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
     * todo 通过id来删除微页面类型信息
     * @param int $id
     * @return array
     * @author jonzhang
     * @date 2017-07-28
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
        if($deleteReturnValue===false)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new ProductTemplateRedis();
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
     * todo 获取微页面类型信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-07-28
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
        $redis = new ProductTemplateRedis();
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
}