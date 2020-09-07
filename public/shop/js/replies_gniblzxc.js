'use strict';//严格模式 
/*设置请求头部*/ 
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;
//post要求的请求token
Vue.http.options.headers = {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")}; 

var app = new Vue({
	el:"#app",
	data : {
		pid:pid,
		rid:rid,
		content:"",
		disabled:false,
		maxLength:140,
		submitText:"确认提交"
	},
	created: function(){
		 
	},
	watch:{ //监听数据发生变化触发函数
		content:function(val,oldval){ 
			this.maxLength = 140 - val.length;
		}
	},
	methods:{
		submitForm:function(){
			var that = this;
			that.disabled = true;
			that.submitText = "数据提交,请稍后...";
			var url ="/shop/microforum/post/repliesed";
			var data={
				pid:that.pid,
				rid:that.rid,
				content:that.content
			}; 
			if(that.content==""){
				that.submitText = "确认提交";
				that.disabled = false;
				tool.tip("请输入回复内容");
				return;
			}
			that.$http.post(url,data).then(function(res){
				that.submitText = "确认提交";
				that.disabled = false;
				if(res.data.status==1){ 
					tool.tip(res.data.info);
					setTimeout(function(){
						location.href="/shop/microforum/post/detail/"+that.pid;
					},1000);
				}else{
					tool.tip(res.data.info);
				}
			},function(err){
				that.submitText = "确认提交";
				that.disabled = false;
				console.log(err);
			});  
		} 
	}
});