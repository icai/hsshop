@extends('merchants.default._layouts')
@section('head_css')
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_kwvhib03.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_ys7uplrz.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">商品分组</div>
        <!-- 二级导航三级标题 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="js-list-filter-region clearfix ui-box">
        <div class="widget-list-filter">
            <div>
            <a href="{{ URL('/merchants/product/createGroup') }}" class="zent-btn zent-btn-success pull-left action">新建商品分组</a>
                <div class="common-helps-entry pull-left">
                    <a href="{{ URL('/home/index/detail/36') }}" target="_blank">
                        商品分组介绍及使用教程
                    </a>
                </div>
                <div class="pull-right search_module">
                    <!-- 搜索 开始 -->
                    <form >
                        <label class="search_items">
                               <input class="search_input" name="title" value="{{isset($_GET['title'])?$_GET['title']:''}}" type="search" placeholder="搜索" value="">
                        </label>
                        @foreach($_GET as $key => $get)
                          @if($key != 'title' )
                            <input type="hidden" name="{{$key}}" value="{{$get}}"/>
                          @endif
                        @endforeach
                     </form>
                    <!-- 搜索 结束 -->
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="no-result">还没有相关数据</div> -->
    @if(!empty($list))
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-left cell-40">分组名称</th>
                <th>
                    商品数
                </th>
                <th>
                    创建时间
                </th>
                <th class="text-right">操作</th>
            </tr>
        </thead>
        <tbody>
             @foreach($list as $group)
            <tr>
                <td class="text-left">
                    <a href="/shop/group/preview/{{$group['wid']}}/{{$group['id']}}">
                        <span style="font-size:13px;color:#3197fa;">
                            @if($group['is_default'] > 0)
                                *
                            @endif
                            {{$group['title']}}
                        </span>
                    </a>
                    @if($group['is_default'] == 1)
                        <br />
                        <span class="gray" style="font-size:12px">店内所有商品，新发布的商品排在前面</span>
                    @elseif($group['is_default'] == 2)
                        <br />
                        <span class="gray" style="font-size:12px">店内所有商品，系统根据商品被浏览、购买情况对商品排序</span>
                    @endif
                </td>
                <td>{{$group['goods_num']}}</td>
                <td>{{$group['created_at']}}</td>
                <td class="text-right">
                    <a href="{{URL('/merchants/product/editgroup/'.$group['id'])}}">编辑</a>
                    @if($group['is_default'] == 0)
                        <span>-</span>
                    <a class="delete" href="javascript:void(0);" data-id="{{$group['id']}}">删除</a>
                    @endif
                    <span>-</span>
                    <a href="javascript:void(0)" class="js-link" data-id="{{$group['id']}}">推广</a>
                </td>
            </tr>
            @endforeach
        </tbody>
     </table>
     	<div class="" style="text-align: right;">
     		{{$pageHtml}}     		
     	</div>
     @else
     <div class="no_result">暂无相关分组记录</div>
     @endif
</div>
@endsection
@section('other')
<!-- tip -->
<div class="tip">请选择商品</div>

<!-- 推广弹窗 -->
<div class="widget-promotion widget-promotion1" style="display: none;">
    <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
        <li class="wsc_code active">微商城</li>
        <li class="xcx_code">小程序</li>
    </ul>
    <div class="widget-promotion-content js-tabs-content">
        <!--微商城-->
        <div class="js-tab-content-wsc" style="display: block;">
            <div>
                <div class="widget-promotion-main">
                    <div class="js-qrcode-content">
                        <div class="widget-promotion-content">
                            <label>商品分组页链接</label>
                            <div class="input-append">
                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
                            </div>
                        </div>
                        <div class="widget-promotion-content">
                            <label class="label-b">商品分组页二维码</label>
                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                <div class="qrcode">
                                    <div class="qr_img"></div>
                                    <div class="clearfix qrcode-links">
                                        <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--小程序-->
        <div class="js-tab-content-xcx" style="display: none;">
            <div>
                <div class="widget-promotion-main">
                    <div class="js-qrcode-content">
                        <div class="widget-promotion-content">
                            <label>小程序链接</label>
                            <div class="input-append">
                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="" />
                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
                            </div>
                        </div>
                        <div class="widget-promotion-content">
                            <label class="label-b">小程序二维码</label>
                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
                                <div class="qrcode">
                                    <div class="qr_img_xcx"></div>
                                    <div class="clearfix qrcode-links">
                                        <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end-->

@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_ys7uplrz.js"></script>
<script type="text/javascript">
    var host = "{{config('app.url')}}"
    var wid = "{{ session('wid') }}"
     /**
        * 获取所有url上的参数
        * 修改 并返回 对应 url的参数值
        */
       function getallparam(obj){
           var sPageURL = window.location.search.substring(1);
           var sURLVariables = sPageURL.split('&');
           var flag = 0;
           for(var i = 0; i< sURLVariables.length; i++){
               var sParameterName = sURLVariables[i].split('=');
               if (undefined != obj[sParameterName[0]]){
                   sParameterName[1] = obj[sParameterName[0]];
                   flag++;
               }
               sURLVariables[i] = sParameterName.join('=');
           }
           var newquery = sURLVariables.join('&');
           for(var key in obj){
               if(-1 === newquery.indexOf(key)){
                   newquery += '&'+key+'='+obj[key];
               }
           }
           return newquery;
       }

       //点击排序
       var SORT = [0,0,0,0];
       var ORDER_BY = ['goods_num','created_at'];
       var ORDER = ['asc','desc'];
       function sort_desc(index,sort){
           var params = getallparam({order:ORDER[sort],orderby:ORDER_BY[index]});
           window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
       }
</script>
@endsection
