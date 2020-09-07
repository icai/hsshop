<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/6
 * Time: 11:25
 */

namespace App\Module;

use App\Model\WXXCXConfigOperateLog;
use App\Model\WXXCXTemplate;
use App\S\Staff\StaffOperLogService;
use App\S\WXXCX\SubscribeMessagePushService;
use App\S\WXXCX\WXXCXConfigService;
use App\Services\Marketing\Exception;
use CurlBuilder;
use WXXCXMicroPageService;
use MallModule;
use App\Lib\WXXCX\ThirdPlatform;
use Log;
use DB;
use App\S\WXXCX\WXXCXTemplateService;
use App\S\WXXCX\WXXCXSyncFooterBarService;
use App\Lib\BLogger;
use App\S\WXXCX\WXXCXFooterBarService;
use App\Model\WXXCXConfig;

class XCXModule
{
    /**
     * 查询服务商的当月提审限额（quota）和加急次数
     */
    const QUERY_QUOTA = 'https://api.weixin.qq.com/wxa/queryquota?access_token=';

    /**
     * 加急审核申请
     */
    const SPEED_UP_AUDIT = 'https://api.weixin.qq.com/wxa/speedupaudit?access_token=';

    /**
     * 插件相关基础接口
     */
    const PLUGIN_BASIC = 'https://api.weixin.qq.com/wxa/plugin?access_token=';

    /**
     * @desc 获取直播房间的
     */
    const LIVE_ROOM = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token=';

