var vm = new Vue({
	el:".content",
	delimiters: ['[[', ']]'],
	data: {
		join_info:{},//参与次数
		prize_list:[],//奖品列表
		start_at:"",
		end_at:"",
		detail: "",//活动说明
		win_list: "",//中奖列表
		is_show: "",//中奖列表是否显示
		share_json: "",//微信分享
		isBtnClick: false,//是否点击按钮
	},
	created:function(){
		this.$http.get("/shop/activity/eggDetail/"+wid+"/"+activityId).then(function(res){
			var res = res.body;
			this.share_json = res.data.share_json;
			
			if(res.status == 1){
				this.join_info = res.data.join_info;
				this.prize_list = res.data.prize_info;
				this.start_at = res.data.start_at;
				this.end_at = res.data.end_at;
				this.detail = res.data.detail;
				this.is_show = res.data.is_show;
			}else{
                tool.hitEgg({
                    type:0,
                    content: res.data.msg?res.data.msg:"活动已结束，下次早点来哦",
                    sureTitle: "知道了",
                    sureBtn:function(){
                        window.history.go(-1);
                    }
                });
			}
		});
		
	},
})

$(function(){
	
	$(".plane").click(function(){
		if(vm.isBtnClick){//已经点击  退出
			return false;
		}
		vm.isBtnClick = true;
		//判断是否绑定手机号
		var _this = $(this);
        // if(isBind){
        //     tool.bingMobile(function(){
        //     	isBind = 0;
        //         hint(_this);
        //     })
        //     vm.isBtnClick = false;
        //     return;
        // }
        hint(_this);
	});
	function hint(_this){
		if(vm.join_info.left_amount == 0){
			vm.isBtnClick = false;
			tool.hitEgg({
				type:0,
			    content: "亲，您的砸蛋次数已用完，随便逛逛吧~",
			    sureTitle: "知道了",
			    sureBtn:function(){
			    	$(".hitImage").removeClass("hitImage");
					$(".hit").addClass("hide");
			    }
			});
			return false; 
		}
		$(".hit").removeClass("hide").css({
			left: _this.offset().left + 15,
			top: _this.offset().top - 150
		});
		_this.children().addClass("hitImage");
		//中奖请求
		function prizeGet(){
			$.get("/shop/activity/egg/"+wid+"/"+activityId,function(res){
				vm.isBtnClick = false;
				if(res.status == 1){
					switch (res.data.status)
					{	
						case 0://0：活动结束
							tool.hitEgg({
								type:0,
							    content: "很遗憾，本次砸金蛋活动已结束，欢迎下次再来哦",
							    sureTitle: "我知道了",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							}) 
						break;
						case 1://1：中奖
							var data = res.data.data;
							if(data.type == 1){//优惠券
								var _html = "<p>恭喜您获得了奖品<span style='color:#fb2e3d;'>"+data.name+"</span>";
							}
							else if(data.type == 2){//积分
								var _html = "<p>恭喜您获得了奖品<span style='color:#fb2e3d;'>"+data.name+"</span>";
							}//赠品
							else{
								var _html = "<p>恭喜您获得了奖品<span style='color:#fb2e3d;'>"+data.name+"</span>";
							}
							tool.hitEgg({
								type:0,
							    content: _html,
							    sureTitle: "再砸一次",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							})
							vm.join_info.left_amount --; 
						break;
						case 2://2：活动未开始
							tool.hitEgg({
								type:0,
							    content: "您来的太早啦，活动还没开始呢！",
							    sureTitle: "我知道了",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							}) 
						break;
						case 3://3：今天的中奖次数已经用完
							tool.hitEgg({
								type:0,
							    content: "今天的中奖次数已经用完，请明天再来",
							    sureTitle: "我知道了",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							}) 
						break;
						case 4://4：本次活动中奖次数已经用完
							tool.hitEgg({
								type:0,
							    content: "您的抽奖次数已用完",
							    sureTitle: "我知道了",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							})
						break;
						case 5://5：插入记录信息失败 (后台出错)
							tool.hitEgg({
								type:0,
							    content: "未知错误",
							    sureTitle: "我知道了",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							}) 
						break;
						case 8://8：未中奖
							tool.hitEgg({
								type:0,
							    content: '大奖与您擦肩而过，再接再厉哦！',
							    sureTitle: "再砸一次",
							    sureBtn:function(){
							    	$(".hitImage").removeClass("hitImage");
									$(".hit").addClass("hide");
							    }
							})
							vm.join_info.left_amount --; 
						break;
					}
					
					
				}
			})
		}
		prizeGet();
	}
	$('.myPrize').click(function(){
		location.href = '/shop/activity/eggPrizeList/'+ wid
	})
});