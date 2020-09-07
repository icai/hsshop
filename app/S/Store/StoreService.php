<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/26
 * Time: 10:21
 */

namespace App\S\Store;


use App\Lib\Redis\StoreRedis;
use App\Lib\WXXCX\ThirdPlatform;
use App\S\S;
use WXXCXCache;

class StoreService extends S
{
    public function __construct()
    {
        parent::__construct('Store');
    }

    /**
     * 获取非分页列表
     * @param array $where 检索条件
     * @param array|string $orderBy 排序字段 可以为多个字段的数组
     * @param string $order 排序方式 ASC|DESC
     * @return array
     * @author Herry
     * @since 2018/06/26 09:57
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

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
        $redis = new StoreRedis();
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
        $redis = new StoreRedis();
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
     * @desc 获取门店列表
     */
    public function getlistPage()
    {
        $wid = session('wid');
        return $this->getListWithPage(['wid'=>$wid], '', '');
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
            $storeRedis = new StoreRedis();
            return $storeRedis->update($id,$data);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 添加门店
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new StoreRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function getStoreNum(){
        $wid = session('wid');
        $number = $this->count(['wid'=>$wid]);
        return $number;
    }

    /**
     * 处理运营时间
     * @param array $storeData 门店数据数组
     * @return array
     * @author 许立 2018年6月26日
     */
    public function dealWithOpenTime($storeData)
    {
        // 开始时间
        $start_array = explode(',', $storeData['start_time']);
        $storeData['open_time'] = end($start_array);

        // 结束时间
        $end_array = explode(',', $storeData['end_time']);
        $storeData['close_time'] = end($end_array);

        // 工作日
        in_array('星期一', $start_array) && $storeData['monday'] = 1;
        in_array('星期二', $start_array) && $storeData['tuesday'] = 1;
        in_array('星期三', $start_array) && $storeData['wednesday'] = 1;
        in_array('星期四', $start_array) && $storeData['thursday'] = 1;
        in_array('星期五', $start_array) && $storeData['friday'] = 1;
        in_array('星期六', $start_array) && $storeData['saturday'] = 1;
        in_array('星期日', $start_array) && $storeData['sunday'] = 1;

        return $storeData;
    }


    /**
     * 门店小程序码
     * @param $wid
     * @return mixed
     * @author: 梅杰 2018年8月2日
     */
    public function getStoreXcxCode($wid)
    {
        $cache  =  WXXCXCache::get($wid,"store",true);
        if (!$cache) {
            //门店列表页面路径
            $page = 'pages/relation/relation';
            $data = (new ThirdPlatform())->createXcxQrCode($wid,$page,'store');
            if ($data['errCode'] == 0) {
                $cache  = $data['data'];
                WXXCXCache::set($wid,$data['data'],'store',7200);
            }
        }
        return $cache;
    }

    /**
     * 店铺门店小程序码下载
     * @param $wid
     * @author: 梅杰 2018年8月2号
     */

    public function downloadStoreXcxCode($wid)
    {
        $param = 'hsshop/image/qrcodes/store';
        $filename = $param."/".".jpg";
        if (file_exists($filename)) {
            return $filename;
        }
        if(!file_exists(iconv("UTF-8", "GBK", public_path($param)))) {
            mkdir(iconv("UTF-8", "GBK", public_path($param)),0777,true);
        }
        if ($code = $this->getStoreXcxCode($wid)) {
            $img = base64_decode($code);
            $a = file_put_contents($filename, $img);//返回的是字节数
            return $filename;
        }
        return false;
    }



}