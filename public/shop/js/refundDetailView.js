new Vue({
  el: '#main',
  data:{
    addressShow:false, //确认地址弹窗
    confirmShow:false, //确认弹窗
    detail:null, //接口数据
    time:{
      day:null,
      hour:null,
      min:null,
      sec:null
    },
    flag:false //防止多次点击
  },
  filters: {  
    listStatus: function (value,sta1,sta2) {  
        switch(value){
            case 1:
              value = '申请退款中';
              break;
            case 2:
              value = '申请退款被拒';
              break;
            case 3:
              value = '退款中';
              break;
            case 4:
              value = '退款完成';
              break;
            case 5:
              value = '买家取消退款';
              break;
            case 6:
              value = '商家同意退货';
              break;
            case 7:
              value = '买家填写退货信息完成';
              break;
            case 8:
              value = '退款到账';
              break;
            case 9:
              value = '退款申请关闭';
              break;
            default:
              value = '';
        }
        return value;
    }
  },
  methods: {
      addressModelShow:function(){
        this.addressShow = true;
      },
      hideAddressModel:function(){
        this.addressShow = false;
      },
      jisuandaojishi:function(closeTime){
        //var closeTime = endtime - currenttime;
        var that = this;
        var displayTime;
        function showTime(){
          var day = Math.floor(closeTime / (60 * 60 * 24));
          var hour = Math.floor(closeTime / (3600)) - (day * 24);
          var minute = Math.floor(closeTime / (60)) - (day * 24 * 60) - (hour * 60);
          var second = Math.floor(closeTime) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
          closeTime -= 1;
          // var html = day+'天'+hour+'小时'+minute+'分'+second+'秒';
          that.time.day = day;
          that.time.hour = hour;
          that.time.min = minute;
          that.time.sec = second;
          if(closeTime == -1){
            clearInterval(displayTime);
            // document.location.href = document.location.href;
            return;
          }
        }
        showTime();
        displayTime = setInterval(function(){
            showTime();
        }, 1000)
      },
      cancelApplyConfirm:function(){
        this.confirmShow = false;
      },
      sureApplyConfirm:function(){
        if(this.flag)return;
        var that = this;
        this.flag = true;
        this.confirmShow = false;
        this.$http.get('/shop/order/refundCancel/' + wid + '/' + oid + '/' + this.detail.refund.id).then(function(data){
          // console.log(data);
          tool.tip(data.body.info);
          // link_url = "/shop/order/detail/"+oid; 
          setTimeout(function(){
            that.flag = false;
            if(parseInt(data.body.data.groupID)){
              window.location.href="/shop/order/groupsOrderDetail/" + oid;
            }else{
              window.location.href="/shop/order/detail/" + oid;
            }
          },2000)
        })
      },
      delApply:function(){
        this.confirmShow = true;
      }
  },
  beforeCreate: function () {
      
  },
  created: function () {
    var that = this;
    hstool.load();
    this.$http.get('/shop/order/refundDetail/'+ wid +'/'+ oid +'/' + pid + '/' + prop_id).then(function(res){
      this.detail = res.body.data;
      hstool.closeLoad();
      if(res.body.status){
        this.jisuandaojishi(res.body.data.refund.end_timestamp - res.body.data.refund.now_timestamp);
      }else{
        tool.tip(res.body.info);
      }
    })
  }
})