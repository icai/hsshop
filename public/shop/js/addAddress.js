Vue.http.options.emulateJSON = true;
var app = new Vue({
	el:"#app",
	data:{
		selected_index : 1,
        default_img : "../images/ap-weixuan@2x.png",
        selected_img: '../images/dui@2x.png',
        no_selected_img: '../images/ap-weixuan@2x.png',
      	name:"",
      	address_detail:"",
      	phone:"",
      	flage:0,
      	province_data : "",        //省份数据
      	city_data:"",              //城市数据
      	area_data: "",             //区域数据
      	select_province_index:"",  //省份id
      	select_city_index:"",	   //城市id
      	select_area_index:"",       //区域id 
      	id : "",
      	hint:"",
      	show_hint:false,
      	sku_id : "",  //规格id
        pid:"",   // 产品id
        rule_id:"", //团购的规则id
        num:"",  //数量
        limit_num : null , //限购数量
        first_add : "",
        isShowProvince:false,
        isShowCity:false,
        isShowArea:false,
        province_name: "请选择",
        city_name: "请选择",
        area_name: "请选择",
        province_val: "",
        city_val: "",
		area_val:"",
		skuId:'',
		activityId: '',
		wid:'',
		//add by 邓钊 2018-9-7
		spanShow: {
			span_a: 0,
			span_b: 0,
			span_c: 0,
		},
		spanActive: 0,
        addressModel: false,
        cityAddress: '省份，城市，县区',
		cityColor: 0,
		isShow: true,
		//end
	},
	
	created:function(){ 
		//得到省份数据
		this.province_data = regionList[-1];
		var Request = new Object();
		Request  =  GetRequest();
		this.id   =  Request.id;
		this.sku_id = Request.sku_id
		this.pid = Request.pid
		this.rule_id = Request.rule_id
		this.num = Request.num
		this.limit_num = Request.limit_num
		this.first_add = Request.first_add
		this.groups_id = Request.groups_id;
		this.activityId = Request.activityId;
		this.skuId = Request.skuId;
        this.come = Request.come;//判断来源good为普通商品，group为团购商品
        this.cart_id = Request.cart_id;//普通商品购物车id
        this.wid = Request.wid;
		//add by 韩瑜 2018-8-15 来源为赠品
        this.giftid = Request.giftid;
        this.activity_id = Request.activity_id;
        //end
		//编辑时
		if(this.id){
			this.name = addressData.title
			this.address_detail = addressData.address
			this.phone = addressData.phone
			//得到编辑时的省市区的id
			this.select_province_index = addressData.province.id;
			this.province_name = addressData.province.title;
			this.city_name = addressData.city.title;
			this.area_name = addressData.area.title;
			this.select_city_index = addressData.city.id
			this.select_area_index = addressData.area.id
			this.city_data = regionList[addressData.province.id]
			this.area_data = regionList[addressData.city.id]
			//add by 邓钊 2018-9-7
			this.spanShow.span_a = 0
			this.spanShow.span_b = 1
			this.spanShow.span_c = 2
            this.spanActive = 2
			this.cityColor = 1
            this.cityAddress = this.province_name + " " + this.city_name + " " + this.area_name
			//end
			//得到type值
			this.flage = addressData.type
			if(this.flage == 1){
				this.default_img = this.selected_img
				this.selected_index = 0
			}else{
				this.default_img = this.no_selected_img
				this.selected_index = 1
			}
		}
	},
	methods:{ 
		//选择默认地址
		selected_default:function(){
		 	this.selected_index = this.selected_index + 1
		 	if(this.selected_index % 2 == 0){
		 		this.default_img = this.selected_img
		 		this.flage = 1
		 	}else{
		 		this.default_img = this.no_selected_img
		 		this.flage = 0
		 	}
		},
		//点击展示省市区
		show_province:function(num){
            this.spanActive = num
		},
		//改变sapn的值和id
		/**
		 * updata by 邓钊 2018-9-7 更改选择city样式
         * */
		change_province_name:function(province_title,province_value){
			this.spanActive = 1
			this.spanShow.span_b = 1
			this.province_name = province_title
			this.province_val = province_value
		},
        /**
         * updata by 邓钊 2018-9-7 更改选择city样式
         * */
		change_city_name:function(city_title,city_value){
            this.spanActive = 2
            this.spanShow.span_c = 2
			this.city_name = city_title
			this.city_val = city_value
		},
		change_area_name:function(area_title,area_value){
			this.area_name = area_title
			this.area_val = area_value
		},
        /**
         * add by 邓钊 2018-9-7 关闭选择city弹框 并赋值
         * */
        getAddress:function(){
            if(!this.select_province_index || !this.select_city_index || !this.select_area_index){
            	return false
			}
            this.addressModel = !this.addressModel
			this.cityAddress = this.province_name + " " + this.city_name + " " + this.area_name
			this.cityColor = 1
        },
		/**
		 * add by 邓钊 2018-9-7 打开选择city弹框
		 * */
        showAddressModel:function(){
            this.addressModel = !this.addressModel
		},
		/**
		 *
		 * **/
        choseAddressModel:function(){
            this.select_province_index = ''
            this.select_city_index = ''
            this.select_area_index = ''
            this.spanShow.span_a = 0
            this.spanShow.span_b = 0
            this.spanShow.span_c = 0
            this.spanActive = 0
            this.province_name = '请选择';
            this.addressModel = !this.addressModel
		},
		//确认按钮请求的接口
		confirm_agree:function(){
			var that = this
			if(!this.id){
				this.id = ""
			}
			this.$http.post('/shop/member/addressAdd',{
				_token:_token,
				title:this.name,
				address:this.address_detail,
				phone:this.phone,
				type:this.flage,
				province_id:this.select_province_index,
				city_id:this.select_city_index,
				area_id:this.select_area_index,
				id: this.id ,
				zip_code:""  //邮政
			}).then(function(res){
        		if(res.body.status == 0){
        			that.show_hint = true
        			that.hint = res.body.info;
        			setTimeout(function(){
        				that.show_hint = false
        			},1000)
        		}else{
        			that.show_hint = true
        			if(this.id){
        				that.hint = "修改成功";
        			}else{
        				that.hint = "添加成功";
        			}
        			setTimeout(function(){
        				that.show_hint = false
        			},1000)
                    // 如果来源是普通商品
                    if(that.come == 'good'){
						if(that.activityId){
							window.location.href="/shop/member/showAddress?activityId="+ that.activityId +"&num="+that.num+"&skuId="+that.skuId +'&come=good';
						}else{
							window.location.href="/shop/member/showAddress?cart_id="+ that.cart_id +'&come=good';
						}
                    }
                    //add by 韩瑜2018-8-10
                    //来源为会员主页时，回到地址列表
                    else if(that.come == 'member'){
	        			window.location.href = '/shop/member/showAddress?wid=' + that.wid + '&come=member';
                    }
                    //add by 韩瑜2018-8-20
                    //来源为大转盘赠品时，回到赠品地址修改页
                    else if(that.come == 'gift1'){
				      	that.$http.post('/shop/activity/setAwardAddress/'+ that.wid, {
				      		type:1,//1大转盘，2砸金蛋，3刮刮卡
				      		activityId:that.activity_id,
				      		addressId:res.body.data.id,
				        	isConfirm:0,
				        	_token: _token
				        })
				        .then(function(res) {
							location.href = '/shop/activity/method/'+ that.giftid + '/1';
				        }) 	
                    }
                    else if(that.come == 'gift2'){
				      	that.$http.post('/shop/activity/setAwardAddress/'+ that.wid, {
				      		type:2,//1大转盘，2砸金蛋，3刮刮卡
				      		activityId:that.activity_id,
				      		addressId:res.body.data.id,
				        	isConfirm:0,
				        	_token: _token
				        })
				        .then(function(res) {
							location.href = '/shop/activity/method/'+ that.giftid + '/2';
				        }) 	
                    }
                    //end
                    else{
                        //如果是第一次添加或者是选择了默认地址
                        if(this.flage == 1 || this.first_add == 1){
                            setTimeout(function(){
                                if(!that.limit_num){
                                    that.limit_num = ""
                                }
                                location.href = '/shop/web/groups/getSettlementInfo?pid=' + that.pid + "&num=" + that.num + "&sku_id=" + that.sku_id + "&rule_id=" + that.rule_id + "&limit_num=" + that.limit_num + '&groups_id='+ that.groups_id;
                            },1000)
                        }else{
                            setTimeout(function(){
                                if(!that.limit_num){
                                    that.limit_num = ""
                                }
                                location.href = '/shop/member/showAddress?pid=' + that.pid + "&num=" + that.num + "&sku_id=" + that.sku_id + "&rule_id=" + that.rule_id + "&limit_num=" + that.limit_num + '&groups_id='+ that.groups_id;
                            },1000)
                        }
                    }
        		}
        	})
		},
	},
	//监听省市区id的变化
	watch:{
		province_val:function(new_province_value,old_province_value){
			//清除上一次填写的省市数据
			this.select_city_index = ""
			this.select_area_index = ""
			if(new_province_value != old_province_value){
				//得到城市数据
				this.select_province_index = new_province_value
				this.city_data = regionList[this.select_province_index],
				this.city_name = "请选择"
				this.area_name = ""
			}
		},
		city_val:function(new_city_value,old_city_value){
			//清除上一次填写的区域数据
			this.select_area_index = ""
			if(new_city_value != old_city_value){
				//得到区域数据
				this.select_city_index = new_city_value
				this.area_data  = regionList[this.select_city_index]
				this.area_name = "请选择"
			}
		},
		area_val:function(new_area_value,old_area_value){
			this.select_area_index = new_area_value
		}
	}
}); 
//获取url中"?"符号后的字符
function GetRequest() {
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

var oHeight = $(document).height(); //浏览器当前的高度
$(window).resize(function(){
	if($(document).height() < oHeight){
		app.isShow = false;
	}else{
		app.isShow = true;
	}
});