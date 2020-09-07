<?php

namespace App\S\Market;

use App\S\S;

/**
 * 调查留言活动规则
 * @author 许立 2018年7月5日
 */
class ResearchRuleService extends S
{
    public function __construct()
    {
        parent::__construct('ActivityResearchRule');
    }

    /**
     * 获取某调查活动的规则列表
     * @param int $id 活动id
     * @return array 规则列表
     * @author 许立 2018年7月5日
     */
    public function getRulesByResearchId($id)
    {
        return $this->model->where('activity_id', $id)->order('sort asc')->get()->toArray();
    }

    /**
     * 根据活动id删除规则
     * @param int $research_id 活动id
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function deleteRulesByResearchId($research_id)
    {
        return $this->model->where('activity_id', $research_id)->delete();
    }

    /**
     * 根据规则id删除规则
     * @param array $ids 规则id数组
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function deleteRulesByIdArray($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * 处理预约图片
     * @param $imageUrl 图片url
     * @return string 处理后图片url
     * @author 何书哲 2018年8月1日
     */
    private function dealResearchRuleImage($imageUrl) {
        if (strpos($imageUrl, config('app.source_url')) !== false) {
            $imageUrl = str_replace(config('app.source_url'), '', $imageUrl);
        }
        return ltrim(parse_url($imageUrl)['path'], '/');
    }


