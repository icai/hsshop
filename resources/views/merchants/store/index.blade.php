@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/shop_1qdhfeb3.css" />
@endsection
@section('slidebar')
@include('merchants.store.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <ul class="common_nav">
            <li @if(isset($_GET['is_show']) && $_GET['is_show'] == '1') class="hover" @endif>
                <a href="/merchants/store?is_show=1">微页面</a>
            </li>
            <li @if(isset($_GET['is_show']) && $_GET['is_show'] == '0') class="hover" @endif>
                <a href="/merchants/store?is_show=0">微页面草稿</a>
            </li>
            <li>
                <a href="/merchants/store/pagecat">微页面分类</a>
            </li>
        </ul>
    </div>
    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!-- logo区域 开始 -->
    <div class="content_header">
        @if ( !empty($store['logo']) )
        <img src="{{ imgUrl($store['logo]'])}}" width="40" height="40" />
        @elseif ( !empty($store_logo) )
        <img src="{{imgUrl($store_logo)}}" width="40" height="40" />
        @else
        <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" width="40" height="40" />
        @endif
        <!-- 店铺名称 开始 -->
        <div class="title_content">
            <p class="homepage_title">
                <strong>店铺主页</strong>(<span>{{ $store['page_title']??'店铺主页' }}</span>)
            </p>
            <p class="title_des">创建时间：<span>{{ $store['created_at']??'2017-01-01' }}</span></p>
        </div>
        <!-- 店铺名称 结束 -->
        <!-- 链接 开始 -->
        <div class="link_itmes">
            <div class="link_tab">
			    <a href="/merchants/store/showMicroPage/edit/{{$store['id']??0}}">
                    <span>编辑</span>
				</a>
            </div>
            <div class="link_btn link_tab" data-url='{{URL("/shop/index/$wid")}}'>
                <span>链接</span>
            </div>
            <div class="QRcode_items link_tab">
                <span>二维码</span> 
                <!-- 二维码 开始 -->
                <div class="shop_QRcode">
                    <p class="items_title">手机扫码访问</p>
                    <div class="RQ_code img_wrap">
                        {!! QrCode::size(150)->generate(URL("/shop/index/$wid")); !!}
                    </div>
                    <div class="QRcode_bottom">
                        <!--<a href="javascript:void(0);">下载二维码</a>-->
                    </div>
                </div>
                <!-- 二维码 结束 -->
            </div>
        </div>
        <!-- 链接 结束 -->
    </div>
    <!-- logo区域 结束 -->
    <!-- 新建模板 开始 -->
    <div class="clearfix mgb20"></div>
    <div class="model_itmes mgb20">
	    <a href="javascript:void(0);" class="btn btn-success" id="add_page">新建微页面</a>
        <!-- 分类&搜索 开始 -->
        <div class="category_search">
            <!-- 分类 开始 -->
            <form method="get" action="" name="categoryForm">
                <select class="chzn-select category_items" data-placeholder="请选择分类" tabindex="1" name="type"> 
                    <option value="0">全部分类</option>
                    @forelse($pageTypeList as $item)
                    <option value="{{$item['id']}}"@if ( request('type') == $item['id'] )selected @endif >{{$item['title']}}</option>  
                    @endforeach
                </select>
                <!-- 搜索 开始 -->
                <label class="search_items">
                    <input class="search_input" type="text" name="title" value="{{ request('title') }}" placeholder="搜索"/>   
                </label>
                <!-- 搜索 结束 -->
            </form>
            <!-- 分类 结束 -->
        </div>
        <!-- 分类&搜索 结束 -->
        <!-- 管理 开始 -->
        <div class="manage_items">
            <!-- 按钮 开始 -->
            <button type="button" class="grounp_btn btn btn-default">批量改分类</button>
            <!-- 按钮 结束 -->
            <!-- 提示框 开始 -->
            <div class="manage_tip">
                <!-- 未分组 开始 -->
                <div class="ungrouped_items">
                    <p class="items_title">您未创建分类</p>
                    <a class="blue_38f" href="javascript:void(0);">管理分类</a>
                </div>
                <!-- 未分组 结束 -->
                <!-- 分组管理 开始 -->
                <div class="grouped_items">
                    <!-- 分组头 开始 -->
                    <div class="grouped_header">
                        修改分类
                        <a class="blue_38f pull-right" href="/merchants/store/pagecat">管理</a>
                    </div>
                    <!-- 分组头 结束 -->
                    <!-- 分组body 开始 -->
                    <div class="grouped_body">
					@forelse($pageTypeList as $item)
                        <label title="{{$item['title']}}">
                            <input type="radio" name="page_group" value="{{ $item['id'] }}" style="display:none"/>
                            <span></span>
                            {{ $item['title'] }}
                        </label>
					@endforeach
                    </div>
                    <!-- 分组body 结束 -->
                    <!-- 分组底部 开始 -->
                    <div class="grouped_footer">
                        <button type="button" class="btn btn-info btn-sm sure_chanage_category" >确定</button>
                        <button type="button" class="btn btn-default btn-sm cancel_chanage_category">取消</button>
                    </div>
                    <!-- 分组底部 结束 -->
                </div>
                <!-- 分组管理 结束 -->
            </div>
            <!-- 提示框 结束 -->
        </div>
        <!-- 管理 结束 -->
    </div>
    <!-- 新建模板 结束 -->
    <!-- 列表 开始 -->
    <table class="data-table table table-hover">
        <!-- 标题 -->
        <tr class="active">
            <td><input class="check_all" type="checkbox" name="" value="" >标题</td>
            <td>
                所属分组
            </td>
            <td>
                @if(isset($_GET['orderby']) && $_GET['orderby'] =='created_at' && $_GET['order'] == 'desc')
                <a href="javascript:;" data-orderby="created_at" onclick="sort_desc(0,0)">创建时间
                    <span class="orderby-arrow desc"></span>
                </a>
                @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='created_at' && $_GET['order'] == 'asc')
                <a href="javascript:;" data-orderby="created_at" onclick="sort_desc(0,1)">创建时间
                    <span class="orderby-arrow asc"></span>
                </a>
                @else
                <a href="javascript:;" data-orderby="created_at" onclick="sort_desc(0,1)">创建时间
                </a>
                @endif
            </td>
            <td>
                商品数
            </td>
            <td>浏览UV/PV</td>
            <!-- <td>到店UV/PV</td> -->
            <td>
                @if(isset($_GET['orderby']) && $_GET['orderby'] =='sequence_number' && $_GET['order'] == 'desc')
                <a href="javascript:;" data-orderby="sequence_number"
             onclick="sort_desc(1,0)">
                序号
                    <span class="orderby-arrow desc"></span>
                </a>
                @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='sequence_number' && $_GET['order'] == 'asc')
                <a href="javascript:;" data-orderby="sequence_number"
                 onclick="sort_desc(1,1)">
                    序号
                    <span class="orderby-arrow asc"></span>
                </a>
                @else
                <a href="javascript:;" data-orderby="sequence_number" onclick="sort_desc(1,1)">序号
                </a>
                @endif
            </td>
            <td>操作</td>
        </tr>
        <!-- 列表 -->
		@forelse($microPageList as $k => $item)
        <tr>
            <td><input class="check_single" type="checkbox" name="" value="{{$item['id']}}"/>
                <a href="{{config('app.url')}}shop/page/preview/{{$wid}}/{{$item['id']??0}}">{{ $item['page_title'] }}</a>
			</td>
            <td>
                @if($item['type_flag'] == 1) <a href="/merchants/store/pagecat?id={{ $item['type_id'] }}">{{ $item['type_title'] }}</a>@else {{ $item['type_title'] }} @endif
            </td>
            <td>{{ $item['created_at'] }}</td>
            <td>{{ $item['product_num']}}</td>
            <td>{{ $biData[$k]['viewuv'] ?? 0 }}/{{ $biData[$k]['viewpv'] ?? 0 }}</td>
            <!-- <td>0/0</td> -->
            <td>
                <a class="js-change-num" href="javascript:void(0);" style="display: inline;">{{ $item['sequence_number'] }}</a>
                <input class="input-mini js-input-num form-control" type="number" min="0" maxlength="8" style="display: none;" value="{{ $item['sequence_number'] }}">
            </td>
            <td class="opt_wrap">
                @if($isCreate)
                    <a class="copy_list" href="javascript:void(0);" data-id="{{ $item['id'] }}">
                        <span class="blue_38f">复制</span>
                    </a>
                @endif
                <a href="/merchants/store/showMicroPage/edit/{{ $item['id'] }}">
                    <span class="blue_38f">编辑</span>
                </a>
                @if ($item['is_home']== 0)
                <a class="del_list" href="javascript:void(0);" data-id="{{ $item['id'] }}">
                    <span class="blue_38f">删除</span>
                </a>
                <a class="link_btn" href="javascript:void(0);" data-url="{{config('app.url')}}shop/microPage/index/{{$wid}}/{{$item['id']??0}}">
                    <span class="blue_38f">链接</span>
                </a>
                @elseif ($item['is_home']== 1)
                    <a class="link_btn" href="javascript:void(0);" data-url="{{config('app.url')}}shop/index/{{$wid}}">
                        <span class="blue_38f">链接</span>
                    </a>
                @endif
                <a class="set_homepage" href="javascript:void(0);" data-id="{{ $item['id'] }}">
                    @if ($item['is_home']== 1)
					<span>店铺主页</span>
					@else
					<span class="blue_38f">设为主页</span>
					@endif
                </a>
            </td>
        </tr>
        @empty
        <!--<div class="no_result">暂无数据!</div>-->
		@endforelse
    </table>
    <!-- 列表 结束 -->
    <!-- 管理和分页 开始 -->
    <div class="manage_page">
        <!-- 分页 开始 -->
        <div class="page_items">
             {{ $pageHtml }}
        </div>
		
        <!-- 分页 结束 -->
    </div>
    <!-- 管理和分页 结束 -->
