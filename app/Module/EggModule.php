<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 10:36
 */

namespace App\Module;


use App\S\Market\CouponService;
use App\S\Market\EggMemberService;
use App\S\Market\EggPrizeService;
use App\S\Market\ScoreService;
use App\S\Market\SmokedEggsService;
use App\S\Member\MemberService;
use Illuminate\Support\Facades\DB;
use Validator;


class EggModule
{

    public $request;

    public function __construct()
    {
        $this->request = app('request');
    }

    /**
     * 砸金蛋活动添加
     * @param $input
     * @return bool
     * @author: 梅杰 time
     */
    public function createActivity($input)
    {
        $activityData = $this->handleData($input);
        $activityPrizeData = $this->handlePrizeData($input);
        try {
            DB::transaction(function () use ($activityData, $activityPrizeData) {
                $eggId = (new SmokedEggsService())->addActivity($activityData);
                //插入本次活动的奖项设置信息
                if (!$eggId) {
                    throw new \Exception('砸金蛋创建失败');
                }
                //1、插入奖品信息
                if (!(new EggPrizeService())->create($activityPrizeData, $eggId)) {
                    throw new \Exception('砸金蛋奖品创建失败');
                }
            });
        } catch (\Exception $exception) {
            \Log::Info('砸金蛋创建失败' . $exception->getMessage());
            return false;
        }
        return true;
    }


//    public

    //验证数据
    public function validateData($input)
    {
        $rule = [
            'title' => 'required',
            'detail' => 'required',
            'start_img_url' => 'required',
            'is_show' => 'required',
            'prize_limit' => 'required',
            'join_limit' => 'required',
            "start_at" => 'required',
            "end_at" => 'required',
            "prize_name" => 'required',
            "prize_title" => 'required',
            "prize_id" => 'required',
            "prize_type" => 'required',
            "prize_probability" => 'required',
            "prize_number" => 'required'
        ];
        $message = [
            'title.required' => '请输入活动标题',
            'detail.required' => '请输入活动详情',
            'start_img_url.required' => '请上传活动开始图片',
            'is_show.required' => '请勾选是否显示中奖名单',
            'prize_limit.required' => '中奖次数限制缺少',
            'join_limit.required' => '参加次数限制缺少',
            "start_at.required" => '请输入开始时间',
            "end_at.required" => '请输入结束时间',
            "prize_name" => '请输入奖品名称',
            "prize_id" => '请选择奖品',
            "prize_type" => '奖品类型缺少',
            "prize_title" => '奖品内容不能为空',
            "prize_probability" => '请设置中奖概率',
            "prize_number" => '请设置奖品数量'
        ];

        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
    }

    /**
     * 处理添加活动主要数据
     * @param $input
     * @return array
     * @author: 梅杰 2018年8月16日
     */
    private function handleData($input)
    {
        $data = [
            'title' => $input['title'],
            'detail' => $input['detail'],
            'start_img_url' => $input['start_img_url'],
            'end_desc' => $input['end_desc'],
            'wid' => session('wid'),
            'is_show' => $input['is_show'],
            'limit_json' => [
                'prize_limit' => $input['prize_limit'],
                'join_limit' => $input['join_limit'],
            ],
            "start_at" => $input['start_at'],
            "end_at" => $input['end_at'],
            'contact_way' => isset($input['contact_way']) ? 1 : 0,
        ];
        if (!empty($input['shareImg']) || !empty($input['share_title']) || !empty($input['share_detail'])) {
            $data['share_json'] = [
                'share_img' => $input['shareImg'],
                'title' => $input['share_title'],
                'share_desc' => $input['share_detail'],
            ];
        } else {
            $data['share_json'] = Null;
        }
        return $data;
    }

