@extends('merchants.default._layouts')
@section('head_css') 
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
<!-- 选择商品样式 -->
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/common_selgoods.css" /> 
<link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/css/together_wxpj42f2.css" />
<style type="text/css">
     .laydate_box, .laydate_box * {box-sizing:content-box;}
 </style>
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrapValidator.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/voteAdd.blade.css" />

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
                    <a href="javascript:void(0)">投票</a>
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
                    <li class="hover">
                        <a href="javascript:void(0)">所有促销</a>
                    </li>
                   
                </ul>  
            </div>  
            
        </div> 
        <!-- 导航模块 结束 -->
        <!-- 新增内容 开始 -->
    
    <div>
    	<!--活动规则-->
        <div id="act_gz">
         	<div class="gz_1">活动规则</div>
         	<div class="gz_2">
         		<div class="activity_set basi_set">
                    <div class="activity_set_hint load">投票限制
                        <div class="explain_1 i" data-original-title="" title=""></div>
                    </div>
               </div>
                <div class="control-group form-group has-success" style="width: 100%;">
	                    <label for="" class="col-sm-3 control-label">人数限制：</label>
	                    <div class="col-sm-3">
	                        <input class="form-control" value="每人每天可以给"></input>
	                    </div>
	                    <div class="col-sm-2">
	                        <input type="number" min="1" id="many_people" class="form-control js_times" value="{{ $voteData['many_people'] or '1' }}">
	                        <small class="help-block" data-bv-validator="greaterThan" data-bv-for="join_limit[amount]" data-bv-result="VALID" style="display: none;">请输入大于等于 1的数值</small>
	                        <small class="help-block" data-bv-validator="integer" data-bv-for="join_limit[amount]" data-bv-result="VALID" style="display: none;">请输入有效的整数值</small>
	                    </div>
	                    <div class="col-sm-2" >
	                        <p class="form-control">人投票</p>
	                    </div>
	                </div>
	                
	                 <div class="control-group form-group has-success" style="width: 100%;">
	                    <label for="" class="col-sm-2 control-label">票数限制：</label>
	                    <div class="col-sm-3">
	                        <input class="form-control" value="每人每天给1人可投"></input>
	                    </div>
	                    <div class="col-sm-2">
	                        <input name="join_limit[amount]" type="number" min="1" class="form-control js_times" value="{{ $voteData['many_ticket'] or '1' }}" data-bv-field="join_limit[amount]" id="many_ticket">
	                        <small class="help-block" data-bv-validator="greaterThan" data-bv-for="join_limit[amount]" data-bv-result="VALID" style="display: none;">请输入大于等于 1 的数值</small>
	                        <small class="help-block" data-bv-validator="integer" data-bv-for="join_limit[amount]" data-bv-result="VALID" style="display: none;">请输入有效的整数值</small>
	                    </div>
	                    <div class="col-sm-2">
	                       <p class="form-control">票</p>
	                    </div>
	                </div>
         	</div>
        </div>
        <!--活动首页信息-->
        <div id="act_sy">
         	<div class="sy_1">活动首页信息</div>
         	<div class="sy_2">
         		<div class="control-group form-group">
                    <label class="col-sm-2 control-label">
                       <span class="red">*</span> 活动标题：
                    </label>
                    <div class="controls col-sm-3">
                        <div class="show_dis">
                            <label>
                                 <input id="act_title"  type="text" size="30" style="border-radius: 4px;" class="form-control ng-pristine ng-untouched ng-valid" value="{{ $voteData['act_title'] or ''}}">
                            </label>      
                        </div>
                     </div>
                </div>
         		
                <div class="form-group control-group">
                    <label class="col-sm-2 control-label">
                       <span class="red">*</span> 活动时间：
                    </label>
                    <div class="col-sm-3 pd_l">
                        <input type="text" name="start_at" class="form-control pd_l5 fz_13" id="datetimepicker1" placeholder="开始时间" value="@if(isset($voteData['start_time'])){{ date('Y-m-d H:i:s',$voteData['start_time']) }}@endif">
                    </div>
                    <div style="padding:0;text-align: center;margin-top: 7px;">--</div>
                    <div class="col-sm-3 pd_l">
                        <input type="text" name="end_at" class="form-control pd_l5 fz_13" id="datetimepicker2" placeholder="结束时间" value="@if(isset($voteData['end_time'])){{ date('Y-m-d H:i:s',$voteData['end_time']) }}@endif">
                    </div>
                </div>              
                                
         		
         		<div class="js-pre-sale-wrap" style="display: block;">

                    <div class="control-group form-group">
                        <label class="col-sm-2 control-label">
                           <span class="red">*</span> 活动图：
                        </label>
	                    <div class="controls">
			                <div class="col-sm-9 uploadImg ">
			                    <div class="setImg rtv" >+
			                        <a href="javascript:void(0);" class="clear_img">×</a>
			                        <!--上传成功显示图片-->
                                    @if(isset($voteData['act_img']) && $voteData['act_img'])
			                        <img id="act_img" class="abt" style="width: 100px; height: 100px;" src="{{ imgUrl() }}{{ $voteData['act_img'] or ''}}">
                                    @else
                                    <img id="act_img" class="abt" style="width: 100px; height: 100px;" src="">
                                    @endif
			                        <input type="hidden" name="shareImg">
			                    </div>
				                <p class="help-desc tip_span">
				                    建议尺寸：640 x 320 像素；您可以拖拽图片调整图片顺序。
				                </p>
			                </div>
	                    </div>       
                	</div>         
                </div>
         	
         	
             	<div class="control-group form-group">
                    <label class="col-sm-4 control-label">
                       <span class="red">*</span> 号码关键字：
                    </label>
                    <div class="controls col-sm-3">
                        <div class="show_dis">
                            <label>
                                <input name="num_keyword" id="num_keyword" type="text" size="30" style="border-radius: 4px;" class="form-control ng-pristine ng-untouched ng-valid" value="{{ $voteData['keyword'] or '' }}">
                            </label>
                           
                        </div>
                    </div>
                </div>
                                
                <div class="control-group">
                    <label class="col-sm-4 control-label">
                       <span class="red">*</span> 投票规则：
                    </label>
                    <div class="controls col-sm-6">
                        <div class="level changef">
                            <textarea id="vote_rule" cols="50" rows="10" style="border-radius: 4px; height: 200px;" name="share_desc" class="form-control ng-pristine ng-untouched ng-valid">{{ $voteData['vote_rule'] or ''}}</textarea>
                        </div>
                    </div>
                </div> 
            </div>

        </div>
        
        <!--报名其他信息-->
        <div id="act_syb">
            <div class="gz_1">报名其他信息</div>
            <div class="gz_2">
                <div class="control-group form-group" style="width: 100%">
                    <label class="col-sm-2 control-label">
                       报名成功后
                    </label>
                </div>
                
                <div class="form-group control-group" style="width: 100%">
                    <label class="col-sm-5 control-label">
                       <span class="red">*</span> 关注微信：
                    </label>
                    <div class="col-sm-3">
                        <label>
                            <input name="share_title" type="text" size="30" style="border-radius: 4px;" class="form-control ng-pristine ng-untouched ng-valid" >
                        </label>
                       
                    </div>
                </div>              
            </div>
        </div>
        
        
        
     <!--大奖设置信息-->
    <div id="act_dj">
        <div class="dj_1">大奖设置信息</div>
        <div class="dj_2">
            <textarea style="display:none" id="prize_set1">{{ $voteData['prize_set'] or '' }}</textarea>
            <textarea id="prize_set" name="" rows="" cols="">{{ $voteData['prize_set'] or '' }}</textarea>
        </div>
    </div>
     
     
     <!--拉票秘籍-->
    <div id="act_dj">
     	<div class="dj_1">拉票秘籍</div>
     	<div class="dj_2">
            <textarea style="display:none;opacity:0" id="canvass_info1" name="" rows="" cols="">{{ $voteData['canvass_info'] or '' }}</textarea>
     		<textarea id="canvass_info" name="" rows="" cols="">{{ $voteData['canvass_info'] or '' }}</textarea>
     	</div>
    </div>
     
      <!--活动规则-->
    <div id="act_dj">
     	<div class="dj_1">活动规则</div>
     	<div class="dj_2">
            <textarea style="display:none;opacity:0" id="act_rule1" name="" rows="" cols="">{{ $voteData['act_rule'] or '' }}</textarea>
     		<textarea id="act_rule" name="" rows="" cols="">{{ $voteData['act_rule'] or '' }}</textarea>
     	</div>
    </div>

    <input type="hidden" name="id" value="{{ $voteData['id'] or 0}}">
    <!--提交预览-->
    <div id="act_tj">
     	<input class="tj_1" type="submit"  value="提交" />
        <button class="tj_2">预览</button>
    </div>
         
    </div>  
            
        <!-- 新增内容 结束 --> 
    
    
@endsection

@section('page_js')
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script> 

 
<!-- 富文本编译器 -->
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>

<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>

 <!-- 当前页面js -->
 <script type="text/javascript">
 	var _host = "{{ imgUrl() }}";

 	var prize_set = '{!! isset($voteData["prize_set"])? $voteData["prize_set"]: ""!!}';
    var canvass_info = '{!! isset($voteData["canvass_info"])? $voteData["canvass_info"]: ""!!}'; 
    var act_rule = '{!! isset($voteData["act_rule"])? $voteData["act_rule"]: ""!!}';
     var start_at = "{{ $detail['start_at']?? '' }}";
    var end_at = "{{ $detail['end_at']?? '' }}";
 </script>
<script src="{{ config('app.source_url') }}mctsource/js/voteAdd.blade.js" type="text/javascript" charset="utf-8"></script>


@endsection

