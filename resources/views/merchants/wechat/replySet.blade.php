@extends('merchants.default._layouts') 
@section('head_css')
<!-- 上传插件样式 -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_u0851kad.css">
@endsection @section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="javascript:void(0);">公众号</a>
            </li>
            <li>
                <a href="javascript:void(0);">{{ $title }}</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>
    <!-- 三级导航 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection @section('content')
@include('merchants.wechat.slidebar')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
    <!--主体左侧列表开始-->
    <!--主体左侧列表结束-->
    <!--主体右侧内容开始-->
    <div class="right_container">
        <div class="header clearfix">
            <!-- 导航模块 开始 -->
            <div class="nav_module clearfix">
                <!-- 左侧 开始 -->
                <div class="pull-left">
                    <!-- （tab试导航可以单独领出来用） -->
                    <!-- 导航 开始 -->
                    <ul class="tab_nav">
                        <li class="hover">
                            <a href="{{ URL('/merchants/wechat/replySet') }}">关键词自动回复</a>
                        </li>
                        <li>
                            <a href="{{ URL('/merchants/wechat/subscribeReply') }}">关注后自动回复</a>
                        </li>
                         <li>
                            <a href="{{ URL('/merchants/wechat/messages') }}">消息托管</a>
                        </li>
                        <!--<li>
                            <a href="{{ URL('/merchants/wechat/messagesTips') }}">小尾巴</a>
                        </li>
                        <li>
                            <a href="{{ URL('/merchants/wechat/weeklyReply') }}">每周回复</a>
                        </li> -->
                    </ul>
                    <!-- 导航 结束 -->
                </div>
                <!-- 左侧 结算 -->
                <!-- 右边 开始-->
                <div class="pull-right">
                    <!-- 搜素框~~或者自己要写的东西 -->
                    <!-- <a class="f12 blue_38f" href="javascript:void(0);" target="_blank"><i class="glyphicon glyphicon-question-sign green f14 mgr10"></i>自动回复使用教程 </a> -->
                </div>
                <!-- 右边 结束 -->
            </div>
            <!-- 导航模块 结束 -->
        </div>
        <!--操作部分开始-->
        <div class="handle">
            <input id="wid" type="hidden" name="wid" value="{{ session('wid') }}" />
            <div class="handle_title">
                <button class="btn btn-success" style="font-size: 12px;">新建自动回复</button>
                <!--弹框开始-->
                <div class="new_cap all">
                    <input type="text" name="" id="" value="未命名规则" />
                    <button class="btn btn-primary">确定</button>
                    <button class="btn btn-default">取消</button>
                </div>
                <!--弹框结束-->
                <!--搜索开始-->
                <div class="search">
                    <input type="text" name="" id="" placeholder="搜索" />
                </div>
                <!--搜索结束-->
            </div>
            <!-- 内容开始 -->
            @forelse ( $list as $k => $v )
            <div class="handle_content">
                <div class="rule_meta">
                    <h5><!-- <span class="num">1)</span> --><span class="name">{{ $v['name'] }}</span>
                        <div class="rule_opts">
                            <a class="rule_edit" href="javascript:void(0)">
                                编辑
                            </a>
                            <span>-</span>
                            <a class="rule_delete" href="javascript:void(0)" data-id="{{ $v['id'] }}">删除</a>
                        </div>
                        <!--弹框开始-->
                        <div class="new_cap all">
                            <input type="text" name="" id="" value="{{ $v['name'] }}" />
                            <button class="btn btn-primary" data-id="{{ $v['id'] }}">确定</button>
                            <button class="btn btn-default">取消</button>
                          </div>
                        <!--弹框结束-->
                    </h5>
                </div>
                <div class="rule_body clearfix">
                    <div class="line"></div>
                    <div class="rule_left">
                        <div class="rule_keywords">
                            关键词：
                        </div>
                        <div class="keywords_list">
                            @forelse ( $v['weixinReplyKeyword'] as $val )
                            <div class="keywords">
                                <a class="close_circle" href="javascript:void(0)" data-id="{{ $val['id'] }}">x</a>
                                <div class="words">
                                    <span class="value">{{ $val['keyword'] }}</span><span class="add">{{ $val['type'] == 0 ? '全匹配' : '模糊' }}</span>
                                </div>
                            </div>
                            @empty
                            @endforelse
                            <div class="left_info @if ( isset($v['weixinReplyKeyword']) && !empty($v['weixinReplyKeyword']) )no @endif"> 
                                还没有任何关键字！
                            </div>
                        </div>
                        <div class="rule_add_keywords">
                            <a class="co_38f js_ad" href="javascript:void(0)" data-rule_id="{{ $v['id'] }}">
                                +添加关键词
                            </a>
                        </div>
                    </div>
                    <div class="rule_right" data-rule_id="{{ $v['id'] }}">
                        <!--规则右边开始-->
                        <div class="rule_reply">
                            自动回复：
                            @if( $v['reply_all'] == 0)
                            <span class="rule_talk">随机发送</span>
                            @else
                                <span class="rule_talk">全部发送</span>
                            @endif

                            <span class="right_info edit_show" data-reply="{{ $v['reply_all'] }}">编辑</span>
                        </div>
                        <ol class="reply_list">
                            @forelse ( $v['weixinReplyContent'] as $value )
                            <li class="reply" data-id="{{ $value['id'] }}">
                                @if ( $value['type'] == '2' )
                                <img class="images" src="{{ $value['config']['show_sub'] }}">
                                @elseif($value['type'] == '7')
                                    <span class="reply_type">微信客服</span>
                                    <span class="reply_text">微信客服</span>
                                @else
                                <span class="reply_type">{{ $value['config']['show_title'] }}</span>
                                @if ( empty($value['url']) )
                                <span class="reply_text">{{ $value['config']['show_sub'] }}</span>
                                @else
                                <a class="co_blue" href="{{ $value['url'] }}" target="_blank">{{ $value['config']['show_sub'] }}</a>
                                @endif
                                @endif
                                <div class="reply_opts">
                                    <a class="replay_edit" href="javascript:void(0)" data-id="{{ $value['id'] }}">编辑</a>
                                    <span>-</span>
                                    <a class="replay_delete" href="javascript:void(0)" data-id="{{ $value['id'] }}">删除</a>
                                </div>
                            </li>
                            @empty
                            @endforelse
                            <div class="right_info @if ( isset($v['weixinReplyContent']) && !empty($v['weixinReplyContent']) )no @endif">
                                还没有任何回复！
                            </div>
                        </ol>
                        <div class="rule_add_reply">
                            <a class="co_38f js_addReply" href="javascript:void(0)">+添加一条回复</a>
                        </div>
                        <!--规则右边结束-->
                    </div>
                </div>
            </div>
            @empty
            <!-- 内容结束 -->
            <div class="no_result">
                还没有自动回复，请点击新建。
            </div>
            @endforelse
            <div>{!! $pageHtml !!}</div>
        </div>
        <!--操作部分结束-->
    </div>
    <!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->

