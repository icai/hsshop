@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/6.1 potential_customers.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/xcxList.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
	<div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>小程序-更新失败小程序统计</span>
            </div>
            <div class="main_content">
                <ul class="table_title flex-between">
                    <li>统计日期</li>
                    <li>失败原因</li>
                    <li>操作</li>
                </ul>

                @forelse($list as $key=>$val)
                    <ul class="table_body  flex-between">
                        <li>{{ $val['start_date'] }}</li>
                        <li style="overflow:hidden;">{{ $val['log'] }}</li>
                        <li>
                            <a href="javascript:void(0);" class="modify" data-date="{{ $val['start_date'] }}">更新</a>
                        </li>
                    </ul>
                @endforeach
                
                <div class="main_bottom t-pr" style="position: relative;">
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot.js')
<script type="text/javascript">
    $(function(){
        $('.modify').one('click',function() {
            var _this = $(this);
            var date = _this.data('date');
            $.get('/staff/xcx/doUpdateErrorStatistic',{date:date},function(res) {
                if (res.errCode == 0) {
                    tipshow(date+'日期数据同步中...,请稍候刷新页面查看','info');
                }else {
                    tipshow(res.errMsg,'warn');
                }
            });
        });

    });
</script>

@endsection