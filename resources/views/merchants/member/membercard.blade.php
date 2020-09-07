@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_ugkp08pc.css">

@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="common_nav">
                <li class="hover">
                    <a href="#&status=1">会员卡管理</a>
                </li>
                <li class="">
                    <a href="{{URL('/merchants/member/storageValue')}}">会员储值</a>
                </li>
                <li class="">
                    <a href="{{URL('/merchants/member/membercard/obtain')}}">领取记录</a>
                </li>
                <li class="">
                    <a href="{{URL('/merchants/member/membercard/refund')}}">退卡记录</a>
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
<div class="content">
    <div class="content_header">
        <div class="pull-left" style="margin-bottom:-1px">
            <!-- （tab试导航可以单独领出来用） -->
            <!-- 导航 开始 -->
            <ul class="tab_nav">
               <!--  <li class="hover status">
                    <a href="javascript:void(0);">我的所有信息</a>
                </li> -->
                <li class="@if($state == 1) hover @endif">
                    <a href="/merchants/member/membercard">会员卡</a>
                </li>
                <li class="@if($state != 1) hover @endif">
                    <a href="/merchants/member/membercard?state=0">禁止的会员卡</a>
                </li>
            </ul>
            <!-- 导航 结束 -->
        </div>

        <div class="pull-right search_module">
            <!-- 搜索 开始 -->
            <label class="search_items">
                <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
            </label>
            <!-- 搜索 结束 -->
        </div>
    </div>
    <div class="content_list">
        <div class="ui-table ui-table-list" id="manage-list">

        <?php
            $status_titles = array('无门槛领卡','按规则发放的会员卡','需购买的会员卡');
            $cardRowFirst = isset($cardRow[0])?$cardRow[0]:array();
            $cardRowSecond = isset($cardRow[1])?$cardRow[1]:array();
            $cardRowThird = isset($cardRow[2])?$cardRow[2]:array();
        ?>
            <div class="grant-type-list">
                <div class="grant-type-header">
                    <h4 class="grant-type-title">无门槛领卡
                        <!-- <span class="grant-type-tip js-grant-type-tip" data-type="rule_no">?</span> -->
                    </h4>
                    <a class="js-add-member" href="{{URL('/merchants/member/membercard/add')}}">新建会员卡</a>
                    <a href="javascript:;" class="js-hide-list">收起</a>
                </div>
                <!-- <hr> -->
                <ul class="grant-type-region js-grant-type-region">
                
                    @foreach($cardRowFirst as $r)
                    <li class="widget-list-item">
                        <div class="card-item @if($r['cover'] == 0) {{$r['cover_value']}} @endif" @if($r['cover'] == 1) style="background:url({{$r['cover_value']}})" @endif>
                            <div class="header">
                                <h3 class="pull-left">{{$r['title'] or ''}}</h3>
                                <h4 class="pull-right">{{$status_titles[$r['card_status']] or ''}}
                                </h4>
                            </div>
                            <div class="detail js-rights-area">
                                <div class="rights-area">
                                    @if(in_array(1,explode(',',$r['member_power'])))
                                        <span class="rights-item postage"></span>
                                    @endif
                                    @if(in_array(2,explode(',',$r['member_power'])))
                                        <span class="rights-item discount"></span>
                                     @endif
                                    @if(in_array(3,explode(',',$r['member_power'])))
                                        <span class="rights-item coupon"></span>
                                     @endif
                                    @if(in_array(4,explode(',',$r['member_power'])))
                                        <span class="rights-item points"></span>
                                     @endif
                                </div>
                            </div>
                            <div class="bottom-area">
                                <div class="operate">
                                    <a href="{{URL('/merchants/member/members/'.$r['id'])}}" target="_blank">查看会员</a>
                                    <!--<i class="spilt">-</i>
                                    <a href="{{URL('/merchants/member/members/import')}}">导入</a>-->
                                    <i class="spilt">-</i>
                                    <a href="javascript:;" class="js-dispense-card" data-bind="{{$r['card_id']}}" data-id="{{ $r['id'] }}">发卡</a>
                                    <i class="spilt">-</i>
                                    <a href="{{URL('/merchants/member/membercard/add/'.$r['id'] )}}">编辑</a>
                                </div>

                                @if(!isset($r['card_id']) || empty($r['card_id']))
                                    @if(isset($r['state']) && $r['state'] == -1)
                                        <div class="state">已删除</div>
                                    @elseif(isset($r['state']) && $r['state'] == 0)
                                        <div class="state">已禁用</div>
                                    @else
                                        @if($r['limit_type'] == 2 && (time() > strtotime($r['limit_end'])))
                                        <div class="state">已过期</div>
                                        @else
                                        <div class="state">使用中</div>
                                        @endif
                                    @endif   
                                @endif
                                @if($r['card_id'])
                                    <div class="state">(已同步)</div>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endforeach

                    <?php
                        //$statusUrl = empty($k)?'':'?card_status='.$k;
                        $hrefUrl = '/merchants/member/membercard/add';
                    ?>

                    <!-- <li class="widget-list-item create-new-card">
                        <a href="{{URL($hrefUrl)}}" class="create-card-link">
                            <h2>+</h2>
                            <h3>新建会员卡</h3></a>
                    </li> -->
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                </ul>
            </div>

            <div class="grant-type-list">
                <div class="grant-type-header">
                    <h4 class="grant-type-title">按规则发放的会员卡
                        <!-- <span class="grant-type-tip js-grant-type-tip" data-type="rule_no">?</span> -->
                    </h4>
                    <a class="js-add-member" href="{{URL('/merchants/member/membercard/add?card_status=1')}}">新建会员卡</a>
                    <a href="javascript:;" class="js-hide-list">收起</a>
                </div>
                <!-- <hr> -->
                <ul class="grant-type-region js-grant-type-region">
                    @foreach($cardRowSecond as $r)
                    <li class="widget-list-item">
                        <div class="card-item @if($r['cover'] == 0) {{$r['cover_value']}} @endif" @if($r['cover'] == 1)style="background:url({{$r['cover_value']}})" @endif>
                            <div class="header">
                                <h3 class="pull-left">{{$r['title'] or ''}}</h3>
                                <h4 class="pull-right">{{$status_titles[$r['card_status']] or ''}}</h4></div>
                            <div class="detail js-rights-area">
                                <div class="rights-area">
                                    <span class="rights-item postage"></span>
                                    <span class="rights-item discount"></span>
                                    <span class="rights-item coupon"></span>
                                    <span class="rights-item points"></span>
                                </div>
                            </div>
                            <div class="bottom-area">
                                <div class="operate">
                                    <a href="{{URL('/merchants/member/members/'.$r['id'])}}" target="_blank">查看会员</a>
                                    <!--<i class="spilt">-</i>
                                    <a href="{{URL('/merchants/member/members/import')}}">导入</a>-->
                                    <i class="spilt">-</i>
                                    {{--<a href="javascript:;" class="js-dispense-card">发卡</a>--}}
                                    {{--<i class="spilt">-</i>--}}
                                    <a href="{{URL('/merchants/member/membercard/add/'.$r['id'].'/1' )}}">编辑</a>
                                </div>
                                 @if($r['card_id'])
                                    <div class="state">同步成功</div>
                                @else
                                    @if(isset($r['state']) && $r['state'] == -1)
                                        <div class="state">已删除</div>
                                    @elseif(isset($r['state']) && $r['state'] == 0)
                                        <div class="state">已禁用</div>
                                    @else
                                        <div class="state">使用中</div>
                                    @endif   
                                @endif
                            </div>
                        </div>
                    </li>
                    @endforeach
                    <!-- <li class="widget-list-item create-new-card">
                        <a href="{{URL('/merchants/member/membercard/add?card_status=1')}}" class="create-card-link">
                            <h2>+</h2>
                            <h3>新建会员卡</h3></a>
                    </li> -->
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                    <li class="widget-list-item justify-fix"></li>
                </ul>
            </div>
           
        </div>
    </div>
