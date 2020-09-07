<?php

namespace App\Module;

use App\S\Market\ResearchRecordService;
use App\S\Market\ResearchRuleService;
use App\S\Market\ResearchService;
use App\S\MarketTools\MessagesPushService;
use App\S\Weixin\ShopService;
use App\Services\Permission\WeixinUserService;
use App\Jobs\SendTplMsg;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Exception;

/**
 * 调查留言活动
 * @author 许立 2018年7月5日
 */
class ResearchModule
{
    /**
     * 新建一个调查活动
     * @param array $data 活动数据
     * @return bool|int
     * @author 许立 2018年07月05日
     * @update 许立 2018年07月09日 返回报错
     * @update 许立 2018年08月03日 返回新增的活动id
     */
    public function addResearch($data)
    {
        if (empty($data['rules'])) {
            error('请设置规则');
        }

        $rules = $data['rules'];
        unset($data['rules']);

        try {
            DB::beginTransaction();

            // 插入活动表
            $activity_id = (new ResearchService())->model->insertGetId($data);

            // 插入活动规则表
            $res = (new ResearchRuleService())->addRules($activity_id, $rules);
            if ($res) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            error($e->getMessage());
            return false;
        }

        return $activity_id;
    }

    /**
     * 编辑一个调查活动
     * @param array $data 活动数据
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function editResearch($data)
    {
        if (empty($data['rules'])) {
            error('请设置规则');
        }

        $rules = $data['rules'];
        unset($data['rules']);

        try {
            DB::beginTransaction();

            // 更新活动表
            $activity_id = $data['id'];
            unset($data['id']);
            (new ResearchService())->model->where('id', $activity_id)->update($data);

            // 获取被删除的规则
            $deleted_rules_ids = $this->_getDeletedRuleIds($activity_id, $rules);

            // 删除规则
            $research_rule_service = new ResearchRuleService();
            $research_rule_service->deleteRulesByIdArray($deleted_rules_ids);

            // 删除调查活动的参与记录
            (new ResearchRecordService())->deleteByRuleIds($deleted_rules_ids);

            // 编辑规则
            $research_rule_service->editRules($activity_id, $rules);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 删除一个调查活动
     * @param array $data 活动数据
     * @return bool
     * @author 许立 2018年7月5日
     */
    public function deleteResearch($activity_id)
    {
        try {
            DB::beginTransaction();

            // 删除活动表
            (new ResearchService())->model->where('id', $activity_id)->delete();

            // 删除规则
            (new ResearchRuleService())->deleteRulesByResearchId($activity_id);

            // 删除调查的参与记录
            (new ResearchRecordService())->deleteByResearchId($activity_id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 获取调查活动详情
     * @param int $activity_id 活动id
     * @return array
     * @author 许立 2018年7月5日
     */
    public function getResearch($activity_id)
    {
        // 获取活动
        $research = (new ResearchService())->getDetail($activity_id);
        if (!empty($research)) {
            // 获取活动规则
            $rules = (new ResearchRuleService())->getRulesByResearchId($activity_id);

            // 处理子规则
            $research['rules'] = array_values($this->_handleSubRules($rules));
        }

        return $research;
    }

    /**
     * 把子规则拼接到父规则中
     * @param array $rules 规则列表
     * @return array
     * @author 许立 2018年7月5日
     */
    private function _handleSubRules($rules)
    {
        $rules = array_column($rules, null, 'id');
        foreach ($rules as $k => $rule) {
            if ($rule['parent_id']) {
                // 规则选项添加到父规则下
                $rules[$rule['parent_id']]['sub_rules'][] = $rule;
                // unset选项规则
                unset($rules[$k]);
            } else {
                $rules[$k]['required'] = $rule['required'] ? true : false;
            }
        }

        return $rules;
    }

    /**
     * 获取调查活动的参与人列表
     * @param int $activity_id 活动ID
     * @param string $name 用户名 许立 2018年6月27日
     * @return array
     * @author 许立 2018年6月27日
     * @update 许立 2018年6月27日 增加用户名检索功能
     * @update 许立 2019年03月04日 参与人列表按参与时间倒序排序
     */
    public function getMembers($activity_id, $name = '')
    {
        $select = DB::table('activity_research_record as r')
            ->select(['r.research_id', 'm.id', 'm.truename', 'm.nickname', 'm.headimgurl', 'r.created_at', 'r.times'])
            ->leftJoin('member as m', 'm.id', '=', 'r.mid')
            ->where('r.research_id', $activity_id);
        // 许立 2018年6月27日 增加用户名检索功能
        $name && $select = $select->where('m.truename', 'like', '%' . $name . '%');
        $select = $select->groupBy(['r.mid', 'r.times']);

        // 总数
        $count = $select->count(); // 这句不能少 不然下面获取的总数不正确
        $count = DB::select('select FOUND_ROWS() as group_count')[0]->group_count;

        // 分页
        $paginator = new LengthAwarePaginator([], $count, 20, null, ['path' => app('request')->url()]);
        $list = $paginator->appends(app('request')->input());
        $pageHtml = $list->links();

        // 列表数据
        $input = app('request')->input();
        $page = !empty($input['page']) ? (int)$input['page'] : 1;
        $page < 1 && $page = 1;
        $per_page = 20;
        $skip = ($page - 1) * $per_page;
        $data = $select
            ->order('r.id desc')
            ->skip($skip)
            ->take($per_page)
            ->get()
            ->toArray();

        return ['data' => $data, 'pageHtml' => $pageHtml];
    }

    /**
     * 获取调查活动的参与人列表-不分页 导出使用
     * @param int $activity_id 活动ID
     * @param string $name 用户名
     * @return array
     * @author 许立 2018年7月4日
     */
    public function getMemberRecords($activity_id, $name = '')
    {
        $select = DB::table('activity_research_record as r')
            ->select(['r.research_id', 'm.id', 'm.truename', 'm.nickname', 'm.headimgurl', 'r.created_at'])
            ->leftJoin('member as m', 'm.id', '=', 'r.mid')
            ->where('r.research_id', $activity_id);
        // 许立 2018年6月27日 增加用户名检索功能
        $name && $select = $select->where('m.truename', 'like', '%' . $name . '%');
        return $select->groupBy('r.mid')
            ->get()
            ->toArray();
    }

    /**
     * 营销活动-调查活动-参与记录详情
     * @param int $activity_id 活动id
     * @param int $mid 用户id
     * @param int $times 第几次参与
     * @param string $name 用户名
     * @return array
     * @author 许立 2018年06月27日
     * @update 许立 2018年06月27日 如果是统计最后一次类型的活动 则只统计最后一次
     * @update 何书哲 2018年7月20日 添加预约时段类型
     * @update 何书哲 2018年7月23日 修改多选判断条件
     * @update 许立 2018年11月13日 活动规则非空判断
     */
    public function researchRecords($activity_id, $mid = 0, $times = 0, $name = '')
    {
        // 获取活动
        $research = (new ResearchService())->getDetail($activity_id);
        if (empty($research)) {
            return [
                'activity_title' => '',
                'records' => [],
                'vote_result' => []
            ];
        }

        // 获取回答列表
        $select = DB::table('activity_research_record as re')
            ->select(['re.rule_id', 're.content', 'ru.type', 'ru.title', 'ru.rule_appoint_type', 'ru.multiple', 'm.nickname', 're.created_at', 're.times', 're.mid'])
            ->leftJoin('activity_research_rule as ru', 'ru.id', '=', 're.rule_id')
            ->leftJoin('member as m', 'm.id', '=', 're.mid')
            ->where('re.research_id', $activity_id);
        // 筛选用户
        $mid && $select = $select->where('re.mid', $mid);
        // 筛选第几次参与
        $times && $select = $select->where('re.times', $times);
        // 用户名检索
        $name && $select = $select->where('m.nickname', 'like', '%' . $name . '%');
        $list = $select->get()->toArray();

        // 处理有选项的规则
        // 选项票数统计数组
        $option_count_array = [];
        $rule_service = new ResearchRuleService();
        $rule_model = $rule_service->model;
        foreach ($list as $k => $v) {
            //何书哲 2018年7月20日 添加预约时段类型appoint_time
            if (in_array($v->type, ['vote_text', 'vote_image', 'appoint_text', 'appoint_image', 'appoint_time'])) {
                $content = json_decode($v->content, true);
                if (empty($content)) {
                    continue;
                }
                // 答案转化成数组 $content=[['id'=>111],['id'=>112,'content'=>'其他用户填写的答案']]
                !is_array($content) && $content = [$content];
                $content_new = [];
                foreach ($content as $answer) {
                    $answer_id = (int)$answer['id'];
                    $option = $rule_model->where('id', $answer_id)->first();
                    if (empty($answer_id) || empty($option)) {
                        continue;
                    }
                    $option_new = [
                        'id' => $answer_id,
                        'title' => $option->title,
                        'image' => ''
                    ];
                    if (!empty($answer['content'])) {
                        // 答案是其他
                        $option_new['type'] = 'other';
                        $option_new['title'] = $answer['content'];
                    } else {
                        // 答案是选项
                        $option_new['type'] = 'option';
                        $option_new['image'] = $option->image;
                    }
                    $content_new[] = $option_new;

                    // 如果是统计最后一次类型的活动 则只统计最后一次
                    if ($research['times_type'] == 1 && $this->_maxTimes($activity_id, $v->mid) != $v->times) {
                        continue;
                    }

                    // 组装选项统计结果数组
                    if (empty($option_count_array[$v->rule_id])) {
                        // 第一条答案统计
                        $option_count_array[$v->rule_id] = [
                            'title' => $v->title,
                            'multiple' => $v->multiple || ($v->rule_appoint_type == 2),//何书哲 2018年7月23日 修改多选判断条件
                            'total' => 1,
                            'options' => [
                                $option_new['id'] => [
                                    'type' => $option_new['type'],
                                    'title' => $option_new['title'],
                                    'vote_count' => 1,
                                ]
                            ]
                        ];
                    } else {
                        // 已存在答案统计
                        $option_count_array[$v->rule_id]['total'] += 1;
                        if (empty($option_count_array[$v->rule_id]['options'][$option_new['id']])) {
                            // 该选项第一次统计
                            $option_count_array[$v->rule_id]['options'][$option_new['id']] = [
                                'title' => $option_new['title'],
                                'type' => $option_new['type'],
                                'vote_count' => 1,
                            ];
                        } else {
                            // 该选项已存在统计
                            $option_count_array[$v->rule_id]['options'][$option_new['id']]['vote_count'] += 1;
                        }
                    }
                }
                $list[$k]->content = json_encode($content_new);
            }
        }

        // 组装没有被用户投票的选项
        foreach ($option_count_array as $rule_id => $v) {
            // 获取子选项列表
            $options = $rule_service->getOptionsByParentId($rule_id);
            $options_new = [];
            foreach ($options as $option) {
                if (isset($v['options'][$option['id']])) {
                    // 计算投票比例
                    $v['options'][$option['id']]['vote_ratio'] = sprintf('%.2f', $v['options'][$option['id']]['vote_count'] / $v['total']);
                    // 该子选项有人投票 则原样返回
                    $options_new[] = $v['options'][$option['id']];
                } else {
                    // 无人投票的子选项 返回0票
                    $options_new[] = [
                        'type' => $option['type'],
                        'title' => $option['title'],
                        'vote_count' => 0,
                        'vote_ratio' => 0
                    ];
                }
            }
            $v['options'] = $options_new;
            $option_count_array[$rule_id] = $v;
        }

        // 去除数组下标 方便前端处理数组
        $option_count_array = array_values($option_count_array);
        foreach ($option_count_array as $k => $rules) {
            $rules['options'] = array_values($rules['options']);
            $option_count_array[$k] = $rules;
        }

        return [
            'activity_title' => $research['title'],
            'records' => $list,
            'vote_result' => $option_count_array
        ];
    }

    /**
     * 验证并插入调查回答内容
     * @param array $input 回答内容
     * @return array
     * @author 许立 2018年07月05日
     * @update 许立 2018年07月11日 活动类型为取最后一次的 不删除之前参与记录 只是统计投票结果只取最后一次
     */
    public function submitAnswer($input)
    {
        // 返回格式
        $return = [
            'err_code' => 1,
            'err_msg'  => '',
            'data'     => []
        ];

        // 验证活动是否存在
        if (empty($input['id']) || empty($input['data'])) {
            $return['err_msg'] = '參數不完整';
            return $return;
        }
        $research = (new ResearchModule())->getResearch($input['id']);
        if (empty($research)) {
            $return['err_msg'] = '活动不存在';
            return $return;
        }

        // 验证活动是否过期或失效
        if ($research['invalidate_at'] > '0000-00-00 00:00:00') {
            $return['err_msg'] = '活动已失效';
            return $return;
        }
        $now = date('Y-m-d H:i:s');
        if ($research['start_at'] > $now) {
            $return['err_msg'] = '活动未开始';
            return $return;
        }
        if ($research['end_at'] <= $now) {
            $return['err_msg'] = '活动已结束';
            return $return;
        }

        // 判断参与次数
        $record_service = new ResearchRecordService();
        // 获取参与次数
        $answer_times = $record_service->getCount($input['id'], $input['mid']);
        if ($answer_times == 1 && $research['times_type'] == 0) {
            $return['err_msg'] = '该活动只能参与一次';
            return $return;
        }

        if ($research['times_type'] == 2 && $answer_times == 10) {
            $return['err_msg'] = '该活动最多参与10次';
            return $return;
        }

        // 转化成数组
        $input['data'] = json_decode($input['data'], true);
        // 验证必填的规则是否填写
        $required_rule_id_array = (new ResearchRuleService())->getRequiredRulesID($input['id']);
        foreach ($required_rule_id_array as $required_id) {
            if (empty($input['data'][$required_id])) {
                $return['err_msg'] = '有必填项没填';
                return $return;
            }
        }

        //保存答案
        $res = $record_service->add($input, $answer_times + 1);
        if (!$res) {
            $return['err_msg'] = '插入答案失败';
            return $return;
        }

        $return['err_code'] = 0;
        $return['data'] = $research;

        return $return;
    }

    /**
     * 发送模板消息
     * @param $data
     * @author 何书哲 2018年10月12日 
     */
    public function sendResearchWechatMsg($data) {
        if (empty($data)) {
            return;
        }
        if ($data['type'] == 0) {//在线报名(商家)
            $shopService = new ShopService();
            $shopData = $shopService->getRowById($data['wid']);
            $data['shop_name'] = $shopData ? $shopData['shop_name'] : '';
            $weixinUserService=  new WeixinUserService();
            $userData = $weixinUserService->init()->where(['wid'=>$data['wid'], 'open_id' => ['<>',null]])->getList(false);
            if (!$userData[0]['data']) {
                return;
            }
            foreach ($userData[0]['data'] as $v)
            {
                $data['open_id'] = $v['open_id'];
                (new MessagePushModule($data['wid'], MessagesPushService::EnrollOnline))->sendMsg($data);
            }
        }
    }

    /**
     * 用户的调查提交记录
     * @param int $mid 用户id
     * @return array
     * @author 许立 2018年7月5日
     */
    public function memberResearches($mid)
    {
        // 获取回答列表
        return DB::table('activity_research_record as re')
            ->select(['r.id', 'r.title', 're.created_at', 're.times'])
            ->leftJoin('activity_research as r', 'r.id', '=', 're.research_id')
            ->where('re.mid', $mid)
            ->groupBy(['research_id', 'times'])
            ->orderBy('re.created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 处理导出数据
     * @param array $list 待处理的数据
     * @param string $type 类型 record:参与记录,vote_result:投票结果
     * @return array
     * @author 许立 2018年7月5日
     * @update 何书哲 2018年7月23日 添加appoint_time类型
     * @update 何书哲 2018年7月25日 用于标志nickname导出只显示一次
     * @update 许立 2018年08月23日 时间组件导出修复
     */
    public function dealWithExportData($list, $type = 'record')
    {
        $records = [];
        if ($type == 'record') {
            $rule_service = new ResearchRuleService();
            $rule_model = $rule_service->model;
            //何书哲 2018年7月25日 用于标志nickname导出只显示一次
            $mid = 0;
            foreach ($list as $v) {
                $record = [
                    'truename' => ($v->mid == $mid) ? '' : $v->nickname,
                    'title' => $v->title,
                    'content' => $v->content,
                    'times' => $v->times,
                    'created_at' => $v->created_at
                ];
                $mid = $v->mid;
                // 根据设置的规则类型处理提交内容
                if ($v->type == 'time') {
                    $content = json_decode($v->content, true);
                    if ($content['rule_time_type']) {
                        $record['content'] = $content['start_time'] . ' ~ ' . $content['end_time'];
                    } else {
                        $record['content'] = $content['start_time'];
                    }
                } elseif ($v->type == 'image') {
                    $record['content'] = imgUrl($v->content);
                } elseif ($v->type == 'address') {
                    $content = json_decode($v->content, true);
                    $record['content'] = implode(',', $content['region']);
                } elseif (in_array($v->type, ['vote_text', 'vote_image', 'appoint_text', 'appoint_image', 'appoint_time'])) {//何书哲 2018年7月23日 添加appoint_time类型
                    $content = json_decode($v->content, true);
                    $option_title = '';
                    if ($content) {
                        foreach ($content as $option) {
                            if ($option_title) {
                                $option_title = $option_title . ', ';
                            }
                            $option_title .= $option['title'] ?? '';
                        }
                    }
                    $record['content'] = $option_title;
                }
                $records[] = $record;
            }
            return [
                'data' => $records,
                'title' => [
                    'truename' => '用户',
                    'title' => '题干',
                    'content' => '内容',
                    'times' => '第几次提交',
                    'created_at' => '提交时间'
                ]
            ];
        } elseif ($type == 'vote_result') {
            $title = '';
            foreach ($list as $rule) {
                foreach ($rule['options'] as $vote) {
                    $vote_new['title'] = ($rule['title'] == $title) ? '' : $rule['title'];
                    $vote_new['multiple'] = ($rule['title'] == $title) ? '' : ($rule['multiple'] ? '多选' : '单选');
                    $vote_new['vote_title'] = $vote['title'];
                    $vote_new['vote_count'] = $vote['vote_count'];
                    $records[] = $vote_new;
                    $title = $rule['title'];
                }
                $records[] = [
                    'title' => $rule['title'].'总票数',
                    'multiple' => '',
                    'vote_title' => '',
                    'vote_count' => $rule['total']
                ];
            }
            return [
                'data' => $records,
                'title' => [
                    'title' => '题干',
                    'multiple' => '题型',
                    'vote_title' => '选项内容',
                    'vote_count' => '票数'
                ]
            ];
        }
    }

    /**
     * 获取被删除的规则
     * @param int $activity_id 活动id
     * @param array $rules 规则数组
     * @return array
     * @author 许立 2018年7月5日
     */
    private function _getDeletedRuleIds($activity_id, $rules)
    {
        $old_rules = (new ResearchRuleService())->getRulesByResearchId($activity_id);
        $old_rules_ids = array_column($old_rules, 'id');
        // 新规则id
        $new_rules_ids = [];
        foreach ($rules as $rule) {
            !empty($rule['id']) && $new_rules_ids[] = $rule['id'];
            if (!empty($rule['sub_rules'])) {
                foreach ($rule['sub_rules'] as $sub_rule) {
                    !empty($sub_rule['id']) && $new_rules_ids[] = $sub_rule['id'];
                }
            }
        }

        return array_diff($old_rules_ids, $new_rules_ids);
    }

    /**
     * 获取用户再某活动中的参与次数
     * @param int $research_id 活动id
     * @param int $mid 用户id
     * @return int
     * @author 许立 2018年07月11日
     */
    private function _maxTimes($research_id, $mid)
    {
        $row = (new ResearchRecordService())->model
            ->where('research_id', $research_id)
            ->where('mid', $mid)
            ->orderBy('times', 'desc')
            ->first();
        return $row ? $row->times : 0;
    }
}