<?php
namespace App\S\Weixin;

use App\S\S;
use App\Lib\Redis\WeixinCaseRedis;

class WeixinCaseService extends S {

	public function __construct()
    {
    	$this->redis = new WeixinCaseRedis();
        parent::__construct('WeixinCase');
    } 

    /**
     * 获取所有案例列表
     * @param  array  $whereData [数组条件]
     * @return [type]            [description]
     */
    public function getAllCaseList($whereData=[],$is_page=true)
    {   
        // 过滤掉过期的案例
        $where['shop_expire_at'] = ['>',date('Y-m-d H:i:s',time())];
        if ($whereData) {
            foreach ($whereData as $key => $value) {
                switch ($key) {
                    case 'business_id':
                        if ($value) {
                            $where['business_id'] = $value;
                        }
                        break;
                    case 'title':
                        if ($value) {
                            $where['title'] = ['like','%'.$value.'%'];
                        }
                        break;
                    case 'type':
                        if ($value) {
                            $where['type'] = $value;
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        if ($is_page) {
            return $this->getListWithPage($where);
        }else {
            return $this->getList($where);
        }
    }

    /**
     * 根据id获取单条记录
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getRowById($id)
    {
        $data = $this->redis->getRow($id);
        if (empty($data)) {
            $obj = $this->model->where('id',$id)->first();
            if ($obj) {
                $data = $obj->toArray();
                $this->redis->updateHashRow($id,$data);
            }
        }
        return $data;
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
            $mysqlData = $this->model->whereIn('id',$redisId)->with('belongsToBusiness')->get()->toArray();
            foreach ($mysqlData as $key => &$value) {
                $value['belongsToBusiness'] = json_encode($value['belongsToBusiness'],JSON_UNESCAPED_UNICODE);
            }
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 处理编辑
     * @param  [int] $id   [主键id]
     * @param  [array] $data [要更新的数组数据]
     * @return [type]       [description]
     */
    public function update($id,$data,$redisRelationData=[])
    {
        $rs = $this->model->wheres(['id' => $id])->update($data);
        if($rs){
            $this->redis->updateHashRow($id,$data);
            if ($redisRelationData) {
                $this->redis->updateHashRow($id,$redisRelationData);
            }
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
     * 根据条件删除
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function delByCondition($where)
    {
        $data = $this->model->wheres($where)->pluck('id')->toArray();
        if (empty($data)) {
            return false;
        }
        foreach ($data as $id) {
            $this->del($id);
        }
        
    }

    /**
     * 批量插入
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function batchInsert($data)
    {
        return $this->model->insert($data);
    }

    /**
     * 文件上传
     * @author 吴晓平 <2018.11.29>
     * @param  [obj] $file        [上传的文件对象]
     * @param  [string] $folder_name [存储的文件夹规则]
     * @param  string $type        [区别是图片还是文件，默认图片]
     * @return [type]              [description]
     */
    public function fileUpload($file,$folder_name,$type="image")
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' => []];
        $allowed_ext = $type == 'image' ? ["jpg", "png", "jpeg"] : ["xls", "csv", "xlsx"];

        $upload_path = base_path('public') . '/' . $folder_name;

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 拼接文件名
        $filename = time() . '_' . str_random(10) . '.' . $extension;
        // 如果上传的不是图片将终止操作
        if ( ! in_array($extension, $allowed_ext)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '上传图片的格式不正确';
            return $returnData;
        }
        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);
        $path = $type == 'image' ? "/$folder_name/$filename" : "public/"."$folder_name/$filename";
        $returnData['data'] = $path;
        return $returnData;
    }
}