</div>
@endsection

@section('other')
    <!-- 删除弹窗 -->
    <div class="popover del_popover left" role="tooltip">
        <form id="delete_form" action="{{URL('/merchants/member/membercard/delete')}}">
            <div class="arrow"></div>
            <div class="popover-content">
                <span>你确定要删除吗？</span>
                <button class="btn btn-primary sure_btn" onclick="return deleteAjax()">确定</button>
                <a class="btn btn-default cancel_btn">取消</a>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <p class="delId"></p>
            </div>
        </form>
    </div>
    <!--弹层-->
    <div class="tip"></div>
    <!-- 发卡弹窗 -->
    <div class="ui-popover top-center" id="give_card">
        <div class="ui-popover-inner dispense-popover dispense-popover-multi">
            <div class="control-group">
                <div class="controls tab">
                    <span class="wsc-tab active">微商城</span>
                    <span class="wsc-tab">小程序</span>
                </div>
            </div>
            <div class="control-group">
                <p>该卡为无门槛会员卡，您可通过发送链接或二维码的方式客户进行领卡</p>
            </div>
            <div class="qrcode">
                <div class="control-group">
                    <label class="control-label take-label">发卡链接：</label>
                    <div class="controls link-url">
                        <input type="text" readonly="" value='{{URL("/shop/member/detail/$wid")}}' class="form-control" disabled>
                        <button type="button" class="btn js-btn-copy" data-clipboard-text="http://kdt.im/RtpWjr">复制</button>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">发卡二维码：</label>
                    <div class="controls">
                        <div class="input-append">
                        </div>
                        <a href="javascript:;" style="display: block;" class="down_qrcode" data-index="0">下载二维码</a>
                    </div>
                </div>
            </div>
            <div class="qrcode hide">
               
                <div class="control-group">
                    <label class="control-label">发卡二维码：</label>
                    <div class="controls">
                        <div class="input-append">
                            <img src="">
                        </div>
                      
                        <a href="javascript:;" style="display: block;" class="down_qrcode" data-index="1">下载二维码</a>
                    </div>
                </div>
            </div>
            <div class="arrow"></div>
        </div>

        <!-- 帮助弹窗 -->
        <div class="ui-popover ui-popover--confirm right-center" id="delete_prover">
            <div class="ui-popover-inner clearfix ">
                <div class="inner__header clearfix">
                    <div class="pull-left" style="line-height: 28px;font-size: 14px;">确定要删除吗收到货收到回复是的海景房含税单价大煞风景含税单价收到回复就是带回家收到回复就开始的话收到回复dsfsdffdsfsdfsdfsdfsdfdsfsdf就是倒海翻江？</div>
                </div>
            </div>
            <div class="arrow"></div>
        </div>
    </div>
