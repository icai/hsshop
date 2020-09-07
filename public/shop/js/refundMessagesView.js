new Vue({
  el: '#main',
  data:{
    detail:null,
    imgUrl:''
  },
  methods: {
    
  },
  beforeCreate: function () {
      
  },
  created: function () {
    hstool.load();
    this.$http.get('/shop/order/refundMessages/'+wid + '/'+oid + '/' + pid + '/' + prop_id).then(function(data){
      hstool.closeLoad();
      if(data.body.status == 1){
        if(data.body.data.data.refund.imgs == ''){
          data.body.data.data.refund.imgs = [];
        }else{
          data.body.data.data.refund.imgs = this.imgUrl + data.body.data.data.refund.imgs.split(",");
        }
        if(data.body.data.data.refund['type'] == 1){
           data.body.data.data.refund['type'] = '退货退款';
        }else if(data.body.data.data.refund['type'] == 0){
           data.body.data.data.refund['type'] = '仅退款';
        }
        switch(data.body.data.data.refund['reason']){
              case 0:
                data.body.data.data.refund['reason'] = '其他';
                break;
              case 1:
                data.body.data.data.refund['reason'] = '配送信息错误';
                break;
              case 2:
                data.body.data.data.refund['reason'] = '买错商品';
                break;
              case 3:
                data.body.data.data.refund['reason'] = '不想买了';
                break;
              case 4:
                data.body.data.data.refund['reason'] = '未按承诺时间发货';
                break;
              case 5:
                data.body.data.data.refund['reason'] = '快递无跟踪记录';
                break;
              case 6:
                data.body.data.data.refund['reason'] = '空包裹';
                break;
              case 7:
                data.body.data.data.refund['reason'] = '快递一直未送达';
                break;
              case 8:
                data.body.data.data.refund['reason'] = '缺货';
                break;
              default:
                
            }
        if(data.body.data.data.messages.length){
          for(var i = 0;i<data.body.data.data.messages.length;i++){
            if(data.body.data.data.messages[i]['imgs'] == ''){
              data.body.data.data.messages[i]['imgs'] = [];
            }else{
              data.body.data.data.messages[i]['imgs'] = data.body.data.data.messages[i]['imgs'].split(",");
              if(data.body.data.data.messages[i]['imgs'].length){
                for(var j =0 ;j<data.body.data.data.messages[i]['imgs'].length;j++){
                  data.body.data.data.messages[i]['imgs'][j] = imgUrl + data.body.data.data.messages[i]['imgs'][j];
                }
              }
            }
            if(data.body.data.data.messages[i].status == 4){
                switch(data.body.data.data.messages[i].reason){
                  case 0:
                    data.body.data.data.messages[i].reason = '其他';
                    break;
                  case 1:
                    data.body.data.data.messages[i].reason = '配送信息错误';
                    break;
                  case 2:
                    data.body.data.data.messages[i].reason = '买错商品';
                    break;
                  case 3:
                    data.body.data.data.messages[i].reason = '不想买了';
                    break;
                  case 4:
                    data.body.data.data.messages[i].reason = '未按承诺时间发货';
                    break;
                  case 5:
                    data.body.data.data.messages[i].reason = '快递无跟踪记录';
                    break;
                  case 6:
                    data.body.data.data.messages[i].reason = '空包裹';
                    break;
                  case 7:
                    data.body.data.data.messages[i].reason = '快递一直未送达';
                    break;
                  case 8:
                    data.body.data.data.messages[i].reason = '缺货';
                    break;
                  default:
                    
                }
            }
         
          }
        }
        this.detail = data.body.data.data;
        
      }
    })

    // 滚动加载
    var stop=true;//触发开关，防止多次调用事件
    var that = this;
    var page = 1;
    $(window).scroll( function(event){
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            if (stop == true) {
                stop = false;
                page = page + 1;//当前要加载的页码
                // 获取分类数据
                that.$http.get('/shop/order/refundMessages/'+wid + '/'+oid + '/' + pid + '/' + prop_id + '?page=' + page).then(function(data){
                    console.log(data);
                    if(data.body.status == 1){
                      if(data.body.data.data.messages.length){
                        for(var i = 0;i<data.body.data.data.messages.length;i++){
                          if(data.body.data.data.messages[i]['imgs'] == ''){
                            data.body.data.data.messages[i]['imgs'] = [];
                          }else{
                            data.body.data.data.messages[i]['imgs'] = data.body.data.data.messages[i]['imgs'].split(",");
                            if(data.body.data.data.messages[i]['imgs'].length){
                              for(var j =0 ;j<data.body.data.data.messages[i]['imgs'].length;j++){
                                data.body.data.data.messages[i]['imgs'][j] = imgUrl + data.body.data.data.messages[i]['imgs'][j];
                              }
                            }
                          }
                            switch(data.body.data.data.messages[i].reason){
                                case 0:
                                    data.body.data.data.messages[i].reason = '其他';
                                    break;
                                case 1:
                                    data.body.data.data.messages[i].reason = '配送信息错误';
                                    break;
                                case 2:
                                    data.body.data.data.messages[i].reason = '买错商品';
                                    break;
                                case 3:
                                    data.body.data.data.messages[i].reason = '不想买了';
                                    break;
                                case 4:
                                    data.body.data.data.messages[i].reason = '未按承诺时间发货';
                                    break;
                                case 5:
                                    data.body.data.data.messages[i].reason = '快递无跟踪记录';
                                    break;
                                case 6:
                                    data.body.data.data.messages[i].reason = '空包裹';
                                    break;
                                case 7:
                                    data.body.data.data.messages[i].reason = '快递一直未送达';
                                    break;
                                case 8:
                                    data.body.data.data.messages[i].reason = '缺货';
                                    break;
                                default:

                            }
                          this.detail.messages.push(data.body.data.data.messages[i]);
                        }
                      }
                    }
                     stop = true;
                })
               
            }
        }
    });
  }
})