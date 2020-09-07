var app = new Vue({
	el: "#app",
	data: {
		listTop: {},
		listBot: {},
		page         : 1,				//分页
		res_data     : [],				//分页请求的数据
		res_toggle   : true,			//请求数据开关
		moreHint     : "加载中......",	//加载更多提示
		classifyName :"",
		pageHeight: null, //页面的高度
		imgHeight: null, //拿到图票的高度
		previewImg: null,
		previewShow: false,
	},
	created: function(){
		var url1 = "/shop/web/groups/getEvaluateClassify/" + pid;
		this.$http.get(url1).then(function(res){
			this.listTop = res.body.data;
		})
		var url2 = "/shop/web/groups/getProductEvaluate/" + pid;
		hstool.load();
		this.$http.get(url2).then(function(res){
			hstool.closeLoad();
			this.listBot = res.body.data;
		})
	},
	methods: {
		selTyype: function(catTop,catIndex){
			var url2 = "/shop/web/groups/getProductEvaluate/" + pid;
			var name = "";
			if(catTop.name != '全部'){
				name = catTop.name;
			}
			hstool.load();
			this.$http.get(url2,{
				params: {
					page: 1,
					classifyName: name
				}
			}).then(function(res){
				hstool.closeLoad();
				this.listBot = res.body.data;
				this.classifyName = catTop.name;
				this.page = 1;
			})
		},
		//预览图片
		lookImg: function(src) {
			//this.$router.push({path: "/preview_picture", query:{imgSrc: src}})
			this.previewShow = true;
			this.previewImg = src;
			var that = this;
			that.pageHeight = window.screen.availHeight;
			//解决安卓手机上图片的位置错位问题
			setTimeout(function() {
				that.$nextTick(function() {
					that.imgHeight = that.$refs.img.height;
				})
			}, 50)
		},
		//隐藏预览
		previewHide: function() {
			this.previewShow = false;
		},
	}
})

downUpload("GET", "/shop/web/groups/getProductEvaluate/"+pid, {page: app.$data.page,classifyName: app.$data.classifyName});
//-----------------------------------------------------------------------------------------
function downUpload(resStyle,url,resData){
	window.onscroll=function(){
		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var sH = document.documentElement.clientHeight;
		if(scrollTop + sH +50 >= document.body.scrollHeight && scrollTop > 100){
			if (app.$data.res_toggle == true) {
				app.$data.res_toggle = false;
				hstool.load();
				app.$data.page++;
				if(resStyle=="post"||resStyle=="POST"){
					resData.page = app.$data.page;			//page分页
					$.post(url, resData, function(res){
						var list = res.data;
				  		//若全部加载完成，加载动画消失
				  		if(list.length==0){
				  			app.$data.moreHint = "无更多数据"
				  		}
				  		hstool.closeLoad();
//				  		app.judgeStatus(list);
				  		for (var i=0; i<list.length; i++) {
				  			app.$data.listBot.push(list[i])
				  		}
				  		app.$data.res_toggle = true
					})
				}else{
					resData.page = app.$data.page;
					$.get(url, resData,function(res){
	  					var list = res.data;
				  		//若全部加载完成，加载动画消失
				  		hstool.closeLoad();
				  		if(list.length==0){
				  			app.$data.moreHint = "无更多数据"
				  			return false;
				  		}
//				  		app.judgeStatus(list);
				  		for (var i=0; i<list.length; i++) {
				  			app.$data.listBot.push(list[i])
				  		}
				  		app.$data.res_toggle = true
					})
				}
			}
		}
	}
}