@endsection

@section('page_js')
    @parent
    <script src="{{ config('app.source_url') }}static/js/require.js"></script>
	<script src="{{ config('app.source_url') }}mctsource/static/js/main.js"></script>
    <!-- 搜索插件 -->
    <!--<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>-->
    <!-- 图表插件 -->
    <!--<script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>-->
    <!--<script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>-->
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_cryfq07c.js"></script>
    <script>
        var wid = '{{ $wid }}';
        var url = "{{ config('app.url') }}";
        function addDelId(delId) {
            $('#delete_form .delId').html('<input type="hidden"  name=\'id\' value="'+delId+'" >');
        }
        
        function deleteAjax() {
            url = $('#delete_form').attr('action');
            $.post(url, $('#delete_form').serialize(), function( data ) {
                console.log(data);
                if ( data.status == 1 ) {
                    layer.msg( data.info,{icon:6});
                    /* 后台验证通过 */
                    if ( data.url ) {
                        /* 后台返回跳转地址则跳转页面 */
                        window.location.href = data.url;
                    } else {
                        /* 后台没有返回跳转地址 */
                        // to do somethings
                    }
                } else {
                    layer.msg( data.info);
                    /* 后台验证不通过 */
                    $('input[type="submit"]').prop('disabled', false);
                    // to do somethings
                }
            }, 'json');
            return false;
        }
        $(function(){
            
            $('.down_qrcode').click(function(){
                var index = $(this).data('index');
                var card_id = $(this).data('id');
                var callback = $(this).data('url');
                if(index == 0){
                    var url = "{{URL('/merchants/member/memberCard/down_qrcode')}}";
                    /**
                     * 会员卡编号要通过点击发卡，然后先保存到弹窗的div中，然后再点击下载的时候获得相应的卡号
                     * 这里的12是仅供测试使用
                    */
                    if(callback){
                        window.location.href= url+'?card_id='+card_id+'&qrcode_type=card&callback='+callback;
                    }else{
                        window.location.href= url+'?card_id='+card_id+'&qrcode_type=card';
                    }
                }else{
                    // 小程序下载
                    var url = "{{URL('/merchants/member/downloadXcxMemberCardCode')}}";
                    window.location.href= url+'?card_id='+card_id+'&qrcode_type=card';
                }
            });
        });

    </script>
@endsection