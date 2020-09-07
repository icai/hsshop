@extends('merchants.default._layouts')
@section('head_css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css" />
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css" />
    <!-- 当前页面css -->
    <!-- 选择商品样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_m15z90ku.css" />
    <style type="text/css">
        .laydate_box, .laydate_box * {box-sizing:content-box;}
    </style>
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="crumb_nav clearfix">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="javascript:void(0);">幸运大转盘</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
    </div>
</div>
@endsection
@section('content')
<div class="content">
	<ul class="screen_nav nav nav-tabs mgb15" role="tablist">
        <li role="presentation" class="active">
            <a>活动列表</a>
        </li>
        <!-- <a class="a-rig" href="{{ config('app.url') }}home/index/detail/626/help" target="_blank"><span class="z-cir">?</span>查看如何玩转【幸运大转盘】</a>           -->
    </ul>
    <!--设置转盘-->
    <div class="app-init-container">    
    	<div class="app__content">
    		<!--进度条-->
    		<div class="game-backstage">
    			<nav class="nav-wrap">
    				<ul class="progress-nav progress-nav-4 clearfix step">
    					<li class="progress-nav-item nav-active active current-active">
    						创建活动
    					</li>    
    					<li class="progress-nav-item nav-active">
    						用户参与设置
    					</li>    
    					<li class="progress-nav-item nav-active">
    						中奖设置
    					</li>    
    					<li class="progress-nav-item">
    						完成
    					</li>
    				</ul>
    			</nav>
			</div>
			<!--设置页面-->
			<form class="form-horizontal clearfix" id="addAdminForm">			
				<div class="game-box">
					<!--左侧-->
					<input type="hidden" id="wid" value="{{session('wid')}}" />
					<div class="game-lef">
						<div class="top-nav">
							幸运大转盘
						</div>
						<div class="top-img">
							<img width="300" height="300" src="{{ config('app.source_url') }}mctsource/images/rotatePlan.png"/>
							<img class="top-activ" width="60" height="66" src="{{ config('app.source_url') }}mctsource/images/activity-lottery-2.png"/>
						</div>						
						<!--<img class="top-look" width="100" height="30" src="{{ config('app.source_url') }}mctsource/images/look_result.png"/>-->
						<div class="info-area">
							<!--<div class="view-prize">查看奖品</div>-->
							<ul class="activity-info">
								<li><span class="cloe3">活动有效时间</span>
									<div class="activity-info-content">
										<p>开始时间<span class="statim" data-name="start_time">2017-08-16 00:00:00</span></p>
										<p>结束时间<span class="endtim" data-name="end_time">2017-08-21 00:00:00</span></p>
									</div>
								</li>					
								<li><span class="cloe3">活动说明</span>
									<div class="activity-info-content miaoshu" data-name="notice">本次活动每人每天可以转动1次，你已经转了1次，如果次数没用完，请重新进入本页面可以再转，下一个中间的可能就是你！</div>
								</li>						
							</ul>
						</div>
					</div>
					<!--右侧-->	
					<!--创建活动-->		
					<fieldset class="filed-block block1 steps step_1">
						<div class="control-group" style="margin-bottom: 20px;">
							<label class="control-label required">活动名称：</label>
							<div class="controls">
								<input class="form-control z-title" type="text" name="title" placeholder="填写活动名称" value="幸运大转盘" maxlength="50">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label required">开始时间：</label>
							<div class="controls">
								<label class="input-append">
				                    <input type="text" id="start_time" name="Btime" value="" class="start_time form-control validate control-error w220">
				                </label>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label required">结束时间：</label>
							<div class="controls">
								<label class="input-append">
				                    <input type="text" id="end_time" name="Ctime" value="" class="end_time form-control validate control-error w220">	
				                </label>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label z-acstri">活动说明：</label>
							<div class="controls">
								<textarea class="form-control z-descr z-string" type="text" id="message-text" maxlength="300"></textarea>
							</div>
						</div>
						<!--update 邓钊 2018年7月27日 增加提示信息 -->
						<div class="control-group">
							<label class="control-label"></label>
							<div class="controls" style='width:250px;'>
								请提示用户在抽到赠品后到“我的奖品”中确认收货地址
							</div>
						</div>
						<!-- end -->					
						<h4 class="field-title">设置分享页</h4>
                        <div class="checkin-rule-list-wrap">
                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享标题设置：</label>
                                <div class="controls">
                                    <input type="text" name="share_title" value=""  maxlength="30" class="form-control" style="width:250px;">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享内容设置：</label>
                                <div class="controls">
                                    <textarea name="share_desc" cols="28" maxlength="100"  style="resize:auto;" ></textarea>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" style="width: 98px">分享页图片：</label>
                                <div class="controls">
                                    <input type="hidden" name="share_img" value="">
                                    <div class="share_img_box hide">
			                            <img src="" style="width: 80px;height: 80px;" class="share_img">
			                            <span class="delete">x</span>
			                        </div>
			                        <a href="javascript:;" class="add-goods js-add-picture">+添加图片</a>
                                </div>
                            </div>
							<p class="up_tip">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</p>
                        </div>
					</fieldset>
					<!--参与设置-->
					<fieldset class="filed-block steps step_2 hide">	
						<div class="over">
							<label class="control-label">参与用户：</label>
							<div class="controls condit">
								<label class="radio inline">
				                    <input type="radio" name="time_limit1" value="0" checked="">所有用户
				                </label>
								<label class="radio inline">
				                    <input type="radio" name="time_limit1" value="1">目标用户
				                </label>	
				                <div class="radio-p card-secl">
				                	<select multiple="multiple" data-placeholder="选择会员等级" class="radio-sel card-data"> 
				                		<option value="-1">选择会员等级</option>
									</select>
									
				                </div>	
			                	<p class="intro">默认所有用户都能参与活动</p>
								<p class="intro">目标用户是指拥有指定会员资格的用户</p>		                						
							</div>
							<div class="clear">												
							</div>
						</div>					
						<div class="control-group">
							<label class="control-label">消耗积分：</label>
							<div class="controls">
								<input class="form-control input-medium js-input-number z-reduce" name="lose_integral" type="number" placeholder="为0时不消耗积分" value="" max="100000" min="0">
								<span class="intro">用户每次参与需要消耗积分</span>
							</div>
						</div>					
						<div class="control-group">
							<label class="control-label">参与送积分：</label>
							<div class="controls">
								<input class="form-control input-medium js-input-number z-isall" name="send_integral" type="number" placeholder="填写积分数" value="" max="100000" min="0">
								<label class="checkbox inline z-sendall">
				                    <input name="give_point_to_no_prize_only" value="0" type="checkbox">仅送给未中奖的用户
				                </label>
							</div>
						</div>					
						<div class="control-group">
							<label class="control-label required">参与次数：</label>
							<div class="controls">					
								<label class="z-rule box_flex">
				                    <input type="radio" class="z-flolef" name="time_limit" value="2" checked="">&nbsp;&nbsp;
			                    	<span class="z-flolef">一人&nbsp;</span><input type="number" style="width: 75px;" value="1" class="form-control lim-times z-flolef" type="text" />&nbsp;次	
				                </label>
								<label class="z-rule box_flex">
				                    <input class="z-flolef" class="z-flolef" type="radio" name="time_limit" value="1">&nbsp;&nbsp;
			                    	<span class="z-flolef">一天&nbsp;</span><input type="number" style="width: 75px;" value="1" class="form-control lim-times z-flolef" type="text" />&nbsp;次
				                </label>				
							</div>
						</div>	
						<div class="find-img">
							<div class="shop_image">
								<label class="control-label choose_price" style="width: auto; margin-left: 3px;">增加抽奖资格：</label>
								<input type="hidden" name="pids" class="find-con" value="">
								<div class="controls z-wid55">
									<ul class="module-goods-list flonone clearfix ui-sortable" name="goods">
									    <!--<li class="sort-find hide">-->
										    <!--<a href="#" target="_blank">
										        <img alt="商品图" width="50" height="50" src="">
										    </a>
										    <a class="close-modal js-delete-goods-find small ng-hide" data-id="" title="删除">×</a>-->
										<!--</li>-->
										<div class="disflx">											
										    <li class="z-inlin" style="border: none">
										        <a href="javascript:void(0);" class="js-find-goods add-goods">
										            <i class="icon-add" style="margin-top: 18px">+</i>
										        </a>
										    </li>
										    <p>可添加商品，用户购买该商品可增加抽奖次数</p>
										</div>
									</ul>
								</div>	
							</div>	
						</div>					
					</fieldset>
					<!--中奖设置-->
					<fieldset class="filed-block steps step_3 hide">
						<div class="separate-line-wrap first-line">
							<hr>
							<div class="separate-line">
								<p class="text-center">中奖概率</p>
							</div>
						</div>
						<div class="control-group" style="padding-left: 72px">
							<label class="control-label">中奖率：</label>
							<div class="controls lin30">
								<input type="number" class="form-control input-smallest js-input-number z-rate" name="probability" placeholder="0-100" value="" max="100" min="0">
								<span class="z-leve">%</span>
								<!--<a class="help-icon" target="_blank">?</a>-->
							</div>
						</div>
						<div class="separate-line-wrap">
							<hr>
							<div class="separate-line">
								<p class="text-center">设置奖品</p>
							</div>
						</div>					
						<p style="color: #666; font-size: 12px; padding-left: 55px;">等级设置的奖品数量越多，则该等级中奖率越高。<br>例如：总中奖率50%，一等奖1个，二等奖2个，三等奖7个，<br />一等奖中奖概率为50%*1/(1+2+7)=5%</p>
						<div class="prizes-wrap js-isolate">
							<div class="game-prize">
								<ul class="prize-list clearfix">					
									<li class="prize-tab-item selected" data-index="0">一等奖</li>					
									<li class="prize-tab-item" data-index="1">二等奖</li>					
									<li class="prize-tab-item" data-index="2">三等奖</li>					
								</ul>
								<div class="prize-content prize-content-set1 addimg">
									<div class="control-group">
										<label class="control-label choose_price">选择奖品：</label>
										<div class="controls prize-spoil fir-lab">
											<label class="radio inline type1">
									            <input type="radio" class="" name="type1" value="1" checked>赠送积分
									        </label>
											<label class="radio inline type1">
									            <input type="radio" class="" name="type1" value="2">送优惠
									        </label>
											<label class="radio inline type1">
									            <input type="radio" class="" name="type1" value="3">赠品
											</label>
											<!-- update 梅杰 2018年7月27日 隐藏 -->
											<!-- update 梅杰 2018年7月30日 老数据兼容 -->
											@if(isset($data['prize'][0]['type']) && $data['prize'][0]['type'] == 4)
											<label class="radio inline type1" style="">
									            <input type="radio" class="" name="type1" value="4">产品
									        </label>
											@else
											<label class="radio inline type1" style="visibility:hidden">
									            <input type="radio" class="" name="type1" value="4">产品
									        </label>
											@endif
										</div>
									</div>	
									<div class="prize-group points-st1">
										<div class="control-group">
											<label class="control-label choose_price choose_price">赠送积分：</label>
											<div class="controls">
												<input type="number" class="form-control input-medium js-input-number jifen fir-con" placeholder="请填写积分数" name="prize_send_integral_1" max="100000" min="1" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number jpin input-num jinfen-num fir-num" placeholder="" name="prize_number_1" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>										
									</div>			
									<div class="prize-group points-st2">	
										<div class="over">								
											<label class="control-label choose_price">送优惠：</label>
											<div class="controls">
												<select class="z-sel fir-con cou-sel"> 
													<option value="-1">选择优惠券</option>
												</select>
												<a target="_blank" href="/merchants/marketing/coupon/set">新建</a>
											</div>
											<div class="clear">												
											</div>
										</div>
										<div class="control-group points-st1">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin fir-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>	
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
									</div>
									<div class="prize-group points-st3">
										<div class="control-group">
											<label class="control-label choose_price">赠品：</label>
											<div class="controls">
												<input class="form-control input-medium js-input-number fir-con" placeholder="请输入赠品名称" max="100000" min="0" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin fir-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
										<div class="control-group">
											<label class="control-label choose_price">兑奖方式：</label>
											<div class="controls">
												<textarea class="form-control z-descr shuoming1" type="text" maxlength="300"></textarea>
											</div>
										</div>
										<hr>
										<div class="control-group">
											<div class="control-label">
												<div class="image-wrap">
													<div class="image-display z-imga"></div>
													<input class="fir-img" type="hidden" name="image_url">
												</div>
											</div>
											<div class="controls sink">
												<button class="btn btn-default js-upload-image" data-target="image_url" type="button">上传奖品图片</button>&nbsp;&nbsp;
												<button class="btn btn-default js-clear-prize-image" type="button">清空</button>
												<div class="help-block">仅支持 jpg、png、 尺寸480*480 不超过1M</div>
											</div>
										</div>					
									</div>
									<div class="prize-group points-st4">
										<div class="control-group shop_image_a">
											<label class="control-label choose_price">选择商品：</label>
											<input type="hidden" name="prize_send_integral_4" class="fir-con" value="">
											<div class="controls">
												<ul class="module-goods-list clearfix ui-sortable" name="goods">
												    <li class="sort hide">
													    <a href="#" target="_blank">
													        <img alt="商品图" width="50" height="45" src="">
													    </a>
													    <a class="close-modal js-delete-goods small ng-hide" data-id="" title="删除">×</a>
													</li>
												    <li>
												        <a href="javascript:void(0);" class="js-add-goods add-goods">
												            <i class="icon-add"></i>
												        </a>
												    </li>
												</ul>
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin fir-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>			
									</div>	
								</div>
								<div class="prize-content prize-content-set2 hide">
									<div class="control-group">
										<label class="control-label choose_price">选择奖品：</label>
										<div class="controls prize-spoil sec-lab">
											<label class="radio inline type2">
									            <input type="radio" class="" name="type2" value="1" checked>赠送积分
									        </label>
											<label class="radio inline type2">
									            <input type="radio" class="" name="type2" value="2">送优惠
									        </label>
											<label class="radio inline type2">
									            <input type="radio" class="" name="type2" value="3">赠品
											</label>
											<!-- update 梅杰 2018年7月27日 隐藏 -->
											<!-- update 梅杰 2018年7月30日 老数据兼容 -->
											@if(isset($data['prize'][1]['type']) && $data['prize'][1]['type'] == 4)
											<label class="radio inline type2" style="">
									            <input type="radio" class="" name="type2" value="4">产品
									        </label>
											@else
											<label class="radio inline type2" style="visibility:hidden">
									            <input type="radio" class="" name="type2" value="4">产品
									        </label>
											@endif
										</div>
									</div>					
									<div class="prize-group points-st1">
										<div class="control-group">
											<label class="control-label choose_price choose_price">赠送积分：</label>
											<div class="controls">
												<input type="number" class="form-control input-medium js-input-number jifen sec-con" placeholder="请填写积分数" name="prize_send_integral_2" max="100000" min="1" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin sec-num" placeholder="" name="point_num" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
									</div>			
									<div class="prize-group points-st2">	
										<div class="over">								
											<label class="control-label choose_price">送优惠：</label>
											<div class="controls">
												<select class="sec-con z-sel cou-sel"> 
													<option value="-1">选择优惠券</option>
												</select>
												<a href="/merchants/marketing/coupon/set">新建</a>
											</div>
											<div class="clear">												
											</div>
										</div>
										<div class="control-group points-st1">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin sec-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
									</div>
									<div class="prize-group points-st3">
										<div class="control-group">
											<label class="control-label choose_price">赠品：</label>
											<div class="controls">
												<input class="form-control input-medium js-input-number sec-con" placeholder="请输入赠品名称" max="100000" min="0" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin sec-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
										<div class="control-group">
											<label class="control-label choose_price">兑奖方式：</label>
											<div class="controls">
												<textarea class="form-control z-descr shuoming2" type="text" maxlength="300"></textarea>
											</div>
										</div>
										<hr>
										<div class="control-group">
											<div class="control-label">
												<div class="image-wrap">
													<div class="image-display z-imgb"></div>
													<input type="hidden" class="sec-img" name="image_url">
												</div>
											</div>
											<div class="controls sink">
												<button class="btn btn-default js-upload-image" data-target="image_url" type="button">上传奖品图片</button>&nbsp;&nbsp;
												<button class="btn btn-default js-clear-prize-image" type="button">清空</button>
												<div class="help-block">仅支持 jpg、png、 尺寸480*480 不超过1M</div>
											</div>
										</div>					
									</div>
									<div class="prize-group points-st4">
										<div class="control-group shop_image_a">
											<label class="control-label choose_price">选择商品：</label>
											<input type="hidden" name="prize_send_integral_4" class="sec-con" value="">
											<div class="controls">
												<ul class="module-goods-list clearfix ui-sortable" name="goods">
												    <li class="sort hide">
													    <a href="#" target="_blank">
													        <img alt="商品图" width="50" height="45" src="">
													    </a>
													    <a class="close-modal js-delete-goods small ng-hide" data-id="" title="删除">×</a>
													</li>
												    <li>
												        <a href="javascript:void(0);" class="js-add-goods add-goods">
												            <i class="icon-add"></i>
												        </a>
												    </li>
												</ul>
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin sec-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>			
									</div>	
								</div>
								<div class="prize-content prize-content-set3 hide">
									<div class="control-group">
										<label class="control-label choose_price">选择奖品：</label>
										<div class="controls prize-spoil tri-lab">
											<label class="radio inline type3">
									            <input type="radio" class="" name="type3" value="1" checked>赠送积分
									        </label>
											<label class="radio inline type3">
									            <input type="radio" class="" name="type3" value="2">送优惠
									        </label>
											<label class="radio inline type3">
												<input type="radio" class="" name="type3" value="3">赠品
											</label>
											<!-- update 梅杰 2018年7月30日 老数据兼容 -->
											@if(isset($data['prize'][2]['type']) && $data['prize'][1]['type'] == 4)
											<label class="radio inline type3" style="">
									            <input type="radio" class="" name="type3" value="4">产品
									        </label>
											@else
											<label class="radio inline type3" style="visibility:hidden">
									            <input type="radio" class="" name="type3" value="4">产品
									        </label>
											@endif
										</div>
									</div>					
									<div class="prize-group points-st1">
										<div class="control-group">
											<label class="control-label choose_price choose_price">赠送积分：</label>
											<div class="controls">
												<input type="number" class="form-control input-medium js-input-number jifen tri-con" placeholder="请填写积分数" name="prize_send_integral_3" max="100000" min="1" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin tri-num" placeholder="" name="point_num" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
									</div>			
									<div class="prize-group points-st2">	
										<div class="over">								
											<label class="control-label choose_price">送优惠：</label>
											<div class="controls">
												<select data-placeholder="选择优惠券" class="tri-con z-sel cou-sel"> 
													<option value="-1">选择优惠券</option>
												</select>
												<a href="/merchants/marketing/coupon/set">新建</a>
											</div>
											<div class="clear">												
											</div>
										</div>
										<div class="control-group points-st1">
											<label class="control-label choose_price" >奖品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin tri-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
									</div>
									<div class="prize-group points-st3">
										<div class="control-group">
											<label class="control-label choose_price">赠品：</label>
											<div class="controls">
												<input class="form-control input-medium js-input-number tri-con" placeholder="请输入赠品名称" max="100000" min="0" value="">
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin tri-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>
										<div class="control-group">
											<label class="control-label choose_price">兑奖方式：</label>
											<div class="controls">
												<textarea class="form-control z-descr shuoming3" type="text" maxlength="300"></textarea>
											</div>
										</div>
										<hr>
										<div class="control-group">
											<div class="control-label">
												<div class="image-wrap">
													<div class="image-display z-imgc"></div>
													<input type="hidden" class="tri-img" name="image_url">
												</div>
											</div>
											<div class="controls sink">
												<button class="btn btn-default js-upload-image" data-target="image_url" type="button">上传奖品图片</button>&nbsp;&nbsp;
												<button class="btn btn-default js-clear-prize-image" type="button">清空</button>
												<div class="help-block">仅支持 jpg、png、 尺寸480*480 不超过1M</div>
											</div>
										</div>					
									</div>
									<div class="prize-group points-st4">
										<div class="control-group shop_image_a">
											<label class="control-label choose_price">选择商品：</label>
											<input type="hidden" name="prize_send_integral_4" class="tri-con" value="">
											<div class="controls">
												<ul class="module-goods-list clearfix ui-sortable" name="goods">
												    <li class="sort hide">
													    <a href="#" target="_blank">
													        <img alt="商品图" width="50" height="45" src="">
													    </a>
													    <a class="close-modal js-delete-goods small ng-hide" data-id="" title="删除">×</a>
													</li>
												    <li>
												        <a href="javascript:void(0);" class="js-add-goods add-goods">
												            <i class="icon-add"></i>
												        </a>
												    </li>
												</ul>
											</div>										
										</div>
										<div class="control-group">
											<label class="control-label choose_price">赠品数量：</label>
											<div class="controls lin30">
												<input type="number" class="form-control input-medium js-input-number input-num jpin tri-num" placeholder="" value="0" data-default="0" min="0">
												<span>个</span>
												<div class="help-block">奖品数量为0时不设此奖项</div>
											</div>	
										</div>			
									</div>
								</div>
							</div>
						</div>					
					</fieldset>
					<!--完成-->
					<div class="filed-block notice steps step_4 hide">
						<h4 class="success-title">你已成功创建该活动！</h4>
						<hr class="hr-title">
						<div class="control-group">
							<label class="control-label">链接地址：</label>
							<div class="form-group controls">
								<div class="input-append cop-div">
									<input type="text" class="form-control float_left cop-int" value="">
									<button type="button" class="btn js-btn-copy float_left cop-btn">复制</button>
								</div>
								<div class="help-block">
									复制该链接给你的粉丝
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">二维码：</label>
							<div class="controls code-aj">
							</div>
						</div>					
						<div class="control-group">
							<label class="control-label"></label>
							<div class="controls">
								<!--<a href="" style="font-size: 14px; color: #38f;" download="qrcode.png" target="_blank">下载</a>-->
								<!--<a class="scan-url" href="/v2/weixin/autoreply/scan">设置带参数二维码</a>-->
							</div>
						</div>
					</div>
					<!--下一步-->
					<div class="app-actions">
						<div class="form-actions text-center">
							<a href="##" class="btn btn-default prev hide coloff">上一步</a>
							<a href="##" class="btn btn-primary next">下一步</a>
							<a href="##" class="btn btn-default reset hide coloff">修改</a>
							<a type="submit" href="/merchants/marketing/wheelList" class="btn btn-primary sure hide">确认</a>
						</div>
					</div>
				</div>
			</form>			
		</div>
    </div>       
</div>	
<form id= "upload" enctype="multipart/form-data" method="post" style="opacity: 0">
    <input type="file" name="file" style="width: 1px;height: 1px;" />
</form>
<script type="text/javascript">
	var _host = "{{ imgUrl() }}";
	var wheel_url = "{{ config('app.url') }}";
	var wid = {{session('wid')}};
    var data = {!! json_encode($data) !!};
    var cardData = {!! json_encode($cardData) !!}
    var couponList = {!! json_encode($conponList) !!}
    console.log(data);   
//  console.log(couponList);
    var id ='';
    var id_1 = '';
    var id_2 = '';
    var id_3 = ''; 
//  console.log(cardData);
</script>
@endsection
@section('page_js')
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/moment.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/locales.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/jqPaginator.min.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
	<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_m15z90ku.js"></script>
@endsection