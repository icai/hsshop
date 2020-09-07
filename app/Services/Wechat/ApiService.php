<?php

namespace App\Services\Wechat;

use App\Exceptions\WechatException;
use App\Lib\Redis\AuthorizationRedis;
use App\Lib\Redis\ShopQrCodeRedis;
use App\Lib\Redis\Wechat;
use App\Module\WeChatAuthModule;
use App\S\Member\MemberService;
use App\S\Wechat\WechatErrorService;
use App\S\Wechat\WeChatShopConfService;
use Validator;
use WechatError;
use App\S\Weixin\ShopService;

/**
 * 店铺公众号服务类
 * 功能简述：
 * 1、获取appId和appSecret
 * 2、获取接口调用凭证
 * 3、自定义菜单查询
 * 4、自定义菜单创建
 *
 *
 * @author 黄东 406764368@qq.com
 * @version  2017年3月19日 20:58:10
 */
class ApiService
{
    /**
     * 应用id
     * @var [string]
     */
    protected $appId;

    /**
     * 应用密钥
     * @var [string]
     */
    protected $appSecret;

    /**
     * 接口调用凭证
     * @var [string]
     */
    protected $accessToken;

    /**
     * 获取配置信息 获取appId和appSecret
     *
     * @param  integer $wid [店铺id]
     * @return $this
     */

    public function getConf($wid)
    {
        $wechatAuthModule = new WeChatAuthModule();
        $conf = $wechatAuthModule->getConf($wid);
        $this->appId = $conf['app_id'];
        return $conf;
    }