    /**
     * 处理奖品主要数据
     * @param $input
     * @return array
     * @author: 梅杰 2018年8月16日
     */
    private function handlePrizeData($input)
    {
        foreach ($input['prize_name'] as $k => $v) {
            //判断所选的奖品库存是否合理
            $rs = $this->getPrizeDetailByData(['type' => $input['prize_type'][$k], 'type_id' => intval($input['prize_id'][$k])]);
            if ($input['prize_type'][$k] != 3 && $input['prize_number'][$k] > $rs['data']['left']) {
                error("第" . ($k + 1) . "个奖品数量大于库存");
            }
            $temp = [
                'name' => $v,
                'type' => $input['prize_type'][$k],
                'type_id' => $input['prize_id'][$k],
                'amount' => $input['prize_number'][$k],
                'content' => $input['prize_title'][$k],
                'left' => $input['prize_number'][$k],
                'percent' => $input['prize_probability'][$k],
                'img' => $input['prize_img'][$k] ?? ' ',
                'method' => $input['prize_method'][$k] ?? ' '
            ];
            if (isset($input['prize_log_id'][$k])) {
                $temp['id'] = $input['prize_log_id'][$k];
            }
            $prize[] = $temp;
        }
        $temp = [
            'type' => 0,
            'type_id' => 0,
            'percent' => (100 - array_sum($input['prize_probability'])),
            'name' => $input['noPrizeName'],
            'content' => $input['noPrizeContent'],
            'amount' => 0,
            'left' => 0,
            'img' => ' ',
            'method' => ' '
        ];
        if (isset($input['noPrizeId'])) {
            $temp['id'] = $input['noPrizeId'];
        }
        array_push($prize, $temp);
        return $prize;
    }


    /**
     * @param $id
     * @param array $data
     * @param array $prize
     * @param $prizeAmount
     * @return bool
     * @update: 梅杰 2018年8月13日 事务处理
     */
    public function editEgg($id,$input)
    {
        $activityData = $this->handleData($input);
        $activityPrizeData = $this->handlePrizeData($input);
        $create = [];
        foreach ($activityPrizeData as $k => $v) {
            if (!isset($v['id'])) {
                $create[] = $v;
                unset($activityPrizeData[$k]);
            }
        }
        try {
            DB::transaction(function () use ($id, $activityData, $activityPrizeData,$create) {
                if (isset($activityData['share_json'])) {
                    $activityData['share_json'] = json_encode($activityData['share_json']);
                }
                $activityData['limit_json'] = json_encode($activityData['limit_json']);
                $smokedEggsService = new SmokedEggsService();
                if ($smokedEggsService->updateActivity($id, $activityData) === false) {
                    throw new \Exception('修改失败');
                }
                $eggPrizeService = new EggPrizeService();
                // @udpate 张永辉 2020年5月20日18:59:49 删除奖品
                $eggPrizeService->delPrize($id, array_column($activityPrizeData, 'id'));
                if ($create && !$eggPrizeService->create($create,$id)) {
                    throw new \Exception('奖品新增失败');
                }
                if (!$eggPrizeService->update($activityPrizeData)) {
                    throw new \Exception('奖品更新失败');
                }
            });

        } catch (\Exception $exception) {
            \Log::Info('砸金蛋更新失败' . $exception->getMessage());
            return false;
        }
        return true;

    }


    /**
     * 获取某个活动的具体信息（后台编辑显示）
     * @param $eggId
     * @return mixed
     * @author: 梅杰 2018年8月16日
     */
    public function getEggDetailByEggId($eggId)
    {
        $eggsService = new SmokedEggsService();
        $eggData = $eggsService->getInfoById($eggId);
        if (!$eggData) {
            error('该活动不存在或已被删除');
        }
        $eggPrizeService = new EggPrizeService();
        $prize_info = $eggPrizeService->getListByWhere(['eggId'=>$eggId,'type'=> ['<>',0]]);
        $eggData['prize_info'] = $prize_info;
        $eggData['noPrize'] = $eggPrizeService->getListByWhere(['eggId'=>$eggId,'type'=> 0]);
        return $eggData;
    }

