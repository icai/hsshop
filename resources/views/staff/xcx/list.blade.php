@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/6.1 potential_customers.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/xcxList.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
<div class="main">
    <!-- 状态区域 -->
    <div class="nav">
        <ul>
            <li @if(request('status') === null) class="active" @endif>
                <a href="?">全部</a>
            </li>
            <li @if(request('status') !== null && request('status') == 0) class="active" @endif>
                <a href="?status=0">无操作</a>
            </li>
            <li @if(request('status') !== null && request('status') == 2) class="active" @endif>
                <a href="?status=2">审核中</a>
            </li>
            <li @if(request('status') !== null && request('status') == 3) class="active" @endif>
                <a href="?status=3">审核被拒</a>
            </li>
            <li @if(request('status') !== null && request('status') == 4) class="active" @endif>
                <a href="?status=4">审核成功</a>
            </li>
            <li @if(request('status') !== null && request('status') == 5 && request('expire') != 1) class="active" @endif>
                <a href="?status=5">已发布</a>
            </li>
            <li @if(request('status') !== null && request('status') == 5 && request('expire') == 1) class="active" @endif>
                <a href="?status=5&expire=1">已发布-全部</a>
            </li>
            <li @if(request('status') !== null && request('status') == 1) class="active" @endif>
                <a href="?status=1">已提交代码</a>
            </li>
            <li @if(request('status') !== null && request('status') == 7) class="active" @endif>
                <a href="?status=7">已作废</a>
            </li>
            <li @if(request('status') !== null && request('status') == 8) class="active" @endif>
                <a href="?status=8">已下架</a>
            </li>
            <li style="padding-top: 4px;">
                <button class="btn btn-primary look_btn">查看</button>
            </li>
        </ul>
        
    </div>
    <!-- 状态区域 -->
    <!-- 搜索区域 -->
    <div class="search">
        <form id="code_form" class="search_form" method="get" action="/staff/xcx/list">
            <span>搜索：</span>
            <select name="search_type">
                <option @if(!empty(request('search_type')) && request('search_type') == 'title') selected='selected' @endif value="title">名称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'request_domain') selected='selected' @endif value="request_domain">域名简称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'app_id') selected='selected' @endif value="app_id">appid</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'shop_name') selected='selected' @endif value="shop_name">店铺名称</option>
                <option @if(!empty(request('search_type')) && request('search_type') == 'mphone') selected='selected' @endif value="mphone">手机号码</option>
            </select>
            <input type="text" name="search_value" @if(!empty(request('search_value'))) value="{{request('search_value')}}" @endif>
            <span class="action_time">操作时间</span>
            <input type="text" name="start_at" class="start_time" id="start_time" @if(!empty(request('start_at'))) value="{{request('start_at')}}" @endif>
            <span>-</span>
            <input type="text" name="end_at" class="end_time" id="end_time" @if(!empty(request('end_at'))) value="{{request('end_at')}}" @endif>
            <input type="submit" class="search_btn" value="搜索"></input>
            <a class="get_host_btn">一键获取域名</a>
        </form>
    </div>
    <!-- 搜索区域 -->
    <!-- 表格区域 -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>ID</th>
                    <th>名称</th>
                    <th>模板ID</th>
                    <th>店铺名称</th>
                    <th>手机号码</th>
                    <th>域名简称</th>
                    <th>业务域名</th>
                    <th>appid</th>
                    <th>是否自动发布</th>
                    <th>进度</th>
                    <th>授权时间</th>
                    <th colspan="3">操作</th>
                </tr>
            </thead>
            <tbody>
            @forelse($data[0]['data'] as $v)
                <tr class="tr-id">
                    <th class="xcx-id" scope="row" data-wid="{{$v['wid']}}">
                    <input type="checkbox" class="xcx-id-box" value="{{$v['id']}}"/>
                    {{$v['id']}}
                    </th>
                    <td class="title_merchant" style='position: relative'>
                        <div>{{$v['title']}}</div>
                        @if($v['shop_expire_at']=='0')
                            <div style='position: absolute; right: 10px; top: 10px'><img width='50' src="{{ config('app.source_url') }}staff/hsadmin/images/is_expire.png"/></div>
                        @else
                            <div>{{$v['shop_expire_at']}}</div>
                        @endif
                        @if(isset($v['online_live_status']) && $v['online_live_status'])
                            <div style="color: #f56c6c;">( 直播 )</div>
                        @endif
                    </td>
                    <td>{{ $v['template_id'] }}(版本：{{$v['version'] == '' ? '无' : $v['version']}})</td>
                    <td>
                        {{ $v['shop_name'] }}
                        @if(isset($v['is_fee']) && $v['is_fee']=='0')
                            <img class="logo" src= "{{ config('app.source_url') }}staff/hsadmin/images/mian@2x.png"/>
                        @elseif(isset($v['is_fee']) && $v['is_fee']=='1')
                            <img class="logo" src="{{ config('app.source_url') }}staff/hsadmin/images/fu@2x.png"/>
                        @elseif(isset($v['is_fee']) && $v['is_fee']=='2')
                            <img class="logo " src="{{ config('app.source_url') }}staff/hsadmin/images/zeng@2x.png"/>
                        @endif
                    </td>
                    <td>{{ $v['mobile'] }}</td>
                    <td>{{$v['request_domain']}}</td>
                    <td>{{$v['webview_domain']??''}}</td>
                    <td class="xcx_app_id">{{$v['app_id']}}</td>
                    <td class="xcx_auto">
                        <div class="switch_items" data-id="{{$v['id']}}">
                        @if($v['is_auth_submit']==1)
                            <input type="checkbox" name="status" value="1" class="switch_check" checked/>
                            <label></label>
                        @else
                            <input type="checkbox" name="status" value="0" class="switch_check"/>
                            <label></label>
                        @endif
                        </div>
                    </td>
                    <td>
                        {{$v['status_string']}}<br /><br/>
                        {{$v['status_time']}}
                        @if(isset($v['commit_live_status']) && $v['commit_live_status'])
                            <div style="color: #f56c6c;">( 直播 )</div>
                        @endif
                    </td>
                    <td>
                        <br /><br/>
                        {{$v['created_at']}}
                    </td>
                    <td class="action border-right-none">

                        <a href="javascript:void(0);" data-id="id" data-xcxid="{{$v['id']}}" id="setting_host" class="setting_host">设置域名</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="get_category" class="get_category">获取类目</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="bind_experiencer" class="bind_experiencer">绑定体验者</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="see_news_modei" class="see_news_modei">查看消息模板</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="cancel_submit" class="cancel_submit">取消审核</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="add_remark_btn" class="add_remark_btn">添加备注</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="revert_Release">版本回退</a>
                        <a href="javascript:void(0);" data-xcxid="{{$v['id']}}" id="urgent-Audit" class="urgent-Audit">加急审核申请</a>
                    </td>
                    <td class="action border-right-none brder-left-none">
                        <a href="javascript:void(0);" id="upload_code" class="upload_code" data-ver="{{ json_encode($v) }}">上传代码</a>                        
                        <a href="javascript:void(0);" id="submit_code" class="submit_code" data-ver="{{ json_encode($v) }}">提交审核</a>
                        <a href="javascript:void(0);" id="code_release" class="code_release">提交发布</a>
                        <a href="{{ config('app.url') }}staff/xcx/getQrCode?xcxid={{$v['id']}}" target="_blank" id="experience_code1" class="experience_code1" >朕要体验</a>
                        <a href="javascript:void(0);" id="get_qrcode" class="get_qrcode" data-id="1" data-ver="{{ json_encode($v) }}">获取二维码</a>
                        @if($v['status']!=2&&$v['status']!=7&&$v['status']!=8)
                            <a href="javascript:void(0);" id="to_void_btn" class="to_void_btn">作废</a>
                        @endif
                        @if($v['status']==7)
                            <a href="javascript:void(0);" id="off_the_shelf" class="off_the_shelf">下架</a>
                        @endif
                        <a href="javascript:void(0);" id="pluginList" class="pluginList" data-ver="{{ json_encode($v) }}">插件列表</a>
                        <a href="javascript:void(0);" id="addPlugin" class="addPlugin" data-ver="{{ json_encode($v) }}">添加插件</a>
                    </td>
                    <td class="action brder-left-none">
                        <a href="javascript:void(0);" id="get_page" class="get_page">获取页面</a>
                        <a href="javascript:void(0);" id="see_detail" class="see_detail" @if($v['isChangeColor'] == 1) style="color: red" @endif data-ver="{{ json_encode($v) }}" data-id="{{$v['id']}}" data-color="{{$v['isChangeColor']}}">查看详情</a>
                        <a href="javascript:void(0);" id="cancel_experiencer" class="cancel_experiencer">解绑体验者</a>
                        <a href="javascript:void(0);" id="set_modei" class="set_modei">设置消息模板</a>
                        <a href="javascript:void(0);" id="get_flower_code" class="get_flower_code" data-id="1" data-ver="{{ json_encode($v) }}">获取小程序码</a>
                        <a href="javascript:void(0);" class="js_setting-webview-host">设置业务域名</a>
                        <a href="{{ config('app.url') }}staff/xcx/seeStaffOperLog?xcxid={{$v['id']}}" id="see_log" class="see_log" >查看日志</a>
                    </td>
                </tr>
            @empty
                暂无数据
            @endforelse
            </tbody>
        </table>
        <div>
        <input type="checkbox" name="allCheck" id="allCheck" />
        <label for="allCheck">全选</label>
        <button class="btn btn-primary btnOpen">开启自动发布</button>
        <button class="btn btn-primary btnClose">关闭自动发布</button>
        <input type="button" class="btn-charge btn btn-primary" value="标记付费" id="isFee"/>
        <input type="button" class="btn-free btn" value="标记免费" id="isFree"/>
        <input type="button" class="btn-charge btn btn-primary" value="标记赠送" id="isGive"/>
        </div>
        {{$data[1]}}<br/>共{{$data[0]['total']}}条数据
    </div>
    <!-- 表格区域 -->
