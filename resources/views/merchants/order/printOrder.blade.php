@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前模块公共css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_llbq22x2.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_kg1fntrz.css" />
@endsection
@section('slidebar')
    @include('merchants.order.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_title">
            <a href="javascript:void(0);" class="third_title_dd">快速打单</a>
        </div>
        <!-- 二级导航三级标题 结束 -->
        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
	<!--author 韩瑜  date 2018.6.29-->
	<div style="margin: 10px 10px 0 10px;background-color: #ffffff;">
		<div class="header_cs_wrap">
	        <div class="header_cs">
	            <span class="header_cs_set params-set">快递管家参数配置</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.huisou.cn/home/index/detail/790/help" target="_blank">快速打单说明</a>
	        </div>
	    </div>
		<div class="content_button_wrap">
				<a href="http://www.kuaidi100.com/" target="_blank">
					<div class="content_button" >登录快递管家平台</div>
				</a>
		</div>
	</div>
@endsection
@section('other')
    <!-- 设置打单参数弹窗 -->
    <!--author 韩瑜  date 2018.6.29-->
    <div class="modal export-modal myModal-adv modal_back" id="params-set">
        <div class="modal-dialog" id="params-set-dialog">
            <form class="form-horizontal" id="params_set_form">
                <div class="modal-content">
                	
                	<!--弹框头-->
                    <div class="modal_header">
                        <button type="button" class="close" data-dismiss="myModal-adv">
                            <span>&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
						<div class="modal_header_title">
                            <span>快递管家参数绑定</span>
                            <a href="https://www.huisou.cn/home/index/detail/790/help" target="_blank">
                            <span class="modal_header_title_r"><img src="/mctsource/images/bz2.png"/>帮助教程</span>
                            </a>
						</div>
                    </div>
                    
                    <!--弹框内容-->
                    <div class="modal_body">
                        <div class="modal_body_dy">
                            <label class="modal_body_dy_choose"><span style="color: #ff0000;">*</span>选择打印方式：</label>
                            <div class="modal_body_dy_r">
                                <label class="radio inline">
                                    <input type="radio" id="print_type" value='1' checked="checked">手动打印
                                </label>
                                <label class="radio inline">
                                    <input type="radio" id="print_type" value='2' disabled>自动打印<span style="color: #949494;">(自动打印需要购买快递管家官方指定打印机)暂未开放</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal_body_cs">
                        	<label class="modal_body_cs_l"><span style="color: #ff0000;">*</span>填写参数配置：</label>
                            <label style="font-size: 14px;">快递管家appKey：</label>
                            <div class="modal_body_in">
                                <input type="text" id="kuaidi_app_id" name="kuaidi_app_id" value="" placeholder="请咨询校对快递管家的参数">
                            </div>
                        </div>
                        <div class="modal_body_ap">
                            <label class="">快递管家appSecret：</label>
                            <div class="modal_body_in">
                                <input type="text" id="kuaidi_app_secret" name="kuaidi_app_secret" value="" placeholder="请详细并认真填写秘钥">
                            </div>
                        </div>
                        <div class="modal_body_zh">
                            <label class="">快递管家登录账号：</label>
                            <div class="modal_body_in">
                                <input type="text" id="kuaidi_app_uid" name="kuaidi_app_uid" value="" placeholder="请填写快递管家的登录名">
                            </div>
                        </div>
                        <div class="modal_body_ts"><span style="color: #ff0000;">*</span>为避免后续打单出错，请在快递管家订单导入API回调url填写：
                            <input type="text" style="width: 260px;" class="int-cody-a" readonly="" value="https://www.huisou.cn/merchants/kuaidi/kuaidiNotify">
                            <a class="btn js-btn-copy code-copy-a" data-clipboard-text="">复制</a>
                        </div>
                    </div>                    
                    <!--弹框按钮-->
                    <div class="modal_button">
                        <div class="modal_button_wrap">
                            <a class="js-confirm modal_button_save">保存</a>
                            <a class="js-cancel modal_button_close">取消</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page_js')
    <!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/order_logistics.js"></script>

@endsection

