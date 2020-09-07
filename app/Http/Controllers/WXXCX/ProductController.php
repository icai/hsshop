<?php
/**
 * 商品类
 * Created by PhpStorm.
 * User: Herry
 * Date: 2017/8/11
 * Time: 13:46
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Module\DiscountModule;
use App\Module\ProductModule;
use App\S\File\FileInfoService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductGroupService;
use App\Services\ProductEvaluatePraiseService;
use Illuminate\Http\Request;
use ProductService;
use MemberCardRecordService;
use Validator;
use ProductEvaluateService;
use ProductEvaluateDetailService;
use CommonModule;
use MallModule;

class ProductController extends Controller
{
    /**
     * 商品列表
     * @update 陈文豪 2018年7月20日 修改搜索条件
     * @update 何书哲 2018年7月24日 小程序商品模糊搜索因分页下拉条数不一致，先注释掉
     * @update 何书哲 2019年03月11日 title参数不为空，查询满足条件的商品名称和商品编码的商品
     */
    public function index(Request $request)
    {
        $fuzzyWhere = [];
        $params = $request->input();

        // 查询条件
        $where = ['wid' => $params['wid']];
        $fuzzyWhere['wid'] = $params['wid'];

        // todo 小程序还没实现核销 暂时过滤核销商品 实现核销后删除此条件
        // $where['is_hexiao'] = 0;

        // 出售状态 出售中 1 已售罄 -2 仓库中 0
        if (isset($params['status']) && $params['status'] === 0) {
            $where['status'] = 0;
            $fuzzyWhere['status'] = 0;
        } else if (isset($params['status']) && $params['status'] == -2) {
            $where['stock'] = array('<=', 0);
            // $where['status'] = array('>=', 0);
            $fuzzyWhere['status'] = -2;
        } else {
            $where['status'] = 1;
            $where['stock'] = array('>', 0);
            $fuzzyWhere['status'] = 1;
        }

        // 检索条件
        // title参数不为空，查询满足条件的商品名称和商品编码的商品
        if (!empty($params['title'])) {

            $productTitleIds = (new \App\S\Product\ProductService())->getProductIdsByTitle($params['wid'], $params['title']);

            if (isset($where['_string'])) {
                if ($productTitleIds) {
                    $where['_string'] .= " ADD (title LIKE '%" . $params['title'] . "%' OR id IN (" . implode(',', $productTitleIds) . "))";
                } else {
                    $where['_string'] .= " ADD (title LIKE '%" . $params['title'] . "%')";
                }
            } else {
                if ($productTitleIds) {
                    $where['_string'] = " (title LIKE '%" . $params['title'] . "%' OR id IN (" . implode(',', $productTitleIds) . "))";
                } else {
                    $where['_string'] = " (title LIKE '%" . $params['title'] . "%')";
                }
            }

        }
        if (!empty($params['group_id'])) {
            $params['group_id'] = addslashes(strip_tags($params['group_id']));
            if (isset($where['_string'])) {
                $where['_string'] .= ' AND FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
            } else {
                $where['_string'] = ' FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
            }
            $fuzzyWhere['groupId'] = $params['group_id'];
        }

        if (!empty($params['title']) && config('app.fuzzy_search_url')) {
            // 商品分词查询
            $fuzzyWhere['keyword'] = $params['title'];
            if ($page = ($request->input('page') ?? 1)) {
                $fuzzyWhere['pageNum'] = $page;
            }
            $fuzzyWhere['pageSize'] = 15;
            $res = jsonCurl(config('app.fuzzy_search_url'), $fuzzyWhere);
            if ($res['code'] == 100) {
                $list['data'] = array_merge([], ProductService::getListById(array_column($res['data']['list'], 'id')));
            } else {
                xcxerror('系统维护中，请联系客服');
            }
        } else {
            // 获取列表
            list($list) = ProductService::listWithPage($where);
        }

        xcxsuccess('', ['data' => $list['data']]);
    }

    /**
     * 商品详情
     * @param Request $request 参数类
     * @param int $id 商品id
     * @return json
     * @author 许立 2018年07月17日 运费修改
     */
    public function detail(Request $request, $id)
    {
        $product = (new ProductModule())->getDetail($id, $request->input('wid'), $request->input('mid'), true);
        // 运费处理
        $product['product']['freight_price'] = $product['product']['freight_string'];
        $product['product']['discount'] = (new DiscountModule())->getDiscountDetailByPid($id, $request->input('wid'));

        xcxsuccess('', ['data' => $product]);
    }

    /**
     * 商品详情页评价列表
     * @update 许立 2018年11月20日 评论返回商家回复
     */
    public function commentList(Request $request, $id)
    {
        if (empty($id)) {
            xcxerror('商品ID不能为空');
        }

        $where = [
            'pid' => intval($id),
        ];

        $input = $request->input();
        //status=1,好评，2：中评，3：差评
        $status = intval($input['status']);
        !empty($status) && $where['status'] = $status;

        list($data) = ProductEvaluateService::init('wid', $input['wid'])->where($where)->getList();

        //商品评价图片
        $productModule = new ProductModule();
        if ($data['data']) {
            $data['data'] = $productModule->getCommentImgArr($data['data']);
        }

        $data['data'] = $productModule->handleCommentReply($data['data']);

        xcxsuccess('', ['data' => $data['data']]);
    }

    /**
     * 商品sku
     */
    public function sku(Request $request, $id)
    {
        if (empty($id)) {
            error('参数不能为空');
        }
        //是否使用会员卡 add by meijie
        $type = $request->input(['isUserCard'], 0);

        $sku = (new ProductModule())->handleSkuDiscountPrice($id, $request->input('mid'), $type);
        xcxsuccess('', ['data' => $sku['data']]);
    }

    /**
     * 商品评价详情
     * @param Request $request 参数类
     * @param FileInfoService $fileInfoService 文件类
     * @param ProductEvaluatePraiseService $productEvaluatePraiseService 点赞类
     * @author 许立 2017年9月12日
     */
    public function commentDetail(Request $request, FileInfoService $fileInfoService, ProductEvaluatePraiseService $productEvaluatePraiseService)
    {
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        $rule = Array(
            'eid' => 'required',
        );
        $message = Array(
            'eid.required' => '评论ID不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }

        // 判断参数 包括undefined
        $input['eid'] = (int)$input['eid'];
        if (empty($input['eid'])) {
            xcxerror('评论ID不存在');
        }

        $evaluateData = ProductEvaluateService::init('wid', $wid)->model->find($input['eid'])->load('member')->load('orderDetail');
        if (empty($evaluateData)) {
            xcxerror('评价不存在');
        }
        $evaluateData = $evaluateData->toArray();

        list($evaluateDetailData) = ProductEvaluateDetailService::init()->where(['eid' => $evaluateData['id']])->order('id asc')->getList();
        //展示页面
        $tmp = [];
        if ($evaluateData['img']) {
            foreach (explode(',', $evaluateData['img']) as $val) {
                $tmp[] = $fileInfoService->getRowById($val);
            }
        }
        $evaluateData['img'] = $tmp;
        //是否已点赞
        list($praise) = $productEvaluatePraiseService->init('mid', $mid)->where(['eid' => $input['eid']])->getList();
        if ($praise['data']) {
            $evaluateData['praise'] = 1;
        } else {
            $evaluateData['praise'] = 0;
        }

        xcxsuccess('', ['comment' => $evaluateData, 'reply' => $evaluateDetailData]);

    }

    /**
     * 商品评价回复列表
     * @param Request $request 参数类
     * @author 许立 2017年9月12日
     */
    public function commentReplies(Request $request)
    {
        $wid = $request->input('wid');
        $eid = $request->input('eid');

        // 判断参数 包括undefined
        $eid = (int)$eid;
        if (empty($eid)) {
            xcxerror('评论ID不存在');
        }

        $evaluateData = ProductEvaluateService::init('wid', $wid)->model->find($eid)->load('member')->load('orderDetail');
        if (empty($evaluateData)) {
            xcxerror('评价不存在');
        }

        list($evaluateDetailData) = ProductEvaluateDetailService::init()->where(['eid' => $eid])->order('id asc')->getList();
        xcxsuccess('', $evaluateDetailData);
    }

    /**
     * 回复商品评价
     * @param Request $request 参数类
     * @author 许立 2017年9月12日
     */
    public function commentReply(Request $request)
    {
        $input = $request->input();

        // 判断参数 包括undefined
        $input['eid'] = (int)$input['eid'];
        if (empty($input['eid'])) {
            xcxerror('评论ID不存在');
        }

        if (empty(trim($input['content']))) {
            xcxerror('内容不能为空');
        }

        $evaluateData = [
            'eid' => $input['eid'],
            'mid' => $input['mid'],
            'reply_id' => isset($input['reply_id']) ? $input['reply_id'] : '',
            'content' => $input['content']
        ];
        $id = ProductEvaluateDetailService::init()->add($evaluateData, false);
        $data = ProductEvaluateDetailService::init()->model->find($id)->load('member')->load('reply');

        xcxsuccess('', $data ? $data->toArray() : []);
    }

    /**
     * 商品评价点赞
     */
    public function commentLike(Request $request, $eid, ProductEvaluatePraiseService $productEvaluatePraiseService)
    {
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        $productEvaluatePraiseData = $productEvaluatePraiseService->init()->model->where(['mid' => $mid, 'eid' => $eid])->get()->toArray();
        if (!empty($productEvaluatePraiseData)) {
            xcxerror('已点赞');
        }
        ProductEvaluateService::init('wid', $wid)->increment($eid, 'agree_num', false);
        $productEvaluatePraiseService->init()->add(['mid' => $mid, 'eid' => $eid], false);

        xcxsuccess('点赞成功');
    }

    /**
     * todo 推荐商品
     * @param Request $request
     * @param ProductModule $productModule
     * @author jonzhang
     * @date 2017-11-22
     */
    public function showRecommendProducts(Request $request, ProductModule $productModule)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $token = $request->input('token');
        //add by jonzhang 2018-05-14
        $productId = $request->input('productId') ?? 0;
        $productId = intval($productId);
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        $wid = CommonModule::getWidByToken($token);
        if (empty($wid)) {
            $returnData['code'] = -101;
            $returnData['hint'] = 'token中的数据有问题';
            return $returnData;
        }
        $result = $productModule->showRecommendProducts($wid, 1, $productId);
        if ($result['errCode'] == 0 && !empty($result['data'])) {
            $returnData['list'] = $result['data'];
        }
        return $returnData;
    }

    /**
     * 获取商品分组详情
     * @param Request $request 请求参数
     * @param $wid 店铺id
     * @param $id 分组id
     * @author 何书哲 2018年9月11日
     * @update 陈文豪 返回参数 修复 代码规范
     */
    public function groupDetail(Request $request, $id)
    {
        empty($id) && xcxerror('商品分组id不能为空');
        $token = $request->input('token');
        empty($token) && xcxerror('没有传递token');
        $wid = CommonModule::getWidByToken($token);
        empty($wid) && xcxerror('token中的数据有问题', -101);
        $detail = (new ProductGroupService())->getGroup(['id' => $id], $wid);
        count($detail['tpl']['data']) && $detail['tpl']['data'][0]['id'] = $id;
        $tpl = MallModule::processTemplateData($wid, json_encode($detail['tpl']['data']));
        $detail['tpl']['data'] = json_decode($tpl, true);
        xcxsuccess('', [
            'id' => $id,
            'wid' => $wid,
            'data' => $detail,
        ]);
    }

    /**
     * 获取商品组商品分页信息
     * @param Request $request
     * @param ProductModule $module
     * @author: 梅杰 2018年10月23日
     */
    public function getProductGroupDetail(Request $request, ProductModule $module)
    {
        if ($groupId = $request->input('group_id', 0)) {

            $wid = $request->input('wid');
            $data = $module->getProductByGroupId($wid, $groupId, 1);
            if ($data['errCode'] == 0) {
                xcxsuccess('操作成功', $data['data']['products'] ?? []);
            }
            xcxerror('操作失败：' . $data['errMsg']);
        }
        xcxerror();
    }


}