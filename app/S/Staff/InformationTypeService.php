<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  11:39
 * DESC
 */

namespace App\S\Staff;


use App\Lib\Redis\InformationTypeRedis;
use App\Model\InformationType;
use App\S\S;
use App\Services\Service;
use App\Services\Staff\InformationService;
use App\S\Staff\InformationService as InfoService;

class InformationTypeService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('InformationType');
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132026
     * @desc 获取分类
     * @param $parentId
     */
    public function  get($parentId=null)
    {
        if (is_null($parentId)){
            $where = [];
        }else{
            $where = [
                'parent_id'     => $parentId
            ];
        }
        $data['data']= $this->getList();
        $typeTmp = [];
        foreach ($data['data'] as $val)
        {
            $typeTmp[$val['id']] = $val['name'];
        }
        $infoTypeData = $this->getListWithPage($where);
        foreach ($infoTypeData[0]['data'] as &$val)
        {
            if ($val['type_path']){
                foreach (explode(',',$val['type_path']) as $v)
                {
                    $val['name_path'][] = $typeTmp[$v];
                }
                $val['name_path'] = implode('->',$val['name_path']);
            }else{
                $val['name_path']=$val['name'];
            }
        }

        return $infoTypeData;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703240909
     * @desc 删除分类
     * @param $id
     */
    public  function delInfoType($id)
    {
        $data = $this->getRowById($id);
        //查看是几级类目
        if (count(explode(',',$data['type_path'])) == 3){
            //查看是否有资讯属于该分类
            $informationService = new InformationService();
            $res = $informationService->init()->model->where(['info_type'=>$id])->get()->toArray();
            if ($res){
                error('已有资讯属于该分类，请先删除资讯');
            }else{
                $this->del($id)?success():error();
            }
        }else{
            $res =$this->model->where(['parent_id'=>$id])->get()->toArray();
            if ($res){
                error('该分类存在子分类，请先删除子分类');
            }else{
                $this->del($id)?success():error();
            }
        }
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
        $redis = new InformationTypeRedis();
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
        $redis = new InformationTypeRedis();
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
            $storeRedis = new InformationTypeRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new InformationTypeRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where=[])
    {
        return $this->getListWithPage($where, '', '');
    }

    public function getAllList($where=[])
    {
        return $this->getList($where);
    }

    /**
     * 重构获取全部（不分页）分类数据
     * @author 吴晓平 <2018年07月03日>
     * @param  array  $whereData [数组条件]
     * @return [type]            [description]
     */
    public function getListByWhere($whereData = [])
    {
        $where = [];
        foreach ($whereData as $key => $value) {
            switch ($key) {
                case 'parent_id':
                    $where['parent_id'] = $value;
                    break;
                case 'name':
                    $where['name'] = $value;
                    break;
                case 'type_path': //update by 吴晓平 2018.07.23（用like如导致类以 包含117，1117这类的id也会被查询到）
                    //$where['type_path'] = ['like','%'.$value.'%'];
                    $value = addslashes(strip_tags($value));
                    $value = intval($value);
                    $where['_string'] = ' find_in_set('.$value.', type_path) ';
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $this->getList($where,'','','sort','desc');
    }

    /**
     * 获取帮助中心，会搜云资讯的分类列表，资讯列表
     * @author 吴晓平 <2018年07月03日>
     * @param  string  $keywords  [帮助中心搜索的关键词]
     * @param  integer $type_path [分类id]
     * @param  integer $newsType  [分类类型 1表示帮助中心，2表示会搜云资讯]
     * @param  string  $list      [是否需要获取资讯列表 list表示需要，其他(detail)表示不需要]
     * @return [type]             [description]
     */
    public function getNewsList($keywords='',$type_path=0,$newsType=1,$list='list',$pageSize=0,$pid=0)
    {
        //直接从数据库中获取帮助中心下的类目
        if ($newsType == 1) {
            $name = '帮助中心';
        }else {
            $name = '会搜云资讯';
        }
        $returnData = [];
        $data = $this->getListByWhere(['parent_id' => 0,'name' => $name]);
        if ($data) {
            $firstLevelId = $data[0]['id'];
            $result = $this->getListByWhere(['type_path' => $firstLevelId]);
            //归类二，三级分类
            foreach ($result as $key => $value) {
                if ($value['parent_id'] == $firstLevelId) {
                    $infoTypeData['sec'][] = $value;
                }else {
                    $infoTypeData['third'][] = $value;
                }
            }
            //标识需不需要获取相应分类下的所以资讯信息列表
            if ($list == 'list') {
                //获取帮助中心资讯列表
                $cateIds = []; //定义三级分类下的数组
                foreach ($infoTypeData['third'] as $key => $value) {
                    $cateIds[] = $value['id'];
                }
                $infoService = new InfoService();
                $where['status'] = 1;
                if ($type_path) {
                    $where['info_type'] = [$type_path];
                }else if($pid) {
                    $cateIds = $this->getSecCateIds($pid);
                    $where['info_type'] = $cateIds;
                }else {
                    $where['info_type'] = $cateIds;
                }
                if ($keywords) {
                    $where['keywords'] = $keywords;
                }
                list($newsList,$pageHtml) = $infoService->getAllWithPage($where,$pageSize);
                $returnData['newsList'] = $newsList;
                $returnData['pageHtml'] = $pageHtml;
            }
            

            //（数据处理）合并三级分类到二级分类下
            foreach ($infoTypeData['sec'] as $k => &$secVal) {
                foreach ($infoTypeData['third'] as $item => $thiVal) {
                    if ($secVal['id'] == $thiVal['parent_id']) {
                        $secVal['child'][] = $thiVal;
                    }
                }
            }
            $returnData['nav'] = $infoTypeData['sec'];
        }
        return $returnData;
    }

    /**
     * 根据二级分类的名称获取对应的资讯信息
     * @author 吴晓平 <2018.07.09>
     * @return [type] [description]
     */
    public function getListFromSecTypeName($name='',$pageSize=2)
    {
        $data = $this->getListByWhere(['name' => $name]);
        $returnData = $this->dealNewsData($data,$pageSize);
        return $returnData;
    }

    /**
     * 根据二级分类的id获取对应的资讯信息
     * @author 吴晓平 <2018.07.09>
     * @return [type] [description]
     */
    public function getListFromSecById($id=0,$pageSize=5)
    {
        $data = $this->getRowById($id);
        $returnData = $this->dealNewsData([$data],$pageSize);
        return $returnData;
    }



    /**
     * 处理资讯数据
     * @author 吴晓平 <2018.07.09>
     * @param  array  $data []
     * @return [type]       [description]
     */
    public function dealNewsData($data = [],$pageSize=0)
    {
        $returnData = [];
        if ($data) {
            $typeData = [];
            foreach ($data as $key => $value) {
                $typePath = explode(',',$value['type_path']);
                if (count($typePath) == 2) {
                    $typeData[] = $value;
                } 
            }
            if (!empty($typeData)) {
                $firstLevelId = $typeData[0]['id'];
                $result = $this->getListByWhere(['type_path' => $firstLevelId]);
                //排除当前的二级分类
                foreach ($result as $key => $value) {
                    if ($value['parent_id'] == $firstLevelId) {
                        $infoTypeData[] = $value;
                    }
                }
                $cateIds = [];
                if (isset($infoTypeData) && $infoTypeData) {
                    foreach ($infoTypeData as $key => $value) {
                        $cateIds[] = $value['id'];
                    }
                }
                $infoService = new InfoService();
                $where['status'] = 1;
                $where['info_type'] = $cateIds;
                $newsList = $infoService->getAllWithPage($where,$pageSize);
                $returnData = $newsList[0]['data'];
            }
        }
        return $returnData;
    }

    /**
     * 根据二级分类id获取所有的三级下的分类id
     * @author 吴晓平 <2018.07.12>
     * @param  integer $pid [二级分类id]
     * @return [type]       [description]
     */
    public function getSecCateIds($pid=0)
    {
        $result = $this->getListByWhere(['type_path' => $pid]);
        //排除当前的二级分类
        foreach ($result as $key => $value) {
            if ($value['parent_id'] == $pid) {
                $infoTypeData[] = $value;
            }
        }
        $cateIds = [];
        if (isset($infoTypeData) && $infoTypeData) {
            foreach ($infoTypeData as $key => $value) {
                $cateIds[] = $value['id'];
            }
        }

        return $cateIds;
    }

}









