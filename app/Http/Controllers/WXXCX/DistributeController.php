<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/11/16
 * Time: 10:37
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Model\CashLog;
use App\Model\DistributeBank;
use App\Model\Income;
use App\Model\Member;
use App\Module\DistributeModule;
use App\Module\MessagePushModule;
use App\S\Distribute\DistributeApplayLogService;
use App\S\Distribute\DistributeApplayPageService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductService;
use App\S\Product\ProductSkuService;
use App\Services\DistributeTemplateService;
use App\Services\Order\OrderService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use App\Lib\WXXCX\ThirdPlatform;

class DistributeController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function wealth(Request $request,DistributeModule $distributeModule)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $distributeModule = new DistributeModule();
        $member = (new MemberService())->model->find($mid);
        if ($member){
            $member = $member->toArray();
        }
        $data['amount'] = $distributeModule->getStayAmount($mid,$wid);
        $data['complete'] = $distributeModule->extractAmount($mid,$wid);
        $data['cash'] = round($member['cash'],2);
        $data['grade'] = $distributeModule->getGradeInfo($wid,$member['distribute_grade_id']);
        xcxsuccess('操作成功',$data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171116
     * @desc 获取佣金收益记录
     * @param Request $request
     */
    public function getIncome(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $distributeModule = new DistributeModule();
        $income = $distributeModule->getIncome($mid,$wid);
        xcxsuccess('操作成功',$income);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取
     * @param Request $request
     */
    public function getCashLog(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $distributeModule = new DistributeModule();
        $result = $distributeModule->getCash($mid,$wid);
        xcxsuccess('操作成功',$result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取提取数据
     * @param Request $request
     */
    public function withdrawals(Request $request)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        $distributeModule = new DistributeModule();
        if ($request->isMethod('post')){
            //提现操作
            $rule = Array(
                'bank_id'           => 'required',
                'money'             => 'required',

            );
            $message = Array(
                'bank_id.required'     => '账户ID不能为空',
                'money.required'       => '提现金额不能为空',
            );
            $input = $request->input();
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                xcxerror($validator->errors()->first());
            }
            $result = $distributeModule->withdrawals($mid,$wid,1);
            if ($result['errCode'] == 0){
                xcxsuccess('操作成功');
            }else{
                xcxerror($result['errMsg']);
            }
        }
        $member = $distributeModule->getWithdrawals($mid,$wid);
        xcxsuccess('操作成功',$member);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取用户账户列表
     * @param Request $request
     */
    public function getMyAccount(Request $request)
    {
        $mid = $request->input('mid');
        $bank = (new DistributeModule())->getMyAccount($mid);
        foreach ($bank as &$val){
            $val['account'] = substr($val['account'],-4);
        }
        xcxsuccess('操作成功',$bank);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 银行信息
     */
    public function getBank()
    {
        $result = (new DistributeModule())->getBank();
        xcxsuccess('操作成功',$result);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取账号信息
     * @param Request $request
     */
    public function addAccount(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'account'               => 'required',
            'bank_name'             => 'required',
            'type'                   => 'required|in:1,2',
            'name'                   => 'required',
            'logo'                   => 'required'
        );
        $message = Array(
            'account.required'     => '账号不能为空',
            'bank_name.required'   => '银行名称不能为空',
            'type.required'         => '类型不能为空',
            'logo.required'         => 'logo不能为空'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $bank = [
            'mid'           => $request->input('mid'),
            'bank_name'    => $input['bank_name'],
            'account'       => $input['account'],
            'name'          => $input['name'],
            'type'          => $input['type'],
            'logo'          => $input['logo'],
        ];
        $id = DistributeBank::insertGetId($bank);
        $bank['id'] = $id;
        xcxsuccess('操作成功',$bank);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 删除账号
     * @param Request $request
     */
    public function delAccount(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'ids'               => 'required',
        );
        $message = Array(
            'ids.required'     => '账户ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        if(!is_array($input['ids'])){
            return myerror('id应该为数组');
        }
        $mid = $request->input('mid');
        $res = DistributeBank::where('mid',$mid)->whereIn('id',$input['ids'])->delete();
        if ($res){
            xcxsuccess('操作成功');
        }else{
            xcxerror('操作失败');
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171117
     * @desc 个人中心是否显示成为分销客
     */
    public function isShowDistribute(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $distributeModule = new DistributeModule();
        $memberData = (new MemberService())->getRowById($mid);
        $res = $distributeModule->isShowDistibute($memberData,$wid);
        xcxsuccess('操作成功',$res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171117
     * @desc 取消成为分销客，或不在显示 tag=1 取消，tag=2 不在显示
     * @param Request $request
     */
    public function cancelDistribute(Request $request)
    {
        $tag = $request->input('tag',1);
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        (new DistributeModule())->cancelDistribute($mid,$wid,$tag);
        xcxsuccess('操作成功');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 成为分销客
     * @param Request $request
     */
    public function beDistribute(Request $request)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');

        //何书哲 2018年11月09日 发送成为分销客消息通知
        if ((new MemberService())->batchUpdate([$mid],['is_distribute'=>1])) {
            (new MessagePushModule($wid, MessagesPushService::BecomePromoter))->sendMsg(['mid'=>$mid]);
            (new MessagePushModule($wid, MessagesPushService::BecomePromoter, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid'=>$mid]);
            xcxsuccess();
        }
        xcxerror();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171129
     * @desc 公共调用接口用来小程序绑定分销上下级关系
     */
    public function bindParent()
    {
        xcxsuccess();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171130
     * @desc 个人中心是否显示我的财富
     * @param Request $request
     */
    public function isShowWealth(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $res = (new DistributeModule())->isShowWealth($mid,$wid);
        xcxsuccess('操作成功',$res);
    }
    
    /**
     * 小程序生成分销二维码
     * @author 吴晓平 2018年07月16日
     * @param  @param Request $request
     * @return [type]           [description]
     */
    public function distributionExplan(Request $request)
    {
        $wid = $request->input('wid') ?? 0;
        $mid = $request->input('mid') ?? 0;
        $url = $request->input('url') ?? '';
        if (empty($url)) {
            xcxerror('分销二维码跳转路径不能为空');
        }
        $qrcodeUrl = $url.'?_pid_='.$mid.'&wid='.$wid;   //设置分销pid
        $result = (new ThirdPlatform())->getXCXQRCode($wid, 200, $qrcodeUrl);
        if ($result['errCode'] == '0'){
            $url = $result['data'];
        }else{
            $url = '';
        }
        xcxsuccess('',$url);
    }

    /**
     * 佣金提现记录
     * @author 张永辉 2018年10月15日
     */
    public function cashLog(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $startTime = date('Y-m-01 00:00:00',strtotime('-10 month'));
        $cash = CashLog::where('wid',$wid)->where('mid',$mid)->where('created_at','>',$startTime)->orderBy('id','desc')->get()->toArray();
        $result  = [];
        $nowMoth = date('Y年m月');
        foreach ($cash as $val) {
            $moth = date('Y年m月',strtotime($val['created_at']));
            $result[$moth][] = $val;
        }

        $data = [];
        foreach ($result as $key => $item) {
            if ($key == $nowMoth) {
                $temp['title'] = '本月';
                $temp['data']  = $item;
            } else {
                $temp['title'] = $key;
                $temp['data']  = $item;
            }
            $data[] = $temp;
        }
        xcxsuccess('操作成功',$data);

    }

    /**
     * 团购订单
     * @author 张永辉 2018年10月15日
     */
    public function distributeOrder(Request $request, OrderService $orderService, MemberService $memberService)
    {
        $mid        = $request->input('mid');
        $memberData = $memberService->model->where('pid', $mid)->get(['id'])->toArray();
        if (!$memberData) {
            xcxsuccess('操作成功',[]);
        }
        $mids      = array_column($memberData, 'id');
        $where     = [
            'mid'             => ['in', $mids],
            'distribute_type' => '1',
        ];
        $page      = $request->input('page') ? $request->input('page') : 1;
        $pagesize  = config('database.perPage');
        $offset    = ($page - 1) * $pagesize;
        $orderData = $orderService->init()->model->wheres($where)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->load('orderDetail')->toArray();
        if ($orderData) {
            $oids       = array_column($orderData, 'id');
            $mids       = array_column($orderData, 'mid');
            $memberTmp  = $memberService->getListById($mids);
            $memberData = [];
            foreach ($memberTmp as $val) {
                $memberData[$val['id']] = $val;
            }
            $res        = Income::where('mid', $mid)->whereIn('oid', $oids)->get()->toArray();
            $incomeData = [];
            foreach ($res as $key => $val) {
                $incomeData[$val['oid']] = $val;
            }
            foreach ($orderData as &$item) {
                $item['commission'] = $incomeData[$item['id']]['money']??'0';
                $item['memberData'] = [
                    'nickname'   => $memberData[$item['mid']]['nickname']??'',
                    'headimgurl' => $memberData[$item['mid']]['headimgurl']??'',
                ];
            }
        }
        xcxsuccess('操作成功',$orderData);
    }

    /**
     * 佣金记录
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     * @author 张永辉
     */
    public function incomeLog(Request $request)
    {
        $mid     = $request->input('mid');
        $startTime = date('Y-m-01 00:00:00',strtotime('-10 month'));
        $income  = Income::where('mid', $mid)->where('status','!=','-3')->where('created_at','>',$startTime)->orderBy('id', 'desc')->get()->load('order')->toArray();
        $result = $data = [];
        $nowMoth = date('Y年m月');
        foreach ($income as $val) {
            $moth = date('Y年m月',strtotime($val['created_at']));
            $result[$moth][] = $val;
        }

        foreach ($result as $key => $item) {
            if ($key == $nowMoth) {
                $temp['title'] = '本月';
                $temp['data']  = $item;
            } else {
                $temp['title'] = $key;
                $temp['data']  = $item;
            }
            $data[] = $temp;
        }
        xcxsuccess('操作成功',$data);
    }

    /**
     * 申请成为分销客
     * @param $id
     * @param DistributeApplayPageService $distributeApplayPageService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张永辉 2018年10月15日
     */
    public function apply(Request $request, DistributeApplayPageService $distributeApplayPageService, DistributeApplayLogService $distributeApplayLogService,MemberService $memberService, $wid = 0, $id = 0)
    {
        $mid     = $request->input('mid');
        $where   = [
            'mid'    => $mid,
            'status' => '0'
        ];
        $logData = $distributeApplayLogService->getList($where, $skip = "", $perPage = "", $orderBy = "id", $order = "desc");
        $memberData = $memberService->getRowById($mid);
        $logData = current($logData);
        if ($request->isMethod('post')) {
            $logData && xcxerror('您已申请过了，等待审核一下吧……');
            if (!empty($memberData['is_distribute'])){
                xcxerror('您已经可以分销，无需申请');
            }
            $insertData = [
                'wid' => $request->input('wid'),
                'mid' => $mid,
            ];
            $distributeApplayPageService->increment($request->input('id'),'num',1);
            $distributeApplayLogService->add($insertData) && xcxsuccess();
            xcxerror();
        }
        $data                    = $distributeApplayPageService->getRowById($id);
        $result['data']          = $data;
        $result['logData']       = $logData;
        $result['is_distribute'] = $memberData['is_distribute']??'0';
        xcxsuccess('操作成功', $result);
    }

    /**
     * 我的团队
     * @author 张永辉 2018年10月15日
     */
    public function myTeam(Request $request,MemberService $memberService)
    {
        if ($request->input('pid')) {
            $sonMenberData = $memberService->getListByConditionWithPage(['pid' => $request->input('pid')]);
            xcxsuccess('', $sonMenberData);
        }
        $mid           = $request->input('mid');
        $memberData    = $memberService->getRowById($mid);
        $sonMenberData = $memberService->getListByConditionWithPage(['pid' => $mid]);
        xcxsuccess('操作成功', ['memberData' => $memberData, 'sonMenberData' => $sonMenberData]);
    }

    /**
     * 获取分销商品列表
     * @param Request $request
     * @param ProductService $productService
     * @param ProductSkuService $productSkuService
     * @param DistributeTemplateService $distributeTemplateService
     * @author 张永辉 2018年10月15日
     */
    public function productList(Request $request, ProductService $productService, ProductSkuService $productSkuService, DistributeTemplateService $distributeTemplateService,DistributeModule $distributeModule,MemberService $memberService)
    {
        $product = $distributeModule->productList($request,$productService,$productSkuService,$distributeTemplateService,$memberService,$request->input('wid'),$request->input('mid'));
//        $where        = [
//            'wid'                    => $request->input('wid'),
//            'distribute_template_id' => ['!=', '0'],
//            'is_distribution'        => '1',
//            'status'                 => '1'
//        ];
//        $product      = $productService->listWithPage($where);
//        $pids         = array_column($product[0]['data'], 'id');
//        $templateIds  = array_column($product[0]['data'], 'distribute_template_id');
//        $res          = $distributeTemplateService->getListByIds($templateIds);
//        $templateData = [];
//        foreach ($res as $val) {
//            $templateData[$val['id']] = $val;
//        }
//        $skuData = $productSkuService->getSkuListByPids($pids);
//        foreach ($product[0]['data'] as &$item) {
//            if (empty($skuData[$item['id']])) {
//                $item['distribute_amount'] = round($item['price'] * $templateData[$item['distribute_template_id']]['one'] * 0.01, 2);
//            } else {
//                foreach ($skuData[$item['id']] as &$val){
//                    $val['distribute_amount'] = round($val['price'] * $templateData[$item['distribute_template_id']]['one'] * 0.01, 2);
//                }
//                $item['skuData'] = $skuData[$item['id']];
//                $item['distribute_amount'] =$skuData[$item['id']][0]['distribute_amount'] ;
//            }
//        }
        xcxsuccess('操作成功',$product);
    }


}