Vue.http.options.emulateJSON = true;
var app = new Vue({
	el: ".orderList",
	data: {
		list_data    : [],				//页面数据	
		no_data      : false,			//没有数据
		nav_bar      : [],								//导航栏
		nav_index    : 0,				//导航栏下标
		status       : "",				//订单状态
		page         : 1,				//分页
		res_data     : [],				//分页请求的数据
		res_toggle   : true,			//请求数据开关
		moreHint     : "加载中......",	//加载更多提示
		confirmShow  : false,			//confirm显示与否
		confirmTitle : "",				//confirm显示标题
		confirmType  : "",				//confirm区别
		wid          : "",
		oid          : "",
		no_express   : "",              //判断商品详情跳转
		toastText    : "",				//提示文字
		toastShow    : false,			//提示开关
		list_index   : "",				//点击的列表数据下标
	},
	mounted: function () {
  		this.$nextTick(function () {
    		
  		})
	},
	created:function(){
			this.nav_bar = reqFrom == 'aliapp' ? (
				takeAwayConfig == 1 ? [
                    {name: '全部', type: ''},
                    {name: '待付款', type: '0'},
                    {name: '待收货', type: '2'},
                ] : [
                    {name: '全部', type: ''},
                    {name: '待付款', type: '0'},
                    {name: '待发货', type: '1'},
                    {name: '待收货', type: '2'},
                ]
            ) : (
            	takeAwayConfig == 1 ? [
                    {name: '全部', type: ''},
                    {name: '待付款', type: '0'},
                    {name: '待成团', type: '-1'},
                    {name: '待收货', type: '2'},
                    {name: '待评价', type: '3'}
                ] : [
                    {name: '全部', type: ''},
                    {name: '待付款', type: '0'},
                    {name: '待成团', type: '-1'},
                    {name: '待发货', type: '1'},
                    {name: '待收货', type: '2'},
                    {name: '待评价', type: '3'}
                ]
			)
			this.status = this.GetRequest().status ? this.GetRequest().status : "";
			this.getData(this.status, this.page);
			//导航下标判断
			if(this.status==""){ this.nav_index = 0; }
			if(this.status== '0'){ this.nav_index = 1; }
			if(this.status=='-1'){ this.nav_index = 2; }
			if(this.status== '1'){ this.nav_index = 3; }
			if(this.status== '2'){ this.nav_index = 4; }
			if(this.status== '3'){ this.nav_index = 5; }
		},
	methods: {
		//导航点击
		onItemClick:function(type, index) {
			this.no_data = false;
			this.nav_index = index;
			this.status = type;
			this.page = 1;
			this.getData(this.status, this.page);
		},
		//订单详情
		order_detail:function(gid, id, wid) {
			if(gid==0){		//到之前的老详情页面
				location.href = "/shop/order/detail/"+id;
			}else{			//到新写的团详情页面
				location.href = "/shop/order/groupsOrderDetail/"+id;
			}
		},
		//取消订单
		cancle:function(wid,oid,index) {
			var that = this;
			that.confirmShow = true;
			that.confirmTitle = "确定取消订单？";
			that.wid = wid;
			that.oid = oid;
			that.list_index = index;
			that.confirmType = 0;
		},
		//立即支付
		pay:function(gid, id) {
			if(gid==0){					//到之前的老详情页面
				location.href = "/shop/order/detail/"+id;
			}else{						//到新写的团详情页面
				location.href = "/shop/order/groupsOrderDetail/"+id;
			}
		},
		//再次购买
		buyagain:function(gid, id, wid, rid) {
			if(gid==0){		//到之前的老详情页面
				location.href = "/shop/product/detail/"+wid+"/"+id;
			}else{			//到新写的团购商品页面
				location.href = "/shop/grouppurchase/detail/"+rid+"/"+wid;
			}
		},
		//邀请好友拼团
		invite:function(gid, wid, gtp) {
			location.href = "/shop/grouppurchase/groupon/"+gid+"/"+wid+"?group_type="+gtp;
		},
		delay:function(oid, wid) {
			var that = this;
			that.$http.post('/shop/order/receiveDelay/'+wid+'/'+oid, {
				_token: _token
			}).then(function(res){
				console.log(res)
				if(res.body.status==0){
					that.toastHint(res.body.info);
				}
			})
		},
		//查看物流
		logistics:function(id, wid, no_express, groups_id) {
			if(no_express  == 1){//无需物流
				if(groups_id == 0){//common 订单  groupdetail
					location.href = "/shop/order/detail/"+id;
				}else{
					location.href = "/shop/order/groupsOrderDetail/"+id;
				}
			}else {
				location.href = "/shop/order/expresslist/"+wid+"/"+id;
			}
		},
		//确认收货
		confirm:function(refundId, oid, wid, index) {
			var that = this;
			if(refundId != 0 && refundId != 4 && refundId != 8) {
				that.confirmTitle = "您正在申请退款中,确认收货将会关闭退款";
				that.confirmShow = true;
				that.confirmType = 1;
				that.list_index = index;
				that.wid = wid;
				that.oid = oid;
			} else {		//确认之后评价
				that.confirmShow = true;
				that.confirmTitle = "确认收货后，订单交易完成，钱款将立即到达商家账户。";
				that.confirmType = 1;
				that.list_index = index;
				that.wid = wid;
				that.oid = oid;
			}
		},
		//立即评价
		appraise:function(oid, wid) {
			location.href = "/shop/order/commentList/"+wid+"/"+oid
		},
		//更多
		more:function(gid, id) {
			if(gid==0){		//到之前的老详情页面
				location.href = "/shop/order/detail/"+id;
			}else{			//到新写的团详情页面
				location.href = "/shop/order/groupsOrderDetail/"+id;
			}
		},
		//confirm的确定
		confirmSure:function(){
			var that = this;
			that.confirmShow = false;
			if(that.confirmType==0){				//取消确定
				that.$http.get("/shop/order/cancle/"+that.wid+"/"+that.oid).then(function(res){
					console.log(res);
					if(res.body.status==1){
						//改变功能按钮
						if(that.status==""){			//全部列表的时候
							that.list_data[that.list_index].btn_cancle = 0;
							that.list_data[that.list_index].btn_pay = 0;
							that.list_data[that.list_index].btn_buyAgain = 1;
						}else if(that.status==0){		//待付款的时候
							that.list_data.splice(that.list_index, 1)
						}		
						
						that.toastHint("取消订单成功");
					}else if(res.body.status==0){
						that.toastHint(res.body.info);
					}
				})
			}else if(that.confirmType==1){			//确认收货确认
				that.$http.post('/shop/order/received/'+that.wid+'/'+that.oid,{
					_token : _token
				}).then(function(res){
					console.log(res)
					if(res.body.status==1){
						if(that.status==""){
							that.list_data[that.list_index].btn_delay     = 0;
							that.list_data[that.list_index].btn_logistics = 0;
							that.list_data[that.list_index].btn_confirm   = 0;
							that.list_data[that.list_index].btn_buyAgain  = 1;
							that.list_data[that.list_index].btn_appraise  = 1;
							that.list_data[that.list_index].btn_more      = 1;
							
						}else if(that.status==2){
							that.list_data.splice(that.list_index, 1);
						}
						that.toastHint(res.body.info);
						//跳评价
						location.href = "/shop/order/commentList/"+that.wid+"/"+that.oid
					}
				})
			}
		},
		//toast提示
		toastHint:function(text){
			var that = this;
			that.toastShow = true;
			that.toastText = text;
			setTimeout(function(){
				that.toastShow = false;
			}, 2000)
		},
		//请求数据
		getData:function(status, page){
			var that = this;
			//加载动画
			hstool.load();
			that.$http.post("/shop/order/index/"+wid,{
				debug: 1,
				status:status,
				page  : page,
				_token: _token
			}).then(function(res){
				console.log(res)
				if(res.status==200){
					//隐藏加载动画
					hstool.closeLoad();
					that.res_data = res.body.data;
					//---订单状态判断---
					that.judgeStatus(that.res_data)
					console.log(that.res_data)
					that.list_data = [];
					//---只有一页数据的情况---
					if(that.res_data.length<10){
						that.moreHint = "无更多数据"
					}
					for(var i=0; i<that.res_data.length; i++){
						that.list_data.push(that.res_data[i])
					}
					//没有数据的时候判断页面显示
					if(that.list_data.length==0){
						that.no_data = true;
					}
					console.log(that.list_data)
				}
			})
		},
		//订单状态判断
		judgeStatus:function(lists){
			for(var i=0; i<lists.length; i++){
				lists[i].btn_cancle    = 0;	//取消按钮
				lists[i].btn_pay       = 0;	//去支付按钮
				//lists[i].btn_devar     = 0;	//删除按钮
				lists[i].btn_buyAgain  = 0;	//再次购买按钮
				lists[i].btn_invite    = 0;	//邀请好友按钮
				lists[i].btn_delay     = 0;	//延长收货按钮
				lists[i].btn_logistics = 0;	//查看物流按钮
				lists[i].btn_confirm   = 0;	//确认收货按钮
				lists[i].btn_appraise  = 0;	//立即评价按钮
				lists[i].btn_more      = 0;	//更多按钮
				
				if (lists[i].status == 0){			//待付款
		            lists[i].statusName = "待支付";
		            lists[i].btn_cancle = 1;
		            lists[i].btn_pay = 1;
		        } else if (lists[i].status == 1) {	//待发货
		            if (lists[i].groups_status == 1) {
		              	lists[i].statusName = "拼团中";
		              	lists[i].btn_invite = 1;
		          	} else if (lists[i].groups_status == 2) {
		          		if (lists[i].is_open_draw == 1) {//开启抽奖活动   抽奖活动失败直接关闭订单  待发货，待收货，评论状态   中奖状态等级最高 
			                lists[i].statusName = "已中奖，待发货";
			                continue;
			            }
		              	lists[i].statusName = "已成团,待发货";
		            } else if (lists[i].is_hexiao == 1){
						lists[i].statusName = "买家已付款";
					}
		            else {
		              	lists[i].statusName = "待发货";
		            }
		        }  else if (lists[i].status == 2) {
		        	if (lists[i].is_open_draw == 1) {//开启抽奖活动 
		                lists[i].statusName = "已中奖，待收货";
		                lists[i].btn_delay = 1;
		              	lists[i].btn_logistics = 1;
		              	lists[i].btn_confirm = 1;
		                continue;
		            } else if (lists[i].is_hexiao == 1){
						lists[i].statusName = "买家已提货";
		              	lists[i].btn_confirm = 1;
					}
		            else {
		              	lists[i].statusName = "待收货";
		              	lists[i].btn_delay = 1;
		              	lists[i].btn_logistics = 1;
		              	lists[i].btn_confirm = 1;
		            }
		        } else if (lists[i].status == 3) {
		            if (lists[i].evaluate == 0) {
		            	if (lists[i].is_open_draw == 1) {//开启抽奖活动 
			                lists[i].statusName = "已中奖，待评价";
			                lists[i].btn_more = 1;
			              	lists[i].btn_buyAgain = 1;
			              	lists[i].btn_appraise = 1;
			            }else{
			              	lists[i].statusName = "待评价";
			              	lists[i].btn_more = 1;
			              	lists[i].btn_buyAgain = 1;
			              	lists[i].btn_appraise = 1;
			            }
		            } else if (lists[i].evaluate == 1) {
		            	if (lists[i].is_open_draw == 1) {//开启抽奖活动 
			                lists[i].statusName = "已中奖，已评价";
			            }else{
			              	 lists[i].statusName = "已评价";
			              	// lists[i].btn_devar = 1;
			              	 lists[i].btn_buyAgain = 1;
			            }
		            }
		        } else if (lists[i].status == 4){
		        	if(lists[i].is_open_draw == 1&& (lists[i].refund_status == 4 || lists[i].refund_status == 8)){
		        		lists[i].statusName = "未中奖,退款成功";
		        		continue;
		        	}
		            if (lists[i].groups_status == 3 && (lists[i].refund_status == 4 || lists[i].refund_status == 8)){
		              	lists[i].statusName = "未成团,退款成功";
		            }else{
		              	lists[i].statusName = "交易已取消";
		              	lists[i].btn_buyAgain = 1;
		            }
		        }else if (lists[i].status == 7) {
		            if (lists[i].is_open_draw == 1) {//开启抽奖活动   
		              lists[i].statusName = "已成团，待抽奖";
		            }
		        } 
				
			}
		},
		GetRequest:function() {
		    var url = location.search; //获取url中"?"符后的字串
		    var theRequest = new Object();
		    if (url.indexOf("?") != -1) {
		        var str = url.substr(1);
		        strs = str.split("&");
		        for(var i = 0; i < strs.length; i ++) {
		            theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
		        }
		    }
		    return theRequest;
		}
	}
});
var _data = {
	_token: _token,
	status: app.$data.status,
}
downUpload("POST", "/shop/order/index/"+wid, _data);
//筛选分类加载；
var lis = document.querySelectorAll(".tabNav span");
console.log(lis)
$.each(lis, function(index,ele){
	this.onclick = function(){
		app.$data.moreHint = "加载中......";
		//解决点击导航后列表不在顶部的问题
		document.body.scrollTop = document.documentElement.scrollTop = 0;
		var status = app.$data.status
		console.log(index, status)
		var _data = {
			_token: _token,
			status: status,
		}
		downUpload("POST", "/shop/order/index/"+wid, _data);
	}
})

