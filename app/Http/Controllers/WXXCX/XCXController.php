<?php

namespace App\Http\Controllers\WXXCX;

use App\Http\Controllers\Controller;
use App\Jobs\LoginStatistics;
use App\Jobs\SubMsgPushJob;
use App\Lib\BLogger;
use App\Lib\Redis\NewUserFlagRedis;
use App\Lib\Redis\SMSKeys;
use App\Lib\WXXCX\ThirdPlatform;
use App\Lib\WXXCX\WXXCXHelper;
use App\Model\Member;
use App\Model\WeixinConfigSub;
use App\Module\BindMobileModule;
use App\Module\CodeModule;
use App\Module\MemberModule;
use App\Module\MessagePushModule;
use App\Module\StoreModule;
use App\Module\XCXModule;
use App\S\File\FileInfoService;
use App\S\Foundation\VerifyCodeService;
use App\S\MarketTools\MessagesPushService;
use App\S\Weixin\ShopService;
use App\S\WXXCX\SubscribeMessagePushService;
use App\S\WXXCX\WXXCXConfigService;
use App\S\WXXCX\WXXCXSyncFooterBarService;
use App\S\WXXCX\WXXCXTopNavService;
use App\Services\Permission\WeixinRoleService;
use App\Services\WeixinService;
use CommonModule;
use Illuminate\Http\Request;
use Log;
use MemberService;
use Validator;
use WXXCXCache;
use WXXCXMicroPageService;

/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/8
 * Time: 16:25
 */
