new Vue({
  el: '#main',
  data:{
    nav:[
        {title:'全部',isActive:true,status:0},
        {title:'待用户处理',isActive:false,status:1}
    ],
    refundList:[],
    imgUrl:imgUrl,
    status:0
  },
  filters: {  
    listStatus: function (value,sta1,sta2) {  
        switch(value){
            case 1:
              value = '申请收到，商家处理中';
              break;
            case 2:
              value = '退款被驳回，待用户处理';
              break;
            case 3:
              value = '退款中';
              break;
            case 4:
              value = '退款成功';
              break;
            case 5:
              value = '取消退款';
              break;
            case 6:
              value = '待用户发货';
              break;
            case 7:
              value = '您已退货，商家处理中';
              break;
            case 8:
              value = '退款成功';
              break;
            case 9:
              value = '退款失败';
              break;
            default:
              value = '';
        }
        return value;
    }
  },
  methods: {
      chooseTab:function(nav){
        for(var i = 0; i<this.nav.length;i++){
            this.nav[i]['isActive'] = false;
        }
        nav.isActive = true;
        this.status = nav.status;
        // 获取分类数据
        hstool.load();
        this.refundList = [];
        this.$http.get('/shop/order/refundList/'+ wid + '/' + nav.status).then(function(res){
            hstool.closeLoad();
            if(res.body.data.data.length){
                for(var i = 0;i<res.body.data.data.length;i++){
                    this.refundList.push(res.body.data.data[i]);
                }
            }
        })
      }
  },
  beforeCreate: function () {
    
  },
  created: function () {
    // 获取分类数据
    hstool.load();
    this.$http.get('/shop/order/refundList/'+ wid,{status:0}).then(function(res){
        hstool.closeLoad();
        if(res.body.data.data.length){
            for(var i = 0;i<res.body.data.data.length;i++){
                this.refundList.push(res.body.data.data[i]);
            }
        }
    })
    var stop=true;//触发开关，防止多次调用事件
    var that = this;
    var page = 1;
    $(window).scroll( function(event){
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            if (stop == true) {
                stop = false;
                page = page + 1;//当前要加载的页码
                // 获取分类数据
                that.$http.get('/shop/order/refundList/'+ wid + '/' + that.status + '?page=' + page).then(function(res){
                    console.log(res);
                    if(res.body.data.data.length){
                        for(var i = 0;i<res.body.data.data.length;i++){
                            that.refundList.push(res.body.data.data[i]);
                        }
                        stop = true;
                    }
                })
               
            }
        }
    });
  }
})