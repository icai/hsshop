<link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
<!-- 核心Bootstrap.css文件（每个页面引入） -->

<link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
<!-- 搜索美化插件 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
<!-- 核心base.css文件（每个页面引入） -->

<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/help_common.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />
    <!--时间插件css引入-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/registerList_ty20180124.css" />
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            <ul class="common_nav">
                <li class="hover">
                    {{--<a href="##" onclick="window.history.go(-1)">团购列表</a>--}}
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

    <table class="table table-hover">
        <thead>
        <tr>
            <th>订单编号</th>
            <th>参团人数</th>
            <th>团购状态</th>
            <th>是否是团长</th>
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
