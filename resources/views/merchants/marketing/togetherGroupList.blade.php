@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/together_o4uqlv05.css" />
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
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
                    <a href="javascript:void(0)">多人拼团</a>
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
                    <li @if(!request('status'))class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/togetherGroupList') }}">所有促销</a>
                    </li>
                    <li @if(request('status') == 1)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/togetherGroupList?status=1') }}">未开始</a>
                    </li>
                    <li @if(request('status') == 2)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/togetherGroupList?status=2') }}">进行中</a>
                    </li>
                    <li @if(request('status') == 3)class="hover"@endif>
                        <a href="{{ URL('/merchants/marketing/togetherGroupList?status=3') }}">已结束</a>
                    </li>
                </ul>  
            </div>
            <!-- <a class="nav_module_blank" href="{{ config('app.url') }}home/index/detail/625/help" target="_blank">查看【多人拼团】使用教程</a> -->
        </div> 
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-20 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="btn btn-success new_btn" href="{{ URL('/merchants/marketing/togetherGroupAdd') }}">新建拼团活动</a>
                </div>
                <div style="position: relative;">
                    <div class="js-list-search ui-search-box">
                        <form action="">
                            <input class="txt" name="status" value="{{request('status')}}" type="hidden" placeholder="">
                            <input class="txt" name="title" value="{{request('title')}}" type="search" placeholder="搜索">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 拼团列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>活动名称</li>
                <li>有效时间</li>
                <li>活动状态</li>
                <li>拼团类型</li>
                <li>订单实付金额<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i><div class="data_tip">拼团活动带来的总付款金额（包含退款）</div></li>
                <li>成团订单数<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i><div class="data_tip">成团订单数量</div></li>
                <li>成团人数<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i><div class="data_tip">拼团成功的客户数</div></li>
                <li>收藏数</li>
                <li class="text-right">操作</li>
            </ul>
            @foreach( $data[0]['data'] as $val)
            <ul class="data_content">
                <li class="blue">{{$val['title']}}</li>
                <li>{{$val['start_time']}}至 {{$val['end_time']}}</li>
                <li class="gray1">
                    @if($val['state']== -1)
                        已失效
                        @elseif($val['state'] == 1)
                        正在进行中
                    @elseif($val['state'] == 2)
                        未开始
                    @elseif($val['state'] == 3)
                        已结束
                    @endif
                </li>
                <li>
                   @if ($val['group_type'] == 1)
                   普通拼团
                   @elseif ($val['group_type'] == 2)
                   9.9元拼团
                   @elseif ($val['group_type'] == 3)
                   0元开团
                   @elseif ($val['group_type'] == 4)
                   团长半价
                   @elseif ($val['group_type'] == 5)
                   团长折扣
                   @elseif ($val['group_type'] == 6)
                   抽奖团
                   @else
                   普通拼团
                   @endif
                </li>
                <li>{{$val['total_pay_price']}}</li>
                <li>{{$val['total_group_order_num']}}</li>
                <li>{{$val['total_group_member_num']}}</li>
                <li>{{$val['favoriteCount']}}</li>
                <li class="text-right pr">
                @if($val['state'] == 2)

                        <a class="spread"  data-id="{{$val['id']}}"  data-title="{{$val['title']}}" data-groupNum="{{$val['groups_num']}}" data-price="{{$val['min']}}" data-img1="{{ imgUrl() }}{{$val['product']['img']}}" data-img2="" data-url='{!! URL("/shop/grouppurchase/detail/".$val['id']."/".session('wid')) !!}' href="javascript:void(0);">推广</a>
                        <div class="qrcode" style="display: none">
                            {!! QrCode::size(150)->generate(URL("/shop/grouppurchase/detail/".$val['id']."/".session('wid'))); !!}
                        </div>
                        -<a href="/merchants/marketing/togetherGroupAdd?id={{$val['id']}}">编辑</a>
                        -<a class="invalid" data-id="{{$val['id']}}" href="javascript:void(0);">始失效<span style="color:#FF6600">[?]</span></a>
                    @elseif($val['state'] == 1)
                        <a class="spread" data-id="{{$val['id']}}" data-title="{{$val['title']}}" data-groupNum="{{$val['groups_num']}}" data-price="{{$val['min']}}" data-img1="{{ imgUrl() }}{{$val['product']['img']}}" data-img2="" data-url='{!! URL("/shop/grouppurchase/detail/".$val['id']."/".session('wid')) !!}' href="javascript:void(0);">推广</a>
                        <div class="qrcode" style="display: none">
                            {!! QrCode::size(150)->generate(URL("/shop/grouppurchase/detail/".$val['id']."/".session('wid'))); !!}
                        </div>
                        -<a class="invalid" data-id="{{$val['id']}}" href="javascript:void(0);">结束活动<span style="color:#FF6600">[?]</span></a>
                        -<a href="/merchants/marketing/togetherGroupAdd?id={{$val['id']}}">编辑</a>
                    @else
                    <a href="/merchants/marketing/togetherGroupAdd?id={{$val['id']}}">查看</a>
                    -<a class="delete" data-id="{{$val['id']}}" href="javascript:void(0);">删除</a>
                    @endif
                    @if(in_array(session('wid'),config('app.li_wid')))
                        -<a href="/merchants/marketing/getRemark?rule_id={{$val['id']}}">留言列表</a>
                    @endif
                    -<a class="watch_data" href="javascript:void(0)" data-id="{{$val['id']}}" data-title="{{$val['title']}}">查看数据</a>
                </li>
               
            </ul>
            @endforeach
            <!-- <ul class="data_content">暂无数据</ul> --> 
        </div> 
        <!-- 拼团列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
            {{$data[1]}}
        </div>
        <!--add by 韩瑜 date 2018.7.13-->
        <!--查看数据弹窗-->
        <div class="data_model" style="position:fixed;top:0;left:0;background-color:rgba(0,0,0,0.4);width:100%;height:100%;z-index:1000;display:none">
        	<div class="data_model_content">
        		<div class="data_model_title">
        			<p>查看数据</p>
        			<span>数据导出</span>
        			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        		</div>
        		<div class="data_model_body">
        			<p class="data_model_name">活动名称：<span></span></p>
        			<div class="data_model_list">
        				<ul>
        					<!--<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        						   <div class="data_tip">
        						大撒的撒多所多
        						</div>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>
        					<li>
        						<p>订单实付金额</p>
        						<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>
        						<span>￥150.00</span>
        					</li>-->
        				</ul>
        			</div>
        		</div>
        	</div>
        </div>
        <!--提示弹窗-->
        
    </div>
@endsection

@section('page_js')
<script type="text/javascript">
	var dcUrl = "{{ config('app.dc_url')}}"
	var wid = "{{session('wid')}}"
</script>
    <script src="{{ config('app.source_url') }}static/js/require.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/together_o4uqlv05.js"></script> 
@endsection