    /**
     * 获取活动的奖品信息
     * author: meijie
     * @param $eggId
     * @return array
     */
    public function getPrizeInfo($eggId)
    {
        $eggsService = new SmokedEggsService();
        //判断是都活动结束
        $re = $eggsService->model->find($eggId);
        if (empty($re)) {
            error('活动不存在');
        }
        $data = $re->toArray();
        if ($data['end_at'] < date('Y-m-d H:i:s')) {
            $data['code'] = 0;
            if (!empty($data['share_json'])) {
                $data['share_json'] = json_decode($data['share_json'], 1);
            } else {
                $data['share_json'] = [];
                $data['share_json']['share_img'] = '';
                $data['share_json']['title'] = '';
                $data['share_json']['share_desc'] = '';
            }
            return $data;
        }
        //获取到与本次活动相关的奖品信息
        $eggPrizeService = new EggPrizeService();
        $prize_info = $eggPrizeService->getListByWhere(['eggId'=>$eggId]);
        return [
            'prize_info' => $prize_info,
        ];
    }


    #todo 移动端接口

    /**
     * 砸金蛋详情
     * @param $id
     * @return array
     * @author: 梅杰 2018年8月16日
     */
    public function getEggDetailById($id)
    {
        $prizeData = $this->getPrizeInfo($id);
        if (isset($prizeData['code']))
            error('', '', $prizeData);
        $wid = session('wid');
        $mid = session('mid');
        $scoreService = new ScoreService();
        $prize_info = [];
        foreach ($prizeData['prize_info'] as $k => $v) {
            if ($v['type'] == 1) {
                $conf = (new CouponService())->getDetail($v['type_id']);
                $conf && $conf['prizeName'] = $v['name'];
                $prize_info['coupon'][] = $conf;
            }
            //如果为积分
            if ($v['type'] == 2) {
                $conf = $scoreService->getInfoById($v['type_id']);
                $conf['prizeName'] = $v['name'];
                $prize_info['score'][] = $conf;
            }
            if ($v['type'] == 3) {
                $v['prizeName'] = $v['name'];
                $prize_info['gift'][] = $v;
            }
        }
        //拼接返回数组
        $returnData = [
            'join_info' => $this->getMemberLeft($id, $mid),
            'prize_info' => $prize_info
        ];
        return $returnData;
    }

    /**
     * @param $eggId
     * @param $mid
     * @return array
     * @author: 梅杰 time
     */
    public function getMemberLeft($eggId, $mid)
    {
        $eggService = new SmokedEggsService();
        $eggMember = new EggMemberService();
        $limitAmount = $eggMember->getAllJoinAmount($eggId, $mid);
        $eggData = $eggService->getInfoById($eggId);
        $limitData = json_decode($eggData['limit_json'], 1);
        switch ($limitData['join_limit']['type']) {
            case 1:
                //当天限制
                $limitAmount = $eggMember->getTodayJoinAmount($eggId, $mid);
                break;
            case 2:
                $limitAmount = $eggMember->getAllJoinAmount($eggId, $mid);
                break;
            default:
                break;
        }
        $re = $limitData['join_limit']['amount'] - $limitAmount;
        return [
            'type' => $limitData['join_limit']['type'],
            'left_amount' => $re < 0 ? 0 : $re
        ];
    }

