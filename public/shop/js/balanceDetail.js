'use strict'
/*设置请求头部*/ 
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;


var app = new Vue({
	el:"#app",
	data:{
		page:1, //分页
		balanceType:0, //余额类型 0全部 1.收入 2.支出 
		list:{}, //余额类型
		postText:"正在加载数据请稍后...",
		isRequest:1, //加载帖子数据状态 0.数据请求中 1.数据请求中 2.无数据了
	},
	beforeCreate:function(){ 
	},
	created:function(){ 
		this.getList();
	},
	watch:{ //监听数据发生变化触发函数
		
	},
	methods:{ 
		/*获取数据*/
		getList:function(isPush){ 
			var that = this;
			if(typeof isPush ==="undefined"){
				hstool.load();
				that.list = {};
			}
			var url = "/shop/member/balanceDetailAjax";
			var data = { 
				page:that.page,
				type:that.balanceType 
			}; 
			that.isRequest = 0; 
			that.postText = "正在加载数据请稍后...";
			
			this.$http.get(url,{params:data}).then(
			function(res){ 
				if(res.body.status==1){
					//微信头像和本地头像需要做判断 
					var list = res.body.data; 
					if(typeof isPush ==="undefined")
						that.list = list;
					if(list.length>0){
						that.isRequest = 1; //数据请求成功
						that.postText = ""; 
						if(typeof isPush !=="undefined"){
							for(var i=0;i<list.length;i++){
								that.list.push(list[i]);
							}
						} 
					}else{
						that.isRequest = 2;  
						if(typeof isPush ==="undefined")
							that.postText = "已加载完毕了";
						else
							that.postText = "没有更多数据了";
					}
				} 
				if(typeof isPush ==="undefined")
					hstool.closeLoad();  
			},function(err){
				that.isRequest = 2; //请求失败了等于没有更多数据了
				if(typeof isPush ==="undefined")
					hstool.closeLoad();   
			});
		},
		setNav:function(index){
			var that = this;
			that.balanceType = index;
			that.page = 1;
			that.getList();
		}
	}, 
	updated:function(){
		 
	}
}); 

$(window).scroll(function(){
	var scrollTop = $(this).scrollTop();    //滚动条距离顶部的高度
    var scrollHeight = $(document).height();   //当前页面的总高度
    var clientHeight = $(this).height();    //当前可视的页面高度 
    if(app.isRequest==1){ //上一次请求成功
    	if(scrollTop + clientHeight >= scrollHeight){ 
	    	app.page +=1;
			app.getList(true);  
	    } 
    } 
}); 
