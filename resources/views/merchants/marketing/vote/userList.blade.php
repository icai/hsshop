@extends('merchants.default._layouts')
@section('head_css') 
<!-- 选择商品样式 -->
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/together_wxpj42f2.css" />
<style type="text/css">
     .laydate_box, .laydate_box * {box-sizing:content-box;}
 </style>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/userList.blade.css" />

@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')

    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">报名用户列表</a>
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li class="hover">
                        <a href="javascript:void(0)">所有促销</a>
                    </li>
                   
                </ul>  
            </div>  
            <!-- <a class="nav_module_blank" href="{{ config('app.url') }}home/index/detail/625/help" target="_blank">投票使用方法</a> -->
        </div> 
        <!-- 导航模块 结束 -->
        <!-- 新增内容 开始 -->
    
    <div>
    	<!--头部-->
       <div id="t_top">
            <form class="filter_conditions flex_between" action="" method="get">
                <input type="hidden" name="vote_id" value="{{ $voteId }}">
                <ul>
                    <li>
                        <label>手机号码：<input type="text" name="mobile" id="phoneNum" value="{{ request('mobile') }}" placeholder="手机号码" /></label>
                    </li>

                    <li>
                        <label>姓名：<input type="text" name="name" id="phoneNum" value="{{ request('name') }}" placeholder="姓名" /></label>
                    </li>
                    
                    <li>
                        <button type="submit" class="btn btn-primary screening">筛选</button>
                        <a href="javascript:;" class="clear_conditions clear_screen">清空筛选条件</a>
                    </li>
                </ul>
                
                
                
            </form>
       </div>
        <!--内容-->
        @if($voteUserList)
        <div id="t_content">
        	<ul class="t_content_header">
        		<li>
        			<input type="checkbox" />序列号
        		</li>
        		<li>图片</li>
        		<li>姓名</li>
        		<li>手机号码</li>
        		<li>
        			<p>微信号</p>
        			<p>微信昵称</p>
        		</li>
        		<li>票数</li>
        		<li>操作</li>
        	</ul>
            @foreach($voteUserList as $val)
        	<ul class="t_content_con" data-id="{{ $val['id'] }}" data-voteid="{{ $val['vote_id'] }}">
        		<li>
        			<input type="checkbox" />{{ $val['id'] }}
        		</li>
        		<li><img src="{{ $val['head_img'] }}" alt="" /></li>
        		<li>{{ $val['book_name'] }}</li>
        		<li>{{ $val['book_mobile'] }}</li>
        		<li>
        			<div>{{ $val['wechat_id'] }}</div>
                    <div>{{ $val['nickname'] }}</div>
        		</li>
        		<li>{{ $val['vote_num'] }}</li>
        		<li>
        			<p class="t_shan" data-id="{{ $val['id'] }}">删除</p>
        			<p class="t_tou">投票人列表</p>
        			<!-- <p>查看其它填写内容</p> -->
        		</li>
        	</ul>
            @endforeach
        </div>
        @else 
        <div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
        </div>
        @endif

        <!-- 分页 -->
        <div class="text-right">
            {!! $pageHtml !!}
        </div>
         
    </div>  
            
        <!-- 新增内容 结束 --> 
    
    
@endsection

@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script> 

 

 <!-- 当前页面js -->
 <script type="text/javascript">
 	var _host = "{{ imgUrl() }}";

 	var prize_set = '{!! isset($voteData["prize_set"])? $voteData["prize_set"]: ""!!}';
    var canvass_info = '{!! isset($voteData["canvass_info"])? $voteData["canvass_info"]: ""!!}'; 
    var act_rule = '{!! isset($voteData["act_rule"])? $voteData["act_rule"]: ""!!}';
 </script>
<script src="{{ config('app.source_url') }}mctsource/js/userList.blade.js" type="text/javascript" charset="utf-8"></script>


@endsection