</div>
<!-- 弹框 开始 -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- 弹框标题 开始 -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">选择新建模板</h4>
            </div>
            <!-- 弹框标题 结束 -->
            <!-- 弹框主体 开始 -->
            <div class="modal-body">
                主体
            </div>
            <!-- 弹框主体 结束 -->
        </div>
    </div>
</div>
<!-- 弹框 结束 -->
<!-- 微页面选择模板弹窗 -->
<div class="widget-feature-template modal in" aria-hidden="false">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3 class="title">选择页面模版</h3>
    </div>
    <ul class="widget-feature-template-filter js-filter-wraper">
        <li class="active">
            <a href="javascript:;" class="js-filter" data-type="0">所有模版</a>
        </li>
        <li>
            <a href="javascript:;" class="js-filter" data-type="1">美妆配饰</a>
        </li>
        <li>
            <a href="javascript:;" class="js-filter" data-type="2">服饰衣帽</a>
        </li>
        <li>
            <a href="javascript:;" class="js-filter" data-type="3">节日活动</a>
        </li>
        <li>
            <a href="javascript:;" class="js-filter" data-type="4">官网展示</a>
        </li>
        <li>
            <a href="javascript:;" class="js-filter" data-type="5">博渊书院</a>
        </li>
    </ul>
    <div class="modal-body">
        <ul class="widget-feature-template-list clearfix">
           
        </ul>
    </div>
    <div class="modal-footer"></div>
</div>
<!-- 微页面选择模板弹窗 -->
<!-- 背景弹窗 -->
<div class="modal-backdrop false in"></div>
<!-- 背景弹窗 -->
@endsection
@section('page_js')
<!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/shop_1qdhfeb3.js"></script>
<script>
    var isCreate = "{{ $isCreate }}";
    $(function(){
        $('.QRcode_bottom a').click(function(){
            var card_id = $(this).data('id');
            var url = "{{URL('/merchants/member/memberCard/down_qrcode')}}";
            /**
             * 会员卡编号要通过点击发卡，然后先保存到弹窗的div中，然后再点击下载的时候获得相应的卡号
             * 这里的12是仅供测试使用
            */
            window.location.href= url+'?card_id='+card_id+'&qrcode_type=store';
        });
    });

</script>
@endsection
