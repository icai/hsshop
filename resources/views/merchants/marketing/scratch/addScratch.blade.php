@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css" />
    <!-- 当前页面css -->
    <!-- 选择商品样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_m15z90yu.css" />
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
	                <a href="javascript:void(0);">刮刮卡</a>
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
                                刮刮卡活动
                            </div>
                            <div class="top-img">
                            </div>
                        </div>
                        <!--右侧-->
                        <!--创建活动-->
                        <fieldset class="filed-block block1 steps step_1">
                            <div class="control-group" style="margin-bottom: 20px;">
                                <label class="control-label required">活动名称：</label>
                                <div class="controls">
                                    <input class="form-control z-title" type="text" name="title" placeholder="填写活动名称" value="刮刮卡活动" maxlength="50">
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
                                <div class="controls controls_box">
                                    <label class="z-rule box_flex">
                                        <input type="radio" class="z-flolef" name="time_limit" value="1" checked="">&nbsp;&nbsp;
                                        <span class="z-flolef z-flolef-span">一人一次</span>
                                    </label>
                                    <label class="z-rule box_flex">
                                        <input class="z-flolef" class="z-flolef" type="radio" name="time_limit" value="2">&nbsp;&nbsp;
                                        <span class="z-flolef z-flolef-span">一天一次</span>
                                    </label>
                                    <label class="z-rule box_flex">
                                        <input class="z-flolef" class="z-flolef" type="radio" name="time_limit" value="3">&nbsp;&nbsp;
                                        <span class="z-flolef z-flolef-span">一天两次</span>
                                    </label>
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
                                </div>
                            </div>
                            <div class="separate-line-wrap">
                                <hr>
                                <div class="separate-line">
                                    <p class="text-center">设置奖品</p>
                                </div>
                            </div>
                            <p style="color: #666; font-size: 12px; padding-left: 55px;">等级设置的奖品数量越多，则该等级中奖率越高。<br>例如：设置一等奖 10个，二等奖20个，则中二等奖概率高于<br />一等奖</p>
                            <div class="prizes-wrap js-isolate">
                                <div class="game-prize">
                                    <ul class="prize-list clearfix">
                                        <li class="prize-tab-item selected" data-index="0">一等奖</li>
                                        <li class="prize-tab-item" data-index="1">二等奖</li>
                                        <li class="prize-tab-item" data-index="2">三等奖</li>
                                        <li class="prize-tab-item" data-index="3">普通奖</li>
                                    </ul>
                                    <div class="prize-content prize-content-set1 addimg">
                                        <div class="control-group">
                                            <label class="control-label choose_price">选择奖品：</label>
                                            <div class="controls prize-spoil fir-lab">
                                                <label class="radio inline type1">
                                                    <input type="radio" class="" name="type1" value="1" checked="">赠送积分
                                                </label>
                                                <label class="radio inline type1">
                                                    <input type="radio" class="" name="type1" value="2">送优惠
                                                </label>
                                                <label class="radio inline type1">
                                                    <input type="radio" class="" name="type1" value="3">赠品
                                                </label>
                                                <label class="radio inline type1" style='display: none'>
                                                    <input type="radio" class="" name="type1" value="4">产品
                                                </label>
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
                                                    <a href="/merchants/marketing/coupon/set">新建</a>
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
                                                    <div class="help-block">仅支持 jpg、png、 <br />尺寸480*480 不超过1M</div>
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
                                                    <input type="radio" class="" name="type2" value="1" checked="">赠送积分
                                                </label>
                                                <label class="radio inline type2">
                                                    <input type="radio" class="" name="type2" value="2">送优惠
                                                </label>
                                                <label class="radio inline type2">
                                                    <input type="radio" class="" name="type2" value="3">赠品
                                                </label>
                                                <label class="radio inline type2" style='display: none'>
                                                    <input type="radio" class="" name="type2" value="4">产品
                                                </label>
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
                                                    <div class="help-block">仅支持 jpg、png、 <br />尺寸480*480 不超过1M</div>
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
                                                    <input type="radio" class="" name="type3" value="1" checked="">赠送积分
                                                </label>
                                                <label class="radio inline type3">
                                                    <input type="radio" class="" name="type3" value="2">送优惠
                                                </label>
                                                <label class="radio inline type3">
                                                    <input type="radio" class="" name="type3" value="3">赠品
                                                </label>
                                                <label class="radio inline type3" style='display: none'>
                                                    <input type="radio" class="" name="type3" value="4">产品
                                                </label>
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
                                                    <div class="help-block">仅支持 jpg、png、 <br />尺寸480*480 不超过1M</div>
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
                                    <div class="prize-content prize-content-set4 hide">
                                        <div class="control-group">
                                            <label class="control-label choose_price">选择奖品：</label>
                                            <div class="controls prize-spoil ctx-lab">
                                                <label class="radio inline type4">
                                                    <input type="radio" class="" name="type4" value="1" checked="">赠送积分
                                                </label>
                                                <label class="radio inline type4">
                                                    <input type="radio" class="" name="type4" value="2">送优惠
                                                </label>
                                                <label class="radio inline type4">
                                                    <input type="radio" class="" name="type4" value="3">赠品
                                                </label>
                                                <label class="radio inline type4" style='display: none'>
                                                    <input type="radio" class="" name="type4" value="4">产品
                                                </label>
                                            </div>
                                        </div>
                                        <div class="prize-group points-st1">
                                            <div class="control-group">
                                                <label class="control-label choose_price choose_price">赠送积分：</label>
                                                <div class="controls">
                                                    <input type="number" class="form-control input-medium js-input-number jifen ctx-con" placeholder="请填写积分数" name="prize_send_integral_4" max="100000" min="1" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label choose_price" >奖品数量：</label>
                                                <div class="controls lin30">
                                                    <input type="number" class="form-control input-medium js-input-number input-num jpin ctx-num" placeholder="" name="point_num" value="0" data-default="0" min="0">
                                                    <span>个</span>
                                                    <div class="help-block">奖品数量为0时不设此奖项</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prize-group points-st2">
                                            <div class="over">
                                                <label class="control-label choose_price">送优惠：</label>
                                                <div class="controls">
                                                    <select data-placeholder="选择优惠券" class="ctx-con z-sel cou-sel">
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
                                                    <input type="number" class="form-control input-medium js-input-number input-num jpin ctx-num" placeholder="" value="0" data-default="0" min="0">
                                                    <span>个</span>
                                                    <div class="help-block">奖品数量为0时不设此奖项</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prize-group points-st3">
                                            <div class="control-group">
                                                <label class="control-label choose_price">赠品：</label>
                                                <div class="controls">
                                                    <input class="form-control input-medium js-input-number ctx-con" placeholder="请输入赠品名称" max="100000" min="0" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label choose_price">赠品数量：</label>
                                                <div class="controls lin30">
                                                    <input type="number" class="form-control input-medium js-input-number input-num jpin ctx-num" placeholder="" value="0" data-default="0" min="0">
                                                    <span>个</span>
                                                    <div class="help-block">奖品数量为0时不设此奖项</div>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label choose_price">兑奖方式：</label>
                                                <div class="controls">
                                                    <textarea class="form-control z-descr shuoming4" type="text" maxlength="300"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <div class="image-wrap">
                                                        <div class="image-display z-imgd"></div>
                                                        <input type="hidden" class="ctx-img" name="image_url">
                                                    </div>
                                                </div>
                                                <div class="controls sink">
                                                    <button class="btn btn-default js-upload-image" data-target="image_url" type="button">上传奖品图片</button>&nbsp;&nbsp;
                                                    <button class="btn btn-default js-clear-prize-image" type="button">清空</button>
                                                    <div class="help-block">仅支持 jpg、png、 <br />尺寸480*480 不超过1M</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prize-group points-st4">
                                            <div class="control-group shop_image_a">
                                                <label class="control-label choose_price">选择商品：</label>
                                                <input type="hidden" name="prize_send_integral_4" class="ctx-con" value="">
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
                            <div class="separate-line-wrap">
                                <hr>
                                <div class="separate-line">
                                    <p class="text-center">中奖结果说明</p>
                                </div>
                            </div>
                            <div>
                                <span class='no_warp_span'>未中奖说明:</span>
                                <textarea class='no_warp' type='text' name="" id="no_wrap" maxlength='300' style='border-color: rgb(204, 204, 204);'></textarea>
                            </div>
                        </fieldset>
                        <!--完成-->
                        <div class="filed-block notice steps step_4 hide">
                            <h4 class="success-title">你已成功创建该活动！</h4>
                            <hr class="hr-title">
                            <div class="control-group">
                                <label class="control-label">路径：</label>
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
                                    <img id="img_xcxm" src="" class="xcx-xcximg" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">
                                </div>
                            </div>
                        </div>
                        <!--下一步-->
                        <div class="app-actions">
                            <div class="form-actions text-center">
                                <a href="##" class="btn btn-default prev hide coloff">上一步</a>
                                <a href="##" class="btn btn-primary next">下一步</a>
                                <a href="##" class="btn btn-default reset hide coloff">修改</a>
                                <a type="submit" href="/merchants/marketing/scratchList" class="btn btn-primary sure hide">确认</a>
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
        var couponList = {!! json_encode($couponList) !!}
            console.log(data);
         console.log(couponList);
        var id ='';
        var id_1 = '';
        var id_2 = '';
        var id_3 = '';
        var id_4 = '';
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
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_m15z90yu.js"></script>
@endsection