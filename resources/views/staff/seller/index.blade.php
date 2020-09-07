@extends('staff.base.head')
@section('head.css')
	<!--时间插件css引入-->
	<link rel="stylesheet" type="text/css" href="https://upx.cdn.huisou.cn/wscphp/res/static/css/bootstrap-datetimepicker.min.css"/>

    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/seller_index.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
    	<div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>业务员跟单-查询记录</span>
            </div>
            <div class="main_content">

                <div class="sorts">
                    <form id="myForm" class="form-inline" method="get" action="/staff/seller/index">
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>姓名</span>
                            </span>
                            <input type="text" value="{{request('name')}}" name="name" />
                        </div>
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>电话</span>
                            </span>
                            <input type="text" value="{{request('mobile')}}" name="mobile" />
                        </div>
                        <div class="form-group">
                            <label for="start_time">开始时间:</label>
                            <input type="text" name="starttime" value="{{request('starttime')}}" id="startDate">
                        </div>
                        <div class="form-group">
                            <label for="end_time">结束时间:</label>
                            <input type="text" name="endtime" value="{{request('endtime')}}" id="endDate">
                            <span style="font-size: 10px">(被邀请人注册时间非邀请时间)</span>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">确认筛选</button>　　
                            <a href="/staff/seller/refresh">刷新参团信息</a>　　　　
                            <a href="?tag=1&{{$_SERVER['QUERY_STRING']}}">导出本次搜索数据</a>　　
                            <span>按时间搜索结果总邀请人数：{{$sum}}</span>　　
                            <span>按时间搜索结果参团总人数：{{$isJoinNum}}</span>
                        </div>
                    </form>
                </div>
                <ul class="ulDiv table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>业务员名称</li>
                    <li>业务员电话</li>
                    <li>邀请人数</li>
                    <li>参团人数</li>
                    <li>操作</li>
                </ul>
                <form class="listForm">
                    @if($data[0]['data'])
                        @foreach($data[0]['data'] as $v)
                    <ul class="ulDiv table_body  flex-between">
                        <li><input class="signlChoose" type="checkbox" data-id="{{ $v['id']}}" name='ids[]' value="" /></li>
                        <li>{{$v['name'] }}</li>
                        <li>{{$v['mobile'] }}</li>
                        <li>{{$v['num'] }}</li>
                        <li>{{$v['groupsNum']??0 }}</li>
                        <li><a href="/staff/seller/getSaleManDetail?id={{$v['wx_mid']}}">查看详情</a></li>


                    </ul>
                        @endforeach
                            <div class="main_bottom flex_end">
                                {{ $data[1] }}
                            </div>
                    @else
                        <div class="xue-rdiv1" style="border-bottom:0; text-align: center; padding: 100px 0;">
                            该分类下暂无数据
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
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/seller_index.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script type="text/javascript">
        $(function(){
            laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
            var start = {
                elem: '#startDate',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2009-06-16 23:59:59', //设定最小日期为当前日期
                max: '2099-06-16 23:59:59', //最大日期
                event: 'focus',
                istime: true,
                istoday: false,
                choose: function(datas){
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas //将结束日的初始值设定为开始日
                }
            };
            var end = {
                elem: '#endDate',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2009-06-16 23:59:59',
                max: '2099-06-16 23:59:59',
                event: 'focus',
                istime: true,
                istoday: false,
                choose: function(datas){
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            };
            laydate(start);
            laydate(end);
        })


    </script>
@endsection