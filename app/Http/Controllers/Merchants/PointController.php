<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/1
 * Time: 11:38
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use App\Jobs\CheckGrantCard;
use App\S\Member\MemberCardService;
use App\S\Member\MemberService;
use DB;
use Illuminate\Http\Request;
use OrderPointExtraRuleService;
use OrderPointRuleService;
use PointApplyRuleService;
use PointRecordService as PPointRecordService;
use SharePointRuleService;
use SignService;
use WeixinService;
use App\S\Weixin\ShopService;

class PointController extends Controller
{
    /**
     * todo 积分规则
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2017-06-01
     */
    public function indexPoint(){
        return view('merchants.member.indexPoint',[
            'title'=>'积分管理',
            'leftNav'=>'indexPoint',
            'slidebar'=>'indexPoint'
        ]);
    }

    /**
     * todo 添加积分抵现
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-01
     */
    public function addPointApplyRule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $isOn=$request->input('is_on')??0;
        $rate=$request->input('rate');
        $percent=$request->input('percent');
        $wid =session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $errMsg='';
        if(empty($rate))
        {
            $errMsg.='请设置积分汇率';
        }
        if(empty($percent))
        {
            $errMsg.='请设置抵现比例';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data=['wid'=>$wid,'is_on'=>$isOn,'percent'=>$percent,'rate'=>$rate];
        //查询改店铺下的积分消耗规则信息
        $result=PointApplyRuleService::getRow($wid);
        //存在了则更改原来的数据
        if($result['errCode']==0&&!empty($result['data']))
        {
            $id=$result['data']['id'];
            return PointApplyRuleService::updateData($id,$data);
        }//不存在了则添加数据
        else if($result['errCode']==0&&empty($result['data']))
        {
            return  PointApplyRuleService::insertData($data);
        }
    }

    /**
     * todo 更改积分抵现
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-01
     */
    public function updatePointApplyRule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $id=$request->input('id');
        $isOn=$request->input('is_on')??0;
        $rate=$request->input('rate');
        $percent=$request->input('percent');
        $wid =session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $errMsg='';
        if(empty($id))
        {
            $errMsg.='id为空';
        }
        if(empty($percent))
        {
            $errMsg.='请设置抵现比例';
        }
        if(empty($rate))
        {
            $errMsg.='请设置积分汇率';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data=['wid'=>$wid,'is_on'=>$isOn,'percent'=>$percent,'rate'=>$rate];
        return  PointApplyRuleService::updateData($id,$data);
    }

    /**
     * todo 查询积分规则
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-05
     */
    public function selectPointApplyRule(Request $request)
    {
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        return PointApplyRuleService::getRow($wid);
    }

