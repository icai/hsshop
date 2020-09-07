<?php
/**
 * 分销模块
 *
 * @package default
 * @author  大王叫我来巡山
 */
namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddDistributeTemplate;
use App\Jobs\DataStatisticsDistributeWithdrawal;
use App\Jobs\SubMsgPushJob;
use App\Model\AdStatistic;
use App\Model\CashLog;
use App\Model\DistributeGrade;
use App\Model\DistributeGradeTemplate;
use App\Model\DistributeTemplate;
use App\Model\Income;
use App\Model\Member;
use App\Model\Order;
use App\Model\Product;
use App\Model\Weixin;
use App\Module\DistributeModule;
use App\Module\MeetingGroupsRuleModule;
use App\Module\MessagePushModule;
use App\S\Distribute\DistributeApplayLogService;
use App\S\Distribute\DistributeApplayPageService;
use App\S\Distribute\DistributeGradeService;
use App\S\Distribute\DistributePurgeLogService;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsRuleService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Order\CompanyPayOrderService;
use App\S\Product\ProductService;
use App\S\Product\RemarkService;
use App\S\Store\AdStatisticService;
use App\S\WXXCX\SubscribeMessagePushService;
use App\Services\CashLogService;
use App\Services\DistributeTemplateService;
use App\Services\Order\OrderService;
//use App\Services\WeixinService;
use QrCode;
use DB;
use Illuminate\Http\Request;
use Validator;
use App\S\Weixin\ShopService;
use MallModule;