class XCXController extends Controller
{
    /**
     * todo 登录
     * @param Request $request
     * @return mixed
     * @author add by jonzhang
     * @date 2017-08-08
     * @update 何书哲 2018年6月27日 返回昵称/头像/店铺id
     * @update 张永辉 2018年7月9日 通过id查询不同的小程序配置信息并写入到token信息里
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     * @update 何书哲 2018年9月21日 返回底部logo跳转链接
     * @update 何书哲 2018年9月25日 返回底部自定义logo
     * @update 梅杰 2018年9月29日 小程序配置已失效判断
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月10日 返回流量主参数
     */
    public function checkLogin(Request $request, WXXCXConfigService $wXXCXConfigService, ShopService $shopService)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        try {
            $wid = $request->input('wid');
            $code = $request->input('code');
            $userInfo = $request->input('user_info');
            $wxxcxConfigId = $request->input('wxxcxConfigId', '0');
            $errMsg = '';
            if (empty($code)) {
                $errMsg .= 'code不存在';
            }
            if (empty($wid)) {
                $errMsg .= '店铺id不存在';
            }
            if (empty($userInfo)) {
                $errMsg .= '用户信息不存在';
            }
            if (strlen($errMsg) > 0) {
                $returnData['code'] = -1000;
                $returnData['hint'] = $errMsg;
                return $returnData;
            }
            //add by jonzhang 2018-01-11 验证店铺是否过期
            $expireData = (new WeixinRoleService())->isExpire($wid);
            if ($expireData['errCode'] < 0 || ($expireData['errCode'] == 0 && $expireData['data'] == 1)) {
                $returnData['code'] = 40005;
                $returnData['hint'] = '店铺过期';
                return $returnData;
            }

            $userInfo = json_decode($userInfo, true);
            if (empty($userInfo)) {
                $returnData['code'] = -1001;
                $returnData['hint'] = '数据格式转化出现问题';
                return $returnData;
            }//获取该店铺下的小程序配置信息
            if (!empty($wxxcxConfigId)) {
                $wxxcxConfigId == 1547 && $wxxcxConfigId = 1642; //线上临时修改 梅杰 2018年9月27
                $xcxConfig = $wXXCXConfigService->getRowById($wxxcxConfigId);
            } else {
                $xcxConfig = $wXXCXConfigService->getRow($wid);
                $wxxcxConfigId = $xcxConfig['data']['id'] ?? '0';
            }
            if ($xcxConfig['errCode'] != 0) {
                $returnData['code'] = $xcxConfig['errCode'];
                $returnData['hint'] = $xcxConfig['errMsg'];
                return $returnData;
            } else if ($xcxConfig['errCode'] == 0 && empty($xcxConfig['data'])) {
                $returnData['code'] = -1004;
                $returnData['hint'] = '小程序配置信息不存在';
                return $returnData;
            }

            if ($xcxConfig['data']['current_status'] == -1) {

                $returnData['code'] = 40006;
                $returnData['hint'] = '小程序配置信息已失效';
                return $returnData;
            }


            $appId = $xcxConfig['data']['app_id'];
            $appSecret = $xcxConfig['data']['app_secret'];
            if (empty($appId) || empty($appSecret)) {
                $returnData['code'] = -1005;
                $returnData['hint'] = 'app_id为空或app_secret为空';
                return $returnData;
            }
            $config = [
                'appid'            => $appId,
                'secret'           => $appSecret,
                'code2session_url' => "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code"
            ];
            $xcx = new WXXCXHelper($config);
            //获取openid
            $result = $xcx->getLoginInfo($code);
            if ($result['code'] == 0 && !empty($result['data'])) {
                $openId = $result['data']['openid'];
                $unionid = $result['data']['unionid'] ?? '';//add by zhangyh 20180206
                $id = 0;
                $shopData = $shopService->getRowById($wid);
                //获取用户信息 source数据库保存的是6，因为查询条件的原因此处传7
                $xcxUser = MemberService::getListByConditionWithPage(['wid' => $wid, 'xcx_openid' => $openId]);
                //用户数据不存在
                if (empty($xcxUser[0]['data'][0])) {
                    $data['wid'] = $wid;
                    $data['openid'] = '';
                    $data['xcx_openid'] = $openId;
                    $data['unionid'] = $unionid;//add by zhangyh
                    $data['nickname'] = $userInfo['nickName'] ?? '';
                    $data['truename'] = $data['nickname'];
                    $data['headimgurl'] = $userInfo['avatarUrl'] ?? '';
                    $data['sex'] = $userInfo['gender'] ?? 0;
                    $data['province'] = $userInfo['province'] ?? '';
                    $data['city'] = $userInfo['city'] ?? '';
                    $data['country'] = $userInfo['country'] ?? '';
                    $data['source'] = 6;
                    //处理并发 add by jonzhang 2018-01-09
                    $memberModule = new MemberModule();
                    try {
                        if ($memberModule->memberCheck($wid, $openId)) {
                            //添加用户数据
                            $userData = MemberService::insertData($data);
                            if ($userData['errCode'] == 0 && !empty($userData['data'])) {
                                $id = $userData['data'];
                            }
                        }
                    } catch (\Exception $e) {
                        BLogger::getLogger('error')->error('用户数据出现相同数据:' . $e->getMessage());
                        $eUser = MemberService::getListByConditionWithPage(['wid' => $wid, 'xcx_openid' => $openId]);
                        $id = $eUser[0]['data'][0]['id'] ?? 0;
                        //add by jonzhang 处理ds_member_check表存在 而ds_member用户信息表不存在的逻辑 2018-02-01
                        if (empty($id)) {
                            $userData = MemberService::insertData($data);
                            if ($userData['errCode'] == 0 && !empty($userData['data'])) {
                                $id = $userData['data'];
                            }
                        }
                    }
                    //新用户标识
                    (new NewUserFlagRedis())->set($id);
                    //何书哲 2018年11月12日 店铺未设置门槛，新用户发送消息模板
                    if (isset($shopData['distribute_grade']) && $shopData['distribute_grade'] == 0) {
                        (new MessagePushModule($wid, MessagesPushService::BecomePromoter, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid' => $id], $wxxcxConfigId);
                    }
                }//用户数据存在
                else if (!empty($xcxUser[0]['data'][0])) {
                    $xcxUserData = $xcxUser[0]['data'][0];
                    $id = $xcxUserData['id'];
                    $updateData = [];
                    //比较数据
                    if ($userInfo['nickName'] != $xcxUserData['nickname']) {
                        $updateData['nickname'] = $userInfo['nickName'];
                    }
                    if ($userInfo['avatarUrl'] != $xcxUserData['headimgurl']) {
                        $updateData['headimgurl'] = $userInfo['avatarUrl'];
                    }
                    if ($userInfo['gender'] != $xcxUserData['sex']) {
                        $updateData['sex'] = $userInfo['gender'];
                    }
                    if ($userInfo['country'] != $xcxUserData['country']) {
                        $updateData['country'] = $userInfo['country'];
                    }
                    if ($userInfo['province'] != $xcxUserData['province']) {
                        $updateData['province'] = $userInfo['province'];
                    }
                    if ($userInfo['city'] != $xcxUserData['city']) {
                        $updateData['city'] = $userInfo['city'];
                    }
                    if ($unionid != $xcxUserData['unionid']) {
                        $updateData['unionid'] = $unionid;
                    }
                    //数据库中用户数据与最新数据不一致时，更新数据库中用户数据
                    if (!empty($updateData)) {
                        $updateData['latest_access_time'] = date("Y-m-d H:i:s", time());
                        MemberService::updateData($id, $updateData);
                    }

                    if (!empty($id)) {
                        $userAccessTime = WXXCXCache::get($id, 'mid', false);
                        if (!$userAccessTime || ($userAccessTime + 1800 < time())) {
                            WXXCXCache::set($id, time(), 'mid');
                            MemberService::updateData($id, ['latest_access_time' => date("Y-m-d H:i:s", time())]);
                        }
                    }

                } else {
                    $returnData['code'] = -1002;
                    $returnData['hint'] = '未知问题';
                    return $returnData;
                }
                //此key内网和线上使用
                $key = WXXCXHelper::randomFromDev(16);
                //此处在本地使用
                $local = env('xcx_token') ?? false;
                if ($local)
                    $key = $openId;
                $strMsg = '';
                if (empty($openId)) {
                    $strMsg .= ' openid为空';
                }
                if (empty($wid)) {
                    $strMsg .= ' wid为空';
                }
                if (empty($id)) {
                    $strMsg .= ' mid为空';
                }
                if (strlen($strMsg) > 0) {
                    $returnData['code'] = -1001;
                    $returnData['hint'] = '保存到缓存中的数据有问题:' . $strMsg;
                    return $returnData;
                }
                $value = $openId . ',' . $wid . ',' . $id . ',' . $wxxcxConfigId;
                //登录成功保存用户信息到缓存
                WXXCXCache::set($key, $value, '3rd_session');
                $returnData['list'] = $key;
                $returnData['mid'] = $id;
                //add zhangyh for 账号打通
                $returnData['isBind'] = (new BindMobileModule())->xcxIsBind($id, $wid);
                //end
                //底部导航栏数据返回 add by wuxiaoping 2017.12.18
                $returnData['barList']['pagePath'] = [];
                $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
                // update 吴晓平  2019年08月30日 会搜云小程序底部导航栏没有立即生效问题修改
                $strData = $xcxSyncFooterBarService->getSyncBarData($wid);
                if (empty($strData) || empty(json_decode($strData, true))) {
                    $barList = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
                    if (isset($barList[0]['data']) && $barList[0]['data']) {
                        foreach ($barList[0]['data'] as $value) {
                            $returnData['barList']['pagePath'][] = $value['page_path'];
                        }
                    }
                } else {
                    $syncData = json_decode($strData, true);
                    foreach ($syncData as $value) {
                        $returnData['barList']['pagePath'][] = $value['pagePath'];
                    }
                }

                //何书哲 2018年6月27日 返回昵称/头像/店铺id
                $returnData['nickName'] = $userInfo['nickName'] ?? '';
                $returnData['avatarUrl'] = $userInfo['avatarUrl'] ?? '';
                $returnData['shopId'] = $wid;
                //何书哲 2018年8月24日 返回小程序底部logo是否显示
                //$shopData = (new WeixinService())->init()->getInfo($wid);
                $returnData['is_logo_show'] = isset($shopData['is_logo_show']) ? $shopData['is_logo_show'] : 1;
                $returnData['is_logo_open'] = isset($shopData['is_logo_open']) ? $shopData['is_logo_open'] : 0;
                $returnData['logo_type'] = isset($shopData['logo_type']) ? $shopData['logo_type'] : 0;
                $returnData['logo_path'] = !empty($shopData['logo_path']) ? imgUrl() . $shopData['logo_path'] : config('app.url') . '/static/images/footer_new_logo11.png';
                $returnData['is_official_account'] = 1;
                $returnData['unit_id'] = $xcxConfig['data']['unit_id'];
                //何书哲 2018年9月19日 登录日志发送数据中心
                dispatch((new LoginStatistics($id, getIP(), 4))->onQueue('LoginStatistics'));
                return $returnData;
            } else {
                BLogger::getLogger('error')->error('获取openid出错,错误码为:' . $result['code'] . ',错误信息为:' . $result['message']);
                $returnData['code'] = -10000;
                $returnData['hint'] = '登录出现异常';
                return $returnData;
            }
        } catch (\Exception $e) {
            BLogger::getLogger('error')->error('登录时出现异常:' . $e->getMessage());
            $returnData['code'] = -10001;
            $returnData['hint'] = '登录时出现异常情况';
            return $returnData;
        }
    }


    /**
     * 新登录
     * @param Request $request
     * @param WXXCXConfigService $wXXCXConfigService
     * @param ThirdPlatform $thirdPlatform
     * @return array
     * @author: 梅杰 2018年9月27
     * @update 梅杰 2018年9月29日 小程序配置已失效判断
     * @update 何书哲 2018年10月10日 返回流量主参数
     * @update 梅杰 2018年10月24日 存放sessionKey
     */
    public function checkLoginV2(Request $request, WXXCXConfigService $wXXCXConfigService, ThirdPlatform $thirdPlatform, ShopService $shopService)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        !($wid = $request->input('wid', 0)) && xcxerror('店铺id为空');
        !($code = $request->input('code', 0)) && xcxerror('code为空');

        $expireData = (new WeixinRoleService())->isExpire($wid);
        //判断店铺是否过期
        if ($expireData['errCode'] < 0 || ($expireData['errCode'] == 0 && $expireData['data'] == 1)) {
            $returnData['code'] = 40005;
            $returnData['hint'] = '店铺过期';
            return $returnData;
        }

        //获取配置文件
        if ($configId = $request->input('wxxcxConfigId', '0')) {
            $xcxConfig = $wXXCXConfigService->getRowById($configId);
        } else {
            $xcxConfig = $wXXCXConfigService->getRow($wid);
        }

        if ($xcxConfig['errCode'] != 0) {
            $returnData['code'] = $xcxConfig['errCode'];
            $returnData['hint'] = $xcxConfig['errMsg'];
            return $returnData;
        } else if ($xcxConfig['errCode'] == 0 && empty($xcxConfig['data'])) {
            $returnData['code'] = -1004;
            $returnData['hint'] = '小程序配置信息不存在';
            return $returnData;
        }

        $configId = $xcxConfig['data']['id'];

        if ($xcxConfig['data']['current_status'] == -1) {

            $returnData['code'] = 40006;
            $returnData['hint'] = '小程序配置信息已失效';
            return $returnData;
        }


        //微信登录获取session_key和openId
        $appId = $xcxConfig['data']['app_id'];
        $loginInfo = $thirdPlatform->wxLogin($appId, $code);
        if (!$loginInfo) {
            xcxerror('微信登录失败');
        }
        $shopData = $shopService->getRowById($wid);
        //获取用户信息 source数据库保存的是6，因为查询条件的原因此处传7
        $xcxUser = MemberService::getListByConditionWithPage(['wid' => $wid, 'xcx_openid' => $loginInfo['openid']]);
        //用户数据不存在
        if (empty($xcxUser[0]['data'][0])) {
            $data['wid'] = $wid;
            $data['xcx_openid'] = $loginInfo['openid'];
            $data['unionid'] = $loginInfo['unionid'] ?? '';//add by zhangyh
            $data['source'] = 6;
            //添加用户数据
            $memberModule = new MemberModule();
            try {
                if (!$memberModule->memberCheck($wid, $loginInfo['openid'])) {
                    throw new \Exception('memberCheck 插入失败');
                }
            } catch (\Exception $exception) {
                \Log::info('memberCheck 异常' . $exception->getMessage());
            }
            $userData = MemberService::insertData($data);
            $userData['errCode'] && xcxerror('登录失败');
            $id = $userData['data'];
            (new NewUserFlagRedis())->set($id);
            //何书哲 2018年11月12日 店铺未设置门槛，新用户发送消息模板
            if (isset($shopData['distribute_grade']) && $shopData['distribute_grade'] == 0) {
                (new MessagePushModule($wid, MessagesPushService::BecomePromoter, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid' => $id], $configId);
            }

        } else {
            //老用户登录
            $xcxUserData = $xcxUser[0]['data'][0];
            $id = $xcxUserData['id'];
            $userAccessTime = WXXCXCache::get($id, 'mid', false);
            if (!$userAccessTime || ($userAccessTime + 1800 < time())) {
                WXXCXCache::set($id, time(), 'mid');
                MemberService::updateData($id, ['latest_access_time' => date("Y-m-d H:i:s", time())]);
            }
        }

        #todo  返回信息
        //此key内网和线上使用
        $key = WXXCXHelper::randomFromDev(16);
        //此处在本地使用
        env('xcx_token', 0) && $key = $loginInfo['openid'];
        $value = $loginInfo['openid'] . ',' . $wid . ',' . $id . ',' . $configId . ',' . $loginInfo['session_key'];
        //登录成功保存用户信息到缓存
        WXXCXCache::set($key, $value, '3rd_session');
        $returnData['list'] = $key;
        $returnData['mid'] = $id;
        //add zhangyh for 账号打通
        $returnData['isBind'] = (new BindMobileModule())->xcxIsBind($id, $wid);
        //end
        //底部导航栏数据返回 add by wuxiaoping 2017.12.18
        $returnData['barList']['pagePath'] = [];
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        // update 吴晓平 2019年08月30日 会搜云小程序底部导航栏没有立即生效问题修改
        $strData = $xcxSyncFooterBarService->getSyncBarData($wid);
        if (empty($strData) || empty(json_decode($strData, true))) {
            $barList = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
            if (isset($barList[0]['data']) && $barList[0]['data']) {
                foreach ($barList[0]['data'] as $value) {
                    $returnData['barList']['pagePath'][] = $value['page_path'];
                }
            }
        } else {
            $syncData = json_decode($strData, true);
            foreach ($syncData as $value) {
                $returnData['barList']['pagePath'][] = $value['pagePath'];
            }
        }

        $returnData['shopId'] = $wid;
        //何书哲 2018年8月24日 返回小程序底部logo是否显示
        $returnData['is_logo_show'] = isset($shopData['is_logo_show']) ? $shopData['is_logo_show'] : 1;
        $returnData['is_logo_open'] = isset($shopData['is_logo_open']) ? $shopData['is_logo_open'] : 0;
        $returnData['logo_type'] = isset($shopData['logo_type']) ? $shopData['logo_type'] : 0;
        $returnData['logo_path'] = !empty($shopData['logo_path']) ? imgUrl() . $shopData['logo_path'] : config('app.url') . '/static/images/footer_new_logo11.png';
        $returnData['is_official_account'] = 1;
        $returnData['unit_id'] = $xcxConfig['data']['unit_id'];
        //何书哲 2018年9月19日 登录日志发送数据中心
        dispatch((new LoginStatistics($id, getIP(), 4))->onQueue('LoginStatistics'));
        //绑定手机号码
        $pid = $request->input('_pid_', '');
        if (!empty($pid) && $pid != $id && is_numeric($pid)) {
            MemberService::bindParent($pid, $id);
        }
        return $returnData;
    }

    /**
     * 主要是之前登录小程序的用户不用重新登录页获取底部导航栏
     * （主动请求该接口，获取数据）
     * 2017.12.20  wuxiaoping
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getPageBarList(Request $request)
    {
        $wid = $request->input('wid');
        if (!$wid) {
            xcxerror('请求数据不正确');
        }
        $returnData = [];
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        $barList = $xcxSyncFooterBarService->getAllList($wid);
        if (isset($barList[0]['data']) && $barList[0]['data']) {
            foreach ($barList[0]['data'] as $key => $value) {
                $returnData[] = $value['page_path'];
            }
        }
        xcxsuccess('', $returnData);
    }


    /**
     * 此方法是测试方法
     */
    public function getText()
    {
        $config = [
            'appid'            => 'wx4f4bc4dec97d474b',
            'secret'           => 'tiihtNczf5v6AKRyjwEUhQ==',
            'code2session_url' => "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
        ];

        $xcx = new WXXCXHelper($config);

        $encryptedData = "CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';
    }

    /**
     * 上传图片
     */
    public function upload(Request $request, FileInfoService $fileInfoService)
    {
        if ($request->hasFile('file')) {
            $result = $fileInfoService->upFile($request->file('file'));
            if ($result['success'] == 1) {
                $content = [
                    'code' => 40000,
                    'hint' => '上传成功',
                    'list' => $result['data']
                ];
                echo json_encode($content);
                exit();
            } else {
                xcxerror('文件上传失败');
            }
        } else {
            xcxerror('请上传文件');
        }
    }

    /**
     * todo 小程序第三方平台 回调方法
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @author jonzhang
     * @date 2017-09-12
     */
    public function receiveEvent(Request $request, ThirdPlatform $thirdPlatform)
    {
        $thirdPlatform->receiveEvent($request);
    }

    /**
     * todo 小程序二维码扫描,回调方法
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     */
    public function sendCallBack(Request $request, ThirdPlatform $thirdPlatform)
    {
        $status = $thirdPlatform->sendCallBack($request);
        //add by wuxiaoping 2018.03.15 用于更新授权
        $type = '';
        if ($status && $status === 'updateauthorized') {
            $status = true;
            $type = 'updateauthorized';
        }
        return view('merchants.xcx.authPrompt', [
            'status' => $status,
            'type'   => $type,
        ]);
    }

    /**
     * todo 小程序上传代码审核结果回调方法
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @author jonzhang
     * @date 2017-09-28
     */
    public function receiveAudit(Request $request, ThirdPlatform $thirdPlatform, $appId)
    {
        $thirdPlatform->receiveAudit($request, $appId);
    }

    /**
     * todo 小程序店铺主页数据
     * @param Request $request
     * @param XCXModule $
     * @author jonzhagn
     * @date 2017-09-14
     */
    public function getHomePage(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => [], 'header' => ''];
        $token = $request->input('token');
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
        //add by jonzhang 2017-12-28
        $wxxcxTopNavService = new WXXCXTopNavService();
        $xcxTopNavData = $wxxcxTopNavService->getRow($wid);
        if ($xcxTopNavData['errCode'] == 0 && !empty($xcxTopNavData['data'])) {
            if ($xcxTopNavData['data']['is_on'] && !empty($xcxTopNavData['data']['template_data'])) {
                $headerData = json_decode($xcxTopNavData['data']['template_data'], true);
                $i = 1;
                foreach ($headerData as &$item) {
                    //$i==1表示店铺首页不需要重新获取数据
                    if ($i > 1) {
                        //效验数据 删除的数据不显示
                        $xcxPageData = WXXCXMicroPageService::getRowById($item['pageId']);
                        if ($xcxPageData['errCode'] < 0 || ($xcxPageData['errCode'] == 0 && empty($xcxPageData['data']))) {
                            unset($item);
                        }
                    }
                    $i++;
                }
                if (!empty($headerData)) {
                    $returnData['header'] = json_encode($headerData);
                }
            }
        }
        $xcxData = $xcxModule->processMainHome($wid);
        if ($xcxData['errCode'] == 0 && !empty($xcxData['data'])) {
            $returnData['list'] = $xcxData['data'];
            return $returnData;
        } else {
            $code = $xcxData['errCode'] == 0 ? 40000 : $xcxData['errCode'];
            $returnData['code'] = $code;
            $returnData['hint'] = $xcxData['errMsg'];
            return $returnData;
        }
    }

    /***
     * todo 小程序微页面
     * @param Request $request
     * @param XCXModule $xcxModule
     * @return array
     * @author jonzhang
     * @date 2017-09-22
     * @update 张永辉 2018年7月2日 微页面添加导航
     */
    public function getXCXMicroPage(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $token = $request->input('token');
        $id = $request->input('id');
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        if (empty($id)) {
            $returnData['code'] = -101;
            $returnData['hint'] = 'id为空';
            return $returnData;
        }
        $wxxcxTopNavService = new WXXCXTopNavService();
        $wid = $request->input('wid');
        $topNavData = $wxxcxTopNavService->getRow($wid);
        $headerData = '';
        if ($topNavData['errCode'] == 0 && !empty($topNavData['data'])) {
            if ($topNavData['data']['is_on'] && !empty($topNavData['data']['template_data'])) {
                $headerData = json_decode($topNavData['data']['template_data'], true);
                $i = 1;
                foreach ($headerData as &$item) {
                    //$i==1表示店铺首页不需要重新获取数据
                    if ($i > 1) {
                        //效验数据 删除的数据不显示
                        $xcxPageData = WXXCXMicroPageService::getRowById($item['pageId']);
                        if ($xcxPageData['errCode'] < 0 || ($xcxPageData['errCode'] == 0 && empty($xcxPageData['data']))) {
                            unset($item);
                        }
                    }
                    $i++;
                }
//                $headerData =json_encode($headerData);
                $headerData = '';
            }
        }
        $xcxData = $xcxModule->processXCXMicroPage($id);
        if ($xcxData['errCode'] == 0 && !empty($xcxData['data'])) {
            $xcxData['data']['headerData'] = $headerData;
            $returnData['list'] = $xcxData['data'];

            return $returnData;
        } else {
            $code = $xcxData['errCode'] == 0 ? 40000 : $xcxData['errCode'];
            $returnData['code'] = $code;
            $returnData['hint'] = $xcxData['errMsg'];
            return $returnData;
        }
    }

    /**
     * 用于小程序开启底部logo链接跳转微页面，浏览统计
     * @param Request $request 请求参数
     * @param XCXModule $xcxModule 小程序Module
     * @return array
     * @author 何书哲 2018年9月11日
     * @update 何书哲 2018年9月29日 店铺底部logo跳转增加留言类型
     */
    public function getLogoMicroPage(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $token = $request->input('token');
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -101;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        $logo_page_type = config('app.logo_page_type');
        $logo_page_id = config('app.logo_page_id');
        if (!$logo_page_type) {
            $returnData['code'] = -102;
            $returnData['hint'] = '类型为空';
            return $returnData;
        }
        if (empty($logo_page_id)) {
            $returnData['code'] = -103;
            $returnData['hint'] = 'id为空';
            return $returnData;
        }
        if ($logo_page_type == 1) {//微页面id
            //获取小程序微页面数据
            $xcxData = $xcxModule->processXCXMicroPage($logo_page_id);
            if ($xcxData['errCode'] != 0 || empty($xcxData['data'])) {
                $returnData['code'] = $xcxData['errCode'] == 0 ? 40000 : $xcxData['errCode'];
                $returnData['hint'] = $xcxData['errMsg'];
                return $returnData;
            }
            $returnData['list']['type'] = 1;
            $returnData['list']['headerData'] = '';
            $returnData['list'] = array_merge($returnData['list'], $xcxData['data']);
        } elseif ($logo_page_type == 2) {//小程序/pages/main/pages/research/research?id=133
            $returnData['list']['type'] = 2;
            $returnData['list']['id'] = $logo_page_id;
        }
        return $returnData;
    }

    /**
     * 店铺底部logo是否开启链接
     * @param Request $request 请求参数
     * @return array
     * @author 何书哲 2018年9月11日
     */
    public function shopLogoIsOpen(Request $request)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $token = $request->input('token');
        $wid = CommonModule::getWidByToken($token);
        if (empty($wid)) {
            $returnData['code'] = -101;
            $returnData['hint'] = 'token中的数据有问题';
            return $returnData;
        }
        //获取店铺数据
        $shopData = (new WeixinService())->init()->getInfo($wid);
        //是否开启底部logo链接，开启则跳转，关闭则不跳转
        if (empty($shopData)) {
            $returnData['code'] = -102;
            $returnData['hint'] = '店铺不存在';
            return $returnData;
        }
        $returnData['list']['is_logo_open'] = $shopData['is_logo_open'];
        return $returnData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170926
     * @desc 发送验证码
     * @param Request $request
     * @param WeixinSmsConfService $weixinSmsConfService
     */
    public function sendCode(Request $request, VerifyCodeService $verifyCodeService)
    {
        $phone = $request->input('phone');
        $sms_code = 2;
        if (!$phone || !$sms_code) {
            error('手机号码或短信码不能为空');
        }
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        //验证图形验证码
        $imgCode = $request->input('img_code');
        $codeModule = new CodeModule();

        $bindMobileModule = new BindMobileModule();
        if (!$bindMobileModule->isAccessSendCode($mid)) {
            xcxerror('您发送验证码已达到上线');
        }
        //生成验证码 随机生成4位
        $code = rand(1000, 9999);
        $contactPhone = '商家客服';
        $datas = [$code, 1, $contactPhone];
        $result = $verifyCodeService->sendCode($phone, $datas, $sms_code);
        if ($result->statusCode != 0) {
            xcxerror((string)$result->statusMsg);
        } else {
            $mid = $request->input('mid');
            $smsKeys = new SMSKeys('bindmobile' . $mid . 'phone' . $phone);
            $codeSms = $smsKeys->get();
            $codeSms = json_decode($codeSms, true) ?? [];
            array_push($codeSms, $code);
            $smsKeys->set(json_encode($codeSms));
            xcxsuccess('验证码发送成功');
        }
    }

    /**
     * 验证码验证
     */
    public function verifyCode(Request $request)
    {
        $phone = $request->input('phone');
        $mid = $request->input('mid');

        $codeSms = (new SMSKeys('bindmobile' . $mid . 'phone' . $phone))->get();
        $codeSms = json_decode($codeSms, true) ?? [];
        $code = $request->input('code');
        if (!in_array($code, $codeSms)) {
            xcxerror('验证码错误');
        } else {
            xcxsuccess('验证码正确');
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170926
     * @desc 绑定手机验证码
     * @param Request $request
     */
    public function bindMobile(Request $request)
    {
        $phone = $request->input('phone');
        $token = $request->input('token');
        $wid = $request->input('wid');
        $mid = $request->input('mid');

        $codeSms = (new SMSKeys('bindmobile' . $mid . 'phone' . $phone))->get();
        $codeSms = json_decode($codeSms, true) ?? [];
        $code = $request->input('code');
        if (!in_array($code, $codeSms)) {
            xcxerror('验证码错误');
        }
        //判断该账户是否已经绑定手机号码
        $res = MemberService::getRowById($mid);
        if ($res['mobile']) {
            xcxerror('您已绑定过手机号了!');
        }
        //处理绑定手机号码
        $res = (new BindMobileModule())->xcxBindMobile($wid, $mid, $phone, $token);
        $mid = CommonModule::getMidByToken($token);
        if ($res) {
            xcxsuccess('操作成功', $mid);
        } else {
            xcxerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 小程序更换绑定手机号码
     * @param Request $request
     */
    public function changeMobile(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'mobile' => 'required',
            'code1'  => 'required',
            'code2'  => 'required',
        );
        $message = Array(
            'mobile.required' => '手机号码不能为空',
            'code1.required'  => '验证码不能为空',
            'code2.required'  => '验证码不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $mid = $input['mid'];
        $code2Sms = (new SMSKeys('bindmobile' . $mid . 'phone' . $input['mobile']))->get();
        $code2Sms = json_decode($code2Sms, true) ?? [];
        $memberData = MemberService::getRowById($mid);
        $code1Sms = (new SMSKeys('bindmobile' . $mid . 'phone' . $memberData['mobile']))->get();
        $code1Sms = json_decode($code1Sms, true) ?? [];
        if (!in_array($input['code1'], $code1Sms) || !in_array($input['code2'], $code2Sms)) {
            xcxerror('验证码错误');
        }

        $mid = $request->input('mid');
        $res = (new BindMobileModule())->changeMobile($mid, $input['mobile']);
        if ($res) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171102
     * @desc 是否需要绑定手机号码
     * @param Request $request
     */
    public function isBind(Request $request)
    {
        xcxsuccess('操作成功', 0);
    }

    /***
     * todo 查询小程序某个店铺信息仅供开发者使用
     * @param Request $request
     * @param XCXModule $xcxModule
     * @return array
     * @author jonzhang
     * @date 2017-11-21
     */
    public function getHomePageByDeveloper(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => '', 'header' => ''];
        $wid = $request->input('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -1001;
            $returnData['errMsg'] = '请输入wid';
            return $returnData;
        }
        //add by jonzhang 2017-12-28
        //小程序首页分类链条
        $wxxcxTopNavService = new WXXCXTopNavService();
        $xcxTopNavData = $wxxcxTopNavService->getRow($wid);
        if ($xcxTopNavData['errCode'] == 0 && !empty($xcxTopNavData['data'])) {
            if ($xcxTopNavData['data']['is_on'] && !empty($xcxTopNavData['data']['template_data'])) {
                $headerData = json_decode($xcxTopNavData['data']['template_data'], true);
                $i = 1;
                foreach ($headerData as &$item) {
                    //$i==1表示店铺首页不需要重新获取数据
                    if ($i > 1) {
                        //效验数据 删除的数据不显示
                        $xcxPageData = WXXCXMicroPageService::getRowById($item['pageId']);
                        if ($xcxPageData['errCode'] < 0 || ($xcxPageData['errCode'] == 0 && empty($xcxPageData['data']))) {
                            unset($item);
                        }
                    }
                    $i++;
                }
                if (!empty($headerData)) {
                    $returnData['header'] = json_encode($headerData);
                }
            }
        } else {
            BLogger::getLogger('info')->info('首部导航数据:' . json_encode($xcxTopNavData));
        }
        //小程序首页数据
        $xcxData = $xcxModule->processMainHome($wid);
        if ($xcxData['errCode'] == 0 && !empty($xcxData['data'])) {
            $returnData['data'] = $xcxData['data'];
            return $returnData;
        } else {
            $code = $xcxData['errCode'] == 0 ? -1002 : $xcxData['errCode'];
            $returnData['errCode'] = $code;
            $returnData['errMsg'] = $xcxData['errMsg'];
            return $returnData;
        }
    }

    /***
     * todo 查询小程序某个微页面信息仅供开发者使用
     * @param Request $request
     * @param XCXModule $xcxModule
     * @return array
     * @author jonzhang
     * @date 2017-11-21
     */
    public function getXCXMicroPageByDeveloper(Request $request, XCXModule $xcxModule)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $id = $request->input('id');
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '请输入id';
            return $returnData;
        }
        return $xcxModule->processXCXMicroPage($id);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171211
     * @desc 个人中心是否显示修改手机号码
     * @param Request $request
     * @update 陈文豪 2018年8月15号 没有配置微信公众号，默认不打开小程序绑定
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isShowChangeMobile(Request $request, ShopService $shopService)
    {
        $wid = $request->input('wid');

        $weixinConfigSub = new WeixinConfigSub();
        $obj = $weixinConfigSub->wheres(['wid' => $wid])->first();
        !$obj && (xcxsuccess('操作成功', 0));

        //$shopData = (new WeixinService())->getStore($wid);
        $shopData = $shopService->getRowById($wid);
        if ($shopData && $shopData['is_sms'] == 1) {
            xcxsuccess('操作成功', 1);
        } else {
            xcxsuccess('操作成功', 0);
        }
    }

    /**
     * 底部导航栏第一个微页面的数据接口
     * @param  Request $request [description]
     * @return [type]           [description]
     * @update 张永辉 2018年7月5日 添加顶部导航
     */
    public function getBarMicroPageFirst(Request $request)
    {
        $wid = $request->input('wid');
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        list($list, $page) = $xcxSyncFooterBarService->getAllList($wid, ['page_path' => 'pages/micropage/index1/index']);
        $returnData['data'] = [];
        if ($list['data']) {
            $id = $list['data'][0]['page_id'];
            $xcxModule = new XCXModule();
            $returnData = $xcxModule->processXCXMicroPage($id);
        }
        $returnData['data']['headerData'] = (new WXXCXTopNavService())->getTopNav($wid);
        xcxsuccess('', $returnData['data']);

    }

    /**
     * 底部导航栏第二个微页面的数据接口
     * @param  Request $request [description]
     * @return [type]           [description]
     * @update 张永辉 2018年7月5日 添加顶部导航
     */
    public function getBarMicroPageSec(Request $request)
    {
        $wid = $request->input('wid');
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        list($list, $page) = $xcxSyncFooterBarService->getAllList($wid, ['page_path' => 'pages/micropage/index2/index']);
        $returnData['data'] = [];
        if ($list['data']) {
            $id = $list['data'][0]['page_id'];
            $xcxModule = new XCXModule();
            $returnData = $xcxModule->processXCXMicroPage($id);
        }
        $returnData['data']['headerData'] = (new WXXCXTopNavService())->getTopNav($wid);
        xcxsuccess('', $returnData['data']);
    }

    /**
     * 底部导航栏第三个微页面的数据接口
     * @param  Request $request [description]
     * @return [type]           [description]
     * @update 张永辉 2018年7月5日 添加顶部导航
     */
    public function getBarMicroPageThird(Request $request)
    {
        $wid = $request->input('wid');
        $xcxSyncFooterBarService = new WXXCXSyncFooterBarService();
        list($list, $page) = $xcxSyncFooterBarService->getAllList($wid, ['page_path' => 'pages/micropage/index3/index']);
        $returnData['data'] = [];
        if ($list['data']) {
            $id = $list['data'][0]['page_id'];
            $xcxModule = new XCXModule();
            $returnData = $xcxModule->processXCXMicroPage($id);
        }
        $returnData['data']['headerData'] = (new WXXCXTopNavService())->getTopNav($wid);
        xcxsuccess('', $returnData['data']);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180127
     * @desc
     */
    public function imgCode()
    {
        echo app('captcha')->src('bind');
    }

    /**
     * todo 店铺是否过期
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2018-01-08
     */
    public function getWidData(Request $request)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => 0];
        $wid = $request->input('wid') ?? 0;
        if (empty($wid)) {
            $returnData['code'] = -40001;
            $returnData['hint'] = 'wid为空';
            return $returnData;
        }
        $expireData = (new WeixinRoleService())->isExpire($wid);
        if ($expireData['errCode'] < 0 || ($expireData['errCode'] == 0 && $expireData['data'] == 1)) {
            $returnData['list'] = 1;
        }
        return $returnData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180402
     * @desc 获取用户手机号码
     */
    public function getMemberMobile(Request $request)
    {
        $mid = $request->input('mid');
        $member = MemberService::getRowById($mid);
        $result = [
            'id'     => $member['id'],
            'mobile' => $member['mobile'],
        ];
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180529
     * @desc 获取基础信息
     */
    public function base()
    {
        $result = [
            'imgUrl'        => imgUrl(),
            'staticImgUrl ' => 'https://upx.cdn.huisou.cn/wscphp/xcx/images/',
            'videoUrl'      => config('app.source_video_url'),
        ];
        xcxsuccess('操作成功', $result);
    }

    /**
     * 根据用户id获取用户信息
     * @param Request $request 请求参数
     * @return json
     * @create 何书哲 2018年6月27日 根据用户id获取用户信息
     */
    public function getMemberInfo(Request $request)
    {
        $mid = $request->input('mid');
        $member = MemberService::getRowById($mid);
        if ($member) {
            $result['mid'] = $member['id'];
            $result['nickName'] = $member['nickname'];
            $result['avatarUrl'] = $member['headimgurl'];
            $result['shopId'] = $member['wid'];
            xcxsuccess('操作成功', $result);
        }
        xcxerror('用户不存在');
    }

    /**
     *  获取店铺相关活动的二维码
     * @param Request $request
     * @return  array 返回包含二维码的数组
     * @author 张永辉 2018年7月2日
     * @update 梅杰 2018年9月25日 区分具体小程序
     */
    public function getQrCode(Request $request, StoreModule $storeModule)
    {
        $wid = $request->input('wid');
        $path = $request->input('path');
        $scene = $request->input('scene');
        $configId = CommonModule::getXcxConfigIdByToken($request->input('token'));
        $result = $storeModule->getQrCode($wid, $scene, $path, $configId);
        if ($result['errCode'] == 0) {
            xcxsuccess('操作成功', $result['data']);
        } else {
            xcxerror($result['errMsg']);
        }
    }

    /**
     * 返回后台保存未提交审核的底部导航数据（修改标题，图标不需要提交审核）
     * @author 吴晓平 <2018年07月18日>
     * @return [type] [description]
     * @update 陈文豪 2019年09月04日11:41:03
     */
    public function getFooterBar(Request $request, WXXCXSyncFooterBarService $xcxSyncFooterBarService)
    {
        $returnData = ['code' => 40000, 'status' => 0, 'hint' => '', 'data' => []];
        $token = $request->input('token');
        $wid = CommonModule::getWidByToken($token);
        if (empty($wid)) {
            $returnData['status'] = -101;
            $returnData['hint'] = 'token中的数据有问题';
            return $returnData;
        }
        $localData = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
        $syncStrData = $xcxSyncFooterBarService->getSyncBarData($wid);

        //未有更新底部导航数据
        if (empty($syncStrData)) {
            return $returnData;
        }

        $syncData = json_decode($syncStrData, true);
        if (empty($syncData)) {
            $returnData['status'] = -102;
            $returnData['hint'] = '修改的底部导航数据异常，重新修改操作';
            return $returnData;
        }

        /*处理新数据*/
        foreach ($syncData as $key => $value) {
            $newSyncData[$key]['id'] = $value['id'];
            $newSyncData[$key]['name'] = $value['text'];
            $newSyncData[$key]['page_path'] = $value['pagePath'];
            $newSyncData[$key]['icon_path'] = $value['iconPath'];
            $newSyncData[$key]['selected_path'] = $value['selectedIconPath'];
            $newSyncData[$key]['url_title'] = $value['urlTitle'];
            $newSyncData[$key]['is_can_revise_url'] = (int)$value['isCanReviseUrl'];
            $newSyncData[$key]['is_sync_weixin'] = $value['isSyncWeixin'];
            $newSyncData[$key]['page_id'] = $value['pageId'];
        }

        if ($localData[0]['data'] && $newSyncData) {
            if (count($localData[0]['data']) == count($newSyncData)) {
                $localColmunName = array_column($localData[0]['data'], 'name');
                $syncColmunName = array_column($newSyncData, 'name');
                $localColmunPage = array_column($localData[0]['data'], 'page_path');
                $syncColmunPage = array_column($newSyncData, 'page_path');
                $localColmunPath = array_column($localData[0]['data'], 'icon_path');
                $syncColmunPath = array_column($newSyncData, 'icon_path');

                // 判断数组是否有变化  2019年09月04日10:52:58 陈文豪
                $diffPage = $diffName = $diffPath = 0;
                if ($localColmunPage != $syncColmunPage) {
                    $diffPage = 1;
                }
                if ($localColmunName != $syncColmunName) {
                    $diffName = 1;
                }
                if ($localColmunPath != $syncColmunPath) {
                    $diffPath = 1;
                }

                // 两组数组有不同的路径，说明需要提交审核
                if (!empty($diffPage)) {
                    $returnData['status'] = -1;
                } else {
                    // 两组数组有不同的标题或不同的图标，不需要提交审核即可生效
                    if (!empty($diffName) || !empty($diffPath)) {

                        $returnData['data'] = $newSyncData;
                    }
                }
            } else {
                // 两组数量不一至说明导航栏有增减操作，有路径改变需要提交审核才行
                $returnData['status'] = -1;
            }
        }
        return $returnData;
    }

    /**
     * 返回后台底部导航数据
     * @param Request $request 请求参数
     * @param WXXCXSyncFooterBarService $xcxSyncFooterBarService
     * @return array
     * @author 何书哲 2018年9月17日
     */
    public function getSyncFooterBar(Request $request, WXXCXSyncFooterBarService $xcxSyncFooterBarService)
    {
        $returnData = ['code' => 40000, 'status' => 0, 'hint' => '', 'data' => []];
        $token = $request->input('token');
        $wid = CommonModule::getWidByToken($token);
        if (empty($wid)) {
            $returnData['status'] = -101;
            $returnData['hint'] = 'token中的数据有问题';
            return $returnData;
        }
        $barList = $xcxSyncFooterBarService->getAllList($wid, [], 'order');
        if (!isset($barList[0]['data']) || empty($barList[0]['data'])) {
            return $returnData;
        }
        $toolBarList = [];
        foreach ($barList[0]['data'] as $key => $value) {
            $iconPathArr = explode('mctsource/', $value['icon_path']);
            $selectedPathArr = explode('mctsource/', $value['selected_path']);
            $toolBarList[$key]['text'] = $value['name'];
            $toolBarList[$key]['pagePath'] = $value['page_path'];
            $toolBarList[$key]['page_id'] = $value['page_id'];
            $toolBarList[$key]['url_title'] = $value['url_title'];
            $toolBarList[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
            $toolBarList[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
        }
        $returnData['data'] = $toolBarList;
        return $returnData;
    }

    /**
     * @description：获取所有的订阅消息模板
     *
     * @return SubScribeMessagePushController|\Illuminate\Http\JsonResponse
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月18日 18:01:37
     *
     * @link: http://192.168.0.239:10000/html/web/share/share.html#5dea0768f2d4b50826106c7f
     */
    public function getAllTemplates(Request $request)
    {
        $token = $request->input('token');
        $wid = CommonModule::getWidByToken($token);
        $list = app(SubscribeMessagePushService::class)->getAllTempList($wid);
        $returnData['data'] = $list;
        return $returnData;
    }

    /**
     * @description：小程序发送订阅模板消息公用接口 （测试专用）
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 20:53:06
     *
     * @link: http://192.168.0.239:10000/html/web/share/share.html#5dea06e8f2d4b50826106c7e
     */
    public function messagePush(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $member = app(\App\S\Member\MemberService::class)->getRowById($mid);
        if (empty($member)) {
            $this->error('登录授权的小程序用户不存在');
        }

        // 模板发送的初步数据
        $data = [
            'wid' => $wid,
            'openid' => $member['openid'],
            'param' => [
                'mid' => $member['id'],
            ]
        ];
        // 组装后的数据
        $result = app(SubscribeMessagePushService::class)->packageSendData(1, $data);
        $sendData = $result;
        $this->dispatch(new SubMsgPushJob(1, $wid, $sendData, ['cid' => $this->request->input('cid', 0)]));

    }

    /**
     * @desc 直播用户登陆绑定关系
     * @param Request $request 请求服务类
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 03 月 20 日
     */
    public function liveUserLogin(Request $request)
    {
        $openid = $request->input('openid');
        $shareOpenid = $request->input('share_openid');
        $wid = $request->input('wid');
        $memberData = Member::query()
            ->where('wid', $wid)
            ->where('xcx_openid', $openid)
            ->first(['id', 'xcx_openid', 'wid', 'pid']);
        $id = $memberData->id ?? 0;
        $pid = $memberData->pid ?? 0;
        if (!$id) {
            $memberModule = new MemberModule();
            try {
                if (!$memberModule->memberCheck($wid, $openid)) {
                    throw new \Exception('memberCheck 插入失败');
                }
            } catch (\Exception $exception) {
                \Log::info('memberCheck 异常' . $exception->getMessage());
            }
            $userData = MemberService::insertData([
                'wid'        => $wid,
                'xcx_openid' => $openid,
                'source'     => 6,
            ]);
            $id = $userData['data'];
            (new NewUserFlagRedis())->set($id);
        }

        if ($shareOpenid && empty($pid)) {
            $shareMemberData = Member::query()
                ->where('wid', $wid)
                ->where('xcx_openid', $shareOpenid)
                ->first(['id', 'xcx_openid', 'wid', 'is_distribute']);
            (new \App\S\Member\MemberService())->bindParent($shareMemberData->id ?? 0, $id);
        }
        xcxsuccess();
    }



}