</div>
<!-- 一键获取域名 -->
<div class="get_host hide">
    <div class="code_model">
       <span class="host_con" style="padding: 10px;"></span>
    </div>
</div>
<!-- 一键获取域名 -->
<!-- 设置域名 -->
<div class="set_code_model hide">
    <div class="code_model" style="padding: 0 40px;">
        <div style="margin-bottom: 5px;">
            <input type="text" style="width: 100%;" class="set_zhost" value="www.huisou.cn">
        </div>
        <p style="color: red; font-size:12px ;">建议格式：网络名.域名主题.域名后缀(例如：www.baidu.com)</p>
    </div>
</div>
<!--设置域名-->
<!-- 设置业务域名 -->
<div class="set_webview_model hide">
    <div class="code_model" style="padding: 0 40px;">
        <div style="margin-bottom: 5px;">
            <input type="text" style="width: 100%;" class="set_webview_zhost" value="hsim.huisou.cn,www.huisou.cn">
        </div>
        <p style="color: red; font-size:12px ;">建议格式：网络名.域名主题.域名后缀(例如：www.baidu.com)</p>
    </div>
</div>
<!--设置业务域名-->
<!-- 查看每月提审额度和加急审核次数 -->
<div class="audit_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">当月剩余提审额度</div>
            <div style="margin-left: 10px;">
                <input type="text" class="rest" name="rest" readonly="true" value="{{$xcxOnline['rest']??''}}">
            </div>
        </div>
        <div>
            <div class="info">当月分配提审额度</div>
            <div style="margin-left: 10px;">
                <input type="text" class="limit" name="limit" readonly="true" value="{{$xcxOnline['limit']??''}}">
            </div>
        </div>
        <div>
            <div class="info">当月剩余加急次数</div>
            <div style="margin-left: 10px;">
                <input type="text" class="speedup_rest" name="speedup_rest" readonly="true" value="{{$xcxOnline['speedup_rest']??''}}">
            </div>
        </div>
        <div>
            <div class="info">当月分配加急次数</div>
            <div style="margin-left: 10px;">
                <input type="text" class="speedup_limit" name="speedup_limit" readonly="true" value="{{$xcxOnline['speedup_limit']??''}}">
            </div>
        </div>
    </div>
