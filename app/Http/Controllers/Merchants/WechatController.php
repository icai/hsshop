<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\SendTplMsg;
use App\Lib\Redis\AuthorizationRedis;
use App\Lib\Redis\Wechat;
use App\Module\MessagePushModule;
use App\Module\ProductModule;
use App\S\MarketTools\MessagesPushService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Foundation\LinkToService;
use App\Services\Wechat\ApiService;
use App\Services\Wechat\AuthorizationService;
use App\Services\WeixinConfigSubService;
use App\S\Wechat\WeixinCustomMenuService;
use App\S\Wechat\WeixinMaterialAdvancedService;
use App\S\Wechat\WeixinMaterialWechatService;
use App\S\Wechat\WeixinReplyRuleService;
use App\Services\WeixinWechatPaymentService;
use App\S\Book\UsersBookService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Validator;
use WeixinService;
use App\S\Book\BookService;
use App\Model\WeixinMaterialWechat;
use App\Model\WeixinMaterialAdvanced;

/**
 * 公众号控制器
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月20日 17:18:21
 */
class WechatController extends Controller
{

    protected $AppId;  //微信开放平台appid
    /**
     * 构造函数
     */
    public function __construct() {
        $this->leftNav = 'wechat';
        $this->AppId = config('app.auth_appid');
    }

    /**
     * 微信状况
     * @return [type] [description]
     */
    public function index( Request $request ) {
        // 功能未完成 先用自动回复做为首页
        return redirect('/merchants/wechat/replySet');
        return view('merchants.wechat.index',array(
            'title'     => '微信状况',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'bodyClass' => ' class=oneNav_right'
        ));
    }

    /**
     * 实时信息
     * @return [type] [description]
     */
    public function constantly(){
        return view('merchants.wechat.constant',array(
            'title'=>'实时信息',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'constantly',
            'bodyClass' => ' class=oneNav_right'
            ));
    }

    /**
     * 群发消息
     * @return [type] [description]
     */
    public function mass(){
        return view('merchants.wechat.mass',array(
            'title'=>'群发消息',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'mass',
            'bodyClass' => ' class=oneNav_right'
            ));
    }

    /**
     * 图文素材 - 微信图文
     *
     * @param  Request                     $request
     * @param  WeixinMaterialWechatService $weixinMaterialWechatService
     * @return view
     */
    public function materialWechat(Request $request, WeixinMaterialWechatService $weixinMaterialWechatService)
    {
        // 店铺id
        $wid = session('wid');

        // 查询微信单条图文列表
        list($list,$pageHtml) = $weixinMaterialWechatService->getAllList($wid);
        $list = $list['data'] ?? [];
        // 转为树形结构
        $list = $weixinMaterialWechatService->listToTree($list);
        return view('merchants.wechat.materialWechat', [
            'title'     => '图文素材 - 微信图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list,
            'pageHtml'  => $pageHtml,
        ]);
    }

    /**
     * 获取单条图文列表
     * author: meijie
     * @param WeixinMaterialWechatService $weixinMaterialWechatService
     */
    public function materialGetSingle(Request $request,WeixinMaterialWechatService $weixinMaterialWechatService)
    {
        // 店铺id
        $wid = session('wid');
        $page = $request->input(['page'])??1;
        $size = $request->input(['size'])??15;
        $title = $request->input(['title'])??'';
        // 获取总条数
        list($list,$pageHtml) = $weixinMaterialWechatService->getAllList($wid,['type'=>1]);
        $list = $list['data'] ?? [];
        $total =count($list) ;
        $reData['page']['total'] = $total;
        $where = [
            'wid'=> $wid ,
            'type'=> 1
        ];
        if(!empty($title)){
            $where['title'] = ['like','%'.$title.'%'];
        }
        $reData['data'] = $weixinMaterialWechatService->getListPage($where,'','',$page,$size);
        foreach ( $reData['data'] as $k=>$v)
        {
            $reData['data'][$k]['type'] = 14;
        }
        $linkToService = new LinkToService();
        $reData['data'] = $linkToService->parseUrl($reData['data']);
        $reData['page']['nowPage'] = $page;
        $reData['page']['perSize'] = $size;
        $reData['page']['totalPage'] = (int)ceil($total/$size);
        success('','',$reData);
    }
    /**
     * 图文素材 - 微信图文 - 单条图文
     *
     * @param  Request                     $request
     * @param  WeixinMaterialWechatService $weixinMaterialWechatService
     * @param  integer                     $id
     * @return view
     */
    public function materialWechatSingle(Request $request, WeixinMaterialWechatService $weixinMaterialWechatService, $id = 0)
    {
        // 店铺id
        $wid = session('wid');

        // 新增/编辑
        if ( $request->isMethod('post') ) {

            //$weixinMaterialWechatService->init('wid', $wid);

            $input = $request->only(['id','type','title','cover','author','show_cover_pic','content_source_url','content_source_title','digest','content']);
            $input['wid'] = $wid;

            // 定义验证规则
            $rules = [
                'title'          => 'required|max:64',
                'cover'          => 'required',
            ];
            // 定义错误消息
            $messages = [
                'title.required'          => '请填写标题',
                'title.max'               => '标题长度限制64以内',
                'cover.required'          => '请选择封面图',
            ];
            // 执行验证
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $input['content'] = ProductModule::delProductContentHost($input['content'],'1');
            if ( empty($input['id']) ) {
                $dbResult = $weixinMaterialWechatService->add($input,false);
                if($dbResult){
                    success('添加图文成功','/merchants/wechat/materialWechat');
                }
            } else {
                $dbResult = $weixinMaterialWechatService->update($input['id'],$input);
                success('修改图文成功','/merchants/wechat/materialWechat');
            }
        }

        // 查询详情数据
        $detail = $id ? $weixinMaterialWechatService->getRowById($id) : [];

        if($detail){
            $detail['content'] = $detail['content'];
            $detail['content'] = ProductModule::addProductContentHost($detail['content'],'1');
        }
        return view('merchants.wechat.materialWechatSingle', [
            'title'     => '图文素材 - 微信图文 - 单条图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'detail'    => $detail,
        ]);
    }

