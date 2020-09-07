@extends('merchants.default._layouts')
@section('head_css')
    <!-- 选择商品样式 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/share_create.css" />

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
    	<h2 class="groupon-title">设置享立减活动</h2>
        <div class="game-box">
			<!--左侧-->
			<input type="hidden" id="wid" value="{{session('wid')}}" />
			<div class="game-lef">
				<div class="top-nav">
					享立减
				</div>
				<div class="top-img">
					<div class="top_tle_img">商品主图</div>
				</div>						
				<div class="info-area">
					<p class="show_active_title">活动名称</p>
					<p class="show_active_ftitle">活动副标题</p>
					<div class="product_price_info">
						<span class="price">￥<span class="num">xx</span></span>
						<span class="oprice">原价:￥<span class="num">xx</span></span>
					</div>
				</div>
			</div>
			<!--右侧-->	
			<!--创建活动-->		
			<fieldset class="filed-block block1 steps step_1">
				<div class="control-group clear">
                    <label class="control-label required">选择商品：</label>
                    <span class="controls"> 
                        <a class="sel-goods"  href="javascript:;">
                            <i class="icon-add">+</i>
                        </a>  
                        <input type="hidden" class="validate" id="goods_id" />
						<input type="hidden" class="product_name" id="goods_name" />
                    </span>
                </div>
                <div class="control-group clear">
				<div>
					<label class="control-label required">活动图：</label>
					<div class="controls">
						<label class="input-append">
		                    <div class="image-wrap image-warp-active margh10">
								<div class="js-upload-image add_active_img" data-imgadd="1">+添加图片</div>
								<input class="fir-img" type="hidden" name="act_img">
							</div>
		                    
							<div class='xlj_example'>
								<div>示例</div>
								<div class='xlj_example_box'>
									<div class='xlj_example_box_img'>
										<img src="" alt="" class='hide'>
									</div>
								</div>
							</div>
		                </label>
					</div>
					</div>
					<div class="up_tip1">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</div>
				</div>
				<div class="control-group clear" style="margin-bottom: 15px;">
					<label class="control-label required">按钮名称：</label>
					<div class="controls">
						<input class="form-control" type="text" name="btn_title" placeholder="填写按钮名称" value="分享0元购" maxlength="6">
					</div>
				</div>
				<div class="control-group">
					<div>
					<label class="control-label required">商品图：</label>
					<div class="controls" style="margin-bottom: 10px;">
						<div class="picture-list ui-sortable">
							<ul class="js-picture-list app-image-list clearfix gridly" style="max-width: 300px;">

								<!-- <li class="sort">
									<img src="" class="js-img-preview">
									<div class="js-delete-picture close-modal small ng-hide">×</div>
									<input type="hidden" name="show_imgs[]" value="">
								</li> -->
								<li class="add">
									<div class="add-goods js-add-picture">+添加图片</div>
								</li>
							</ul>
						</div>
					</div>
					</div>
					<div class="up_tip1">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</div>
				</div>
				
				<div class="control-group clear" style="margin-bottom: 15px;">
					<label class="control-label required">活动名称：</label>
					<div class="controls">
						<input class="form-control z-title" type="text" name="title" placeholder="填写活动名称" value="享立减活动" maxlength="23">
						<p class="info">最多添加23个字</p>
					</div>
				</div>
				<div class="control-group clear" style="margin-bottom: 15px;">
					<label class="control-label required">活动副标题：</label>
					<div class="controls">
						<input class="form-control" type="text" name="subtitle" maxlength="10">
						<span class="info">最多添加10个字</span>
					</div>
				</div>
				<div class="control-group">
                    <label class="control-label required">生效时间：</label>
                    <span class="marlef5">
                        <input type="text" id="start_time" value="" class="form-control validate control-error date_inpt"/>
                        <span>-</span>
                        <input type="text" id="end_time" value="" class="form-control validate control-error date_inpt"/>
                    </span>
                </div>
				<div class="control-group clear">
					<label class="control-label required lable_max">新用户点击分享链接每次助减：</label>
					<div class="controls">
						<div class="input-append">
		                    <input type="number" id="start_time" name="Btime" value="1" class="form-control control-error z-wid70 zhujian_price control_block">
	                    	<span class="">元</span>
		                </div>
		                <span class="info">助减金额<（单价-保底价）</span>	                    
					</div>
				</div>
				<div class="control-group clear">
					<label class="control-label required">保底价：</label>
					<div class="controls">
	                    <input type="number" id="end_time" name="Ctime" value="0" class="form-control control-error z-wid70 baodi_price control_block">
                    	<span class="">元</span>
					</div>
				</div>
				<div class="control-group clear">
					<label class="control-label required lable_max" style="margin-left: -4px">助减人数初始值：</label>
					<div class="controls sign_con">
						<label class="input-append flex_lable" style="margin-right: 30px;">
		                    <input type="radio" name="is_initial" value="0" class="" checked>
	                    	<span>关闭</span>
		                </label>
		                <label class="input-append flex_lable">
		                    <input type="radio" name="is_initial" value="1" class="">
	                    	<span>开启</span>
		                </label>
					</div>
				</div>
				<div class="control-group clear initial_value none">
					<label class="control-label required">初始值为：</label>
					<div class="controls">
						<label class="input-append">
							<input type="number" name="initial_value" value="0" class="form-control control-error z-wid70 control_block">
							<span>人</span>
						</label>
					</div>
				</div>
				{{--<div class="control-group clear">--}}
					{{--<label class="control-label required">分享卡片图：</label>--}}
					{{--<div class="controls">--}}
						{{--<label class="input-append">--}}
		                    {{--<div class="image-wrap card_img margh10">--}}
								{{--<div class="js-upload-image" data-imgadd="3" style="color: #5498FF;">+加图</div>--}}
								{{--<input class="fir-img" type="hidden" name="card_img" value="">--}}
							{{--</div>--}}
		                    {{--<span class="up_tip">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</span>--}}
		                {{--</label>--}}
					{{--</div>--}}
				{{--</div>--}}
				<div class="control-group clear">
					<label class="control-label required">分享标题设置：</label>
					<div class="controls">
						<label class="input-append">
		                    <input class="form-control" type="text" name="share_title" placeholder="" value="" maxlength="23">
		                    <span class="up_tip">不超过23个汉字</span>
		                </label>
					</div>
				</div>
				<div class="control-group clear">
					<div>
					<label class="control-label required">分享图片：</label>
					<div class="controls">
						<label class="input-append">
		                    <div class="image-wrap image-wrap-share margh10" style='float: none;width: 100px'>
								<div class="image-display z-imga js-upload-image" data-imgadd="2">+添加图片</div>
								<input class="fir-img" type="hidden" name="share_img">
							</div>
							<div class='example'>
								<div class='example_title'>示例</div>
								<div class='example_box'>
									<div class='example_box_img'>
										<img src="" alt="" class='hide'>
									</div>
								</div>
							</div>
		                </label>
					</div>
					</div>
					<div class="up_tip1">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</div>
				</div>
                <div class="control-group clear">
					<div>
                    <label class="control-label required">规则图片：</label>
                    <div class="controls">
                        <label class="input-append">
                            <div class="image-wrap image-wrap-rule margh10 clearfix" style='float: none'>
                                <div class="image-display z-imga" style='float: left;border: none'>
									<img class="img-goods" src="{{ imgUrl() }}hsshop/image/static/xianglijian_rule.jpg" />
                                </div>
								<div class='js-upload-image' data-imgadd="4" style='cursor: pointer;margin-left: 110px;color: #3197FA;font-size: 12px;margin-top: 5px'>修改图片</div>
                                <input class="fir-img" type="hidden" name="rule_img" value="/hsshop/image/static/xianglijian_rule.jpg">
                            </div>
                        </label>
					</div>
					</div>
					<div class="up_tip1">建议尺寸750*125px，宽度不大于400px（限制比例6:1）</div>
                </div>
                <div class="control-group clear">
                    <label class="control-label required">弹窗规则标题：</label>
                    <div class="controls">
                        <label class="input-append">
                            <input class="form-control" type="text" name="rule_title" placeholder="" value="享立减规则" maxlength="6" >
                            <span class="up_tip">不超过6个汉字</span>
                        </label>
                    </div>
                </div>
                <div class="control-group clear">
                    <label class="control-label required">规则详细内容：</label>
                    <div class="controls">
                        <label class="input-append">
                            <textarea class="form-control" name="rule_text" placeholder="" value=""></textarea>
                            <span class="up_tip" style='display: block;text-align: right;margin-top: 5px'>不超过500个汉字</span>
                        </label>
						<span href="javascript:void(0);" class="rule_info">规则参考</span>
                    </div>

                </div>
			</fieldset>
		</div>
		<div class="t-footer">
	        <input class="btn js-btn-quit btn-sm" type="button" onclick="history.back();" value="取 消">
	        <input class="btn btn-primary btn-sm ml10 js-btn-save" type="button" value="保 存">
	    </div>
	</div>
    <div id="rule_model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">规则参考</h4>
                </div>
                <div class="modal-body">参与享立减活动的商品，所有用户均可邀请新用户来助减金额<br />
				<br />
