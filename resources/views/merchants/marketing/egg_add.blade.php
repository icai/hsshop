@extends('merchants.default._layouts')
@section('head_css')
<!--bootstrapValidator文件引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marking-hitEgg-new.css" />
@endsection
@section('slidebar')
@include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{URL('/merchants/marketing')}}">营销中心</a>
            </li>
            <li>
                <a href="#">砸金蛋活动</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
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
            <li>活动列表</li>
        </ul>
        <!-- <a href="##" class="tutorial"><span id="icon">?</span>  查看【幸运砸蛋】使用教程</a> -->
    </div>
    <!--添加数据部分-->
    <div class="add_data">
        <!--新建幸运砸蛋 中部主要内容-->
        <div class="content_step">
            <ul class="step">
                <li class="active">活动设置</li>
                <li>奖项设置</li>
            </ul>
        </div>
        <form id="defaultForm" class="form-horizontal center_main clearfix" role="form">
            <div class="center_left pull-left rtv">
                <img src="{{ config('app.source_url') }}hsadmin/images/hitEggTitle.png"/>
                <div class="changeIndex rtv">
                    <div class="show_name">
                        <img src="{{ config('app.source_url') }}mctsource/images/hitEgg.png"/>
                    </div>
                    <div class="egg_cont flex_around">
                        <span class="egg egg_1">
                            <img src="{{ config('app.source_url') }}mctsource/images/goldEgg.png"/>
                        </span>
                        <span class="egg egg_2">
                            <img src="{{ config('app.source_url') }}mctsource/images/goldEgg.png"/>
                        </span>
                        <span class="egg egg_3">
                            <img src="{{ config('app.source_url') }}mctsource/images/goldEgg.png"/>
                        </span>
                    </div>
                    <!-- 活动规则开始 -->
                    <div class="egg_rule">
                        <div class="rule_box">
                            <div class="participate">
                                <div class="rule_title">
                                    参与次数
                                </div>
                                    <div class="js_daily item  @if(isset($limitData) && $limitData['join_limit']['type'] == 2)  hide @endif ">
                                        <span>今日您还可以抽奖次数：</span>
                                        <span class="red"> {{ isset($limitData)? $limitData['join_limit']['amount']: 1 }} </span>
                                    </div>

                                    <div class="js_the item @if(isset($limitData) && $limitData['join_limit']['type'] == 1)  hide @endif">
                                        <span>本次抽奖次数剩余：</span>
                                        <span class="red">{{ isset($limitData)? $limitData['join_limit']['amount']: 1}}</span>
                                    </div>



                                @if(isset( $detail['start_at'] ))
                                    <div class="item start_time ">
                                        <span>开始时间：</span>
                                        <span class="start_time_s red fz_12" style="">{{ $detail['start_at'] }} </span>
                                    </div>
                                    <div class="item end_time ">
                                        <span>结束时间：</span>
                                        <span class="end_time_s red fz_12">{{ $detail['end_at'] }} </span>
                                    </div>
                                    @else
                                    <div class="item start_time hide">
                                        <span>开始时间：</span>
                                        <span class="start_time_s red fz_12" style=""> </span>
                                    </div>
                                    <div class="item end_time hide">
                                        <span>结束时间：</span>
                                        <span class="end_time_s red fz_12"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="rule_box">
                            <div class="participate">
                                <div class="rule_title">
                                    活动说明
                                </div>
                                <div class="item">
                                    <div class="egg_intro">亲,祝您好运哦！</div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- 活动规则开始 -->
                </div>
                <!--改变背景图片的显示处-->
                <img class="new_add new_0" src="{{ config('app.source_url') }}shop/images/bg.jpg"/>
            </div>
            <!--第一部-->
            <div class="center_right step_1 pull-left rtv">
                <!--基础设置-->
                <div class="activity_set basi_set"><p class="activity_set_hint">基础设置</p></div>
                <!--活动标题-->
                <div class="form-group">
                    <label for="active_title" class="col-sm-3 control-label wd_120"><span class="red">*</span> 活动标题：</label>
                    <div class="col-sm-9 malef_10">
                        <input type="text" name="title" class="form-control" id="active_title" placeholder="标题最多不超过20字" @if(isset($detail['title'])) value="{{$detail['title']}}" @endif>
                    </div>
                </div>
                <!--活动时间-->
                <div class="form-group">
                    <label for="active_Btime" class="col-sm-3 control-label wd_120"><span class="red">*</span> 活动时间：</label>
                    <div class="col-sm-4 pd_l malef_10">
                        <input type="text" name="start_at" class="form-control pd_l5 fz_13" id="datetimepicker1" placeholder="开始时间">
                    </div>
                    <div class="col-sm-1 div_line">--</div>
                    <div class="col-sm-4 pd_l">
                        <input type="text" name="end_at" class="form-control pd_l5 fz_13" id="datetimepicker2" placeholder="结束时间">
                    </div>
                </div>
                <!--活动详情-->
                <div class="form-group">
                    <label class="col-sm-3 control-label">活动详情：</label>
                    <div class="col-sm-9 malef_10">
                        <textarea name="detail" @if(isset($detail['detail'])) value="{{ $detail['detail'] }}" @endif id="active_tetail"></textarea>
                    </div>
                </div>
                <!--活动开始-->
                <div class="form-group">
                    <label for="active_end" class="col-sm-3 control-label">活动开始：</label>
                    <div class="col-sm-9 malef_10">
                        <div>
                            <button type="button" class="btn btn-primary activeImg">选择图片</button>
                            <!-- <span class="fz_13" style="margin-left: 10px;">提示语：</span> -->
                        </div>
                        <div style="margin-top: 10px;">建议尺寸640*320px，宽度不小于320px（限制比例2:1），大小不超过3M</div>
                        @if(isset($detail['start_img_url']))
                            <input type="hidden" name="start_img_url" value="{{$detail['start_img_url']}}"/>
                            <img src="{{imgUrl($detail['start_img_url'])}}" width="250px" height="136px" style="margin-top: 10px;" />
                            @else
                            <input type="hidden" name="start_img_url" value="mctsource/images/reward.jpg"/>
                            <img src="{{ config('app.source_url') }}mctsource/images/reward.jpg" width="250px" height="136px" style="margin-top: 10px;" />
                        @endif

                    </div>
                </div>
                <!--活动结束-->
                <div class="form-group">
                    <label for="active_end" class="col-sm-3 control-label" style="padding-top: 0;">
                    活动结束：
                    <br/><font color="#ccc">（选填）</font>
                    </label>
                    <div class="col-sm-9 malef_10">
                        <input type="text" name="end_desc" class="form-control" id="active_end" @if(isset($detail['end_desc'])) value="{{$detail['end_desc']}}" @endif placeholder="活动结束语最多不超过30字" />
                    </div>
                </div>
                <!--中奖名单显示-->
                <!--update by 韩瑜 2018-8-20 营销活动优化，隐藏中奖名单开关-->
				<div class="form-group" style="display: none;">
                    <label for="" class="col-sm-3 control-label">中奖名单：<br/></label>
                    <div class="col-sm-9 uploadImg">
                        @if(isset($detail['is_show'])  && $detail['is_show'] == 1)
                            <label class="lab_flx"><input type="radio" name="is_show" id="showName" value="1"  checked="checked" />显示</label>
                            <label class="lab_flx"><input type="radio" name="is_show" id="hideName" value="0"  />隐藏</label>
                        @else
                            <label class="lab_flx"><input type="radio" name="is_show" id="showName" value="1"  />显示</label>
                            <label class="lab_flx"><input type="radio" name="is_show" id="hideName" value="0"  checked="checked" />隐藏</label>
                        @endif
                    </div>
                </div>
                
                <!--抽奖限制-->
                <!--update by 韩瑜 2018-8-10 营销活动优化-->
                <div class="activity_set basi_set">
                    <div class="activity_set_hint load">抽奖限制
                    </div>
                </div>
                <!--end-->
                
                <!--次数限制-->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">次数限制：</label>
                    <div class="col-sm-3 malef_10">
                        <p class="form-control">每人可抽奖</p>
                    </div>
                    <div class="col-sm-2">
                        <input name="join_limit[amount]" type="number" min="0" class="form-control js_times" @if(isset($limitData)) value="{{$limitData['join_limit']['amount']}}" @else value="1" @endif/>
                    </div>
                    <div class="col-sm-4">
                        <select name="join_limit[type]" class="form-control js_set">
                            <option value="2" @if(isset($limitData) && $limitData['join_limit']['type'] == 2 ) selected="selected" @endif >次/活动全程</option>
                            <option value="1" @if(isset($limitData) && $limitData['join_limit']['type'] == 1 ) selected="selected" @endif >次/每天</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"></label>
                    <div class="col-sm-3 malef_10">
                        <p class="form-control">每人最多中奖</p>
                    </div>
                    <div class="col-sm-2">
                        <input name="prize_limit[amount]" type="number" min="0" class="form-control" @if(isset($limitData)) value="{{$limitData['prize_limit']['amount']}}" @else value="1" @endif/>
                    </div>
                    <div class="col-sm-4">
                        <select name="prize_limit[type]" class="form-control">
                            <option value="2" @if(isset($limitData) && $limitData['prize_limit']['type'] == 2 ) selected="selected" @endif >次/活动全程</option>
                            <option value="1" @if(isset($limitData) && $limitData['prize_limit']['type'] == 1 ) selected="selected" @endif>次/每天</option>
                        </select>
                    </div>
                </div>
                <!--分享设置-->
                <div class="activity_set basi_set">
                    <div class="activity_set_hint share">分享设置
                        <div class="explain_2 i"></div>
                    </div>
                </div>
                <!--上传分享封面-->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">分享封面：<br/><font color="#ccc">（选填）</font></label>
                    <div class="controls" style="margin-left: -10px">
                        @if(isset($shareData) && !empty($shareData['share_img']) )
                            <input type="hidden" name="shareImg" value="{{$shareData['share_img']}}">
                            <div class="share_img_box">
                                <img style="width: 80px;height: 80px;" class="share_img" src="{{imgUrl($shareData['share_img'])}}" >
                                <span class="delete">x</span>
                            </div>
                            <a href="javascript:;" class="js-add-picture">修改图片</a>
                        @else
                            <input type="hidden" name="shareImg" value="">
                            <div class="share_img_box hide">
                                <img style="width: 80px;height: 80px;" class="share_img" src="">
                                <span class="delete">x</span>
                            </div>
                            <a href="javascript:;" class="add-goods js-add-picture">+添加图片</a>
                        @endif
                    </div>
                    <p class="up_tip">文件大小不超过3M，建议尺寸为750px*750px ,宽度大于400PX，（限制比例1:1）</p>
                </div>
                <!--分享标题-->
                <div class="form-group">
                    <label for="share_title" class="col-sm-3 control-label">分享标题：<br/><font color="#ccc">（选填）</font></label>
                    <div class="col-sm-9" style="margin-left: -10px">
                        <input type="text" name="share_title" class="form-control" id="share_title" @if(isset($shareData) && !empty($shareData)) value="{{$shareData['title']}}" @endif placeholder="少于30个汉字，含标点符号">
                    </div>
                </div>
                <!--分享描述-->
                <div class="form-group">
                    <label for="share_detail" class="col-sm-3 control-label">分享描述：<br/><font color="#ccc">（选填）</font></label>
                    <div class="col-sm-9" style="margin-left: -10px">
                        <input type="text" name="share_detail" class="form-control" id="share_detail" @if(isset($shareData) && !empty($shareData['share_desc']) ) value="{{$shareData['share_desc']}}" @endif placeholder="最多不超过50字">
                    </div>
                </div>
                
                <!--下一步按钮-->
                <div class="activity_set basi_set"></div>
                <div class="form-group">
                    <label for="share_link" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                        <button type="button" class="next btn btn-primary">下一步</button>
                    </div>
                </div>
                <!--分享实时展示部分-->
               
            </div>
            <!--第二部-->
            <div class="center_right step_2 pull-left rtv hide">
                <!--奖项设置-->
                <div class="activity_set basi_set"><p class="activity_set_hint share">奖项设置</p></div>
                <!--中奖设置-->
                @if(isset($detail))
                    <b>中奖设置</b>
                    <ul class="getPrize_set flex_center">
                        <li>奖项名称</li>
                        <li>奖项内容</li>
                        <li>获奖概率</li>
                        <li>奖项数量</li>
                        <li></li>
                    </ul>
                    <div class="prize_data">
                        @foreach($detail['prize_info'] as $v)
                            <div class="form-group getPrize_data">
                                <div class="col-sm-2">
                                    <input type="text" name="prize_name[]" class="form-control" value="{{ $v['name']  }}" placeholder="名称" />
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="prize_title[]" value="{{  $v['content'] }}" readonly style="background-color:rgba(0,0,0,0.1);">
                                        <input type="hidden" class="form-control" name="prize_id[]" value="{{  $v['type_id'] }}">
                                        <input type="hidden" class="form-control" name="prize_type[]" value="{{  $v['type'] }}">
                                        <input type="hidden" class="form-control" name="prize_log_id[]" value="{{  $v['id'] }}">
                                        <input type="hidden" class="form-control" name="prize_method[]" value="{{  $v['method'] }}">
                                        <input type="hidden" class="form-control" name="prize_img[]" value="{{  $v['img'] }}">
                                    <span class="input-group-btn">
							        	<button class="btn btn-default" type="button">重选</button>
							      	</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input type="number" name="prize_probability[]" class="form-control prize_probability js_input" min="0" max="100" value="{{  $v['percent'] }}">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="prize_number[]" min="0" max="5000" class="form-control js_input" value="{{  $v['amount'] }}" />
                                </div>
                                <div class="col-sm-1 flex_around">
                                    
                                    <a href="##"  class="dustbin" style="display: inline-block"><img src="{{ config('app.source_url') }}hsadmin/images/cancel.png"/></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-success addPrize">添加奖项</button>
                    <!--未中奖设置-->
                    <b class="flex_middle">未中奖设置
                        <div class="explain_3 i"></div>
                    </b>
                    <div class="form-group noPrize_set">

                        <div class="col-sm-3 malef15">
                            <input type="text" name="noPrizeName" class="form-control" id="noPrize_hint_1" value="{{ $noPrize['name'] }}">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="noPrizeContent" class="form-control" id="noPrize_hint_2" value="{{ $noPrize['content'] }}">
                        </div>
                        <div class="col-sm-4">
                            <input type="hidden" name="noPrizeId" class="form-control" id="noPrize_hint_3" value="{{ $noPrize['id'] }}">
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="number" min="0" max="100" name="noPrize_probability" class="form-control" id="noPrize_probability" value="{{ $noPrize['percent'] }}" disabled="disabled">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div>
                @else
                    <b>中奖设置</b>
                    <ul class="getPrize_set flex_center">
                        <li>奖项名称</li>
                        <li>奖项内容</li>
                        <li>获奖概率</li>
                        <li>奖项数量</li>
                        <li></li>
                    </ul>
                    <div class="prize_data">

                    </div>
                    <button type="button" class="btn btn-success addPrize">添加奖项</button>
                    <!--未中奖设置-->
                    <b class="flex_middle">未中奖设置
                        <div class="explain_3 i"></div>
                    </b>
                    <div class="form-group noPrize_set">
                        <div class="col-sm-3 malef15">
                            <input type="text" name="noPrizeName" class="form-control" id="noPrize_hint_1" value="感谢参与">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="noPrizeContent" class="form-control" id="noPrize_hint_2" value="很遗憾您没有中奖，感谢参与！">
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="number" min="0" max="100" name="noPrize_probability" class="form-control" id="noPrize_probability" value="" disabled="disabled">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div>
                @endif
	                {{csrf_field()}}
	                <div class="activity_set basi_set"></div>
	                <!--确定发布按钮-->
	                <button type="submit" class="btn btn-primary public_save">发布并保存</button>
				</div>
            </div>
        </form>
        
        <!--添加奖项的模态框-->
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h5 class="modal-title" id="myModalLabel">
                            <span class="coupons">优惠券</span>
                            <span class="cromo_code a_active">积分</span>
                            <span class="gift a_active">赠品</span>
                        </h5>
                    </div>
                    <!--优惠卷显示-->
                    <div class="modal-body modal-body_1">
                        <div class="buildPrize flex_end">
                            <a class="setPrize" href="JavaScript:void(0);">创建奖品</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-success">刷新</button>
                        </div>
                        <!--动态添加的容器-->
                        <div class="prizeList">
                            
                            <div class="noPrize hide">
                                <span>您还没有创建优惠券，</span>
                                <a href="/merchants/marketing/coupon/set" target="_blank">可到奖品库中创建</a>
                            </div>
                        </div>
                        <div class= "myModalPage" style="text-align: right;margin-right: 20px;"></div><!-- 分页 -->
                    </div>
                    <!--优惠 码显示-->
                    <div class="modal-body modal-body_2 hide">
                        <div class="buildPrize flex_end">
                            <a class="setPrize" href="JavaScript:void(0);">创建奖品</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-success">刷新</button>
                        </div>
                        <!--动态添加的容器-->
                        <div class="prizeList">
                            <div class="integralList">
                                
                                <div class="noPrize hide">
                                    <span>您还未添加积分，</span>
                                    <a class="setIntegral" href="javascript:void(0);" target="_blank">可在此添加</a>
                                </div>
                            </div>
                            <div class="addIntegral hide">
                                <div class="form-group clearfix">
                                    <label class="col-sm-4 control-label wd_120" style="margin-top: 5px;">单次奖励积分：</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control per_score" placeholder="100" />
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-sm-4 control-label wd_120" style="margin-top: 5px;">活动总积分：</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control amount_score" placeholder="总积分最多500000" />
                                    </div>
                                </div>
                                <div class="btn_group">
                                    <button type="button" class="btn btn-primary sureAddIntegral">确定</button>
                                    <button type="button" class="btn btn-default">取消</button>
                                </div>
                            </div>
                        </div>
                        <div class= "myModalPage1" style="text-align: right;margin-right: 20px;"></div><!-- 分页 -->
                    </div>
                    <!--add by 韩瑜 2018-8-13 赠品添加-->
                    <div class="modal-body modal-body_3 hide">
						<div class="control-group">
							<label class="control-label choose_price">赠品：</label>
							<div class="controls">
								<input class="form-control input-medium js-input-number fir-con zp-name" placeholder="请输入赠品名称" max="100000" min="0" value="">
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
						<div class="control-group">
							<label class="control-label choose_price">上传奖品图片：</label>
							<div class="control-label addpic_wrap">
								<div class="image-wrap">
									<button class="btn btn-default js-upload-image addpic" data-target="image_url" type="button"></button>
									<div class="image-display z-imga js-upload-image" style="display: none;"></div>
									<input class="fir-img" type="hidden" name="image_url">
								</div>
							</div>
							<div class="controls sink">
								<button class="js-clear-prize-image" type="button">清空</button>
								<div class="help-block">仅支持 jpg、png、 尺寸480*480px 不超过1M</div>
							</div>
						</div>					
					</div>
					
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sureAdd">确定</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<!--主要内容js文件-->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!-- 富文本编译器 -->
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
<!--bootstrap表单验证js文件引入-->
<script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/zh_CN.js" type="text/javascript" charset="utf-8"></script>
<!--主要内容js文件-->
<script type="text/javascript">
    var _host = "{{ imgUrl() }}";
    var editorContent = '{!! isset($detail["detail"])? $detail["detail"]: ""!!}';
    var edit = "{{ isset($detail['id']) ? $detail['id'] : '' }}";
    var start_at = "{{ $detail['start_at']?? '' }}";
    var end_at = "{{ $detail['end_at']?? '' }}";
    
</script>
<script src="{{ config('app.source_url') }}mctsource/js/marking-hitEgg-new.js" type="text/javascript" charset="utf-8"></script>

@endsection