    /**
     * 图文素材 - 微信图文 - 多条图文
     *
     * @param  Request                     $request
     * @param  WeixinMaterialWechatService $weixinMaterialWechatService
     * @param  integer                     $id
     * @return view
     */
    public function materialWechatMulti(Request $request, WeixinMaterialWechatService $weixinMaterialWechatService, $id = 0)
    {
        // 店铺id
        $wid = session('wid');

        // 新增/编辑
        if ( $request->isMethod('post') ) {

            $input = $request->only(['id','type','title','cover','author','show_cover_pic','content_source_url','content_source_title','digest','content']);
            $input['wid'] = $wid;
            //先取出要添加的第一条信息
            $savedata = [];
            $saveData['wid']                  = $wid;
            $saveData['type']                 = $input['type'];
            $saveData['title']                = $input['title'][0];
            $saveData['cover']                = $input['cover'][0];
            $saveData['author']               = $input['author'][0];
            $saveData['show_cover_pic']       = $input['show_cover_pic'][0];
            $saveData['content_source_title'] = $input['content_source_title'][0];
            $saveData['content_source_url']   = $input['content_source_url'][0];
            $saveData['digest']               = $input['digest'];
            $saveData['content']              = $input['content'][0];

            // 定义验证规则
            $rules = [
                'title'   => 'required|max:64',
                'author'  => 'max:20',
                //'content' => 'required|max:20000',
            ];
            // 定义错误消息
            $messages = [
                'title.required'   => '请填写标题',
                'title.max'        => '标题长度限制64以内',
                'author.max'       => '作者长度限制20以内',
                'content.required' => '请填写内容',
                //'content.max'      => '内容长度限制20000以内',
            ];
            // 执行验证
            $validator = Validator::make($saveData, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $count = count($input['title']);
            if ( $input['id'][0] == 0 ) {
                //先插入第一条数据，生成的id为其他多图文数据的parent_id
                $dbResult = $weixinMaterialWechatService->add($saveData,false);
                if($dbResult){
                    for($i=1; $i<=($count-1);$i++){
                        $otherData['wid']                  = $wid;
                        $otherData['parent_id']            = $dbResult;
                        $otherData['type']                 = $input['type'];
                        $otherData['title']                = $input['title'][$i];
                        $otherData['cover']                = $input['cover'][$i];
                        $otherData['author']               = $input['author'][$i];
                        $otherData['show_cover_pic']       = $input['show_cover_pic'][$i];
                        $otherData['content_source_title'] = $input['content_source_title'][$i];
                        $otherData['content_source_url']   = $input['content_source_url'][$i];
                        $otherData['digest']               = $input['digest'];
                        $otherData['content']              = $input['content'][$i];
                        $weixinMaterialWechatService->add($otherData,false);
                    }
                    success('添加图文成功','/merchants/wechat/materialWechat');
                }
            } else {
                $weixinMater = WeixinMaterialWechat::where('id', $input['id'][0])->orWhere('parent_id', $input['id'][0])->pluck('id');
                $delIds = $weixinMater->diff($input['id'])->values()->all();
                for($i=0; $i<$count;$i++){
                    $saveData['title']                = $input['title'][$i];
                    $saveData['cover']                = $input['cover'][$i];
                    $saveData['author']               = $input['author'][$i];
                    $saveData['show_cover_pic']       = $input['show_cover_pic'][$i];
                    $saveData['content_source_title'] = $input['content_source_title'][$i];
                    $saveData['content_source_url']   = $input['content_source_url'][$i];
                    $saveData['digest']               = $input['digest'];
                    $saveData['content']              = $input['content'][$i];
                    if ($input['id'][$i] == 0) {
                        $id = $input['id'][1] ?? 0;
                        $childData = $weixinMaterialWechatService->getRowById($id);
                        if ($childData) {
                            $saveData['parent_id'] = $childData['parent_id'] == 0 ? $childData['id'] : $childData['parent_id'];
                            $weixinMaterialWechatService->add($saveData, false);
                        }
                    } else {
                        $dbResult[] = $weixinMaterialWechatService->update($input['id'][$i], $saveData);
                    }
                }
                if($dbResult){
                    if ($delIds) {
                        $weixinMaterialWechatService->deleteArr($delIds);
                    }
                    success('修改图文成功','/merchants/wechat/materialWechat');
                }
            }

            $dbResult && success();
            error();
        }

        // 查询详情数据
        if ( $id ) {
            $detail = $weixinMaterialWechatService->getRowById($id);
            $detail['content'] = $detail['content'];
            $detail['_child'] = $weixinMaterialWechatService->getChildList($id);
        } else {
            $detail = [];
            $detail['_child'] = [];
        }
        return view('merchants.wechat.materialWechatMulti', [
            'title'     => '图文素材 - 微信图文 - 多条图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'detail'    => $detail,
        ]);
    }

    /**
     * [materialWechatMultiDel 微信图文删除]
     * @param  WeixinMaterialWechatService $weixinMaterialWechatService [description]
     * @param  [type]                      $id                          []
     * @return [type]                                                   [description]
     */
    public function materialWechatMultiDel( WeixinMaterialWechatService $weixinMaterialWechatService,$id)
    {
        $wid = session('wid');
        $dbResult = $weixinMaterialWechatService->del($id);
        if($dbResult){
            success();
        }else{
            error();
        }
    }

    /**
     * 图文素材 - 高级图文
     *
     * @param  Request                       $request
     * @param  WeixinMaterialAdvancedService $weixinMaterialAdvancedService
     * @return view
     */
    public function materialAdvanced(Request $request, WeixinMaterialAdvancedService $weixinMaterialAdvancedService)
    {
        // 店铺id
        $wid = session('wid');
        $where['wid'] = $wid;
        // 查询高级单条图文列表
        list($list,$pageHtml) = $weixinMaterialAdvancedService->getAllList($wid);
        $list = $list['data'] ?? [];
        // 转为树形结构
        $list = $weixinMaterialAdvancedService->listToTree($list);

        return view('merchants.wechat.materialAdvanced', [
            'title'     => '图文素材 - 高级图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list,
            'pageHtml'  => $pageHtml,
        ]);
    }

    /**
     * 图文素材 - 高级图文 - 单条图文
     *
     * @param  Request                       $request
     * @param  WeixinMaterialAdvancedService $weixinMaterialAdvancedService
     * @param  integer                       $id
     * @return view
     */
    public function materialAdvancedSingle(Request $request, WeixinMaterialAdvancedService $weixinMaterialAdvancedService, $id = 0)
    {
        // 店铺id
        $wid = session('wid');
        // 新增/编辑
        if ( $request->isMethod('post') ) {
            $input = $request->only(['id','type','title','cover','author','show_cover_pic','content_source_url','content_source_title','digest','content']);

            $saveData = [];
            $saveData['wid']        = $wid;
            $saveData['type']       = $input['type'];
            $saveData['title']      = $input['title'];
            $saveData['digest']     = $input['digest'] ?? '';
            $saveData['cover']      = $input['cover'] ?? '';
            $saveData['href']       = $input['content_source_url'];
            $saveData['href_title'] = $input['content_source_title'];
            // 定义验证规则
            $rules = [
                'title'   => 'required|max:64',
                'href' => 'required',
            ];
            // 定义错误消息
            $messages = [
                'title.required'   => '请填写标题',
                'title.max'        => '标题长度限制64以内',
                'href.required' => '请设置链接地址',
            ];
            // 执行验证
            $validator = Validator::make($saveData, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }

            if ( $input['id'] == 0 ) {
                $dbResult = $weixinMaterialAdvancedService->add($saveData,false);
                if($dbResult){
                    success('添加成功','/merchants/wechat/materialAdvanced');
                }
            } else {
                $dbResult = $weixinMaterialAdvancedService->update($input['id'],$saveData);
                if($dbResult){
                    success('修改图文成功','/merchants/wechat/materialAdvanced');
                }
            }

            $dbResult && success();
            error();
        }

        // 查询详情数据
        $detail = $id ? $weixinMaterialAdvancedService->getRowById($id) : [];
        $detail['created_at'] = isset($detail['created_at']) ? substr($detail['created_at'], 0, 10) : date('Y-m-d');
        $detail['_child'] = $detail['_child'] ?? [];
        return view('merchants.wechat.materialAdvancedSingle', [
            'title'     => '图文素材 - 高级图文 - 单条图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'detail'    => $detail,
        ]);
    }

    /**
     * 图文素材 - 高级图文 - 多条图文
     *
     * @param  Request                       $request
     * @param  WeixinMaterialAdvancedService $weixinMaterialAdvancedService
     * @param  integer                       $id
     * @return view
     */
    public function materialAdvancedMulti(Request $request, WeixinMaterialAdvancedService $weixinMaterialAdvancedService, $id = 0)
    {
        // 店铺id
        $wid = session('wid');

        // 新增/编辑
        if ( $request->isMethod('post') ) {

            $input = $request->only(['id','type','title','cover','author','show_cover_pic','content_source_url','content_source_title','digest','content']);

            //先取出要添加的第一条信息
            $saveData = [];
            $saveData['wid']                  = $wid;
            $saveData['type']                 = $input['type'];
            $saveData['title']                = $input['title'][0];
            $saveData['cover']                = $input['cover'][0];
            // $saveData['author']               = $input['author'];
            // $saveData['show_cover_pic']       = $input['show_cover_pic'];
            $saveData['href_title'] = $input['content_source_title'][0];
            $saveData['href']   = $input['content_source_url'][0];
            // $saveData['digest']               = $input['digest'];
            // $saveData['content']              = $input['content'];

            // 定义验证规则
            $rules = [
                'title'   => 'required|max:64',
                'cover'     => 'required',
                'href'  => 'required',
            ];
            // 定义错误消息
            $messages = [
                'title.required'   => '请填写标题',
                'title.max'        => '标题长度限制64以内',
                'cover.required'  => '请添加图片',
                'href.required' => '请添加链接',
            ];
            // 执行验证
            $validator = Validator::make($saveData, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $count = count($input['title']);
            if ( empty($input['id'][0]) ) {
                //先插入第一条数据，生成的id为其他多图文数据的parent_id
                $dbResult = $weixinMaterialAdvancedService->add($saveData);
                if($dbResult){
                    for($i=1; $i<=($count-1);$i++){
                        $otherData['wid']        = $wid;
                        $otherData['parent_id']  = $dbResult;
                        $otherData['type']       = $input['type'];
                        $otherData['title']      = $input['title'][$i];
                        $otherData['cover']      = $input['cover'][$i];
                        $otherData['href_title'] = $input['content_source_title'][$i];
                        $otherData['href']       = $input['content_source_url'][$i];

                        $weixinMaterialAdvancedService->add($otherData);
                    }
                    success('添加多条图文成功','/merchants/wechat/materialAdvanced');
                }
            } else {
                $weixinMater = WeixinMaterialAdvanced::where('id', $input['id'][0])->orWhere('parent_id', $input['id'][0])->pluck('id');
                $delIds = $weixinMater->diff($input['id'])->values()->all();
                for($i=0; $i<$count;$i++){
                    $saveData['title']      = $input['title'][$i];
                    $saveData['cover']      = $input['cover'][$i];
                    $saveData['href_title'] = $input['content_source_title'][$i];
                    $saveData['href']       = $input['content_source_url'][$i];
                    if ($input['id'][$i] == 0) {
                        $id = $input['id'][1] ?? 0;
                        $childData = $weixinMaterialAdvancedService->getRowById($id);
                        if ($childData) {
                            $saveData['parent_id'] = $childData['parent_id'] == 0 ? $childData['id'] : $childData['parent_id'];
                            $weixinMaterialAdvancedService->add($saveData, false);
                        }
                    } else {
                        $dbResult[] = $weixinMaterialAdvancedService->update($input['id'][$i], $saveData);
                    }
                }
                if($dbResult){
                    if ($delIds) {
                        $weixinMaterialAdvancedService->deleteArr($delIds);
                    }
                    success('修改多条图文成功','/merchants/wechat/materialAdvanced');
                }
            }

            $dbResult && success();
            error();
        }

        // 查询详情数据
        if ( $id ) {
            $detail = $weixinMaterialAdvancedService->getRowById($id);
            $detail['_child'] = $weixinMaterialAdvancedService->getChildList($id);
        } else {
            $detail = [];
            $detail['_child'] = [];
        }

        return view('merchants.wechat.materialAdvancedMulti', [
            'title'     => '图文素材 - 高级图文 - 多条图文',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'materialWechat',
            'bodyClass' => ' class=oneNav_right',
            'detail'    => $detail,
        ]);
    }

    /**
     * [materialAdvancedMultiDel 高级图文删除]
     * @param  WeixinMaterialAdvancedService $weixinMaterialAdvancedService [description]
     * @param  [type]                        $id                            [description]
     * @return [type]                                                       [description]
     */
    public function materialAdvancedMultiDel(WeixinMaterialAdvancedService $weixinMaterialAdvancedService,$id)
    {
        $wid = session('wid');
        $dbResult = $weixinMaterialAdvancedService->del($id);
        if($dbResult){
            success();
        }else{
            error();
        }
    }

    /**
     * 定时发送
     * @return [type] [description]
     */
    public function timerSend(){
        return view('merchants.wechat.timer_send',array(
            'title'=>'定时发送',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'timersend',
            'bodyClass' => ' class=oneNav_right'
        ));
    }

    /**
     * 历史消息
     * @return [type] [description]
     */
    public function historyMsg(){
        return view('merchants.wechat.history_msg',array(
            'title'=>'历史消息',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'historymsg',
            'bodyClass' => ' class=oneNav_right'
        ));
    }

    /**
     * 自动回复
     * @return view
     */
    public function replySet(Request $request)
    {
        // 店铺id
        $wid = session('wid');
        $weixinReplyRuleService = new WeixinReplyRuleService();

        //获取全部数据(回复规则，关键字，回复内容)
        list($list,$pageHtml) = $weixinReplyRuleService->getAllList($wid,['type' => 1]);
        // 数据处理
        $list = $weixinReplyRuleService->makeDatas($list);

        return view('merchants.wechat.replySet', [
            'title'      => '自动回复',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'replySet',
            'bodyClass'  => ' class=oneNav_right',
            'list'       => $list,
            'pageHtml'   => $pageHtml,
        ]);
    }

    /**
     * Author: MeiJay
     * 回复类型：全部回复 随机回复
     */
    public function replyType(Request $request)
    {
        $input = $request->only(['id', 'reply_all']);
        // 加入店铺id
        $input['wid'] = session('wid');

        // 定义验证规则
        $rules = [
            'id'  => 'required',
            'wid' => 'required',
            'reply_all' => 'required|in:1,0',
        ];
        // 定义错误消息
        $messages = [
            'wid.required'  => '店铺异常',
            'reply_all.required' => '参数丢失',
            'reply_all.in'       => '参数错误',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }
        $weixinReplyRuleService = new WeixinReplyRuleService();
        // 编辑
        $returnData = $weixinReplyRuleService->update($input['id'],$input);
        if($returnData['errcode'] == 0){
            success();
        }else {
            error();
        }
    }



    /**
     * 添加/编辑回复规则
     * @param  Request $request  [http请求类实例]
     * @param  [type]  $ruleType [规则类型：1自动回复；2关注时回复；3每周回复]
     * @return json
     */
    public function replyRuleAdd(Request $request, $ruleType)
    {
        // 接收参数
        $input = $request->only(['id', 'name']);
        $input['type'] = $ruleType;

        // 加入店铺id
        $input['wid'] = session('wid');

        // 定义验证规则
        $rules = [
            'wid'  => 'required',
            'type' => 'required|in:1,3',
            'name' => 'required|max:30',
        ];
        // 定义错误消息
        $messages = [
            'wid.required'  => '店铺异常',
            'type.required' => '参数丢失',
            'type.in'       => '参数错误',
            'name.required' => '请填写规则名称',
            'name.max'      => '规则名称长度限制30以内',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // 数据库和redis操作
        //$weixinReplyRuleService = D('WeixinReplyRule', 'wid', $input['wid'], ['type' => $ruleType]);
        $weixinReplyRuleService = new WeixinReplyRuleService();
        if ( $input['id'] ) {
            // 编辑
            $returnData = $weixinReplyRuleService->update($input['id'],$input);
            if($returnData['errcode'] == 0){
                $result = true;
            }else {
                $result = false;
            }

        } else {
            // 新增
            $result = $weixinReplyRuleService->add($input);
            $input['id'] = $result;
        }
        if ( $result ) {
            success('操作成功', '', $input['id']);
        }

        error();
    }

    /**
     * 删除回复规则
     *
     * @param  Request $request [http请求类实例]
     * @return json
     */
    public function replyRuleDel(Request $request)
    {
        // 接收参数
        $input = $request->only(['id', 'type']);

        // 定义验证规则
        $rules = [
            'id'   => 'required|exists:weixin_reply_rule',
            'type' => 'required|in:1,3',
        ];
        // 定义错误消息
        $messages = [
            'id.required'   => '参数丢失',
            'id.exists'     => '规则不存在',
            'type.required' => '参数丢失',
            'type.in'       => '类型不合法',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }
        // 数据库和redis操作
        $weixinReplyRuleService = new WeixinReplyRuleService();
        $dbResult = $weixinReplyRuleService->del($input['id']);

        if ( $dbResult ) {
            // 删除规则对应的关键字
            D('WeixinReplyKeyword')->model->wheres(['rule_id' => $input['id']])->delete();
            //删除规则对应的回复内容
            D('WeixinReplyContent')->model->wheres(['rule_id' => $input['id']])->delete();
            success('操作成功');
        }

        error();
    }

    /**
     * 添加/编辑关键词
     *
     * @param  Request $request  [http请求类实例]
     * @param  [type]  $ruleType [规则类型：1自动回复；2关注时回复；3每周回复]
     * @return json
     */
    public function replyKeywordAdd(Request $request, $ruleType)
    {
        // 接收参数
        $input = $request->only(['id', 'rule_id', 'type', 'keyword']);

        // 加入店铺id
        $input['wid'] = session('wid');

        // 定义验证规则
        $rules = [
            'wid'     => 'required',
            'rule_id' => 'required|exists:weixin_reply_rule,id|count:weixin_reply_keyword,NULL,' . $input['id'] . ',20,deleted_at,NULL,wid,' . $input['wid'],
            'keyword' => 'required|max:30|unique:weixin_reply_keyword,NULL,' . $input['id'] . ',id,deleted_at,NULL,wid,' . $input['wid'],
            'type'    => 'required|in:0,1',
        ];
        // 定义错误消息
        $messages = [
            'wid.required'     => '店铺异常',
            'rule_id.required' => '参数丢失',
            'rule_id.exists'   => '规则不存在',
            'rule_id.count'    => '单个回复规则最多支持20个关键词',
            'keyword.required' => '请填写关键词',
            'keyword.max'      => '关键词长度限制30以内',
            'keyword.unique'   => '关键词重复',
            'type.required'    => '请选择类型',
            'type.in'          => '类型参数错误',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // mysql操作
        $weixinReplyKeywordService = D('WeixinReplyKeyword');
        if ( $input['id'] ) {
            // 编辑
            $dbResult = $weixinReplyKeywordService->model->wheres(['id'=>$input['id']])->update($input);
        } else {
            // 新增
            $dbResult = $weixinReplyKeywordService->model->insertGetId($input);
            $input['id'] = $dbResult;
        }

        // 同步redis数据
        if ( $dbResult ) {
            $obj = $weixinReplyKeywordService->model->wheres(['rule_id' => $input['rule_id']])->order('id ASC')->get();
            $redisDatas = [];
            if($obj){
                $redisDatas = $obj->toArray();
            }
            $weixinReplyRuleService = new WeixinReplyRuleService();
            //更新redis数据
            $weixinReplyRuleService->relationSave($input['rule_id'],$redisDatas);
            $dbResult && success('操作成功', '', $input['id']);
        }

        error();
    }

    /**
     * 删除关键词
     *
     * @param  Request $request [http请求类]
     * @return json
     */
    public function replyKeywordDel(Request $request)
    {
        // 接收参数
        $input = $request->only(['id', 'type', 'rule_id']);

        // 定义验证规则
        $rules = [
            'id'      => 'required|exists:weixin_reply_keyword',
            'rule_id' => 'required|exists:weixin_reply_rule,id',
            'type'    => 'required|in:1,2,3',
        ];
        // 定义错误消息
        $messages = [
            'id.required'      => '参数丢失',
            'id.exists'        => '关键词不存在',
            'rule_id.required' => '参数丢失',
            'rule_id.exists'   => '规则不存在',
            'type.required'    => '参数丢失',
            'type.in'          => '类型不合法',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // mysql操作
        $weixinReplyKeywordService = D('WeixinReplyKeyword');
        $dbResult = $weixinReplyKeywordService->model->wheres(['id' => $input['id']])->delete();

        if ( $dbResult ) {
            // 同步redis数据
            $obj = $weixinReplyKeywordService->model->wheres(['rule_id' => $input['rule_id']])->order('id ASC')->get();
            $redisDatas = [];
            if($obj){
                $redisDatas = $obj->toArray();
            }
            $weixinReplyRuleService = new WeixinReplyRuleService();
            $weixinReplyRuleService->relationSave($input['rule_id'],$redisDatas);
            $dbResult && success('操作成功', '', $input['id']);
        }

        error();
    }

    /**
     * 添加/编辑回复内容
     *
     * @param  Request $request  [http请求类实例]
     * @param  integer $ruleType [规则类型：1自动回复；2关注时回复；3每周回复]
     * @return json
     */
    public function replyContentAdd(Request $request, $ruleType)
    {
        // 接收参数
        $input = $request->only(['id', 'type', 'rule_id']);
        // 加入店铺id
        $input['wid'] = session('wid');
        // 定义验证规则
        $rules = [
            'wid'     => 'required',
            'rule_id' => 'required|exists:weixin_reply_rule,id|count:weixin_reply_content,NULL,' . $input['id'] . ',10,deleted_at,NULL,wid,' . $input['wid'],
            'type'    => 'required|in:1,2,3,4,5,6,7',
        ];
        // 定义错误消息
        $messages = [
            'wid.required'     => '店铺异常',
            'rule_id.required' => '参数丢失',
            'rule_id.exists'   => '规则不存在',
            'rule_id.count'    => '单个回复规则最多支持10条回复',
            'type.required'    => '请选择类型',
            'type.in'          => '类型参数错误',
        ];
        /**
         * 根据不同类型追加对应判断
         *
         * 通过加content_前缀实现数据接收，接收后做字符串截取放入config中
         *
         * 类型说明：
         * 1文本（表情、链接、文字）；2图片；3(多)图文（微信图文，高级图文）；4语音；5音乐；6其他（1商品；2商品分组；3微页面；4微页面分类；5店铺主页；6会员主页）
         */
        $receive = [];
        switch ( $input['type'] ) {
            // 1文本（表情、链接、文字）
            case '1':
                $receive = ['content_content'];
                $rules['config.content'] = 'required|max:300';
                $messages['config.content.required'] = '请填写内容';
                $messages['config.content.max']      = '内容长度限制在300以内';
                break;
            // 2图片
            case '2':
                $receive = ['content_url'];
                $rules['config.url'] = 'required';
                $messages['config.url.required'] = '请选择图片';
                break;
            // 3(多)图文（微信图文，高级图文）
            case '3':
                $receive = ['content_type', 'content_id', 'content_title'];
                $rules['config.type']  = 'required|in:1,2';
                $rules['config.id']    = 'required';
                $rules['config.title'] = 'required';
                $messages['config.type.required']  = '类型错误';
                $messages['config.type.in']        = '类型错误';
                $messages['config.id.required']    = '图文错误';
                $messages['config.title.required'] = '标题缺失';
                break;
            // 4语音
            case '4':
                $receive = ['content_url', 'content_media_id'];
                $rules['config.url']      = 'required';
                $rules['config.media_id'] = 'required';
                $messages['config.url.required']      = '请选择语音文件';
                $messages['config.media_id.required'] = '素材id缺失';
                break;
            // 5音乐
            case '5':
                $receive = ['content_title', 'content_desc', 'content_img', 'content_normal', 'content_hd'];
                $rules['config.title']    = 'required';
                $rules['config.desc']     = 'required';
                $rules['config.img']      = 'required';
                $rules['config.normal']   = 'required';
                $rules['config.hd']       = 'required';
                $messages['config.title.required']    = '请填写音乐标题';
                $messages['config.desc.required']     = '请填写音乐描述';
                $messages['config.img.required']      = '请选择缩略图';
                $messages['config.normal.required']   = '请填写普通音质音乐地址';
                $messages['config.hd.required']       = '请填写高清音质音乐地址';
                break;
            // 6其他（1商品；2商品分组；3微页面；4微页面分类；5店铺主页；6会员主页,7微信客服）
            case '6':
                $receive = ['content_type', 'content_id', 'content_title','content_activeType'];
                $rules['config.type']  = 'required|in:1,2,3,4,5,6,7';
                $rules['config.id']    = 'required';
                $rules['config.title'] = 'required';
                $messages['config.type.required']  = '类型错误';
                $messages['config.type.in']        = '类型错误';
                $messages['config.id.required']    = '数据错误';
                $messages['config.title.required'] = '标题缺失';
                break;
            case '7':
                $receive = ['content_content'];
                $rules['config.content'] = 'required|max:300';
                $messages['config.content.required'] = '请填写内容';
                $messages['config.content.max']      = '内容长度限制在300以内';
                break;
            default:
                error('类型异常');
                break;
        }
        $config = $receive ? $request->only($receive) : [];

        //上传图片到微信服务器
        if($input['type'] == 2){
            $apiService = new ApiService();
            $filename  = $config['content_url'];
            $result = $apiService->uploadFile($input['wid'],substr($filename,1),'image',2);
            $config['content_media_id'] = $result['media_id'];
        }

        if ( $config ) {
            foreach ($config as $key => $value) {
                $input['config'][substr($key, 8)] = $value ?? 0;
            }
        }
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // mysql操作
        $input['config'] = json_encode($input['config']);
        $weixinReplyContentService = D('WeixinReplyContent');
        if ( $input['id'] ) {
            // 编辑
            $dbResult = $weixinReplyContentService->model->wheres(['id'=>$input['id']])->update($input);
        } else {
            // 新增
            $dbResult = $weixinReplyContentService->model->insertGetId($input);
            $input['id'] = $dbResult;
        }

        // 同步redis数据
        if ( $dbResult ) {
            $obj = $weixinReplyContentService->model->wheres(['rule_id' => $input['rule_id']])->order('id ASC')->get();
            $redisDatas = [];
            if($obj){
                $redisDatas = $obj->toArray();
            }
            $weixinReplyRuleService = new WeixinReplyRuleService();
            $weixinReplyRuleService->relationSave($input['rule_id'],$redisDatas,2);
            $dbResult && success('操作成功', '', $input['id']);
        }

        error();
    }

    /**
     * 删除回复内容
     *
     * @param  Request $request [http请求类实例]
     * @return json
     */
    public function replyContentDel(Request $request)
    {
        // 接收参数
        $input = $request->only(['id', 'type', 'rule_id']);

        // 定义验证规则
        $rules = [
            'id'      => 'required|exists:weixin_reply_content',
            'rule_id' => 'required|exists:weixin_reply_rule,id',
            'type'    => 'required|in:1,2,3',
        ];
        // 定义错误消息
        $messages = [
            'id.required'      => '参数丢失',
            'id.exists'        => '回复内容不存在',
            'rule_id.required' => '参数丢失',
            'rule_id.exists'   => '规则不存在',
            'type.required'    => '参数丢失',
            'type.in'          => '类型不合法',
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // mysql操作
        $weixinReplyContentService = D('WeixinReplyContent');
        $dbResult = $weixinReplyContentService->model->wheres(['id' => $input['id']])->delete();

        // 同步redis数据
        if ( $dbResult ) {
            $obj = $weixinReplyContentService->model->wheres(['rule_id' => $input['rule_id']])->order('id ASC')->get();
            $redisDatas = [];
            if($obj){
                $redisDatas = $obj->toArray();
            }
            $weixinReplyRuleService = new WeixinReplyRuleService();
            $weixinReplyRuleService->relationSave($input['rule_id'],$redisDatas,2);
            $dbResult && success('操作成功', '', $input['id']);
        }

        error();
    }

    /**
     * 关注时回复
     *
     * @return view
     */
    public function subscribeReply()
    {
        // 店铺id
        $wid = session('wid');
        // 查询数据
        $weixinReplyRuleService = new WeixinReplyRuleService();
        list($list) = $weixinReplyRuleService->getAllList($wid,['type' => 2]);
        // 初始化默认值
        if ( empty($list['data']) ) {
            $weixinReplyRuleService->add([
                'wid'  => $wid,
                'name' => '关注时回复',
                'type' => 2,
            ]);
            list($list) = $weixinReplyRuleService->getAllList($wid);
        }
        // 数据处理
        $list = $weixinReplyRuleService->makeDatas($list);
        return view('merchants.wechat.subscribeReply', [
            'title'     => '关注时回复',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'replySet',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list,
        ]);
    }

    /**
     * 消息托管
     * @return [type] [description]
     */
    public function messages(){
        // 店铺id
        $wid = session('wid');
        // 查询数据
        $weixinReplyRuleService = new WeixinReplyRuleService();
        list($list) = $weixinReplyRuleService->getAllList($wid,['type' => 3]);
        // 初始化默认值
        if ( empty($list['data']) ) {
            $weixinReplyRuleService->add([
                'wid'  => $wid,
                'name' => '消息托管回复',
                'type' => 3,
            ]);
            list($list) = $weixinReplyRuleService->getAllList($wid);
        }
        // 数据处理
        $list = $weixinReplyRuleService->makeDatas($list);
        return view('merchants.wechat.messages',array(
            'title'=>'消息托管',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'messages',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list
            ));
    }

    /**
     * 自动回复消息小尾巴
     * @return [type] [description]
     */
    public function messagesTips(){
        return view('merchants.wechat.messages_tips',array(
            'title'=>'小尾巴',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'messagestips',
            'bodyClass' => ' class=oneNav_right'
            ));
    }

    /**
     * 每周回复
     *
     * @return View
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年3月16日 10:01:41
     */
    public function weeklyReply( WeixinReplyRuleService $weixinReplyRuleService ) {
        // 店铺id
        $wid = session('wid');

        // 查询数据
        list($list, $pageHtml) = $weixinReplyRuleService->getAllList($wid);
        // 数据处理
        $list = $weixinReplyRuleService->makeDatas($list);

        return view('merchants.wechat.weeklyReply', [
            'title'     => '每周回复',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'replySet',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list,
            'pageHtml'  => $pageHtml,
        ]);
    }

    /**
     * 快捷短语
     * @return [type] [description]
     */
    public function phrase(){
        return view('merchants.wechat.phrase',array(
            'title'=>'快捷短语',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'phrase',
            'bodyClass' => ' class=oneNav_right'
            ));
    }

    /**
     * 自定义菜单
     *
     * @return View
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年3月16日 10:01:41
     */
    public function menu( ApiService $apiService,Request $request ) {
        $input = $request->only(['id', 'parent_id', 'name', 'value', 'content']);
        $userInfo = session('userInfo');
        // 店铺id
        $wid = session('wid');
        $weixinCustomMenuService = new WeixinCustomMenuService();
        $customMenus = $weixinCustomMenuService->getMenuList($wid);

        // 数据处理
        return view('merchants.wechat.menu', [
            'title'       => '自定义菜单',
            'leftNav'     => $this->leftNav,
            'slidebar'    => 'menu',
            'bodyClass'   => ' class=oneNav_right',
            'customMenus' => $customMenus
        ]);
    }

    /**
     * [menuSave 微信自定义菜单保存数据操作]
     * @param  WeixinCustomMenuService $WeixinCustomMenuService [注入的微信自定义菜单服务类]
     * @return [type]                                           [description]
     */
    public function menuSave( ApiService $apiService, Request $request )
    {

        $callback = $request->input('callback');

        $input = $request->only(['id', 'parent_id', 'name', 'value', 'content','type','activityType']);
        //店铺id
        $wid = session('wid');

        //进行相关的增，删，改操作
        $weixinCustomMenuService = new WeixinCustomMenuService();
        $weixinCustomMenuService->save($wid,$input);
    }

    /**
     * [createMenu 生成微信自定义菜单]
     * @return [type] [description]
     */
    public function createMenu(ApiService $apiService)
    {
        // 店铺id
        $wid = session('wid');
        $WeixinCustomMenuService = new WeixinCustomMenuService();
        $customMenus = $WeixinCustomMenuService->getWeixinMenuData($wid);
        $apiService->customMenuCreate($wid,$customMenus);
        success('微信自定义菜单已更新');
    }

    /**
     *  微信开放平台未授权时的设置页面
     */
    public function wxsettled(Request $request)
    {
        //获取微信开放平台的预授权码
        $authorizationService = new AuthorizationService();
        $re = $authorizationService->getPreAuthCode();
        $pre_auth_code = $re['pre_auth_code'] ?? '';
        $authUrl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$this->AppId.'&pre_auth_code='.$pre_auth_code.'&redirect_uri='.config('app.url').'merchants/wechat/weixinSet';
        return view('merchants.wechat.wxsettled',[
            'title' => '微信绑定',
            'leftNav'   => $this->leftNav,
            'bodyClass' => 'class=oneNav_right',
            'authUrl'   => $authUrl
        ]);
    }

    //点击微信授权跳转接口
    public function authRedirect(Request $request)
    {
        $type = $request->input('type') ?? '';
        $authUrl = '';
        //获取微信开放平台的预授权码
        $authorizationService = new AuthorizationService();
        $re = $authorizationService->getPreAuthCode();
        $pre_auth_code = $re['pre_auth_code'] ?? '';
        $authUrl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$this->AppId.'&pre_auth_code='.$pre_auth_code.'&redirect_uri='.config('app.url').'merchants/wechat/weixinSet/'.$type;
        success('',$authUrl);
    }

    /**
     * [微信开放平台授权成功的显示页面]
     * @param  Request              $request              [description]
     * @param  AuthorizationService $authorizationService [description]
     * @return [type]                                     [description]
     */
    public function weixinSet(Request $request,AuthorizationService $authorizationService)
    {
        $type = '';
        $param = $request->route('param');
        $wid = session('wid');
        $auth_code = '';
        $info = [];
        $uid = session('userInfo')['id'];
        $service_type_info = 0; //公众号类别标识 （0：订阅号 1：升级后的订阅号 2：服务号）
        if( !empty($request->input('auth_code')) ){
            $auth_code = $request->input('auth_code');
            if($param == 'updateauthorized'){
                $type = 'updateauthorized';
            }

            //获取authorizer_access_token
            $ret = $authorizationService->getAuthrizerAccessToken($auth_code,$wid);

            //读取component_access_token的redis值
            $wechatRedis = new Wechat('component_access_token');
            $component_access_token = $wechatRedis->get();

            //获取公众号信息
            $info = $authorizationService->getAuthUsers($ret['authorizer_appid'],$component_access_token,$wid,$uid,$type);

            $nick_name = $info['authorizer_info']['nick_name'];
            $wechat_id = $info['authorizer_info']['alias'];
            $service_type_info = $info['authorizer_info']['service_type_info']['id'];
            //添加重新授权标志
            $authRedis = new AuthorizationRedis('wid_'.$wid.'_reset_flag');
            $authRedis->set(time());

        }
        //直接读取数据库记录
        if(empty($info)){
            $service = new WeChatShopConfService();
            $info = $service->getRowByWid($wid);
            if (empty($info)) {
                return redirect('/merchants/wechat/wxsettled');
            }
            $nick_name = $info['name'];
            $wechat_id = $info['wechat_id'];
            $service_type_info = $info['service_type_info'];
        }

        return view('merchants.wechat.wechatSet',[
            'title'     => '公司微信帐户',
            'nick_name' => $nick_name,
            'wechat_id' => $wechat_id,
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'setting',
            'bodyClass' => ' class=oneNav_right',
            'service_type_info' => $service_type_info
        ]);
    }


    /**
     * [解除绑定]
     * 清空关键词，自动回复内容，图文，菜单相关数据
     * @return [type] [description]
     */
    public function relieveAuth()
    {
        $wid = session('wid');
		//update by wuxiaoping 2018.05.02
        $authorizationService = new AuthorizationService();
        $result = $authorizationService->relieveAuth($wid);
        if($result){
            success('操作成功','/merchants/wechat/wxsettled');
        }else{
            error();
        }
    }

    //授权失败页面
    public function errorAuth()
    {
        return view('merchants.wechat.errorAuth',[
                'title' => '微信授权失败',
            ]);
    }


    /*************************************预约管理开始***********************************************/

    /**
     * 预约管理列表
     * @return [type] [description]
     */
    public function book()
    {
        $wid = session('wid');
        $bookService = new BookService();
        $usersBookService = new UsersBookService();
        list($list,$pageHtml) = $bookService->getAllList($wid);

        //数据处理
        foreach ($list['data'] as $key => &$value) {
            if ($value['limit_type'] == 0) { //限定时间
                $value['limit_type'] = '限定时间';
                $value['start_time'] = date('Y-m-d',$value['start_time']);
                $value['end_time']   = date('Y-m-d',$value['end_time']);
                $value['limit_num']  = '';
            }else if ($value['limit_type'] == 1) {
                $value['limit_type'] = '限定每日量';
                $value['start_time'] = '';
                $value['end_time']   = '';
                $value['limit_num']  = $value['limit_num'];
            }else if ($value['limit_type'] == 2) {
                $value['limit_type'] = '限定全部总量';
                $value['start_time'] = '';
                $value['end_time']   = '';
                $value['limit_num']  = $value['limit_total'];
            }else {
                $value['limit_type'] = '不限';
                $value['start_time'] = '';
                $value['end_time']   = '';
                $value['limit_num']  = '';
            }
            $returnData = $usersBookService->statistics($wid,0,$value['id']);
            $value['bookTotal']    = $returnData['bookTotal'];
            $value['pendingTotal'] = $returnData['pendingTotal'];
        }
        return view('merchants.wechat.book',[
            'title'     => '预约管理列表',
            'slidebar'  => 'book',
            'leftNav'   => 'wechat',
            'bodyClass' => ' class=oneNav_right',
            'list'      => $list,
            'pageHtml'  => $pageHtml
        ]);
    }

    /**
     * 微页面
     * @return [type] [description]
     */
    public function bookListApi(Request $request)
    {
        $wid = session('wid');
        $input = $request->input() ?? [];
        $input['end_time'] = time();
        $bookService = new BookService();
        list($list,$pageHtml) = $bookService->getAllList($wid,$input,'',true,5);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                $value['url'] = config('app.url').'shop/book/detail/'.$wid.'/'.$value['id'];
            }
        }
        success('','',$list);
    }
    /**
     * 新增/编辑页
     * @return [type] [description]
     */
    public function bookSave(Request $request)
    {
        $input = $request->input();
        $id = $input['id'] ?? 0;
        $wid = session('wid');
        $bookService = new BookService();
        $usersBookService = new UsersBookService();
        if ($request->isMethod('post')) {
            $rule = [
                'keywords'  => 'required',
                'title'     => 'required',
                'cover_img' => 'required',
            ];
            $messages = [
                'keywords.required'  => '请填入触发的关键词或关键词输入格式不正确',
                'title.required'     => '请填入图文消息标题',
                'cover_img.required' => '请先上传封面图片',
            ];
            $validator = Validator::make($input,$rule,$messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if ($input['limit_type'] == 0) {
                $startTime = $input['start_time'] ?? '';
                $endTime   = $input['end_time'] ?? '';
                if (!$startTime || !$endTime) {
                    error('请填入限制时间');
                }
            }
            $contentData = [];
            $formContent  = $input['content'] ?? [];
            $formOther    = $input['other'] ?? [];
            $dropBoxother = $input['dropBoxother'] ?? [];
            if ($formContent) {
                foreach ($formContent as $cKey => $cValue) {
                    if ($cValue['ikey']) {
                        $contentData[] = $cValue;
                    }
                }
            }
            if ($formOther) {
                unset($input['other']);
                foreach ($formOther as $tKey => $tValue) {
                	if ($tValue['ikey']){
                		$contentData[] = $tValue;
                	}
                }
            }
            if ($dropBoxother) {
                unset($input['dropBoxother']);
                foreach ($dropBoxother as $dKey => $dValue) {
                    if ($dValue['ikey']) {
                        $dValue['ival'] = $this->changeCharComma($dValue['ival']); //把字符号转成英文的
                        $contentData[] = $dValue;
                    }
                }
            }

            $input['content'] = json_encode($contentData,JSON_UNESCAPED_UNICODE);

            if ($input['limit_type'] == 1) {
                $statistics = $usersBookService->statistics($wid,0,$id);
                if ($input['limit_num']){
                    if ($statistics['currentTotal'] > $input['limit_num']) {
                        error('设置的每日限制数已小于当前已预约用户数');
                    }
              }
            }

            if ($input['limit_type'] == 2) {
                $statistics = $usersBookService->statistics($wid,0,$id);
                if ($input['limit_total']) {
                    if ($statistics['bookTotal'] > $input['limit_total']) {
                        error('设置的限制总数已小于当前已预约用户数');

                    }
                }
            }

            if($input['limit_type'] == 0)
            {
                $statistics = $usersBookService->statistics($wid,0,$id);
                if ($statistics['timeLimit']){
                    if ($statistics['timeLimit']['book_date'] > $input['end_time']) {
                        error('有未执行的预约不在设置的范围内，无法进行设置');
                    }
                }

            }
            //输入多个关键词时，逗号不统一问题（统一转换成英文的逗号）
            $input['keywords'] = $this->changeCharComma($input['keywords']);
            $input['wid'] = $wid;
            if ($input['limit_type'] == 0) {
                $input['start_time']  = strtotime($input['start_time']);
                $input['end_time']    = strtotime($input['end_time']);
                $input['limit_num']   = 0;
                $input['limit_total'] = 0;
            }else if ($input['limit_type'] == 1) {
                $input['start_time']  = 0;
                $input['end_time']    = 0;
                $input['limit_total'] = 0;
            }else if ($input['limit_type'] == 2) {
                $input['start_time']  = 0;
                $input['end_time']    = 0;
                $input['limit_num'] = 0;
            }
             //数据库处理
            if ($id) {
                $result = $bookService->update($id,$input);
            }else {
                $result = $bookService->add($input);
            }

            //消息提示
            if ($result) {
                success();
            }
            error();
        }
        $bookDataInfo = [];
        if ($id) {
            $bookDataInfo = $bookService->getRowById($id);
            if ($bookDataInfo['limit_type'] == 0) {
               $bookDataInfo['start_time'] = date('Y-m-d',$bookDataInfo['start_time']);
               $bookDataInfo['end_time'] = date('Y-m-d',$bookDataInfo['end_time']);
            }
            $formContent = json_decode($bookDataInfo['content'],true);
            foreach ($formContent as $key => &$value) {
                $value['addType'] = 'content';
                if(!isset($value['iclass']) && !isset($value['shopClass'])){
                    $value['addType'] = 'other';
                }
            }
            $bookDataInfo['content'] = $formContent;

        }
        return view('merchants.wechat.bookSave',[
            'title'        => $id ? '新增预约' : '编辑预约',
            'slidebar'     => 'book',
            'leftNav'      => 'wechat',
            'bodyClass'    => ' class=oneNav_right',
            'bookDataInfo' => $bookDataInfo
        ]);
    }

    /**
     * 转换逗号（统一格式）
     * @return [type] [description]
     */
    public function changeCharComma($charComma)
    {

        if (stripos($charComma, '，')) {
            $string = str_replace('，', ',', $charComma);
        }else if(stripos($charComma, ' ')) {
            $string = str_replace(' ', ',', $charComma);
        }else if(stripos($charComma, ';')) {
            $string = str_replace('; ', ',', $charComma);
        }else if (stripos($charComma, '|')){
            $string = str_replace('| ', ',', $charComma);
        }else{
            $string = $charComma;
        }

        return $string;
    }

    /**
     * 删除预约
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookDel(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            error('请先指定要删除的预约');
        }

        $bookService = new BookService();
        $bookInfo = $bookService->getRowById($id);
        if(empty($bookInfo)){
            error('该预约不存在或已被删除');
        }

        $result = $bookService->del($id);

        if ($result) {
            success('删除成功');
        }
        error('删除失败');
    }


    /**
     * 根据预约id显示详细数据
     */
    public function chaxun(Request $request)
    {
        //接受数据
        $input = $request->input() ?? [];
        $id = $input['id'] ?? 0 ;
        if (!$id) {
            error('操作异常');
        }
        //根据数据查询
        $list = (new UsersBookService()) -> getRowById($id);
        if($list)
        {
            $list['form_content'] = json_decode($list['form_content'],true);

        }else{
            $list = [];
        }

        return view('merchants.wechat.bookDetail',[
            'title' => '预约客户详情',
            'slidebar'  => 'book',
            'leftNav'      => 'wechat',
            'bodyClass'    => ' class=oneNav_right',
            'list'  => $list,
        ]);

    }


    /**
     * 根据预约id修改状态接口
     */
    public function usersAlter(Request $request)
    {
        $input = $request -> input() ?? [];
        $id = $input['id'] ?? 0;
        if($request->isMethod('post'))
        {
            $content= trim($input['content']);
            $status = $input['status'];
            $list = (new UsersBookService()) -> getRowById($id);
            if($list)
            {
                if($list['content'] == $content && $list['status'] == $status){
                    $data['shop_updated'] = $list['shop_updated'];
                }else{
                    $data['shop_updated'] = time();
                }

            }
            $data['content'] = $content;
            $data['status'] = $status;
            $rs  = (new UsersBookService())->update($id,$data);
            $msg = '更新成功';
            $jobData = [
                    'mid'       => $list['mid'],
                    'book_id'   => $list['book_id'],
                    'book_time' => $list['book_date'] . '   '.$list['book_time']
            ];
            if($status == 2 ){
                $jobData['book_type'] = 'mic_subscribe_success';
                (new MessagePushModule($list['wid'], MessagesPushService::ActivityBook))->sendMsg($jobData);
            }else{
                $jobData['content'] = empty($content) ? '无' : $content;
                $jobData['book_type'] = 'mic_subscribe_refuse';
                (new MessagePushModule($list['wid'], MessagesPushService::ActivityBook))->sendMsg($jobData);
            }
            if($rs){
                success($msg);
            }
            error();

        }else{
            //根据数据查询
            if (!$id) {
                error('操作异常');
            }
            $list = (new UsersBookService()) -> getRowById($id);
            return view('merchants.wechat.usersAlter',[
                'title' => '预约客户处理',
                'slidebar'  => 'book',
                'leftNav'      => 'wechat',
                'bodyClass'    => ' class=oneNav_right',
                'list'        => $list,

            ]);
        }


    }
    /**
     * 预约客户列表展示
     */
    public function  userList($book_id,Request $request)
    {
        // 店铺id
        $wid = session('wid') ?? 0;
        if(!$wid)
        {
            error('您还没有权限查看');
        }
        $input = $request->input() ?? [];
        $input['book_id'] = $book_id ?? 0;
        list($list,$page) = (new UsersBookService()) ->getAllList($wid,$input);
        if ($list['data']) {
            $list =  $list['data'];
            foreach($list as $k=>&$v)
            {
                $v['form_content'] = json_decode($v['form_content'],true);
                if($v['shop_updated'])
                {
                  $v['shop_updated'] = date('Y-m-d H:i:s',$v['shop_updated']);
                }

                if($v['form_content']){
                    foreach ($v['form_content'] as $item) {

                        if (isset($item['yclass']) &&  $item['yclass']== 'name'){
                            $v['name'] = $item['yval'];
                        }

                        if (isset($item['yclass']) && $item['yclass'] == 'phones'){
                            $v['phone'] = $item['yval'];
                        }

                    }
                }
            }
        }else {
            $list = [];
        }

        return view('merchants.wechat.userList',[
            'title' => '预约客户列表',
            'slidebar'  => 'book',
            'leftNav'      => 'wechat',
            'bodyClass'    => ' class=oneNav_right',
            'list'  => $list,
            'page' => $page,
            'input' => $input

        ]);
    }
    /**
     * 预约客户删除
     */
    public function delApi(Request $request)
    {
        $wid = session('wid');
        if(!$wid)
        {
            return  '您还没有权限删除';
        }
        $input = $request->input('id') ?? 0;
        if(!$input)
        {
            error('请选择要删除的预约');
        }
        if((new UsersBookService())->del($input)){
            success();
        }
        error();
    }

    /**
     * @author 付国维
     * @date 20171103
     * @desc 预约订单批量导出,导出格式.xls
     */
    public function orderExport(Request $request)
    {
      //接受数据
        $input = $request->input() ?? [];
        if(empty($input))
        {
            error('请选择数据');
        }
        //获取店铺名字
        $wid = session('wid') ?? 0;
        if(empty($wid))
        {
            error('您没有权限导出');
        }
        $UsersBookService = new UsersBookService();
        $list =$UsersBookService  ->getAllList($wid,$input,'',2,false);
        if ($list) {
            foreach($list as $k=>&$v)
            {
                $v['name'] = $v['phone'] = '';
                $formContent = json_decode($v['form_content'],true);
                if($formContent){
                    foreach ($formContent as $value){
                        if($value['yclass'] == 'name') {
                            $v['name'] = $value['yval'];
                        }
                        if($value['yclass'] == 'phones'){
                            $v['phone'] = $value['yval'];
                        }
                        //因为要导出其他用户自定义填写的内容先排除掉预约的日期与时间必填项
                        if ($value['yclass'] <> 'book_date' && $value['yclass'] <> 'book_time' && $value['yclass'] <> 'name' && $value['yclass'] <> 'phones') {
                            $v['fields'][] = ['ykey' => $value['ykey'],'yval' => $value['yval']];
                        }
                    }
                }
                $list[$k]['form_content'] =json_decode($v['form_content'],true);
            }

            //执行导出
            $UsersBookService ->exportExcel1($list);
        }

    }

}