    /**
     * 获取接口调用凭证
     *
     * access_token是公众号的全局唯一票据，公众号调用各接口时都需使用access_token。开发者需要进行妥善保存。access_token的存储至少要保留512个字符空间。access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效。
     *
     * 1、为了保密appsecrect，第三方需要一个access_token获取和刷新的中控服务器。而其他业务逻辑服务器所使用的access_token均来自于该中控服务器，不应该各自去刷新，否则会造成access_token覆盖而影响业务；
     * 2、目前access_token的有效期通过返回的expire_in来传达，目前是7200秒之内的值。中控服务器需要根据这个有效时间提前去刷新新access_token。在刷新过程中，中控服务器对外输出的依然是老access_token，此时公众平台后台会保证在刷新短时间内，新老access_token都可用，这保证了第三方业务的平滑过渡；
     * 3、access_token的有效时间可能会在未来有调整，所以中控服务器不仅需要内部定时主动刷新，还需要提供被动刷新access_token的接口，这样便于业务服务器在API调用获知access_token已超时的情况下，可以触发access_token的刷新流程。
     *
     * 公众号调用接口并不是无限制的。为了防止公众号的程序错误而引发微信服务器负载异常，默认情况下，每个公众号调用接口都不能超过一定限制，当超过一定限制时，调用对应接口会收到如下错误返回码：{"errcode":45009,"errmsg":"api freq out of limit"}
     *
     * @param  integer $wid [店铺id]
     * @return string       [接口调用凭证]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getAccessToken($wid)
    {
        // 查询过则直接返回
        if (empty($this->accessToken)) {
            // 查询微信支付配置 获取接口调用凭证
            /*$uid = D('Weixin')->model->where('id', $wid)->value('uid');
            $weixinService = D('Weixin', 'uid', $uid);
            $info = $weixinService->getInfo($wid);*/
            $shopService = new ShopService();
            $info = $shopService->getRowById($wid);
            // 数据库和redis没有记录 或者 已过期 则调 获取（刷新）授权公众号
            if (!isset($info['expires_at']) || empty($info['expires_at']) || strtotime($info['expires_at']) < time() || !isset($info['access_token']) || empty($info['access_token'])) {
                //获取授权方的APPID
                $weChatShopConfService = new WeChatShopConfService();
                $conf = $weChatShopConfService->getConfigByWid($wid);
                $appId = $conf['app_id'] ?? '';

                \Log::info('获取店铺id=' . $wid . 'appid=' . $appId);
                if ($appId) {
                    //从redis中获取第三方平台的access_token
                    $authorizationService = new AuthorizationService();
                    $wechatRedis = new Wechat('component_verify_ticket');
                    $component_access_token = $authorizationService->getComponentAccessToken($wechatRedis->get());
                    $ret = $authorizationService->refreshAccessToken($appId, $info['authorizer_refresh_token'], $component_access_token);

                    if (isset($ret['errcode']) && !empty($ret['errcode'])) {
                        //error('授权authorizer_access_token过期');
                        $refreshToken = $authorizationService->getRefreshToken($component_access_token, $appId);
                        $ret = $authorizationService->refreshAccessToken($appId, $refreshToken, $component_access_token);

                        if (!empty($ret['errcode'])) {

                            throw new WechatException('获取token失败：' . $ret['errcode'] . "(" . $ret['errmsg'] . ")");
                        }

                    }
                    //微信更新token bug
                    $datas['authorizer_refresh_token'] = $ret['authorizer_refresh_token'];
                    $datas['access_token'] = $ret['authorizer_access_token'];
                    $datas['expires_at'] = date('Y-m-d H:i:s', ($ret['expires_in'] - 500 + time()));
                    //$result = $weixinService->where(['id' => $wid])->update($datas, false);
                    $result = $shopService->update($wid, $datas);
                    if (!$result) {
                        error('数据更新失败');
                    }
                    $this->accessToken = $ret['authorizer_access_token'];
                }

            } else {
                $this->accessToken = $info['access_token'];
            }

        }


        return $this->accessToken;
    }

    /**
     * 自定义菜单查询
     *
     * 使用接口创建自定义菜单后，开发者还可使用接口查询自定义菜单的结构。另外请注意，在设置了个性化菜单后，使用本自定义菜单查询接口可以获取默认菜单和全部个性化菜单信息。
     * menu为默认菜单，conditionalmenu为个性化菜单列表。
     *
     * @param  integer $wid [店铺id]
     * @return boolean        [自定义菜单数据]
     */
    public function customMenuGet($wid)
    {
        // 获取调用接口凭证则
        $this->getAccessToken($wid);
        // 请求微信接口
        $result = jsonCurl('https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $this->accessToken);
        if (isset($result['errcode'])) {
            error('数据获取失败');
        }

        return $result;
    }

    /**
     * 自定义菜单创建
     *
     * 1、自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。
     * 2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。
     * 3、创建自定义菜单后，菜单的刷新策略是，在用户进入公众号会话页或公众号profile页时，如果发现上一次拉取菜单的请求在5分钟以前，就会拉取一下菜单，如果菜单有更新，就会刷新客户端的菜单。测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。
     *
     * 参数       是否必须                   说明
     * button       是                    一级菜单数组，个数应为1~3个
     * sub_button   否                    二级菜单数组，个数应为1~5个
     * type         是                    菜单的响应动作类型
     * name         是                    菜单标题，不超过16个字节，子菜单不超过40个字节
     * key          click等点击类型必须   菜单KEY值，用于消息接口推送，不超过128字节
     * url          view类型必须          网页链接，用户点击菜单可打开链接，不超过1024字节
     * media_id     media_id类型和view_limited类型必须    调用新增永久素材接口返回的合法media_id
     *
     * @param  integer $wid [店铺id]
     * @param  array $datas [数据数组]
     * @return boolean        [成功返回true，失败返回false]
     * @update 梅杰 20118年10月15日 创建菜单时直接返回微信API错误信息
     */
    public function customMenuCreate($wid, $datas)
    {
        // 获取调用接口凭证则
        $this->getAccessToken($wid);

        // 数据验证
        if (!isset($datas['button'])) {
            error('数据结构错误');
        }

        // 自定义菜单最多包括3个一级菜单
        if (count($datas['button']) > 3) {
            error('一级菜单最多3个');
        }

        // 数据处理
        foreach ($datas['button'] as $key => $value) {
            $datas['button'][$key]['name'] = urlencode($value['name']);
            if (isset($value['key'])) {
                $datas['button'][$key]['key'] = urlencode($value['key']);
            } elseif (isset($value['url'])) {
                $datas['button'][$key]['url'] = urlencode($value['url']);
            }
            if (isset($value['sub_button'])) {
                // 每个一级菜单最多包含5个二级菜单
                if (count($value['sub_button']) > 5) {
                    error('二级菜单最多5个');
                }
                foreach ($value['sub_button'] as $k => $v) {
                    $datas['button'][$key]['sub_button'][$k]['name'] = urlencode($v['name']);
                    if (isset($v['key'])) {
                        $datas['button'][$key]['sub_button'][$k]['key'] = urlencode($v['key']);
                    } elseif (isset($v['url'])) {
                        $datas['button'][$key]['sub_button'][$k]['url'] = urlencode($v['url']);
                    }
                }
            }
        }

        // 请求微信接口
        $result = jsonCurl('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->accessToken, urldecode(json_encode($datas)));
        if (!isset($result['errcode']) || $result['errcode'] != 0) {
//            $msg = (new WechatErrorService())->handle($result['errcode']);
            error($result['errmsg'] . "(未认证的服务号或者订阅号可能会出现权限问题)");
        }
    }

    /**
     * 网页授权获取用户信息
     *
     * @param  integer $wid [店铺id]
     * @return object  RedirectResponse [获取code码时返回重定向类对象]
     */
    public function auth($wid)
    {
        // http请求
        $request = app('request');
        // code
        $code = $request->input('code');
        // 获取appId和appSecret
        $this->getConf($wid);

        if (empty($code)) {

            // 用户同意授权，获取code
            return redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appId . '&redirect_uri=' . $request->url() . '&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect');
        } else {
            $access_token = session('access_token_' . $wid);
            $expires_at = session('expires_at_' . $wid);
            if (empty($access_token) || ($expires_at && time() > $expires_at) || empty(session('mid'))) {
                /**
                 * 通过code换取网页授权access_token
                 *
                 * 返回说明：
                 * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
                 * expires_in  access_token接口调用凭证超时时间，单位（秒）
                 * refresh_token   用户刷新access_token
                 * openid  用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和 * 公众号唯一的OpenID
                 * scope   用户授权的作用域，使用逗号（,）分隔
                 */
                $oauth = jsonCurl('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code');

                // 保存授权信息至session
                $request->session()->put('access_token_' . $wid, $oauth['access_token']);
                $request->session()->put('expires_at_' . $wid, (time() + $oauth['expires_in'] - 120));
                $request->session()->put('openid_' . $wid, $oauth['openid']);
                $request->session()->save();

                /**
                 * 拉取用户信息(需scope为 snsapi_userinfo)
                 *
                 * openid   用户的唯一标识
                 * nickname    用户昵称
                 * sex 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                 * province    用户个人资料填写的省份
                 * city    普通用户个人资料填写的城市
                 * country 国家，如中国为CN
                 * headimgurl  用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
                 * privilege   用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
                 * unionid 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
                 */
                $userInfo = jsonCurl('https://api.weixin.qq.com/sns/userinfo?access_token=' . $oauth['access_token'] . '&openid=' . $oauth['openid']);
                if (isset($userInfo['errcode'])) {
                    error('用户授权失败');
                }

                // 构建会员数据
                $memberDatas['nickname'] = $userInfo['nickname'] ?: '会员' . str_random(2) . '_' . str_numeric_random(8);
                $memberDatas['headimgurl'] = $userInfo['headimgurl'] ?: config('app.source_url') . 'public/static/images/member_default.png';
                $memberDatas['sex'] = $userInfo['sex'] ?: 0;

                // 新增/编辑会员信息（db+redis）
                $memberInfo = (new MemberService())->getRowByOpenid($appId);
                if ($memberInfo) {
                    $mid = $memberInfo['id'];
                    $result = (new MemberService())->updateData($mid, $memberDatas);
                } else {
                    // 新增
                    $memberDatas['wid'] = $wid;
                    $memberDatas['openid'] = $oauth['openid'];
                    $memberDatas['truename'] = $memberDatas['nickname'];
                    $memberDatas['score'] = 0;
                    $memberDatas['source'] = 1;
                    $memberDatas['buy_num'] = 0;
                    $memberDatas['province_id'] = 0;
                    $memberDatas['city_id'] = 0;
                    $memberDatas['area_id'] = 0;
                    $memberDatas['is_member'] = 0;

                    $mid = (new MemberService())->add($memberDatas);
                    $result = $mid;
                }
                if (!$result) {
                    error('获取用户信息失败');
                }

                // 保存会员信息至session
                $request->session()->put('mid', $mid);
                $request->session()->save();
            }
        }
        return false;
    }

    /**
     * [saveAuthUsers 进入会员首页保存用户信息]
     * @param  [int] $wid [店铺id]
     * @return [type]      [description]
     */
    public function saveAuthUsers($wid)
    {
        $request = app('request');
        $code = $request->input('code');
        $appId = $appSecret = '';
        //获取公众号的AppId
        $weChatShopConfService = new WeChatShopConfService();
        $weChatConf = $weChatShopConfService->getRowByWid($wid);
        if (!empty($weChatConf) && !empty($weChatConf['app_secret'])) {
            $appId = $weChatConf['app_id'];
            $appSecret = $weChatConf['app_secret'];
        } else {
//            error('店铺公众号没有被授权');
        }
        if ($appSecret) {
            if (empty($code)) {
                return redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $request->fullUrl() . '&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect');
            } else {
                $access_token = session('access_token_' . $wid);
                $expires_at = session('expires_at_' . $wid);
                if (empty($access_token) || ($expires_at && time() > $expires_at) || empty(session('mid')) || empty(session($wid . "_mid"))) {
                    /**
                     * 通过code换取网页授权access_token
                     *
                     * 返回说明：
                     * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
                     * expires_in  access_token接口调用凭证超时时间，单位（秒）
                     * refresh_token   用户刷新access_token
                     * openid  用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和 * 公众号唯一的OpenID
                     * scope   用户授权的作用域，使用逗号（,）分隔
                     */
                    $oauth = jsonCurl('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code');

                    if (isset($oauth['errcode'])) {
                        error('微信网页授权失败');
                    }

                    // 保存授权信息至session
                    $request->session()->put('access_token_' . $wid, $oauth['access_token']);
                    $request->session()->put('expires_at_' . $wid, (time() + $oauth['expires_in'] - 120));
                    $request->session()->put('openid_' . $wid, $oauth['openid']);
                    $request->session()->save();

                    /**
                     * 拉取用户信息(需scope为 snsapi_userinfo)
                     *
                     * openid   用户的唯一标识
                     * nickname    用户昵称
                     * sex 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                     * province    用户个人资料填写的省份
                     * city    普通用户个人资料填写的城市
                     * country 国家，如中国为CN
                     * headimgurl  用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
                     * privilege   用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
                     * unionid 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
                     */
                    $userInfo = jsonCurl('https://api.weixin.qq.com/sns/userinfo?access_token=' . $oauth['access_token'] . '&openid=' . $oauth['openid']);
                    if (isset($userInfo['errcode'])) {
                        error('用户授权失败');
                    }

                    // 构建会员数据
                    $memberDatas['nickname'] = $userInfo['nickname'] ?: '会员' . str_random(2) . '_' . str_numeric_random(8);
                    $memberDatas['headimgurl'] = $userInfo['headimgurl'] ?: config('app.source_url') . 'public/static/images/member_default.png';
                    $memberDatas['sex'] = $userInfo['sex'] ?: 0;

                    // 新增/编辑会员信息（db+redis）
                    $memberInfo = (new MemberService())->getRowByOpenid($oauth['openid']);
                    if ($memberInfo) {
                        $mid = $memberInfo['id'];
                        $result = (new MemberService())->updateData($mid, $memberDatas);
                    } else {
                        // 新增
                        $memberDatas['wid'] = $wid;
                        $memberDatas['openid'] = $oauth['openid'];
                        $memberDatas['truename'] = $memberDatas['nickname'];
                        $memberDatas['score'] = 0;
                        $memberDatas['source'] = 1;
                        $memberDatas['buy_num'] = 0;
                        $memberDatas['province_id'] = 0;
                        $memberDatas['city_id'] = 0;
                        $memberDatas['area_id'] = 0;
                        $memberDatas['is_member'] = 0;

                        $mid = (new MemberService())->add($memberDatas);
                        $result = $mid;
                    }
                    if (!$result) {
                        error('获取用户信息失败');
                    }
                    //如果该店铺有重新绑定标识
                    $shopRedis = new AuthorizationRedis('wid_' . $wid . '_reset_flag');
                    $shop_reset_flag = $shopRedis->get();
                    if ($shop_reset_flag != false) {
                        $memberRedis = new AuthorizationRedis($wid . '_mid_' . $mid . '_reset_flag');
                        $member_reset_flag = $memberRedis->get();
                        if ($memberRedis == false || $member_reset_flag != $shop_reset_flag) {
                            $memberRedis->set($shop_reset_flag);
                        }
                    }
                    // 保存会员信息至session
                    $request->session()->put('mid', $mid);
                    $request->session()->save();
                }
            }

        } else {
            $memberDatas['nickname'] = '会员' . str_random(2) . '_' . str_numeric_random(8);
            $memberDatas['headimgurl'] = config('app.source_url') . 'public/static/images/member_default.png';
            $memberDatas['sex'] = 0;

            // 新增/编辑会员信息（db+redis）
            $memberInfo = (new MemberService())->getRowByOpenid($appId);
            if ($memberInfo) {
                // 编辑
                $mid = $memberInfo['id'];
                $result = (new MemberService())->updateData($mid, $memberDatas);
            } else {
                // 新增
                $memberDatas['wid'] = $wid;
                $memberDatas['openid'] = $appId;
                $memberDatas['truename'] = $memberDatas['nickname'];
                $memberDatas['score'] = 0;
                $memberDatas['source'] = 1;
                $memberDatas['buy_num'] = 0;
                $memberDatas['province_id'] = 0;
                $memberDatas['city_id'] = 0;
                $memberDatas['area_id'] = 0;
                $memberDatas['is_member'] = 0;

                $mid = (new MemberService())->add($memberDatas);
                $result = $mid;
            }
            if (!$result) {
                error('获取用户信息失败');
            }
            // 保存会员信息至session
            $request->session()->put('mid', $mid);
            $request->session()->save();
        }
        return false;
    }

    /**
     * 新增永久图文素材
     *
     * 最近更新，永久图片素材新增后，将带有URL返回给开发者，开发者可以在腾讯系域名内使用（腾讯系域名外使用，图片将被屏蔽）。
     *
     * 请注意:
     * 1、新增的永久素材也可以在公众平台官网素材管理模块中看到
     * 2、永久素材的数量是有上限的，请谨慎新增。图文消息素材和图片素材的上限为5000，其他类型为1000
     * 3、素材的格式大小等要求与公众平台官网一致。具体是，图片大小不超过2M，支持bmp/png/jpeg/jpg/gif格式，语音大小不超过5M，长度不超过60秒（公众平台官网可以在文章中插入小于30分钟的语音，但这些语音不能用于群发等场景，只能放在文章内，这方面接口暂不支持），支持mp3/wma/wav/amr格式
     * 4、调用该接口需https协议
     *
     * @param  array $input [提交数据]
     * @param  integer $wid [店铺id]
     * @return json             [调用全局辅助json返回函数]
     */
    public function newsAdd($input, $wid)
    {
        // 定义验证规则
        $rules = [
            'title' => 'required|max:64',
            'thumb_media_id' => 'required',
            'show_cover_pic' => 'in:0,1',
            'content' => 'required|max:20000',
        ];
        // 定义错误消息
        $messages = [
            'title.required' => '请填写标题',
            'title.max' => '标题长度限制在64以内',
            'thumb_media_id.required' => '请上传封面图',
            'show_cover_pic.in' => '请选择是否显示封面',
            'content.required' => '请填写内容',
            'content.max' => '内容长度限制在2w字内',
        ];
        if (is_array(current($input))) {
            foreach ($input as $value) {
                // 执行验证
                $validator = Validator::make($value, $rules, $messages);
                if ($validator->fails()) {
                    error($validator->errors()->first());
                }
                $datas['articles'][] = $value;
            }
        } else {
            // 执行验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $datas['articles'][] = $input;
        }

        // 获取调用接口凭证则
        $this->getAccessToken($wid);

        // 请求微信接口
        $result = jsonCurl('https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . $this->accessToken, json_encode($datas));
        dd($result, $datas);

        // 通信失败
        if ($result === false) {
            error('通信失败');
        }

        // 通信错误
        if (isset($result['errcode'])) {
            error('通信错误');
        }

        return true;
    }

    /**
     * 上传图文消息内的图片获取URL
     * 吴晓平 2017.07.20 update
     * 本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下
     *  $type 上传素材的类型
     *  图片（image）: 2M，支持PNG\JPEG\JPG\GIF格式
     * 语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
     * 视频（video）：10MB，支持MP4格式
     * 缩略图（thumb）：64KB，支持JPG格式
     * @param [int] $apiType上传接口类型  1-表示上传图片到微信服务器返回url地址   2-上传素材到微信返回media_id
     * @param  [type] $wid [description]
     * @return [type]      [description]
     */
    public function uploadFile($wid, $filename, $type = 'image', $apiType = 1)
    {

        // 获取调用接口凭证则
        $this->getAccessToken($wid);

        //上传素材接口
        switch ($apiType) {
            case 1:
                $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . $this->accessToken . '&type=' . $type;
                break;

            case 2:
                $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $this->accessToken . '&type=' . $type;
                break;

            default:
                $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . $this->accessToken . '&type=' . $type;
                break;
        }

        $result = $this->http_post($url, $filename);

        return $result;
    }

    /**
     * [curl 上传文件]
     * @author 吴晓平 2017.07.20
     * @param  string $url [接口链接]
     * @param  string $filename [带路径的文件]
     * @return [type]           [description]
     */
    public function http_post($url = '', $filename = '')
    {
        $curl = curl_init();

        //检查判断PHP版本
        if (class_exists('\CURLFile')) { //php版本大于5.5
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            $data = array('media' => new \CURLFile($filename));
        } // php版本5.5以下
        else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('media' => '@' . realpath($filename));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'TEST');
        $result = curl_exec($curl);

        return json_decode($result, true);
    }

    /***************************************微信卡券************************************************/
    //创建卡券
    public function wxCardCreated($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用创建卡券的url
        $url = "https://api.weixin.qq.com/card/create?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    //编辑卡券
    public function wxCardUpdate($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用更新卡券的url
        $url = "https://api.weixin.qq.com/card/update?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    //微信卡券：消耗卡券
    public function wxCardConsume($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用消耗卡券的url
        $url = "https://api.weixin.qq.com/card/code/consume?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    //微信卡券：删除卡券
    public function wxCardDelete($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用删除卡券的url
        $url = "https://api.weixin.qq.com/card/delete?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    //微信卡券：查询卡券详情
    public function wxCardGetInfo($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用查询卡券的url
        $url = "https://api.weixin.qq.com/card/get?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    //微信卡券：设置白名单
    public function wxCardWhiteList($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用设置白名单的url
        $url = "https://api.weixin.qq.com/card/testwhitelist/set?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    //微信卡券：获取颜色
    public function wxCardColor()
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用获取颜色的url
        $url = "https://api.weixin.qq.com/card/getcolors?access_token=" . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    //创建二维码接口
    public function qrcodeCreated($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用获取二维码接口的url
        $url = 'https://api.weixin.qq.com/card/qrcode/create?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    public function getTplId($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用获取二维码接口的url
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    public function getTplListFromWechat($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用获取二维码接口的url
        $url = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    public function sendTplNotify($wid, $data)
    {
        $accessToken = $this->getAccessToken($wid);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    public function getAppid($wid)
    {
        if (empty($this->appId)) {
            $this->getConf($wid);
        }
        return $this->appId;
    }

    public function activeCard($wid, $data)
    {
        $accessToken = $this->getAccessToken($wid);
        $url = 'https://api.weixin.qq.com/card/membercard/activate?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    //创建临时二维码ticket
    public function tempQrcodeCreated($wid, $data)
    {
        //获得accessToken值
        $accessToken = $this->getAccessToken($wid);
        //调用获取二维码接口的url
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    public function getUserInfo($wid, $openId)
    {
        $accessToken = $this->getAccessToken($wid);
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $this->accessToken . '&openid=' . $openId;
        $userInfo = jsonCurl($url);
        return $userInfo;
    }

    public function tempShopQrcodeCreated($wid)
    {
        $data = [
//            'expire_seconds'    => 900,
//            'action_name'       => 'QR_STR_SCENE',
            'action_name' => 'QR_LIMIT_STR_SCENE',
        ];
        $data['action_info']['scene']['scene_str'] = 'subscribe';
        $accessToken = $this->getAccessToken($wid);
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    public function getShopQrcode($wid)
    {
        $redis = new ShopQrCodeRedis('ShopQrcode');
        $data = $redis->getRow($wid);
        if (!$data) {
            $conf = (new WeChatShopConfService())->getRowByWid($wid);
            if ($conf) {
                $re = $this->tempShopQrcodeCreated($wid);
                if (isset($re['url'])) {
                    $data = [
                        'url' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $re['ticket'],
                        'name' => $conf['name']
                    ];
                    $redis->setRow($wid, $data);
                }
            }
        }
        return $data;
    }

    /**
     * 获取用户增减数据
     * @param  [type] $wid  [店铺id]
     * @param  [type] $data [日期范围数组]
     * @return [type]       [description]
     */
    public function getUserSummary($wid, $data)
    {
        $accessToken = $this->getAccessToken($wid);
        $url = 'https://api.weixin.qq.com/datacube/getusersummary?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    /**
     * 获取累计用户数据
     * @param  [type] $wid  [店铺id]
     * @param  [type] $data [日期范围数组]
     * @return [type]       [description]
     */
    public function getUserCumulate($wid, $data)
    {
        $accessToken = $this->getAccessToken($wid);
        $url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token=' . $accessToken;
        $result = jsonCurl($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $result;
    }


}