    /***
     * todo 小程序店铺主页模板数据
     * @param int $wid
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     * @udpate 张永辉 2019年10月12日15:10:50 字节跳动小程序不判断是否发布小程序
     */
    public function processMainHome($wid = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '',
            'data' => ['title' => '', 'bgColor' => '', 'description' => '', 'rule' => '', 'rule_desc' => '', 'rule_title' => '', 'template_info' => '']
        ];
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRow($wid);
        // add by jonzhang 2018-03-12 小程序下架
        if ($row['errCode'] == 0 && empty($row['data']) && app('request')->input('come_from') != 'byteDance') {
            BLogger::getLogger('info')->info('该小程序已经下架或者取消授权,店铺id为:' . $wid);
            return $returnData;
        }
        $where = ['wid' => $wid, 'current_status' => 0, 'is_home' => 1];
        $xcxDataList = WXXCXMicroPageService::getListByConditionWithPage($where);
        if (!empty($xcxDataList[0]['data'])) {
            $xcxData = $xcxDataList[0]['data'][0];
            $templateInfo = $xcxData['template_info'];
            if (!empty($templateInfo) && $templateInfo != '[]') {
                $templateInfo = MallModule::processTemplateData($wid, $templateInfo, 0, 1);
                //小程序微页面富文本图片 加上图片域名 Herry 20180503
                $templateInfo = ProductModule::addProductContentHost($templateInfo);
            }
            $returnData['data'] = [
                'title' => $xcxData['title'],
                'bgColor' => $xcxData['bg_color'],
                'titleColor' => $xcxData['title_color'] ?? '',
                'description' => $xcxData['description'],
                //add MayJay
                'rule' => $xcxData['rule'],
                'rule_desc' => $xcxData['rule_desc'],
                'rule_title' => $xcxData['rule_title'],
                //end
                'template_info' => $templateInfo
            ];
        }
        return $returnData;
    }

    /***
     * todo 查询小程序下微页面数据信息
     * @param int $id
     * @return array
     * @author jonzhang
     * @date 2017-09-22
     * @update 张永辉 2018年6月29 数据返回当前微页面是否是首页
     */
    public function processXCXMicroPage($id = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        $where = ['current_status' => 0, 'id' => $id];
        $xcxDataList = WXXCXMicroPageService::getListByConditionWithPage($where);
        if (!empty($xcxDataList[0]['data'])) {
            $xcxData = $xcxDataList[0]['data'][0];
            $templateInfo = $xcxData['template_info'];
            $wid = $xcxData['wid'];
            if (!empty($templateInfo) && $templateInfo != '[]') {
                $templateInfo = MallModule::processTemplateData($wid, $templateInfo, 0, 1);
                //小程序微页面富文本图片 加上图片域名 Herry 20180503
                $templateInfo = ProductModule::addProductContentHost($templateInfo);
            }
            $returnData['data'] = [
                'title' => $xcxData['title'],
                'bgColor' => $xcxData['bg_color'],
                'description' => $xcxData['description'],
                'template_info' => $templateInfo,
                //add MayJay
                'rule' => $xcxData['rule'],
                'rule_desc' => $xcxData['rule_desc'],
                'rule_title' => $xcxData['rule_title'],
                'is_home' => $xcxData['is_home'],
                //end
            ];
        }
        return $returnData;
    }

    /**
     * 修改服务器域名
     * @update 张永辉 2018年7月9日 通过小程序配置id查询配置信息
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function modifyDomain($id, $action, $domain, $operatorId = 0, $operator = '')
    {
        //post data
        $httpsDomain = 'https://' . $domain;
        $postData = [
            'action' => $action,
            'requestdomain' => [$httpsDomain],
            'wsrequestdomain' => ['wss://' . $domain, config("app.xcx_socket_url")],
            'uploaddomain' => [$httpsDomain],
            'downloaddomain' => [$httpsDomain, "https://upx.cdn.huisou.cn", "https://hsshop-image-cs.huisou.cn", "https://image.huisou.cn"]
        ];

        $staffOperService = new StaffOperLogService();
        $xcxConfigService = new WXXCXConfigService();

        $row = $xcxConfigService->getRowById($id);
        $row['errCode'] != 0 && error('小程序不存在');

        //post url
        $postUrl = 'https://api.weixin.qq.com/wxa/modify_domain?access_token=' . $this->_getToken($row['data']['id']);
        //调用微信接口
        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //post result
        $result = json_decode($jsonData, true);
        //何书哲 2018年8月30日 添加后台操作日志
        $result['errcode'] != 0 && $staffOperService->write('操作失败：' . json_encode($result), 1, $id);
        if ($result['errcode'] == 0) {
            // 保存域名信息
            $xcxConfigService->updateData($row['data']['id'], [
                'request_domain' => $httpsDomain,
                'ws_request_domain' => implode(',', $postData['wsrequestdomain']),
                'upload_domain' => $httpsDomain,
                'download_domain' => implode(',', $postData['downloaddomain']),
            ]);
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                [$row['data']['wid'] ?? '', $row['data']['app_id'] ?? '', 1, '修改服务器域名成功', time(), $operatorId, $operator]);
            $staffOperService->write(json_encode($postData), 1, $id);
        } else {
            $result['errmsg'] = $this->dealWeCodeMessage($result);
        }
        return $result;
    }

    /**
     * 上传代码
     * add by wuxiaoping 2017.12.14 param (list)
     * @param integer $xcxid 小程序id
     * @param integer $template_id 小程序模板库id
     * @param string $version 小程序提交版本
     * @param string $desc 小程序提交描述
     * @param array $barList 底部导航数据数组
     * @param boolean $is_auth 是否自动提交审核
     * @param array $itemList 提交审核列表数据
     * @param integer $operatorId 操作者id
     * @param string $operator 操作者登录名
     * @param int $liveStatus 0:普通送审 1：直播送审
     * @return mixed
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     * @update 梅杰 2019年08月01日 19:50:14 增加图片域名
     * @update 何书哲 2020年03月11日 15:01:56 添加直播权限状态参数及直播组件参数
     */
    public function commit($xcxid, $template_id, $version, $desc, $barList = [], $is_auth = false, $itemList = [], $operatorId = 0, $operator = '', $liveStatus = 0)
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();

        $row = $xcxConfigService->getRowById($xcxid);
        if ($row['errCode'] != 0) {
            error('小程序不存在');
        }
        if (empty($row['data']['request_domain'])) {
            error('请设置域名');
        }

        // add by jonzhang 2018-03-07 默认读取小程序底部导航数据
        $tabBar = [];
        // 通过后台上传代码 $barList为空
        if (empty($barList)) {
            list($list, $pageHtml) = $xcxSyncFooterBarService->getAllList($row['data']['wid'], [], 'order');
            if (!empty($list['data'])) {
                $toolBarList = [];
                foreach ($list['data'] as $key => $value) {
                    $iconPathArr = explode('mctsource/', $value['icon_path']);
                    $selectedPathArr = explode('mctsource/', $value['selected_path']);
                    $toolBarList[$key]['text'] = $value['name'];
                    $toolBarList[$key]['pagePath'] = $value['page_path'];
                    $toolBarList[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
                    $toolBarList[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
                }
                $tabBar['selectedColor'] = '#b1292d';
                $tabBar['list'] = $toolBarList;
                $tabBar['backgroundColor'] = '#fff';
                $tabBar['borderStyle'] = 'black';
            }
        }
        /**默认小程序头部设置 add by wuxiaoping 2018.05.07 begin**/
        $returnData = $this->processMainHome($row['data']['wid']);
        /*$windowData['navigationBarBackgroundColor'] = "#000000";
        $windowData['navigationBarTextStyle'] = "white";
        $windowData['backgroundColor'] = "#ffffff";
        $windowData['backgroundTextStyle'] = "light";*/
        $windowData = [];
        // 自定义导航标题背景色,标题字体颜色
        if (isset($returnData['data']['bgColor']) && !empty($returnData['data']['bgColor'])) {
            $windowData['navigationBarBackgroundColor'] = $returnData['data']['bgColor'];
        }
        if (isset($returnData['data']['titleColor']) && !empty($returnData['data']['titleColor'])) {
            $windowData['navigationBarTextStyle'] = $returnData['data']['titleColor'] ?? 'black';
        }
        /**end**/

        // 定义请求数据
        $extJson = [
            'extAppid' => $row['data']['app_id'],
            'ext' => [
                'domain' => $row['data']['request_domain'],
                'wid' => $row['data']['wid'],
                'wxxcxConfigId' => $row['data']['id'],
                'imgDomain' => config("app.source_img_url"),
                'live_status' => (int)$liveStatus,
                'live_url' => $liveStatus == 1 ? 'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin' : ''
            ],
            'window' => $windowData,
            'tabBar' => $barList ? $barList : $tabBar
        ];
        $postData = [
            'template_id' => $template_id,
            'ext_json' => $this->_jsonUnescapedUnicode($extJson),
            'user_version' => $version,
            'user_desc' => $desc
        ];

        $remark = "";
        if (!empty($postData)) {
            $remark = json_encode($postData);
        }

        // 插入小程序操作记录表
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator,remark) values (?,?,?,?,?,?,?,?,?)', [$row['data']['wid'], $row['data']['app_id'] ?? '', 4, '上传代码', time(), $template_id, $operatorId, $operator, $remark]);

        // 上传代码url
        $postUrl = 'https://api.weixin.qq.com/wxa/commit?access_token=' . $this->_getToken($row['data']['id']);
        // JSON_UNESCAPED_UNICODE 中文疑难杂症解决方案
        $result = $this->curlJson($postUrl, json_encode($postData, JSON_UNESCAPED_UNICODE));
        if ($result['errcode'] == 0) {
            // 上传成功添加小程序订阅消息模板
            app(SubscribeMessagePushService::class)->saveSubTemplate($row['data']['wid']);

            // 上传成功更新小程序状态
            $xcxConfigService->updateData($row['data']['id'], [
                'status' => 1,
                'status_time' => time(),
                'version' => $version,
                'version_desc' => $desc,
                'template_id' => $template_id,
                'commit_live_status' => $liveStatus
            ]);
            // 插入小程序上传代码成功操作记录
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator,live_status) values (?,?,?,?,?,?,?,?,?)', [$row['data']['wid'], $row['data']['app_id'] ?? '', 3, '上传代码成功', time(), $template_id, $operatorId, $operator, $liveStatus]);
            // 何书哲 2018年8月30日 添加后台操作日志
            $staffOperService->write(json_encode($postData, JSON_UNESCAPED_UNICODE), 2, $xcxid);
            // 自动提交审核时处理 add by wuxiaoping 2017.12.14
            if ($is_auth) {
                $this->submitAudit($row['data']['id'], $itemList, $operatorId, $operator);
            }
            success('上传代码成功');
        } else {
            $staffOperService->write('操作失败：' . json_encode($result), 2, $xcxid);
            error($this->dealWeCodeMessage($result));
        }
        error('上传代码失败');
    }

    /**
     * 获取类目
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function getCategory($row, $flag = false, $operatorId = 0, $operator = '')
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();

        //插入小程序获取类目操作记录
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)', [$row['data']['wid'] ?? '', $row['data']['app_id'] ?? '', 6, '获取类目', time(), $operatorId, $operator]);

        //获取类目url
        $url = 'https://api.weixin.qq.com/wxa/get_category?access_token=' . $this->_getToken($row['data']['id']);
        //调用微信接口
        //$jsonData = CurlBuilder::to($url)->asJsonRequest()->get();
        //$result = json_decode($jsonData, true);
        $result = $this->curlJson($url);
        //何书哲 2018年8月30日 添加后台操作日志
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode($result), 4, $row['data']['id']);
        }
        if (isset($result['errcode'])) {
            if ($result['errcode'] == 0) {
                //保存类目
                $xcxConfigService->updateData($row['data']['id'], ['category_list' => json_encode($result['category_list'])]);
                //插入小程序获取类目成功操作记录
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)', [$row['data']['wid'], $row['data']['app_id'] ?? '', 5, '获取类目成功', time(), $operatorId, $operator]);
                $staffOperService->write(json_encode($result['category_list']), 4, $row['data']['id']);
                //add by wuxiaoping 2018.01.05
                if ($flag) {
                    return $result['category_list'];
                }
                //add by jonzhang 2018-03-13
                if (!empty($result['category_list'])) {
                    success('获取类目成功');
                } else {
                    error('没有获取到类目');
                }
            }

            error($this->dealWeCodeMessage($result));
        }
        error('获取类目失败');
    }

    /**
     * 获取页面
     * @upadte 陈文豪 2018年07月10日 修改为ID
     */
    public function getPage($xcxid, $operatorId = 0, $operator = '')
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();

        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');

        $wid = $row['data']['wid'];

        //插入小程序获取页面操作记录
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 8, '获取页面', time(), $operatorId, $operator]);

        //获取页面url
        $url = 'https://api.weixin.qq.com/wxa/get_page?access_token=' . $this->_getToken($row['data']['id']);
        //调用微信接口
        //$jsonData = CurlBuilder::to($url)->asJsonRequest()->get();
        //$result = json_decode($jsonData, true);
        //add by jonzhang 2018-04-19 没有使用封装的CurlBuilder而使用源生的get请求
        // CurlBuilder::to($url)->asJsonRequest()->get() 这个封装的方法 当小程序authorizer_access_token过期后,获取到最新的authorizer_access_token
        // 第一次会报 {"errcode":85085,"errmsg":"submit audit reach limit, please try later hint: [.B3v901351513]"}
        $result = $this->curlJson($url);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode($result), 3, $xcxid);
        }
        if (isset($result['errcode'])) {
            if ($result['errcode'] == 0) {
                //保存页面列表
                $xcxConfigService->updateData($row['data']['id'], ['page_list' => json_encode($result['page_list'])]);
                //插入小程序获取页面成功操作记录
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 7, '获取页面成功', time(), $operatorId, $operator]);
                //总后台日志
                $staffOperService->write(json_encode($result['page_list']), 3, $xcxid);
                success('获取页面成功');
            }

            error($this->dealWeCodeMessage($result));
        }

        error('获取页面失败');
    }

    /**
     * 提交审核
     * @modify 张国军 2018-06-26 记录提交审核接口数据
     * @modify 张国军 2018年07月3日 提交审核添加日志记录
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     * @update 何书哲 2019年10月10日 提交审核添加审核单id字段
     */
    public function submitAudit($xcxid, $list, $operatorId = 0, $operator = '')
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();
        $xcxFooterBarService = new WXXCXFooterBarService();
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();

        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');

        $wid = $row['data']['wid'];

        $auditList = "";
        if (!empty($list)) {
            $auditList = json_encode($list);
        }

        //插入小程序提交审核操作记录
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator,remark) values (?,?,?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 10, '提交审核', time(), $row['data']['template_id'], $operatorId, $operator, $auditList]);

        //提交审核url
        $postUrl = 'https://api.weixin.qq.com/wxa/submit_audit?access_token=' . $this->_getToken($row['data']['id']);
        $postData['item_list'] = $list;
        //$result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //$result = json_decode($result, true);

        //JSON_UNESCAPED_UNICODE 中文疑难杂症解决方案
        $result = $this->curlJson($postUrl, json_encode($postData, JSON_UNESCAPED_UNICODE));
        $remark = "";
        is_array($result) && $remark = json_encode($result);

        //插入小程序提交审核结果操作记录
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator,remark) values (?,?,?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 19, '提交审核接口', time(), $row['data']['template_id'], $operatorId, $operator, $remark]);
        //何书哲 2018年8月30日 添加后台操作日志
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode($result), 5, $xcxid);
        }
        //返回处理
        switch ($result['errcode']) {
            case 0:
                /*总后台提交审核成功后，把底部导航栏的修改同步到微信小程序 begin add by wuxiaoping 2018.04.28*/
                $localBarData = $xcxFooterBarService->getAllList($wid);
                $syncBarData = $xcxSyncFooterBarService->getAllList($wid);
                //同步底部导航栏 hsz 2018.05.25 begin
                if (isset($syncBarData[0]['data']) && $syncBarData[0]['data']) {
                    foreach ($syncBarData[0]['data'] as $key => $value) {
                        $xcxSyncFooterBarService->del($value['id']);
                    }
                }
                if (isset($localBarData[0]['data']) && $localBarData[0]['data']) {
                    foreach ($localBarData[0]['data'] as $ikey => $item) {
                        $saveData['wid'] = $item['wid'];
                        $saveData['name'] = $item['name'];
                        $saveData['page_path'] = $item['page_path'];
                        $saveData['icon_path'] = $item['icon_path'];
                        $saveData['selected_path'] = $item['selected_path'];
                        $saveData['order'] = $item['order'];
                        $saveData['page_id'] = $item['page_id'];
                        $saveData['url_title'] = $item['url_title'];
                        $saveData['is_sync_weixin'] = $item['is_sync_weixin'];
                        $saveData['is_can_revise_url'] = $item['is_can_revise_url'];
                        $xcxSyncFooterBarService->add($saveData);
                    }
                }
                //同步底部导航栏  hsz end
                /*
                if ((isset($localBarData[0]['data']) && $localBarData[0]['data']) && (isset($syncBarData[0]['data']) && $syncBarData[0]['data'])) {
                    if (count($localBarData[0]['data']) <> count($syncBarData[0]['data'])) {
                        foreach ($syncBarData[0]['data'] as $key => $value) {
                            $res = $xcxSyncFooterBarService->del($value['id']);
                        }
                    }
                    foreach ($localBarData[0]['data'] as $ikey =>$item) {
                        $saveData['wid']               = $item['wid'];
                        $saveData['name']              = $item['name'];
                        $saveData['page_path']         = $item['page_path'];
                        $saveData['icon_path']         = $item['icon_path'];
                        $saveData['selected_path']     = $item['selected_path'];
                        $saveData['order']             = $item['order'];
                        $saveData['page_id']           = $item['page_id'];
                        $saveData['url_title']         = $item['url_title'];
                        $saveData['is_sync_weixin']    = $item['is_sync_weixin'];
                        $saveData['is_can_revise_url'] = $item['is_can_revise_url'];
                        $xcxSyncFooterBarService->add($saveData);
                    }
                }
                */
                /****end****/
                // 插入小程序提交审核成功操作记录
                // update 何书哲 2019年10月10日 提交审核添加审核单id字段
                $xcxConfigService->updateData($row['data']['id'], ['status' => 2, 'status_time' => time(), 'submit_audit_item_list' => $auditList, 'audit_id' => $result['auditid'] ?? '']);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 9, '提交审核成功', time(), $row['data']['template_id'], $operatorId, $operator]);
                $staffOperService->write(json_encode($postData, JSON_UNESCAPED_UNICODE), 5, $xcxid);
                success('提交审核成功');
                break;
            case 85009:
                $xcxConfigService->updateData($row['data']['id'], ['status' => 2, 'status_time' => time(), 'submit_audit_item_list' => $auditList]);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                    [$wid, $row['data']['app_id'] ?? '', 9, '已经有正在审核的版本', time(), $operatorId, $operator]);
                error('已经有正在审核的版本');
                break;

            default:
                error($this->dealWeCodeMessage($result));
                break;
        }

        error('提交审核失败');
    }

    /**
     * 发布
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function release($xcxid, $operatorId = 0, $operator = '')
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();

        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');

        $wid = $row['data']['wid'];
        //插入小程序提交发布操作记录
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)', [$wid, $row['data']['app_id'] ?? '', 12, '提交发布', time(), $row['data']['template_id'], $operatorId, $operator]);

        //小程序提交发布url
        $postUrl = 'https://api.weixin.qq.com/wxa/release?access_token=' . $this->_getToken($row['data']['id']);
        $result = $this->curlJson($postUrl, '{}');
        //何书哲 2018年8月30日 添加后台操作日志
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode($result), 7, $xcxid);
        }
        //返回处理
        if ($result['errcode'] == 0) {
            $xcxConfigService->updateData($row['data']['id'], ['status' => 5, 'status_time' => time()]);
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
                [$wid, $row['data']['app_id'] ?? '', 11, '发布成功', time(), $row['data']['template_id'], $operatorId, $operator]);
            $staffOperService->write('操作成功', 7, $xcxid);
            success('发布成功');
        }

        error($this->dealWeCodeMessage($result));
    }

    /**
     * 绑定微信用户为小程序体验者
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function bindTester($row, $wechatID)
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();
        $postUrl = 'https://api.weixin.qq.com/wxa/bind_tester?access_token=' . $this->_getToken($row['data']['id']);
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData(['wechatid' => $wechatID])->post();
        $result = json_decode($result, true);
        //返回处理
        //何书哲 2018年8月30日 添加后台操作日志
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败: ' . json_encode($result), 6, $row['data']['id']);
        }
        switch ($result['errcode']) {
            case 0:
                $staffOperService->write('操作成功: ' . json_encode(['wechatid' => $wechatID]), 6, $row['data']['id']);
                success('绑定成功');
                break;

            default:
                error($this->dealWeCodeMessage($result));
                break;
        }

        error('绑定失败');
    }

    /**
     * 解除绑定小程序的体验者
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function unbindTester($xcxid, $wechatID)
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();
        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');

        $postUrl = 'https://api.weixin.qq.com/wxa/unbind_tester?access_token=' . $this->_getToken($row['data']['id']);
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData(['wechatid' => $wechatID])->post();
        $result = json_decode($result, true);
        //返回处理
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode(['wechatid' => $wechatID]), 8, $xcxid);
        }
        switch ($result['errcode']) {
            case 0:
                $staffOperService->write('操作成功：' . json_encode(['wechatid' => $wechatID]), 8, $xcxid);
                success('解绑成功');
                break;

            default:
                error($this->dealWeCodeMessage($result));
                break;
        }

        error('解绑失败');
    }

    /**
     * 获取体验小程序的体验二维码
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function getQrCode($xcxid)
    {
        $staffOperService = new StaffOperLogService();
        //get url
        $url = 'https://api.weixin.qq.com/wxa/get_qrcode?access_token=' . $this->_getToken($xcxid);

        //调用微信接口
        //$jsonData = CurlBuilder::to($url)->asJsonRequest()->get();
        //add by jonzhang 2018-04-19
        $jsonData = $this->curlJson($url, [], false);
        $result = json_decode($jsonData, true);
        if ($result['errcode'] != 0) {
            $staffOperService->write('操作失败', 10, $xcxid);
            error($this->dealWeCodeMessage($result));
        }
        $staffOperService->write('操作成功', 10, $xcxid);
        header("Content-type:image/png");
        echo $jsonData;
        exit;
    }

    /**
     * 获取帐号下已存在的模板列表
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function getTemplates($row)
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();

        $postUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=' . $this->_getToken($row['data']['id']);
        $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData(['offset' => 0, 'count' => 10])->post();
        $result = json_decode($result, true);
        if ($result['errcode'] == 0) {
            //何书哲 2018年8月30日 添加后台操作日志
            $staffOperService->write(json_encode($result['list']), 9, $row['data']['id']);
            success('', '', $result['list']);
        }
        $staffOperService->write('操作失败: ' . json_encode($result), 9, $row['data']['id']);
        error($this->dealWeCodeMessage($result));
    }

    /**
     * 组合模板并添加至帐号下的个人模板库
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function addTemplates($xcxid)
    {
        $xcxConfigService = new WXXCXConfigService();
        $staffOperService = new StaffOperLogService();
        $row = $xcxConfigService->getRowById($xcxid);
        $row['errCode'] != 0 && error('小程序不存在');

        $templates = [
            ['id' => 'AT0004', 'value' => array(9, 1, 3, 33, 17, 56, 34), 'name' => '交易提醒'],
            ['id' => 'AT0007', 'value' => array(7, 6, 3, 2, 23, 53), 'name' => '订单发货提醒'],
            ['id' => 'AT0036', 'value' => array(33, 35, 3, 5, 30), 'name' => '退款通知']
        ];
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=' . $this->_getToken($row['data']['id']);
        foreach ($templates as $key => $val) {
            $postData['id'] = $val['id'];
            $postData['keyword_id_list'] = $val['value'];
            $result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
            $result = json_decode($result, true);
            if ($result['errcode'] != 0) {
                $staffOperService->write('操作失败：' . json_encode($postData), 11, $xcxid);
                error($this->dealWeCodeMessage($result));
            }
        }
        $staffOperService->write(json_encode($templates), 11, $xcxid);
        success();
    }

    /**
     * 5.3不支持JSON_UNESCAPED_UNICODE解决方案
     *
     * @param  array $array
     *
     * @return string
     *
     * @author 黄东[406764368@qq.com] BY 2017-07-14T22:54:41+0800
     */
    private function _jsonUnescapedUnicode($array)
    {
        $str = json_encode($array);
        $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", function ($matchs) {
            return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
        }, $str);

        return $str;
    }

    /**
     * 获取小程序token
     * @param $id int 小程序在数据库中的ID
     */
    private function _getToken($id)
    {
        $result = (new ThirdPlatform())->getAuthorizerAccessToken(['id' => $id]);
        if ($result['errCode'] == 0) {
            return $result['data'];
        } else {
            error($result['errMsg']);
        }
    }

    /**
     * 原生CURL
     * @param $url
     * @param $datas
     * @param bool $jsonFlag
     * @param string $type
     * @param int $second
     * @return array
     */
    public function curlJson($url, $datas = [], $jsonFlag = true, $type = '', $second = 30)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '119.29.29.29');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 80);
        if (stripos($url, 'https://') !== false) {
            // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 自动设置Referer
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        if ($datas) {
            // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POST, true);
            // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        if ($type == 'json') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache"));
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (intval($info['http_code']) == 200) {
            if ($jsonFlag === true) {
                return json_decode($result, true);
            } else {
                return $result;
            }
        } else {
            Log::info('Curl request failed: ' . json_encode($info));
            //何书哲 2019年01月03日 module会有很多地方使用errmsg，添加errmsg
            return ['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服'];
        }
    }

    /**
     * todo 获取小程序审核状态
     * @param array $data
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-09-29
     */
    public function getAuditStatus($data = [])
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => ''];
        if (empty($data)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为空';
            return $returnData;
        }
        $thirdPlatForm = new ThirdPlatform();
        $authorizerData = $thirdPlatForm->getAuthorizerAccessToken($data);
        if ($authorizerData['errCode'] == 0 && !empty($authorizerData['data'])) {
            $authorizerAccessToken = $authorizerData['data'];
            $postUrl = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=%s';
            $postUrl = sprintf($postUrl, $authorizerAccessToken);
            try {
                //此处为get请求
                $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->get();
                $jsonData = json_decode($jsonData, true);
                if (isset($jsonData['errcode']) && $jsonData['errcode'] == 0) {
                    $message = '';
                    $status = $jsonData['status'];
                    switch ($status) {
                        case 0:
                            $message = '审核成功';
                            break;
                        case 1:
                            $message = '审核失败，原因为：' . $jsonData['reason'] ?? '原因为空';
                            break;
                        case 2:
                            $message = '审核中';
                            break;
                    }
                    $auditId = $jsonData['auditid'] ?? 'auditid为空';
                    $returnData['data'] = $message . ' auditid:' . $auditId;
                    return $returnData;
                } else {
                    $code = $jsonData['errcode'] ?? '错误码为空';
                    $message = $this->dealWeCodeMessage($jsonData);
                    $returnData['errCode'] = -2;
                    $returnData['errMsg'] = '错误码:' . $code . '错误信息:' . $message;
                    return $returnData;
                }
            } catch (\Exception $ex) {
                $returnData['errCode'] = -10;
                $returnData['errMsg'] = $ex->getMessage();
                return $returnData;
            }
        } else {
            return $authorizerData;
        }
    }

    /***
     * todo 天趋势，周趋势，月趋势
     * @param array $data
     * @return array
     */
    public function visitTrend($data = [])
    {
        $returnData = [
            'errCode' => 0, 'errMsg' => '',
            'data' => [
                'total' => [
                    "pv" => ["value" => 0, "growth" => "-"],//浏览量
                    "uv" => ["value" => 0, "growth" => "-"],//访客数
                    "new_uv" => ["value" => 0, "growth" => "-"],//新访客数
                    "stay_time_uv" => ["value" => 0, "growth" => "-"],//人均停留时长
                    "visit_depth" => ["value" => 0, "growth" => "-"]//平均访问深度
                ]]];

        if (empty($data['wid']) || empty($data['type']) || empty($data['beginDate']) || empty($data['endDate'])) {
            $returnData['errCode'] = -13;
            $returnData['errMsg'] = '传入数据为:' . json_encode($data);
            return $returnData;
        }

        $wid = $data['wid'];
        $type = $data['type'];
        $beginDate = $data['beginDate'];
        $endDate = $data['endDate'];

        $scope = [1, 2, 3];
        if (!in_array($type, $scope)) {
            $returnData['errCode'] = -11;
            $returnData['errMsg'] = '类型不符合要求';
            return $returnData;
        }
        //微信接口所需要的日期格式为20171110 此处传递的日期格式为2017-11-10
        $beginDate = date("Ymd", strtotime($beginDate));
        $endDate = date("Ymd", strtotime($endDate));

        $yp = 0;
        $yu = 0;
        $ynu = 0;
        $ystu = 0;
        $yvd = 0;
        $thirdPlatform = new ThirdPlatform();
        for ($i = 0; $i < 2; $i++) {
            if ($type == 1)//天趋势
            {
                if ($i == 1) {
                    $beginDate = date("Ymd", strtotime("-1 day", strtotime($beginDate)));
                    $endDate = date("Ymd", strtotime("-1 day", strtotime($endDate)));
                }
                $where = ['wid' => $wid, 'beginDate' => $beginDate, 'endDate' => $endDate];
                //$visitTrendData = $thirdPlatform->visitTrendForDaily($where);
                //获取dc库dc_viewxcx_count表数据 Herry 20180409
                $visitTrendData = $this->getVisitData($wid, $beginDate, $endDate);
            } else if ($type == 2)//周趋势
            {
                if ($i == 1) {
                    $beginDate = date("Ymd", strtotime("-1 week", strtotime($beginDate)));
                    $endDate = date("Ymd", strtotime("-1 week", strtotime($endDate)));
                }
                $where = ['wid' => $wid, 'beginDate' => $beginDate, 'endDate' => $endDate];
                //$visitTrendData = $thirdPlatform->visitTrendForWeekly($where);
                $visitTrendData = $this->getVisitData($wid, $beginDate, $endDate, 1);
            } else if ($type == 3)//月趋势
            {
                if ($i == 1) {
                    //取当前月的上一个月
                    $beginDate = date("Ymd", strtotime("-1 month", strtotime($beginDate)));
                    //获取月有多少天数
                    $num = date("t", strtotime($beginDate));
                    //获取年月
                    $date = date("Ym", strtotime($beginDate));
                    //年月日第一天
                    $beginDate = $date . '01';
                    //年月日最后一天
                    $endDate = $date . $num;
                }
                $where = ['wid' => $wid, 'beginDate' => $beginDate, 'endDate' => $endDate];
                //$visitTrendData = $thirdPlatform->visitTrendForMonthly($where);
                $visitTrendData = $this->getVisitData($wid, $beginDate, $endDate, 2);
            }
            if (isset($visitTrendData['errCode']) && $visitTrendData['errCode'] == 0 && !empty($visitTrendData['data'])) {
                //昨天的浏览量和访客数
                if ($i == 0) {
                    //浏览量
                    $yp = $returnData['data']['total']['pv']['value'] = $visitTrendData['data'][0]['visit_pv'] ?? 0;
                    //访客数
                    $yu = $returnData['data']['total']['uv']['value'] = $visitTrendData['data'][0]['visit_uv'] ?? 0;
                    //新访客数
                    $ynu = $returnData['data']['total']['new_uv']['value'] = $visitTrendData['data'][0]['visit_uv_new'] ?? 0;
                    //人均停留时长
                    $ystu = $returnData['data']['total']['stay_time_uv']['value'] = $visitTrendData['data'][0]['stay_time_uv'] ?? 0;
                    //平均访问深度
                    $yvd = $returnData['data']['total']['visit_depth']['value'] = $visitTrendData['data'][0]['visit_depth'] ?? 0;
                }//前二天的浏览量和访客数
                else if ($i == 1) {
                    $bp = $visitTrendData['data'][0]['visit_pv'] ?? 0;
                    //访客数
                    $bu = $visitTrendData['data'][0]['visit_uv'] ?? 0;
                    //新访客数
                    $bnu = $visitTrendData['data'][0]['visit_uv_new'] ?? 0;
                    //人均停留时长
                    $bstu = $visitTrendData['data'][0]['stay_time_uv'] ?? 0;
                    //平均访问深度
                    $bvd = $visitTrendData['data'][0]['visit_depth'] ?? 0;

                    //浏览量
                    if ($yp > $bp) {
                        //此处需要对$b=0做特殊处理
                        if ($bp > 0)
                            $returnData['data']['total']['pv']['growth'] = '+' . sprintf("%01.2f", ($yp - $bp) / $bp * 100) . '%';
                        else
                            $returnData['data']['total']['pv']['growth'] = '+' . sprintf("%01.2f", $yp * 100) . '%';
                    } else if ($bp > $yp) {
                        $returnData['data']['total']['pv']['growth'] = '-' . sprintf("%01.2f", ($bp - $yp) / $bp * 100) . '%';
                    }

                    //访客数
                    if ($yu > $bu) {
                        //此处需要对$b=0做特殊处理
                        if ($bu > 0)
                            $returnData['data']['total']['uv']['growth'] = '+' . sprintf("%01.2f", ($yu - $bu) / $bu * 100) . '%';
                        else
                            $returnData['data']['total']['uv']['growth'] = '+' . sprintf("%01.2f", $bu * 100) . '%';
                    } else if ($bu > $yu) {
                        $returnData['data']['total']['uv']['growth'] = '-' . sprintf("%01.2f", ($bu - $yu) / $bu * 100) . '%';
                    }

                    //新访客数
                    if ($ynu > $bnu) {
                        //此处需要对$b=0做特殊处理
                        if ($bnu > 0)
                            $returnData['data']['total']['new_uv']['growth'] = '+' . sprintf("%01.2f", ($ynu - $bnu) / $bnu * 100) . '%';
                        else
                            $returnData['data']['total']['new_uv']['growth'] = '+' . sprintf("%01.2f", $bnu * 100) . '%';
                    } else if ($bnu > $ynu) {
                        $returnData['data']['total']['new_uv']['growth'] = '-' . sprintf("%01.2f", ($bnu - $ynu) / $bnu * 100) . '%';
                    }

                    //人均停留时长
                    if ($ystu > $bstu) {
                        //此处需要对$b=0做特殊处理
                        if ($bstu > 0)
                            $returnData['data']['total']['stay_time_uv']['growth'] = '+' . sprintf("%01.2f", ($ystu - $bstu) / $bstu * 100) . '%';
                        else
                            $returnData['data']['total']['stay_time_uv']['growth'] = '+' . sprintf("%01.2f", $bstu * 100) . '%';
                    } else if ($bstu > $ystu) {
                        $returnData['data']['total']['stay_time_uv']['growth'] = '-' . sprintf("%01.2f", ($bstu - $ystu) / $bstu * 100) . '%';
                    }

                    //平均访问深度
                    if ($yvd > $bvd) {
                        //此处需要对$b=0做特殊处理
                        if ($bvd > 0)
                            $returnData['data']['total']['visit_depth']['growth'] = '+' . sprintf("%01.2f", ($yvd - $bvd) / $bvd * 100) . '%';
                        else
                            $returnData['data']['total']['visit_depth']['growth'] = '+' . sprintf("%01.2f", $bvd * 100) . '%';
                    } else if ($bvd > $yvd) {
                        $returnData['data']['total']['visit_depth']['growth'] = '-' . sprintf("%01.2f", ($bvd - $yvd) / $bvd * 100) . '%';
                    }
                }
            }
        }
        return $returnData;
    }

    /***
     * todo 获取小程序要发布的最新版本
     * @return array
     * @author jonzhang
     * @date 2018-01-27
     */
    public function getXCXOnLine()
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $templateService = new WXXCXTemplateService();
        $templateData = $templateService->getListByCondition(['current_status' => 0, 'is_online' => 1, 'type' => 2]);
        if ($templateData['errCode'] == 0 && !empty($templateData['data'][0])) {
            $returnData['data'] = $templateData['data'][0];
        } else {
            return $templateData;
        }
        return $returnData;
    }

    /**
     * 获取小程序线上版本
     * @return array
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月14日 19:12:46
     */
    public function getXcxOnlineVersion()
    {
        // 返回线上普通版本和直播版本
        $version = [
            'common' => [],
            'live' => []
        ];
        $onlineVersion = WXXCXTemplate::query()
            ->where('current_status', 0)
            ->where('is_online', '>', 0)
            ->where('type', 2)
            ->get()
            ->toArray();
        foreach ($onlineVersion as $item) {
            if ($item['is_online'] == 1) {
                $version['common'] = $item;
            } else {
                $version['live'] = $item;
            }
        }
        return $version;
    }


    /**
     * todo 小程序审核撤回
     * @param $wid
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2018-01-29
     * @update 张永辉 2018年7月10日 根据id获取小程序配置信息
     * @update 何书哲 2018年8月30日 添加后台操作日志
     */
    public function cancelAudit($id, $operatorId = 0, $operator = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $staffOperService = new StaffOperLogService();
        if (empty($id)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = 'id不能为空';
            return $returnData;
        }
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($id);
        if ($row['errCode'] != 0) {
            return $row;
        }
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
            [$row['data']['wid'], $row['data']['app_id'] ?? '', 13, '取消审核', time(), $operatorId, $operator]);
        $tokenData = (new ThirdPlatform())->getAuthorizerAccessToken(['id' => $id]);
        if ($tokenData['errCode'] == 0 && !empty($tokenData['data'])) {
            $getUrl = 'https://api.weixin.qq.com/wxa/undocodeaudit?access_token=%s';
            $getUrl = sprintf($getUrl, $tokenData['data']);
            $jsonData = CurlBuilder::to($getUrl)->asJsonRequest()->get();
            $jsonData = json_decode($jsonData, true);
            //何书哲 2018年8月30日 添加后台操作日志
            if (isset($jsonData['errcode']) && $jsonData['errcode'] != 0) {
                $staffOperService->write('操作失败：' . json_encode($jsonData), 12, $id);
            }
            if (isset($jsonData['errcode'])) {
                if ($jsonData['errcode'] == 0) {
                    $xcxConfigService->updateData($row['data']['id'], ['status' => 6, 'status_time' => time()]);
                    DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                        [$row['data']['wid'], $row['data']['app_id'] ?? '', 14, '取消审核成功', time(), $operatorId, $operator]);
                    $staffOperService->write('操作成功', 12, $id);
                    return $returnData;
                }

                if ($jsonData['errcode'] == -1) {
                    $returnData['errCode'] = -102;
                    $returnData['errMsg'] = $this->dealWeCodeMessage($jsonData);
                    return $returnData;
                } else if ($jsonData['errcode'] == 87013) {
                    $returnData['errCode'] = -103;
                    $returnData['errMsg'] = $this->dealWeCodeMessage($jsonData);
                    return $returnData;
                } else {
                    $returnData['errCode'] = -104;
                    $returnData['errMsg'] = $this->dealWeCodeMessage($jsonData);
                    return $returnData;
                }
            } else {
                $code = $jsonData['errcode'] ?? '周趋势错误码为空';
                $message = $jsonData['errmsg'] ?? '周趋势错误信息为空';
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '错误码:' . $code . '错误信息:' . $message;
                return $returnData;
            }
        } else {
            return $tokenData;
        }
    }

    /**
     * 统计小程序访问数据 脚本执行
     */
    public function xcxStatistics()
    {
        DB::table('wxxcx_config')->select('wid')
            ->where('current_status', 0)
            ->chunk(100, function ($shops) {
                $third_platform = new ThirdPlatform();
                $connect = DB::connection('mysql_dc_log');
                foreach ($shops as $shop) {
                    try {
                        $yesterday = date('Ymd', strtotime('-1 days'));
                        $return = $third_platform->visitTrendForDaily(['wid' => $shop->wid, 'beginDate' => $yesterday, 'endDate' => $yesterday]);
                        if ($return['errCode'] == 0) {
                            //todo 获取访问分布
                            //查询成功 插入dc_viewxcx_count表 先查询是否已经有昨天的记录
                            $res = $connect->select("select id from dc_wxxcx_config where wid = " . $shop->wid . " and createTime = " . $yesterday);
                            if (empty($res[0]->id)) {
                                $connect->update("insert into dc_wxxcx_config values (0," . $shop->wid . "," . $return['data']['visit_pv'] . "," . $return['data']['visit_uv'] . "
                                ," . $return['data']['visit_uv_new'] . "," . $return['data']['stay_time_uv'] . "," . $return['data']['visit_depth'] . ")");
                            }
                        } else {
                            //失败 记录到错误日志文件
                            Log::notice('获取昨日小程序访问数据错误,wid:' . $shop->wid . ',date:' . $yesterday . ',error:' . $return['data']['errMsg']);
                        }

                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                        continue;
                    }
                }
            });

        Log::info('统计店铺订单数据脚本执行完成');
    }

    /**
     * 从dc库获取小程序访问数据
     * @param $wid int 店铺ID
     * @param $begin int 开始日期 如: 20180408
     * @param $end int 结束日期 如: 20180408
     * @param $type int 查询类型 0日趋势 1周趋势 2月趋势
     * @return array
     */
    public function getVisitData($wid, $begin, $end, $type = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => ''];
        $connect = DB::connection('mysql_dc_log');

        $begin = strtotime($begin);
        $end = strtotime($end);
        $where = "wid = " . $wid . " and ";
        if ($type == 0) {
            $where .= "createTime = " . $begin;
        } elseif ($type == 1 || $type == 2) {
            $where .= "createTime >= " . $begin . " and createTime <= " . $end;
        }
        $res = $connect->select("select sum(xcx_pv) as visit_pv, sum(xcx_uv) as visit_uv, sum(visit_uv_new) as visit_uv_new, sum(stay_time_uv) as stay_time_uv, sum(visit_depth) as visit_depth from dc_viewxcx_count where " . $where);
        $returnData['data'] = [
            [
                'visit_pv' => $res[0]->visit_pv ?: 0,
                'visit_uv' => $res[0]->visit_uv ?: 0,
                'visit_uv_new' => $res[0]->visit_uv_new ?: 0,
                'stay_time_uv' => $res[0]->stay_time_uv ?: 0,
                'visit_depth' => $res[0]->visit_depth ?: 0
            ]
        ];

        return $returnData;
    }

    /**
     * todo 设置小程序业务域名
     * @author jonzhang @date 2018-04-27
     * @upadte 陈文豪 2018年07月10日 修改为ID
     * @update 何书哲 2018年08月30日 添加后台操作日志
     */
    public function setWebviewDomain($xcxid, $action = "set", $domain = "", $operatorId = 0, $operator = '')
    {
        $returnData = ["errCode" => 0, "errMsg" => ""];
        $staffOperService = new StaffOperLogService();
        $errMsg = "";
        if (empty($xcxid)) {
            $errMsg .= "id为空";
        }
        if (empty($domain)) {
            $errMsg .= "域名不能够为空";
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -1001;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        $sourceDomain = $domain;
        //post data
        $httpsDomain = [];
        $domain = explode(",", $domain);
        foreach ($domain as $item) {
            $httpsDomain[] = "https://" . $item;
        }
        $postData = [
            'action' => $action,
            'webviewdomain' => $httpsDomain
        ];

        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxid);
        if ($row['errCode'] != 0 || ($row['errCode'] == 0 && empty($row['data']))) {
            //BLogger::getLogger('info')->info('setWebviewDomain:',$row);
            return $row;
        }
        //post url
        $postUrl = "https://api.weixin.qq.com/wxa/setwebviewdomain?access_token=%s";
        $postUrl = sprintf($postUrl, $this->_getToken($row['data']['id']));
        //调用微信接口
        //BLogger::getLogger('info')->info('setWebviewDomain postData:',$postData);
        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //BLogger::getLogger('info')->info('setWebviewDomain returnValue:'.$jsonData);
        //post result
        $result = json_decode($jsonData, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $staffOperService->write('操作失败：' . json_encode($result), 16, $xcxid);
        }
        if (isset($result['errcode'])) {
            if ($result['errcode'] == 0) {
                //保存域名信息
                $xcxConfigService->updateData($row['data']['id'], [
                    'webview_domain' => $sourceDomain
                ]);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                    [$row['data']['wid'], $row['data']['app_id'] ?? '', 15, '设置小程序业务域名成功', time(), $operatorId, $operator]);
                $staffOperService->write(json_encode($postData), 16, $xcxid);
            } else {
                $returnData['errCode'] = -1002;
                $returnData['errMsg'] = '错误码:' . $result['errcode'] . ',错误信息:' . $this->dealWeCodeMessage($result);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                    [$row['data']['wid'], $row['data']['app_id'] ?? '', 16, '设置小程序业务域名失败', time(), $operatorId, $operator]);
            }
        } else {
            $returnData['errCode'] = -1003;
            $returnData['errMsg'] = '调用设置小程序业务域名接口失败';
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time) values (?,?,?,?,?)',
                [$row['data']['wid'], $row['data']['app_id'] ?? '', 17, '调用设置小程序业务域名接口失败', time()]);
        }
        return $returnData;
    }

    /***
     * todo 小程序审核成功后，借助队列自动发布
     * @param $id
     * @author jonzhang
     * @date 2018-05-29
     * @update 陈文豪 2018年07月10日 修改为ID提交
     * @update 梅杰 2019年10月12日 09:55:11 日志bug
     */
    public function autoRelease($id)
    {
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($id);
        if ($row['errCode'] != 0) {
            return;
        } else if ($row['errCode'] == 0) {
            if (empty($row['data'])) {
                BLogger::getLogger('info')->info('该店铺没有查询到小程序,wid为:' . $id);
                return;
            }
            if (isset($row['data']['status']) && $row['data']['status'] != 4) {
                return;
            }
        }
        $wid = $row['data']['wid'];

        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
            [$wid, $row['data']['app_id'] ?? '', 12, '提交发布', time(), $row['data']['template_id'], 999999, 'system']);

        $postUrl = 'https://api.weixin.qq.com/wxa/release?access_token=' . $this->_getToken($row['data']['id']);
        $result = $this->curlJson($postUrl, '{}');

        if ($result['errcode'] == 0) {
            // add 何书哲 2020年03月11日 获取最近一次上传代码成功的直播状态，并更新发布小程序的直播状态
            $update = [
                'status' => 5,
                'status_time' => time(),
            ];
            $latestCommitLog = WXXCXConfigOperateLog::where('wid', $wid)
                ->where('appid', $row['data']['app_id'] ?? '')
                ->where('action', 3)
                ->orderBy('id', 'desc')
                ->first(['id', 'live_status']);
            if (!empty($latestCommitLog)) {
                $update['online_live_status'] = data_get($latestCommitLog, 'live_status', 0);
            }
            $xcxConfigService->updateData($row['data']['id'], $update);
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
                [$wid, $row['data']['app_id'] ?? '', 11, '发布成功', time(), $row['data']['template_id'], 999999, 'system']);
        } else {
            $strMsg = '错误码:' . $result['errcode'] ?? '';
            $strMsg = $strMsg . ',错误信息:' . $result['errmsg'] ?? '';
            BLogger::getLogger('info')->info('自动发布失败:' . $strMsg);
        }
    }

    /**
     * todo 批量上传小程序
     * @author jonzhang
     * @date 2018-06-07
     * @upadte 陈文豪 2018年07月10日 修改为ID提交
     */
    public function batchCommit($num = 50)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $i = 0;
        $commitFailureArr = [];
        $xcxConfig = new WXXCXConfigService();
        //status为5 表示已发布的小程序 current_status 为0 表示有效的小程序
        $xcxDataList = $xcxConfig->getListByCondition(['status' => 5, 'current_status' => 0], '', '', $num);
        if ($xcxDataList['data']) {
            $xcxOnlineData = $this->getXCXOnLine();
            foreach ($xcxDataList['data'] as $item) {
                if ($xcxOnlineData['errCode'] == 0 && !empty($xcxOnlineData['data'])) {
                    $xcxOnline = $xcxOnlineData['data'];
                    $xcxSyncFooterBarService = new WXXCXFooterBarService();
                    $syncFooterList = $xcxSyncFooterBarService->getAllList($item['wid'], [], 'order');
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
                    $result = $this->commitForBatch($item['id'], $xcxOnline['template_id'], $xcxOnline['user_version'], $xcxOnline['user_desc'], $barList, false, [], 999999, 'system');
                    if ($result['errCode'] == 0) {
                        $i++;
                    } else {
                        $commitFailureArr[] = $result;
                    }
                }
            }
        }
        if (count($commitFailureArr) > 0) {
            BLogger::getLogger('error')->error('batchcommit:' . json_encode($commitFailureArr));
        }
        $returnData['errMsg'] = '批量上传成功了' . $i . '条数据';
        return $returnData;
    }

    /***
     * todo 上传代码 批量使用
     * @param $wid
     * @param $template_id
     * @param $version
     * @param $desc
     * @param array $barList
     * @param bool $is_auth
     * @param array $itemList
     * @param int $operatorId
     * @param string $operator
     * @return array
     * @author jonzhang
     * @date 2018-06-07
     * @upadte 陈文豪 2018年07月10日 修改为ID提交
     */
    public function commitForBatch($xcxid, $template_id, $version, $desc, $barList = [], $is_auth = false, $itemList = [], $operatorId = 0, $operator = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxid);
        if ($row['errCode'] != 0) {
            $returnData['errCode'] = -201;
            $returnData['errMsg'] = $row['errMsg'];
            return $returnData;
        }

        if (empty($row['data']['request_domain'])) {
            $returnData['errCode'] = -202;
            $returnData['errMsg'] = '请设置域名';
            return $returnData;
        }
        $wid = $row['data']['wid'];
        $postUrl = 'https://api.weixin.qq.com/wxa/commit?access_token=' . $this->_getToken($row['data']['id']);
        //add by jonzhang 2018-03-07 默认读取小程序底部导航数据
        $tabBar = [];
        //通过后台上传代码 $barList为空
        if (empty($barList)) {
            list($list, $pageHtml) = (new WXXCXSyncFooterBarService())->getAllList($wid, [], 'order');
            if (!empty($list['data'])) {
                $toolBarList = [];
                foreach ($list['data'] as $key => $value) {
                    $value['iconPath'] = $value['icon_path'];
                    $value['selectedIconPath'] = $value['selected_path'];
                    $value['text'] = $value['name'];
                    $value['pagePath'] = $value['page_path'];
                    $iconPathArr = explode('mctsource/', $value['iconPath']);
                    $selectedPathArr = explode('mctsource/', $value['selectedIconPath']);
                    $toolBarList[$key]['text'] = $value['text'];
                    $toolBarList[$key]['pagePath'] = $value['pagePath'];
                    $toolBarList[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
                    $toolBarList[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
                }
                $tabBar['selectedColor'] = '#b1292d';
                $tabBar['list'] = $toolBarList;
                $tabBar['backgroundColor'] = '#fff';
                $tabBar['borderStyle'] = 'black';
            }
        }
        /**默认小程序头部设置 add by wuxiaoping 2018.05.07 begin**/
        $returnData = $this->processMainHome($wid);
        /*$windowData['navigationBarBackgroundColor'] = "#000000";
        $windowData['navigationBarTextStyle'] = "white";
        $windowData['backgroundColor'] = "#ffffff";
        $windowData['backgroundTextStyle'] = "light";*/
        $windowData = [];
        // 自定义导航标题背景色
        if (isset($returnData['data']['bgColor']) && !empty($returnData['data']['bgColor'])) {
            $windowData['navigationBarBackgroundColor'] = $returnData['data']['bgColor'];
        }
        /**end**/

        // 定义请求数据
        $postData['template_id'] = $template_id;
        $postData['ext_json'] = array(
            'extAppid' => $row['data']['app_id'],
            'ext' => array(
                'domain' => $row['data']['request_domain'],
                'wid' => $wid,
                'wxxcxConfigId' => $xcxid,
            ),
            'window' => $windowData,
            'tabBar' => $tabBar
        );
        //添加配置文件生成底部导航数据 add by wuxiaoping 2017.12.14
        if ($barList) {
            /*$pagesList = json_decode($row['data']['page_list'],true);
            foreach ($barList['list'] as $key => $value) {
                $pages[] = $value['pagePath'];
            }
            $postData['ext_json']['pages'] = !empty($pagesList) ? $pagesList : $pages;*/
            $postData['ext_json']['tabBar'] = $barList;

        }
        $postData['ext_json'] = $this->_jsonUnescapedUnicode($postData['ext_json']);
        $postData['user_version'] = $version;
        $postData['user_desc'] = $desc;
        //$result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //$result = json_decode($result, true);
        $remark = "";
        if (!empty($postData))
            $remark = json_encode($postData);
        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator,remark) values (?,?,?,?,?,?,?,?,?)',
            [$wid, $row['data']['app_id'] ?? '', 4, '上传代码', time(), $template_id, $operatorId, $operator, $remark]);
        //JSON_UNESCAPED_UNICODE 中文疑难杂症解决方案
        $result = $this->curlJson($postUrl, json_encode($postData, JSON_UNESCAPED_UNICODE));

        if ($result['errcode'] == 0) {
            $xcxConfigService->updateData($row['data']['id'], ['status' => 1, 'status_time' => time(), 'version' => $version, 'version_desc' => $desc, 'template_id' => $template_id]);
            DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
                [$wid, $row['data']['app_id'] ?? '', 3, '上传代码成功', time(), $template_id, $operatorId, $operator]);
        } else {
            $returnData['errCode'] = -203;
            $returnData['errMsg'] = $result['errmsg'];
        }
        return $returnData;
    }

    /**
     * todo 批量提交审核
     * @author jonzhang
     * @date 2018-06-07
     * @upadte 陈文豪 2018年07月10日 修改为ID提交
     */
    public function batchsubmitAudit($num = 20)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $i = 0;
        $j = 0;
        //提交审核时异常数据
        $errorArr = [];
        //提交审核后,审核失败的数据
        $auditFailureArr = [];
        $xcxConfig = new WXXCXConfigService();
        //status为1 表示已经上传的小程序 current_status 为0 表示有效的小程序
        $xcxDataList = $xcxConfig->getListByCondition(['status' => 1, 'current_status' => 0], '', '', $num);
        if ($xcxDataList['data']) {
            foreach ($xcxDataList['data'] as $item) {
                $j++;
                //剔除猛犸小程序
                if ($item['id'] == 22) {
                    continue;
                }
                if (!empty($item['submit_audit_item_list']) && !empty($item['request_domain']) && !empty($item['title']) && !empty($item['page_list']) && !empty($item['category_list'])) {
                    $result = $this->submitAuditForBatch($item['id'], $item['submit_audit_item_list'], 999999, 'system');
                    if ($result['errCode'] == 0) {
                        $i++;
                    } else {
                        $auditFailureArr[] = $result;
                    }
                } else {
                    $errorArr[] = $item;
                }
            }
        }
        if (count($errorArr) > 0) {
            BLogger::getLogger('error')->error('batchSubmitAudit error data:' . json_encode($errorArr));
        }
        if (count($auditFailureArr) > 0) {
            BLogger::getLogger('error')->error('batchSubmitAudit failure data:' . json_encode($auditFailureArr));
        }
        $returnData['errMsg'] = '共查询出' . $j . '条数据,提交审核成功了' . $i . '条数据';
        return $returnData;
    }

    /**
     * todo 提交审核批量使用
     * @param $xcxid
     * @param $list
     * @param int $operatorId
     * @param string $operator
     * @author jonzhang
     * @date 2018-06-07
     * @upadte 陈文豪 2018年07月10日 修改为ID提交
     */
    public function submitAuditForBatch($xcxid, $list, $operatorId = 0, $operator = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxid);
        if ($row['errCode'] != 0) {
            $returnData['errCode'] = -201;
            $returnData['errMsg'] = $row['errMsg'];
            return $returnData;
        }
        $wid = $row['data']['wid'];

        DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
            [$wid, $row['data']['app_id'] ?? '', 10, '提交审核', time(), $row['data']['template_id'], $operatorId, $operator]);

        $postUrl = 'https://api.weixin.qq.com/wxa/submit_audit?access_token=' . $this->_getToken($row['data']['id']);
        $postData['item_list'] = $list;
        //$result = CurlBuilder::to($postUrl)->asJsonRequest()->withData($postData)->post();
        //$result = json_decode($result, true);

        //JSON_UNESCAPED_UNICODE 中文疑难杂症解决方案
        $result = $this->curlJson($postUrl, json_encode($postData, JSON_UNESCAPED_UNICODE));

        //返回处理
        switch ($result['errcode']) {
            case 0:
                /*总后台提交审核成功后，把底部导航栏的修改同步到微信小程序 begin add by wuxiaoping 2018.04.28*/
                $xcxFooterBarService = new WXXCXFooterBarService();
                $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
                $localBarData = $xcxFooterBarService->getAllList($wid);
                $syncBarData = $xcxSyncFooterBarService->getAllList($wid);
                //同步底部导航栏 hsz 2018.05.25 begin
                if (isset($syncBarData[0]['data']) && $syncBarData[0]['data']) {
                    foreach ($syncBarData[0]['data'] as $key => $value) {
                        $xcxSyncFooterBarService->del($value['id']);
                    }
                }
                if (isset($localBarData[0]['data']) && $localBarData[0]['data']) {
                    foreach ($localBarData[0]['data'] as $ikey => $item) {
                        $saveData['wid'] = $item['wid'];
                        $saveData['name'] = $item['name'];
                        $saveData['page_path'] = $item['page_path'];
                        $saveData['icon_path'] = $item['icon_path'];
                        $saveData['selected_path'] = $item['selected_path'];
                        $saveData['order'] = $item['order'];
                        $saveData['page_id'] = $item['page_id'];
                        $saveData['url_title'] = $item['url_title'];
                        $saveData['is_sync_weixin'] = $item['is_sync_weixin'];
                        $saveData['is_can_revise_url'] = $item['is_can_revise_url'];
                        $xcxSyncFooterBarService->add($saveData);
                    }
                }
                //同步底部导航栏  hsz end
                /*
                if ((isset($localBarData[0]['data']) && $localBarData[0]['data']) && (isset($syncBarData[0]['data']) && $syncBarData[0]['data'])) {
                    if (count($localBarData[0]['data']) <> count($syncBarData[0]['data'])) {
                        foreach ($syncBarData[0]['data'] as $key => $value) {
                            $res = $xcxSyncFooterBarService->del($value['id']);
                        }
                    }
                    foreach ($localBarData[0]['data'] as $ikey =>$item) {
                        $saveData['wid']               = $item['wid'];
                        $saveData['name']              = $item['name'];
                        $saveData['page_path']         = $item['page_path'];
                        $saveData['icon_path']         = $item['icon_path'];
                        $saveData['selected_path']     = $item['selected_path'];
                        $saveData['order']             = $item['order'];
                        $saveData['page_id']           = $item['page_id'];
                        $saveData['url_title']         = $item['url_title'];
                        $saveData['is_sync_weixin']    = $item['is_sync_weixin'];
                        $saveData['is_can_revise_url'] = $item['is_can_revise_url'];
                        $xcxSyncFooterBarService->add($saveData);
                    }
                }
                */
                /****end****/
                $xcxConfigService->updateData($row['data']['id'], ['status' => 2, 'status_time' => time()]);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,template_id,operator_id,operator) values (?,?,?,?,?,?,?,?)',
                    [$wid, $row['data']['app_id'] ?? '', 9, '提交审核成功', time(), $row['data']['template_id'], $operatorId, $operator]);
                break;
            case 85009:
                $xcxConfigService->updateData($row['data']['id'], ['status' => 2, 'status_time' => time()]);
                DB::insert('insert into ds_wxxcx_config_operate_log (wid,appid,action,action_name,create_time,operator_id,operator) values (?,?,?,?,?,?,?)',
                    [$wid, $row['data']['app_id'] ?? '', 9, '已经有正在审核的版本', time(), $operatorId, $operator]);
                $returnData['errCode'] = -202;
                $returnData['errMsg'] = "有正在审核的版本";
                break;
            default:
                $returnData['errCode'] = -203;
                $returnData['errMsg'] = $result['errmsg'];
                break;
        }
        return $returnData;
    }


    /**
     * 更新小程序审核的最新状态
     * @param array $data
     * @return array
     */
    public function getLatestAuditStatusByXcxId($data = [])
    {
        if (empty($data)) {
            error('查询条件为空');
        }
        $thirdPlatForm = new ThirdPlatform();
        $authorizerData = $thirdPlatForm->getAuthorizerAccessToken($data);
        if ($authorizerData['errCode'] != 0 || empty($authorizerData['data'])) {
            error($authorizerData['errMsg']);
        }
        $postUrl = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=%s';
        $postUrl = sprintf($postUrl, $authorizerData['data']);
        $jsonData = CurlBuilder::to($postUrl)->asJsonRequest()->get();
        $jsonData = json_decode($jsonData, true);
        if (!isset($jsonData['errcode']) || isset($jsonData['errcode']) && $jsonData['errcode'] != 0) {
            error('错误码：' . $jsonData['errcode'] . ',错误信息：' . $jsonData['errmsg']);
        }
        BLogger::getLogger('info')->info('小程序更新审核状态：latest:' . json_encode($jsonData));
        //审核状态，其中0为审核成功，1为审核失败，2为审核中
        //0未上传 1已上传代码 2审核中 3审核被拒 4审核成功 5已发布 6取消审核 7已作废 8已下架
        $xcxConfigService = new WXXCXConfigService();
        $xcxConfigData = $xcxConfigService->getRowById($data['id']);
        BLogger::getLogger('info')->info('小程序更新审核状态：config:' . json_encode($xcxConfigData));
        if (!(in_array($xcxConfigData['data']['status'], [2, 3, 4]) || $xcxConfigData['data']['status'] == 5 && $jsonData['status'] != 0)) {
            error('已更新到最新状态');
        }
        $status = 0;
        if ($jsonData['status'] == 2) {
            $status = 2;
        } elseif ($jsonData['status'] == 1) {
            $status = 3;
        } else {
            $status = 4;
        }
        BLogger::getLogger('info')->info('小程序更新审核状态：status:' . $status);
    }

    /**
     * 处理错误提示
     * @param array $result 结果参数数组
     * @return string 错误提示
     * @update 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 11:21:56 添加错误状态提示
     */
    private function dealWeCodeMessage(array $result = [])
    {
        $message = $result['errcode'] . ' ' . $result['errmsg'];

        switch ($result['errcode']) {
            case -80082:
                $message = '暂时没有对应插件权限';
                break;
            case -1:
                $message = '系统繁忙';
                break;
            case 61007:
                $message = '小程序授权平台太多或者开放平台权限不够';
                break;
            case 85001:
                $message = '微信号不存在或微信号设置为不可搜索';
                break;
            case 85002:
                $message = '小程序绑定的体验者数量达到上限';
                break;
            case 85003:
                $message = '微信号绑定的小程序体验者达到上限';
                break;
            case 85004:
                $message = '微信号已经绑定';
                break;
            case 85006:
                $message = '标签格式错误';
                break;
            case 85007:
                $message = '页面路径错误';
                break;
            case 85008:
                $message = '类目填写错误';
                break;
            case 85009:
                $message = '已经有正在审核的版本';
                break;
            case 85010:
                $message = 'item_list有项目为空';
                break;
            case 85011:
                $message = '标题填写错误';
                break;
            case 85013:
                $message = '无效的自定义配置';
                break;
            case 85014:
                $message = '无效的模版编号';
                break;
            case 85015:
                $message = '该账号不是小程序账号';
                break;
            case 85016:
                $message = '域名数量超过限制';
                break;
            case 85017:
                $message = '没有新增域名，请确认小程序已经添加了域名或该域名是否没有在第三方平台添加';
                break;
            case 85018:
                $message = '域名没有在第三方平台设置';
                break;
            case 85019:
                $message = '没有审核版本';
                break;
            case 85020:
                $message = '审核状态未满足发布';
                break;
            case 85023:
                $message = '审核列表填写的项目数不在1-5以内';
                break;
            case 85043:
                $message = '模版错误';
                break;
            case 85044:
                $message = '代码包超过大小限制';
                break;
            case 85045:
                $message = 'ext_json有不存在的路径';
                break;
            case 85046:
                $message = 'tabBar中缺少path';
                break;
            case 85047:
                $message = 'pages字段为空';
                break;
            case 85048:
                $message = 'ext_json解析失败';
                break;
            case 85064:
                $message = '找不到模版';
                break;
            case 85065:
                $message = '模版库已满';
                break;
            case 85077:
                $message = '小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）';
                break;
            case 85085:
                $message = '近7天提交审核的小程序数量过多，请耐心等待审核完毕后再次提交';
                break;
            case 85086:
                $message = '提交代码审核之前需提前上传代码';
                break;
            case 85087:
                $message = '小程序已使用api navigateToMiniProgram，请声明跳转appid列表后再次提交';
                break;
            case 85109:
                $message = '没有审核版本';
                break;
            case 86000:
                $message = '不是由第三方代小程序进行调用';
                break;
            case 86001:
                $message = '不存在第三方的已经提交的代码';
                break;
            case 86002:
                $message = '小程序还未设置昵称、头像、简介。请先设置完后再重新提交';
                break;
            case 87013:
                $message = '每个小程序每天可撤销审核一次，一个月最多撤回10次';
                break;
            case 89019:
                $message = '业务域名无更改，无需重复设置';
                break;
            case 89020:
                $message = '尚未设置小程序业务域名，请先在第三方平台中设置小程序业务域名后在调用本接口';
                break;
            case 89021:
                $message = '请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名';
                break;
            case 89029:
                $message = '业务域名数量超过限制';
                break;
            case 89231:
                $message = '个人小程序不支持调用setwebviewdomain接口';
                break;
            case 89236:
                $message = '该插件不能申请';
                break;
            case 89237:
                $message = '已经添加该插件';
                break;
            case 89238:
                $message = '申请或使用的插件已经达到上限';
                break;
            case 89239:
                $message = '该插件不存在';
                break;
            case 89243:
                $message = '该申请为“待确认”状态，不可删除';
                break;
            case 89244:
                $message = '不存在该插件 appid';
                break;
            case 89256:
                $message = 'token 信息有误';
                break;
            case 89257:
                $message = '该插件版本不支持快速更新';
                break;
            case 89258:
                $message = '当前小程序帐号存在灰度发布中的版本，不可操作快速更新';
                break;
            case 61007:
                $message = '小程序授权平台太多或者开放平台权限不够';
                break;
            case 89401:
                $message = '系统不稳定，请稍后再试，如多次失败请通过社区反馈';
                break;
            case 89402:
                $message = '该审核单不在待审核队列，请检查是否已提交审核或已审完';
                break;
            case 89403:
                $message = '本单属于平台不支持加急种类，请等待正常审核流程';
                break;
            case 89404:
                $message = '本单已加速成功，请勿重复提交';
                break;
            case 89405:
                $message = '本月加急额度不足，请提升提审质量以获取更多额度';
                break;

            default:
                break;
        }

        return $message;
    }

    /**
     * 查询服务商的当月提审限额（quota）和加急次数
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月09日 20:56:38
     */
    public function queryQuota()
    {
        // 筛选小程序
        $app = WXXCXConfig::where('current_status', 0)->orderBy('status_time', 'desc')->orderBy('status', 'desc')->select(['id'])->first();
        if (empty($app)) {
            error('还未绑定可用小程序');
        }

        // 拼接url
        $url = self::QUERY_QUOTA . $this->_getToken($app['id']);
        $result = $this->curlJson($url);

        if ($result['errcode'] == 0) {
            // 查询成功
            success('', '', [
                'rest' => $result['rest'],
                'limit' => $result['limit'],
                'speedup_rest' => $result['speedup_rest'],
                'speedup_limit' => $result['speedup_limit'],
            ]);
        } else {
            // 查询失败
            error($this->dealWeCodeMessage($result));
        }
    }

    /**
     * @desc 获取直播间数据
     * @param int $wid
     * @param int $start
     * @param int $limit
     * @return mixed
     * @throws Exception
     * @throws \App\Exceptions\CommonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 焦建荣【945184949@qq.com】2020年03月06日
     *
     * @update 梅杰 at 2020-03-28 10:18:42 增加企业筛选
     */
    public function liveRoom(int $wid, $start = 0, $limit = 15)
    {
        // 筛选小程序
        $app = WXXCXConfig::query()->where('current_status', 0)
            ->where('wid', $wid)
            ->orderBy('status_time', 'desc')
            ->orderBy('status', 'desc')
            ->select(['id'])
            ->first();

        if (empty($app)) {
            error('还未绑定可用小程序');
        }

        $liveRoomUrl = self::LIVE_ROOM . $this->_getToken($app['id']);

        $response = (new \GuzzleHttp\Client())->request('post', $liveRoomUrl, ['json' => ['start' => $start, 'limit' => $limit]]);

        if (empty($response)) {
            // 查询失败
            error($this->dealWeCodeMessage(['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服']));
        }

        $data = json_decode($response->getBody(), true);

        if (isset($data['errcode']) && 1 == $data['errcode']) {
            $data['total'] = 0;
            return $data;
        }

        if (isset($data['errcode']) && 0 == $data['errcode']) {
            return $data;
        }

        \Log::info('error' . 'code:' . $data['errcode'] . ' errmsg:' . $data['errmsg']);

        throw new Exception('未授权', 41026);
    }

    /**
     * 加急审核申请
     * @param $xcxId 小程序id
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 11:13:54
     */
    public function speedUpAudit($xcxId)
    {
        $app = WXXCXConfig::where('id', $xcxId)->where('current_status', 0)->select(['audit_id'])->first();

        if (empty($app)) {
            error('还未绑定可用小程序');
        }
        if (empty($app['audit_id'])) {
            error('提审限额功能上线后还未提交审核，不能申请加急审核');
        }

        $staffOperService = new StaffOperLogService();
        $postUrl = self::SPEED_UP_AUDIT . $this->_getToken($xcxId);
        $postData = ['auditid' => $app['audit_id']];
        $result = $this->curlJson($postUrl, json_encode($postData, JSON_UNESCAPED_UNICODE));

        if ($result['errcode'] == 0) {
            // 加急成功
            $staffOperService->write('加急审核申请成功', 23, $xcxId);
            success('加急审核申请成功');
        } else {
            // 加急失败
            $message = $this->dealWeCodeMessage($result);
            $staffOperService->write('加急审核申请失败：' . $message, 23, $xcxId);
            error($message);
        }
    }

    /**
     * 已添加插件列表
     * @param $xcxId 小程序主键id
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:19:53
     */
    public function pluginList($xcxId)
    {
        $app = WXXCXConfig::where('id', $xcxId)->where('current_status', 0)->first(['id']);
        if (empty($app)) {
            error('还未绑定可用小程序');
        }
        // 拼接url
        $url = self::PLUGIN_BASIC . $this->_getToken($app['id']);
        $response = (new \GuzzleHttp\Client())->request('post', $url, ['json' => ['action' => 'list']]);
        if (empty($response)) {
            // 查询失败
            error($this->dealWeCodeMessage(['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服']));
        }
        $result = json_decode($response->getBody(), true);

        $staffOperLogService = new StaffOperLogService();

        if ($result['errcode'] == 0) {
            // 获取已添加插件列表成功
            // 添加总后台操作日志
            $staffOperLogService->write('操作成功', 24, $xcxId);
            return $result['plugin_list'];
        } else {
            // 获取已添加插件列表失败
            // 添加总后台操作日志
            $staffOperLogService->write('操作失败：' . json_encode($result), 24, $xcxId);
            $message = $this->dealWeCodeMessage($result);
            error($message);
        }
    }

    /**
     * 申请添加插件
     * @param $xcxId 小程序主键id
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:27:09
     */
    public function pluginApply($xcxId)
    {
        $app = WXXCXConfig::where('id', $xcxId)->where('current_status', 0)->first(['id']);
        if (empty($app)) {
            error('还未绑定可用小程序');
        }
        // 拼接url
        $url = self::PLUGIN_BASIC . $this->_getToken($app['id']);
        $data = [
            'action' => 'apply',
            'plugin_appid' => 'wx2b03c6e691cd7370'
        ];
        $response = (new \GuzzleHttp\Client())->request('post', $url, ['json' => $data]);
        if (empty($response)) {
            // 查询失败
            error($this->dealWeCodeMessage(['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服']));
        }
        $result = json_decode($response->getBody(), true);

        $staffOperLogService = new StaffOperLogService();

        if ($result['errcode'] == 0) {
            // 申请添加插件成功
            // 添加总后台操作日志
            $staffOperLogService->write('操作成功：' . json_encode($data), 25, $xcxId);
            return true;
        } else {
            // 申请添加失败
            // 添加总后台操作日志
            $staffOperLogService->write('操作失败：' . json_encode($result), 25, $xcxId);
            $message = $this->dealWeCodeMessage($result);
            error($message);
        }
    }

    /**
     * 更新插件版本
     * @param $xcxId 小程序主键id
     * @param $pluginAppId 插件appid
     * @param $userVersion 插件版本号
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:45:40
     */
    public function pluginUpdate($xcxId, $pluginAppId, $userVersion)
    {
        $app = WXXCXConfig::where('id', $xcxId)->where('current_status', 0)->first(['id']);
        if (empty($app)) {
            error('还未绑定可用小程序');
        }
        // 拼接url
        $url = self::PLUGIN_BASIC . $this->_getToken($app['id']);
        $data = [
            'action' => 'update',
            'plugin_appid' => $pluginAppId,
            'user_version' => $userVersion
        ];
        $response = (new \GuzzleHttp\Client())->request('post', $url, ['json' => $data]);
        if (empty($response)) {
            // 查询失败
            error($this->dealWeCodeMessage(['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服']));
        }
        $result = json_decode($response->getBody(), true);

        $staffOperLogService = new StaffOperLogService();

        if ($result['errcode'] == 0) {
            // 更新插件版本成功
            // 添加总后台操作日志
            $staffOperLogService->write('操作成功：' . json_encode($data), 26, $xcxId);
            return true;
        } else {
            // 更新插件版本失败
            // 添加总后台操作日志
            $staffOperLogService->write('操作失败：' . json_encode($result), 26, $xcxId);
            $message = $this->dealWeCodeMessage($result);
            error($message);
        }
    }

    /**
     * 删除插件
     * @param $xcxId 小程序主键id
     * @param $pluginAppId 插件appid
     * @return mixed
     * @throws \App\Exceptions\CommonException
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2020年03月11日 20:51:28
     */
    public function pluginUnbind($xcxId, $pluginAppId)
    {
        $app = WXXCXConfig::where('id', $xcxId)->where('current_status', 0)->first(['id']);
        if (empty($app)) {
            error('还未绑定可用小程序');
        }
        // 拼接url
        $url = self::PLUGIN_BASIC . $this->_getToken($app['id']);
        $data = [
            'action' => 'unbind',
            'plugin_appid' => $pluginAppId,
        ];
        $response = (new \GuzzleHttp\Client())->request('post', $url, ['json' => $data]);
        if (empty($response)) {
            // 查询失败
            error($this->dealWeCodeMessage(['errcode' => 10000, 'errmsg' => '访问请求错误，请联系客服']));
        }
        $result = json_decode($response->getBody(), true);

        $staffOperLogService = new StaffOperLogService();

        if ($result['errcode'] == 0) {
            // 删除插件成功
            // 添加总后台操作日志
            $staffOperLogService->write('操作成功：' . json_encode($data), 27, $xcxId);
            return true;
        } else {
            // 删除插件成功失败
            // 添加总后台操作日志
            $staffOperLogService->write('操作失败：' . json_encode($result), 27, $xcxId);
            $message = $this->dealWeCodeMessage($result);
            error($message);
        }
    }

}

