Vue.http.options.emulateJSON = true;
var app = new Vue({
	el:"#app",
	data:{
		imgUrl:imgUrl,
		isShowRules  : false	,			    //是否展示提示信息
		isShowVoteSuccess  : false,				//是否展示投票成功弹框
		Heigth : ""   ,							//图片高度
		id : "" ,								//宝贝号码
		captcha	: "" ,							//验证码
		days  : "00",
		hours : "00",
		minutes : "00",
		color_type : "1",
		orderBy	   : "",
		memberList:[],                          //萌宝列表
		orderby:"created_at",                   //萌宝列表参数
		page:1, 								//萌宝页数
		request:true,                           //上拉加载
		noMore:false,                           //没有更多
		keyword:"",                             //搜索
		search:"",                              //搜索
		info:null,								//投票信息
		img:''
	},
	
	created:function(){ 
		var that = this
		this.Heigth = (window.screen.width/2) - 10
		//单转双
        function evenNum(num){
           num = num < 10 ? "0" + num : num;
           return num;
        }
        //倒计时
        setTime(time);
		function setTime(time){
			//天数计算
	    	that.days = evenNum(Math.floor((time)/(24*3600)));
		    //小时计算
		    that.hours = evenNum(Math.floor((time)%(24*3600)/3600));
		    //分钟计算
		    that.minutes = evenNum(Math.floor((time)%3600/60));
		    time = time - 1;
		    if(time < 0){
		    	tool.notice(0,"提示","本次活动已结束，请下次再来","我知道了");
		    	return false;
		    }
		    setTimeout(function(){
		    	setTime(time);
		    },1000)
		}
		//获取萌宝列表
	    getMmeberList(that);
	    //上拉加载
	    getScroll(that);
	},
	methods:{ 
		show_rules:function(){
			this.isShowRules = !this.isShowRules
		},
		hide_ticket:function(){
			this.isShowVoteSuccess = !this.isShowVoteSuccess;
		},
		closeImg:function(){
			this.isShowVoteSuccess = false;
			$('#app').addClass('baby_index');
			$('#app').removeClass('baby_indexT');
		},
		//点击投票
		vote_ticket:function(){
			if(!this.id){
				tool.tip("请填写萌宝号码");
				return false;
			}
			var that = this;
			hstool.load();
			that.$http.post('/shop/vote/index/'+wid+'/'+id,{
				_token : _token,
				id	 : this.id
				/*captcha : this.captcha,*/
			}).then(function(res){
				hstool.closeLoad();
				$("#captcha_img").click();				
				if(parseInt(res.body.status)){
					this.img = res.body.data.imgUrl;
					if (res.body.status == 1) {
						$('#app').addClass('baby_indexT');
						$('#app').removeClass('baby_index');
						this.isShowVoteSuccess = !this.isShowVoteSuccess;
						this.info = res.body.data;
					}else{
						tool.tip(res.body.info);
					}
				}else{
					tool.tip(res.body.info);
				}
					
 			}) 
			
		},
		get_vote_num:function(baby_id){
			this.id = baby_id;

		},
		//最近参赛
		recent_entry:function(){
			this.search = "";
			this.keyword = "";
			this.orderby = 'created_at';
			this.color_type = 1
			this.page = 1;
			getMmeberList(this);
		},
		//投票排行
		vote_rank:function(){
			this.search = "";
			this.keyword = "";
			this.color_type = 2;
			this.orderby = 'vote_num';
			this.page = 1;
			getMmeberList(this);
		},
		//搜索
		enter:function(){
			this.keyword = this.search;
			this.page = 1;
			getMmeberList(this)
		},
		blur:function(){//ios 点击完成不触发 enter
			this.keyword = this.search;
			this.page = 1;
			getMmeberList(this)
		}
	},
	
}); 
//获取萌宝列表信息
function getMmeberList(that){
	//获取页面信息 列表
    that.$http.get(host+"shop/vote/getSearchList",{'params':{
    	orderby:that.orderby,
    	vote_id:id,
    	page: that.page,
    	keyword:that.keyword
    }}).then(function (res) {
    	that.request = true;
        if(res.body.status == 1){
        	var list = res.body.data.data;
        	if(that.page > 1){//上拉加载
        		list = that.memberList.concat(list);
        	}
        	//判断列表条数
        	if(res.body.data.data.length < res.body.data.per_page){
        		that.noMore = true;
        	}else{
        		that.noMore = false;
        	}
        	that.memberList = list;
        }else{
        	console.log("error:" + res.body.info);
        }
    });
}
//上拉加载
function getScroll(that){
	window.onscroll=function(){
		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var sH = document.documentElement.clientHeight;
		if(scrollTop + sH +50 >= document.body.scrollHeight && scrollTop > 100 && !that.noMore && that.request){
			that.request = false;
			that.page = that.page + 1;
			getMmeberList(that);
		}
	}
}