Vue.http.options.emulateJSON = true;
var app = new Vue({
    el: "#app",
    data: {
        data1:data1,
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
        pay_toggle : true,
        groups_id:'',//团购id
        freight:"",//运费
        payment:"",//支付方式
        pay_id:"",//支付id
        no_logistics:data1.no_logistics,
        pay:1,
        show_tip:false,
        isShowShare: false, //是否显示分享引导弹窗
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
        if(data1.isAutoFrame == 1){
            this.show_tip = true;
        }
        //实时数据
        var urlTip = "/shop/web/groups/getGroupsMessage";
        this.$http.get(urlTip).then(function(res) {
            this.topTip = res.body.data;
            var topTipIndex = 0;
            var that = this
            setInterval(function() {
                if (topTipIndex >= that.topTip.length) {
                    topTipIndex = 0;
                }
                that.topTipList = that.topTip[topTipIndex];
                setTimeout(function() {
                    that.topTipList = null
                    topTipIndex++;
                }, 3000)
            }, 10000)
        })
    },
    methods: {      
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
                    console.log(this.freight)
                    this.allprice = returnFloat(this.number * parseFloat(this.price) + parseFloat(this.freight));
                }
            })
        },
        hideModel:function(){
            this.show_tip = false;
            this.isShowShare = false;
        },
        setShowShare: function() {
            this.isShowShare = !this.isShowShare;
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
            var remark_no = localStorage.getItem("remark_no");
            
            if(that.pay_toggle){
                that.pay_toggle = false;
                
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
                    pid: pid,
                    num: 1,
                    tag:1
                }
                if(remark_no){
                    postData.remark_no = remark_no;
                }
                that.$http.post('/shop/meeting/groups/createOrder', postData).then(function(res) {
                    if (res.body.status == 1) {
                        window.location.href="/shop/pay/index?id=" + res.body.data.id + '&special=groups';
                        that.pay = 2;
                        that.payUrl = "/shop/pay/index?id=" + res.body.data.id + '&special=groups';
                        that.pay_toggle = true;
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
            var dom =e.target;
            this.payment = 3;
            //跳到余额支付
            window.location.href = '/shop/pay/index?id=' + this.pay_id + '&payment=' + 3;
        },
        handleClick_wec:function(e){
            this.payment = 1;
            //跳到微信支付
            window.location.href = '/shop/pay/index?id=' + this.pay_id + '&payment=' + 1;
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
/**
 ** 乘法函数，用来得到精确的乘法结果
 ** 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
 ** 调用：accMul(arg1,arg2)
 ** 返回值：arg1乘以 arg2的精确结果
 **/

//获取url_参数
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}