class DistributeController extends Controller
{
    /**
     *
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->leftNav = 'distribute';
    }

    /**
     * 微信状况
     * @return [type] [description]
     */
    public function index(DistributeModule $distributeModule)
    {
        $shop           = Weixin::find(session('wid'))->toArray();
        $shop['demand'] = json_decode($shop['demand'], true);
        $template       = DistributeTemplate::where('wid', session('wid'))->orderBy('is_default', 'desc')->first();
        if ($template) {
            $template = $template->toArray();
        }
        $grade = $distributeModule->getStoreDistributeGrade(session('wid'));
        return view('merchants.distribute.index', array(
            'title'    => '分销',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'index',
            'shop'     => $shop,
            'template' => $template,
            'grade'    => $grade,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 分销模板
     */
    public function template(DistributeGradeService $distributeGradeService, ShopService $shopService)
    {
        $grade = $distributeGradeService->getByWhere(['wid' => session('wid')]);
        $shop  = $shopService->getRowById(session('wid'));
        return view('merchants.distribute.template', array(
            'title'    => '分销模板',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'template',
            'grade'    => $grade,
            'shop'     => $shop
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function commission(ShopService $shopService)
    {
        $wid = session('wid');
        /*$shopData = (new WeixinService())->getStore(session('wid'));
        $shopData = $shopData['data'];*/
        $shopData = $shopService->getRowById($wid);
        return view('merchants.distribute.commission', array(
            'title'       => '佣金发放',
            'leftNav'     => $this->leftNav,
            'slidebar'    => 'commission',
            'company_pay' => $shopData['company_pay'],
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     */
    public function partner(ShopService $shopService)
    {
        $wid      = session('wid');
        $shopData = $shopService->getRowById($wid);
        return view('merchants.distribute.partner', array(
            'title'            => '分销伙伴',
            'leftNav'          => $this->leftNav,
            'slidebar'         => 'partner',
            'distribute_grade' => $shopData['distribute_grade']??'0',
            'shop'             => $shopData
        ));
    }

    /**
     * @author zhangyh
     * @Email  zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 开启关闭分销
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function open($status, ShopService $shopService)
    {
        $wid = session('wid');
        //查看店铺是否有模板
        $count = DistributeTemplate::where('wid', $wid)->count();
        if ($count == 0) {
            $templateData = [
                'wid'        => $wid,
                'price'      => 100,
                'cost'       => 70,
                'zero'       => 0,
                'one'        => 30,
                'sec'        => 0,
                'three'      => 0,
                'title'      => '分销模板7:3',
                'is_default' => 1,
            ];
            DistributeTemplate::insert($templateData);

            $templateData = [
                'wid'   => $wid,
                'price' => 100,
                'cost'  => 60,
                'zero'  => 0,
                'one'   => 40,
                'sec'   => 0,
                'three' => 0,
                'title' => '分销模板6:4',
            ];
            DistributeTemplate::insert($templateData);
        }
        //(new WeixinService())->init()->where(['id'=>$wid])->update(['is_distribute'=>$status]);
        $rs = $shopService->update($wid, ['is_distribute' => $status]);
        if ($rs) {
            success();
        }
        error();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 添加分销模板
     * @param Request $request
     */
    public function addTemplate1(AddDistributeTemplate $request, DistributeTemplateService $distributeTemplateService)
    {
        $input = $request->input();
        if ($input['zero'] * $input['one'] * $input['sec'] != 0) {
            return myerror('仅支持两级分销');
        }
        if ($input['price'] != ($input['zero'] + $input['one'] + $input['sec'] + $input['three'] + $input['cost'])) {
            return myerror('数据填写错误，请重新输入');
        }
        $wid = session('wid');
//        $count = DistributeTemplate::where('wid',$wid)->count();
//        if ($count>=10 && !isset($input['id'])){
//            return myerror('最多添加10个分销模板');
//        }
        $templateData = [
            'wid'   => session('wid'),
            'price' => $input['price'],
            'cost'  => round(($input['cost'] / $input['price']) * 100, 3),
            'zero'  => 0,
            'one'   => round(($input['one'] / $input['price']) * 100, 3),
            'sec'   => 0,
            'three' => 0,
            'title' => $input['title'],
        ];

        if (isset($input['id']) && !empty($input['id'])) {
            $result = DistributeTemplate::find($input['id']);
            if (!$result || $result->wid != $wid) {
                return myerror('修改ID错误或该模板不属于该店铺');
            }
            $templateData['id'] = $input['id'];
            $res                = $distributeTemplateService->up(['id' => $input['id']], $templateData);
            if ($res['success'] == 0) {
                return myerror();
            } else {
                return mysuccess();
            }
        } else {
            $res                = $distributeTemplateService->add($templateData);
            $templateData['id'] = $res;
            if ($res) {
                return mysuccess('操作成功', '', $templateData);
            } else {
                return myerror();
            }
        }
    }


    /**
     * 添加分销模板
     * @param AddDistributeTemplate $request
     * @param DistributeTemplateService $distributeTemplateService
     * @return $this|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @author 张永辉 2018年12月07日添加分销模板
     */
    public function addTemplate(Request $request, DistributeTemplateService $distributeTemplateService)
    {

        $data          = $request->input();
        $template      = [];
        $gradeTemplate = [];
        $wid           = session('wid');
        foreach ($data['data'] as $val) {
            if ($val['cost'] + $val['one'] + $val['sec'] != $val['price']) {
                error('请配置适当的分销比例');
            }

            if (empty($val['grade_id'])) {
                $template = [
                    'wid'   => $wid,
                    'id'    => $val['id']??'',
                    'price' => $val['price'],
                    'cost'  => round(($val['cost'] / $val['price']) * 100, 3),
                    'zero'  => 0,
                    'one'   => round(($val['one'] / $val['price']) * 100, 3),
                    'sec'   => round(($val['sec'] / $val['price']) * 100, 3),
                    'three' => 0,
                    'title' => $data['title'],
                ];
            } else {
                $gradeTemplate[] = [
                    'id'       => $val['id'],
                    'grade_id' => $val['grade_id'],
                    'wid'      => $wid,
                    'price'    => $val['price'],
                    'cost'     => round(($val['cost'] / $val['price']) * 100, 3),
                    'zero'     => 0,
                    'one'      => round(($val['one'] / $val['price']) * 100, 3),
                    'sec'      => round(($val['sec'] / $val['price']) * 100, 3),
                    'three'    => 0,
                ];
            }
        }


        if ($data['flag'] == 1) {
            unset($template['id']);
            $id = $distributeTemplateService->add($template);
            if ($gradeTemplate) {
                foreach ($gradeTemplate as $key => $val) {
                    $gradeTemplate[$key]['template_id'] = $id;
                    unset($gradeTemplate[$key]['id']);
                }
                DistributeGradeTemplate::insert($gradeTemplate);
            }
        } else {
            $id = $template['id'];
            unset($template['id']);
            $distributeTemplateService->up(['id' => $id], $template);
            if ($gradeTemplate) {
                foreach ($gradeTemplate as $key => $val) {
                    $id = $val['id'];
                    unset($val['id']);
                    DistributeGradeTemplate::where('id', $id)->update($val);
                }
            }
        }
        success();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 选择分销模板
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function choice(Request $request, DistributeTemplateService $distributeTemplateService)
    {
        if ($request->isMethod('post')) {
            if (!$request->input('id')) {
                return myerror('请选择分销模板');
            }
            $id  = $request->input('id');
            $wid = session('wid');
            $res = $distributeTemplateService->up(['wid' => $wid, 'is_default' => 1], ['is_default' => 0]);
            $res = $distributeTemplateService->up(['id' => $id], ['is_default' => 1]);
            if ($res) {
                return mysuccess();
            } else {
                return myerror();
            }
        }
        $template = $distributeTemplateService->getList();
        return view('merchants.distribute.choice', array(
            'title'    => '选择分销',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'choice',
            'template' => $template
        ));
    }


    //	作者：2585469768@qq.com
    //	时间：2017-05-24
    //	描述：分销收入  分销人脉

    public function partnerIncome()
    {
        return view('merchants.distribute.partnerIncome', array(
            'title'    => '合伙人佣金详情',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'partnerIncome'
        ));
    }

    public function partnerContacts()
    {
        return view('merchants.distribute.partnerContacts', array(
            'title'    => '合伙人人脉',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'partnerContacts'
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170523
     * @desc 删除模板
     */
    public function del(DistributeTemplateService $distributeTemplateService, $id)
    {
        $product       = Product::where('distribute_template_id', $id)->first();
        $groupRuleData = (new GroupsRuleService())->getList(['distribute_template_id' => $id]);
        if ($product || $groupRuleData) {
            return myerror('您有商品或团购活动选择了该分销模板，请修改后再删除');
        }
        $num = $distributeTemplateService->count(['wid' => session('wid')]);
        if ($num <= 1) {
            return myerror('店铺至少存在一个分销模板');
        }
        $result = $distributeTemplateService->del(['id' => $id]);
        if ($result['success'] == 0) {
            return myerror($result['message']);
        } else {
            return mysuccess();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170524
     * @desc 统一设置分销模板
     */
    public function setTemplate(ProductService $productService, $id)
    {
        $wid = session('wid');
        $res = DistributeTemplate::find($id);
        if (!$res) {
            return myerror('分销模板不存在');
        }
        Product::where('wid', $wid)->where('is_distribution', 1)->update(['distribute_template_id' => $id]);
        /*$query = Product::where('wid',$wid)->get();
        foreach ($productService->withAll as $val){
            $query->load($val);
        }
        $productDatas = $query->toArray();
        $productService->init('wid',$wid)->updateR($productDatas);*/
        $productData = Product::where('wid', $wid)->where('is_distribution', 1)->get(['id'])->toArray();
        $data        = [];
        foreach ($productData as $val) {
            $data[] = [
                'id'                     => $val['id'],
                'distribute_template_id' => $id,
            ];
        }
        $productService->batchUpdateRedis($data);

        success();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170524
     * @desc 复制模板
     * @param $id
     */
    public function copy(DistributeTemplateService $distributeTemplateService, $id)
    {
        if (DistributeTemplate::where('wid', session('wid'))->count() >= 10) {
            return myerror('最多添加10个分销模板');
        }
        $res = DistributeTemplate::find($id);
        if (!$res && $res->wid != session('wid')) {
            return myerror('ID不存在');
        }
        $templateData = $res->toArray();
        unset($templateData['id'], $templateData['created_at'], $templateData['updated_at']);
        $id = $distributeTemplateService->add($templateData);
        if ($id) {
            $templateData['id'] = $id;
            return mysuccess('操作成功', '', $templateData);
        } else {
            return myerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170524
     * @desc 获取分销模板接口
     */
    public function getTemplate(Request $request)
    {
        $wid          = session('wid');
        $templateData = DistributeTemplate::where('wid', $wid)->orderBy('id', 'DESC')->get()->load('gradeTemplate')->toArray();
        if ($request->input('from') == 1) {
            foreach ($templateData as $key => &$val) {
                $val['cost']  = $val['price'] * $val['cost'] / 100;
                $val['zero']  = $val['price'] * $val['zero'] / 100;
                $val['one']   = $val['price'] * $val['one'] / 100;
                $val['sec']   = $val['price'] * $val['sec'] / 100;
                $val['three'] = $val['price'] * $val['three'] / 100;
            }
            success('', '', $templateData);
        }

        $result = [];
        foreach ($templateData as $val) {
            $temp[0]            = [
                'id'         => $val['id'],
                'wid'        => $val['wid'],
                'price'      => $val['price'],
                'cost'       => $val['cost'],
                'zero'       => $val['zero'],
                'one'        => $val['one'],
                'sec'        => $val['sec'],
                'three'      => $val['three'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at'],
            ];
            $data['title']      = $val['title'];
            $data['is_default'] = $val['is_default'];
            $data['id']         = $val['id'];
            $data['data']       = array_merge($temp, $val['gradeTemplate']);
            $result[]           = $data;

        }
        return mysuccess('操作成功', '', $result);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 佣金发放信息
     * @param Request $request
     * @param CashLogService $cashLogService
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cashLog(Request $request, CashLogService $cashLogService)
    {
        $input    = $request->input();
        $cashData = $cashLogService->get($request->input());
        return mysuccess('操作成功', '', $cashData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201700525
     * @desc 同意提现
     * @param $id
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年11月06日 佣金发放通知
     * @update 吴晓平 修改小程序收益到帐提醒发送订阅模板消息 2019年12月20日 09:47:54
     */
    public function agree(CashLogService $cashLogService, ShopService $shopService, $id, $status)
    {
        $cash = CashLog::find($id);
        if (!$cash || $cash->wid != session('wid')) {
            return myerror('记录错误');
        }
        if ($status != 1 && $status != 2) {
            return myerror('status必须是1或2');
        }
        /*$weixinData = (new WeixinService())->getStore(session('wid'));
        $weixinData = $weixinData['data'];*/
        $wid        = session('wid');
        $weixinData = $shopService->getRowById($wid);

        if ($weixinData['company_pay'] == 1 && $cash->type != 3) {
            error('该用户使用低版本小程序提现功能，请通过手动打款完成');
        }

        if ($status == 2 && $weixinData['company_pay'] == 1 && $cash->type == 3) {
            $result = (new DistributeModule())->dealAgree($id);
            if ($result['errCode'] != '0') {
                error($result['errMsg']);
            }
        } elseif ($status == 2) {
            // 线下分销数据写入日志服务器 hsz
            $job = new DataStatisticsDistributeWithdrawal($cash->wid, $cash->mid, $cash->money, $cash->type, $cash->source, time());
            dispatch($job->onQueue('dsDraw'));
        }

        //TODO 何书哲 2018年11月6日 佣金发放通知
        if ($status == 2) {
            $commissionData = [
                'mid'             => $cash->mid,
                'money'           => $cash->money,
                'commission_type' => 'commission_distribute'
            ];
            // @update 吴晓平 修改小程序收益到帐提醒发送订阅模板消息 2019年12月20日 09:47:54
            // 模板发送的初步数据
            $data = [
                'wid' => $wid,
                'openid' => '',
                'param' => []
            ];
            // 发送模板的相关内容
            $param = [
                'mid' => $cash->mid,
                'money' => $cash->money,
                'time' => $cash->created_at->toDateTimeString(),
            ];
            // 组装后的数据
            $sendData = app(SubscribeMessagePushService::class)->packageSendData(6, $data);
            $this->dispatch(new SubMsgPushJob(6, $wid, $sendData, $param));

            $cash->source == 0 && (new MessagePushModule($cash->wid, MessagesPushService::CommissionGrant))->sendMsg($commissionData);
            $cash->source == 1 && (new MessagePushModule($cash->wid, MessagesPushService::CommissionGrant, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($commissionData);
        }

        $res = $cashLogService->up(['id' => $id], ['status' => $status]);
        if ($res) {
            return mysuccess();
        } else {
            return myerror();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 拒绝退款
     * @param CashLogService $cashLogService
     * @param $id
     */
    public function refuse(CashLogService $cashLogService, $id)
    {
        $cash = CashLog::find($id);
        if (!$cash || $cash->wid != session('wid')) {
            return myerror('记录错误');
        }
        if ($cash->status != 0) {
            return myerror('该记录暂时不能拒绝');
        }
        DB::beginTransaction();
        $res = $cashLogService->up(['id' => $id], ['status' => 3]);
        if (!$res) {
            DB::rollBack();
        }

//        $res = Member::where('id',$cash->mid)->increment('cash',$cash->money);
        $res = (new MemberService())->increment($cash->mid, 'cash', $cash->money);
        if ($res) {
            DB::commit();
            return mysuccess();
        } else {
            DB::rollBack();
            return myerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 获取用户信息
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年12月26日 默认分销等级标题
     */
    public function getMember(Request $request, MemberService $memberService, ShopService $shopService)
    {
        $where        = $request->input();
        $wid          = $request->session()->get('wid');
        $where['wid'] = $wid;
        /*$result = $weixinService->getStore($wid);
        $shopData = $result['data'];*/
        $shopData = $shopService->getRowById($wid);
        if ($shopData && $shopData['distribute_grade'] == 1) {
            $where['is_distribute'] = 1;
        }
        $member = $memberService->getListByConditionWithPage($where);
        $res    = DistributeGrade::where('wid', session('wid'))->get(['id', 'title'])->toArray();
        $grade  = [];
        foreach ($res as $val) {
            $grade[$val['id']] = $val['title'];
        }

        $defaultTitle = $shopData['distribute_default_grade_title'] ?? '普通分销员';

        foreach ($member[0]['data'] as &$val) {
            $val['distribute_grade'] = $grade[$val['distribute_grade_id']] ?? $defaultTitle;
        }
        return mysuccess('操作成功', '', $member);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 人脉
     * @update 张永辉 2018年7月18 分销查询修改
     * @update 张永辉 2020年3月28日11:38:05 查询用户sql修改,给一个默认头像
     */
    public function relationship(Request $request, $id)
    {
        $input    = $request->input();
        $page     = $input['page']??1;
        $pagesize = config('database.perPage');
        $offset   = ($page - 1) * $pagesize;

        // $sql = 'SELECT id,headimgurl,truename,mobile,pid,source,buy_num FROM ds_member WHERE pid=' . $id . ' LIMIT ' . $pagesize . ' OFFSET ' . $offset;
        $sql = 'SELECT id,IF(headimgurl="","https://image.huisou.cn/hsshop/image/2020/03/28/1127398539732109_s.png", headimgurl) as headimgurl,truename,mobile,pid,source,buy_num FROM ds_member WHERE pid=' . $id . ' LIMIT ' . $pagesize . ' OFFSET ' . $offset;
        $data['list'] = DB::select($sql);
        if (isset($input['tag']) && $input['tag'] == 1) {
            return mysuccess('操作成功', '', $data);
        }
        $data['member'] = Member::find($id)->toArray();

        $sql  = 'SELECT COUNT(id) as num FROM ds_member WHERE pid=' . $id;
        $temp = DB::select($sql);
//        $data['one'] = [];
//        $data['two'] = 0;
//        $data['thr'] = 0;
//        foreach ($temp as $val){
//            if ($val->one){
//                $data['one'][$val->one] = $val->one;
//            }
//            if ($val->two){
//                $data['two'][$val->two] = $val->two;
//            }
//            if ($val->thr){
//                $data['thr'][$val->thr] = $val->thr;
//            }
//
//        }

        $data['count']['one'] = $temp[0]->num;
        $data['count']['two'] = 0;
        $data['count']['thr'] = 0;

        //分页信息
        $count            = $data['count']['one'];
        $pageNum          = ceil($count / $pagesize);
        $data['pageInfo'] = [
            'pageNow'  => $page,
            'pageSize' => $pagesize,
            'count'    => $count,
            'pageNum'  => $pageNum,
        ];

        $pData = [
            'id'       => '',
            'nickname' => '',
            'mobile'   => '',
        ];
        if ($data['member']['pid']) {
            $pData = (new MemberService())->getRowById($data['member']['pid']);
            $pData = [
                'id'       => $pData['id'],
                'nickname' => $pData['nickname'],
                'mobile'   => $pData['mobile'],
            ];
        }
        $data['pData'] = $pData;
        unset($data['one'], $data['two'], $data['thr']);
        return mysuccess('操作成功', '', $data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170526
     * @desc 佣金流水
     * @param $mid
     * @update 带提现金额精确到两位小数
     */
    public function getIncome(Request $request, $mid)
    {
        $input    = $request->input();
        $page     = $input['page']??1;
        $pagesize = config('database.perPage');
        $offset   = ($page - 1) * $pagesize;

        $where['wid'] = session('wid');
        $where['mid'] = $mid;
        $income       = Income::wheres($where)->skip($offset)->take($pagesize)->get()->load('orderMember')->load('order')->toArray();
        if (isset($input['tag']) && $input['tag'] == 1) {
            return mysuccess('操作成功', '', $income);
        }
        $count           = Income::wheres($where)->count();
        $pageNum         = ceil($count / $pagesize);
        $pageInfo        = [
            'pageNow'  => $page,
            'pageSize' => $pagesize,
            'count'    => $count,
            'pageNum'  => $pageNum,
        ];
        $member          = Member::find($mid)->toArray();
        $where['status'] = ['in', [0, 1]];
        $res             = Income::wheres($where)->sum('money');
        $member['all']   = $res;
        $member['wait']  = $res - $member['cash'];
        $member['wait']  = round($member['wait'],2);
        $data            = [
            'pageInfo' => $pageInfo,
            'member'   => $member,
            'income'   => $income,
        ];

        return mysuccess('操作成功', '', $data);
    }

    public function reDistribute($oid)
    {
        $order = Order::find($oid)->toArray();
        $res   = (new OrderService())->distribute($order, $order['wid']);
        show_debug($res);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171114
     * @desc 添加分销门槛
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addDistributeGrade(Request $request, ShopService $shopService)
    {
        $rule      = Array(
            'distribute_grade' => 'required|in:0,1',
        );
        $message   = Array(
            'distribute_grade.required' => '参数必须传递',
        );
        $input     = $request->input();
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        };
        $wid = session('wid');
        if ($input['distribute_grade'] == 1) {
            $demand = $request->input('demand');
            foreach ($demand as $key => $val) {
                if (!$val) {
                    unset($demand[$key]);
                }
            }
            if (!$demand) {
                error('条件不能为空');
            }
            $data = [
                'distribute_grade' => $input['distribute_grade'],
                'demand'           => json_encode($demand),
            ];
        } else {
            $data['demand'] = '';
            $shopData       = $shopService->getRowById($wid);
            if ($shopData['is_apply_distribute'] == 0) {
                $data['distribute_grade'] = '0';
            }
        }

        //$weixinService->init()->where(['id'=>$wid])->update($data,false);
        $shopService->update($wid, $data);
        //处理老数据
        if ($input['distribute_grade'] == 1) {
            (new DistributeModule())->HandleOldData(session('wid'));
        }

        success('操作成功');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 获取分销人员
     */
    public function getDistributeMember(Request $request, MemberService $memberService)
    {
        $input = $request->input();
        $res   = $memberService->searchMember(['pid' => 0, 'wid' => session('wid')], $input);
        foreach ($res[0]['data'] as &$val) {
            if (!$val['headimgurl']) {
                $val['headimgurl'] = imgUrl() . 'hsshop/image/static/noheadurl.png';
            }
        }
        success('操作成功', '', $res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 添加下级
     * @param Request $request
     */
    public function addJunior(Request $request)
    {
        $rule      = Array(
            'mid' => 'required',
            'ids' => 'required',
        );
        $message   = Array(
            'mid.required' => '参数必须传递',
            'ids.required' => '用户id必须传递',
        );
        $input     = $request->input();
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        };
        if (!is_array($input['ids'])) {
            error('用户必须是数组');
        }
        foreach ($input['ids'] as $key => $val) {
            if ($val == $input['mid']) {
                unset($input['ids'][$key]);
            }
        }
        if (!$input['ids']) {
            error('请选择除自己以外的分销商');
        }
        (new DistributeModule())->addJunior($input['mid'], $input['ids'], session('wid')) ? success() : error();

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171120
     * @desc 添加佣金
     * @param Request $request
     */
    public function addCash(Request $request, DistributeModule $distributeModule)
    {
        $input     = $request->input();
        $rule      = Array(
            'id'   => 'required',
            'cash' => 'required|numeric|min:1'
        );
        $message   = Array(
            'id.required'   => '用户id不能为空',
            'cash.required' => '金额不能为空',
            'cash.min'      => '金额最低为1元',
            'cash.numeric'  => '必须为数值',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $res = $distributeModule->addCash($input['id'], $input['cash'], session('wid'));
        if ($res['errCode'] != 0) {
            error($res['errMsg']);
        } else {
            success();
        }
    }


    /*
    * @desc 佣金数据导出 .xls格式
    * @付国维   20180108
    */
    public function exportXlsApi(Request $request, CashLogService $cashLogService)
    {
        $input = $request->input();
//        $input = [
//            'all' => 2,
//        ];
        if (!$input) {
            error('请选中导出的数据');
        }
        $wid = session('wid');
        if (!$wid) {
            error('您没有权限导出，请先登录');
        }
        //查询条件
        $where['wid'] = $wid;
        if ($input['all'] == 1) {
            $data        = explode(',', $input['orderids']);
            $where['id'] = ['in', $data];
            $cashData    = CashLog::wheres($where)->orderBy('id', 'desc')->get()->load('member')->toArray();
            $cashLogService->exportExcel($cashData);
        } else {
            $cashData = CashLog::wheres($where)->orderBy('id', 'desc')->get()->load('member')->toArray();
            $cashLogService->exportExcel($cashData);
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180408
     * @desc 开启企业打款
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function openCompanyPay(Request $request, ShopService $shopService)
    {
        $input     = $request->only(['company_pay']);
        $rule      = Array(
            'company_pay' => 'required|in:0,1,2'
        );
        $message   = Array(
            'company_pay.required' => '参数错误',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $wid      = session('wid');
        $shopData = $shopService->getRowById($wid);
        if ((new CashLogService())->isExist($wid) && in_array($input['company_pay'], [0, 1]) && in_array($shopData['company_pay'], [0, 1])) {
            error('您存在未处理的提现申请，请先处理后再修改该功能');
        }
        if ($shopService->update($wid, $input)) {
            success();
        }
        error();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180507
     * @desc 获取推广来源数据
     */
    public function getSourceInfo(Request $request, MemberService $memberService)
    {
        $ids    = ['127011', '231431', '231446'];
        $source = [
            '127011' => '小程序',
            '231431' => '公众号文章',
            '231446' => '分众电梯',
        ];

        $where = [];
        if (!empty($input['pid'])) {
            $where['topid'] = $input['pid'];
        }
        if (!empty($input['nickname'])) {
            $where['nickname'] = $input['nickname'];
        }
        if (!empty($input['sex'])) {
            $where['sex'] = $input['sex'];
        }
        if (!empty($input['starttime'])) {
            $where['intime'] = ['between', [$input['starttime'], $input['endtime']]];
        }
        if (!empty($input['is_open_groups'])) {
            if ($input['is_open_groups'] == 1) {
                $where['is_open_groups'] = 1;
            } else {
                $where['is_open_groups'] = 0;
            }
        }
        $adstatService = new AdStatisticService();
        $memberData    = $adstatService->getListWithPage($where);
        return view('merchants.distribute.getSourceInfo', array(
            'title'      => '广告来源',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'template',
            'memberData' => $memberData,
            'source'     => $source,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180514
     * @desc 刷新数据
     */
    public function refresh(MemberService $memberService)
    {
        //
        $start_time = date('Y-m-d', time()) . ' 00:00:00';
        $end_time   = date('Y-m-d H:i:s', time());
        $where      = [
            'wid'        => ['in', ['626', '661']],
            'pid'        => ['<>', '0'],
            'created_at' => ['between', [$start_time, $end_time]]
        ];
        $memberData = $memberService->getList($where);
        foreach ($memberData as $val) {
            $this->_deal($val, $val);
        }
        $sql  = 'SELECT ads.mid FROM ds_ad_statistic as ads LEFT JOIN  ds_groups_detail as gd ON ads.mid = gd.member_id WHERE ads.is_open_groups=0 AND gd.id IS NOT NULL GROUP BY mid';
        $data = DB::select($sql);
        $data = json_decode(json_encode($data), true);
        $ids  = array_column($data, 'mid');
        if ($ids) {
            AdStatistic::whereIn('mid', $ids)->update(['is_open_groups' => '1']);
        }

        return redirect('merchants/distribute/getSourceInfo');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180512
     * @desc 处理数据
     * @param $data
     */
    private function _deal($tempData, $data, $level = 1)
    {
        $memberService = new MemberService();
        $ids           = ['127011', '231431', '231446'];
        $source        = [
            '127011' => '小程序',
            '231431' => '公众号文章',
            '231446' => '分众电梯',
        ];
        if (in_array($tempData['pid'], $ids)) {
            $adData = [
                'mid'        => $data['id'],
                'wid'        => $data['wid'],
                'nickname'   => $data['nickname'],
                'headimgurl' => $data['headimgurl'],
                'sex'        => $data['sex'],
                'pid'        => $data['pid'],
                'topid'      => $tempData['pid'],
                'intime'     => $data['created_at'],
                'level'      => $level,
            ];
            try {
                $res = (new AdStatisticService())->model->where('mid', $data['id'])->get()->toArray();
                if (!$res) {
                    AdStatistic::insertGetId($adData);
                }

            } catch (\Exception $exception) {
                \Log::info($exception->getMessage());
            }
            return true;

        } elseif (!in_array($tempData['pid'], $ids) && $tempData['pid'] != 0) {
            if ($level > 10) {
                return true;
            }
            $val = $memberService->getRowById($tempData['pid']);
            $this->_deal($val, $data, $level + 1);
            return true;
        }

    }

    /*
     * @date 20180514
     * @desc 最低提现金额
     * @desc 最低提现金额
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function withdrawGrade(Request $request, ShopService $shopService)
    {
        $input     = $request->input();
        $rule      = Array(
            'withdraw_grade' => 'required|integer',
        );
        $message   = Array(
            'withdraw_grade.required' => '请输入最低提现金额',
            'withdraw_grade.integer'  => '最低提现金额必须是整数',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $wid = session('wid');
        //$res = $weixinService->init()->where(['id'=>$wid])->update(['withdraw_grade'=>$input['withdraw_grade']],false);
        $res = $shopService->update($wid, ['withdraw_grade' => $input['withdraw_grade']]);
        if ($res) {
            success();
        } else {
            error();
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getGroupsInfo(Request $request)
    {
        $mid                 = $request->input('mid');
        $groupsDetailService = new GroupsDetailService();
        $res                 = $groupsDetailService->getListByWhere(['member_id' => $mid]);
        if (!$res) {
            error('该用户未参加拼团活动');
        }
        $remarkNo = [];
        foreach ($res as $val) {
            $remarkNo[] = $val['remark_no'];
        }
        $data = (new MeetingGroupsRuleModule())->getRemark(0, [], $mid);
        return view('merchants.distribute.getGroupsInfo', array(
            'title'    => '注册信息列表',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'togetherGroupList',
            'result'   => $data,
        ));
    }


    /**
     * 获取分销数据
     * @param Request $request
     * @author 张永辉 2018年9月6日
     */
    public function getOrderList(Request $request, MemberService $memberService)
    {
        $mid  = $request->input('mid');
        $data = $memberService->model->where('pid', $mid)->get(['id'])->toArray();
        $mids = array_column($data, 'id');
        if (!$mids) {
            success('操作成功', '', []);
        }
        $input        = $request->input();
        $where['mid'] = ['in', $mids];
        if (!empty($input['type'])) {
            switch ($input['type']) {
                case '1':
                    $time       = date('Y-m-d', strtotime($input['start_time']));
                    $start_time = $time . ' 00:00:00';
                    $end_time   = $time . ' 23:59:59';
                    break;
                case '2':
                    $start_time = date('Y-m-d H:i:s', strtotime($input['start_time']));
                    $end_time   = date('Y-m-d H:i:s', strtotime('+1 months', strtotime($input['start_time'])) - 1);
                    break;
                case '3':
                    $start_time = $input['start_time'] . ' 00:00:00';
                    $end_time   = $input['end_time'] . ' 23:59:59';
                    break;
            }
            if (!empty($start_time) && !empty($end_time)) {
                $where['created_at'] = ['between', [$start_time, $end_time]];
            }
        }
        $orderService = new OrderService();
        $orderData    = $orderService->init()->where($where)->getlist();
        if (!$orderData[0]['data']) {
            success('操作成功', '', $orderData);
        }
        $mids       = array_unique(array_column($orderData[0]['data'], 'mid'));
        $res        = (new MemberService())->getListById($mids);
        $memberData = [];
        array_map(function ($item) use (&$memberData) {
            $memberData[$item['id']] = $item['nickname'];
        }, $res);
        foreach ($orderData[0]['data'] as &$val) {
            $val['nickname'] = $memberData[$val['mid']];
        }
        success('操作成功', '', $orderData);
    }


    /**
     * 设置用户申请成为分销，开启关闭分销申请
     * @author 张永辉 2018年9月21日
     */
    public function applyDistribut(Request $request, $status, ShopService $shopService, DistributeApplayPageService $distributeApplayPageService)
    {
        if (!in_array($status, [0, 1])) {
            error('参数错误');
        }
        $wid      = session('wid');
        $shopData = $shopService->getRowById($wid);
        if ($status == 1) {
            (new DistributeModule())->HandleOldData($wid);
            $res = $distributeApplayPageService->model->where('wid', $wid)->get(['id'])->toArray();
            if (!$res) {
                $str        = '[{"showRight":false,"cardRight":3,"type":"rich_text","content":"<p style=\"line-height: 1.75em;\"><!-- ngIf: editor[\'content\'] --><\/p><p style=\"margin: 5px 0px; line-height: 1.75em;\"><strong><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">1.<\/span><span style=\";color:black\">\u5e97\u94fa\u4ecb\u7ecd<\/span><\/strong><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.75em;\"><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">&nbsp; &nbsp;<\/span><span style=\"font-size: 14px;\"><span style=\"font-family: Arial, sans-serif; color: black;\">XX<\/span><span style=\"color: black;\">\u5e97\u94fa\u76ee\u524d\u5df2\u4e0a\u67b6\u7684\u4ea7\u54c1\u8d85\u8fc7<\/span><span style=\"font-family: Arial, sans-serif; color: black;\">120<\/span><span style=\"color: black;\">\u6b3e\uff0c\u6db5\u76d6\u7c7b\u76ee\u5305\u62ec\uff1a\u670d\u88c5\u978b\u5e3d\u3001\u7f8e\u5986\u9996\u9970\u3001\u5316\u5986\u54c1\u3001\u751f\u6d3b\u65e5\u7528\u54c1\u7b49\uff0c\u4ea7\u54c1\u5b9a\u4f4d\u4e8e\u5973\u6027\u6d88\u8d39\uff0c\u4ea7\u54c1\u8d28\u91cf\u5f97\u5230\u6d88\u8d39\u8005\u4e00\u81f4\u597d\u8bc4\u4e0e\u9752\u7750\uff0c\u5e97\u94fa\u5b97\u65e8\u5bf9\u4ea7\u54c1\u9ad8\u5ea6\u8d1f\u8d23\uff0c\u8ba9\u6d88\u8d39\u8005\u653e\u5fc3\u8d2d\u4e70\u4f7f\u7528\uff0c\u670d\u52a1\u66f4\u591a\u7684<\/span><span style=\"font-family: Arial, sans-serif; color: black;\">C<\/span><span style=\"color: black;\">\u7aef\u7528\u6237<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.75em;\"><span style=\"font-size: 14px;\"><span style=\"color: black;\"><br\/><\/span><\/span><\/p>","editing":"editing","bgcolor":"#ffffff","initShow":true,"is_add_content":false},{"showRight":true,"cardRight":25,"type":"cube","editing":"editing","is_add_content":false,"isPromptAddPic":false,"isAddPic":true,"telType":1,"margin":0,"addTitle":false,"aspectRatio":0.81818181818181823,"resize_image":1,"position":[{"width":1,"height":1,"top":0,"left":0},{"width":1,"height":1,"top":0,"left":1},{"width":1,"height":1,"top":0,"left":2}],"content":[{"type":0,"id":0,"linkTitle":"","img":"hsshop\/image\/2018\/10\/18\/1421506536114190.png","title":""},{"type":0,"id":0,"linkTitle":"","img":"hsshop\/image\/2018\/10\/18\/1421506536114190.png","title":""},{"type":0,"id":0,"linkTitle":"","img":"hsshop\/image\/2018\/10\/18\/1421506536114190.png","title":""}]},{"showRight":false,"cardRight":3,"type":"rich_text","content":"<p style=\"line-height: 1.5em;\"><!-- ngIf: editor[\'content\'] --><\/p><p style=\"margin: 5px 0px; line-height: 1.5em;\"><strong><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">2.<\/span><span style=\";color:black\">\u5e97\u94fa\u5206\u9500\u7b80\u4ecb<\/span><\/strong><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"font-size: 14px;\"><span style=\"color: black;\">\u6839\u636e\u516c\u53f8\u6218\u7565\u89c4\u5212\uff0c\u8ba9\u66f4\u591a\u6d88\u8d39\u8005\u4f7f\u7528<\/span><span style=\"font-family: Arial, sans-serif; color: black;\">XX<\/span><span style=\"color: black;\">\u5e97\u94fa\u7684\u4ea7\u54c1\uff0c\u73b0\u9488\u5bf9\u5e97\u94fa\u9002\u5f53\u4ea7\u54c1\u5f00\u542f\u5206\u9500\u73a9\u6cd5\uff0c\u5982\u679c\u4f60\u7684\u6d89\u730e\u548c\u4ea4\u9645\u8f83\u5e7f\uff0c\u4e14\u5174\u8da3\u5e7f\u6cdb\uff0c\u5728\u5de5\u4f5c\u751f\u6d3b\u4e4b\u5916\u8d5a\u53d6\u90e8\u5206\u6536\u76ca\uff0c\u90a3\u4e48\u4f60\u53ef\u4ee5\u52a0\u5165\u6211\u4eec\u6210\u4e3a\u5e97\u94fa\u4e00\u5458\uff0c\u901a\u8fc7\u5c06\u5e97\u94fa\u5546\u54c1\u5206\u4eab\u7ed9\u5176\u4ed6\u7528\u6237\u5e76\u4ea7\u751f\u8d2d\u4e70\u5373\u53ef\u83b7\u5f97\u4e30\u539a\u4f63\u91d1\uff0c\u5df2\u83b7\u5f97\u7684\u4f63\u91d1\u53ef\u7528\u4e8e\u63d0\u73b0\u81f3\u94f6\u884c\u5361\u6216\u5176\u4ed6\u6e20\u9053\u3002<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\">&nbsp;<\/p>","editing":"editing","bgcolor":"#ffffff","initShow":true,"is_add_content":false},{"showRight":true,"cardRight":5,"type":"image_ad","editing":"editing","advsListStyle":2,"advSize":1,"resize_image":1,"images":[{"user_id":269,"file_info_id":336698,"file_classify_id":0,"created_at":"2018-10-18 14:22:18","updated_at":"2018-10-18 14:22:18","deleted_at":null,"weixin_id":303,"file_mine":1,"file_cover":"","FileInfo":{"id":336698,"path":"hsshop\/image\/2018\/10\/18\/1422184648784715.png","s_path":"hsshop\/image\/2018\/10\/18\/1422182747385703_s.png","m_path":"hsshop\/image\/2018\/10\/18\/14221811139537_m.png","l_path":"hsshop\/image\/2018\/10\/18\/1422186357221096_l.png"},"image_id":336698}],"is_add_content":false},{"showRight":false,"cardRight":3,"type":"rich_text","content":"<p style=\"line-height: 1.5em;\"><!-- ngIf: editor[\'content\'] --><\/p><p style=\"margin: 5px 0px; line-height: 1.5em;\"><strong><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">3.<\/span><span style=\";color:black\">\u5982\u4f55\u6210\u4e3a\u5206\u9500\uff1a<\/span><\/strong><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"color: black; font-size: 14px;\">&nbsp; &nbsp;\u63d0\u4ea4\u5206\u9500\u7533\u8bf7\u4e14\u901a\u8fc7\u5546\u5bb6\u540e\u53f0\u5ba1\u6838\uff0c\u5373\u53ef\u6210\u4e3a\u5e97\u94fa\u5206\u9500\u5ba2\u3002<\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><strong><span style=\"font-family:&#39;Arial&#39;,&#39;sans-serif&#39;;color:black\">4.<\/span><span style=\";color:black\">\u5206\u9500\u8bf4\u660e\uff1a<\/span><\/strong><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-size: 14px;\"><span style=\"font-family: Arial, sans-serif; color: black;\">1\uff09<\/span><span style=\"color: black;\">\u53ea\u6709\u5148\u7533\u8bf7\u6210\u4e3a\u5e97\u94fa\u5206\u9500\u5ba2\uff0c\u624d\u80fd\u901a\u8fc7\u5206\u4eab\u5206\u9500\u5546\u54c1\u83b7\u5f97\u5bf9\u5e94\u4f63\u91d1\uff0c\u672a\u6210\u4e3a\u5206\u9500\u5ba2\u5206\u4eab\u5546\u54c1\uff0c\u5373\u4f7f\u7528\u6237\u8d2d\u4e70\u5e76\u652f\u4ed8\uff0c\u4f60\u4e5f\u4e0d\u80fd\u83b7\u5f97\u5bf9\u5e94\u4f63\u91d1<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-size: 14px;\"><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">2\uff09<\/span><span style=\"font-size: 14px; color: black;\">\u6210\u4e3a\u5206\u9500\u5ba2\u540e\uff0c\u4f60\u53ef\u4ee5\u5728\u5e97\u94fa<\/span><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">-<\/span><span style=\"font-size: 14px; color: black;\">\u4f1a\u5458\u4e2d\u5fc3<\/span><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">-<\/span><span style=\"font-size: 14px; color: black;\">\u6211\u7684\u5206\u9500<\/span><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">-<\/span><span style=\"font-size: 14px; color: black;\">\u5206\u9500\u5546\u54c1\u9009\u53d6\u5408\u9002\u7684\u5546\u54c1\u8fdb\u884c\u5206\u9500\u6d3b\u52a8\uff0c\u53ea\u8981\u4fc3\u6210\u7528\u6237\u8d2d\u4e70\u5373\u53ef\u83b7\u5f97\u5bf9\u5e94\u4f63\u91d1<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-size: 14px;\"><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">3<\/span><span style=\"font-size: 14px; color: black;\">\uff09\u6210\u4e3a\u5206\u9500\u540e\uff0c\u5df2\u83b7\u5f97\u4f63\u91d1\u5206\u4e3a\u53ef\u63d0\u73b0\u548c\u5f85\u63d0\u73b0\u4e24\u4e2a\u90e8\u5206\uff0c\u53ef\u63d0\u73b0\u91d1\u989d\u4f60\u53ef\u4ee5\u6839\u636e\u63d0\u73b0\u6307\u5f15\u63d0\u53d6\u5230\u4f60\u7684\u94f6\u884c\u5361\u6216\u652f\u4ed8\u5b9d\u8d26\u53f7\uff1b\u5f85\u63d0\u73b0\u91d1\u989d\u9700\u8981\u7b49\u7528\u6237\u786e\u5b9a\u6536\u8d27\u6216\u6536\u8d27\u540e<\/span><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">15<\/span><span style=\"font-size: 14px; color: black;\">\u5929\u624d\u80fd\u63d0\u73b0\uff0c\u6240\u6709\u7684\u63d0\u73b0\u7533\u8bf7\u5747\u9700\u8981\u5546\u5bb6\u5ba1\uff08\u5177\u4f53\u60c5\u51b5\u8bf7\u4ee5\u5546\u5bb6\u8bbe\u7f6e\u89c4\u5219\u4e3a\u51c6\uff09<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-size: 14px;\"><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">4<\/span><span style=\"font-size: 14px; color: black;\">\uff09\u5bf9\u4e8e\u5206\u9500\u5ba2\u5b58\u5728\u6076\u610f\u5237\u5355\u6216\u83b7\u5229\u7684\u60c5\u51b5\uff0c\u5546\u5bb6\u67e5\u5b9e\u6838\u5bf9\u540e\u6709\u6743\u76f4\u63a5\u6e05\u9000\u5176\u4e0b\u7ea7\u5168\u90e8\u7528\u6237\u6216\u51bb\u7ed3\u7528\u6237\u63d0\u73b0\u91d1\u989d<\/span><\/span><\/p><p style=\"margin: 5px 0px; font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; word-spacing: 0px; line-height: 1.5em;\"><span style=\"font-size: 14px;\"><span style=\"font-size: 14px; font-family: Arial, sans-serif; color: black;\">5<\/span><span style=\"font-size: 14px; color: black;\">\uff09\u8bf7\u9075\u5b88\u56fd\u5bb6\u53ca\u5730\u65b9\u6cd5\u5f8b\u6cd5\u89c4\u89c4\u5b9a\uff0c\u4e0d\u5bf9\u793e\u4f1a\u9020\u6210\u5371\u5bb3\u3002<\/span><\/span><\/p><p style=\"line-height: 1.5em;\"><!-- end ngIf: editor[\'content\'] -->\n &nbsp; &nbsp;<!-- ngIf: !editor[\'content\'] --><\/p>","editing":"editing","bgcolor":"#ffffff","initShow":true,"is_add_content":false}]';
                $insertData = [
                    'wid'           => $wid,
                    'title'         => '店铺分销客申请样板',
                    'bg_color'      => '#ffffff',
                    'template_info' => $str,
                ];
                $distributeApplayPageService->add($insertData);
            }
            $data = [
                'is_apply_distribute' => '1',
                'distribute_grade'    => '1',
            ];
        } else {
            $data['is_apply_distribute'] = 0;
            $shopData['demand'] || $data['distribute_grade'] = 0;
        }
        $shopService->update($wid, $data);
        success();
    }


    /**
     * 分销申请模板列表
     * @author 张永辉 2018年9月29日
     */
    public function applyList(Request $request, DistributeApplayPageService $distributeApplayPageService)
    {
        $where['wid'] = session('wid');
        $data         = $distributeApplayPageService->getlistPage($where);
        return view('merchants.distribute.applyList', array(
            'title'    => '申请模板',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'template',
            'data'     => $data,
        ));
    }

    /**
     * 删除分销申请模板
     * @author 张永辉 2018年9月29日
     */
    public function delApplyList(Request $request, DistributeApplayPageService $distributeApplayPageService)
    {
        $input = $request->input();
        if (empty($input['ids'])) {
            error('请选择删除的模板');
        }
        $input['ids'] = explode(',', $input['ids']);
        $where        = [
            'id'  => ['in', $input['ids']],
            'wid' => ['!=', session('wid')],
        ];
        $data         = $distributeApplayPageService->getList($where);
        $data && error('该模板不属于你');
        $distributeApplayPageService->batchDel($input['ids']) && success();
        error();
    }


    /**
     * 添加申请页面模板
     * @author 张永辉 2018年9月29日
     */
    public function addApplyPage(Request $request, DistributeApplayPageService $distributeApplayPageService)
    {
        $input = $request->input();
        if ($request->isMethod('post')) {
            $rule      = Array(
                'title'         => 'required',
                'bg_color'      => 'required',
                'template_info' => 'required',
            );
            $message   = Array(
                'title.required'         => '标题不能为空',
                'bg_color.required'      => '背景颜色不能为空',
                'template_info.required' => '内容不能为空',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $insertData   = [
                'wid'           => session('wid'),
                'title'         => $input['title'],
                'bg_color'      => $input['bg_color'],
                'description'   => $input['description']??'',
                'share_title'   => $input['share_title']??'',
                'share_img'     => $input['share_img']??'',
                'share_desc'    => $input['share_desc']??'',
                'template_info' => $input['template_info'],
            ];
            $templateInfo = json_decode($insertData['template_info'], true);
            if ($templateInfo) {
                $insertData['template_info'] = MallModule::processTemplateData(session('wid'), $insertData['template_info'], 1);
            }
            if (empty($input['id'])) {
                $distributeApplayPageService->add($insertData) && success();
                error();
            } else {
                $distributeApplayPageService->update($input['id'], $insertData) && success();
                error();
            }

        }
        $data = [
            'is_custom'     => '1',
            'template_info' => '',
        ];
        if (!empty($input['id'])) {
            $data = $distributeApplayPageService->getRowById($input['id']);
        }
        return view('merchants.distribute.addApplyPage', array(
            'title'    => '申请模板',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'togetherGroupList',
            'data'     => $data,
        ));

    }


    /**
     * 分销申请人列表
     * @author 张永辉 2018年9月29日
     */
    public function applayMemberList(Request $request, ShopService $shopService, DistributeApplayLogService $distributeApplayLogService, DistributeModule $distributeModule, MemberService $memberService)
    {
        $input     = $request->input();
        $tempWhere = [];
        if (!empty($input['mobile'])) {
            $tempWhere['mobile'] = $input['mobile'];
        }
        if (!empty($input['nickname'])) {
            $tempWhere['nickname'] = $input['nickname'];
        }
        if (!empty($input['nickname'])) {
            $tempWhere['nickname'] = $input['nickname'];
        }
//        if (!empty($input['buy_num']) && $input['buy_num'] != '-1') {
//            $tempWhere['buy_num'] = ['>', $input['buy_num']];
//        }

        $where['wid'] = session('wid');
        if ($tempWhere) {
            $tempWhere['wid'] = session('wid');
            $data             = $memberService->model->wheres($tempWhere)->get(['*'])->toArray();
            $data ? $where['mid'] = ['in', array_column($data, 'id')] : $where['mid'] = 0;
        }
        if (!empty($input['start_time']) && !empty($input['end_time'])) {
            $where['created_at'] = ['between', [$input['start_time'], $input['end_time']]];
        }
        if (isset($input['status']) && $input['status'] != '') {
            $where['status'] = $input['status'];
        }
        $data = $distributeApplayLogService->getlistPage($where);
        if ($data[0]['data']) {
            $data[0]['data'] = $distributeModule->applyMember($data[0]['data']);
        }
        $shopData = $shopService->getRowById(session('wid'));
        return view('merchants.distribute.applyMemberList', array(
            'title'    => '申请模板',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'partner',
            'data'     => $data,
            'shopData' => $shopData,
        ));
    }


    /**
     * 审核分销客申请
     * @author 张永辉 2018年9月29日
     * @update 何书哲 2018年11月09日 发送审核通过成为分销客消息通知
     * @update 吴晓平 修改小程序申请成为分销客发送订阅模板消息 2019年12月20日 09:47:54
     */
    public function checkApplyMember(Request $request, DistributeApplayLogService $distributeApplayLogService, MemberService $memberService, $id, $status)
    {
        if (!in_array($status, ['1', '2'])) {
            error('审核参数错误');
        }
        $res = $distributeApplayLogService->model->find($id);
        if (!$res || $res->wid != session('wid')) {
            error('操作非法');
        }
        $distributeApplayLogService->update($id, ['status' => $status, 'reason' => $request->input('reason')]);
        // @update 吴晓平 修改小程序申请成为分销客发送订阅模板消息 2019年12月20日 09:47:54
        // 模板发送的初步数据
        $data = [
            'wid' => $res->wid,
            'openid' => '',
            'param' => []
        ];
        // 发送模板的相关内容
        $param = [
            'mid' => $res->mid,
            'status' => $status
        ];
        // 组装后的数据
        $sendData = app(SubscribeMessagePushService::class)->packageSendData(3, $data);
        $this->dispatch(new SubMsgPushJob(3, $res->wid, $sendData, $param));
        if ($status == '1') {
            $result = $memberService->updateData($res['mid'], ['is_distribute' => '1']);
            //何书哲 2018年11月09日 发送审核通过成为分销客消息通知
            (new MessagePushModule(session('wid'), MessagesPushService::BecomePromoter))->sendMsg(['mid' => $res['mid']]);
        }
        success();
    }

    /**
     * 清退接口
     * @author 张永辉
     */
    public function purge(Request $request, MemberService $memberService, DistributePurgeLogService $distributePurgeLogService, $mid)
    {
        $wid = session('wid');
        $res = $memberService->getList(['id' => $mid, 'wid' => $wid]);
        if (!$res) {
            error('该用户不存在');
        }
        $res        = current($res);
        $insertData = [
            'wid'    => $wid,
            'mid'    => $mid,
            'source' => $res['source'],
            'reason' => $request->input('reason'),
        ];
        $distributePurgeLogService->add($insertData);
        $memberService->updateData($mid, ['is_distribute' => 0]);
        success();
    }


    /**
     * 清退记录
     * @author 张永辉 2018年10月9日
     */
    public function purgeLog(DistributePurgeLogService $distributePurgeLogService, MemberService $memberService, Request $request)
    {
        $input    = $request->input();
        $tmpwhere = [];
        if (!empty($input['nickname'])) {
            $tmpwhere['nickname'] = $input['nickname'];
        }
        if (!empty($input['mobile'])) {
            $tmpwhere['mobile'] = $input['mobile'];
        }

        if ($tmpwhere) {
            $tmpwhere['wid'] = session('wid');
            $memberData      = $memberService->model->wheres($tmpwhere)->get(['id'])->toArray();
            $ids             = array_column($memberData, 'id');
        }
        $logWhere['wid'] = session('wid');
        if (isset($ids)) {
            $logWhere['mid'] = ['in', [0]];
        }
        if (!empty($ids)) {
            $logWhere['mid'] = ['in', $ids];
        }
        if (!empty($input['source'])) {
            $logWhere['source'] = $input['source'];
        }
        $logData = $distributePurgeLogService->getlistPage($logWhere);
        if ($logData[0]['data']) {
            $mids       = array_column($logData[0]['data'], 'mid');
            $mids       = array_unique($mids);
            $where      = [
                'id' => ['in', $mids]
            ];
            $res        = $memberService->getList($where);
            $memberData = [];
            foreach ($res as $val) {
                $memberData[$val['id']] = $val;
            }
            foreach ($logData[0]['data'] as &$item) {
                $item['member'] = $memberData[$item['mid']]??[];
            }
        }
        return view('merchants.distribute.purgeLog', array(
            'title'    => '分销伙伴',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'partner',
            'data'     => $logData,
            'source'   => ['公众号关注', '公众号关注', '分享', '导入', '录入', '小程序', '小程序'],
        ));
    }


    /**
     * 开启关闭自动审核
     * @author 张永辉 2018年10月11日
     */
    public function autoCheck(ShopService $shopService, $status)
    {
        if (!in_array($status, ['0', '1'])) {
            error('审核状态错误');
        }
        $shopService->update(session('wid'), ['is_auto_check' => $status]) && success();
        error();
    }


    /**
     * 获取生成二维码
     * @param Request $request
     * @author 张永辉
     */
    public function qrCode(Request $request)
    {
        $url = $request->input('url');
        if (!$url) {
            error('连接不能为空');
        }
        $size = $request->input('size', '200');
        $data = QrCode::size($size)->generate($url);
        success('操作成功', '', $data);
    }

    /**
     * 添加店铺分销员等级
     * @author 张永辉 2018年12月06日
     */
    public function addStoreDistributeGrade(Request $request, DistributeModule $distributeModule)
    {
        $input     = $request->input();
        $rule      = Array(
            'title' => 'required',
            'pids'  => 'required',
        );
        $message   = Array(
            'title.required' => '标题不能为空',
            'pids.required'  => '商品不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $input['wid'] = session('wid');
        $res          = $distributeModule->addStoreDistributeGrade($input);
        $res['errCode'] && error($res['errMsg']);
        success('', '', $res['data']);
    }


    /**
     * 删除分销等级
     * @author 张永辉 2018年12月6日
     */
    public function delStoreDistributeGrade(Request $request, DistributeModule $distributeModule)
    {
        $input     = $request->input();
        $rule      = Array(
            'id' => 'required',
        );
        $message   = Array(
            'id.required' => 'id不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $res = $distributeModule->delStoreDistributeGrade($input['id']);
        success('操作成功', '', $res['data']);
    }


    /**
     * 设置用户分销等级
     * @author 张永辉 2018年12月26日
     */
    public function setMemberDistributeGrade(Request $request, MemberService $memberService)
    {
        $input     = $request->input();
        $rule      = Array(
            'mids' => 'required',
            'gid'  => 'required',
        );
        $message   = Array(
            'mids.required' => '用户id不能为空',
            'gid.required'  => '等级id',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        if ($input['gid']) {
            $data = DistributeGrade::find($input['gid']);
            if (!$data) {
                error('等级不存在');
            }
        }
        $mids = explode(',', $input['mids']);
        $memberService->batchUpdate($mids, ['distribute_grade_id' => $input['gid']]);
        success();
    }


    /**
     * 获取分销等级
     * @author 张永辉 2018年12月26日
     */
    public function getDistributeGrade()
    {
        $res    = DistributeGrade::where('wid', session('wid'))->get(['id', 'title'])->toArray();
        $data[] = [
            'id'    => 0,
            'title' => Weixin::where('id', session('wid'))->value('distribute_default_grade_title'),
        ];
        $res    = array_merge($res, $data);
        success('', '', $res);
    }


    /**
     * 设置顶级分销
     * @author 张永辉 2019年1月4日
     */
    public function setDistributeTopLevel(Request $request, MemberService $memberService)
    {
        $input     = $request->input();
        $rule      = Array(
            'mid'  => 'required',
            'type' => 'required',
        );
        $message   = Array(
            'mid.required'  => '用户id不能为空',
            'type.required' => 'type不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $memberData = $memberService->getRowById($input['mid']);
        if (empty($memberData) || $memberData['wid'] != session('wid')) {
            error('该用户不属于你');
        }
        if ($input['type'] == 1) {
            if ($memberData['pid'] != 0) {
                $memberService->decrement($memberData['pid'], 'son_num', 1);
            }
            $res = $memberService->batchUpdate([$input['mid']], ['distribute_top_level' => 1, 'pid' => 0]);
        } else {
            $res = $memberService->batchUpdate([$input['mid']], ['distribute_top_level' => 0]);
        }
        $res && success();
        error();
    }


}
















