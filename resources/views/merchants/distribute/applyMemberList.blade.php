@extends('merchants.default._layouts')
@section('head_css')
    <!--当前页面-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_wxpkkkf2.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/applyMemberList.css" />
   
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')
	
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav"> 
            <li>  
                <a href="{{ URL('/merchants/distribute/partner') }}">分销合伙人</a>
            </li>  
			<li class="hover">  
                <a href="{{ URL('/merchants/distribute/applayMemberList') }}">分销审核</a>
            </li>
			<li>  
                <a href="{{ URL('/merchants/distribute/purgeLog') }}">清退记录</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection

@section('content')
<div class="content">
	<!--筛选部分-->
	<div class="screen">
	    <form>
            <div class="form-inline clearfloat">
                <div class="form-group col-sm-3">
                    <label for="phoneNum">手机号:</label>
                    <input type="number" class="form-control" name="mobile" id="mobile" placeholder="手机号码"
                           value="{{request('mobile')}}">
                </div>
                <div class="form-group col-sm-3">
                    <label for="nickName">微信昵称:</label>
                    <input type="text" class="form-control" name="nickname" id="nickName" placeholder="微信昵称"
                           value="{{request('nickname')}}">
                </div>
                <div class="form-group col-sm-6">
                    <label>创建时间：</label>
                    <input type="text" class="form-control" id="startTime" placeholder=""
                           name="start_time" value="{{request('start_time')}}">
                    -
                    <input type="text" class="form-control" id="endTime" placeholder="" name="end_time"  value="{{request('end_time')}}">
                </div>
            </div>
            <div class="form-inline clearfloat mt20">
                <div class="form-group col-sm-3">
                    <label>&nbsp;&nbsp;&nbsp;购次:</label>
                    <select name="buy_num" class="form-control" id="buyNum">
                        <option @if(request('buy_num') == '-1') selected @endif value="-1">全部</option>
                        <option @if(request('buy_num') == '1') selected @endif value="1">1+</option>
                        <option @if(request('buy_num') == '2') selected @endif value="2">2+</option>
                        <option @if(request('buy_num') == '3') selected @endif value="3">3+</option>
                        <option @if(request('buy_num') == '4') selected @endif value="4">4+</option>
                        <option @if(request('buy_num') == '5') selected @endif value="5">5+</option>
                        <option @if(request('buy_num') == '10') selected @endif value="10">10+</option>
                        <option @if(request('buy_num') == '15') selected @endif value="15">15+</option>
                        <option @if(request('buy_num') == '20') selected @endif value="20">20+</option>
                        <option @if(request('buy_num') == '30') selected @endif value="30">30+</option>
                        <option @if(request('buy_num') == '50') selected @endif value="50">50+</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>申请状态:</label>
                    <select name="status" class="form-control" id="status">
                        <option  value="">全部</option>
                        <option @if(request('status') == '0') selected @endif value="0">等待审核</option>
                        <option @if(request('status') == '1') selected @endif value="1">审核通过</option>
                        <option @if(request('status') == '2') selected @endif value="2">审核拒绝</option>
                        <option @if(request('status') == '3') selected @endif value="3">满足门槛通过</option>
                    </select>
                </div>
            </div>

            <div class="btns_clean">
                <input class="btn btn-primary" type="submit" value="筛选"/>
                <a id="clearJudge" href="javascript:;">清空筛选条件</a>
            </div>
        </form>

    </div>
    <div class="switcher-wraper">
        <div class="js-distribute-switch switch-small">
            自动审核
            <label class="ui-switcher @if(!empty($shopData['is_auto_check'])) ui-switcher-on @else ui-switcher-off @endif " data-is-open="{{$shopData['is_auto_check']}}"></label>
        </div>
        <span class="help">
            <i class="glyphicon glyphicon-question-sign f14 "></i>
            <span class="tips-content">开启后，用户提交申请30分钟内商家未处理，系统自动审核通过</span>
        </span>
    </div>
	<div class="member_list">
		<ul class="list_item list_header">
			<li>序号</li>
			<li>昵称</li>
			<li>手机号</li>
			<li>购次</li>
			<li>状态</li>
			<li>申请时间</li>
            <li>操作</li>
		</ul>
		<div class="list_div">
            @forelse($data[0]['data'] as $val)
            <ul class="list_item list_body">
                <li>{{$val['id']}}</li>
                <li>{{$val['memberData']['nickname']}}</li>
                <li>{{$val['memberData']['mobile']}}</li>
                <li>{{$val['memberData']['buy_num']}}</li>
                <li>@if($val['status'] == '0')待审核@elseif($val['status']=='1')审核通过@elseif($val['status'] == '2') 拒绝 @elseif($val['status'] == '3') 满足门槛通过@endif</li>
                <li>{{$val['created_at']}}</li>
                <li class="color-blue">
                    @if($val['status'] =='0')
                    <span class="js-action" data-id="{{$val['id']}}" data-type="1">通过</span>
                    -
                    <span class="js-action" data-id="{{$val['id']}}" data-type="2">拒绝</span>
                        @elseif($val['status'] == '1')
                        已通过
                        @elseif($val['status'] == '2')
                        已拒绝
                        @elseif($val['status'] == '3')
                        满足门槛通过
                        @endif
                </li>
            </ul>
                @endforeach
		</div>
	</div>
	<div class="main_bottom flex-end">
		<!-- 分页 -->
        {{$data[1]}}
	</div>
    
</div>
<div class="popup js-popup">
	<div class="popup-wraper">
		<span class="close-wraper js-close-wraper">X</span>
		<p class="popup-title">确定拒绝该申请？</p>
		<p class="popup-desc-title"><span>* </span>拒绝理由：</p>
		<textarea name="" id="" class="reason js-reason" placeholder="以证实此用户设计刷单获取不正当利益"></textarea>
		<div class="popup-btns">
			<div class="clear-btn cancle-btn js-cancle-btn">取消</div>
			<div class="clear-btn sure-btn js-sure-btn">确定</div>
		</div>
	</div>
</div>
<div class="popup js-popup1">
	<div class="popup-wraper" style="height: 260px;">
		<span class="close-wraper js-close-wraper1">X</span>
		<p class="popup-title" style="padding-top: 86px;">确定通过该申请？</p>
		<div class="popup-btns">
			<div class="clear-btn cancle-btn js-cancle-btn1">取消</div>
			<div class="clear-btn sure-btn js-sure-btn1">确定</div>
		</div>
	</div>
</div>

@endsection
@section('page_js')
    <script>
        var _host = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/moment.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/locales.min.js"></script>    
    <script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/applyMemberList.js"></script>
@endsection