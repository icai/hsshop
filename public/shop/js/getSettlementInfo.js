Vue.http.options.emulateJSON = true;
var app = new Vue({
    el: "#app",
    data: {
        number: "",
        price: "",
        allprice: "",
        isShowGiveUp: false,
        product_message: "", //产品信息
        defaultAddress: "", //默认地址
        address_id: "",
        sku_id: "", //规格id
        pid: "", // 产品id
        rule_id: "", //团购的规则id
        num: "", //数量
        limit_num: null, //限购数量
        hint: "", //提示信息
        hint_show: false, //是否展示提示
        sel_show: false, //余额支付弹窗
        topTip: "",
        topTipList: null,
        topTipList1: null,
        pay_toggle : true,
        groups_id:'',//团购id
        freight:"",//运费
        payment:"",//支付方式
        pay_id:"",//支付id
        balanceMoney:balance,//余额
        no_logistics:data1.no_logistics,
        reqFrom: reqFrom,
        selectPayType: 1, //支付类型
        selectPayTypeOff: 1,
        payShow: false
    },
    created: function() {
        // this.allprice = returnFloat((this.number * this.price) + this.freight); //底部的总价=商品价格*商品数量+运费
        this.product_message = data1
        this.number = data1.num;
        this.price = data1.price;
        this.allprice = data1.allPrice
        this.freight = data1.freight;
        //    this.limit_num = 6
        var Request = new Object();
        Request = GetRequest();
        this.sku_id = Request.sku_id
        this.pid = Request.pid
        this.rule_id = Request.rule_id
        this.num = Request.num
        this.limit_num = Request.limit_num
        this.groups_id = GetQueryString("groups_id");
        this.address_id = Request.address_id || '';
        //选择默认地址
        this.$http.get('/shop/member/getDefaultAddress?address_id='+ this.address_id).then(function(res) {
            if (res.body.status == 1) {
                this.defaultAddress = res.body.data
                this.address_id = res.body.data.id
            }
        })
        //实时数据
        var urlTip = "/shop/web/groups/getGroupsMessage";
        this.$http.get(urlTip).then(function(res) {
            this.topTip = res.body.data;
            var topTipIndex = 0;
            var that = this
            getDanmu(that.topTip);
        })
    },
    methods: {
        selectPay: function(num) {
            this.selectPayTypeOff = num
            this.selectPayType = num
        },
        closePay: function() {
            this.payShow = false
        },
        //商品数量减少
        number_reduce: function() {
            var nb = Number(this.number);
            //最小为1
            if (nb == 1) {
                this.number = 1;
            } else {
                nb = nb - 1;
                this.number = nb;
            }
            //改变数量后获取新的运费信息
            this.$http.get('/shop/web/groups/getFreight?pid='+ this.pid+'&sku_id='+this.sku_id+'&num='+this.number).then(function(res) {
                if (res.body.status == 1) {
                    this.freight = res.body.data;
                    this.allprice = returnFloat(this.number * parseFloat(this.price) + parseFloat(this.freight));
                }
            })
        },
        //获取url参数
        getUrl:function(name){
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
            var r = window.location.search.substr(1).match(reg); 
            if (r != null) return unescape(r[2]); 
            return null; 
        },
        //商品数量增加
        number_add: function() {
            var that = this;
            var nb = Number(this.number);
            if(data1.surplus != -1){//限购类型 每人限购时判断剩余购买数量
                if(nb + 1 > data1.surplus){
                    that.hint_show = true;
                    that.hint = "该商品不能超过限购数量";
                    setTimeout(function() {
                        that.hint_show = false
                    }, 1000)
                    return false;
                }
            }
            //最大为999
            //库存数量限制
            if(nb >= parseFloat(data1.skuData.stock_num)){
                that.hint_show = true,
                that.hint = "商品数量不应超过库存量",
                setTimeout(function() {
                    that.hint_show = false
                }, 1000)
                return false;
            }
            if (nb == 999) {
                this.number = 999;
            } else if ((parseFloat(this.limit_num)) && (nb >= this.limit_num)) {
                this.number = this.limit_num
                that.hint_show = true,
                    that.hint = "该商品不能超过限购数量",
                    setTimeout(function() {
                        that.hint_show = false
                    }, 1000)
            } else {
                nb = nb + 1;
                this.number = nb;
            }
            //改变数量后获取新的运费信息
            this.$http.get('/shop/web/groups/getFreight?pid='+ this.pid+'&sku_id='+this.sku_id+'&num='+this.number+'&address_id='+this.address_id).then(function(res) {
                if (res.body.status == 1) {
                    this.freight = res.body.data;
                    this.allprice = returnFloat(this.number * parseFloat(this.price) + parseFloat(this.freight));
                }
            })
        },
        //新增地址跳转
        addAddress: function() {
            var that = this;
            if (!that.pid) {
                that.pid = ""
            }
            if (!that.num) {
                that.num = ""
            }
            if (!that.sku_id) {
                that.sku_id = ""
            }
            if (!that.rule_id) {
                that.rule_id = ""
            }
            if (!that.limit_num) {
                that.limit_num = ""
            }
            location.href = '/shop/member/showAddress?pid=' + that.pid + "&num=" + that.num + "&sku_id=" + that.sku_id + "&rule_id=" + that.rule_id + "&limit_num=" + that.limit_num+"&groups_id="+that.groups_id;
        },
        immediately_pay: function() {
        	var that = this;
            // var remark_no = localStorage.getItem("remark_no");
            var remark_no = remark_no;
        	if (!that.address_id && this.no_logistics == 0) {
                that.address_id = ""
                that.hint_show = true;
                that.hint = "请填写收货地址"
                setTimeout(function() {
                    that.hint_show = false
                }, 1000)
                return false;
	        }
            that.payShow = true
        },
        // update by 黄新琴  2018-7-30 9:25 判断当前环境，如果是支付宝小程序打开，调用支付宝支付或者余额支付
        submitPay:function(num){
            var that = this;
            if(that.pay_toggle){
                that.pay_toggle = false;
                if (!data1.skuData.id) {
                    data1.skuData.id = ""
                }
                if (!data1.groups_id) {
                    data1.skuData.id = ""
                }
                var payment = {
                    _token: _token,
                    pid: data1.pid,
                    num: that.number,
                    sku_id: that.sku_id,
                    groups_id: data1.groups_id,
                    rule_id: data1.rule_id,
                    address_id: that.address_id
                }
                var postData = {
                    _token: _token,
                    pid: data1.pid,
                    num: that.number,
                    sku_id: that.sku_id,
                    groups_id: data1.groups_id,
                    rule_id: data1.rule_id,
                    address_id: that.address_id
                }
                if(this.no_logistics == 1){
                    postData.address_id = 0;
                }
                that.$http.post('/shop/web/groups/createOrder', postData).then(function(res) {
                    if (res.body.status == 1) {
                        that.pay_toggle = true;
                        that.pay_id = res.body.data.id;
                        if (that.reqFrom == 'aliapp') {
                            if(num == 1){
                                if(Number(that.balanceMoney) < Number(that.price)){
                                    tool.tip('余额不足');
                                    return;
                                }
                                that.handleClick_yue()
                            }else{
                                that.handleClick_alipay();
                            }
                        } else if (that.reqFrom == 'baiduapp') {
                            if(num == 1){
                                if(Number(that.balanceMoney) < Number(that.price)){
                                    tool.tip('余额不足');
                                    return;
                                }
                                that.handleClick_yue()
                            }else{
                                that.handleClick_baidupay();
                            }
                        } else {
                            if(that.selectPayType == 1){
                                if(Number(that.balanceMoney) < Number(that.price)){
                                    tool.tip('余额不足');
                                    return;
                                }
                                that.handleClick_yue()
                            }else{
                                that.handleClick_wec()
                            }
                        }
                    }else if (res.body.status == 0) {
                        that.hint_show = true;
                        that.hint = res.body.info;
                        setTimeout(function() {
                            that.hint_show = false
                        }, 1000)
                        that.pay_toggle = true
                    }
                })
            }
        },
        handleClick_yue:function(e){
		    this.payment = 3;
		    //跳到余额支付
        	window.location.href = '/shop/pay/index?id=' + this.pay_id + '&payment=' + 3;
	    },
	    handleClick_wec:function(e){
		    this.payment = 1;
		    //跳到微信支付
        	window.location.href = '/shop/pay/index?id=' + this.pay_id + '&payment=' + 1;
        },
        // add by 黄新琴 2018-7-30 9:36 支付宝支付跳转
        handleClick_alipay:function(e){
		    this.payment = 4;
            //跳到支付宝支付
            my.navigateTo({url:'/pages/shop/alipay/alipay?id='+this.pay_id});
	    },
        sel_close:function(){//支付选项遮罩关闭
    		var that =this;
        	that.sel_show=!that.sel_show
    	},
        GiveUpPay: function() {
            this.isShowGiveUp = !this.isShowGiveUp
        },
        continuePay: function() {
            this.isShowGiveUp = !this.isShowGiveUp
        },
        /*
         * add by 韩瑜 
         * date 2018-10-16
         * 百度钱包支付跳转
         */
        handleClick_baidupay:function(){
            swan.webView.navigateTo({url: '/pages/baidupay/baidupay?id='+this.pay_id});
        },
        //付款页面点击跳转到商品详情
        link_jump:function(){
            console.log(this.rule_id)
            window.location.href = '/shop/grouppurchase/detail/' + this.rule_id
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
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = (strs[i].split("=")[1]);
        }
    }
    return theRequest;
}
/**
 * [returnFloat 金额保留两位小数处理方式]
 * @author [huoguanghui]
 * @param {num || string} [计算后的价格]
 * @return {[string]} [精确后的价格]
 */
function returnFloat(value){
    var value=Math.round(parseFloat(value)*100)/100;//四舍五入精确价格
    var xsd=value.toString().split(".");//数字装换字符串
    if(xsd.length==1){
        value=value.toString()+".00";
        return value;
    }
    if(xsd.length>1){
        if(xsd[1].length<2){
            value=value.toString()+"0";
        }
        return value;
    }
}

//获取url_参数
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}
//弹窗
function input(val){
    var div=$("<div><img src="+val.headimgurl+"alt=''/>"+val.nickname+","+val.sec+"秒前拼单了这个商品</div>");
    $(".d_show").append(div);
}
function getDanmu(date){
    var i=0;
    setInterval(function(){
		if($(".d_show").height()<70){
			if(i<date.length){
				input(date[i]);
				i++;
			}else{
                i=0;
                input(date[i]);    
				i++;
			}
		}else{
			$($(".d_show").children("div").get(0)).toggle(1200);
            // $($(".d_show").children("div").get(0)).remove();
            setTimeout(function(){
			    $($(".d_show").children("div").get(0)).remove(1200);                
            },1200)
			if(i<date.length){
				input(date[i]);
				i++;
			}else{
				i=0;
				input(date[i]);
				i++;
			}
		}
	},2300);
}