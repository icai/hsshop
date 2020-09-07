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
use App\Lib\Redis\InformationRedis;
use Illuminate\Pagination\LengthAwarePaginator;


class InformationService extends S
{

    protected $redis;
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('Information');
        $this->redis = new InformationRedis();
    }

    public function getAllList($WhereData = [])
    {
        $where = [];
//        if(is_array($WhereData) && $WhereData){
//            $keys = array_keys($WhereData);
//            if(in_array('goodsType',$keys)){
//                $where['type'] = ['like','%'.$WhereData['goodsType'].'%'];
//            }
//            if(in_array('industry',$keys)){
//                $where['_string'] = ' find_in_set('.$WhereData['industry'].',industry_ids)';
//            }
//
//            if (in_array('name',$keys)) {
//                $where['name'] = ['like','%'.$WhereData['name'].'%'];
//            }
//        }

        $list = $this->getListWithPage($WhereData,['sort','id'],'DESC');
        return $list;

    }

    /**
     * 重构获取分页资讯数据
     * @author 吴晓平 <2018年07月03日>
     * @param  array  $whereData [数组条件]
     * @return [type]            [description]
     * @update 许立 2018年08月15日 搜索逻辑优化
     */

    public function getAllWithPage($whereData = [],$pageSize=15)
    {
        if (empty($whereData['keywords'])) {
            // 无搜索
            $where = [];
            foreach ($whereData as $key => $value) {
                switch ($key) {
                    case 'status':
                        $where['status'] = $value;
                        break;
                    case 'info_type':
                        $where['info_type'] = ['in',$value];
                        break;
                    case 'cate':
                        $where['info_type'] = $value;
                        break;
                    case 'remove':
                        $where['id'] = ['<>',$value];
                        break;
                    default:
                        # code...
                        break;
                }
            }
            /*if ($is_mobile) {
                return $this->getNewsWithPageForMobile($where,$pageSize);
            }*/
            return $this->getListWithPage($where,['sort','id'],'DESC',$pageSize);

        } else {
            // 有搜索
            return $this->_searchByKeyword($whereData);
        }
    }

    /**
     * 获取资讯移动端数据 + 简单模式分页
     * @param  [type] $where    [数组条件]
     * @param  [type] $pageSize [每页显示的页数]
     * @return [type]           [description]
     */
    public function getNewsWithPageForMobile($where,$pageSize)
    {
        $count = $this->model->wheres($where)->count();
        $paging = new LengthAwarePaginator([], 20, $pageSize, null, ['path' => app('request')->url()]);
        $list = $paging->appends(app('request')->input());
        //分页HTML
        $pageHtml = $list->links('vendor.pagination.simple-default');
        //分页信息
        $list = $list->toArray();

        $select = $this->model->select('id')->wheres($where);
        $select->order('id desc');
        $page = app('request')->input('page') ? (int) app('request')->input('page') : 1;
        !$page && $page = 1;
        $skip = ($page - 1) * $pageSize;
        $list['from'] = $skip + 1;
        $list['to'] = $page * $pageSize;
        $ids = $select->skip($skip)->take($pageSize)->pluck('id')->toArray();
        $data = $this->getListById($ids);

        $list['data'] = $data;
        return [$list,$pageHtml];
    }

    /**
     * 根据关键字标题内容的优先级顺序搜索列表
     * @param array $where 搜索条件
     * @return array
     * @author 许立 2018年08月15日
     */
    private function _searchByKeyword($where)
    {
        // 公共条件
        $select = $this->model->select('id');
        !empty($where['status']) && $select->where('status', $where['status']);
        !empty($where['info_type']) && $select->whereIn('info_type', $where['info_type']);
        !empty($where['cate']) && $select->where('cate', $where['cate']);
        !empty($where['remove']) && $select->where('id', '<>', $where['remove']);

        // 1 查出相关关键字记录id
        //$keywordSelect = clone $select;
        $titleSelect = clone $select;
        $contentSelect = clone $select;
        //$keywordIds = $keywordSelect->where('keywords', 'like', '%' . $where['keywords'] . '%')
        //    ->pluck('id')
        //    ->toArray();
        // 2 查出相关标题记录id
        $titleIds = $titleSelect->where('title', 'like', '%' . $where['keywords'] . '%')
            ->pluck('id')
            ->toArray();
        // 3 查出相关内容记录id
        $contentIds = $contentSelect->where('content', 'like', '%' . $where['keywords'] . '%')
            ->pluck('id')
            ->toArray();
        // 4 根据优先级合并id并去重 得到分页后的当前页的id数组
        $idArr = [];
        //foreach ($keywordIds as $id) {
        //    $idArr[] = $id;
        //}
        foreach ($titleIds as $id) {
            !in_array($id, $idArr) && $idArr[] = $id;
        }
        foreach ($contentIds as $id) {
            !in_array($id, $idArr) && $idArr[] = $id;
        }

        // 5 根据id数组获取记录
        $pageSize = 15;
        $page = app('request')->input('page') ? (int) app('request')->input('page') : 1;
        !$page && $page = 1;
        $offset = ($page - 1) * $pageSize;
        // 当前页的id数组
        $currentPageIdArr = array_slice($idArr, $offset, $pageSize);
        $result = $this->getListById($currentPageIdArr);

        // 6 按id顺序显示列表
        $newResult = [];
        foreach ($result as $v) {
            $newResult[$v['id']] = $v;
        }
        $finalResult = [];
        foreach ($currentPageIdArr as $id) {
            $finalResult[] = $newResult[$id];
        }

        // 7 分页
        $paging = new LengthAwarePaginator([], count($idArr), $pageSize, null, ['path' => app('request')->url()]);
        $list = $paging->appends(app('request')->input());

        return [
            [
                'data' => $result
            ],
            $list->links()
        ];
    }


    /**
     * 获取相关的新闻资讯
     * @author 吴晓平 <2018.07.11>
     * @param  integer $info_type [当前资讯所属分类id]
     * @param  integer $currentId [当前资讯id]
     * @param  string  $keywords  [当前资讯包含关键词]
     * @return [type]             [description]
     */
    public function getRelevantNews($info_type=0,$currentId=0,$keywords='')
    {
        $releveNews = $this->model->where(function($query) use($info_type,$currentId,$keywords) {
            $query->where('info_type',$info_type)
                  ->where('id','<>',$currentId);
            })->orWhere(function($query) use ($currentId,$keywords) {
                $query->where('keywords', 'like', '%'.$keywords.'%');
                })->where(function($query) use ($currentId) {
                    $query->where('id','<>',$currentId);
                })->take(5)->get()->toArray();

        return $releveNews;
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
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 获取单条资讯内容
     * @author 吴晓平 <2018年07月04日>
     * @param  [type] $id [资讯id]
     * @return [type]     [description]
     */
    public function getRowById($id)
    {
        $result = [];
        $result = $this->redis->getRow($id);
        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $this->redis->addArr($result);
        }
        return $result;
    }

    //添加数据
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 处理编辑
     * @param  [int] $id   [主键id]
     * @param  [array] $data [要更新的数组数据]
     * @return [type]       [description]
     */
    public function update($id,$data)
    {
        $rs = $this->model->wheres(['id' => $id])->update($data);
        if($rs){
            $this->redis->updateHashRow($id,$data);
            return true;
        }

        return false;
    }


}









