<?php

namespace App\S\Market;

use App\S\S;

/**
 * 调查提交记录
 * @author 许立 2018年7月5日
 */
class ResearchRecordService extends S
{
    public function __construct()
    {
        parent::__construct('ActivityResearchRecord');
    }

    /**
     * 插入一个用户的一次调查回答
     * @param array $input 回答内容
     * @param int $answer_times 第几次参与
     * @return bool
     * @author 许立 2018年7月5日
     * @update 何书哲 2018年7月19日 添加计数单位、预约时段
     */
    public function add($input, $answer_times)
    {
        $research_id = $input['id'];
        $mid = $input['mid'];
        $model = $this->model;

        foreach ($input['data'] as $k => $v) {
            $data = [
                'research_id' => $research_id,
                'mid' => $mid,
                'rule_id' => (int)$k,
                'times' => $answer_times
            ];

            // 只保存需要的数据
            switch ($v['type']) {
                case 'num'://何书哲 2018年7月19日 添加单位（比如人数）
                case 'text':
                    $data['content'] = $v['val'];
                    break;
                case 'image':
                    $data['content'] = $v['url'];
                    break;
                case 'appoint_time'://何书哲 2018年7月19日 添加预约时段（单选、多选、下拉）
                case 'vote_text':
                case 'vote_image':
                case 'appoint_text':
                case 'appoint_image':
                    $data['content'] = $v['option'] ? json_encode($v['option']) : '';
                    break;
                default:
                    $data['content'] = json_encode($v);
                    break;
            }

            $res = $model->insert($data);
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    /**
     * 根据规则ID删除记录
     * @param array $ids 规则id数组
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function deleteByRuleIds($ids)
    {
        return $this->model->whereIn('rule_id', $ids)->delete();
    }

    /**
     * 根据活动id删除记录
     * @param int $activity_id 活动id
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function deleteByResearchId($activity_id)
    {
        return $this->model->where('research_id', $activity_id)->delete();
    }

    /**
     * 获取参与次数
     * @param int $activity_id 活动id
     * @param int $mid 用户id
     * @return int
     * @author 许立 2018年7月5日
     */
    public function getCount($activity_id, $mid)
    {
        $res = $this->model
            ->select(\DB::raw('max(times) as answer_times'))
            ->where('research_id', $activity_id)
            ->where('mid', $mid)
            ->first()
            ->toArray();

        return (int)$res['answer_times'] ?: 0;
    }
}