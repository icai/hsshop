@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/caseList.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">    	 
        <div class="content">
        	<div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>案例列表</span>
                <a href="/staff/weixin/case_create" type="submit" class="btn btn-primary btn-right">同步</a>
            </div>
                <div class="main_content" style="padding: 10px 1px">   
                    <div class="sorts">
                        <form id="myForm" class="form-inline">
                            <div class="input-group col-sm-2">
                                <span class="input-group-addon">
                                    <span>案例名称</span>
                                </span>
                                <input name="name" class="form-control" value="{{ request('name') }}" />
                            </div>
                            <button type="submit" class="btn btn-primary">搜索</button>
                        </form>
                    </div> 
                </div>      	          
            	<ul class="sheet table_title flex-between">
                    <li class="emalb">案例名称</li>
                    <li class="emalb">案例类型</li>
                    <li class="emalb">行业分类</li>
                    <li class="emalb">案例二维码</li>
                    <li class="emalb">过期时间</li>
                    <li class="fun">操作</li>
                </ul>
                @forelse($list['data'] as $val)
                <ul class="sheet table_body  flex-between">
                    <li class="emalb">{{ $val['title'] }}</li>
                    <li class="emalb">{{ $val['type_title'] }}</li>
                    <li class="emala">{{ $val['belongsToBusiness']['title'] }}</li>
                    <li class="emalb">
                        @if($val['type'] == 1)
                        <img style="width:50px; height: 50px; display: block; margin: auto;" src="{{ imgUrl($val['qrcode']) }}" />
                        @elseif($val['type'] == 2)
                            @if(!starts_with($val['qrcode'],'http'))
                            <img style="width:50px; height: 50px; display: block; margin: auto;" src="data:image/png;base64,{{ $val['qrcode'] }}" />
                            @else 
                            <img style="width:50px; height: 50px; display: block; margin: auto;" src="{{ $val['qrcode'] }}" alt="">
                            @endif
                        @else 
                            <img style="width:50px; height: 50px; display: block; margin: auto;" src="{{ $val['qrcode'] }}" />
                        @endif
                    </li>
                    <li class="emalb">{{ $val['shop_expire_at'] }}</li>
                    <li class="fun">
                        <a href="/staff/weixin/case_edit?id={{ $val['id'] }}" class="">修改</a>
                        <a href="##" class="del" data-id="{{ $val['id'] }}">删除</a>                   
                    </li>
                </ul>
                @empty
                暂无数据
                @endforelse
                <div class="main_bottom flex_end">
                    {!! $pageHtml !!}
                </div>
            </div>
        </div>
        <!-- 蒙版 -->
        <div class="zent-dialog-r-wrap">
            <img src="" alt="">
        </div>
        <!-- 蒙版end -->        
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/list.js" type="text/javascript" charset="utf-8"></script>
@endsection