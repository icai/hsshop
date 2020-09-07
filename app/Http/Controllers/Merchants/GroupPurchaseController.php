<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/7/3
 * Time: 10:20
 */

namespace App\Http\Controllers\Merchants;


use App\Http\Controllers\Controller;
use App\Module\GroupsRuleModule;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsSkuService;
use App\S\Product\ProductPropService;
use App\S\Product\ProductPropsToValuesService;
use EasyWeChat\ShakeAround\Group;
use Illuminate\Http\Request;
use Validator;

class GroupPurchaseController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170703
     * @desc 使失效
     * @param GroupsRuleService $groupsRuleService
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function invalid(GroupsRuleService $groupsRuleService,$id)
    {
        $rule = $groupsRuleService->getRowById($id);
        if (!$rule || session('wid') != $rule['wid']){
            return myerror('传递参数错误');
        }
        // 正在进行中的团购调整时间进行结束
        $now = date('Y-m-d H:i:s',time());
        if (strtotime($rule['start_time'])<time() && time()<strtotime($rule['end_time'])){
            $groupsRuleService->update($rule['id'],['end_time'=>$now]);
            return mysuccess('已结束');
        }

        $data['status'] = -1;
        $data['end_time'] = $now;
        $res = $groupsRuleService->update($id,$data);
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170703
     * @desc 删除拼团
     * @param GroupsRuleService $groupsRuleService
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function del(GroupsRuleService $groupsRuleService,$id)
    {
        $rule = $groupsRuleService->getRowById($id);
        if (!$rule || session('wid') != $rule['wid']){
            return myerror('传递参数错误');
        }
        if (strtotime($rule['start_time'])<=time() && strtotime($rule['end_time'])>=time() && $rule['status'] != -1){
            return myerror('活动正在进行不能删除');
    }
//        $res = $groupsRuleService->del($id);
        $res = $groupsRuleService->update($id,['status'=>-2]);
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170704
     * @desc
     * @param $id
     */
    public function getProps($id)
    {
        $str = (new ProductPropsToValuesService())->getSkuList($id);
        return mysuccess('操作成功','',$str['stocks']);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 添加团购
     * @desc
     * @update 许立 2019年01月25日 价格改为1000万
     */
    public function editRule(Request $request,GroupsRuleService $groupsRuleService,GroupsSkuService $groupsSkuService)
    {
        $input = $request->input();
        $now = date('Y-m-d H:i:s');
        $startTime = $input['start_time'] > $now ? $input['start_time'] : $now;
        /* start  add guguowei  服务保障*/
        $service_by = $input['service_by'] ?? 0;
        $service_bz = $input['service_bz'] ?? 0;
        $service_th = $input['service_th'] ?? 0;
        $explode = $service_by.','.$service_bz.','.$service_th;
        /*end*/
		//判断拼团存在时间是否大于批团活动结束时间 Herry 20171110
        $expireHours = $input['expire_hours'] ?? 0;
        if ($expireHours && strtotime($startTime) + $expireHours * 3600 > strtotime($input['end_time'])) {
            return myerror('未成团订单存在时间必须在生效时间内');
        }

        $rule = Array(
            'pid'         => 'required',
            'title'       => 'required',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'is_open'     => 'required',
            'skus'         => 'required',
            'groups_num'  => 'required',
            'img'          => 'required',
            'img2'         => 'required',

        );
        $message = Array(
            'pid.required'  => '商品id不能为空',
            'title.required'  => '团购名称不能为空',
            'start_time.required' => '开始时间不能为空',
            'end_time.required' => '结束时间不能为空',
            'is_open.required' => '是否开启凑团不能为空',
            'skus.required'     => '商品规格价格必须设置',
            'groups_num.required'=> '参团人数不能为空',
            'img.required'=> '活动图片不能为空',
            'img2.required'=> '活动图片不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        };

       $this->checkData($input);

        $ruleData = [
            'wid'           => session('wid'),
            'pid'           => $input['pid'],
            'title'         => $input['title'],
            'start_time'    => $input['start_time'],
            'end_time'      => $input['end_time'],
            'num'           => $input['num']??0,
            'is_open'       => $input['is_open'],
            'groups_num'    => $input['groups_num'],
            'img'           => $input['img'],
            'img2'          => $input['img2'],
            'head_discount' => $input['head_discount']??0,
            //add by WuXiaoPing 2017-08-11
            'share_title'   => $input['share_title'] ?? '',
            'share_desc'    => $input['share_desc'] ?? '',
            'share_img'     => $input['share_img'] ?? '',

            //add by wuxiaoping 2017.11.09
            'is_open_draw'  => $input['is_open_draw'] ?? 0,
            'draw_type'     => $input['draw_type'] ?? 0,
            'draw_pnum'     => $input['draw_pnum'] ?? 0,
            'draw_phones'   => $input['draw_phones'] ?? '',

            //add by wuxiaoping 2017.12.06
            'group_type'    => $input['group_type'],

            'label'     => $input['label'] ?? '',
            'subtitle'     => $input['subtitle'] ?? '',
            //自动成团或关闭参数 Herry 20171108
            'auto_success' => $input['auto_success'] ?? 0,
            'expire_hours' => $expireHours,
            /*start  fuguowei  服务保障 富文本内容*/
            'service_txt'  => $input['service_txt'] ?? '',
            'service_status'=>$explode,
            /*end*/
            'distribute_template_id'    => $input['distribute_template_id']??0,
            'limit_type'             => $input['limit_type']??-1,
        ];

        //编辑团购
        if (isset($input['id']) && !empty($input['id'])){
            $res = $groupsRuleService->update($input['id'],$ruleData);
            if ($res){
//                $groupsSkuService->delByRuleId($input['id']);
//                $skus = json_decode($input['skus'],true);
                $skus = $input['skus'];
                foreach ($skus as $val){
                    if ($val['price'] >= 10000000) {
                        error('规格价格不能超过10000000');
                    }
                    $tempDta = [
                        'price'         => $val['price'],
                        'head_price'    => $val['head_price'],
                    ];
                    $groupsSkuService->update($val['id'],$tempDta);
                }
                return mysuccess();
            }
        }else{
            //添加团购
            $rule_id = $groupsRuleService->add($ruleData);
            if ($rule_id){
                $skus = $input['skus'];
                foreach ($skus as $val){
                    if ($val['price'] >= 10000000) {
                        error('规格价格不能超过10000000');
                    }
                    $tempDta = [
                        'rule_id'       => $rule_id,
                        'sku_id'        => $val['id'],
                        'price'         => $val['price'],
                        'head_price'    => $val['head_price'],
                    ];
                    $groupsSkuService->add($tempDta);
                }
            }else{
                return myerror();
            }

            return mysuccess();
        }
    }


    public function checkData($input)
    {
        if (strtotime($input['start_time'])>strtotime($input['end_time'])) {
            error('开始时间不能大于结束时间');
        }

        if ($input['groups_num'] >100 || $input['groups_num']<2) {
            error('参团人数是2～100人');
        }
        $input['num'] = $input['num']??0;
        if ($input['num']<0) {
            error('限购数量必须为整数');
        }
        foreach ($input['skus'] as $val){
            if ($input['head_discount']){
                if ($val['price']<$val['head_price']){
                    error('团长价格必须小于团购价格');
                }
            }
        }

        /*开启中奖设置限制  add by wuxiaoping 2017.11.09*/
        if (isset($input['draw_type']) && $input['draw_type'] == 1) {
            if ($input['draw_phones'] == '') {
                error('至少输入一个指定的手机号');
            }else{
                $drawPhones = explode(',',$input['draw_phones']);
                $phoneNums = count($drawPhones);
                if ($phoneNums > $input['draw_pnum']) {
                    error('手机号必须小于中奖人数');
                }
            }
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 团购
     */
    public function groupList(Request $request)
    {
        $ruleModule = new GroupsRuleModule();
        $data = $ruleModule->getGroupsRuleList(session('wid'),4);
        success('操作成功','',$data);
    }

    /***
     * todo 显示拼团信息
     * @param Request $request
     * @param GroupsRuleService $groupsRuleService
     * @return array
     * @author jonzhang
     * @date 2018-04-09
     */
    public  function showGroupList(Request $request,GroupsRuleService $groupsRuleService)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $title=$request->input('title');
        $wid=session('wid');
        $status=$request->input('status')??0;
        $status=intval($status);
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $where['wid']=$wid;
        if(!empty($title))
        {
            $where['title']=$title;
        }
        $result=$groupsRuleService->getListByCondition($where,$status,'id','desc',6);
        if($result['errCode']==0)
        {
            $returnData['data']=$result['data'];
            $returnData['total']=$result['total'];
            $returnData['currentPage']=$result['currentPage'];
            $returnData['pageSize']=$result['pageSize'];
            return $returnData;
        }
        else
        {
            return $result;
        }
    }
}