<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/6
 * Time: 17:02
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Module\XCXModule;
use App\S\WXXCX\WXXCXConfigService;
use WeixinService;
use App\Services\UserService;
use App\S\WXXCX\WXXCXFooterBarService;
use App\Lib\WXXCX\ThirdPlatform;
use App\S\WXXCX\WXXCXTemplateService;
use App\S\WXXCX\WXXCXConfigRecordService;
use App\Lib\BLogger;
use App\Services\WeixinService as XCXWeixinService;
use App\S\Staff\StaffOperLogService;
use App\S\Weixin\ShopService;
use App\S\WXXCX\XCXStatisticsLogService;
use App\S\Foundation\Bi;


class XCXController extends Controller
{
    /**
     * 小程序列表
     * @param Request $request 请求参数
     * @param ShopService $shopService 店铺service
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @param WXXCXConfigRecordService $WXXCXConfigRecordService 小程序备注service
     * @param UserService $userService 用户service
     * @param XCXModule $XCXModule 小程序module
     * @return view
     * @update 何书哲 2018年8月30日 小程序列表排序
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年11月05日 如果店铺已删除，则对应的小程序也过滤掉
     * @update 何书哲 2019年10月10日 代码注释格式
     * @update 陈文豪 2020年01月06日 处理店铺过期
     * @update 陈文豪 2020年01月07日 过滤店铺过期
     * @update 何书哲 2020年03月14日 获取线上普通版本和直播版本
     */
    public function list(Request $request, ShopService $shopService, WXXCXConfigService $WXXCXConfigService, WXXCXConfigRecordService $WXXCXConfigRecordService, UserService $userService, XCXModule $XCXModule)
    {
        // 过滤
        $input = $request->input();
        $where = ['current_status' => 0];
        // 下架小程序不使用current_status=0 这个状态值 add by jonzhang 2018-03-20
        isset($input['status']) && ($input['status'] == 8) && ($where = array_except($where, 'current_status'));
        isset($input['status']) && ($where['status'] = $input['status']);

        // 搜索字段查询
        if (!empty($input['search_value'])) {
            if ($input['search_type'] == 'mphone') {
                // 手机号码查询
                // 通过手机号码查询出uid
                $userResultData = $userService->getListByCondition(['mphone' => $input['search_value']]);
                if ($userResultData['errCode'] == 0 && !empty($userResultData['data']) && !empty($userResultData['data'][0]['id'])) {
                    // 通过uid查询出wid
                    $storeResultData = $shopService->getListWithoutPage(['uid' => $userResultData['data'][0]['id']]);
                    $where['wid'] = ['in', array_column($storeResultData, 'id')];
                }
            } else if ($input['search_type'] == 'shop_name') {
                // 店铺名称查询
                $storeResultData = $shopService->getListWithoutPage(['shop_name' => $input['search_value']]);
                $where['wid'] = ['in', array_column($storeResultData, 'id')];
            } else {
                $where[$input['search_type']] = ['like', "%" . $input['search_value'] . "%"];
            }
        }
        
        // 操作时间查询
        if (!empty($input['start_at']) && !empty($input['end_at'])) {
            $where['updated_at'] = ['between', [$input['start_at'], $input['end_at']]];
        } elseif (!empty($input['start_at'])) {
            $where['updated_at'] = ['>=', $input['start_at']];
        } elseif (!empty($input['end_at'])) {
            $where['updated_at'] = ['<=', $input['end_at']];
        }

        // 模板
        isset($input['templateId']) && ($where['template_id'] = $input['templateId']);

        /*
        全部：按照进度的操作时间排序
        无操作：按照授权时间排序，最新操作的排在最前面
        审核中：按照提交审核的时间排序，最新操作的排在最前面
        审核被拒：按照审核被拒的时间排序，最新操作的排在最前面
        审核成功：按照腾讯审核通过的时间排序，最新通过的排在最前面
        已发布：按照已发布的时间排序，最新发布的排在最前面
        已提交代码：按照操作时间排序，最新操作的排在最前面
        已作废：最新操作的排在最前面
        已下架：最新操作的排在最前面
         */
        // 何书哲 2018年8月30日 小程序列表排序
        if (!isset($input['status']) || isset($input['status']) && in_array($input['status'], [1, 2, 5])) {
            // 全部、已提交代码、审核中、已发布按照进度时间来排
            $orderBy = 'status_time';
        } elseif ($input['status'] == 0) {
            // 无操作按照授权时间
            $orderBy = 'created_at';
        } elseif (in_array($input['status'], [3, 4])) {
            // 审核成功、审核被拒按审核时间来排
            $orderBy = 'audit_result_time';
        } else {
            // 已作废、已下架按最近更新时间排序
            $orderBy = 'updated_at';
        }

        if (!isset($where['wid']) && !isset($input['expire']) && isset($input['status']) && $input['status'] == 5) {
            $whereExpire['shop_expire_at'] = ['>', date('Y-m-d H:i:s')];
            $storeResultData = $shopService->getListWithoutPage($whereExpire);
            $where['wid'] = ['in', array_column($storeResultData, 'id')];
        }

        // 获取小程序列表
        $list = $WXXCXConfigService->listWithPage($where, $orderBy, 'DESC');

        // 店铺信息查询
        $widIdArr = array_column($list[0]['data'], 'wid');
        $shopData = $shopService->getListById($widIdArr);
        foreach ($shopData as $item) {
            $shopData[$item['id']] = $item;
        }

        // 小程序列表信息
        foreach ($list[0]['data'] as $k => &$v) {
            // 何书哲 2018年11月05日 如果店铺已删除，则对应的小程序也过滤掉
            if (!isset($shopData[$v['wid']])) {
                unset($list[0]['data'][$k]);
                continue;
            }
            $v['authorizer_expire_time'] = date('Y-m-d H:i:s', $v['authorizer_expire_time']);
            $v['status_string'] = $WXXCXConfigService::STATUS_STRING_MAP[$v['status']];
            $v['func_info_name'] = $WXXCXConfigService->processFuncInfo($v['func_info']);
            $v['status_time'] = date("Y-m-d H:i:s", $v['status_time']);
            !empty($v['reason']) && ($v['reason'] = htmlspecialchars_decode($v['reason']));
            // add by jonzhang 2018-04-24 店铺下的小程序有备注时，查看详情变色
            $v['isChangeColor'] = 0;
            $xcxConfigRecordData = $WXXCXConfigRecordService->getListByCondition(['app_id' => $v['app_id'], 'wid' => $v['wid']], 'create_time', 'desc', 15);
            $xcxConfigRecordData['errCode'] == 0 && !empty($xcxConfigRecordData['data']) && ($v['isChangeColor'] = 1);
            // add by wuxiaoping 添加店铺名称，手机号码列表显示
            $v['shop_name'] = '';
            $v['mobile'] = '';
            $v['shop_expire_at'] = 0;
            $weixinData = $shopData[$v['wid']];
            if ($weixinData) {
                $v['shop_name'] = $weixinData['shop_name'];
                $v['is_fee'] = $weixinData['is_fee'] ?? 0;
                $userData = $userService->init()->where(['id' => $weixinData['uid']])->getInfo($weixinData['uid']);
                $userData && ($v['mobile'] = $userData['mphone']);
                if (strtotime($weixinData['shop_expire_at']) > time()) {
                    $v['shop_expire_at'] = $weixinData['shop_expire_at'];
                }
            }
        }

        // add 何书哲 2020年03月14日 获取线上普通版本和直播版本
        $xcxOnlineData = $XCXModule->getXcxOnlineVersion();

        return view('staff.xcx.list', array(
            'title' => '小程序',
            'sliderbar' => 'slidebar',
            'data' => $list,
            'xcxOnline' => $xcxOnlineData['common'],
            'xcxOnlineLive' => $xcxOnlineData['live']
        ));
    }

