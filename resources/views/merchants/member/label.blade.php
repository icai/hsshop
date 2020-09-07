@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_common_q2gir63n.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_qkwcmsll.css">

@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection

@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="common_nav">
                <li class="hover">
                    <a href="#&status=1">标签管理</a>
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
            <div class="content_header">
                <div class="btn_grounp">
                    <a href="/merchants/member/label/add" class="btn btn-success">新建标签</a>
                    <a class="btn btn-default"  href="/merchants/member/label/csv">导出标签</a><!--  target="_blank" /merchants/member/label/csv -->
                    <a class="btn btn-default"  href="/merchants/member/label/xls">导出excel</a>
                </div>
                <div class="search">
                </div>
            </div>
            <div class="content_list">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="col-sm-2">标签名</th>
                        <th class="col-sm-2">微信会员</th>
                        <th class="col-sm-2">手机会员</th>
                        <th class="col-sm-3">自动加标签条件</th>
                        <th class="col-sm-3">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($label as $l)
                    <tr>
                        <td>{{$l['rule_name'] or ''}}</td>
                        <td>
                        @if(!empty($l['w_name_num']) && $l['w_name_num']>0)
                           <a target="_blank" href="{{URL('/merchants/member/fans/screen?tag_id='.$l['id'])}}" >
                            {{$l['w_name_num'] or 0}}
                           </a>
                        @else
                            0
                        @endif
                        </td>
                        <td>{{$l['mobile_name_num'] or 0}}</td>
                        <td>
                        @php
                        $whereTag = ['trade_limit'=>$l['trade_limit'],'amount_limit'=>$l['amount_limit'],'points_limit'=>$l['points_limit']];
                        if(!empty($l['trade_limit'])){
                            $whereTag['trade_limit'] = '累计成功交易 '.$l['trade_limit'].' 笔';
                        }else{
                            unset($whereTag['trade_limit']);
                        }
                        if(!empty($l['amount_limit'])){
                            $whereTag['amount_limit'] = '累计购买金额 '.$l['amount_limit'].' 元';
                        }else{
                            unset($whereTag['amount_limit']);
                        }

                        if(!empty($l['points_limit'])){
                            $whereTag['points_limit'] = '累计积分达到 '.$l['points_limit'];
                        }else{
                            unset($whereTag['points_limit']);
                        }
                        if(empty($whereTag)){
                            $td = '未设置';
                        }else{
                            $td = implode("</br>",$whereTag);
                        }

                        echo $td;
                        @endphp
                        </td>
                        <td><a href="{{URL('/merchants/member/label/del').'/'.$l['id']}}" class="delete">删除</a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="page">
                    <?php
                        echo $pageList;
                    ?>
                </div>
            </div>
        </div>
    
@endsection

@section('other')
<!-- 删除弹窗 -->
<div class="popover del_popover left" role="tooltip">
    <div class="arrow"></div>
    <div class="popover-content">
        <span>你确定要删除吗？</span>
        <button class="btn btn-primary sure_btn" >确定</button>
        <button class="btn btn-default cancel_btn">取消</button>
    </div>
</div>
<!--弹层-->
<div class="tip"></div>
@endsection

@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- layer -->
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_ldmupx9r.js"></script>
@endsection