1.分享者选择享立减活动商品分享给好友，好友如果是新用户，点击进入小程序，即可帮助分享者减价；<br />
2.每个用户只能助减1次，1次只能助减一款活动商品，即使以后商家多次开启享立减活动，助减过的用户也不能再次帮他人助减<br />
3.活动期间，每单限购一件，如想要再次购买该产品，可再次邀请新用户助减；<br />
4.分享您想购买的活动商品到微信群或微信好友，减至理想价即可下单；<br />
5.每个商品均有保底价及每个新用户可助减的金额，部分享立减商品最低可减至0元；<br />
6.只有商家使用【翻新】功能时，已助减过的用户可以再次帮助他人助减；<br />
*活动最终解释权归商家所有<br />
<br />

                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" type="button" class="btn btn-primary copy">使用</button>
                </div>
            </div>
        </div>
        <!-- <div class="modal-backdrop fade"></div> -->
    </div>
@endsection

@section('page_js')
<!--选择商品-->
<script type="text/javascript">
	var _host = "{{ imgUrl() }}";
	var imgUrl = "{{ imgUrl() }}";
    var shareEvent = {!! json_encode($data) !!};
    var showImg = {!! json_encode($showImgs) !!}
    console.log(shareEvent)
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<!--当前页面js-->
<script src="{{ config('app.source_url') }}mctsource/js/share_create.js"></script> 
@endsection