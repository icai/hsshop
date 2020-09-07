@extends('merchants.default._layouts')
@section('head_css')
	<!--时间插件css引入-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <!--bootstrape验证插件css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_newGroup.css"/>
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
                    <a href="javascript:void(0)">团购返现</a>
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
    	<!--顶部导航内容-->
    	<div class="content_top">
    		<ul>
    			<li>所有团购</li>
    			<li>未开始</li>
    			<li>进行中</li>
    			<li>已结束</li>
    		</ul>
    		<a href="##" class="tutorial"><span id="icon">?</span> 查看【团购管理】使用教程</a>
    	</div>
    	<!--中部主要内容-->
    	<div class="content_center">
    		<span class="remind">提醒：团购返现目前只支持【微信支付—代销】模式，若开启【微信支付-自有】模式则导致返现失效！<a href="##">查看详情</a></span>
    		<span class="remind setGroupDetail">设置团购详情</span>
    		<!--中部左侧内容-->
    		<div class="content_change_left">
    			<div class="title_img">
    				<img src="{{ config('app.source_url') }}mctsource/images/phoneImg.png"/>
    			</div>
    			<div class="goods_img">团购商品主图</div>
    			<div class="goods_price">
    				<lft>￥<span>0.00</span></lft>
    				<rgt>剩余时间：<br/>
    					<span id="day">D</span> :	
    					<span id="hour">H</span> :	
    					<span id="minute">M</span> :	
    					<span id="second">S</span>	
    				</rgt>
    				<div id="floatDiv">团购</div>
    			</div>
    			<p class="goods_name">团购商品标题</p>
    			<span id="cashback">
    				<lft>团购返现</lft>
    				<a href="javascript:void(0);">点击查看></a>
    			</span>
    			<span id="sell_number">
    				已售<p id="selled">0</p>件，售出达<p id="sell_num">10</p>件即得<p id="sell_money">10</p>元。
    			</span>
    			<div class="detail_msg">
    				<h5>详细信息区</h5>
    				SKU信息、运费、其他自定义组件内容。
    			</div>
    		</div>
    		<!--中部右侧主要内容-->
    		<div class="content_change_right">
    			<form id="defaultForm" class="form-horizontal top" role="form">
					<div class="form-group">
					    <label for="adressName" class="col-sm-4 control-label"><i class="necessary">*</i>选择商品：</label>
					    <div class="col-sm-7">
					    	<div class="add_goods">
					    		<span>+</span>
					    	</div>
						    <input type="hidden" name="goodChoose" id="goodChoose" value="" />
					    </div>
					</div>
					<div class="bottom">
						<div class="form-group">
						    <label for="adressName" class="col-sm-4 control-label">商品名称：</label>
						    <div class="col-sm-7">
						    	<a href="##" class="add_title">团购商品标题</a>
						    </div>
						</div>
						<div class="form-group">
						    <label for="" class="col-sm-4 control-label">原价：</label>
						    <div class="col-sm-7">
						    	<tile class="original">￥ <span id="original_price">0.00</span></tile>
						    </div>
						</div>
						<div class="form-group">
						    <label for="group_price" class="col-sm-4 control-label"><i class="necessary">*</i>团购价：</label>
						    <div class="col-sm-4">
						    	<div class="input-group">
								    <div class="input-group-addon">￥</div>
								    <input class="form-control group_price" name="group_price" type="text" placeholder="0.00">
							    </div>
						    </div>
						</div>
						<div class="form-group">
						    <label for="" class="col-sm-4 control-label">剩余库存：</label>
						    <div class="col-sm-7">
						    	<leave>0</leave>
						    </div>
						</div>
						<div class="form-group">
						    <label for="begTimg" class="col-sm-4 control-label"><i class="necessary">*</i>开始时间：</label>
						    <div class="col-sm-7">
						    	<div class='input-group date' id='datetimepicker'>
				                    <input type='text' class="form-control" id="begTimg" name="begTimg" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				             	</div>
						    </div>
						</div>
						<div class="form-group">
						    <label for="closeTime" class="col-sm-4 control-label"><i class="necessary">*</i>结束时间：</label>
						    <div class="col-sm-7">
						    	<div class='input-group date' id='datetimepicker1'>
				                    <input type='text' class="form-control" id="closeTime" name="closeTime" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				             	</div>
						    </div>
						</div>
						<div class="form-group">
						    <label for="" class="col-sm-4 control-label">参团返现：</label>
						    <div class="col-sm-7">
						    	<label><input type="radio" name="cash_back" id="cashBack" value="1" checked="checked"/>返现</label>
						    	<label><input type="radio" name="Cash_back" id="no_cashBack" value="0" />不返现</label>
						    </div>
						</div>
						<div class="form-group cashBack_condition">
						    <label for="" class="col-sm-4 control-label">返现条件：</label>
						    <div class="col-sm-8">
						    	<div class="Reward_conditions_show"></div>
						    	<a href="##" class="add_reward_conditions"><span id="addIcon">+</span>新增奖励条件</a>
						    </div>
						</div>
					</div>
	        		<div class="content_bottom">
						<input type="submit" class="btn btn-primary" value="保存"></input>
	        			<button type="button" class="btn btn-default">取消</button>
	        		</div>
				</form>
				<div id="row"></div>
    		</div>
    	</div>
    </div>

@endsection

@section('page_js')
    <!--bootstrap表单验证插件js-->
    <script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
    <!--时间插件引入的JS文件-->
    <script src="{{ config('app.source_url') }}static/js/moment/moment.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/locales.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
    <!--layer.js文件引入-->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>	
    <!--主要内容js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing-newGroup.js" type="text/javascript" charset="utf-8"></script>
@endsection