<?php
namespace App\S\Product;

use App\Lib\Redis\ProductProps;
use App\Model\ProductProps as PropsModel;
use App\S\S;

/**
 * 快递
 */
class ProductPropsService extends S
{
    public function __construct()
    {
        parent::__construct('ProductProps');
    }

    /**
     * 获取非分页列表
     * @return array
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

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ProductProps();
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
     * 给属性列表加上属性名字段
     * @param $propIds
     * @param $list
     * @return mixed
     */
    public function addPropTitleToProps($propIds, $list)
    {
        list($propList) = $this->listWithoutPage(['id' => ['in', $propIds]]);

        //拼接
        foreach ($list as $k => $v) {
            foreach ($propList['data'] as $vv) {
                if ($v['prop_id'] == $vv['id']) {
                    $v['prop_title'] = $vv['title'];
                    $list[$k] = $v;
                }
            }
        }

        return $list;
    }

    /**
     * 添加一个属性
     */
    public function add($wid, $title)
    {
        $id = $this->checkValue($wid, $title);
        if (empty($id)) {
            //新增一条
            $id = $this->model->insertGetId(['wid' => $wid, 'title' => $title]);
        }

        return $id;
    }

    /**
     * 检查某属性是否存在 存在则返回属性值ID
     */
    private function checkValue($wid, $title)
    {
        $row = $this->getRowByTitle($wid, $title);
        if ($row) {
            return $row[0]['id'];
        } else {
            return 0;
        }
    }

    /**
     * 根据属性名获取详情
     * @param $title string 属性名
     * @return array
     */
    private function getRowByTitle($wid, $title)
    {
        $where = [
            'title' => $title
        ];

        //查询系统公用或者当前店铺
        $where['_closure'] = function ($query) use ($wid) {
            $query->where('wid', 0)->orWhere('wid', $wid);
        };

        return $this->model->wheres($where)->get()->toArray();
    }
}