@extends('merchants.default._layouts')
@section('head_css')
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_do2yfvy3.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li>通用设置</li>
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
    <form class="form-horizontal" id="shopConfigForm" role="form" method="post" action="{{ URL('/merchants/currency/generalSet') }}">
        <!--标题设置-->
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{ $detail['id'] or '' }}" />
        <input type="hidden" name="wid" value="{{ session('wid') }}" />
        <div class="form-group title">
            <label for="adressName" class="col-sm-2 control-label">标题设置：</label>
            <div class="col-sm-7">
                <label>
                    @if ( isset($detail['is_title']) && !empty($detail['is_title']) )
                    <input type="checkbox" class="js-readme" name="is_title" id="is_title_suffix" value="1" checked />
                    @else
                    <input type="checkbox" class="js-readme" name="is_title" id="is_title_suffix" value="1" />
                    @endif
                    页面标题后加统一后缀：
                </label>
                <label><input type="text" class="form-control" name="title" id="title_suffix" value="{{ $detail['title'] or '' }}" >(可手动修改)</label><br />
                <label>
                    @if ( isset($detail['is_shopname']) && !empty($detail['is_shopname']) )
                    <input type="checkbox" class="js-readme" name="is_shopname" id="use_shop_name" value="1" checked />
                    @else
                    <input type="checkbox" class="js-readme" name="is_shopname" id="use_shop_name" value="1" />
                    @endif
                    微信内打开微页面时，使用“店铺名称”作为页面标题
                </label>
                <div class="weixinTitleImg">
                    <img src="{{ config('app.source_url') }}mctsource/images/weixintitle.png"/>
                </div>
            </div>
        </div>
        <hr />
        <!--开启购物车-->
        <div class="form-group shopCar">
            <label for="adressName" class="col-sm-2 control-label">购物车：</label>
            <div class="col-sm-7">
                <label>
                    @if ( isset($detail['is_cart']) && !empty($detail['is_cart']) )
                    <input type="checkbox" class="js-readme" name="is_cart" id="show_cart" value="1" checked />
                    @else
                    <input type="checkbox" class="js-readme" name="is_cart" id="show_cart" value="1" />
                    @endif
                    开启购物车
                </label>
                <i>开启后，买家可以将商品“加入购物车”，最后一起结算。当购物车里有商品时，每个页面都将显示购物车图标</i><br />
                <div class="showCarImgs">
                    图标样式：
                    <label><input type="radio" name="cart_icon" id="cart_icon" value="1" checked /><img src="{{ config('app.source_url') }}mctsource/images/shopImg_1.png"/></label>
                    @for ($i = 2; $i < 5; $i++)
                    <label><input type="radio" name="cart_icon" id="cart_icon" value="{{ $i }}" @if (isset($detail['cart_icon']) && $detail['cart_icon'] == $i) checked @endif /><img src="{{ imgUrl() }}mctsource/images/shopImg_{{ $i }}.png"/></label>
                    @endfor
                </div>
            </div>
        </div>
        <hr />
        <!--成交记录-->
        <div class="form-group successTrad">
            <label for="" class="col-sm-2 control-label">销量及成交记录：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_record" id="show_sale_record" value="1" @if (isset($detail['is_record']) && $detail['is_record'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_record" id="show_sale_record" value="0" @if (!isset($detail['is_record']) || $detail['is_record'] == 0) checked="checked" @endif/>关闭</label>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">商品评价：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_comment" id="product_comment_type" value="2" @if (isset($detail['is_comment']) && $detail['is_comment'] == 2) checked="checked" @endif/>全网开启评价</label>
                <label><input type="radio" name="is_comment" id="product_comment_type" value="1" @if (isset($detail['is_comment']) && $detail['is_comment'] == 1) checked="checked" @endif/>全网关闭评价</label>
                <label><input type="radio" name="is_comment" id="product_comment_type" value="0" @if (!isset($detail['is_comment']) || $detail['is_comment'] == 0) checked="checked" @endif/>关闭全网评价及买家评论</label>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">更多商品推荐：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_more" id="show_more_recommend" value="1" @if (isset($detail['is_more']) && $detail['is_more'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_more" id="show_more_recommend" value="0" @if (!isset($detail['is_more']) || $detail['is_more'] == 0) checked="checked" @endif/>关闭</label>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">列表显示售罄商品：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_sellout" id="show_sellout_product" value="1" @if (isset($detail['is_sellout']) && $detail['is_sellout'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_sellout" id="show_sellout_product" value="0" @if (!isset($detail['is_sellout']) || $detail['is_sellout'] == 0) checked="checked" @endif/>关闭</label>
                <i>开启后，售罄商品会在列表显示，并显示“已售罄”标记</i>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">联系商家/在线客服：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_service" id="show_contact" value="1" @if (isset($detail['is_service']) && $detail['is_service'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_service" id="show_contact" value="0" @if (!isset($detail['is_service']) || $detail['is_service'] == 0) checked="checked" @endif/>关闭</label>
                <a href="##">使用帮助</a>
                <br />
                <i>开启后，买家可在商品详情页及订单详情页面联系到店铺客服</i>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">会搜云买家版收录：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_included" id="allow_buyer_record" value="1" @if (isset($detail['is_included']) && $detail['is_included'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_included" id="allow_buyer_record" value="0" @if (!isset($detail['is_included']) || $detail['is_included'] == 0) checked="checked" @endif/>关闭</label>
                <a href="##">使用帮助</a>
                <br />
                <i>允许收录，符合规则的商品将展示在会搜云买家版；若设置不允许，将在T+1天后开始生效</i>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">店铺顶部导航：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_nav" id="show_top_nav" value="1" @if (isset($detail['is_nav']) && $detail['is_nav'] == 1) checked="checked" @endif/>开启</label>
                <label><input type="radio" name="is_nav" id="show_top_nav" value="0" @if (!isset($detail['is_nav']) || $detail['is_nav'] == 0) checked="checked" @endif/>关闭</label>
                <a href="##">使用帮助</a>
                <br />
                <i>设置关闭，则不显示店铺顶部导航</i>
            </div>
        </div>
        <hr />
        <!--设置营业状态-->
        <div class="form-group operatingState">
            <label for="" class="col-sm-2 control-label">设置营业状态：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_business" id="openSell" class="business" value="1" @if (!isset($detail['is_business']) || $detail['is_business'] == 1) checked="checked" @endif/>营业</label>
                <label><input type="radio" name="is_business" id="closeSell" class="business" value="0" @if (isset($detail['is_business']) && $detail['is_business'] == 0) checked="checked" @endif/>休息</label>
                <a href="##">使用帮助</a>
                <br />
                <i>商家设置休息后，买家将不能购买任何商品，请谨慎操作</i>
            </div>
        </div>
        <div class="form-group openStore">
            <label for="" class="col-sm-2 control-label">设置自动开业：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_auto" id="noSetAutoOpen" class="autoOpen" value="0" @if (!isset($detail['is_auto']) || $detail['is_auto'] == 0) checked="checked" @endif/>不设置自动开业时间</label><br />
                <label><input type="radio" name="is_auto" id="setAutoOpen" class="autoOpen" value="1" @if (isset($detail['is_auto']) && $detail['is_auto'] == 1) checked="checked" @endif/>设置自动开业时间</label><br />
                <div class="openTime">开业时间：
                    <!--日期、时间选择-->
                    <div class='input-group date' id='datetimepicker'>
                        <input type='text' class="form-control" name="auto_time" value="{{ $detail['auto_time'] or '' }}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group businessTime">
            <label for="" class="col-sm-2 control-label">营业时间：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_all_day" id="allDay" class="business_time" value="1" @if (!isset($detail['is_all_day']) || $detail['is_all_day'] == 1) checked="checked" @endif/>全天</label>
                <label><input type="radio" name="is_all_day" id="selfSet" class="business_time" value="0" @if (isset($detail['is_all_day']) && $detail['is_all_day'] == 0) checked="checked" @endif/>自定义</label>
                <div class="businessRangTime">
                    <!--日期、时间选择-->
                    <div class='input-group date' id='datetimepicker1'>
                        <input type='text' class="form-control" name="business_start" value="{{ $detail['business_start'] or '' }}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>至
                    <div class='input-group date' id='datetimepicker2'>
                        <input type='text' class="form-control" name="business_end" value="{{ $detail['business_end'] or '' }}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">店铺底部LOGO：</label>
            <div class="col-sm-7">
                <label><input type="radio" name="is_footer_logo" id="" class="logoSet" value="0" @if (!isset($detail['is_footer_logo']) || $detail['is_footer_logo'] == 0) checked="checked" @endif/>默认</label><br />
                <label><input type="radio" name="is_footer_logo" id="selfLogeSet" class="logoSet" value="1" @if (isset($detail['is_footer_logo']) && $detail['is_footer_logo'] == 1) checked="checked" @endif/>自定义LOGO</label>
                <div class="logoPreview" style="display: inline-block; width: 90px; height: 30px;"></div>
                <a href="##" id="imgUpLoad"  data-toggle="modal" data-target="#myModal">点击上传</a><br />
                <!-- logo上传 -->
		        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		            <div class="modal-dialog">
		                <div class="modal-content">
		                    <!-- 弹框头部 开始 -->
		                    <div class="modal-header">
		                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		                        <h4 class="modal-title">上传logo</h4>
		                    </div>
		                    <!-- 弹框头部 结束 -->
		                    <!-- 弹框主体 开始 -->
		                    <div class="modal-body">
		                        <!-- 上传组件 开始 -->
		                        <div class="upload_items">
		                            <div class="upload_left">
		                                <!-- 剪切区 开始 -->
		                                <div class="imageBox">
		                                    <div class="thumbBox"></div>
		                                    <div class="spinner" style="display: none">Loading...</div>
		                                </div>
		                                <!-- 剪切区 结束 -->
		                            </div>
		                            <!-- 剪切预览区 开始 -->
		                            <div class="cropped upload_right"></div>
		                            <!-- 剪切预览区 结束 -->
		                        </div>
		                        <!-- 上传组件 结束 -->
		                    </div>
		                    <!-- 弹框主体 结束 -->
		                    <!-- 弹框底部 开始 -->
		                    <div class="upload_bottom modal-footer">
		                        <!-- 图片操作区 开始 -->
		                        <div class="opt_wrap">
		                            <!-- 上传按钮 开始 -->
		                            <div class="upload_opt btn btn-primary">
		                                <a class="upload_img btn btn-primary" href="javascript:void(0)" >
		                                    <label for="upload_file">上传图像</label>
		                                </a>
		                                <input id="upload_file" type="file" name="upload_file"  />
		                            </div>
		                            <input id="btnZoomIn" class="btn btn-primary" type="button" value="+"  >
		                            <input id="btnZoomOut" class="btn btn-primary" type="button" value="-" >
		                            <!-- 上传按钮 结束 -->
		                        </div>
		                        <!-- 图片操作区 结束 -->
		                        <!-- 保存 开始 -->
		                        <div class="opt_wrap">
		                            <input id="btnCrop" class="btn btn-primary" type="button"  value="确定上传" data-dismiss="modal" />
		                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		                        </div>
		                        <!-- 保存 结束 -->
		                    </div>
		                    <!-- 弹框底部 结束 -->
		                </div>
		            </div>
		        </div>
		        <!--logo上传结束-->
                <i id="prompt">建议：图片尺寸240*80，图片格式png&nbsp;&nbsp;&nbsp;&nbsp; <a href="##" id="lookExample">查看示例</a></i>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"></label>
            <div class="col-sm-7">
                <button type="button" id="save" class="btn btn-primary">保存</button>
            </div>
        </div>
    </form>
    <div class="successPromrt hide">保存成功1</div>
</div>
    @endsection
    @section('page_js')
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
        <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!--时间插件引入的JS文件-->
    <script src="{{ config('app.source_url') }}static/js/moment/moment.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/locales.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
    <!--layer.js文件引入-->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>	
    <!--上传图片裁剪插件引入-->
	<script src="{{ config('app.source_url') }}static/js/cropbox.js"></script>
    <!-- 表单验证插件 -->
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/localization/messages_zh.js"></script>
	<!-- 当前页面js -->
    <script type="text/javascript">
	    var imgUrl = "{{ config('app.source_url') }}" + 'mctsource/';
	</script>
    <script src="{{ config('app.source_url') }}mctsource/js/currency_do2yfvy3.js"></script> 
    @endsection