    /**
     * 修改服务器地址
     *
     * 授权给第三方的小程序，其服务器域名只可以为第三方的服务器，当小程序通过第三方发布代码上线后，小程序原先自己配置的服务器域名将被删除，只保留第三方平台的域名，所以第三方平台在代替小程序发布代码之前，需要调用接口为小程序添加第三方自身的域名。
     *
     * 需要先将域名登记到第三方平台的小程序服务器域名中，才可以调用接口进行配置。
     *
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * action           add添加, delete删除, set覆盖, get获取。当参数是get时不需要填四个域名字段。
     * requestdomain    request合法域名，当action参数是get时不需要此字段。
     * wsrequestdomain  socket合法域名，当action参数是get时不需要此字段。
     * uploaddomain     uploadFile合法域名，当action参数是get时不需要此字段。
     * downloaddomain   downloadFile合法域名，当action参数是get时不需要此字段。
     *
     * @return json
     *
     * @author 许立[843168640@qq.com]
     * @since 2017-09-14 14:14:00
     * @update 张永辉 2018年7月9日 根绝配置id查询数据
     */
    public function modifyDomain(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $id = intval($input['id']);
            $row = $WXXCXConfigService->getRowById($id);
            $row['errCode'] != 0 && error('小程序不存在');

            $domain = trim($input['domain']);
            empty($domain) && error('请填写域名');

            $action = trim($input['action']);
            empty($action) && error('请选择动作');

            // 暂时只做设置域名
            $action != 'set' && error('暂时只开发设置域名功能');

            //add by jonzhang
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $result = $XCXModule->modifyDomain($id, $action, $domain, $operatorId, $operator);
            //add by jonzhang 2018-04-18
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                success('设置域名成功');
            } else {
                if (isset($result['errmsg'])) {
                    error($result['errmsg']);
                }
                error('接口返回数据有问题');
            }
        }
        error('只允许POST请求');
    }

    /**
     * 上传代码
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * template_id      代码库中的代码模版ID
     * ext_json         第三方自定义的配置
     * user_version     代码版本号，开发者可自定义
     * user_desc        代码描述，开发者可自定义
     *
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @param WXXCXFooterBarService $WXXCXFooterBarService 小程序底部导航service
     * @param mixed
     *
     * @update 陈文豪 20180710 改以id提交
     */
    public function commit(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService, WXXCXFooterBarService $WXXCXFooterBarService)
    {
        if ($request->isMethod('post')) {
            // 参数
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            if ($row['errCode'] != 0) {
                error('小程序不存在');
            }

            $template_id = $input['template_id'] ?? 0;
            $liveStatus = $input['live_status'] ?? 0;
            // update 何书哲 2020年03月11日 添加live_status参数，标识直播送审/普通送审
            if (!in_array($liveStatus, [0, 1])) {
                error('请选择直播送审还是普通送审');
            }
            if (empty($version = trim($input['user_version'] ?? ''))) {
                error('请填写代码版本号');
            }
            if (empty($desc = trim($input['user_desc'] ?? ''))) {
                error('请填写代码描述');
            }

            // add by wuxiaoping 2018.01.08 总后台上传代码读取底部导航栏数据
            $syncFooterList = $WXXCXFooterBarService->getAllList($row['data']['wid'], [], 'order');
            $data = $barList = [];
            if (isset($syncFooterList[0]['data']) && $syncFooterList[0]['data']) {
                foreach ($syncFooterList[0]['data'] as $key => $value) {
                    $iconPathArr = explode('mctsource/', $value['icon_path']);
                    $selectedPathArr = explode('mctsource/', $value['selected_path']);
                    $data[$key]['text'] = $value['name'];
                    $data[$key]['pagePath'] = $value['page_path'];
                    $data[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
                    $data[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
                }
                $barList['selectedColor'] = '#b1292d';
                $barList['list'] = $data;
                $barList['backgroundColor'] = '#fff';
                $barList['borderStyle'] = 'black';
            }

            // add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $XCXModule->commit($xcxid, $template_id, $version, $desc, $barList, false, [], $operatorId, $operator, $liveStatus);
        }

        error('只允许POST请求');
    }

    /**
     * 一键获取域名
     * @param Request $request 请求参数
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @return mixed
     */
    public function getAllDomains(Request $request, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('get')) {
            $list = $WXXCXConfigService->getAllDomains();
            $domains = implode(',', array_column($list, 'request_domain'));
            success('', '', ['data' => $domains]);
        }

        error('只允许GET请求');
    }

    /**
     * 获取类目
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @return mixed
     * @update 张永辉 2018年7月10 根据小程序配置id获取小程序配置信息
     */
    public function getCategory(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('get')) {
            $input = $request->input();

            $id = intval($input['id']);
            $row = $WXXCXConfigService->getRowById($id);
            $row['errCode'] != 0 && error('小程序不存在');

            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';
            $XCXModule->getCategory($row, false, $operatorId, $operator);
        }

        error('只允许GET请求');
    }

    /**
     * 获取页面
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @return mixed
     * @update 陈文豪 20180710 改以id提交
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function getPage(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('get')) {
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $XCXModule->getPage($xcxid, $operatorId, $operator);
        }

        error('只允许GET请求');
    }

    /**
     * 提交审核
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * item_list        提交审核项的一个列表（至少填写1项，至多填写5项）
     * address          小程序的页面，可通过“获取小程序的第三方提交代码的页面配置”接口获得
     * tag              小程序的标签，多个标签用空格分隔，标签不能多于10个，标签长度不超过20
     * first_class      一级类目名称，可通过“获取授权小程序帐号的可选类目”接口获得
     * second_class     二级类目(同上)
     * third_class      三级类目(同上)
     * first_id         一级类目的ID，可通过“获取授权小程序帐号的可选类目”接口获得
     * second_id        二级类目的ID(同上)
     * third_id         三级类目的ID(同上)
     * title            小程序页面的标题,标题长度不超过32
     *
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序service
     * @return mixed
     *
     * @update 陈文豪 20180710 改以id提交
     */
    public function submitAudit(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            $list = $input['item_list'] ?? [];
            empty($list) && error('参数不完整');

            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $XCXModule->submitAudit($xcxid, $list, $operatorId, $operator);
        }

        error('只允许POST请求');
    }

    /**
     * 发布
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序配置service
     * @return mixed
     * @update 陈文豪 20180710 改以id提交
     */
    public function release(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $XCXModule->release($xcxid, $operatorId, $operator);
        }

        error('只允许POST请求');
    }

    /**
     * 绑定微信用户为小程序体验者
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * wechatid         微信号
     *
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序配置service
     * @return mixed
     *
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息设置域名
     */
    public function bindTester(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $id = intval($input['id']);
            $row = $WXXCXConfigService->getRowById($id);
            $row['errCode'] != 0 && error('小程序不存在');

            $wechatID = trim($input['wechatid']);
            empty($wechatID) && error('请填写微信号');

            $XCXModule->bindTester($row, $wechatID);
        }

        error('只允许POST请求');
    }

    /**
     * 解除绑定小程序的体验者
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * wechatid         微信号
     *
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序配置service
     * @return mixed
     *
     * @update 陈文豪 20180710 改以id提交
     */
    public function unbindTester(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            $wechatID = trim($input['wechatid']);
            empty($wechatID) && error('请填写微信号');

            $XCXModule->unbindTester($xcxid, $wechatID);
        }

        error('只允许POST请求');
    }

    /**
     * 获取体验小程序的体验二维码
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序配置service
     * @return mixed
     * @update 陈文豪 20180710 改以id提交
     */
    public function getQrCode(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('get')) {
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            $XCXModule->getQrCode($xcxid);
        }

        error('只允许GET请求');
    }

    /**
     * 获取帐号下已存在的模板列表
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * offset           offset和count用于分页，表示从offset开始，拉取count条记录，offset从0开始，count最大为20。最后一页的list长度可能小于请求的count
     * count
     *
     * @param Request $request 请求参数
     * @param XCXModule $XCXModule 小程序module
     * @param WXXCXConfigService $WXXCXConfigService 小程序配置service
     * @return mixed
     *
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息设置域名
     */
    public function getTemplates(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $id = intval($input['id']);
            $row = $WXXCXConfigService->getRowById($id);
            $row['errCode'] != 0 && error('小程序不存在');

            $XCXModule->getTemplates($row);
        }

        error('只允许POST请求');
    }

    /**
     * 组合模板并添加至帐号下的个人模板库
     * 参数             说明
     * access_token     请使用第三方平台获取到的该小程序授权的authorizer_access_token
     * id         模板标题id
     * keyword_id_list  开发者自行组合好的模板关键词列表，关键词顺序可以自由搭配（例如[3,5,4]或[4,5,3]），最多支持10个关键词组合
     * @update 陈文豪 20180710 改以id提交
     */
    public function addTemplates(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        if ($request->isMethod('post')) {
            //参数
            $input = $request->input();

            $xcxid = intval($input['xcxid']);
            $row = $WXXCXConfigService->getRowById($xcxid);
            $row['errCode'] != 0 && error('小程序不存在');

            $XCXModule->addTemplates($xcxid);
        }

        error('只允许POST请求');
    }

    /**
     * todo 获取小程序的审核状态
     * @param Request $request
     * @param XCXModule $XCXModule
     * @return mixed
     * @author jonzhang
     */
    public function getAuditStatusByDeveloper(Request $request, XCXModule $XCXModule)
    {
        $data = [];
        $id = $request->input('id') ?? 0;
        $wid = $request->input('wid') ?? 0;

        !empty($id) && $data['id'] = $id;
        !empty($wid) && $data['wid'] = $wid;

        return $XCXModule->getAuditStatus($data);
    }

    /***
     * todo  小程序模板库列表数据
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2018-01-27
     */
    public function templateList(Request $request)
    {
        return view('staff.xcx.templateList', [
            'title' => '小程序',
            'sliderbar' => 'slidebar'
        ]);
    }

    /***
     * todo 获取第三方平台中的草稿箱 后台开发使用
     * @param ThirdPlatform $thirdPlatform
     * @return mixed
     * @author jonzhang
     * @date 2018-01-19
     */
    public function getTemplateDraftList(ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => 0];
        try {
            $result = $thirdPlatform->getTemplateDraftList();
            if ($result['errCode'] == 0) {
                $returnData['data'] = $result['data'];
                $returnData['errMsg'] = '同步了' . $result['data'] . '条数据';
            } else {
                $returnData = $result;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
        }
        return $returnData;
    }

    /***
     * todo  获取第三方平台中的小程序模板库 后台开发使用
     * @param ThirdPlatform $thirdPlatform
     * @return mixed
     * @author jonzhang
     * @date 2018-01-19
     */
    public function getTemplateList(ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => 0];
        try {
            $result = $thirdPlatform->getTemplateList();
            if ($result['errCode'] == 0) {
                $returnData['data'] = $result['data'];
                $returnData['errMsg'] = '同步了' . $result['data'] . '条数据';
            } else {
                $returnData = $result;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
        }
        return $returnData;
    }

    /***
     * todo 把草稿箱小程序添加模板库
     * @param Request $request 请求参数
     * @param WXXCXTemplateService $wxxcxTemplateService 小程序模板库service
     * @param ThirdPlatform $thirdPlatform 第三方平台
     * @return array
     * @author jonzhang
     * @date 2018-01-19
     */
    public function insertTemplate(Request $request, WXXCXTemplateService $wxxcxTemplateService, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $id = $request->input('id') ?? 0;
            $id = intval($id);
            if (empty($id)) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            $wxxcxTemplateData = $wxxcxTemplateService->getListByCondition(['id' => $id, 'current_status' => 0, 'type' => 1]);
            if ($wxxcxTemplateData['errCode'] == 0 && !empty($wxxcxTemplateData['data'])) {
                $draftId = $wxxcxTemplateData['data'][0]['draft_id'];
                return $thirdPlatform->insertTemplate($draftId);
            } else if ($wxxcxTemplateData['errCode'] == 0 && empty($wxxcxTemplateData['data'])) {
                $returnData['errCode'] = -1002;
                $returnData['errMsg'] = 'id不存在';
                return $returnData;
            } else {
                return $wxxcxTemplateData;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 小程序模板库
     * @param Request $request 请求参数
     * @param WXXCXTemplateService $wxxcxTemplateService 小程序模板库service
     * @return array
     * @author jonzhang
     * @date 2018-01-19
     */
    public function showTemplateList(Request $request, WXXCXTemplateService $wxxcxTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        try {
            $type = $request->input('type') ?? 0;
            $type = intval($type);
            if (empty($type)) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = 'type为空';
                return $returnData;
            }
            //1表示 草稿
            //2表示模板
            $where['type'] = $type;
            $where['current_status'] = 0;
            $xcxTemplateData = $wxxcxTemplateService->getListByCondition($where, 'create_time', 'desc', 15);
            if ($xcxTemplateData['errCode'] == 0 && !empty($xcxTemplateData['data'])) {
                foreach ($xcxTemplateData['data'] as &$item) {
                    $item['create_time'] = date('Y/m/d H:i:s', $item['create_time']);
                }
                $returnData['data'] = $xcxTemplateData['data'];
                $returnData['total'] = $xcxTemplateData['total'];
                $returnData['currentPage'] = $xcxTemplateData['currentPage'];
                $returnData['pageSize'] = $xcxTemplateData['pageSize'];
                return $returnData;
            } else {
                return $xcxTemplateData;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 把某个小程序页面设置为小程序微页面店铺主页
     * @param Request $request 请求参数
     * @param WXXCXTemplateService $wxxcxTemplateService 小程序模板库service
     * @return array
     * @date 2018-01-26
     * @update 何书哲 2020年03月14日 添加type参数，区分普通版本和直播版本
     */
    public function updateXCXOnLine(Request $request, WXXCXTemplateService $wxxcxTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $id = $request->input('id') ?? 0;
            $id = intval($id);
            if (empty($id)) {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            $type = $request->input('type', 1);
            if (!in_array($type, [1, 2])) {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = 'type错误';
                return $returnData;
            }
            // 查询出符合要求的数据
            $result = $wxxcxTemplateService->getListByCondition(['current_status' => 0, 'is_online' => $type, 'type' => 2]);
            if (!empty($result['data'])) {
                //把原来的版本更改为下线状态
                foreach ($result['data'] as $item) {
                    $wxxcxTemplateService->updateData($item['id'], ['is_online' => 0]);
                }
            }
            return $wxxcxTemplateService->updateData($id, ['is_online' => $type]);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 正在审核的小程序 撤销审核
     * @param Request $request
     * @param XCXModule $xcxModule
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-01-30
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息设置域名
     */
    public function cancelAudit(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $id = $request->input('id') ?? 0;
            if (empty($id)) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = 'id不能为空';
                return $returnData;
            }

            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            return $xcxModule->cancelAudit($id, $operatorId, $operator);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 删除模板数据
     * @param Request $request
     * @param WXXCXTemplateService $wxxcxTemplateService
     * @return array
     * @author jonzhang
     * @date 2018-01-31
     */
    public function deleteTemplate(Request $request, WXXCXTemplateService $wxxcxTemplateService, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $id = $request->input('id') ?? 0;
            $id = intval($id);
            $isSync = $request->input('isSync') ?? 0;
            $isSync = intval($isSync);
            if (empty($id)) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            if ($isSync) {
                //调用微信接口删除模板库数据
                $wxxcxTemplateData = $wxxcxTemplateService->getListByCondition(['id' => $id, 'current_status' => 0]);
                if ($wxxcxTemplateData['errCode'] == 0 && !empty($wxxcxTemplateData['data'])) {
                    $isOnline = $wxxcxTemplateData['data'][0]['is_online'];
                    if ($isOnline) {
                        $returnData['errCode'] = -1003;
                        $returnData['errMsg'] = '当前版本库不能够删除';
                        return $returnData;
                    }
                    $templateId = $wxxcxTemplateData['data'][0]['template_id'];
                    $thirdPlatformData = $thirdPlatform->deleteTemplate($templateId);
                    if ($thirdPlatformData['errCode'] == 0) {
                        return $wxxcxTemplateService->updateData($id, ['current_status' => -1]);
                    } else {
                        return $thirdPlatformData;
                    }
                } else if ($wxxcxTemplateData['errCode'] == 0 && empty($wxxcxTemplateData['data'])) {
                    $returnData['errCode'] = -1002;
                    $returnData['errMsg'] = 'id不存在';
                    return $returnData;
                } else {
                    return $wxxcxTemplateData;
                }
            } else {
                return $wxxcxTemplateService->updateData($id, ['current_status' => -1]);
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 添加小程序备注
     * @param Request $request
     * @param WXXCXConfigRecordService $wxxcxConfigRecordService
     * @return array
     * @author jonzhang
     * @date 2018-03-08
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function insertXCXRecord(Request $request, WXXCXConfigRecordService $wxxcxConfigRecordService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $staffOperService = new StaffOperLogService();
        try {
            $xcxid = $request->input('xcxid') ?? 0;
            $appId = $request->input('appId') ?? '';
            $appName = $request->input('appName') ?? '';
            $content = $request->input('content') ?? '';
            $wid = $request->input('wid') ?? 0;
            $wid = intval($wid);
            $userData = $request->session()->get('userData');

            $errMsg = '';
            empty($appId) && $errMsg .= 'appId为空';
            empty($appName) && $errMsg .= 'appName为空';
            empty($content) && $errMsg .= 'content为空';
            empty($wid) && $errMsg .= '店铺id为空';
            (empty($userData['id']) || empty($userData['login_name'])) && $errMsg .= '登录超时';

            if (strlen($errMsg) > 0) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = $errMsg;
                //何书哲 2018年8月30日 添加后台操作日志
                $staffOperService->write('操作失败: ' . $returnData['$errMsg'], 15, $xcxid);
                return $returnData;
            }
            if (mb_strlen($content, 'UTF-8') > 100) {
                $returnData['errCode'] = -1002;
                $returnData['errMsg'] = '字符太长';
                $staffOperService->write('操作失败: ' . $returnData['$errMsg'], 15, $xcxid);
                return $returnData;
            }
            $data = ['wid' => $wid, 'operate_id' => $userData['id'], 'operator' => $userData['login_name'], 'content' => $content, 'app_id' => $appId, 'app_name' => $appName];
            $staffOperService->write('操作成功: ' . $content, 15, $xcxid);
            return $wxxcxConfigRecordService->insertData($data);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 显示小程序备注信息
     * @param Request $request
     * @param WXXCXConfigRecordService $wxxcxConfigRecordService
     * @return array
     * @author jonzhang
     * @date 2018-03-08
     */
    public function showXCXRecordList(Request $request, WXXCXConfigRecordService $wxxcxConfigRecordService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $appId = $request->input('appId') ?? '';
            $wid = $request->input('wid') ?? 0;
            $wid = intval($wid);

            $errMsg = '';
            empty($appId) && $errMsg .= 'appId为空';
            empty($wid) && $errMsg .= '店铺id为空';

            if (strlen($errMsg) > 0) {
                $returnData['errCode'] = -1001;
                $returnData['errMsg'] = $errMsg;
                return $returnData;
            }
            $xcxConfigRecordData = $wxxcxConfigRecordService->getListByCondition(['app_id' => $appId, 'wid' => $wid], 'create_time', 'desc', 15);
            if ($xcxConfigRecordData['errCode'] == 0 && !empty($xcxConfigRecordData['data'])) {
                foreach ($xcxConfigRecordData['data'] as &$item) {
                    $item['create_time'] = date('Y/m/d H:i:s', $item['create_time']);
                }
                $returnData['data'] = $xcxConfigRecordData['data'];
                $returnData['total'] = $xcxConfigRecordData['total'];
                $returnData['currentPage'] = $xcxConfigRecordData['currentPage'];
                $returnData['pageSize'] = $xcxConfigRecordData['pageSize'];
                return $returnData;
            } else {
                return $xcxConfigRecordData;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 获取小程序二维码
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-03-09
     * @update 陈文豪 20180710 改以id提交
     * @update 陈文豪 20180719 改以id提交
     */
    public function getXCXQRCode(Request $request, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $xcxid = $request->input('xcxid') ?? 0;
            $xcxid = intval($xcxid);
            $wid = $request->input('wid') ?? 0;
            $width = $request->input('width') ?? 430;
            $width = intval($width) == 0 ? 430 : $width;
            $path = $request->input('path') ?? 'pages/index/index';
            if (empty($xcxid)) {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            return $thirdPlatform->getXCXQRCode($wid, $width, $path, $xcxid);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }


    /**
     * todo 获取小程序码
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array
     * @author jonzhang
     * @date 2018-03-09
     * @update 陈文豪 20180710 改以id提交
     */
    public function getXCXCode(Request $request, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        try {
            $xcxid = $request->input('xcxid') ?? 0;
            $xcxid = intval($xcxid);
            $width = $request->input('width') ?? 430;
            $width = intval($width) == 0 ? 430 : $width;
            $page = $request->input('page') ?? 'pages/index/index';
            if (empty($xcxid)) {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            return $thirdPlatform->getXCXCode($xcxid, $width, $page);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 作废小程序
     * @param Request $request
     * @param WXXCXConfigRecordService $wxxcxConfigRecordService
     * @return array
     * @author jonzhang
     * @date 2018-03-09
     * @update 陈文豪 20180710 改以id提交
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function cancelXCX(Request $request, WXXCXConfigService $wxxcxConfigService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $staffOperService = new StaffOperLogService();
        try {
            $xcxid = $request->input('xcxid') ?? 0;
            $xcxid = intval($xcxid);

            $row = $wxxcxConfigService->getRowById($xcxid);
            if ($row['errCode'] == 0 && !empty($row['data'])) {
                if ($row['data']['status'] == 2) {
                    $returnData['errCode'] = -1002;
                    $returnData['errMsg'] = '审核中的小程序不能够作废';
                    $staffOperService->write('操作失败：审核中的小程序不能够作废', 18, $xcxid);
                    return $returnData;
                } else if ($row['data']['status'] == 8) {
                    $returnData['errCode'] = -1005;
                    $returnData['errMsg'] = '该小程序已经下架';
                    $staffOperService->write('操作失败：该小程序已经下架', 18, $xcxid);
                    return $returnData;
                }
                $id = $row['data']['id'] ?? 0;
            } else {
                if ($row['errCode'] == 0) {
                    $staffOperService->write('操作失败：没有要处理的数据', 18, $xcxid);
                    $returnData['errCode'] = -1003;
                    $returnData['errMsg'] = '没有要处理的数据';
                    return $returnData;
                } else {
                    return $row;
                }
            }
            if (empty($id)) {
                $returnData['errCode'] = -1004;
                $returnData['errMsg'] = 'id为空';
                $staffOperService->write('操作失败：没有要处理的数据', 18, $xcxid);
                return $returnData;
            }
            $staffOperService->write('操作成功', 18, $xcxid);
            return $wxxcxConfigService->updateData($id, ['status' => 7]);
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }


    /**
     * todo 下架小程序
     * @param Request $request
     * @param WXXCXConfigRecordService $wxxcxConfigRecordService
     * @return array
     * @author jonzhang
     * @date 2018-03-12
     * @update 陈文豪 20180710 改以id提交
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function pullOffXCX(Request $request, WXXCXConfigService $wxxcxConfigService, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $staffOperService = new StaffOperLogService();
        try {
            $xcxid = $request->input('xcxid') ?? 0;
            $xcxid = intval($xcxid);

            $row = $wxxcxConfigService->getRowById($xcxid);
            if ($row['errCode'] == 0 && !empty($row['data'])) {
                if ($row['data']['status'] != 7) {
                    $returnData['errCode'] = -1002;
                    $returnData['errMsg'] = '只有作废的小程序才可以下架';
                    $staffOperService->write('操作失败：只有作废的小程序才可以下架', 19, $xcxid);
                    return $returnData;
                }
                $id = $row['data']['id'] ?? 0;
            } else {
                if ($row['errCode'] == 0) {
                    $staffOperService->write('操作失败：没有要处理的数据', 19, $xcxid);
                    $returnData['errCode'] = -1003;
                    $returnData['errMsg'] = '没有要处理的数据';
                    return $returnData;
                } else {
                    return $row;
                }
            }
            if (empty($id)) {
                $staffOperService->write('操作失败：没有要处理的数据', 19, $xcxid);
                $returnData['errCode'] = -1004;
                $returnData['errMsg'] = 'id为空';
                return $returnData;
            }
            //add by jonzhang 2018-05-22
            $operatorId = session('userData')['id'] ?? 0;
            $operator = session('userData')['login_name'] ?? '';

            $searchData = $thirdPlatform->changeVisitStatus(['id' => $id, 'wid' => $row['data']['wid'], 'appId' => $row['data']['app_id'] ?? ''], "close", $operatorId, $operator);
            if ($searchData['errCode'] == 0) {
                return $wxxcxConfigService->updateData($id, ['status' => 8, 'current_status' => -1]);
            } else {
                return $searchData;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -10000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 更改小程序是否可以搜索到
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @author jonzhang
     * @date 2018-03-20
     */
    public function changeVisitStatus(Request $request, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $wid = $request->input('wid') ?? 0;
        $wid = intval($wid);
        $id = $request->input('id') ?? 0;
        $id = intval($id);
        $status = $request->input('status') ?? 'open';

        !empty($wid) && $data['wid'] = $wid;
        !empty($id) && $data['id'] = $id;

        if (empty($data)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '参数为空';
            return $returnData;
        }

        return $thirdPlatform->changeVisitStatus($data, $status);
    }

    /**
     * todo 小程序版本回退
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-03-30
     */
    public function revertCodeRelease(Request $request, ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $wid = $request->input('wid') ?? 0;
        $wid = intval($wid);
        $id = $request->input('id') ?? 0;
        $id = intval($id);

        !empty($wid) && $data['wid'] = $wid;
        !empty($id) && $data['id'] = $id;

        if (empty($data)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '参数为空';
            return $returnData;
        }
        return $thirdPlatform->revertCodeRelease($data);
    }

    /***
     * todo 设置小程序业务域名
     * @param Request $request
     * @param XCXModule $XCXModule
     * @return array
     * @author jonzhang
     * @date 2018-04-27
     * @update 陈文豪 20180710 改以id提交
     *
     */
    public function setWebviewDomain(Request $request, XCXModule $XCXModule)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $staffOperService = new StaffOperLogService();
        $xcxid = $request->input('xcxid') ?? 0;
        $xcxid = intval($xcxid);
        $action = $request->input('action');
        $domain = $request->input('domain');

        $errMsg = '';
        empty($xcxid) && $errMsg .= 'id为空';
        empty($action) && $action = 'set';
        empty($domain) && $errMsg .= '业务域名为空';
        mb_strlen($domain) > 200 && $errMsg .= '业务域名太长';

        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -10001;
            $returnData['errMsg'] = $errMsg;
            $staffOperService->write('操作失败：' . $returnData['errMsg'], 16, $xcxid);
            return $returnData;
        }

        //add by jonzhang 2018-05-22
        $operatorId = session('userData')['id'] ?? 0;
        $operator = session('userData')['login_name'] ?? '';

        return $XCXModule->setWebviewDomain($xcxid, $action, $domain, $operatorId, $operator);
    }

    /**
     * todo 批量设置业务域名
     * @param Request $request
     * @param XCXModule $XCXModule
     * @return array
     * @author jonzhang
     * @date 2018-05-02
     * @update 陈文豪 20180710 改以id提交
     */
    public function setWebviewDomainForBatch(Request $request, XCXModule $XCXModule, WXXCXConfigService $WXXCXConfigService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => ['cnt' => 0, 'successCnt' => 0]];
        $domain = $request->input('domain') ?? 'www.huisou.cn,hsim.huisou.cn';
        $isAll = $request->input('isAll') ?? 'false';
        $cnt = 0;
        $successCnt = 0;

        $configData = $WXXCXConfigService->getListByCondition(['current_status' => 0]);
        if ($configData['errCode'] == 0) {
            foreach ($configData['data'] as $item) {
                if (empty($item['webview_domain']) || $isAll) {
                    $webViewData = $XCXModule->setWebviewDomain($item['id'], 'set', $domain);
                    if ($webViewData['errCode'] == 0) {
                        $successCnt++;
                    }
                    $cnt++;
                }
            }
        }
        $returnData['data']['cnt'] = $cnt;
        $returnData['data']['successCnt'] = $successCnt;
        return $returnData;
    }

    /***
     * todo
     * @param Request $request
     * @param WXXCXConfigService $wxxcxConfigService
     * @return array
     * @author jonzhang
     * @date 2018-05-23
     */
    public function updateDataForBatch(Request $request, WXXCXConfigService $wxxcxConfigService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $ids = $request->input('ids');
        $isAuto = $request->input('isAuto') ?? 0;
        $isAuto = intval($isAuto);
        if (empty($ids) || $ids == "[]") {
            $returnData['errCode'] = -10001;
            $returnData['errMsg'] = "ids为空";
            return $returnData;
        }
        $ids = json_decode($ids, true);
        if (empty($ids)) {
            $returnData['errCode'] = -10002;
            $returnData['errMsg'] = "ids不符合要求";
            return $returnData;
        }
        $i = 0;
        foreach ($ids as $id) {
            $result = $wxxcxConfigService->updateData($id, ['is_auth_submit' => $isAuto]);
            if ($result['errCode'] == 0) {
                $i++;
            }
        }
        $returnData['errMsg'] = "更新了" . $i . "条数据";
        return $returnData;
    }

    /***
     * todo 批量上传代码 [开发使用]
     * @param Request $request
     * @param XCXModule $xcxModule
     * @author jonzhang
     * @date 2018-06-08
     */
    public function batchCommit(Request $request, XCXModule $xcxModule)
    {
        $num = $request->input('num') ?? 50;
        $num = intval($num);
        if (empty($num)) {
            $num = 50;
        }
        return $xcxModule->batchCommit($num);
    }

    /***
     * todo 批量提交审核 [开发使用]
     * @param Request $request
     * @param XCXModule $xcxModule
     * @author jonzhang
     * @date 2018-06-08
     */
    public function batchsubmitAudit(Request $request, XCXModule $xcxModule)
    {
        $num = $request->input('num') ?? 20;
        $num = intval($num);
        if (empty($num)) {
            $num = 20;
        }
        return $xcxModule->batchsubmitAudit($num);
    }

    /**
     * 查看日志
     * @param Request $request 请求参数
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @update 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 11:53:48 添加类型23（加急审核申请）日志
     */
    public function seeStaffOperLog(Request $request)
    {
        $xcxid = $request->input('xcxid') ?? 0;
        $xcxid = intval($xcxid);

        if (empty($xcxid)) {
            error('小程序配置id不能为空');
        }

        $where['xcx_config_id'] = $xcxid;
        $where['type'] = ['in', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27]];
        $logData = (new StaffOperLogService())->getListWithPage($where, 'created_at', 'desc', 50);

        return view('staff.xcx.staffLog', array(
            'title' => '小程序',
            'sliderbar' => 'slidebar',
            'data' => $logData[0]['data'],
            'pageHtml' => $logData[1]
        ));
    }

    /**
     * 更新小程序审核状态
     * @param Request $request
     */
    public function updateXcxAuditStatus(Request $request)
    {
        if (!$request->isMethod('GET')) {
            error('只允许get请求');
        }

        $input = $request->input();
        $xcxid = intval($input['xcxid']);

        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');
        $data['id'] = $xcxid;

        $res = (new XCXModule())->getLatestAuditStatusByXcxId($data);
//        if ($res['errCode'] == 0) {
//            success();
//        } else {
//            error($res['errMsg']);
//        }
    }

    /**
     * 总后台获取小程序统计失败列表
     * @author 吴晓平 <2018.11.19>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function updateErrorStatistics(Request $request)
    {
        $wid = $request->input('wid') ?? 0;
        $date = $request->input('date') ?? '';
        $whereData = [];
        $xcxStatisticsLogService = new XCXStatisticsLogService();
        if ($wid) {
            $whereData['wid'] = $wid;
        }
        if ($date) {
            $whereData['start_date'] = $date;
        }
        $list = $xcxStatisticsLogService->getAllErrorList($whereData);
        return view('staff.xcx.errorList', [
            'title' => '小程序',
            'sliderbar' => 'slidebar',
            'list' => $list
        ]);
    }

    /**
     * 处理更新统计
     * @author 吴晓平 <2018.11.19>
     * @return [type] [description]
     */
    public function doUpdateErrorStatistic(Request $request, XCXStatisticsLogService $xcxStatisticsLogService)
    {
        $date = $request->input('date') ?? '';
        if (empty($date)) {
            error('请先选择要更新的统计失败日期');
        }
        $where['status'] = 0;
        $where['start_date'] = $date;
        $rs = $xcxStatisticsLogService->isHaveData($where);
        if (empty($rs)) {
            error('该日期数据不存在或已被处理');
        }
        $result = (new Bi())->updateXcxStatistics($date);
        return $result;

    }

    /**
     * 查询服务商的当月提审限额（quota）和加急次数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月09日 20:57:58
     */
    public function queryQuota(XCXModule $xcxModule)
    {
        $xcxModule->queryQuota();
    }

    /**
     * 加急审核申请
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 11:16:37
     */
    public function speedUpAudit(Request $request, XCXModule $xcxModule)
    {
        $id = intval($request->input('id', 0));

        if (empty($id)) {
            error('参数错误');
        }

        $xcxModule->speedUpAudit($id);
    }

    /**
     * 已添加插件列表
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:33:29
     */
    public function pluginList(Request $request, XCXModule $xcxModule)
    {
        $id = intval($request->input('id', 0));
        if (empty($id)) {
            error('参数错误');
        }
        success('获取成功', '', $xcxModule->pluginList($id));
    }

    /**
     * 申请添加插件
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:36:04
     */
    public function pluginApply(Request $request, XCXModule $xcxModule)
    {
        $id = intval($request->input('id', 0));
        if (empty($id)) {
            error('参数错误');
        }
        $xcxModule->pluginApply($id);
        success('申请添加成功');
    }

    /**
     * 更新插件版本
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:47:13
     */
    public function pluginUpdate(Request $request, XCXModule $xcxModule)
    {
        $id = intval($request->input('id', 0));
        $userVersion = trim($request->input('user_version') ?? '');
        $pluginAppId = trim($request->input('plugin_appid') ?? '');
        if (empty($id) || empty($userVersion) || empty($pluginAppId)) {
            error('参数错误');
        }
        $xcxModule->pluginUpdate($id, $pluginAppId, $userVersion);
        success('更新插件版本成功');
    }

    /**
     * 删除已添加插件
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序module
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:52:19
     */
    public function pluginUnbind(Request $request, XCXModule $xcxModule)
    {
        $id = intval($request->input('id', 0));
        $pluginAppId = trim($request->input('plugin_appid') ?? '');
        if (empty($id)|| empty($pluginAppId)) {
            error('参数错误');
        }
        $xcxModule->pluginUnbind($id, $pluginAppId);
        success('删除插件成功');
    }

}