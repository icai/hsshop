@extends('shop.common.template')
@section('title', $title)
@section('head_css')
	<style type="text/css">
		.content.white{
			background-color:#F8F8F8;
			font-size:14px;
		}
		.info-wrap{
			margin-top:10px;
			background-color:#fff;
		}
		.input-wrap{
			display: flex;
			display: -webkit-flex;
			border-bottom:1px solid #eee;
			padding: 5px 0;
		}
		.input-wrap-title{
			display: inline-block;
			width:80px;
			line-height: 30px;
			padding: 5px 0 5px 10px; 
			box-sizing: border-box;
			text-align: left;
		}
		.input-wrap-span{
			display: inline-block; 
			padding:5px 10px;
			width: calc(100% - 80px); 
		}
		.input-wrap-txt{
			border:none;
			line-height: 30px;
			width:100%;
			padding: 0 5px;
			box-sizing: border-box;
		}
		/*提交样式*/
		.btn-wrap{
			text-align:center;
			padding: 30px 0;
		}
		.btn-submit{
			color:#fff;
			border:none;
			background-color:#169BD5;
			border-radius: 5px;
			padding: 10px 40px;
		}
	</style>
@endsection
@section('main')
	<div class="content white" id="app">
		<article class="info-wrap">
			<section class="input-wrap">
				<label class="input-wrap-title">名字</label>
				<span class="input-wrap-span">
					<input type="text" class="input-wrap-txt" v-model="ceoInfo.name" maxlength="10" placeholder="请输入名字" />
				</span>
			</section>
			<section class="input-wrap">
				<label class="input-wrap-title">联系方式</label>
				<span class="input-wrap-span">
					<input type="text" class="input-wrap-txt" v-model="ceoInfo.phone" maxlength="11" placeholder="请输入联系方式" />
				</span>
			</section>
			<section class="input-wrap">
				<label class="input-wrap-title">企业名称</label>
				<span class="input-wrap-span">
					<input type="text" class="input-wrap-txt" v-model="ceoInfo.enterprise_name" maxlength="20" placeholder="请输入企业名称" />
				</span>
			</section>
			<section class="input-wrap">
				<label class="input-wrap-title">职位</label>
				<span class="input-wrap-span">
					<input type="text" class="input-wrap-txt" v-model="ceoInfo.position" maxlength="10" placeholder="请输入职位" />
				</span>
			</section>
			<section class="btn-wrap">
				<button class="btn-submit" v-on:click="submit()" >提交</button>
			</section>
		</article> 
	</div>
	@include('shop.common.footer')
@endsection
@section('page_js')
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
	<script type="text/javascript"> 
		/*设置请求头部*/ 
		Vue.http.options.emulateHTTP = true;
		Vue.http.options.emulateJSON = true; 
		Vue.http.options.headers =  {'X-CSRF-TOKEN': "{{ csrf_token() }}"}; 
		var _url ="{{$_url}}";
		var app = new Vue({
			el:"#app",
			data:{ 
				ceoInfo:{
					name:'', //名称
					phone:'',  //联系方式
					enterprise_name:'',//企业名称
					position:''    //职位
				}
			},
			created:function(){  

			},
			methods:{
				//提交  
				submit:function(){
					var that = this;
					var url ="/shop/grouppurchase/ceoInfo";
					var data =that.ceoInfo;
					if(data.name==""){
						tool.tip("名称不能为空.");
						return;
					}
					if(data.phone==""){
						tool.tip("联系方式不能为空.");
						return;
					} 
					if(!tool.isPhone(data.phone)){
						tool.tip("手机格式不正确.");
						return;
					}
					if(data.enterprise_name==""){
						tool.tip("企业名称不能为空.");
						return;
					}
					if(data.position==""){
						tool.tip("职位不能为空.");
						return;
					}
					hstool.load(); 
					that.$http.post(url,data).then(function(res){
						console.log(res); 
						if(res.body.status==1){
							tool.tip(res.body.info);
							setTimeout(function(){
								location.href=_url;
							},1000); 
						}else{
							tool.tip(res.body.info);
						} 
						hstool.closeLoad();  
					},function(err){  
						console.log("异常");
						hstool.closeLoad();   
					}); 
				},

			}
		});
	</script>
@endsection