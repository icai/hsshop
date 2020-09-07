@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前模块公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_llbq22x2.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_f2k6aroq.css" />
@endsection
@section('slidebar')
@include('merchants.order.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <input id="wid" type="hidden" value="{{session('wid')}}">
        <ul class="common_nav">
            @if ( Route::input('level', '0') == '0' )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/order/evaluateOrder') }}">全部评论</a>
            </li>
            @if ( Route::input('level', '0') == '1' )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/order/evaluateOrder/1') }}">好评</a>
            </li>
            @if ( Route::input('level', '0') == '2' )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/order/evaluateOrder/2') }}">中评</a>
            </li>
            @if ( Route::input('level', '0') == '3' )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/order/evaluateOrder/3') }}">差评</a>
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
@endsection
@section('content')
<div class="content comment_manage">
    <table class="table">
        <thead>
            <tr>
                <th class="col-xs-3">商品评价</th>
                <th class="col-xs-2">商品信息</th>
                <th class="col-xs-1">订单号</th>
                <th class="col-xs-1">买家</th>
                <th class="col-xs-2">标签</th>
                <th class="col-xs-2">评价时间</th>
                <th class="col-xs-1">操作</th>
            </tr>
        </thead>
        <tbody>
        @forelse($evaluate[0]['data'] as $val)
            <tr>
                <td class="shop_title_{{$val['id']}}">
                    <span>[@if($val['status'] == 1) 好评 @elseif($val['status'] == 2)中评 @elseif($val['status'] == 3) 差评  @endif]</span>
                    {{$val['content']}}
                    @if(!empty($val['detail']))
                        <p class="reply_content"><span><b>回复</b>：</span><span class="reply_detail">{{$val['detail']['content']}}</span></p>
                    @endif
                </td>
                <td class="shop_info">{{$val['product']['title']}}</td>
                <td class="shop_order">{{$val['order']['oid']}}</td>
                <td>{{$val['member']['nickname']}}</td>
                <td>
                    @forelse($val['ec'] as $v)
                          {{$v['classify_name']}},
                    @endforeach
                </td>
                <td>{{$val['created_at']}}</td>
                <td>
                    <span class="make_tag" data-index="{{$val['id']}}" data-pid = "{{$val['pid']}}">打标签</span>
                    <br />
                    @if(!empty($val['detail']))
                        <span class="reply_already">已回复</span>
                     @else
                        <span class="reply_action" data-index="{{$val['id']}}">回复</span>
                    @endif
                    <br />
                    <a href="javascript:void(0);" class="delete_action blue_38f">删除</a>
                    <input type="hidden" value="{{$val['id']}}"/>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!--回复弹框-->
        <div class="replyBoard"></div>
        <div class="comment_content">
            <form role="form" name="commentForm" id="commentForm">
                <div class="form-group">
                    <textarea class="form-control" rows="3" placeholder="回复内容" name="comment"></textarea>
                </div>
                <div class="tn">
                    <a class="reply_btn btn btn-primary" href="javascript:void(0);">回复</a>
                    <div class="uploadimage">
                        <img src="">
                        <input class="upload_images" type="file" name="" onchange="readFile()" accept="image/png,image/gif,image/jpg,,image/jpeg" />
                    </div>
                </div>
            </form>
        </div>
        <div class="pageHtml">
            <span>{{$evaluate[1]}}</span>
        </div>
    </div>
<!-- 推广商品 -->
<div class="widget-promotion" style="top: -999px; left: 549px;">
    <div class="tap_title">请给评价添加标签</div>
    <div class="tap_container">
        <span>
            <input type="checkbox" name="tag" value="有图">有图
        </span>
        <span>
            <input type="checkbox" name="tag" value="一般">一般
        </span>
        <span>
            <input type="checkbox" name="tag" value="好看">好看
        </span>
        <span>
            <input type="checkbox" name="tag" value="很实用">很实用
        </span>
        <span>
            <input type="checkbox" name="tag" value="还可以">还可以
        </span>
    </div>
    <div class="form-group">
        <input type="text" class="form-control tag_title" name="tag_title"> <a href="javascript:void(0)" class="add_tag">添加+</a>
    </div>
    <div class="btn_group">
        <button class="zent-btn zent-btn-primary tag_save">保存</button>
        <button class="zent-btn tag_cancel">取消</button>
    </div>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_f2k6aroq.js"></script>
@endsection
