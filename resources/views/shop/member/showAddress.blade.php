@extends('shop.common.template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showAddress.css">
@endsection
@section('main')  
	<div class="addressManagement" id="app" v-cloak>
	    <!--地址列表-->
	    <div class="address_list">
	      <div class="address_list_sort" v-for="(val,index) in  address_list_data">
	        <div class="address_detail" @click="chooseAddress(val)" style='padding: 10px'>
	          <div class="address_title">
	          	<span v-text="val.title"></span>
	          	<span v-text="val.phone"></span>
	          </div>
	          <div class="address_desc">
	          	<span v-text="val.province.title"></span><span v-text="val.city.title"></span><span v-text="val.area.title"></span>
	          	<span v-text="val.address"></span>
	          </div>
	        </div>
	        <div class="operation flex_between" style='padding: 0 10px'>
	          <div class="flex_around slecect_img" @click="selected_default(index,val.id)" >
	            <img style="width: 18px;height: 18px;" :src="click_index == index ? '{{ config('app.source_url') }}shop/images/dui@2x.png' : '{{ config('app.source_url') }}shop/images/ap-weixuan@2x.png'  " alt="" >
	            <span style="color:#333333" v-if="click_index == index">已设置为默认</span>
	            <span style="color:#333333" v-else>设为默认</span>
	          </div>
	          <div class="flex_between">
	            <div class="flex_around editor_address" @click="editor_address(val.id)">
	              <img src="{{ config('app.source_url') }}shop/images/fankui@2x.png" alt="">
	              <span>编辑</span>
	            </div>
	            <div class="flex_around del_address" @click="close_androidBounces_open(index,val.id)">
	              <img src="{{ config('app.source_url') }}shop/images/del.png" alt="">
	              <span>删除</span>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    <!--安卓删除弹框-->
	    <div class="android_bounces" v-if="isShowAndroidBounces">
	    	<div class="mask" @click="close_androidBounces_open"></div>
	    	<div class="mask_content">
	    		<div>确定要删除地址吗?</div>
	    		<div>
	    			<span @click="close_androidBounces_open">取消</span>
	    			<span @click="onConfirm">确定</span>
	    		</div>
	    	</div>
	    </div>
	    <!--底部地址添加-->
	    <div class="footer_add_address flex_around" style='padding: 10px 0;background-color: #f5f5f5;bottom: 0 '>
	      <div @click="newAddress">
			  <span style="display: inline-block;width: 150px;height: 40px;background: #F72F37;border-radius: 4px;text-align: center;line-height: 40px;color: #fff;">＋手动添加</span>
	      </div>
			@if($reqFrom != 'aliapp')
	      <div class="hehe">
	        <span style="display: inline-block;width: 150px;height: 40px;background: #5cb85c;border-radius: 4px;text-align: center;line-height: 40px;color: #fff;">＋微信地址</span>
	      </div>
        @endif
	    </div>
  	</div>
@include('shop.common.footer')    
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var _token = $('meta[name="csrf-token"]').attr("content");
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script src="{{ config('app.source_url') }}shop/js/showAddress.js"></script>
<script type="text/javascript">
	var url = location.href.split('#').toString();
    $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
        if(data.errCode == 0){
            wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: data.data.appId, // 必填，公众号的唯一标识
                timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                signature: data.data.signature,// 必填，签名，见附录1
                jsApiList: [
                    'checkJsApi',
                    'openAddress'
                ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });
            
        }
    })
    wx.ready(function () {
     	$('.hehe').click(function(){
     		wx.openAddress({
                trigger: function (res) {
                	//tool.tip('用户开始拉出地址');
                },
                success: function (res) {
	                var rs = {};
					rs.province   = res.provinceName;
					rs.city       = res.cityName;
					rs.area       = res.countryName;
					rs.detail     = res.detailInfo;
					rs.userName   = res.userName;
					rs.telNumber  = res.telNumber;
					rs.postalCode = res.postalCode;
	                $.get('/shop/member/wechat/addressAdd',rs,function(obj){
	                	if (obj.errCode == 0) {
	                		window.location.reload();
	                	}else{
	                		tool.tip(obj.errMsg);
	                	}
	                });
                },
              	cancel: function (res) {
            		//tool.tip('用户取消拉出地址');
              	},
              	fail: function (res) {
                	//alert(JSON.stringify(res));
              	}
            });
     	})
    });
</script>
@endsection