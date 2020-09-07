Vue.http.options.emulateJSON = true;
var app = new Vue({
	el:"#app",
	data:{
		imgUrl:imgUrl,
		phone       : ""     ,   		       //家长电话
		name        : ""		,			  //宝贝姓名
		sex		    : ""     ,               //宝贝性别	
		parent_name : ""    ,                //家长姓名
		images      : ""     ,			     //上传图片
		hint        : ""		,		 	//提示信息
		isShowHint  : false,				    //是否展示提示信息
		isShowRules  : false	,			    //是否展示提示信息
		isShowVoteSuccess  : false,			//是否展示投票成功弹框
		isShowVoteSuccess_s:false,         //是否展示弹框
		info:null,
		id : "" ,
		img: "",
		List:[]
	},
	mounted: function () {
		this.$nextTick(function () {
		});
	},
	created:function(){ 
		this.getInfo();
		
	},
	methods:{ 
		agreeComfrim:function(){
			var that = this;
			hstool.load();
			that.$http.post('/shop/vote/enroll',{
				_token : _token,
				id	 : id,
				name : this.name,
				sex : this.sex,
				contact : this.parent_name,
				phone : this.phone,
				img : this.images
			}).then(function(res){
				hstool.closeLoad();
				if (res.body.status == 1) {
					$('#app').addClass('enrollT');
					$('#app').removeClass('enroll');
					this.img = res.body.data.imgUrl;
					this.isShowVoteSuccess = !this.isShowVoteSuccess
					this.info = res.body.data;
					
				}else{
					tool.tip(res.body.info);
				}
 			}) 
		},
		closeImg:function(){
			this.isShowVoteSuccess = false;
			$('#app').addClass('enroll');
			$('#app').removeClass('enrollT');
		},
		closeImg_s:function(){
			this.isShowVoteSuccess_s = false;
			$('#app').addClass('enroll');
			$('#app').removeClass('enrollT');
		},
		file:function(e){
			var file = e.target.files[0];
			var fD = new FormData();
            var that = this;
            fD.append('file', file);
            fD.append('token', _token);
            var http = new XMLHttpRequest();
            hstool.load();
            http.onreadystatechange = function(){
                if(http.readyState == 4){
                    if(http.status >= 200 && http.status <300 || http.status == 304){
                        d = JSON.parse(http.response);
                        hstool.closeLoad();
			 			that.images= imgUrl+""+d.data.path;
                    }
                }
            };
            http.open('post', '/shop/order/upfile/'+wid);
            http.send(fD);
		},
		//插件图片上传
		imgUploader:function(){  
		},
		show_rules:function(){
			this.isShowRules = !this.isShowRules
		},
		vote_ticket:function(){
			// this.isShowVoteSuccess = !this.isShowVoteSuccess
		},
		getInfo:function(){
			this.$http.get(host+'shop/vote/getEnrollData/'+ id).then(function(res){
			
				if(res.body.head_img){
					this.List = res.body;
					this.images=res.body.head_img
					this.name=res.body.name
					this.sex=res.body.sex
					this.parent_name=res.body.contact
					this.phone=res.body.phone
					
					$('.agreett').css('display','none')
					$('.agreet').css('display','block')
				}
			
			})
		},
		agreeComfrimt:function(){
			this.isShowVoteSuccess_s = !this.isShowVoteSuccess_s
		},
		vote_tickett:function(){
			this.isShowVoteSuccess_s = !this.isShowVoteSuccess_s
		}
		
		
	},
	
}); 


