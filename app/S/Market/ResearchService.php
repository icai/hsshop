<?php

namespace App\S\Market;

use App\S\S;

/**
 * 调查活动
 * @author 许立 2018年7月5日
 */
class ResearchService extends S
{
    public function __construct()
    {
        parent::__construct('ActivityResearch');
    }

    /**
     * 获取带分页列表
     * @param array $where 查询条件
     * @param string|array $orderBy 排序字段
     * @param string $order 排序顺序
     * @param int $pageSize 每页数量
     * @return array
     * @author 许立 2018年7月5日
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        //直接查询列表所有字段 不缓存redis
        $this->setParameter('all');
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 获取导航文案数据
     * @return array
     * @author 许立 2018年7月5日
     */
    public function getStaticList()
    {
        return ['all' => '所有活动', 'future' => '未开始', 'on' => '进行中', 'end' => '已结束'];
    }

    /**
     * 获取调查活动详情
     * @param int $id 活动id
     * @return array
     * @author 许立 2018年7月5日
     */
    public function getDetail($id)
    {
        $detail = $this->model->where('id', $id)->first();
        return $detail ? $detail->toArray() : [];
    }

    /**
     * 使失效调查活动
     * @param int $id 活动id
     * @return int 更新的行数
     * @author 许立 2018年7月5日
     */
    public function invalidateResearch($id)
    {
        return $this->model->where('id', $id)->update(['invalidate_at' => date('Y-m-d H:i:s')]);
    }
}