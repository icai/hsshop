<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/13
 * Time: 10:35
 */

namespace App\S\Market;
use App\S\Member\MemberService;
use PointRecordService as SPointRecordService;
use SignService as SSignService;
use App\S\S;

class SignRecordService extends S
{
    public function __construct()
    {
        parent::__construct('SignRecord');
    }
    /**
     * todo 记录用户签到信息
     * @param $wid 店铺id
     * @param $mid 会员id
     * @author jonzhang
     * @date 2017-05-27
     */
    public function addSignRecord($wid=0,$mid=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>['signDay'=>0,'score'=>0]];
        if(empty($mid)||empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //取得当前日期
        $currentDate=date('Y-m-d');
        //当前日期对应的时间戳
        $currentTimeStamp=strtotime($currentDate);
        //前一天日期的时间戳
        $yesterdayTimeStamp=$currentTimeStamp-86400;
        //查询用户前一天是否签到
        $beforeSignRecordData=$this->model->select(['sign_day'])->where(['sign_time'=>$yesterdayTimeStamp,'wid'=>$wid,'mid'=>$mid])->get()->toArray();
        //dd($beforeSignRecordData);
        //定义签到天数,默认为1
        $signDay=1;
        if(!empty($beforeSignRecordData))
        {
            $signDay = $beforeSignRecordData[0]['sign_day']+1;
        }
        //dd($signDay);
        //获取用户是否签到
        $cnt=$this->model->where(['sign_time'=>$currentTimeStamp,'wid'=>$wid,'mid'=>$mid])->count();
        //dd($cnt);
        //cnt大于0表示数据当天已经签到 cnt等于0表示当天还没有签到
        if($cnt==0)
        {
            //添加签到信息
            $insertID=$this->model->insertGetId(['wid'=>$wid,'mid'=>$mid,'sign_time'=>$currentTimeStamp,'sign_day'=>$signDay]);
            //插入数据成功
            if($insertID)
            {
                $memberScore=0;
                //查询用户当前积分
                $memberData=(new MemberService())->getRowById($mid);
                if(!empty($memberData)) {
                    $memberScore = $memberData['score'];
                }

                //查询店铺是否开启签到
                $signData=SSignService::getRow($wid);
                if($signData['errCode']==0&&!empty($signData['data']))
                {
                    //店铺签到开关
                    $signIsON=$signData['data']['is_on'];
                    //店铺签到模板信息
                    $signTemplateData=$signData['data']['template_data'];
                    //dd($signTemplateData);
                    if($signIsON)
                    {
                        //把签到模板字符串转化为数组
                        $signTemplateData=json_decode($signTemplateData,true);
                        //dd($signTemplateData);
                        //转化数组失败
                        if(is_null($signTemplateData))
                        {
                            $returnData['errCode']=-3;
                            $returnData['errMsg']='签到规则数据有问题';
                            return $returnData;
                        }
                        //dd($signTemplateData);
                        $ruleScore=0;
                        foreach($signTemplateData as $signItem)
                        {
                            //模板数组中找到签到模块和签到规则
                            if($signItem['type']=='sign'&&!empty($signItem['signList']))
                            {
                                $signRuleData=$signItem['signList'];
                                foreach($signRuleData as $signRuleItem)
                                {
                                    //查找当前用户签到天数对应的签到积分规则
                                    //签到前端已传递数值
                                    if($signRuleItem['limit']&&$signRuleItem['signDay']==$signDay)
                                    {
                                        $ruleScore=$ruleScore+$signRuleItem['signCredite'];
                                    }
                                    else if(!$signRuleItem['limit']&&$signRuleItem['signDay']<=$signDay)
                                    {
                                        $result=$signDay%$signRuleItem['signDay'];
                                        if($result==0)
                                        {
                                            $ruleScore = $ruleScore + $signRuleItem['signCredite'];
                                        }
                                    }
                                }
                                break;
                            }
                        }
                        if(!empty($ruleScore))
                        {
                            //记录用户积分
                            $pointRecordData=['wid'=>$wid,'mid'=>$mid,'point_type'=>2,'is_add'=>1,'score'=>$ruleScore];
                            SPointRecordService::insertData($pointRecordData);
                            $memberScore=$memberScore+$ruleScore;
                            //更改用户积分
                            (new MemberService())->updateData($mid, ['score'=>$memberScore]);
                        }
                    }
                }
                $returnData['data']['signDay']=$signDay;
                $returnData['data']['score']=$memberScore;
            }
        }
        else
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='已签到';
        }
        //签到失败 那么data为0
        return $returnData;
    }

    /**
     * todo 获取用户签到天数
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-07
     */
    public function getUserSign($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>['isSign'=>0,'signDay'=>0]];
        if(empty($data['mid'])||empty($data['wid']))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $mid=$data['mid'];
        $wid=$data['wid'];
        //取得当前日期
        $currentDate=date('Y-m-d');
        //当前日期对应的时间戳
        $currentTimeStamp=strtotime($currentDate);
        //前一天日期的时间戳
        $yesterdayTimeStamp=$currentTimeStamp-86400;
        $signResult=$this->model->select(['sign_day'])->where(['sign_time'=>$currentTimeStamp,'wid'=>$wid,'mid'=>$mid])->get()->toArray();
        //dd($signResult);
        //如果没有查询到数据 $signResult为[]
        //用户当天已经签到，累计签到天数
        if(!empty($signResult))
        {
            $returnData['data']['isSign']=1;
            $returnData['data']['signDay']=$signResult[0]['sign_day'];
            return $returnData;
        }
        //用户当天没有签到，前面签到了多少天
        $beforeSignRecordData=$this->model->select(['sign_day'])->where(['sign_time'=>$yesterdayTimeStamp,'wid'=>$wid,'mid'=>$mid])->get()->toArray();
        if(!empty($beforeSignRecordData))
        {
            $returnData['data']['isSign']=0;
            $returnData['data']['signDay']=$beforeSignRecordData[0]['sign_day'];
            return $returnData;
        }
        return $returnData;
    }
}