<!--图片弹框开始-->
<div class="modal in export-modal myModal-adv" id="myModal-adv" onselectstart="return false;" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" id="modal-dialog-adv">
        <form class="form-horizontal">
            <div class="modal-content content_first">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                            <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                        </li>
                    </ul>
                    <div class="search-region">
                        <div class="ui-search-box">
                            <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="category-list-region">
                        <ul class="category-list">
                          
                        </ul>

                    </div>
                    <div class="attachment-list-region">
                        <div class="imgData">
                            <ul class="image-list">
                               
                            </ul>
                            <div class="attachment-pagination">
                                <div class= "picturePage"></div><!-- 分页 -->
                            </div>
                            <a href="##" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;">上传图片</a>
                        </div>
                        <!--列表中的图片个数为0的时候显示这个模态框  no隐藏数据-->
                        <div id="layerContent_right" class="no">
                            <a class="js_addImg" href="#uploadImg">+</a>
                            <p>暂无数据，点击添加</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
            <div class="modal-content content_second">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <div class="cap_head clearfix">
                        <a class="co_38f js_prev" href="javascript:void(0);"><选择图片 </a>
                        <span>上传图片</span>
                    </div>

                </div>
                <div class="modal-body">
                    <div id="uploadLayerContent_botm">
                        <div id="wrapper">
                            <div id="container">
                                <!--头部，相册选择和格式选择-->
                                <div id="uploader">
                                    <div class="queueList">
                                        <div id="dndArea" class="placeholder">
                                            <label id="filePicker"></label>
                                            <!-- <p>或将照片拖到这里，单次最多可选300张</p> -->
                                        </div>
                                    </div>
                                    <div class="statusBar" style="display:none;">
                                        <div class="progress">
                                            <span class="text">0%</span>
                                            <span class="percentage"></span>
                                        </div>
                                        <div class="info"></div>
                                        <div class="btns">
                                            <div id="filePicker2"></div>
                                            <div class="uploadBtn">开始上传</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
            <div class="modal-content content_third">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                            <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                        </li>
                    </ul>
                    <div class="search-region">
                        <div class="ui-search-box">
                            <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <ul id="iconStyleSelect">
                        <li id="style">风格:
                            <a href="##" class="selected">全部</a>
                            <a href="##">普通</a>
                            <a href="##">简约</a>
                        </li>
                        <li id="color">颜色:
                            <a href="##" class="selected">全部</a>
                            <a href="##">白色</a>
                            <a href="##">灰色</a>
                        </li>
                        <li id="type">类型:
                            <a href="##" class="selected">全部</a>
                            <a href="##">常规</a>
                            <a href="##">购物</a>
                            <a href="##">交通</a>
                            <a href="##">食物</a>
                            <a href="##">商务</a>
                            <a href="##">娱乐</a>
                            <a href="##">美妆</a>
                        </li>
                    </ul>
                    <div id="iconImgShow">
                        <ul id="iconImgSelect">
                            <li>
                                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bb3503203766425965b7517336df979d.png?imageView2/2/w/160/h/160/q/75/format/png" />
                                <div class="attachment-selected no">
                                    <i class="icon-ok icon-white"></i>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div id="pageNum">
                        共<span>270</span>条，每页27条&nbsp;&nbsp;
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="selected-count-region hide">
                        已选择<span class="js-selected-count">2</span>张图片
                    </div>
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!--图片弹框结束-->
<!--选择图文弹框开始-->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">高级图文</li>
                    <li class="js_item">微信图文</li>
                    <li class="js_manage">
                        <a class="co_38f" href="javascript:void(0);">微信图文素材管理</a>
                    </li>
                    <li class="co_000 js_link">
                        <a class="co_38f" href="javascript:void(0)">高级图文素材管理</a>
                    </li>
                </ul>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="title">
                                <span>标题</span>
                                <a class="co_38f" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="set_time">创建时间</th>
                            <th class="search">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="small">
                       <tr class="table_info">
                           <td colspan="3">
                               <div class="info"></div>
                           </td>
                       </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer clearfix">
                <div class= "myModalPage"></div><!-- 分页 -->
            </div>
        </div>
    </div>
