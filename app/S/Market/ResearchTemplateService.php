<?php

namespace App\S\Market;

use App\S\S;

/**
 * 调查活动模板
 * @author 何书哲 2018年7月16日
 */
class ResearchTemplateService extends S {

    public function __construct()
    {
        parent::__construct('ActivityResearchTemplate');
    }

    /**
     * 获取非分页模板列表
     * @param array $where 查询条件
     * @param string|array $orderBy 排序字段
     * @param string $order 排序顺序
     * @return array 模板列表
     * @author 何书哲 2018年7月16日
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        $this->setParameter('all');
        return $this->getList($where, '', '', $orderBy, $order);
    }

    /**
     * 根据主键获取调查模板内容
     * @param $id 调查模板主键id
     * @return array 模板数据
     * @author 何书哲 2018年7月18日
     */
    public function getResearchTemplateById($id)
    {
        $tempate = $this->model->where('id', $id)->first();
        return $tempate ? $tempate->toArray() : [];
    }

    /**
     * 添加调查留言模板
     * @param array $data 调查留言模板字段
     * @return boolean
     * @author 何书哲 2018年7月17日
     */
    public function addResearchTemplate($data=[]) {
        return $this->model->insert($data);
    }


}