//-----------------------------------------------------------------------------------------
function downUpload(resStyle,url,resData){
	window.onscroll=function(){
		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var sH = document.documentElement.clientHeight;
		if(scrollTop + sH +50 >= document.body.scrollHeight && scrollTop > 100){
			if (app.$data.res_toggle == true) {
				app.$data.res_toggle = false;
				app.$data.page++;
				if(resStyle=="post"||resStyle=="POST"){
					resData.page = app.$data.page;			//page分页
					$.post(url, resData, function(res){
						console.log(res)
						var list = res.data;
				  		//若全部加载完成，加载动画消失
				  		if(list.length==0){
				  			app.$data.moreHint = "无更多数据"
				  		}
				  		app.judgeStatus(list);
				  		for (var i=0; i<list.length; i++) {
				  			app.$data.list_data.push(list[i])
				  		}
				  		app.$data.res_toggle = true
					})
				}else{
					$.get(url, function(res){
	  					console.log(res)
	  					var list = res.data;
				  		//若全部加载完成，加载动画消失
				  		if(list.length==0){
				  			app.$data.moreHint = "无更多数据"
				  		}
				  		app.judgeStatus(list);
				  		for (var i=0; i<list.length; i++) {
				  			app.$data.list_data.push(list[i])
				  		}
				  		app.$data.res_toggle = true
					})
				}
			}
		}
	}
}