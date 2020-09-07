@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_cryfq08c.css">

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
                <li>
                    <a href="{{URL('/merchants/member/membercard')}}">会员卡管理</a>
                </li> 
                <li class="">
                    <a href="{{URL('/merchants/member/storageValue')}}">会员储值</a>
                </li>
                <li class="hover">
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
        <div class="pull-right search_module">
            <!-- 搜索 开始 -->
            <label class="search_items">
                <input class="search_input" id="search" type="text" name="" value="{{$card_no or ''}}" placeholder="搜索"/>
            </label>
            <!-- 搜索 结束 -->
        </div>
    </div>
    <div class="content_list">
        <div class="ui-box">
          <table class="ui-table ui-table-list">
            <thead>
                <tr class="widget-list-header">
                    <th class="cell-20 text-left">领取时间</th>
                    <th class="cell-15">会员卡号</th>
                    <th class="cell-15">会员</th>
                    <th class="cell-15">会员卡
                        <span class="list-header-line">|</span>类型
                    </th>
                    <th class="cell-10">售价 (元)</th>
                    <th class="cell-10">状态</th>
                    <th class="cell-15 text-right">操作</th></tr>
            </thead>
              @php
                  $status_titles = array('无门槛领卡','按规则发放的会员卡','需购买的会员卡');
              @endphp
            <tbody class="js-list-body-region">

                @foreach($obtain as $o)
                <tr class="widget-list-item">
                    <td class="text-left">{{$o['in_card_at']}}</td>
                    <td>{{$o['card_num'] or ''}}</td>
                    <td>
                        <a target="_blank" href="{{ URL('merchants/member/customer') }}">{{$o['member_title']}}</a>
                    </td>
                    <td>
                        <p>{{$o['memberCard']['title'] or ''}}</p>
                        @if($o['memberCard']['card_status']==0)
                        <p style="color: #9b9b9b">无门槛会员卡</p>
                        @elseif($o['memberCard']['card_status'] == 1)
                        <p style="color: #9b9b9b">有规则会员卡</p>
                        @else
                        <p style="color: #9b9b9b">购买会员卡</p>
                        @endif
                        
                    </td>
                    <td>{{$card_price or ''}}</td>
                    <td>
                        @if($o['memberCard']['state'] == -1)
                            已删除
                        @elseif($o['memberCard']['state'] == 0)
                            已禁用
                        @else
                            正在使用
                        @endif
                    </td>
                    <td>
                       
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
            {{$page}}
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

    </script>
@endsection