@extends('merchants.default._layouts')
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <!-- 选择商品样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
    <!-- 当前页面css -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/together_wxpj42f2.css" />
    <style type="text/css">
        .laydate_box, .laydate_box * {box-sizing:content-box;}
    </style>
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
       
        
        <!-- 新增内容 开始 -->
        <div class="page-groupon clearfix">
            <h2 class="groupon-title">设置拼团活动</h2>
            <div class="app-design"> 
                <div class="control-group">
                    <label class="control-label"><span class="required">*</span> 选择商品：</label>
                    <span class="control-group-span"> 
                        <a class="sel-goods"  href="javascript:;">
                        	+
                            <i class="icon-add hide"></i>
                        </a>  
                        <input type="hidden" class="validate" id="goods_id" />
                        <p class="t-tips error-message">请选择一个要进行多人拼团的商品。</p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label"><span class="required">*</span> 推广图：</label>
                    <span class="controls control-group-span example_span">
                        <input type="hidden" class="validate" name="activity_img" value="" />
                        <a href="javascript:;" class="add-goods js-activity-img" data-index = '1' style='width: 140px'>
                            <span style='color: #3197FA'>+添加图片</span>
                            <div class="share_img_box hide">
                                <img src="" style="width: 140px;height: 80px;" class="activity_img">
                                <span class="delete del_activity">x</span>
                            </div>
                        </a>
                        <span class="active_span">建议尺寸750*400px，高度不大于400px（限制比例2:1），大小不超过3M</span>
                        <p class="t-tips error-message">请选择活动图。</p>
                        <div class='example hide'>
                            <div class='example_title'>示例</div>
							<div class='example_box'>
								<div class='example_box_img'>
									<img src="" alt="" class='hide' id="pintuan_img">
								</div>
							</div>
                        </div>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label"><span class="required">*</span> 活动图：</label>
                    <span class="controls control-group-span"> 
                        <input type="hidden" class="validate" name="img2" value="" />
                        <a href="javascript:;" class="add-goods js-activity-img" data-index = '2'>
                            <span style='color: #3197FA'>+添加图片</span>
                            <div class="share_img_box hide">
                                <img src="" style="width: 80px;height: 80px;" class="activity_img2">
                                <span class="delete del_activity">x</span>
                            </div>
                        </a>
                        <span class="active_span">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</span>
                        <p class="t-tips error-message">请选择活动图。</p> 
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8"><span class="required">*</span> 活动名称：</label>
                    <span class="control-group-span">
                    <!-- <span class="control-error"> -->
                        <input type="text" id="title" class="form-control validate w220" value="" placeholder="输入活动名称">
                        <p class="t-tips error-message">请输入活动名称。</p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8">活动副标题：</label>
                    <span class="control-group-span">
                    <!-- <span class="control-error"> -->
                        <input type="text" id="subtitle" class="form-control w220" value="" placeholder="输入活动副标题">
                        <p class="t-tips error-message">请输入活动副标题。</p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8">活动标签：</label>
                    <span class="control-group-span">
                    <!-- <span class="control-error"> -->
                        <input type="text" id="label" class="form-control w220" value="" placeholder="输入活动标签">
                        <p class="t-tips error-message">请输入活动标签。</p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8"><span class="required">*</span> 生效时间：</label>
                    <span class="control-group-span">
                        <input type="text" id="start_time" value="" class="form-control validate control-error w220" />
                        <span class="time_span">&nbsp;至&nbsp;</span>
                        <input type="text" id="end_time" value="" class="form-control validate control-error w220" />
                        <p class="t-tips error-message">请设置团购的结束时间。</p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8"><span class="required">*</span> 参团人数：</label>
                    <span class="control-group-span">
                        <input type="text" id="join_num" class="form-control validate w220" value="2"> 
                        <p class="t-tips gray">请填写2-100的数字</p>
                        <p class="t-tips error-message">参团人数需不超过100人上限</p> 
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label">商品限购：</label>
                    <div class="control-group-span">
                    	<div class="box_flex">
	                        <input id="is_limit" type="checkbox" >
	                        <span class="fsiz13">开启限购</span>                    		
                    	</div>
                        <div class="product_limit hide">
                            <div style="margin-top: 8px;" class="box_flex">
                                <input type="radio" checked="" name="limit_type" value="0">
                                <span class="fsiz13">每单限购&nbsp;</span> 
                                <input class="form-control w50 limit_num" type="text" value="">
                                <span>&nbsp;件/人</span> 
                            </div>
                            <div style="margin-top: 8px;" class="box_flex">
                                <input type="radio" name="limit_type" value="1">
                                <span>每人限购&nbsp; </span> 
                                <input class="form-control w50 limit_num" type="text" value="">
                                <span>&nbsp;件/人</span> 
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">凑团设置：</label>
                    <span class="controls control-group-span"> 
                    	<div class="box_flex">
	                        <input class="js-join-group-switch" type="checkbox" checked="">
	                        <span class="fsiz13">开启凑团</span> 
                        </div>
                        <p class="t-tips">
                            <span>开启凑团后，对于未参团的买家，活动商品详情页会显示未成团的团列表，买家可以直接任选一个参团，提升成团率。</span>
                        </p>
                    </span>
                </div>   
                <div class="control-group">
                    <label class="control-label pd_8">未成团订单存在时间：</label>
                    <span class="controls control-group-span"> 
                        <input type="number" class="form-control text-center w70" value="0" id="expire_day" /><span class="time_span">&nbsp;天&nbsp;</span><input type="number" class="form-control  text-center w70" value="24" id="expire_hours" /> 小时后自动成团
                        <p class="t-tips">
                            <span>若未开启自动成团，到时候后订单会自动关闭或退款</span><br />
                            <span>若开启了自动成团，到时间后未成团订单自动成团</span>
                        </p>
                    </span> 
                </div>
                <div class="control-group">
                    <label class="control-label">自动成团设置：</label>
                    <span class="controls control-group-span"> 
                        <label for="auto_success" class="cb-groups box_flex">
                            <input name="auto_success" id="auto_success" type="checkbox" checked="checked" />
                            <span>开启自动成团</span>
                        </label>  
                    </span> 
                </div> 
                <div class="control-group">
                    <label class="control-label">是否开启抽奖：</label>
                    <span class="controls control-group-span box_flex">
                        <input type="checkbox" id="is_open_draw" />
                        <span>开启抽奖(商品即为奖品)</span>
                    </span>
                    <div class="group-box hide">
                        <div class="group-wrap mt5">
                            设置中奖人数：<input type="number" id="draw_pnum" class="form-control text-center w70"  />&nbsp; 人
                        </div>
                        <p class="group-wrap-span">
                            <span>设置为0，则不会有人中奖</span>
                            <span>设置人数大于0时，抽奖团活动结束后，中奖订单将会显示为待发货，未中奖订单将会全额退款到客户处</span>
                        </p>
                        <div class="group-wrap mt10 mb5">
                            设置中奖类型：
                        </div>
                        <div class="group-wrap ml160">
                            <label for="rd_sj" class="cb-groups"><input type="radio" name="draw_type" value="0" checked="checked" />随机 </label>
                            <span class="group-wrap-span">从团员中随机选取中奖人员</span>
                        </div>
                        <!--<p class="t-tips">
                            <span>从团员中随机选取中奖人员</span>
                        </p>-->
                        <div class="group-wrap ml160">
                            <label for="rd_zd" class="cb-groups"><input type="radio" name="draw_type" value="1" />指定 </label>
                            <span class="group-wrap-span">指定中奖人员</span>
                        </div>
                        <!--<p class="t-tips">
                            <span>指定中奖人员</span>
                        </p>-->
                        <div class="group-wrap mt10">
                            手机号：<input type="text" id="draw_phones" class="form-control w300" placeholder="多个手机号以逗号隔开" disabled="" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">服务保障：</label>
                    <span class="controls control-group-span box_flex">
                        <input  type="checkbox" class="service_by">
                        <span style="margin-right: 10px;" >全场包邮</span>
                        <input  type="checkbox" class="service_bz">
                        <span style="margin-right: 10px;" >品质保证</span>
                        <input  type="checkbox" class="service_th">
                        <span>七天无理由退换</span>
                    </span>
                    <div class="service" style="margin-left: 136px;margin-top: 10px; display: none">
                        <textarea style="display:none" id="service_txt1">{{ $rule['service_txt'] or '' }}</textarea>
                        <textarea id="service_txt" name="" rows="" cols="">{{$rule['service_txt'] or '' }}</textarea>
                    </div>
                </div>
                @if($distribute['is_distribute'] == 1)
                <div class="control-group">
                    <label class="control-label">
                        是否开启分销：
                    </label>
                    <div class="controls">
                        <div class="show_dis">
                            <label class="mr15 box_flex" style="float: left;">
                                <input name="is_distribute" type="radio" value="1"/>
                            	<span>是</span>
                            </label>
                            <label class="box_flex">
                                <input name="is_distribute" type="radio" value="0"/>
                                <span>否</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="show_fenxiao">
                    <div class="control-group">
                        <label class="control-label" style="margin-top: 2px;">
                            分销模板：
                        </label>
                        <div class="controls">
                            <div class="level changef">
                                <span class="level f_level" data-id=''></span>
                                <a id="defaultDistribute" href="javascript:;">【更换】</a>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" style="margin-top: 2px;">
                            佣金设置：
                        </label>
                        <div class="controls">
                            <ul class="comset">
                                <li>
                                    <p>商品价格</p>
                                    <div class="is_distribute_price"></div>
                                </li>
                                <li>
                                    <p>商品成本</p>
                                    <div class="is_distribute_cost"></div>
                                </li>
                                <!--<li>
                                    <p>本级佣金</p>
                                    <div class="is_distribute_zero"></div>
                                </li>-->
                                <li>
                                    <p>一级佣金</p>
                                    <div class="is_distribute_one"></div>
                                </li>
                                <li>
                                     <p>二级佣金</p>
                                     <div class="is_distribute_sec"></div>
                                 </li>
                                <!-- <li>
                                     <p>三级佣金</p>
                                     <div class="is_distribute_three"></div>
                                 </li>-->
                            </ul>
                            <p class="t-tips control-tips">如果商品有多个sku的售价不一致，则按相应比例换算成佣金金额</p>
                        </div>
                    </div>
                </div>
                @endif
                <div class="control-group">
                    <label class="control-label">团长优惠：</label>
                    <span class="controls control-group-span"> 
                    	<div class="box_flex">
	                        <input class="js-chief-discount-switch" type="checkbox">
	                        <span>团长享受优惠价</span>                    		
                    	</div>
                        <p class="t-tips">
                            <span>
                                开启团长(开团人)优惠后，团长将享受更优惠价格，有助于提高开团率和成团率。
                            </span> 
                        </p>
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label">
                        <span class="required">*</span> 拼团类型：
                    </label>
                    <div class="controls">
                        <div class="show_dis">
                            <label class="mr15 box_flex show_dis_label">
                                <input name="group_type" type="radio" value="1" checked />
                                <span>普通拼团</span>
                            </label>
                            <label class="mr15 box_flex show_dis_label">
                                <input name="group_type" type="radio" value="2"/>
                                <span>9.9元拼团</span>
                            </label>
                            <label class="mr15 box_flex show_dis_label">
                                <input name="group_type" type="radio" value="3"/>
								<span>0元开团</span>
                            </label>
                            <label class="mr15 box_flex show_dis_label">
                                <input name="group_type" type="radio" value="4"/>
                                <span>团长半价</span>
                            </label>
                            <label class="mr15 box_flex show_dis_label">
                                <input name="group_type" type="radio" value="5"/>
                                <span>团长折扣</span>
                            </label>
                            <label class="box_flex show_dis_label">
                                <input name="group_type" type="radio" value="6"/>
                                <span>抽奖团</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label pd_8">分享标题设置：</label>
                    <span class="controls"> 
                        <input class="form-control w220" type="text" id="share_title"> 
                    </span>
                </div>
                <div class="control-group">
                    <label class="control-label">分享内容设置：</label>
                    <span class="controls"> 
                        <textarea cols="50" rows="10" class="form-control" style="width: 300px;" id="share_desc"></textarea> 
                    </span>
                </div>

                <div class="control-group">
                    <label class="control-label">分享页图片：</label>
                    <div class="controls" style='position: relative;'>
                        <input type="hidden" name="share_img">
                        <a href="javascript:;" class="add-goods js-add-picture">
                            <span style="color: #3197FA">+添加图片</span>
                            <div class="share_img_box hide" id="rule_img">
                                <img src="" style="width: 80px;height: 80px;" class="share_img">
                                <span class="delete del_share">x</span>
                            </div>
                        </a>
                        <span class="active_span" style="vertical-align: top;margin-left: 10px">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</span>
                        <div class='example'>
                            <div class='example_title'>示例</div>
							<div class='example_box'>
								<div class='example_box_img'>
									<img src="" alt="" class='hide' id="shara_img">
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="control-group none" id="div_spec">
                    <label class="control-label"><span class="required">*</span> 优惠设置：</label>  
                    <table class="spec-table">
                    </table>  
                </div>  
            </div>
               
                
                
            </div> 
        </div>
        <!-- 新增内容 结束 --> 
    </div>
    <!-- 底部保存 -->
    <div class="t-footer">
        <input class="btn js-btn-quit btn-sm" type="button" onclick="history.back();" value="取消">
        <input class="btn btn-primary btn-sm ml10 js-btn-save" type="button" value="保存">
        <input type="hidden" id="wid" value="{{session('wid')}}" />
        <input type="hidden" id="group_id" value="@if($tag==1){{json_decode($rule,true)['id']}}@endif" />
    </div> 
    <!--弹框 选取商品-->

@endsection

@section('page_js')
    <script type="text/javascript">
        var host = "{{ config('app.url') }}";
    </script>
    <!-- 富文本编译器 -->
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>

    <script>
        var _host = "{{ imgUrl() }}";
        var rule = {!! $rule !!};
        var tag = {{$tag}}; //0 添加，1：编辑 2: 查看
        var distribute = {!! json_encode($distribute) !!};
        var service_txt = '{!! isset(json_decode($rule,true)["service_txt"])? json_decode($rule,true)["service_txt"]: ""!!}';
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/distribute_8gn5zwn3.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/together_wxpj42f2.js"></script>    
@endsection