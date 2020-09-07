@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-店铺访客</span>
            </div>
            <div class="main_content">

                <div class="sorts">
                    <form class="form-inline" method="get" action="/staff/BusinessManage/customer">
                        <div class='input-group col-sm-2'>
		                    <span class="input-group-addon">
		                        <span>姓名</span>
		                    </span>
                        <input type='text' name="truename" class="form-control" placeholder="姓名" value="{{request('truename')}}" />
                        </div>
                        <div class='input-group col-sm-2'>
		                    <span class="input-group-addon">
		                        <span>昵称</span>
		                    </span>
                            <input type='text' name="nickname" class="form-control" placeholder="请输入昵称" value="{{request('nickname')}}" />
                        </div>
                        <div class='input-group col-sm-2'>
		                    <span class="input-group-addon">
		                        <span>电话号</span>
		                    </span>
                            <input type='text' name="mobile" class="form-control" placeholder="电话" value="{{request('mobile')}}" />
                        </div>

                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="reset" class="btn btn-primary">重置</button>
                    </form>
                </div>

                <div class="sorts">
                    <a href="javascript:;" style="color: #333;">访客列表</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>姓名</li>
                    <li>手机号码</li>
                    <li>昵称</li>
                    <li>性别</li>
                    <li>时间</li>
                    <li>操作</li>
                </ul>
                @forelse($data[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li class="original_name">{{$val['truename']}}</li>
                        <li class="original_mobile">{{$val['mobile']}}</li>
                        <li class="original_nickname">{{$val['nickname']}}</li>
                        <li>
                            @if($val['sex'] == 1)
                                男
                            @elseif($val['sex'] == 2)
                                女
                            @else
                                未知
                            @endif
                        </li>
                        <li>{{$val['created_at']}}</li>
                        <li>
                            <a href="javascript:;" data-id="{{$val['id']}}" data-parent="" class="modify change_phone">修改手机号码</a>
                        </li>
                    </ul>
                @endforeach
                <div class="main_bottom t-pr" style="position: relative;">
                    {{$data[1]}}
                </div>
            </div>
        </div>
    </div>
    
<!-- 修改手机号 -->
<div class="change_tip">
    <div class="change_code" style="padding: 0 40px;">
    	<div class="change_flex">
    		<div class="change_left">姓名：</div>
    		<span class="change_span span_name">aaaa</span>
    	</div>
    	<div class="change_flex">
    		<div class="change_left">微信昵称：</div>
    		<span class="change_span span_nickname">aaaaa</span>
    	</div>
    	<div class="change_flex">
    		<div class="change_left">原手机号：</div>
    		<span class="change_span span_phone">aaaaa</span>
    	</div>
        <div class="change_flex">
        	<div class="change_left">新手机号：</div>
            <input type="text" class="set_zhost" placeholder="新手机号">
        </div>
    </div>
</div>
@endsection
@section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/customer_fangke.js" type="text/javascript" charset="utf-8"></script>
@endsection