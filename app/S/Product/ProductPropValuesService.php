<?php
namespace App\S\Product;

use App\Lib\Redis\ProductPropValues;
use App\Model\ProductPropValues as PropValuesModel;
use App\S\S;

/**
 * 快递
 */
class ProductPropValuesService extends S
{
    public function __construct()
    {
        parent::__construct('ProductPropValues');
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
        $redis = new ProductPropValues();
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
     * 添加一个属性值
     */
    public function addValue($title)
    {
        $id = $this->checkValue($title);
        if (empty($id)) {
            //新增一条
            $id = PropValuesModel::insertGetId(['title' => $title]);
        }

        return $id;
    }

    /**
     * 编辑
     */
    public function editValue($id, $title)
    {
        //获取详情
        $row = $this->getDetail($id);
        if (empty($row)) {
            error('属性值不存在');
        }

        //修改
        PropValuesModel::where('id', $id)->update(['title' => $title]);
        (new ProductPropValues())->updateRow(['id' => $id, 'title' => $title]);

        success();
    }

    /**
     * 检查某属性值是否存在 存在则返回属性值ID
     */
    private function checkValue($title)
    {
        $row = $this->getRowByTitle($title);
        if ($row) {
            return $row[0]['id'];
        } else {
            return 0;
        }
    }

    /**
     * 根据属性值名获取详情
     * @param $title string 属性值名
     * @return array
     */
    public function getRowByTitle($title)
    {
        return PropValuesModel::where('title', $title)->get()->toArray();
    }

    /**
     * 获取详情 默认取redis redis不存在则取数据库
     */
    public function getDetail($id)
    {
        $redis = new ProductPropValues();
        $data = $redis->get($id);
        if (empty($data)) {
            //redis不存在 取数据库
            $data = PropValuesModel::where('id', $id)->first()->toArray();
            //保存redis
            $redis->add($data);
        }
        return $data;
    }

    /**
     * 给属性列表加上属性值名字段
     * @param $valueIds
     * @param $list
     * @return mixed
     */
    public function addValueTitleToProps($valueIds, $list)
    {
        list($valueList) = $this->listWithoutPage(['id' => ['in', $valueIds]]);

        //拼接
        foreach ($list as $k => $v) {
            foreach ($valueList['data'] as $vv) {
                if ($v['value_id'] == $vv['id']) {
                    $v['title'] = $vv['title'];
                    $list[$k] = $v;
                }
            }
        }

        return $list;
    }

    /**
     * 属性值列表去重
     * @param array $values 原始属性值列表
     * @return array 去重后的属性值列表
     * @author 许立 2018年7月3日
     */
    public function removeRepeatValue($values)
    {
        // 唯一标题数组
        $title_list = [];

        // 循环 去重
        foreach ($values as $k => $value) {
            if (in_array($value['title'], $title_list)) {
                // 移除该属性值
                unset($values[$k]);
            } else {
                // 加入唯一标题数组
                $title_list[] = $value['title'];
            }
        }

        return $values;
    }
}