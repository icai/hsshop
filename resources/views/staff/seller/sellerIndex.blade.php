@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/statistical_index.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
    	<div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>业务员跟单-分组统计</span>
                <span><a href="/staff/seller/exportSalesman">导出全部</a></span>

            </div>
            <div class="main_content">
                <ul class="ulDiv table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>业务员姓名</li>
                    <li>手机号</li>
                    <li>总单量</li>
                    <li>总有效单量</li>
                    <li>当月单量</li>
                    <li>当月有效单量</li>
                    <li>操作</li>
                </ul>
                <form class="listForm">
                    @if($liSalesMan)
                        @foreach($liSalesMan as $v)
                        <ul class="ulDiv table_body  flex-between">
                            <li><input type="checkbox" name='ids[]' value="" /></li>
                            <li>{{ $v['name']?? ''}}</li>
                            <li>{{ $v['mobile'] ?? ''}}</li>
                            <li>{{ $v['total'] ?? 0}}</li>
                            <li>{{ $v['totalValid'] ?? 0}}</li>
                            <li>{{ $v['month'] ?? 0}}</li>
                            <li>{{ $v['monthValid'] ?? 0}}</li>
                            <li data-phone="{{ $v['mobile'] }}" data-id="{{ $v['id'] }}">
                                <a href="##" class="del">删除</a>
                            </li>
                        </ul>
                        @endforeach
                            <div class="main_bottom flex_end">
                                {{ $page }}
                            </div>
                    @else
                        <div class="xue-rdiv1" style="border-bottom:0; text-align: center; padding: 100px 0;">
                            暂无数据
                        </div>
                    @endif
                </form>
            </div>
        </div>
	</div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/statistical_index.js" type="text/javascript" charset="utf-8"></script>
@endsection