    /**
     * 抽取奖品
     * @param $id
     * @param $wid
     * @return array|bool
     * @author: 梅杰 2018年8月16号
     */
    public function getPrize($id, $wid)
    {
        //返回数组
        $reData = [
            'status' => 1,
            'msg' => '',
            'data' => []
        ];
        $mid = session('mid');
        #todo 判断活动是否还在进行
        $runStatus = $this->checkIsRun($id);
        if ($runStatus != 1) {
            $reData['msg'] = 'finish';
            $reData['status'] = $runStatus;
            return $reData;
        }
        #todo 当前用户是否中奖数是否符合规则
        $rs = $this->checkPrizeCount($id, $mid);
        /*if ($rs['status'] != 1) {
            $reData['msg'] = $rs['msg'];
            $reData['status'] = $rs['status'];
            return $reData;
        }*/
        //update by 吴晓平 2018.09.28 中奖次数用完统计更改为不中奖
        if ($rs['status'] == 3 || $rs['status'] == 4) {
            $reData['msg'] = '中奖次数已用完，提示未中奖';
            $reData['status'] = 8;
            return $reData;
        }
        #todo 当前用户是否抽奖数是否符合规则
        $rs = $this->checkJoinCount($id, $mid);
        if ($rs['status'] != 1) {
            $reData['msg'] = $rs['msg'];
            $reData['status'] = $rs['status'];
            return $reData;
        }
        $prizeData = $this->getPrizeInfo($id);
        $prize_arr = $prizeData['prize_info'];
        $prize_id = $this->getRand(array_column($prize_arr, 'percent','id')); //根据概率获取奖品id
        //获取到的奖品信息
        $prizeInfo = array_filter($prize_arr,function ($var) use ($prize_id){
            return $var['id'] == $prize_id;
        });
        $prizeInfo = array_values($prizeInfo)[0];
        //判断是否未中奖
        if ($prizeInfo['type'] == 0) {
            $reData['msg'] = $prizeInfo['name'];
            $reData['status'] = 8;
            return $reData;
        }
        //判断库存
        if ($prizeInfo['left'] <= 0) {
            $reData['msg'] = 'no prize';
            $reData['status'] = 8;
            return $reData;
        }
        //2、修改相关库存
        if ($this->updatePrizeStock($prizeInfo)) {
            //2、发放奖励
            $re = $this->sendPrize($mid, $prizeInfo, $rs['log_id']);
            if ($re) {
                $reData['msg'] = '恭喜您';
                $reData['data'] = $prizeInfo;
                return $reData;
            }
        }
        return false;
    }

    /**
     * 检查是否该活动还在进行
     * author: meijie
     * @param $eggId
     * @return integer  status 0 已结束 1 正在进行 2 尚未开始
     */
    public function checkIsRun($eggId)
    {
        $status = 1;
        $now = date('Y-m-d H:i:s');
        $eggService = new SmokedEggsService();
        $re = $eggService->getInfoById($eggId);
        if ($re == false || empty($re)) {
            $status = 0;
        }
        //活动结束
        if ($re['status'] == 1 || $now > $re['end_at']) {
            $status = 0;
        }
        //活动未开始
        if ($now < $re['start_at']) {
            $status = 2;
        }
        return $status;
    }


    /**
     * 检查用户参与次数是否正常
     * author: meijie
     * @param $eggId
     * @param $mid
     * @return array
     */
    public function checkJoinCount($eggId, $mid)
    {
        $reData = [
            'status' => 1,
            'msg' => 'success',
            'log_id' => 0,
        ];
        //获取到本次活动的限制信息
        $eggService = new SmokedEggsService();
        //1、检查参与次数
        $eggMember = new EggMemberService();
        $allJoinAmount = $eggMember->getAllJoinAmount($eggId, $mid);
        //判断
        $eggData = $eggService->getInfoById($eggId);
        $limitData = json_decode($eggData['limit_json'], 1);
        switch ($limitData['join_limit']['type']) {
            case 1:
                //当天限制
                $limitAmount = $eggMember->getTodayJoinAmount($eggId, $mid);
                if ($limitAmount > $limitData['join_limit']['amount']) {
                    $reData = [
                        'status' => 6,
                        'msg' => '今天的参与次数已经用完'
                    ];
                    return $reData;
                }
                break;
            case 2:
                if ($allJoinAmount > $limitData['join_limit']['amount']) {
                    $reData = [
                        'status' => 7,
                        'msg' => '本次活动您的参与次数已经用完'
                    ];
                    return $reData;
                }
                break;
            default:
                break;
        }
        $log_id = $this->createJoinLog($eggId, $mid,$eggData['wid']);
        if (!$log_id) {
            $reData = [
                'status' => 5,
                'msg' => '插入参与记录出错'
            ];
            return $reData;
        }
        $reData['log_id'] = $log_id;
        return $reData;
    }