</div>
<!-- 查看每月提审额度和加急审核次数 -->
<!-- 上传代码 -->
<!-- 普通送审 -->
<div class="upload_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span class="upload_title"></span>
            </div>
        </div>
        <div>
            <div class="info">
                模板ID：
            </div>
            <div>
                <input type="text" class="template_id" name="template_id" value="{{$xcxOnline['template_id']??''}}">
            </div>
        </div>
        <div>
            <div class="info">
                版本号：
            </div>
            <div>
                <input type="text" class="version" name="version" value="{{$xcxOnline['user_version']??'无'}}">
            </div>
        </div>
        <div>
            <div class="info">
                备注：
            </div>
            <div>
                <input type="text" class="baseinfo" name="baseinfo" value="{{$xcxOnline['user_desc']??''}}">
            </div>
        </div>
    </div>
</div>
<!-- 直播送审 -->
<div class="upload_code_model_live hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span class="upload_title"></span>
            </div>
        </div>
        <div>
            <div class="info">
                模板ID：
            </div>
            <div>
                <input type="text" class="template_id" name="template_id" value="{{$xcxOnlineLive['template_id']??''}}">
            </div>
        </div>
        <div>
            <div class="info">
                版本号：
            </div>
            <div>
                <input type="text" class="version" name="version" value="{{$xcxOnlineLive['user_version']??'无'}}">
            </div>
        </div>
        <div>
            <div class="info">
                备注：
            </div>
            <div>
                <input type="text" class="baseinfo" name="baseinfo" value="{{$xcxOnlineLive['user_desc']??''}}">
            </div>
        </div>
    </div>
