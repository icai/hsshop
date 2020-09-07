Vue.http.options.emulateJSON = true;
var app = new Vue({
	el: ".apply_afterSales",
	data: {
		imgUrl:imgUrl,
		type       : '',					//类型判断
 		refundType : 1,						//退款类型
		refunds    : [
			{name:"我要退货退款", val: 1},
			{name:"我要退款（无需退货）", val: 0}
		],
		prouct_info: {},					//产品信息
		order_info : {},					//订单信息
		refundReason: -1,					//退款原因
		reasons     : [],			
		goodsState : -1,					//货物状态
		states     : [
			{name: "未收到货", val: 0},
			{name: "已收到货", val: 1}
		],
		refundMoney: '',					//退款金额占位符
		money      : '',					//退款金额
		refundAmountMax: '',				//最大退款金额
		explain    : '',					//退款说明
		font_num   : 170,					//退款说明字数
		phoneNum   : '',					//手机号码
		toastShow  : false,					//提示开关
		images     : [],					//上传的图片
		btnTxt     : "",					//按钮文字
		res_url    : "",					//连接区分
		flag:true
	},
	mounted: function () {
		this.$nextTick(function () {
			this.imgUploader()
		})
	},
	created:function(){
		this.type = this.GetRequest().type;
		console.log(this.type)
		this.getData(wid, oid, pid, propID);
		//0:其他, 1:配送信息错误, 2:买错商品, 3:不想买了，4：未按承诺时间发货，5：快递无跟踪记录，6：空包裹，7：快递一直未送达，8：缺货'
		if(this.type==1){
			this.reasons = [
				{name: "未按承诺时间发货", val: 4},
				{name: "多拍/错拍/不想要", val: 3},
				{name: "其他", val: 0},
			]
		}else{
			this.reasons = [
				{name: "未按承诺时间发货", val: 4},
				{name: "快递无跟踪记录", val: 5},
				{name: "空包裹", val: 6},
				{name: "快递一直未送达", val: 7},
				{name: "多拍/错拍/不想要", val: 3},
				{name: "缺货", val: 8},
				{name: "其他", val: 0}
			]
		}
	},
	methods:{
		//字数统计
		fontNum:function(){
			this.font_num = 170 - this.explain.length;
		},
		//上传图片
		uploadImg:function(e){
			var that = this;
			var files = e.target.files || e.dataTransfer.files;
			if (!files.length) return;
			//上传动画
			hstool.load();
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
			 			var imgSrc = imgUrl+""+d.data.path;
			 			that.images.push(d.data.path);
                    }
                }
            };
            http.open('post', '/shop/order/upfile/'+wid);
            http.send(fD);
		},
		//插件图片上传
		imgUploader:function(e){
			var that = this;
		},
		
		//删除图片
		delImgIndex:function(index){
			this.images.splice(index,1);
		},
		//请求页面数据
		getData:function(wid, oid, pid, prop_id){
			var that = this;
			if(isEdit==0){
				that.res_url = '/shop/order/refundApply/' + wid + '/' + oid + '/' + pid + '/' + prop_id;
			}else if(isEdit==1){
				that.res_url = '/shop/order/refundApplyEdit/' + wid + '/' + oid + '/' + pid + '/' + prop_id;
			}
			that.$http.get(that.res_url, {
				_token: _token
			}).then(function(res){
				console.log(res)
				if(res.status==200){
					if(res.body.data){
						if(typeof(res.body.data)=='object'){			//如果能退款，退款data中有数据的；
							if(isEdit==0){
								that.prouct_info = res.body.data.product;
								that.order_info = res.body.data.order;
								that.refundAmountMax = res.body.data.order.refundAmountMax;
								if(res.body.data.order.refund_freight > 0){
									that.refundMoney = "￥" + res.body.data.order.refundAmountMax + '(含运费'+ res.body.data.order.refund_freight +'元)';
								}else{
									that.refundMoney = "￥" + res.body.data.order.refundAmountMax;
								}
								
								that.btnTxt = "提交申请"
							}else{
								that.prouct_info = res.body.data.product;
								that.order_info = res.body.data.order;
								that.refundAmountMax = res.body.data.order.refundAmountMax;
								if(res.body.data.order.refund_freight > 0){
									that.refundMoney = "￥" + res.body.data.order.refundAmountMax + '(含运费'+ res.body.data.order.refund_freight +'元)';
								}else{
									that.refundMoney = "￥" + res.body.data.order.refundAmountMax;
								}
								that.btnTxt = "提交修改"
								that.refundReason = res.body.data.refund.reason;
								that.money = Number(res.body.data.refund.amount);
								that.explain = res.body.data.refund.remark;
								that.phoneNum = res.body.data.refund.phone;
								that.goodsState = res.body.data.refund.order_status;
								that.images = res.body.data.refund.imgs?res.body.data.refund.imgs.split(","):[];
							}
						}else{										//如果给你返回数据，是一个拼团id
							location.href = "/shop/grouppurchase/notSupport?groups_id="+res.body.data;
						}
					}else{
						that.toastHint(res.body.info);
					}
				}
			})
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
		//提交
		submit:function(){
			var that = this;
			//判断退款原因
			if(that.refundReason == -1){
				that.toastHint("请选择退款原因");
				return false;
			}
			//判断退款金额
			if (!that.money) {
				that.toastHint("请输入退款金额");
				return false;
			}else if(isNaN(that.money)){
				that.toastHint("请输入数字");
				return false;
			}else if(that.money<0 || parseFloat(that.money) > parseFloat(that.refundAmountMax)){
				that.toastHint("退款金额不能超过最大限额");
				return false;
			};
			//收货状态
			if(that.type==2 && that.goodsState==-1){
				that.toastHint("请选择货物状态");
				return false;
			}
			//退款说明
			if(!that.explain){
				that.toastHint("请输入退款说明");
				return false;
			};
			//联系电话
 			if(!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(that.phoneNum))){ 
 				that.toastHint("请输入正确的手机号码");
 				return false;
 			}       
 			
			var data = {
//				type        : that.type== 1 ? 0:that.refundType,
				type        : that.type== 1 ? 0:1,
				amount      : that.money.toFixed(2),
				order_status: that.goodsState==-1 ? "":that.goodsState,
				reason      : that.refundReason==-1 ? "":that.refundReason,
				phone       : that.phoneNum,
				remark      : that.explain,
				imgs        : that.images,
			};
			if(!that.flag) return;
			console.log(333)
			that.flag = false;
			that.$http.post(that.res_url, {
				_token: _token,
				data: data
			}).then(function(res){
				if(res.status==200){
					that.toastHint(res.body.info);
					//是否是团申请退款
					var link_url = ""; 
					var groupId = that.order_info.groups_id;
					if(groupId==0){		//普通订单
						link_url = "/shop/order/detail/"+oid; 
					}else{				//团订单
						link_url = "/shop/order/groupsOrderDetail/"+oid; 
					}
					setTimeout(function(){
						that.flag = true;
						location.href = link_url;
					}, 1000);
				}else{
					that.flag = true;
				}
  			})
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