    /**
     * 新建用户参与记录
     * author: meijie
     * @param $eggId
     * @param $mid
     * @return bool
     */
    public function createJoinLog($eggId, $mid,$wid)
    {
        $eggMember = new EggMemberService();
        $data = [
            'wid' => $wid,
            'mid' => $mid,
            'egg_id' => $eggId,
            'is_prize' => 0,
            'prize_id' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $eggMember->create($data);
    }


    /**
     * 检查用户的中奖次数是否正常
     * author: meijie
     * @param $eggId
     * @param $mid
     * @return array
     */
    public function checkPrizeCount($eggId, $mid)
    {
        $reData = [
            'status' => 1,
            'msg' => 'success'
        ];
        //获取到本次活动的限制信息
        $eggService = new SmokedEggsService();
        //1、检查参与次数
        $eggMember = new EggMemberService();
        //判断
        $eggData = $eggService->getInfoById($eggId);
        $limitData = json_decode($eggData['limit_json'], 1);
        switch ($limitData['prize_limit']['type']) {
            case 1:
                //当天限制
                $limitAmount = $eggMember->getTodayPrizeAmount($eggId, $mid);
                if ($limitAmount >= $limitData['prize_limit']['amount']) {
                    $reData = [
                        'status' => 3,
                        'msg' => '今天的中奖次数已经用完'
                    ];
                }
                break;
            case 2:
                $limitAmount = $eggMember->getAllPrizeAmount($eggId, $mid);
                if ($limitAmount >= $limitData['prize_limit']['amount']) {
                    $reData = [
                        'status' => 4,
                        'msg' => '本次活动您的中奖次数已经用完'
                    ];
                }
                break;
            default:
                break;
        }
        return $reData;
    }


    /**
     * 奖品发放
     * @param $prize_data
     * @return bool
     * @author: 梅杰 time
     */
    public function updatePrizeStock($prize_data)
    {
        try {
            DB::transaction(function () use ( $prize_data) {
                //更改奖品项库存
                $eggPrizeService = new EggPrizeService();
                if (!$eggPrizeService->updateStock($prize_data['id'])) {
                    throw new \Exception('奖品库修改失败');
                }

                //更改奖品本身库存
                if ($prize_data['type'] == 2 && !(new ScoreService())->updateStock($prize_data['type_id'])) {
                    //修改积分仓库库存i
                    throw new \Exception('积分仓库修改失败');
                }
            });
        } catch (\Exception $exception) {
            \Log::info('奖品库存修改失败'.$exception->getMessage());
            return false;
        }
        return true;
    }


    /**
     * @param $mid
     * @param $prize
     * @param $log_id
     * @param $prize_id
     * @return bool
     * @author: 梅杰 2018年8月16号
     */
    public function sendPrize($mid, $prize, $log_id)
    {
        $wid = session('wid');
        // 更改记录表
        $re = $this->updateEggMemberLog($log_id, $prize);
        if ($re == false) {
            return false;
        }
        //发放奖励
        switch ($prize['type']) {
            case 1://发放优惠券
                (new CouponModule())->createCouponLog($mid, $prize['type_id'], 1, $wid);
                //修改优惠券库存
                return (new CouponService())->increment($prize['type_id'], 'left', -1);
                break;
            case 2://积分
                //获取积分
                $score = (new ScoreService())->getInfoById($prize['type_id']);
                return $this->grantScore($wid, $mid, $score['per_score']);
                break;
            default:
                break;
        }
        return true;
    }

    //赠送积分
    public function grantScore($wid, $mid, $score)
    {
        $memberService = new MemberService();
        $memberService->incrementScore($mid, $score);
        //添加积分领取类型
        $input = [
            'wid' => $wid,
            'mid' => $mid,
            'point_type' => 9,
            'is_add' => 1,
            'score' => $score,
        ];
        return \PointRecordService::insertData($input);
    }


    /**
     * @param $log_id
     * @param $prize
     * @return bool
     * @author: 梅杰 2018年8月17日 更新中奖参与表
     */
    public function updateEggMemberLog($log_id, $prize)
    {
        $eggMember = new EggMemberService();
        $data = [
            'is_prize' => 1,
            'prize_id' => $prize['id'],
            'prize_content' => json_encode($prize)

        ];
        return $eggMember->update($log_id, $data);
    }


    /*
     * 概率抽奖
     * author: meijie
     * @param $proArr
     * @return int|string
     */
    public function getRand($proArr)
    {
        //计算中奖概率
        $rs = ''; //中奖结果
        $proSum = array_sum($proArr); //概率数组的总概率精度
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $rs = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $rs;
    }

    /**
     * 根据具体奖品数据中的积分或者优惠券信息
     * author: meijie
     * @param $prizeDetail
     * @return array
     */
    public function getPrizeDetailByData($prizeDetail)
    {
        $wid = session('wid');
        $prize_info = [];
        switch ($prizeDetail['type']) {
            case 1:
                $prize_info = (new CouponService())->getDetail($prizeDetail['type_id']);
                break;
            case 2:
                $scoreService = new ScoreService();
                $prize_info = $scoreService->getInfoById($prizeDetail['type_id']);
                break;
            case 3:

                break;
            default:
                break;

        }

        return [
            'data' => $prize_info,
            'type' => $prizeDetail['type']
        ];
    }

    //通过用户获得的奖品Id获取到奖品信息
    public function getPrizeById($prizeId)
    {
        $eggPrizeService = new EggPrizeService();
        $prizeData = $eggPrizeService->getInfoById($prizeId);
        if (!$prizeData) {
            return [];
        }
        return $prizeData;
    }


    /**
     * 获取中奖记录的详细信息
     * @param $list
     * @return mixed
     * @author: 梅杰 2018年8月17号
     */
    public function getLogDetail($list)
    {
        foreach ($list['data'] as $k => $v) {
            if ($v['is_prize'] == 1) {
                if($v['prize_content']) {
                    $pData = json_decode($v['prize_content'],1);
                } else {
                    $pData = $this->getPrizeById($v['prize_id']);
                }
                $list['data'][$k]['pName'] = $pData['name'] ?? '';
                $list['data'][$k]['type'] = $pData['type'] ?? '';

            }
        }
        return $list;
    }


    /**
     * 会员砸金蛋奖品列表
     * @param $mid
     * @return array
     * @author: 梅杰 2018年8月17日
     */
    public function getMemberAllPrizeInfo($mid)
    {
        $service = new EggMemberService();
        $where = ['mid' => $mid, 'is_prize' => 1];
        $list = $service->listWithPage($where);
        $prize_info = '';
        foreach ($list[0]['data'] as &$v) {
            if ($v['prize_content']) {
                //获取奖品信息
                $pData = json_decode($v['prize_content'],1);
            }else {
                $pData = $this->getPrizeById($v['prize_id']);
            }
            switch ($pData['type']) {
                case 1:
                    $prize_info = (new CouponService())->getDetail($pData['type_id']);
                    break;
                case 2:
                    $scoreService = new ScoreService();
                    $prize_info = $scoreService->getInfoById($pData['type_id']);
                    break;
                case 3: //赠品
                    $pData['title'] = $pData['name'];
                    $prize_info = $pData;
                    break;
                default:
                    break;
            }
            $prize_info['type'] = $pData['type'];
            $v['prize_content'] = $prize_info;
        }
        return $list;
    }




}