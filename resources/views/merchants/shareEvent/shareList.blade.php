@extends('merchants.default._layouts')
@section('head_css')
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/share_list.css" />
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
                    <a href="javascript:void(0)">享立减</a>
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
                    <li @if(!isset($_GET['type']))class="hover" @endif>
                        <a href="/merchants/shareEvent/list">所有活动</a>
                    </li>
                    <li @if(isset($_GET['type']) && $_GET['type'] == 0)class="hover" @endif>
                        <a href="/merchants/shareEvent/list?type=0">有效</a>
                    </li>
                    <li @if(isset($_GET['type']) && $_GET['type'] == 1)class="hover" @endif>
                        <a href="/merchants/shareEvent/list?type=1">已失效</a>
                    </li>
                </ul>  
            </div>
            <a class="nav_module_blank" href="##" data-toggle="modal" data-target="#setXiang">享立减设置</a>
        </div> 
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-15 clearfix pr">
            <div class="widget-list-filter">
            	<div class="pull-left">
                    <a class="btn btn-success " href="{{ URL('/merchants/shareEvent/create') }}">新建享立减活动</a>
                </div>
                <div class="pull-right">
                    <input type="text" name="searchInput" id="searchInput" placeholder="请输入商品名称"/>
                    <!--<span style="font-weight: bold;font-size: 15px;">是否开启红包?</span>
                    <span class="switch fr switchEnvelope" data-toggle="modal" data-target="#envelopeRule"></span>
                    <div class="btn btn-success openEnvelopeModal" data-toggle="modal" data-target="#envelopeModal">设置红包</div>-->
                    <div class="js-list-search ui-search-box" style="display: inline-block;">
                        <div class="btn btn-primary sure_btn" data-toggle="modal" data-target="#myModal">一键翻新</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>活动名称</li>
                <li>商品名称</li>
                <li>创建时间</li>
                <li>活动时间</li>
                <li>活动状态</li>
                <li>状态</li>
                <li>收藏量</li>
                <li class="">操作</li>
            </ul>
            @foreach($list as $v)
            <?php
                $typeName = $v['type'] == 1 ? '已失效' : '有效';
            ?>
            <ul class="data_content">
                <li class="blue ovhid shop_name" title="{{$v['title']}}"><a href="/merchants/shareEvent/create?id={{$v['id']}}">{{$v['title']}}</a></li>
                <li class="gray1 shop_name" title="{{$v['product_name']??''}}">{{$v['product_name']??''}}</li>
                <li class="gray1">{{$v['created_time']}}</li>
                <li class="gray1">{{$v['start_time']}}至{{$v['end_time']}}</li>
                <li class="gray1">{{$v['activityStatusName']}}</li>
                <li class="gray1">
        			{{$typeName}}
                </li>
                <li class="gray1">
        			{{$v['favoriteCount']}}
                </li>
                <li class="pr">                    
                    <a class="see_create" href="/merchants/shareEvent/create?id={{$v['id']}}">编辑</a>
                    @if($v['type']==1)
                    -<a class="delete" href="javascript:void(0);" data-id="{{$v['id']}}">删除</a>
                    @endif
                    @if($v['type']==0)
                        - <a class="spread" data-id="{{$v['id']}}" data-title=" {{$v['title']}}" data-groupNum="{{$v['unit_amount']/100}}" data-price="{{$v['lower_price']/100}}" data-img1="{{imgUrl($v['act_img'])}}" data-img2="" data-url='pages/activity/pages/shareSale/shareSale/shareSale?activityId={{$v['id']}}&list_Or_url=0' href="javascript:void(0);">推广</a>
                        -<a class="invalid" href="javascript:void(0);" data-id="{{$v['id']}}">使失效<span style="color:#ff6600">[?]</span></a>
                    @endif
                    -<a class="see_create" href="/merchants/shareEvent/ShareEventDataAnalysis?id={{$v['id']}}">查看数据</a>
                </li>
            </ul>
            @endforeach
            <!-- <ul class="data_content">暂无数据</ul> --> 
        </div> 
        <!-- 列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
            {{$pageHtml}}
        </div>
        <!--一键翻新-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        	<div class="modal-dialog" role="document">
        		<div class="modal-content">
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        				<h4 class="modal-title" id="myModalLabel">享立减一键翻新</h4>
        			</div>
        			<div class="modal-body">
        				一键翻新后，店铺所有享立减的老用户将会变成新用户，可以再次帮助分享者减价。
        			</div>
        			<div class="modal-footer">
        				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        				<button type="button" class="btn btn-primary btn_refresh">确定</button>
        			</div>
        		</div>
        	</div>
        </div>
        <!--红包规则说明-->
        <div class="modal fade" id="envelopeRule" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">红包规则说明</h4>
                    </div>
                    <div class="modal-body">
                        <p>开启红包后,您可以设置红包的金额</p>
                        <p>用户每天都可以领一个红包，红包当天有效，过期失效。</p>
                        <p>用户分享了享立减活动商品后，该红包自动抵扣领取的金额。</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary rule_sure">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <!--享立减设置弹框-->
        <div class="modal fade" id="setXiang" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <p class="modal-title" id="myModalLabel">享立减设置</p>
                    </div>
                    <div class="modal-body">
                        <h4>生成卡片设置</h4>
                        <div class="control-group clear">
                            <label class="control-label required">卡片图片设置：</label>
                            <div class="controls">
                                <label class="input-append">
                                	<div id="">
	                                    <div class="sel-goods card_img">
	                                        <img class="img-goods" src="">
	                                        <!-- <span class="remove-img">×</span> -->
	                                    </div>
	                                    <div class="image-wrap image-warp-active">
	                                        <div class="js-upload-image add_active_img card_img_add" data-imgadd="1">+添加</div>
	                                    </div>                                		
                                	</div>
                                    <p class="up_tip">建议宽高尺寸：750*1334</p>
                                </label>
                            </div>
                        </div>
                        <h4 style="margin-top: 15px;">分享设置</h4>
                        <div class="control-group clear" style="margin-bottom: 20px;">
                            <label class="control-label required">分享标题设置：</label>
                            <div class="controls">
                                <input class="form-control z-title" type="text" name="share_title" maxlength="23">
                                <p class="up_tip" style="margin-left:105px">不超过23个汉字</p>
                            </div>
                        </div>

                        <div class="control-group clear">
                            <label class="control-label required">分享图片：</label>
                            <div class="controls">
                                <label class="input-append">
                                	<div class="">
	                                    <div class="sel-goods share_img">
	                                        <img class="img-goods" src="">
	                                        <!-- <span class="remove-img">×</span> -->
	                                    </div>
	                                    <div class="image-wrap image-warp-active">
	                                        <div class="js-upload-image add_active_img share_img_add"  data-imgadd="2">+添加</div>
	                                    </div>                                		
                                	</div>
                                    <p class="up_tip">建议宽高尺寸：420*336</p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary xiang_sure">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 设置红包弹框 -->
        <div class="modal fade" id="envelopeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">红包设置</h4>
                    </div>
                    <div class="modal-body">
                        <div class="set_envelope">
                           <div class="envelope_label required">新人红包：</div>
                           <div class="envelope_config">
                               <div >
                                   <label>
                                       <input type="radio" name="type" value="0" checked>固定
                                   </label>
                                   <label>
                                       <input type="radio" name="type" value="1">随机
                                   </label>
                               </div>
                               <div class="config_item">
                                   每人减
                                   <input class="form-control wd_80 moneyLimit" type="number" name="fixed">
                                   元
                               </div>
                               <div class="config_item none">
                                   每人减
                                   <input class="form-control wd_80 moneyLimit" type="number" name="minimum">
                                   至
                                   <input class="form-control wd_80 moneyLimit" type="number" name="maximum">
                                   元
                               </div>
                           </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary setEnvelope">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 推广弹窗 -->
		<!--updata by 韩瑜 2018-8-13-->
		<div class="widget-promotion widget-promotion1" style="display: none;">
		    <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
		        <li class="wsc_code active">微商城</li>
		        <li class="xcx_code">小程序</li>
		    </ul>
		    <div class="widget-promotion-content js-tabs-content">
		    	<!--微商城-->
		        <div class="js-tab-content-wsc" style="display: block;">
		            <div>
		                <div class="widget-promotion-main">
		                    <div class="js-qrcode-content">
		                        <div class="widget-promotion-content">
		                            <label>商品页链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
		                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">商品页二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
		                                <div class="qrcode">
		                                    <div class="qr_img"></div>
		                                    <div class="clearfix qrcode-links">
		                                        <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
		                                    </div>
		                                </div>
		                           	</div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		        <!--小程序-->
		        <div class="js-tab-content-xcx" style="display: none;">
		            <div>
		                <div class="widget-promotion-main">
		                    <div class="js-qrcode-content">
		                        <div class="widget-promotion-content">
		                            <label>小程序链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="" />
		                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">小程序二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
		                                <div class="qrcode">
		                                    <div class="qr_img_xcx"></div>
		                                    <div class="clearfix qrcode-links">
		                                        <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
		                                    </div>
		                                </div>
		                           </div>
		                        </div>           	
		                    </div> 
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<!--end-->
    </div>
@endsection

@section('page_js')
<script type="text/javascript">
	var _host = "{{ imgUrl() }}";
	var wid = {{session('wid')}};
    var reduceData = {!! json_encode($reduceData) !!};//红包 享立减设置数据
    console.log(reduceData);
    // var _href = "{{ config('app.source_url') }}merchants/shareEvent/list";
    var _href = "{{ URL('/merchants/shareEvent/list') }}";
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/share_list.js"></script> 
@endsection