</div>
<!-- 上传代码 -->
<!-- 提交审核 -->
<div class="submit_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span class="title_up"></span>
            </div>
        </div>
        <div class="">
            <div class="info">
                所在服务类目：
            </div>
            <div class="service">
                <select class="submit_select">
                </select>
            </div>
        </div>
        <div  class="">
            <div class="info">
                功能页面：
            </div>
            <div class="service">
                <select class="service_select">
                </select>
            </div>
        </div>
    </div>
</div>
<!-- 提交审核 -->
<!-- 查看消息模板 -->
<div class="see_code_model hide">

</div>
<!--获取二维码-->
<div class="get_qrcode_model hide">
    <div class="qr_code_model">
        <div>
            <div class="info">名称:</div>
            &nbsp;<span class="qr_code_title">1111</span>
        </div>
        <div>
            <div class="info">宽度:</div>
            &nbsp;<input type="text" class="qr_code_width" name="qr_code_width" value="430">&nbsp;px
        </div>
        <div>
            <div class="info">小程序页面:</div>
            &nbsp;<div>
                <select name="qr_code_path" class="qr_code_path">
                </select>
            </div>
        </div>
    </div>
    <div class="qr_code_img">
        <img id="img_qrcode" src="" width="200px;" height="200px;" class="xcx-xcximg"/>
    </div>
</div>
<!--获取二维码-->
<!--获取菊花码-->
<div class="get_flower_model hide">
    <div class="qr_flower_code_model">
        <div>
            <div class="info">名称:</div>
            &nbsp;<span class="qr_flowercode_title">1111</span>
        </div>
        <div>
            <div class="info">宽度:</div>
            &nbsp;<input type="text" class="qr_flowercode_width" name="qr_code_width" value="430">&nbsp;px
        </div>
        <div>
            <div class="info">小程序页面:</div>
            &nbsp;<div>
                <select name="qr_flowercode_path" class="qr_flowercode_path">
                </select>
            </div>
        </div>
    </div>
    <div class="flower_img">
        <img id="img_xcxm"  width="200px" height="200px;" class="xcx-flower-img"/>
    </div>
</div>
<!--获取菊花码-->
<!--添加备注-->
<div class="add_remark_model hide">
    <div class="remark_model">
        <div>
            <div class="info">名称:</div>
            &nbsp;<span class="remark_add_title">1111</span>
        </div>
        <div>
            <div class="info">备注:</div>
            &nbsp;<input type="text" max-length="20" class="remark_add_cont" name="remark_add_cont" value="" placeholder="请输入备注">
        </div>
    </div>
