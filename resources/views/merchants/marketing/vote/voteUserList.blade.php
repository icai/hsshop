@extends('merchants.default._layouts')
@section('head_css') 
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 选择商品样式 -->
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/together_wxpj42f2.css" />
<style type="text/css">
     .laydate_box, .laydate_box * {box-sizing:content-box;}
 </style>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/voteUserList.blade.css" />

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
                    <a href="javascript:void(0)">投票人列表</a>
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
        </div> 
        <!-- 导航模块 结束 -->
        <!-- 新增内容 开始 -->
    
    <div>
    	<!--头部-->
    	<div id="header">
            <form method="get" action="">
            <input type="hidden" name="enroll_id" value="{{ $input['enroll_id'] or 0 }}">
            <input type="hidden" name="vote_id" value="{{ $input['vote_id'] or 0 }}">
    		<div id="t_top">
	       		<label for="">
	       			<span>微信号：</span>
	       			<input type="text" name="wechat_id" value="{{ $input['wechat_id'] or '' }}" />
	       		</label>
	       		
	       		
	       		<label for="">
	       			<span>微信昵称:</span>
	       			<input type="text" name="nickname" value="{{ $input['nickname'] or '' }}" />
	       		</label>
	       		<button type="submit" class="btn btn-primary screening">筛选</button>
	       </div>
	       <div id="t_select">
	       	<label for="">
	       		<span>性别：</span>
	       		<select name="sex" id="t_set">
                    @if(isset($input['sex']) && $input['sex']== 1)
	       			<option value="1" selected="selected">男</option>
	       			<option value="2">女</option>
                    @else
                    <option value="1">男</option>
                    <option value="2" selected="selected">女</option>
                    @endif
	       		</select>
	       	</label>
	       </div>
           </form>
    	</div>
       
       
        <!--内容-->
        @if($memberDatas)
        <div id="t_content">
        	<ul class="t_content_header">
        		<li>
        			<input type="checkbox" />序列号
        		</li>
        		<li>头像</li>
        		<li>微信昵称</li>
        		<li>微信号</li>
        		<li>
        			性别
        		</li>
        		
        	</ul>
            @foreach($memberDatas as $val)
        	<ul class="t_content_con">
        		<li>
        			<input type="checkbox" />{{ $val['id'] }}
        		</li>
        		<li><img src="{{ $val['headimgurl'] or ''}}" alt="微信头像" width="50" height="50" /></li>
        		<li>{{ $val['nickname'] or ''}}</li>
        		<li>{{ $val['wechat_id'] or ''}}</li>
        		<li>
        			@if($val['sex'] == 1)
                        男
                    @else
                        女
                    @endif
        		</li>
        		
        	</ul>
        	@endforeach
        </div>
       @else
        <div class="no-result widget-list-empty" style="border: 1px solid #F2F2F2;padding: 50px;text-align: center;">还没有相关数据
        </div>
       @endif 

         
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
<script src="{{ config('app.source_url') }}mctsource/js/voteUserList.blade.js" type="text/javascript" charset="utf-8"></script>


@endsection

