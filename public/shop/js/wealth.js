
var app = new Vue({
	el:'#app',
	data:{
		list1:[],
		wid:wid,
		status:true,        //导航置顶开关
		pageStatus:true,   //滚动加载开关
		show_share:false,    //分享卡片
		page:1,
		nav_index:0,  		//悬浮导航下标
		nav_list:['分销商品','我的团队','订单明细','收支明细'],
		memberData:[],      //我的团队本级信息
		sonMember:[],		//我的团队下级信息
		balance_index:0,	//收支明细导航下标
		balance_list:['收益记录','提现记录'],
		moreHint     : "加载中......",	//加载更多提示
		sort_desc:1,
		productList:[],   //分销商品列表
		incomeLog:[],		//收入明细
		cashLog:[],			//提现明细	
		distributeOrder:[], 	//分销商品订单
		withdraw_type:['','银行卡','支付宝','微信'],
		withdraw_status:['等待同意', '同意提现', '确认已打款', '提现被拒'],
		order_status:['待付款','待发货','已发货','已完成','已关闭','待抽奖'],
		show_more:false,//更多规格价格弹窗
		moreIndex:0,//更多规格价格弹窗index
		distribute_amount:0,
		ismore:true,
		order:'buy_num',     //我的团队累计购次
		orderBy:'asc',    // 我的团队累计购次排序    desc降序   asc升序
		show_rule:false,
		grade:grade,  //分销等级
		show_son:false,   // 二级用户弹窗
		secSon:[], //二级用户列表
		showMoreBtn:false,
		secPage:1,
		sec_son_id:0
	},
	methods:{
		// 导航切换
		nav_tab:function(index){
			document.body.scrollTop = document.documentElement.scrollTop = 0;
			this.nav_index = index;
			this.page = 1;
			this.pageStatus = true;
			this.moreHint = '加载中......';
			switch(index){
				case 0 : 
					this.productList = [];
					distribute(this);
					break;
				case 1 : 
					this.sonMember = [];
					myTeam(this);
					break;
				case 2 : 
					this.distributeOrder = [];
					orderDetail(this);
					break;
				case 3 : 
					incomeDetail(this);
					break;
			}
		},
		// 收支明细tab切换
		balanceClick:function(index){
			this.balance_index = index
			if(index==1){
				cashLog(this);
			}
		},
		// 我的团队累计购次排序
		sortNum:function(){
			this.page = 1;
			this.sonMember = [];
			if(this.sort_desc == 1){
				this.sort_desc = 0;
				this.orderBy  = 'desc';
			}else{
				this.sort_desc = 1;
				this.orderBy = 'asc';
				
			}
			myTeam(app);
		},
		// 二级用户弹窗列表排序
		sortSecNum(){
			var that = this;
			this.secPage = 1;
			this.secSon = [];
			if(this.orderBy == 'asc'){
				this.orderBy = 'desc'
			}else{
				this.orderBy = 'asc'
			}
			$.ajax({
				url:'/shop/distribute/myTeam',
				type:'POST',
				data:{
					page:this.secPage,
					order:this.order,
					orderBy:this.orderBy,
					pid:this.sec_son_id
				},
				dataType:'json',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success:function(res){
					console.log(res)
					if(res.status == 1){
						hstool.closeLoad();
						for(var i=0;i<res.data[0].data.length;i++){
							that.secSon.push(res.data[0].data[i]);
						}
					}
				}
			})
		},
		// 跳转订单详情
		good_detail:function(wid,id){
			location.href = "/shop/product/detail/"+ wid +'/'+id;
		},
		shareCard:function(data){
			this.show_share = true;
			this.list1 = data;
			wxShare();
		},
		closeShare:function(){
			this.show_share = false;
		},
		/*
		 * add by 韩瑜
		 * date 2018-10-30
		 * 点击更多规格
		 */
		more_btn:function(index){
			var that = this
			that.show_more = true;
			that.moreIndex = index
			for(var i = 0; i < that.productList[index].skuData.length; i++){
				that.productList[index].skuData[i].distribute_amount = (+that.productList[index].skuData[i].distribute_amount).toFixed(2);
				that.productList[index].skuData[i].distribute_amount_sec = (+that.productList[index].skuData[i].distribute_amount_sec).toFixed(2);
			}
			if(that.productList[index].skuData.length < 6){
				that.ismore = false
			}else {
				that.ismore = true
			}
			$('body').addClass('ofhd')
		},
		close_more_btn:function(){
			this.show_more = false;
			$('body').removeClass('ofhd')
		},
		// 点击查看分销等级规则
		showRule(){
			if(this.grade.grade.length != 0){
				this.show_rule = true
			}	
		},
		closeRule(){
			this.show_rule = false
		},
		// 点击查看二级用户
		showSon(id){
			var that = this;
			this.show_son = true;
			this.secPage = 1;
			this.sec_son_id = id;
			this.orderBy = 'asc'
			hstool.load();
			$.ajax({
				url:'/shop/distribute/myTeam',
				type:'POST',
				data:{
					page:1,
					order:this.order,
					orderBy:this.orderBy,
					pid:id
				},
				dataType:'json',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success:function(res){
					console.log(res)
					if(res.status == 1){
						hstool.closeLoad();
						that.secSon = [];
						for(var i=0;i<res.data[0].data.length;i++){
							that.secSon.push(res.data[0].data[i]);
						}
						if(res.data[0].last_page != 1){
							showMoreBtn = true
						}
					}
				}
			})
		},
		// 关闭二级用户弹窗
		close_sec_btn(){
			this.show_son = false;
			this.orderBy = 'asc';
		},
		// 二级用户弹窗加载更多
		moreClick(){
			this.secPage++;
			console.log(this.secPage)
			$.ajax({
				url:'/shop/distribute/myTeam',
				type:'POST',
				data:{
					page:this.secPage,
					order:this.order,
					orderBy:this.orderBy,
					pid:this.sec_son_id
				},
				dataType:'json',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success:function(res){
					console.log(res)
					if(res.status == 1){
						hstool.closeLoad();
						for(var i=0;i<res.data[0].data.length;i++){
							that.secSon.push(res.data[0].data[i]);
						}
						if(res.data[0].last_page == this.secPage){
							showMoreBtn = false
						}
					}
				}
			})
		}
	},
	created:function(){
		distribute(this,1);

	}
})
// tab标签滚动悬浮置顶
$(window).scroll(function(){
	var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
	var sH = document.documentElement.clientHeight;
	app.status = scrollTop>=$(".withdrawal").height() ? false : true;
	if(scrollTop + sH  >= document.body.scrollHeight && scrollTop>$(".withdrawal").height() && app.pageStatus){
		app.pageStatus = false;
		app.page++;
		switch(app.nav_index){
			case 0 :
				distribute(app);
				break;
			case 1 :
				myTeam(app);
				break;
			case 2 :
				orderDetail(app);
				break;
			case 3 :
				// distribute();
				break;

		}
	}
})
// 获取我的团队数据
function myTeam(that){
	hstool.load();
	$.ajax({
		url:'/shop/distribute/myTeam',
		type:'POST',
		data:{
			page:that.page,
			order:that.order,
			orderBy:that.orderBy
		},
		dataType:'json',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			if(res.status == 1){
				hstool.closeLoad();
				that.memberData = res.data.memberData;
				// that.sonMember = res.data.sonMenberData[0].data;
				if(res.data.sonMenberData[0].data.length == 0){
					that.moreHint="无更多数据";
				}else{
					that.pageStatus = true;
					for(var i=0;i<res.data.sonMenberData[0].data.length;i++){
						that.sonMember.push(res.data.sonMenberData[0].data[i]);
					}
				}
			}
		}
	})
}
// 获取分销商品列表
function distribute(that){
	hstool.load();
	$.ajax({
		url:'/shop/distribute/productList',
		type:'get',
		data:{
			page:that.page
		},
		dataType:'json',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			if(res.status == 1){
				hstool.closeLoad();
				if(res.data[0].data.length == 0){
					that.moreHint="无更多数据";
				}else{
					that.pageStatus = true;
				}
				for(var i=0;i<res.data[0].data.length;i++){
					that.productList.push(res.data[0].data[i]);
				}
				that.list1 = that.productList[0]
			}
		}
	})
}

// 获取订单明细
function orderDetail(that,page){
	hstool.load();
	$.ajax({
		url:'/shop/distribute/distributeOrder',
		type:'GET',
		data:{
			page:that.page
		},
		dataType:'json',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			if(res.status == 1){
				hstool.closeLoad();
				if(res.data.length == 0){
					that.moreHint="无更多数据";
				}else{
					that.pageStatus = true;
					for(var i=0;i<res.data.length;i++){
						that.distributeOrder.push(res.data[i]);
					}
				}
				
			}
			console.log(that.distributeOrder)
		}
	})
}
// 收益明细
function incomeDetail(that){
	hstool.load();
	$.ajax({
		url:'/shop/distribute/incomeLog',
		type:'GET',
		dataType:'json',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			if(res.status == 1){
				hstool.closeLoad();
				that.incomeLog = res.data;
			}
		}
	})
}
// 提现明细
function cashLog(that){
	hstool.load();
	$.ajax({
		url:'/shop/distribute/cashLog',
		type:'GET',
		dataType:'json',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			if(res.status == 1){
				hstool.closeLoad();
				that.cashLog = res.data
			}
		}
	})
}