</div>
<!--添加备注-->
<!-- 查看消息模板 -->
<div class="detail_model hide">
    <ul class="table_tab">
        <li class="remark_tab">备注</li>
        <li class="detail_tab">详情</li>
    </ul>
    <div class="table-detail">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="table_title">名称</td>
                    <td class="merchant_name"></td>
                    <td class="table_title">域名简称</td>
                    <td class="request_domain"></td>
                    <td class="table_title">域名绑定</td>
                    <td class="res_bind">未绑定</td>
                </tr>
                <tr>
                    <td class="table_title">授权方接口调用凭据(令牌)</td>
                    <td class="authorizer_access_token"></td>
                    <td class="table_title">令牌过期时间</td>
                    <td class="authorizer_expire_time"></td>
                    <td class="table_title">接口调用凭据刷新令牌</td>
                    <td class="authorizer_refresh_token"></td>
                </tr>
                <tr>
                    <td class="table_title">授权功能</td>
                    <td class="func_info_name"></td>
                    <td class="table_title">授权方公众号类型</td>
                    <td class="res_title"></td>
                    <td class="table_title">授权方认证</td>
                    <td class="verify_type">微信认证</td>
                </tr>
                <tr>
                    <td class="table_title">小程序原始ID</td>
                    <td class="res_app_id"></td>
                    <td class="table_title">授权方公众号所设置的微信号</td>
                    <td>未设置</td>
                    <td class="table_title">二维码图片的URL</td>
                    <td><a href="javascript:void(0);" class="see_clic" style="cursor: pointer;" target="_blank">点击查看</a></td>
                </tr>
                <tr>
                    <td class="table_title">小程序主体名称</td>
                    <td class="principal_name"></td>
                    <td class="table_title">账号介绍</td>
                    <td class="signature"></td>
                    <td class="table_title">idc</td>
                    <td>0(官方文档未说明该字段含义)</td>
                </tr>
                <tr>
                    <td class="table_title">服务器域名</td>
                    <td>request合法域名:<span class="request_domain"></span><br>
                    	socket合法域名:<span class="ws_request_domain"></span><br>
                		uploadFile合法域名:<span class="upload_domain"></span><br>
            			downloadFile合法域名:<span class="download_domain"></span>
                    </td>
                    <td class="table_title">服务类目</td>
                    <td class="category_list"></td>
                    <td class="table_title">业务功能</td>
                    <td>微信门店功能:<span class="open_store">未开通</span><br>
                    	微信扫商品功能:<span class="open_scan">未开通</span><br>
                		微信支付功能:<span class="open_pay">未开通</span><br>
            			微信卡券功能:<span class="open_card">未开通</span><br>
        				微信摇一摇功能:<span class="open_shake">未开通</span>
                    </td>
                </tr>
                <tr>
                    <td class="table_title">小程序页面</td>
                    <td rowspan="3" class="page_list"></td>
                    <td class="table_title">visit_status</td>
                    <td>0(官方文档未说明该字段含义)</td>
                    <td class="td_reason">(审核被拒原因)</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="remark-table">
        <p id="remark-test">备注记录</p>
        <table class="remark_detail table table-bordered">
           <thead>
               <tr>
                   <td>小程序名称</td>
                   <td>备注信息</td>
                   <td>备注人</td>
                   <td>备注时间</td>
               </tr>
           </thead>
           <tbody class="remark_tbody">
                <tr class="tr_remark_detail">
                </tr>
           </tbody>
        </table>
        <div class="page"> 
        </div>
    </div>
</div>
<div class="pluginList_model hide">
  <div class="pluginListTable">
    <table class="table table-bordered">
      <thead>
        <th>图片</th>
        <th>appid</th>
        <th>状态</th>
        <th>插件名称</th>
        <th>版本</th>
        <th>操作</th>
      </thead>
      <tbody class="pluginTableData"></tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6" >
            <button type="button" class="btn btn-primary">确定</button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
@section('foot.js')
	<script type="text/javascript">
		var url = "{{ config('app.url') }}";
		re=new RegExp("https://","g");
   		url=url.replace(re,"");
	</script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!-- ajax分页js -->
	<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/xcxList.js" type="text/javascript" charset="utf-8"></script>
@endsection