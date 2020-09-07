var vm = new Vue({
	el: "#app",
	delimiters: ['[[', ']]'],
	data:{
		host:host,//域名
  		imgUrl:imgUrl,//动态图片地址
  		wid:wid,//店铺id
  		pageShow: false,//页面展示
		nav_bar:[//nav信息
			{ status: "", title: "全部" },
		    { status: "1", title: "待成团" },
		    { status: "2", title: "已成团" },
		    { status: "3", title: "拼团失败" }
		],
		nav_index:0,//nav当前下标
		status:"",//列表状态
		groupList:[],//拼团列表
	    page:1,//页码数
	    noMore: false,//没有更多数据
	    share: null,//分享
	    shareShow:false,//分享蒙版显示
	},
	created:function(){
		getPage(this);
		//加载
		getScroll(this);
	},
	methods:{
		//切换nav
		navChange:function(status,index){
			this.nav_index = index;
			this.status = status;
			this.page = 1;
			getPage(this);
		},
		//邀请好友拼团
		getShare:function(item){
			this.share = item.shareData;
			this.shareShow = true;
			this.share.share_url = this.host + "shop/grouppurchase/groupon/" + item.id+"/"+this.wid+"?group_type=2"
			wxShare();
		},
		//分享隐藏
		shareHide:function(){
			this.shareShow = false;
		}
	}
})
//获取页面信息
function getPage(that){
	//获取拼团列表
    that.$http.get(that.host+"shop/web/groups/myGroups?status="+that.status+"&page="+that.page).then(function (res) {
        request = true;
        if (res.body.status == 1) {
        	var list  = res.body.data[0];
        	// that.wid = res.body.data[1];
        	//状态处理
	        for (var i = 0; i < list.length;i ++){
		        //团详情类型  分享2 待成团1 已成团3  拼团失败4   group_type
		        if (list[i].status == 1){
		            list[i].statusText = "待成团";
		            list[i].group_type = 1;
		        } else if (list[i].status == 2) {
		            list[i].statusText = "已成团";
		            list[i].group_type = 3;
		        } else if (list[i].status == 3) {
		            list[i].statusText = "未成团";
		            list[i].group_type = 4;
		        }
	        }
	        if (list.length < 15) {
	          	that.noMore = true;
	        } else {
	          	that.noMore = false;
	        }
	        //若是大于1 则拼接数组
	        if (that.page > 1) {
	          	list = that.groupList.concat(list)
	        }
	        that.pageShow = true;
	        that.groupList = list;
	    }
    });
}
var request = true;
//上拉加载
function getScroll(that){
	window.onscroll=function(){
		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var sH = document.documentElement.clientHeight;
		if(scrollTop + sH +50 >= document.body.scrollHeight && scrollTop > 100 && !vm.noMore && request){
			request = false;
			that.page = that.page + 1;
			getPage(that);
		}
	}
}