</div>
<!--选择图文弹框结束-->
<!--微页面模态框开始-->
<div class="modal" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">微页面</li>
                    <!-- <li class="js_item">微页面分类</li> -->
                    <li class="js_manage">
                        <a class="co_38f" href="javascript:void(0);">分类管理</a>
                    </li>
                    <li class="co_000 js_link">
                        <a class="co_38f" href="javascript:void(0)">新建微页面</a>-
                        <a class="co_38f" href="javascript:void(0)">草稿管理</a>
                    </li>
                </ul>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="title">
                                <span>标题</span>
                                <a class="co_38f" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="set_time">创建时间</th>
                            <th class="search">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="small">
                       
                    </tbody>
                </table>
            </div>
            <div class="modal-footer clearfix">
                <div class= "myModal1Page"></div><!-- 分页 -->
            </div>
        </div>
    </div>
</div>
<!--微信模态框结束-->
<!--商品模态框开始-->
<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                <ul class="list">
                    <li class="js_small list_active">已上架商品</li>
                    <li class="js_item">商品分组</li>
                    <li class="co_000 js_manage">
                        <a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建分组</a>
                    </li>
                    <li class="co_000 js_link">
                        <a class="co_38f" target="_blank" href="{{URL('/merchants/product/create')}}">新建商品</a>
                    </li>
                </ul>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="title">
                                <span>标题</span>
                                <a class="co_38f" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="set_time">创建时间</th>
                            <th class="search">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="small">
                       
                    </tbody>
                </table>
            </div>
            <div class="modal-footer clearfix">
                <div class= "myModal2Page"></div><!-- 分页 -->
            </div>
        </div>
    </div>
</div>
<!--商品模态框结束-->
<!--营销活动弹框开始-->
<div class="modal" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <ul class="list">
                    <li class="js_switch js_egg list_active" data-type="egg" data-activity="1">砸金蛋</li>
                    <li class="js_switch js_wheel" data-type="wheel"  data-activity="2">大转盘</li>
                    <!-- <li class="js_newActive">+新建营销活动</li> -->
                </ul>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w_25 tl">
                                <span>标题</span>
                                <a class="co_38f refresh" href="javascript:void(0);">刷新</a>
                            </th>
                            <th class="w_20">开始时间</th>
                            <th class="w_20">结束时间</th>
                            <th class="search w_25" style="width: 200px;">
                                <input type="text" />
                                <button class="btn btn-default">搜</button>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        
                       <tr class="table_info hide">
                           <td colspan="4">
                               <div class="info">没有任何数据</div>
                           </td>
                       </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer clearfix">
                <div class= "myModalPage"></div><!-- 分页 -->
            </div>
        </div>
    </div>
</div>
<!--营销活动弹框结束-->
@endsection
@section('page_js')
@parent
<!-- 关键词模态框 -->
<div class="rule_add_cap all" style="display: none">
    <div class="cap_keywords">
        <label class="control_label"><em>*</em>关键词:</label>
        <input type="text" name="" id="saytext" placeholder="关键词最多支持十五字" maxlength="15" />
        <span class="emotion"></span>
    </div>
    <div class="cap_rule">
        <label class="control_label"><em>*</em>规则:</label>
        <label ><input type="radio" name="optionsRadios" value="0" checked/>全匹配</label>
        <label ><input type="radio" name="optionsRadios" value="1"/>模糊</label>
    </div>
    <div class="btn_group">
        <button class="btn btn-primary">确定</button>
        <button class="btn btn-default">取消</button>
    </div>
</div>;


<!--jq扩充包用于qq表情-->
<script src="{{config('app.source_url')}}static/js/jquery-browser.js" ></script>
<!--qq表情包插件-->
<script src="{{config('app.source_url')}}static/js/jquery.qqFace.js" ></script>
<!-- webuploader上传插件引入 -->
<script src="{{config('app.source_url')}}static/js/webuploader.js"></script>
<script src="{{config('app.source_url')}}mctsource/js/wechat_upload.js"></script>
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<script type="text/javascript">
    //主体左侧列表高度控制
    $('.left_nav').height($('.content').height());
    var domain_url = "{{config('app.source_url')}}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_u0851kad.js"></script>
@endsection
