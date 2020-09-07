'use strict';//严格模式 
var fileobj;
$(function(){
	//上传图片按钮点击事件  update by 黄新琴
	$(".js-img-file").click(function(){
		fileobj.config.maxnum = 9 - $(".js-upload-img").length;
		$("#img_file").trigger("click");
	}); 

	
	var maxnum = 9 - $(".js-upload-img").length;
	var t_config = {
		el:document.getElementById("img_file"), 
		_token: $('meta[name="csrf-token"]').attr('content'), 
		maxnum: maxnum,//最大上传图片数
		wid: wid,
		done:function(res){
			if(res.status == 1){
				console.log(res);
				var base64 =res.base64,
					html ='<div class="iblock pr img-box"><img class="js-upload-img"  src="'+base64+'" /><span class="img-close"></span></div>  ';
				$("#add_img_box").before(html);
				
			}else{
				alert(res.msg);
			}
			//console.log(res);
		}
	}; 
	fileobj = new tAjaxFile(t_config);
	

	// 删除图片按钮点击事件
	$("body").on("click",".img-close",function(){  
		var data = fileobj.getUploadInfo();
		var index = $(this).parent().index();  
		console.log(index);
		if(data.status==1){
			fileobj.resultData.splice(index,1);
			$(this).parent().remove();
		}else{
			tool.tip("稍等一会儿!");
		}
		
	});
}); 

/*设置请求头部*/ 
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;
//post要求的请求token
Vue.http.options.headers = {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")}; 

var app = new Vue({
	el:"#app",
	data : {
		img_wrapper : true,
		emoji_wrapper : false,
		discussions_id : categoriesDatas[0].id,
		title:"",
		content:"",
		imgids:"",
		items:categoriesDatas,
		submitText:"立即发布",
		disabled:false,
		maxLength:500,
	},
	created: function(){ 
	},
	watch:{ //监听数据发生变化触发函数
		content:function(val,oldval){ 
			this.maxLength = 500 - val.length;
		}
	},
	methods:{
		/*切换标签或图片上传*/
		switchWrapper:function(index){  
			if(index==1){
				this.img_wrapper = true;
				this.emoji_wrapper = false; 
			}else{
				this.img_wrapper = false;
				this.emoji_wrapper = true;
			} 
		},
		/*发帖表情按钮点击事件*/
		selectPostType:function(id){ 
			this.discussions_id = id; 
		},
		/*提交表单*/
		submitForm:function(obj){   
			var that = this;
			that.disabled = true;
			that.submitText = "数据提交,请稍后...";
			var data = fileobj.getUploadInfo(); 
			if(that.title==""){
				that.submitText = "立即发布";
				that.disabled = false;
				tool.tip("请输入标题!");
				return;
			} 
			if(data.status==1){  
				//图片上传完了 提交数据
				data = data.data;
				for(var i=0;i<data.length;i++){
					that.imgids+=data[i].id+',';
				}
				if(that.imgids!="")
					that.imgids = that.imgids.substr(0,that.imgids.length-1);
				if(that.content=="" && that.imgids ==""){
					that.submitText = "立即发布";
					that.disabled = false;
					tool.tip("请输入文字或上传照片!");
					return;
				}
				var url = "/shop/microforum/post/released";
				var data = {
					discussions_id:that.discussions_id,
					title:that.title,
					content:that.content,
					imgids:that.imgids 
				}; 
				that.$http.post(url,data).then(function(res){
					that.submitText = "立即发布";
					that.disabled = false;
					if(res.data.status==1){ 
						tool.tip("发布成功");
						setTimeout(function(){
							location.href="/shop/microforum/post/owner";
						},1000);
					}else{
						tool.tip(res.data.info);
					}
				},function(err){
					that.submitText = "立即发布";
					that.disabled = false;
					console.log(err);
				});  
			}else{
				setTimeout(function(){
					that.submitForm(obj);
				},100);
			}
		} 
	}
});