    /**
     * todo 处理积分规则
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-01
     */
    public  function processPointRule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $processData=$request->input('data'); 
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        if(empty($processData))
        {
            $returnData['errCode']=-2;
            $returnData['errCode']='数据为空';
            return $returnData;
        }
        //事务
        DB::beginTransaction();
        try
        {
            foreach ($processData as $item)
            {
                //消费送积分
                if ($item['type'] == 'consume')
                {
                    $consomeResult = OrderPointRuleService::getRowByCondition(['wid' => $wid]);
                    if ($consomeResult['errCode'] == 0)
                    {
                        $id = 0;
                        //消费积分规则
                        $ruleData = ['wid' => $wid, 'is_on' => $item['is_on'], 'basic_rule' => $item['basic_rule']];
                        if (!empty($consomeResult['data']))
                        {
                            $id = $consomeResult['data']['id'];
                            $orderRuleResult = OrderPointRuleService::updateData($id, $ruleData);
                            if ($orderRuleResult['errCode'] != 0)
                            {
                                throw new \Exception('更改基本规则失败');
                            }
                        }
                        else
                        {
                            $orderRuleResult = OrderPointRuleService::insertData($ruleData);
                            $id = $orderRuleResult['data'];
                            if ($orderRuleResult['errCode'] != 0)
                            {
                                throw new \Exception('添加基本规则失败');
                            }
                        }
                        //查询数据库中消费积分额外规则数据
                        $extraRuleResult = OrderPointExtraRuleService::getListByConditionWithPage(['p_id'=>$id]);
                        //用户传递消费积分额外规则有数据存在
                        if (!empty($item['extra_item']))
                        {
                            $extra = $item['extra_item'];
                            $modifyID = [];
                            $insertData = [];
                            foreach ($extra as $extraItem)
                            {
                                //存在的消费积分额外规则进行更改数据
                                if (!empty($extraItem['id']))
                                {
                                    array_push($modifyID, $extraItem['id']);
                                    $extraRuleData = ['used_money' => $extraItem['used_money'], 'reward_point' => $extraItem['reward_point']];
                                    $updateExtraRuleResult=OrderPointExtraRuleService::updateData($extraItem['id'], $extraRuleData);
                                    if ($updateExtraRuleResult['errCode'] != 0)
                                    {
                                        throw new \Exception('更改额外奖励规则失败');
                                    }
                                } //不存在的消费积分额外规则进行添加数据
                                else
                                {
                                    unset($extraItem['id']);
                                    $extraItem['p_id'] = $id;
                                    $insertData[] = $extraItem;
                                }
                            }
                            if ($extraRuleResult['errCode'] == 0 && !empty($extraRuleResult['data']))
                            {
                                if (empty($modifyID))
                                {
                                    //用户输入的额外规则中没有原来数据，则删除消费积分额外规则表中原来全部的数据
                                    $deleteExtraRule=OrderPointExtraRuleService::deleteByCondition(['p_id' => $id]);
                                    if ($deleteExtraRule['errCode'] != 0)
                                    {
                                        throw new \Exception('删除全部额外奖励规则失败');
                                    }
                                }
                                else
                                {
                                    //用户输入的额外规则中有原来数据，则删除消费积分额外规则表中其它的数据
                                    foreach ($extraRuleResult['data'] as $extraRuleResultItem)
                                    {
                                        if (!in_array($extraRuleResultItem['id'],$modifyID))
                                        {
                                            $deleteExtraRule=OrderPointExtraRuleService::deleteByCondition(['id' => $extraRuleResultItem['id']]);
                                            if ($deleteExtraRule['errCode'] != 0)
                                            {
                                                throw new \Exception('删除额外奖励规则失败');
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($insertData)) {
                                foreach ($insertData as $insertDataItem)
                                {
                                    $addExtraRule=OrderPointExtraRuleService::insertData($insertDataItem);
                                    if ($addExtraRule['errCode'] != 0)
                                    {
                                        throw new \Exception('添加额外奖励规则失败');
                                    }
                                }
                            }
                        }
                        else
                        {
                            if ($extraRuleResult['errCode'] == 0 && !empty($extraRuleResult['data']))
                            {
                                $deleteExtraRule=OrderPointExtraRuleService::deleteByCondition(['p_id' => $id]);
                                if ($deleteExtraRule['errCode'] != 0)
                                {
                                    throw new \Exception('删除原来的全部额外奖励规则失败');
                                }
                            }
                        }
                    }
                }//分享积分
                else if($item['type'] =='share')
                    {
                        $shareResult = SharePointRuleService::getRowByCondition(['wid' => $wid]);
                        if ($shareResult['errCode'] == 0)
                        {
                            //分享送积分规则
                            $shareData=['wid'=>$wid,'is_on'=>$item['is_on'],'basic_rule'=>$item['basic_rule'],'limit_rule'=>$item['limit_rule']];
                            if (!empty($shareResult['data']))
                            {
                                $id=$shareResult['data']['id'];
                                $orderRuleResult = SharePointRuleService::updateData($id,$shareData);
                                if ($orderRuleResult['errCode'] != 0) {
                                    throw new \Exception('更改分享送积分失败');
                                }
                            }
                            else
                            {
                                $orderRuleResult = SharePointRuleService::insertData($shareData);
                                if ($orderRuleResult['errCode'] != 0) {
                                    throw new \Exception('添加分享送积分失败');
                                }
                            }
                        }
                    }
            }
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();//事务回滚
            //echo $e->getCode();
            $message=$e->getMessage();
            $returnData['errCode']=-3;
            $returnData['errMsg']=$message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 店铺积分是否开启
     * @param Request $request
     * @author jonzhang
     * @date 2017-06-02
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function updateStorePointStatus(Request $request,ShopService $shopService)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $isOn=$request->input('is_on');

        $wid=session('wid');
        $errMsg='';
        if(empty($wid))
        {
            error('登录超时');
        }
        if(is_null($isOn))
        {
            $errMsg.='积分开启状态为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        //return WeixinService::updateData($wid,['is_point'=>$isOn]);
        $shopService->update($wid,['is_point' => $isOn]);
        return $returnData;
    }

    /**
     * todo 店铺积分是否开启
     * @param Request $request
     * @author jonzhang
     * @date 2017-06-02
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function selectStorePointStatus(Request $request,ShopService $shopService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        /*$result=WeixinService::selectPointStatus(['id'=>$wid]);
        if($result['errCode']==0&&!empty($result['data']))
        {
            $returnData['data']=$result['data'][0]['is_point'];
        }*/
        $result = $shopService->getRowById($wid);
        if (!empty($result)) {
           $returnData['data'] = $result['is_point'];
        }
        return $returnData;
    }

    /**
     * todo 通过系统给会员添加积分
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-02
     */
    public function addPointBySystem(Request $request, MemberService $memberService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        //会员id
        $mid=$request->input('mid');
        //积分
        $score=$request->input('score');
        $score=intval($score);
        //积分来源类型
        $type=$request->input('type')??5;
        //yuanyin
        $remark=$request->input('msg');
        //店铺id
        $wid=session('wid');
        $errMsg='';
        if(empty($wid))
        {
           error('登录超时');
        }
        if(empty($mid))
        {
            $errMsg.='id为空';
        }
        if(empty($score))
        {
            $errMsg.='分数为0';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $isAdd=1;
        if($score<0)
        {
            $isAdd=0;
            $score=abs($score);
        }
        if($score>100000)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='积分太大';
            return $returnData;
        }
        $memberScore=0;
        //查询改用户当前积分
        $memberData=$memberService->getListById(['id'=>$mid]);
        if(!empty($memberData))
        {
            $memberScore=$memberData[0]['score'];
        }
        //系统去积分不能够大于用户拥有的积分
        if($isAdd==0&&$score>$memberScore)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='系统去积分不能够大于用户拥有的积分';
            return $returnData;
        }
        $pointRecordData=['wid'=>$wid,'mid'=>$mid,'point_type'=>$type,'is_add'=>$isAdd,'score'=>$score,'remark'=>$remark];
        //消费积分记录
        PPointRecordService::insertData($pointRecordData);

        $score = $isAdd == 1 ? $score : '-'.$score;
        $memberService->incrementScore($mid,$score);

        //添加积分触发发放会员卡 add by zhangyh 20180123
        dispatch((new CheckGrantCard($mid,$wid))->onQueue('CheckGrantCard'));

        return $returnData;
    }

    /**
     * todo 查询会员积分变更
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-05
     */
    public function selectPointRecord(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $mid=$request->input('mid');
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        if(empty($mid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='会员id为空';
            return $returnData;
        }
        $data=['mid'=>$mid,'wid'=>$wid];
        return PPointRecordService::getMemberPointRecord($data,1);
    }

    /**
     * todo 显示积分规则
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-05
     */
    public function selectPointRule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        //消费送积分规则
        $orderPointRuleData=OrderPointRuleService::getRowByCondition(['wid' => $wid]);
        if($orderPointRuleData['errCode']==0&&!empty($orderPointRuleData['data']))
        {
            $consumeData=[];
            $consumeData['type']='consume';
            $consumeData['id']=$orderPointRuleData['data']['id'];
            $consumeData['is_on']=$orderPointRuleData['data']['is_on'];
            $consumeData['basic_rule']=$orderPointRuleData['data']['basic_rule'];
            $consumeData['extra_item']=[];
            //消费送积分的额外规则
            $orderPointExtraRuleData=OrderPointExtraRuleService::getListByConditionWithPage(['p_id' =>$orderPointRuleData['data']['id']]);
            if($orderPointExtraRuleData['errCode']==0&&!empty($orderPointExtraRuleData['data']))
            {
                foreach($orderPointExtraRuleData['data'] as $item)
                {
                    $extraItem=[];
                    $extraItem['id']=$item['id'];
                    $extraItem['used_money']=$item['used_money'];
                    $extraItem['reward_point']=$item['reward_point'];
                    $consumeData['extra_item'][]=$extraItem;
                }
            }
            $returnData['data'][]=$consumeData;
        }
        //分享送积分规则
        $sharePointRuleData=SharePointRuleService::getRowByCondition(['wid' => $wid]);
        if($sharePointRuleData['errCode']==0&&!empty($sharePointRuleData['data']))
        {
            $shareData=[];
            $shareData['type']='share';
            $shareData['basic_rule']=$sharePointRuleData['data']['basic_rule'];
            $shareData['limit_rule']=$sharePointRuleData['data']['limit_rule'];
            $shareData['id']=$sharePointRuleData['data']['id'];
            $shareData['is_on']=$sharePointRuleData['data']['is_on'];
            $returnData['data'][]=$shareData;
        }
        return $returnData;
    }

    /**
     * todo 添加/更改 签到配置信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-06
     */
    public function processSign(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $data=$request->input('data');
        $id=$request->input('id');
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            error('登录超时');
        }
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errCode']='传递的data数据有问题';
            return $returnData;
        }
        $errMsg='';
        $data['is_on']=$data['is_on']??0;
        if(empty($data['template_data']))
        {
            $errMsg.='参数template_data数值不能够为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }

        /*update by 吴晓平 2018年08月14日 处理分享信息填写不完整时还能保存问题*/    
        $template_data = json_decode($data['template_data'],true);
        $shareData = [];
        if (isset($template_data[0]['share_img']) && isset($template_data[0]['shareTitle']) && isset($template_data[0]['shareDesc'])) {
            $shareData = ['share_img' => $template_data[0]['share_img'],'share_title' => $template_data[0]['shareTitle'],'share_desc' => $template_data[0]['shareDesc']];
        }
        $msg = $this->validatorShareData($shareData);

        if ($msg) {
            $returnData['errCode']=-101;
            $returnData['errMsg']=$msg;
            return $returnData;
        }
        //end
        $signResult=SignService::getRow($wid);
        if($signResult['errCode']==0&&!empty($signResult['data']))
        {
            $updateData=['is_on'=>$data['is_on'],'template_data'=>$data['template_data']];
            //$id=$signResult['data'][0]['id'];
            if(empty($id))
            {
                $returnData['errCode']=-3;
                $returnData['errMsg']='id为空';
                return $returnData;
            }
            $updateSignResult=SignService::updateData($id,$updateData);
            if($updateSignResult['errCode']==0)
            {
                return $returnData;
            }
            else
            {
                $returnData['errCode']=$updateSignResult['errCode'];
                $returnData['errCode']=$updateSignResult['errMsg'];
                return $returnData;
            }
        }
        else if ($signResult['errCode']==0&&empty($signResult['data']))
        {
            $insertData=['wid'=>$wid,'is_on'=>$data['is_on'],'template_data'=>$data['template_data']];
            $insertSignResult=SignService::insertData($insertData);
            if($insertSignResult['errCode']==0)
            {
                return $returnData;
            }
            else
            {
                $returnData['errCode']=$insertSignResult['errCode'];
                $returnData['errCode']=$insertSignResult['errMsg'];
                return $returnData;
            }
        }
        $returnData['errCode']=-100;
        $returnData['errMsg']='查询数据出现问题';
        return $returnData;
    }

    /**
     * 签到分享内容设置
     * @author 吴晓平 <2018年08月14日>
     * @param  array  $shareData [分享内容数组]
     * @return [type]            [description]
     */
    public function validatorShareData($shareData = [])
    {
        $msg = '';
        if (!empty($shareData)) {
            if (!$shareData['share_img'] && $shareData['share_title'] && $shareData['share_desc']) {
                $msg =  '请填写分享图片';
            }
            else if($shareData['share_img'] && !$shareData['share_title'] && $shareData['share_desc']) {
                $msg = '请填写分享标题';
            }
            else if ($shareData['share_img'] && $shareData['share_title'] && !$shareData['share_desc']) {
                $msg = '请填写分享内容';
            }
            else if ($shareData['share_img'] && !$shareData['share_title'] && !$shareData['share_desc']) {
                $msg = '请填写分享标题及内容';
            }
            else if ($shareData['share_title'] && !$shareData['share_img'] && !$shareData['share_desc']) {
                $msg = '请填写分享内容及图片';
            }
            else if ($shareData['share_desc'] && !$shareData['share_title'] && !$shareData['share_img']) {
                $msg = '请填写分享标题及图片';
            }else {
                return '';
            }
        }
        return $msg;
    }
}