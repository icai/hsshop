@extends('merchants.default._layouts')
@section('head_css')
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
    <!--时间插件css引入-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/registerList_ty20180124.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            <ul class="common_nav">
                <li class="hover">
                    <a href="##" onclick="window.history.go(-1)">团购列表</a>
                </li>
                <li>
                    {{--<a href="{{URL('/merchants/member/import')}}">导入会员</a>--}}
                </li>
            </ul>
            <!-- 普通导航 结束  -->
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
        <div class="search">
            <form class="form-inline" method="get" action="">
                <input type="hidden" name="rule_id" value="{{request('rule_id')}}">
                <div class="form-group">
                    <label for="phoneNumber">留言内容:</label>
                    <input type="text" class="form-control" name="content" value="{{request('content')}}" id="phoneNumber" placeholder="留言内容" style="width: 130px;">
                </div>
                <div class="form-group">
                    <label for="memberName">团购状态:</label>
                    <select name="status">
                        <option value="">全部</option>
                        <option @if(request('status')==1) selected @endif value="1">待成团</option>
                        <option @if(request('status')==2) selected @endif  value="2">已成团</option>
                        <option  @if(request('status')==3) selected @endif value="3">未成团</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="memberName">是否团长:</label>
                    <select name="is_head">
                        <option @if(request('is_head')=='') selected @endif  value="">全部</option>
                        <option @if(request('is_head')=='1') selected @endif value="1">是</option>
                        <option @if(request('is_head')=='0') selected @endif value="0">否</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_time">开始时间:</label>
                    <input type="text" name="starttime" value="{{request('starttime')}}" id="startDate">
                </div>
                <div class="form-group">
                    <label for="end_time">结束时间:</label>
                    <input type="text" name="endtime" value="{{request('endtime')}}" id="endDate">
                </div>
                <div class="row btns">
                    <button type="submit" class="btn btn-primary">筛选</button>
                    <a href="?rule_id={{request('rule_id')}}&type=1">导出信息</a>
                    <label>　　　　　　搜索总数：{{$result['count']}}</label>
                </div>
            </form>
        </div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th>订单编号</th>
            <th>参团人数</th>
            <th>团购状态</th>
            <th>是否是团长</th>
            <th>查看</th>
            <th>参团时间</th>
            <th>成团时间</th>
            <th>留言信息</th>
        </tr>
        </thead>
        <tbody >
        @forelse ( $result['data'] as $v )
            <tr>
                <td>{{$v['oid']}}</td>
                <td>{{$v['num']}}</td>
                <td>
                    @if($v['status']==0)
                        未开团
                        @elseif($v['status']==1)
                        待成团
                        @elseif($v['status']==2)
                        已成团
                        @elseif($v['status']==3)
                        未成团
                        @endif
                </td>
                <td>@if(isset($v['is_head']) && $v['is_head'] == 1)是@else否@endif</td>
                <td><a href="?goups_id={{$v['id']}}">查看同团</a></td>
                <td>{{$v['created_at']}}</td>
                <td>@if($v['complete_time']=='0000-00-00 00:00:00') 未成团 @else {{$v['complete_time']}} @endif</td>
                <td style="text-align: left">
                    @forelse($v['remark'] as $item)
                        {{$item['title']??''}} : @if(isset($item['type']) && $item['type'] == 6)<a href="{{imgUrl()}}{{$item['content']??''}}" target="_blank">点击查看图片</a>@else{{$item['content']??''}}@endif<br/>
                     @endforeach
                </td>
            </tr>


        @endforeach


        </tbody>
    </table>
    {{$result['page']}}

@endsection
@section('page_js')
    <!--时间插件引入的JS文件-->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <!-- layer选择时间插件 -->
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script>
        var imgUrl = "{{ imgUrl() }}";//动态图片域名
    </script>
    <!-- 当前页面js -->
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