    /**
     * 添加活动规则
     * @param int $activity_id 活动id
     * @param array $rules 规则数组
     * @return bool
     * @author 许立 2018年07月05日
     * @update 许立 2018年07月09日 返回字符限制提示
     * @update 许立 2018年07月11日 预约活动文本预约规则 删除前端传递的多余数据
     * @update 何书哲 2018年07月17日 去掉subtitle规则描述不能超过60字约束（因为有一些模板里面的规则描述超过60字）
     * @update 何书哲 2018年7月18日 添加调查活动线型下标、标题样式下标、描述样式下标、单位、背景色、图片是否显示
     * @update 何书哲 2018年8月3日 类型为set_image时允许title为空
     */
    public function addRules($activity_id, $rules)
    {
        foreach ($rules as $sort => $rule) {
            //何书哲 2018年8月3日 类型为set_image时允许title为空
            if (empty($rule['type']) || (!empty($rule['type']) && $rule['type'] != 'set_image' && empty($rule['title']))) {
                throw new \Exception('规则参数不完整');
            }

            if (mb_strlen($rule['title'], 'utf-8') > 16) {
                throw new \Exception('规则标题不能超过16字');
            }
            $data = [
                'activity_id' => $activity_id,
                'sort' => $sort,
                'type' => $rule['type'],
                'title' => $rule['title'],
                'subtitle' => $rule['subtitle'] ?? '',
                'required' => (!empty($rule['required']) && ($rule['required'] == 'true' || $rule['required'] == 1)) ? 1 : 0,
                'multiple' => $rule['multiple'] ?? 0,
                'min_options' => $rule['min_options'] ?? 0,
                'max_options' => $rule['max_options'] ?? 0,
                'image' => isset($rule['image']) && $rule['image'] ? $this->dealResearchRuleImage($rule['image']) : '',
                'rule_text_height' => $rule['rule_text_height'] ?? 0,
                'rule_text_width' => $rule['rule_text_width'] ?? 0,
                'rule_time_type' => $rule['rule_time_type'] ?? 0,
                'rule_phone_value' => $rule['rule_phone_value'] ?? 0,
                'rule_image_type' => $rule['rule_image_type'] ?? 0,
                'rule_appoint_type' => $rule['rule_appoint_type'] ?? 0,
                'rule_appoint_default' => $rule['rule_appoint_default'] ?? '',
                'show_right' => $rule['showRight'] ? 1 : 0,
                'card_right' => $rule['cardRight'] ? $rule['cardRight'] : 0,
                'rule_line_idx' => isset($rule['rule_line_idx']) ? $rule['rule_line_idx'] : 4,//何书哲 2018年7月18日 添加调查活动线型下标
                'rule_title_idx' => isset($rule['rule_title_idx']) ? $rule['rule_title_idx'] : 0,//何书哲 2018年7月18日 添加调查活动标题样式下标
                'rule_desc_idx' => isset($rule['rule_desc_idx']) ? $rule['rule_desc_idx'] : 0,//何书哲 2018年7月18日 添加调查活动描述样式下标
                'unit' => isset($rule['unit']) ? $rule['unit'] : '人',//何书哲 2018年7月18日 添加调查活动单位
                'bg_color' => isset($rule['bg_color']) ? $rule['bg_color'] : '#fff',//何书哲 2018年7月18日 添加调查活动背景色
                'rule_image_flag' => isset($rule['rule_image_flag']) && $rule['rule_image_flag'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动图片是否显示
                'rule_option_check' => isset($rule['rule_option_check']) && $rule['rule_option_check'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动选项是否选中
            ];

            $rule_id = $this->model->insertGetId($data);
            if (empty($rule_id)) {
                return false;
            }

            //投票和预约等规则的子规则
            if (!empty($rule['sub_rules'])) {
                // 预约活动文本预约规则 删除前端传递的多余数据
                // if ($rule['type'] == 'appoint_text') {
                //     unset($rule['sub_rules'][0]);
                //     $rule['sub_rules'] = array_values($rule['sub_rules']);
                // }
                foreach ($rule['sub_rules'] as $k => $v) {
                    if (($v['type'] == 'vote_text' || $v['type'] == 'appoint_text') && empty($v['title'])) {
                        throw new \Exception('标题不能为空');
                    }
                    $sub_rule = [
                        'activity_id' => $activity_id,
                        'parent_id' => $rule_id,
                        'sort' => $k,
                        'type' => $v['type'],
                        'title' => $v['title'],
                        'image' => isset($v['image']) && $v['image'] ? $this->dealResearchRuleImage($v['image']) : '',
                        'rule_image_flag' => isset($v['rule_image_flag']) && $v['rule_image_flag'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动图片是否显示
                        'rule_option_check' => isset($v['rule_option_check']) && $v['rule_option_check'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动选项是否选中
                    ];
                    $this->model->insertGetId($sub_rule);
                }
            }
        }

        return true;
    }

    /**
     * 编辑活动规则
     * @param int $activity_id 活动id
     * @param array $rules 规则数组
     * @return bool
     * @author 许立 2018年07月05日
     * @update 许立 2018年07月11日 预约活动文本预约规则 删除前端传递的多余数据
     * @update 何书哲 2018年7月18日 调查活动线型下标、标题样式下标、描述样式下标、单位、背景色、图片是否显示
     * @update 何书哲 2018年8月3日 类型为set_image时允许title为空
     */
    public function editRules($activity_id, $rules)
    {
        foreach ($rules as $sort => $rule) {
            //何书哲 2018年8月3日 类型为set_image时允许title为空
            if (!empty($rule['type']) && $rule['type'] != 'set_image' && empty($rule['title'])) {
                throw new \Exception('标题不能为空');
            }
            $data = [
                'activity_id' => $activity_id,
                'sort' => $sort,
                'type' => $rule['type'],
                'title' => $rule['title'],
                'subtitle' => $rule['subtitle'] ?? '',
                'required' => (!empty($rule['required']) && ($rule['required'] == 'true' || $rule['required'] == 1)) ? 1 : 0,
                'multiple' => $rule['multiple'] ?? 0,
                'min_options' => $rule['min_options'] ?? 0,
                'max_options' => $rule['max_options'] ?? 0,
                'image' => isset($rule['image']) && $rule['image'] ? $this->dealResearchRuleImage($rule['image']) : '',
                'rule_text_height' => $rule['rule_text_height'] ?? 0,
                'rule_text_width' => $rule['rule_text_width'] ?? 0,
                'rule_time_type' => $rule['rule_time_type'] ?? 0,
                'rule_phone_value' => $rule['rule_phone_value'] ?? 0,
                'rule_image_type' => $rule['rule_image_type'] ?? 0,
                'rule_appoint_type' => $rule['rule_appoint_type'] ?? 0,
                'rule_appoint_default' => $rule['rule_appoint_default'] ?? '',
                'show_right' => $rule['showRight'] ? 1 : 0,
                'card_right' => $rule['cardRight'] ? $rule['cardRight'] : 0,
                'rule_line_idx' => isset($rule['rule_line_idx']) ? $rule['rule_line_idx'] : 4,//何书哲 2018年7月18日 添加调查活动线型下标
                'rule_title_idx' => isset($rule['rule_title_idx']) ? $rule['rule_title_idx'] : 0,//何书哲 2018年7月18日 添加调查活动标题样式下标
                'rule_desc_idx' => isset($rule['rule_desc_idx']) ? $rule['rule_desc_idx'] : 0,//何书哲 2018年7月18日 添加调查活动描述样式下标
                'unit' => isset($rule['unit']) ? $rule['unit'] : '人',//何书哲 2018年7月18日 添加调查活动单位
                'bg_color' => isset($rule['bg_color']) ? $rule['bg_color'] : '#fff',//何书哲 2018年7月18日 添加调查活动背景色
                'rule_image_flag' => isset($rule['rule_image_flag']) && $rule['rule_image_flag'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动图片是否显示
                'rule_option_check' => isset($rule['rule_option_check']) && $rule['rule_option_check'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动选项是否选中
            ];
            if (empty($rule['id'])) {
                // 新增规则
                $rule_id = $this->model->insertGetId($data);
            } else {
                // 修改规则
                $rule_id = $rule['id'];
                $this->model->where('id', $rule_id)->update($data);
            }

            // 投票和预约等规则的子规则
            if (!empty($rule['sub_rules'])) {
                // 预约活动文本预约规则 删除前端传递的多余数据
                // if ($rule['type'] == 'appoint_text') {
                //     unset($rule['sub_rules'][0]);
                //     $rule['sub_rules'] = array_values($rule['sub_rules']);
                // }
                foreach ($rule['sub_rules'] as $k => $v) {
                    if (($v['type'] == 'vote_text' || $v['type'] == 'appoint_text') && empty($v['title'])) {
                        throw new \Exception('标题不能为空');
                    }

                    $sub_rule = [
                        'activity_id' => $activity_id,
                        'parent_id' => $rule_id,
                        'sort' => $k,
                        'type' => $v['type'],
                        'title' => $v['title'],
                        'image' => isset($v['image']) && $v['image'] ? $this->dealResearchRuleImage($v['image']) : '',
                        'rule_image_flag' => isset($v['rule_image_flag']) && $v['rule_image_flag'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动图片是否显示
                        'rule_option_check' => isset($v['rule_option_check']) && $v['rule_option_check'] == 'true' ? 1 : 0,//何书哲 2018年7月19日 添加调查活动选项是否选中
                    ];
                    if (empty($v['id'])) {
                        // 新增子规则
                        $this->model->insertGetId($sub_rule);
                    } else {
                        // 编辑子规则
                        $this->model->where('id', $v['id'])->update($sub_rule);
                    }
                }
            }
        }
    }

    /**
     * 获取必填的规则ID列表
     * @param int $activity_id 活动id
     * @return array
     * @author 许立 2018年7月5日
     */
    public function getRequiredRulesID($activity_id)
    {
        return $this->model->where('activity_id', $activity_id)->where('required', 1)->pluck('id')->toArray();
    }

    /**
     * 获取规则的子选项
     * @param int $parent_id 父规则id
     * @param string $type 子选项类型 all:全部,option:选项类型,other:其他
     * @return array
     * @author 许立 2018年6月29日
     */
    public function getOptionsByParentId($parent_id, $type = 'all')
    {
        $select = $this->model
            ->select('id', 'type', 'title')
            ->where('parent_id', $parent_id);
        // 查询具体类型
        $type != 'all' && $select = $select->where('type', $type);
        // 返回列表
        return $select->order('sort asc')
            ->get()
            ->toArray();
    }
}