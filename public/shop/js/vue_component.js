Vue.component('spellTitle', {
  props: ['content'],
  data: function () {
    return {
      isscroll: false
    }
  },
  methods: {
    increment: function () {
      // this.counter += 1
      // this.$emit('increment')
    }
  },
  mounted: function(){
      
  },
  template: '<div class="control-group"><div class="spell_title_out">'+
  '<div class="spell_title">'+
  '<a href="javascript:void(0);" :class="index == 0 ? \'active\': \'\' " v-for="(list, index) in content">{{list.name}}</a>'+
  '</div></div><div class="component-border"></div></div>',
})
Vue.component('spellGoods', {
  props: ['content'],
  data: function () {
    return {
      isscroll: false,
      state:0
    }
  },
  created:function(){
     that =this
     var groups = that.content.groups
     if (groups) {
        for (var j = 0; j < groups.length; j++) {
          var formstart = groups[j].start_time.replace(/-/g, '/')
          var starttime = Date.parse(new Date(formstart))

          var formend = groups[j].end_time.replace(/-/g, '/')
          var endtime = Date.parse(new Date(formend))

          var formnow = groups[j].now_time.replace(/-/g, '/')
          var nowtime = Date.parse(new Date(formnow))
          if (starttime > nowtime) {
            console.log('未开始')
            groups[j]['state'] = 2;
          } else if (nowtime > starttime && endtime > nowtime) {
            groups[j]['state'] = 1; //开始
            console.log('开始')
          } else {
            console.log('结束')
            groups[j]['state'] = 0; //结束
          }
        }
      }
  },
  methods: {
    increment: function () {
      // this.counter += 1
      // this.$emit('increment')
    }
  },
  mounted: function(){
      //console.log(this.content.groups);
  },
  template: '<div><div>'+
            '<div class="control-group" style="background-color:#fff" v-if="content.style == 3">'+
              '<div class="portal-list-item" v-for="list in content.groups">'+
              '<div class="gp-list-state" style="position:absolute;top:8px;right:8px;width:50px;height:25px;background:#de2626;border-radius:26px;line-height:25px;z-index:100;text-align:center"  v-if="list.state==1">活动中</div>'+
                '<div class="gp-list-state" style="position:absolute;top:8px;right:8px;width:50px;height:25px;background:#666666;border-radius:26px;line-height:25px;z-index:100;text-align:center;color: #fff" v-if="list.state==2">未开始</div>'+
                  '<a :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid" class="std-goods-image goods-image">'+
                      '<img v-lazy=\'imgUrl + list.rectangle_image\'>'+
                  '</a>'+
                  '<p class="goods-name">{{list.title}}</p>'+
                  '<div class="detail">'+
                      '<div class="left-side">'+
                          '<div class="sale-price">'+
                              '<i>￥</i>{{list.price}}'+
                          '</div>'+
                          '<div class="group-desc">'+
                              '<span>已拼{{list.attend_num}}件</span>'+
                          '</div>'+
                      '</div>'+
                      '<div class="right-side">'+
                          '<div class="local-groups" v-if="list.member.length">'+
                              '<span></span>'+
                              '<div class="avatar" v-if="list.member[0] != undefined">'+
                                  '<img v-lazy="list.member[0][\'headimgurl\'] != undefined ? list.member[0][\'headimgurl\'] : \'\'"></div>'+
                              '<div class="avatar" v-if="list.member[1] != undefined">'+
                                  '<img v-lazy="list.member[1][\'headimgurl\'] != undefined ? list.member[1][\'headimgurl\'] : \'\' "></div>'+
                          '</div>'+
                          '<a :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid" class="enter-button">'+
                              '<img v-lazy="_host + \'shop/images/kaituan@2x.png\'">'+
                          '</a>'+
                      '</div>'+
                  '</div>'+
              '</div>'+
            '</div>'+
            '<div class="control-group" style="background-color:#fff" v-if="content.style == 4">'+
                '<a :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid" class="new-arrivals-list-item-1 double-grid-one-v2"  v-for="list in content.groups">'+
                    '<div class="double-grid-item-v2" avalonctrl="new-arrivals-list-item-1">'+
                        '<div class="std-goods-image-square">'+
                        '<div class="gp-list-state" style="position:absolute;top:5px;right:20px;width:50px;height:25px;background:#de2626;border-radius:26px;line-height:25px;z-index:100;text-align:center;"  v-if="list.state==1">活动中</div>'+
                        '<div class="gp-list-state" style="position:absolute;top:5px;right:20px;width:50px;height:25px;background:#666666;border-radius:26px;line-height:25px;z-index:100;text-align:center;color: #fff" v-if="list.state==2">未开始</div>'+
                            '<img v-lazy=\'imgUrl + list.square_image\'>'+
                        '</div>'+
                        '<div class="detail">'+
                            '<div class="name-block">'+
                                '<div class="goods-name">{{list.title}}</div>'+
                            '</div>'+
                            '<div class="core">'+
                                '<div class="info">'+
                                    '<p class="sale-price">'+
                                        '<i>￥</i>{{list.price}}</p>'+
                                '</div>'+
                                '<div class="local-groups" v-if="list.member.length">'+
                                    '<span></span>'+
                                    '<div class="avatar" v-if="list.member[0] != undefined">'+
                                        '<img v-lazy="list.member[0][\'headimgurl\'] != undefined ? list.member[0][\'headimgurl\'] : \'\'"></div>'+
                                    '<div class="avatar" v-if="list.member[1] != undefined">'+
                                        '<img v-lazy="list.member[1][\'headimgurl\'] != undefined ? list.member[1][\'headimgurl\'] : \'\' "></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</a>'+
            '</div>'+
            '<!-- 拼团2 -->'+
            '<div class="control-group" v-if="content.style == 2">'+
                '<div class="portal-list-item" v-for="list in content.groups">'+
                '<div class="gp-list-state" style="position:absolute;top:8px;right:8px;width:50px;height:25px;background:#de2626;border-radius:26px;line-height:25px;z-index:100;text-align:center"  v-if="list.state==1">活动中</div>'+
                '<div class="gp-list-state" style="position:absolute;top:8px;right:8px;width:50px;height:25px;background:#666666;border-radius:26px;line-height:25px;z-index:100;text-align:center;color: #fff" v-if="list.state==2">未开始</div>'+
                    '<a :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid" class="std-goods-image goods-image">'+
                        '<img v-lazy=\'imgUrl + list.rectangle_image\'>'+
                        '<div class="header_img">'+
                            '<div class="info">'+
                                '<div class="info_spell" v-if="list.label">'+
                                    '<span>{{list.label}}</span>'+
                                '</div>'+
                                '<p class="info_title">{{list.title}}<p>'+
                                '<p class="info_tag" >{{list.subtitle}}<p>'+
                                '<div class="info_price">'+
                                    '<span>￥{{list.price}}</span>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</a>'+
                    '<div class="detail">'+
                        '<div class="left-side">'+
                            '<div class="local-groups pintuan3" v-if="list.member.length">'+
                                '<span></span>'+
                                '<div class="avatar" v-if="list.member[0] != undefined">'+
                                    '<img v-lazy="list.member[0][\'headimgurl\'] != undefined ? list.member[0][\'headimgurl\'] : \'\'"></div>'+
                                '<div class="avatar" v-if="list.member[1] != undefined">'+
                                    '<img v-lazy="list.member[1][\'headimgurl\'] != undefined ? list.member[1][\'headimgurl\'] : \'\' "></div>'+
                            '</div>'+
                            '<div class="group-desc" v-if="list.member.length">'+
                                '<span>'+
                                   '等{{list.attend_num}}名好友参团'+
                                '</span>'+
                            '</div>'+
                        '</div>'+
                        '<div class="right-side">'+
                            '<a :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid" class="enter-button">'+
                                '<img v-lazy="_host + \'shop/images/kaituan@2x.png\'">'+
                            '</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<!-- 拼团1 -->'+
            '<div class="control-group" v-if="content.style == 1">'+
                '<div class="gp-list-wrap list-1">'+
                    '<div class="gp-list-box">'+
                        '<div class="gp-list-item" style="width: 140px; margin-right: 10px;" v-for="list in content.groups">'+
                          '<a style="margin: 0" :href="\'/shop/grouppurchase/detail/\'+list.id + \'/\' + wid">'+
                              '<div class="gp-list-img-wrap" style="width: 140px;border: none">'+
                                  '<div class="gp-list-state" style="position:absolute;top:5px;right:5px;width:50px;height:25px;background:#de2626;border-radius:26px;line-height:25px;z-index:100;text-align:center"  v-if="list.state==1">活动中</div>'+
                                  '<div class="gp-list-state" style="position:absolute;top:5px;right:5px;width:50px;height:25px;background:#666666;border-radius:26px;line-height:25px;z-index:100;text-align:center;color: #fff" v-if="list.state==2">未开始</div>'+
                                  '<img class="gp-list-img" v-lazy="imgUrl + list.square_image">'+
                                  '<div class="gp-list-label" v-if="list.lable">{{list.lable}}</div>'+
                              '</div>'+
                              '<div class="gp-list-goods-name" style="padding-left:5px;box-sizing: border-box;" v-if="list.title">{{list.title}}</div>'+
                              '<div class="gp-list-other" style="padding-left:5px;box-sizing: border-box;">'+
                                  '<div class="gp-list-price">￥{{list.price}}</div>'+
                                  '<div class="gp-list-people" v-if="list.member.length">'+
                                      '<img v-if="list.member[0] != undefined" v-lazy="list.member[0][\'headimgurl\'] != undefined ? list.member[0][\'headimgurl\'] : \'\' " class="gp-list-people-img">'+
                                      '<img v-if="list.member[1] != undefined" v-lazy="list.member[1][\'headimgurl\'] != undefined ? list.member[1][\'headimgurl\'] : \'\' " class="gp-list-people-img" style="right:20px;">'+
                                  '</div>'+
                              '</div>'+
                          '</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>'+
    '</div>',
}) 
Vue.component('notice', {
  props: {
      content:{
          type: String,
          default: ''
      },
      bgColor:{
          type: String,
          default: '#ffc'
      },
      bgTxt:{
          type: String,
          default: '#f90'
      }
  },
  data: function () {
  return {
    isscroll: false,
    classScroll: ''
  }
  },
  methods: {
    increment: function (str) {
        if(str.length > 500){
            return "scroll-notice_d"
        }else if(str.length > 300 && str.length <= 500){
            return "scroll-notice_c"
        }else if(str.length > 100 && str.length <= 300){
            return "scroll-notice_b"
        }else if(str.length <= 100){
            return "scroll-notice_a"
        }
    }
  },
  mounted: function(){
    this.$nextTick(function(){
      //对DOM的操作放这
        this.$el.style.backgroundColor = this.bgColor
        this.$el.style.color = this.bgTxt
        // alert(this.$el.childNodes[0].childNodes[0].childNodes[0].offsetWidth);
      // alert(this.$el.childNodes[0].offsetWidth)
      var spantxt = this.$el.childNodes[0].childNodes[0].childNodes[0].innerHTML
      if(this.$el.childNodes[0].childNodes[0].childNodes[0].offsetWidth >= document.body.clientWidth){
          this.classScroll = this.increment(spantxt)
          this.isscroll = true;
      }
      if(this.$el.childNodes[0].childNodes[0].childNodes[0].offsetWidth>540){
          this.classScroll = this.increment(spantxt)
          this.isscroll = true;
      }
    })
  },
    //updata by 邓钊 2018-8-28 删除公告二字
  template: '<div class="custom-notice" ref="mybox">'+
      '<div class="custom-notice-inner">'+
        '<div class="custom-notice-scroll">'+
          '<span class="js-scroll-notice" :class="classScroll">{{content}}</span>'+
        '</div>'+
      '</div>'+
    '</div>',
}) 
/**
 * @author: 魏冬冬（zbf5279@dingtalk.com）
 * @description: 商品分组组件
 * @param {type} 
 * @return: 
 * @Date: 2019-07-12 18:06:16
 * @update: 商品分组增加市场价  2019-07-12
 */
Vue.component('goodGroup', {
  props: ['content'],
  data: function () {
  return {
    isscroll: false,
    data:{
    left_nav:this.content ? this.content.left_nav : "",
    top_nav:this.content ? this.content.top_nav : "",
    },
    group_type:this.content ? this.content.group_type : "",//1为左侧，2,为顶部
    goods:[],
    height:null,
    oHeight:null,
    goodDetail:null,
    index:null,
    // update 华亢 2018/7/12 商品分组平铺 则没有最小高度
    group_pinpu:this.content.group_pinpu?this.content.group_pinpu:0, //0不平铺 1平铺
    listStyle:this.content.listStyle?this.content.listStyle:0, //0 详细列表 1 小图
    page:[],
   }
  },
  methods: {
    increment: function () {
      // this.counter += 1
      // this.$emit('increment')
    },
    chooseKind: function(item,position,index){
      var that = this;
      setTimeout(function(){
          if(position == 1){
            if(that.data.left_nav.length>0){
              for(var i = 0;i<that.data.left_nav.length;i++){
                 that.data.left_nav[i]['isActive'] = false;
              }
            }
          }else if(position == 2){
            if(that.data.top_nav.length>0){
              for(var i = 0;i<that.data.top_nav.length;i++){
                 that.data.top_nav[i]['isActive'] = false;
                 that.goods = that.data.top_nav[i]['goods'];
              }
            }
          }
          this.index = index
          $('.custom-tag-list-goods>div').css({'display':'none'})
          $('.custom-tag-list-goods>div').eq(index).css({'display':'block'})
        that.goods = item.goods;
        item.isActive = true;
      },100)
    },
    getDate: function(goodId,name,price,thumbnail,stock){
      tool.spec.open({
        "type":1,
        "url":'/shop/product/getSku',
        'data':{pid:goodId,_token:$('meta[name="csrf-token"]').attr('content')},
        'initSpec':{'img':thumbnail,'price':price,'title':name,'stock':stock},
        "callback":function(data){
          if(data.index == 0){
            var postData = {'id':goodId,"num":data.data.num,'propid':data.data.spec_id,content:''}
            $.ajax({
              url:'/shop/cart/add/'+id,// 跳转到 action
              data:postData,
              type:'post',
              cache:false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:'json',
              success:function (response) {
                  if (response.status == 1){
                      $('#Xms3Sq4JR6').hide();
                      $('#LBqDHKuruf').hide();
                      tool.spec.close()
                      $('.goods-num').show().html(response.data.cartNum); 
                      tool.tip('已加入购物车,赶紧去结算吧!');
                  }else{
                      tool.tip(response.info);
                  }
              },
              error : function() {
                  // view("异常！");
                  tool.tip("异常！");
              }
          });
          }else if(data.index == 1){

            var postData = {'id':goodId,"num":data.data.num,'propid':data.data.spec_id,content:''}
            $.ajax({
              url:'/shop/cart/add/'+id+'?tag=1',// 跳转到 action
              data:postData,
              type:'post',
              cache:false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:'json',
              success:function (response) {
                if (response.status == 1){
                    window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']';
                }else{
                    tool.tip(response.info);
                }
              },
              error : function() {
                  tool.tip("异常！");
              }
          });
          }    
      }});
    },
    getGroupGood:function(item,index){
        var _this=this
        var num = index;
        var page = this.page[index].page;
        this.$http.get("/shop/group/productGroupDetail?group_id="+ item.id +'&page=' + page + '&isNew=1').then(
            function(res){
            if(res.body.status == 1){
                if(res.body.data.length >= 0 &&res.body.data.length <15){
                    var data = res.body.data
                    for(var k = 0; k < data.length; k++){
                        //data[k].thumbnail = imgUrl + data[k].thumbnail
                        item.goods.push(data[k])
                    }
                 item.btnFlag = false;
                }if(res.body.data.length == 15){
                    var data = res.body.data
                    for(var k = 0; k < data.length; k++){
                        //data[k].thumbnail = imgUrl + data[k].thumbnail
                        item.goods.push(data[k])
                    }
                }
                this.page[num].page ++                          
            }              
            }
        )
    },
    getHeadGroupGood:function(){
        var _this=this
        if(_this.data.top_nav.length > 0){
            var list = _this.data.top_nav;
            var id = null;
            var num = null;
            var index = null;
            for(var i = 0; i < list.length;i++){
                if(list[i].isActive){
                    index = i 
                    id = list[i].id
                    num = this.page[i].page++
                } 
            }
            this.$http.get("/shop/group/productGroupDetail?group_id="+ id +'&page=' + num + '&isNew=1').then(
                function(res){
                  if(res.body.status == 1){
                      if(res.body.data.length >= 0 && res.body.data.length < 15){
                          for(var j = 0; j < list.length; j++){
                              console.log(list[j],'list999999')
                              if(list[j].id == id){
                                  var data = res.body.data
                                  for(var k = 0; k < data.length; k++){
                                    list[j].goods.push(data[k])
                                  }
                                  list[j].btnFlagTop =false;
                              }
                          }
                      } 
                      if(res.body.data.length == 15){
                        var data = res.body.data
                        for(var j = 0; j < list.length; j++){
                            if(list[j].id == id){
                                var data = res.body.data
                                for(var k = 0; k < data.length; k++){
                                  list[j].goods.push(data[k])
                                }
                            }
                        }
                    }
                  }              
                }
            )
          }
    }
  },
  mounted: function(){
    this.$nextTick(function(){
      //对DOM的操作放这
      //左侧商品栏
      var that = this;
      if (this.data.left_nav.length>0) {
        for(var i = 0; i < this.data.left_nav.length; i++){
            var obj = {
                id: this.data.left_nav[i].id,
                page:2
            }
            this.page.push(obj)
            var goods = this.data.left_nav[i].goods
             if ( goods.length <15) {
                this.$set(this.content.left_nav[i],'btnFlag',false)
            }else{
                this.$set(this.content.left_nav[i],'btnFlag',true)
            }
        }
      } else if((this.data.top_nav.length>0)){
          //头部商品
       for(var i = 0; i < this.data.top_nav.length; i++){
        var obj = {
            id: this.data.top_nav[i].id,
            page:2
        }
        this.page.push(obj)
        var goods = this.data.top_nav[i].goods
         if ( goods.length < 15) {
             this.data.top_nav[i].btnFlagTop = false
        }
        else if(goods.length == 15){
             this.data.top_nav[i].btnFlagTop = true
        }
      }
    }
      
      if(this.group_type == 1){
      var side_height = $(that.$el).find('.custom-tag-list-goods').height();
      var aside_height = $(that.$el).find('.custom-tag-list-menu-block').height();
      //分组的nav的高度
      this.oHeight = aside_height + 'px';
      // update 华亢 2018/7/12 商品分组平铺 则没有最小高度
      var minHeight = $(window).height() -120 +'px';
     // console.log($(window).height()-120)
      if(this.group_pinpu == 1){
        this.oHeight = minHeight;
        $('#container').css({'padding-bottom':0});
        $('#container').css({'min-height':minHeight});
        $('.content').css({'min-height':'auto'})
      }
      //end
      var list_height = $(that.$el).children('.custom-tag-list').children('.custom-tag-list-goods').height();
      if(side_height >= list_height){
        this.height = side_height;
      }else{
        this.height = list_height;
      }
      }else if(this.group_type == 2){
      // console.log(this);
         console.log(this.data.top_nav);
       if(this.data.top_nav.length>0){
        this.goods = this.data.top_nav[0]['goods'];
       }
      // console.log(this.goods);
      }
      // if(this.$el.childNodes[0].childNodes[0].childNodes[0].offsetWidth>this.$el.childNodes[0].offsetWidth){
      //   this.isscroll = true;
      // }
    })
  },
  template: '<div style="position:relative">'+
      '<div class="custom-tags js-custom-tags" v-if="group_type == 2 && data.top_nav.length != 0">'+
          '<div class="js-tabber-tags tabber tabber-bottom red clearfix tabber-n4 ">'+
              '<div class="custom-tags-more js-show-all-tags"></div>'+
              '<div id="J_tabber_scroll_wrap" class="custom-tags-scorll clearfix">'+
                  '<div id="J_tabber_scroll_con" class="custom-tags-scorll-con">'+
                      '<a data-tagname="tag-1" href="javascript:void(0);" v-for="(item, index) in data.top_nav" @click="chooseKind(item,2)" :class="item.isActive ? \'active\':\'\'" :style="{width:item.width}">{{item.name}}</a>'+
                  '</div>'+
              '</div>'+
          '</div>'+
          '<div class="js-goods-tag js-goods-tag-1 show" data-alias="1b6gm5ocg" style="min-height:100px;" v-if="listStyle == 0">'+
              '<div class="js-list b-list">'+
                  '<ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;">'+
                      '<!-- 商品区域 -->'+
                      '<!-- 展现类型判断 -->'+
                      '<li class="js-goods-card goods-card normal" v-for="good in this.goods">'+
                          '<a :href="good.url" class="js-goods link clearfix">'+
                              '<div class="photo-block" style="background-color: rgb(255, 255, 255);">'+
                                  '<img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_300"></div>'+
                              '<div class="info">'+
                                  '<p class="goods-title" v-html="good.name"></p>'+
                                  '<p class="goods-price">'+
                                      '<em v-html="good.price"></em></p>'+
                                  '<div class="goods-buy btn1"></div>'+
                                  '<div class="js-goods-buy buy-response"></div>'+
                              '</div>'+
                          '</a>'+
                          '<img v-if="good.is_selling == 0" style="position: absolute;top: 0;width:52px;left:0px;" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">'+
                      '</li>'+
                  '</ul>'+
                  '<div style="text-align:center;" v-for="(item,index) in data.top_nav" v-if="item.isActive && item.btnFlagTop" >'+
                      '<button class="custom-tag-list-topbtn" @click="getHeadGroupGood()"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                '</div>'+
              '</div>'+
          '</div>'+
          '<div v-if="listStyle == 1">' +
              '<!-- 小图模式 -->'+
              '<ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;">'+
                '<!-- 商品区域 -->'+
                '<!-- 展现类型判断 -->'+
                '<li class="js-goods-card goods-card small-pic card card" v-for="good in this.goods">'+
                    '<a :href="good.url" class="js-goods link clearfix">'+
                        '<div class="photo-block" style="background-color: rgb(255, 255, 255);">'+
                            '<img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_400">'+
                        '</div>'+
                        '<div class="info clearfix info-title info-price btn1">'+
                            '<p class=" goods-title " v-html="good.name"></p>'+
                            '<p class="goods-sub-title c-black hide" v-html="good.productDes"></p>'+
                            '<p class="goods-price" style="float: none;margin-bottom: 0" v-if="good.oprice > 0">'+
                                '<em v-html="good.price"></em>'+
                            '</p>'+
                            '<p class="goods-price" style="margin-bottom: 16px" v-else>'+
                                '<em v-html="good.price"></em>'+
                            '</p>'+
                            '<p class="goods-price-taobao" v-if="good.oprice > 0" v-html="good.oprice">100</p>'+
                        '</div>'+
                        '<div class="goods-buy info-title info-price btn1"></div>'+
                        '<div class="js-goods-buy buy-response"></div>'+
                    '</a>'+
                    '<img v-if="good.is_selling == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">'+
                '</li>'+
              '</ul>'+
              '<div style="text-align:center;" v-for="(item,index) in data.top_nav" v-if="item.isActive && item.btnFlagTop" >'+
                      '<button class="custom-tag-list-topbtn " @click="getHeadGroupGood()"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                '</div>'+
          '</div>'+
      '</div>'+
      '<div class="custom-tag-list clearfix js-custom-tag-list" v-if="group_type == 1 && data.left_nav.length != 0" v-bind:style="{height:oHeight,minHeight:400+\'px\'}">'+
          '<div class="custom-tag-list-menu-block" >'+
              '<div style="height: 250px; display: none;"></div>'+
              '<ul class="custom-tag-list-side-menu js-side-menu" style="position: relative;">'+
                  '<li :class="item.isActive ? \'current\':\'\'" @click="chooseKind(item,1,index)" v-for = "(item,index) in data.left_nav">'+
                      '<a class="js-menu-tag">'+
                          '<span>{{item.name}}</span>'+
                      '</a>'+
                  '</li>'+
              '</ul>'+
          '</div>'+
          '<div class="custom-tag-list-goods">'+
            '<div v-for="(item,index) in data.left_nav">'+
              '<p class="custom-tag-list-title" :id="item.href" v-if="index == this.index || index == 0">{{item.name}}</p>'+
              '<ul class="custom-tag-list-goods-list js-custom-goods-list" v-if="item.goods.length && index == this.index || index == 0">'+
                  '<li class="custom-tag-list-single-goods clearfix" v-for="good in item.goods">'+
                      '<a :href="good.url" class="custom-tag-list-goods-img">'+
                          '<img class="js-lazy" alt="" v-lazy="good.thumbnail+thump_200"><img v-if="good.is_selling == 0" class="no_sell" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell1.png"/></a>'+
                      '<div class="custom-tag-list-goods-detail">'+
                          '<a :href="good.url" class="custom-tag-list-goods-title" v-html="good.name"></a>'+
                          '<span class="custom-tag-list-goods-price" v-html="good.price"></span>'+
                          '<a :href="good.url" class="custom-tag-list-goods-oprice" v-if="good.is_price_negotiable == 0" v-text="\'市场价：\' + good.oprice"></a>'+
                          '<a class="custom-tag-list-goods-buy js-custom-tag-list-goods-buy" href="javascript:void(0)">'+
                              '<!--<span class="ajax-buy" @click="getDate(good.id,good.name,good.price,good.thumbnail,good.stock)"></span>-->'+
                              '<span class="ajax-loading"></span>'+
                          '</a>'+
                      '</div>'+
                  '</li>'+
                  '<div style="text-align:center;" v-if="item.btnFlag">'+
                  '<button class="custom-tag-list-btn " @click="getGroupGood(item,index)"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                  '</div>'+
              '</ul>'+
              '<ul class="custom-tag-list-goods-list js-custom-goods-list" v-if="!item.goods.length && index == this.index ">'+
                 '<li class="no-goods-list" >此类下暂时没有商品</li>'+
              '</ul>'+
            '</div>'+
          '</div>'+
      '</div>'+
    '</div>',
})
Vue.component('imageLink', {
  props: ['content'],
  data: function () {
  return {
    data:{
    images: this.content || [
        {'linkTitle':'45345','linkName':'345354','linkUrl':'34534','chooseLink':'5445','dropDown':false,'thumbnail':'https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FgU9Jb9FlllqqotFp6AaMInVBmxC.png?imageView2/2/w/280/h/280/q/75/format/webp','image_id':'','link_type':'','link_id':''},
        {'linkTitle':'45345','linkName':'345354','linkUrl':'34534','chooseLink':'5445','dropDown':false,'thumbnail':'https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FgU9Jb9FlllqqotFp6AaMInVBmxC.png?imageView2/2/w/280/h/280/q/75/format/webp','image_id':'','link_type':'','link_id':''},
        {'linkTitle':'45345','linkName':'345354','linkUrl':'34534','chooseLink':'5445','dropDown':false,'thumbnail':'https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FgU9Jb9FlllqqotFp6AaMInVBmxC.png?imageView2/2/w/280/h/280/q/75/format/webp','image_id':'','link_type':'','link_id':''},
        {'linkTitle':'45345','linkName':'345354','linkUrl':'34534','chooseLink':'5445','dropDown':false,'thumbnail':'https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FgU9Jb9FlllqqotFp6AaMInVBmxC.png?imageView2/2/w/280/h/280/q/75/format/webp','image_id':'','link_type':'','link_id':''},
        {'linkTitle':'45345','linkName':'345354','linkUrl':'34534','chooseLink':'5445','dropDown':false,'thumbnail':'https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FgU9Jb9FlllqqotFp6AaMInVBmxC.png?imageView2/2/w/280/h/280/q/75/format/webp','image_id':'','link_type':'','link_id':''},
     ]
    }
  }
  },
  methods: {
  increment: function () {
    // this.counter += 1
    // this.$emit('increment')
  }
  },
  mounted: function(){
  },
  template: '<div>'+
    '<ul class="custom-nav-4 clearfix" style="min-height: 66px;">'+
      '<li v-for="item in data.images">'+
        '<a :href="item.linkUrl" v-if="item.thumbnailFlag != 0">'+
          '<span class="nav-img-wap">'+
            '<img class="js-lazy" v-lazy="item.thumbnail">'+
          '</span>'+
          '<span class="title" v-html="item.linkTitle"></span>'+
        '</a>'+
      '</li>'+
    '</ul>'+
  '</div>',
})
Vue.component('guanWang', {
  props: ['content'],
  data: function () {
    //console.log(this)
  return {
    list:this.content
  }
  },
  methods: {
  increment: function () {
    // this.counter += 1
    // this.$emit('increment')
  }
  },
    mounted: function(){
      this.$nextTick(function(){
        $(this.$el).find('.swiper-wrapper').css('width',$(this.$el).find('.swiper-slide').width() * this.list.lists.length);
        // $('.container').css('backgroundImage','url('+ imgUrl + this.list.bg_image +')');
        $('.container').css({
          'backgroundImage' : 'url('+ imgUrl + this.list.bg_image +')',
          'background-repeat':'no-repeat',
          'background-size':'cover',
          'background-position':'center top'
        });
      })
    },
    template: '<div>'+
    '<div id="wxdesc" class="tpl-fbb">'+
      '<div class="swiper-container">'+
        '<div class="swiper-wrapper clearfix" style="padding-left: 0px; padding-right: 0px; height: 188px; transition-duration: 0.3s; transform: translate3d(0px, 0px, 0px);">'+
          '<div class="swiper-slide tpl-fbb-item done swiper-slide-visible swiper-slide-active" v-for="item in list[\'lists\']" style="width:auto;">'+
            '<a :href="item.linkUrl">'+
              '<div class="tpl-fbb-item-wrap" v-bind:style="{background:\'url(\'+ imgUrl + item.bg_image +\')\'}">'+
                '<div class="tpl-fbb-item-name" v-html="item.title"></div>'+
                '<div class="tpl-fbb-item-line"></div>'+
                '<div class="tpl-fbb-item-icon" v-if="item.icon">'+
                  '<img :src="item.icon" width="30" height="30">'+
                '</div>'+
                '<div class="tpl-fbb-item-text" v-html="item.desc"></div>'+
                '<div class="tpl-fbb-item-date" v-html="item.tag"></div>'+
              '</div>'+
            '</a>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>'+
  '</div>',
})
Vue.component("seckill",{
  props: ['list'],
  data: function(){
    return {
      imgUrl:imgUrl, 
      host: host,
      secKill: 0,//秒杀状态 0 未开始 1 开始 2 结束
      productStatus: 0,//商品状态 0已售罄 1 正常销售 
      days: '00',
      hours: '00',
      minutes: '00',
      seconds: '00',
      wid:wid//店铺id
    }
  },
  created: function(){
    //console.log(this.list)
    //判断商品是否还有库存
    var stock = this.list.total_seckill_stock;
    this.productStatus = parseInt(stock) > 0 ? 1 : 0;
    //秒杀倒计时
    var date = new Date(this.list['content'][0]['now_at'].replace(/-/g,'/'));
    var newTime = date.getTime();//现在时间  时间戳
    var startTime = new Date(this.list['content'][0]['start_at'].replace(/-/g,'/'));//秒杀开始时间
    var endTime = new Date(this.list['content'][0]['end_at'].replace(/-/g,'/'));//秒杀结束时间
    var residueTime = 0;//剩余时间
    startTime = startTime.getTime();//时间戳
    endTime = endTime.getTime();
    this.days = '00';
    this.hours = '00';
    this.minutes = '00';
    this.seconds = '00';
    //单转双
    function evenNum( num ){
       num = num < 10 ? "0" + num : num;
       return num;
    }
    //倒计时函数
    function getrtime(that,time){
      var EndTime= new Date(time);
      var t =EndTime.getTime() - newTime;
      if(t >= 0){
        var d=evenNum(Math.floor(t/1000/60/60/24));
        var h=evenNum(Math.floor(t/1000/60/60%24));
        var m=evenNum(Math.floor(t/1000/60%60));
        var s=evenNum(Math.floor(t/1000%60));
        that.days = d;
        that.hours = h;
        that.minutes = m;
        that.seconds = s;
        setTimeout(function(){
          getrtime(that,time);
          newTime += 1000;
        },1000);
      }  
    }
    if( startTime > newTime ){//未开启活动
      this.secKill = 0;
      getrtime(this,startTime);
    }else if( startTime <= newTime && newTime <= endTime ){//活动开启
      this.secKill = 1;
      getrtime(this,endTime);
    }else{//活动结束
      this.secKill = 2;
    }
    //秒杀活动使失效
    if(this.list['content'][0]['invalidate_at'] != "0000-00-00 00:00:00"){
      this.secKill = 2;
    }
  },
  template:'<div>'+
      '<div class="ump-seckill js-ump-seckill" data-alias="1hvqpnbvg" data-activity-id="183957" style="min-height: 349px;position:relative;background-color: #ffffff">'+
        '<a :href="host+\'shop/seckill/detail/\'+wid+\'/\'+list[\'content\'][0][\'id\']" >'+
          '<div class="thumb-wrap">'+
            '<img class="thumb" :src="imgUrl+list[\'content\'][0][\'product\'][\'img\']">'+
          '</div>'+
          '<div class="goods-activity" style="position:relative;">'+
            '<div class="activity-info clearfix">'+
              '<div class="activity-price current-price">'+
                 '<span class="price-title">秒杀</span>¥<i class="js-goods-price price">{{list[\'min_seckill_price\']}}</i>'+
              '</div>'+ 
              '<div class="original-price">'+
                 '¥{{list[\'content\'][0][\'product\'][\'price\']}}'+
              '</div>'+
              '<div class="overview-countdown">'+
                 '<div class="countdown-title">距秒杀{{secKill?\'结束\':\'开始\'}}还剩余</div>'+
                 '<div class="js-time-count-down countdown"><span>{{days}}</span> 天 <span>{{hours}}</span> 时 <span>{{minutes}}</span> 分 <span>{{seconds}}</span> 秒</div>'+
              '</div>'+
            '</div>'+
            '<h3 class="title ellipsis">{{list[\'content\'][0][\'product\'][\'title\']}}</h3>'+
            // '<span class="btn-red btn-activity">立即抢购</span>'+
            '<span class="tag tag-red tag-activity ">立即抢购</span>'+
            '<span class="activity-tips text-cancel">剩余：{{list[\'total_seckill_stock\']}}件</span>'+
          '</div>'+
        '</a>'+
        '<div v-if= "!productStatus || secKill == 2" style="position:absolute;top:0;left:0;bottom:0;right:0;background-color:rgba(0,0,0,0.3);">'+
          '<img v-if= "secKill != 2" style="position:absolute;top:calc(50% - 60px);left:calc(50% - 60px);width:120px;height:120px; transform:rotate(-30deg);" :src="imgUrl+\'static/images/sellOut.png\'"/>'+
          '<img v-if= "secKill == 2" style="position:absolute;top:calc(50% - 60px);left:calc(50% - 60px);width:120px;height:120px; transform:rotate(-30deg);" :src="imgUrl+\'static/images/end.png\'"/>'+
        '</div>'+
      '</div>'+
    '</div>',
});
//视频组件
Vue.component("cvideo",{
    props:["list"],
    methods:{
      showVideo:function(list){
        $('#shop-nav').hide();
        $('.js-footer-auto-ele').hide();
        this.$set(this.list,'videoShow',true)
      },
      closeVideo:function(list){
        list.videoShow = false;
        $('#shop-nav').show();
        $('.js-footer-auto-ele').show();
      }
    },
    created:function(){
        // console.log(this.list)
    },
    template:'<div class="video_bg"><img class="default_img" @click="showVideo(list)" v-if="!list.videoShow" :src="list.videoItem.file_cover ? imgUrl+list.videoItem.file_cover : \'\/hsshop\/image\/static\/video_bg.jpg\'">'+
              '<div class="play_icon" @click.stop="showVideo(list)" v-if="!list.videoShow"></div>'+
              '<div class="play_video_model" v-if="list.videoShow">'+
              '<video width="100%" height="auto" preload="auto" x-webkit-airplay="true" x5-playsinline="true" webkit-playsinline="true" playsinline="true" controls="" autoplay="autoplay">'+
                    '<source :src="videoUrl+list.videoItem.FileInfo.path" :type="list.videoItem.FileInfo.type">'+
                '</video>'+
                '<img @click.stop="closeVideo(list)" src="'+ APP_SOURCE_URL + 'shop/images/close@2x.png' +'">' +  
              '</div>'+
            '</div>',
})
function getScrollTop(){
  var scrollTop=0;
  if(document.documentElement&&document.documentElement.scrollTop){
    scrollTop=document.documentElement.scrollTop;
  }
  else if(document.body){
    scrollTop=document.body.scrollTop;
  }
  return scrollTop;
}
function GenNonDuplicateID(){
  return Math.random().toString(36).substr(3)
}

//魔方组件 @author huoguanghui
Vue.component("cube",{
    props:["list","wid","host"],
    data: function () {
        return {
            screenWidth:0,
            height:0,
            mg: 0,
        }
    },
    created:function(){
        var list = this.list;
        if (list.margin == undefined) {
            list.margin = 0;
        }
        this.mg = '0 ' + list.margin + 'px';
        this.screenWidth = $(window).width() > 540 ? (540-list.margin*2) : ($(window).width()-list.margin*2);
        var screenWidth = this.screenWidth;
        
        //魔方各例定位，宽高数据处理
        for( var i = 0;i < list.position.length;i ++ ){
            if( list.telType == 0 || list.telType == 1 || list.telType == 2 || list.telType == 7 ){//魔方特例
                this.height = (screenWidth-(list.position.length-1)*list.margin)/list.position.length*list.aspectRatio + 'px';
                list.position[i].top    = 0;
                list.position[i].left   = (list.position[i].left == 0 ? 0 : (list.position[i].left*(screenWidth-(list.position.length-1)*list.margin)/list.position.length+list.position[i].left*list.margin)) + 'px';
                list.position[i].width  = (screenWidth-(list.position.length-1)*list.margin)/list.position.length + 'px';
                list.position[i].height = (screenWidth-(list.position.length-1)*list.margin)/list.position.length*list.aspectRatio + 'px';
            }else if(list.telType == 8){
                this.height = screenWidth/2 + 'px';
                list.position[i].top    = (list.position[i].top == 0 ? 0 : list.position[i].top*(screenWidth-list.margin)/4+list.position[i].top/2*list.margin) +'px';
                list.position[i].left   = (list.position[i].left == 0 ? 0 : list.position[i].left*(screenWidth-list.margin)/4+list.position[i].left/2*list.margin) +'px';
                list.position[i].width  = (list.position[i].width == 4 ? screenWidth : list.position[i].width == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
                list.position[i].height = (list.position[i].height == 4 ? screenWidth : list.position[i].height == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
            }
            else{//魔方普通
                this.height = screenWidth + 'px';
                list.position[i].top    = (list.position[i].top == 0 ? 0 : list.position[i].top*(screenWidth-list.margin)/4+list.position[i].top/2*list.margin) +'px';
                list.position[i].left   = (list.position[i].left == 0 ? 0 : list.position[i].left*(screenWidth-list.margin)/4+list.position[i].left/2*list.margin) +'px';
                list.position[i].width  = (list.position[i].width == 4 ? screenWidth : list.position[i].width == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
                list.position[i].height = (list.position[i].height == 4 ? screenWidth : list.position[i].height == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
            }
            switch (list.content[i].type) {
                case 1: //商品
                    list.content[i].linkUrl = host + "shop/product/detail/"+this.wid+"/"+list.content[i].id;
                    break;
                case 2: //微页面
                    list.content[i].linkUrl = host + "shop/microPage/index/"+this.wid+"/"+list.content[i].id;
                    break;
                case 3: //享立减
                    console.log("尚未开放享立减功能")
                    break;
                case 4: //拼团
                    list.content[i].linkUrl = host + "shop/grouppurchase/detail/"+list.content[i].id+"/"+this.wid;
                    break;
                case 5: //秒杀
                    list.content[i].linkUrl = host + "shop/seckill/detail/"+this.wid+"/"+list.content[i].id;
                    break;
                case 6: //签到
                    list.content[i].linkUrl = host + "shop/point/sign/"+this.wid;
                    break;
                case 8: //自定义链接
                    list.content[i].linkUrl = list.content[i].linkTitle;
                    break;
                case 7: //商品分组
                    list.content[i].linkUrl = host + "shop/group/detail/"+this.wid + '/' + list.content[i].id;
                    break;
                case 10: //优惠券
                    list.content[i].linkUrl = host + "shop/activity/couponDetail/"+this.wid + '/' + list.content[i].id;
                    break;
                default:
                    // statements_def
                    break;
            }
        }
    },
    template:   '<div class="cube">'+
                    '<div class="row" :style="{height:height,margin: mg}">'+
                        '<div class="cube-row" v-for="(list1,index1) in list.position" :style="{top:list1.top,left: list1.left,width: list1.width,height: list1.height}">'+
                            '<a :href="list.content[index1].type == 0?\'javascript:void(0);\':list.content[index1].linkUrl" :style="{backgroundImage: \'url(\'+imgUrl + list.content[index1].img+\')\'}" :data-src="imgUrl + list.content[index1].img" :class="{\'J_parseImg\':list.content[index1].type == 0 && (list.resize_image==undefined || (list.resize_image!=undefined && list.resize_image ==1))}"></a>'+
                            '<div class="cube-title" v-if="list.addTitle && list.content[index1].title">{{list.content[index1].title}}</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'
})
// 联系方式组件
Vue.component("cmobile",{
    props:["list","reqfrom"],
    data:function(){
        return {
            tel:'tel:',
            decor:'#mp.weixin.qq.com',
            aliapp: 'aliapp'
        }
    },
    created:function(){
        
    },
    methods: {
        makePhoneCall: function (phone) {
            my.postMessage({phone_number:phone});
        }
    },
    template:'<div class="mobile-wrap">\
    <p class="mobile-title">{{list.title}}</p>\
    <div v-if="reqfrom != aliapp">\
        <div v-if="list.mobileStyle==2">\
            <a :href="tel+item.area_code+item.mobile" class="flexBox default-mobile" v-for="(item,index) in list.lists">\
                <img :src="item.icon">\
                <span>{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span>\
            </a>\
        </div>\
        <div v-else="list.mobileStyle==1">\
            <a :href="tel+item.area_code+item.mobile" class="userdefined" v-for="(item,index) in list.lists">\
                <img :src="item.image" />\
                <p class="image-shadow" v-show="item.imageShadowShow == 1"><span>{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span></p>\
            </a>\
        </div>\
    </div>\
    <div v-if="reqfrom == aliapp">\
        <div v-if="list.mobileStyle==2">\
            <div @click="makePhoneCall(item.area_code+item.mobile)" class="flexBox default-mobile" v-for="(item,index) in list.lists">\
                <img :src="item.icon">\
                <span>{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span>\
            </div>\
        </div>\
        <div v-else="list.mobileStyle==1">\
            <div @click="makePhoneCall(item.area_code+item.mobile)" class="userdefined" v-for="(item,index) in list.lists">\
                <img :src="item.icon" />\
                <p class="image-shadow" v-show="item.imageShadowShow == 1"><span>{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span></p>\
            </div>\
        </div>\
    </div>\
    <div v-if="!list.mobileStyle">\
    <div class="mobile-content" v-for="(item,index) in list.lists">\
    <a @click="makePhoneCall(item.area_code+item.mobile)" v-if="reqfrom==aliapp"><img :src="list.icon" class="mobile-icon"/><span class="calling">{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span>\
    </a>\
    <a :href="tel+item.area_code+item.mobile" v-else><img :src="list.icon" class="mobile-icon"/><span class="calling">{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span>\
    </a>\
    </div>\
    </div>\
</div>'
});

/** 
 * author huakang 2018/06/20
 * 享立减模块 首页插件
*/
Vue.component("shareRebate",{
    props: ['list'],
    data: function(){
      return {
        imgUrl:imgUrl, 
        host: host,
        productStatus: 0,//商品状态 0已售罄 1 正常销售 
        days: [],
        hours: [],
        minutes: [],
        seconds: [],
        isStart: [], // 0未开始  1已开始 
        wid:wid,//店铺id
        mid:mid//分享者id
      }
    },
    created: function(){
      //  console.log(this.list.activitys)
        //秒杀倒计时
        var date = (this.list['activitys'][0]?this.list['activitys'][0]['currentTime']:0)*1000;
        var newTime = date;//现在时间  时间戳
        var endTime1 = this.list['activitys'].length && Array.prototype.slice.call(this.list['activitys']).map(function(v){return (v.endTime?v.endTime:0)*1000});
        var startTime1 = this.list['activitys'].length && Array.prototype.slice.call(this.list['activitys']).map(function(v){return (v.startTime?v.startTime:0)*1000});
        this.days = ['00'];
        this.hours = ['00'];
        this.minutes = ['00'];
        this.seconds = ['00'];
        this.isStart = [0];
        //单转双
        function evenNum( num ){
            num = num < 10 ? "0" + num : num;
            return num;
        }
        //倒计时函数
        function getrtime(that,startArr,endArr){
            if(typeof startArr== 'number'){
                return false
            }
            var StartTime1 = startArr.map(function(v){return typeof v == 'number'?new Date(v):v});
            var EndTime1 = endArr.map(function(v){return typeof v == 'number'?new Date(v):v});
            var flag1 = StartTime1.map(function(v){return v.getTime()-newTime});
            var flag2 = EndTime1.map(function(v){return v.getTime()-newTime});
            var t1 = [];
            for (var j=0;j<flag1.length;j++){
                if (flag1[j]>0){
                    that.$set(that.isStart,[j],0);
                    t1.push(flag1[j]);
                } else if(flag1[j]<=0 && flag2[j]>=0){
                    that.$set(that.isStart,[j],1);
                    t1.push(flag2[j]);
                } else {
                    that.$set(that.isStart,[j],2);
                }
            }
            // console.log(t1);
            for(var i=0,l=t1.length;i<l;i++){
                if(t1[i]>=0){
                    var d=evenNum(Math.floor(t1[i]/1000/60/60/24));
                    var h=evenNum(Math.floor(t1[i]/1000/60/60%24));
                    var m=evenNum(Math.floor(t1[i]/1000/60%60));
                    var s=evenNum(Math.floor(t1[i]/1000%60));
                    that.$set(that.days,[i],d)
                    that.$set(that.hours,[i],h)
                    that.$set(that.minutes,[i],m)
                    that.$set(that.seconds,[i],s)
                }
            }
            setTimeout(function(){
                getrtime(that,startArr,endArr);
                newTime += 1000;
            },1000);
        }
        getrtime(this,startTime1,endTime1);
    },
    template:'<div>'+
        '<div class="ump-shareRebate js-ump-shareRebate" data-alias="1hvqpnbvg" style="position:relative;" v-if="list[\'activitys\'].length">'+'<div style="margin-top:8px" v-for="(item,idx) in list[\'activitys\']" v-if="isStart[idx]!=2">'+
            '<a :href="host+\'shop/product/detail/\'+wid+\'/\'+item[\'product_id\']+\'?activityId=\'+list.activity_id[idx]" class="flexBox">'+
            '<div class="shareRebate-left" style="width: 2.7rem;height: 2.7rem">'+
                '<div class="left-picture" style="width: 2.7rem;height: 2.7rem"><img v-lazy="imgUrl+item[\'activityImg\']"/></div>'+
                '<p>共{{item[\'attendCount\']}}人正在参与</p>'+
            '</div>'+
            '<div class="shareRebate-right">'+
                '<div class="share-content">'+
                    '<p class="title p">{{item[\'name\']}}</p>'+
                    '<p class="subtitle p">{{item[\'subtitle\']}}</p>'+
                    '<p class="countdown">{{isStart[idx]==0?"距开始":isStart[idx]==1?"距结束":"已结束"}}{{days[idx]}}天{{hours[idx]}}时{{minutes[idx]}}分{{seconds[idx]}}秒</p>'+
                    '<div class="price flexBox" style="bottom: 5px"><p class="flexBox"><span class="currentPrice">￥{{item[\'lowerPrice\']}}</span></p><button>{{item[\'buttonTitle\']}}</button></div>'+
                '</div>'+
            '</div>'+
          '</a>'+
        '</div>'+
      '</div></div>',
  });

/** 
 * author 华亢 at 2018/8/3
 * 插件 留言板
*/
Vue.component('infoBoard',{
    props:['list'],
    data:function(){
        return {
            imgUrl:imgUrl, 
            host: host,
            id:'',
            wid:wid,//店铺id
        }
    },
    methods:{
        isGo:function(data){
            switch(data){
                case 0:
                // // layer.msg('该活动只能参加一次')
                // alert('该活动只能参加一次')
                // ;break;
                case 1:
                case 2:
                location.href = this.host+'shop/activity/researchDetail/'+this.wid+'/'+this.id
                ;break;
            }
        }
    },
    created:function(){
      if(this.list.resList.length){
        this.id = this.list.resList[0].id
      }
    },
    template:'<div v-if="list.resList.length"><div class="infoBoard">\
    <a href="javascript:void(0);" class="info-board-wrap" @click="isGo(list.resList[0].times_type)">\
    <img :src="host+\'shop/static/images/reseach_img.png\'" class="board-logo"/>\
    {{list.resList[0].name}}\
    <div class="board-arrow"><img :src="host+\'shop/static/images/arrow@2x.png\'"/></div>\
    </a>\
    </div></div>'
})
/** 
 * author 华亢 at 2018/8/31
 * return type-"seckill_list" 秒杀列表
*/
var secKillProps = ['content','showBtn','showTimer','showTitle','btnStyle','remanentStyle','remanent','hideOut','hideEnd','idx'];

var secKillBig = {
    props:secKillProps,
    data:function(){
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isComing:'结束'
        }
    },
    created:function(){
        var date = new Date(this.content.seckill.now_at.replace(/-/g,'/'));
        var newTime = date.getTime();//现在时间  时间戳
        var startTime = new Date(this.content.seckill.start_at.replace(/-/g,'/'));//秒杀开始时间
        var endTime = new Date(this.content.seckill.end_at.replace(/-/g,'/'));//秒杀结束时间
        var residueTime = 0;//剩余时间
        startTime = startTime.getTime();//时间戳
        endTime = endTime.getTime();
        function getrtime(that,time){
            var EndTime= new Date(time);
            var t =EndTime.getTime() - newTime;
            if(t >= 0){
                var d=evenNum(Math.floor(t/1000/60/60/24));
                var h=evenNum(Math.floor(t/1000/60/60%24));
                var m=evenNum(Math.floor(t/1000/60%60));
                var s=evenNum(Math.floor(t/1000%60));
                that.days = d;
                that.hours = h;
                that.minutes = m;
                that.seconds = s;
                setTimeout(function(){
                getrtime(that,time);
                newTime += 1000;
                },1000);
            }  
        }
        function evenNum( num ){
            num = num < 10 ? "0" + num : num;
            return num;
        }
        if(this.content.seckill.seckill_status == "COMING"){
            getrtime(this,startTime);
            this.isComing = '开始';
        }else{
            getrtime(this,endTime);
            this.isComing = '结束';
        }
        
    },
    methods:{
        goActivity:function(){
            if(this.secStatus){
                return false
            }else{
                location.href = host+'shop/seckill/detail/'+wid+'/'+this.content.seckill.id
            }
        }
    },
    computed:{
        btnType:function(){
            return judgeBtn(this.content.seckill.seckill_status,this.btnStyle)
        },
        persent:function(){
            var mount = this.content.seckill.seckill_sold_num + this.content.seckill.seckill_stock;
            var per = Math.ceil(this.content.seckill.seckill_sold_num*100/mount);
            if(this.remanentStyle == 2){
                return per+'%'
            }
        },
        headUrl:function(){
            return host+this.content.product.img
        },
        secStatus:function(){
            switch(this.content.seckill.seckill_status){
                case 'COMING':;
                case 'NORMAL':return false;
                case 'EXPIRED':;
                case 'SELLOUT':return true;
            }
        },
        secCountDown:function(){
            switch(this.content.seckill.seckill_status){
                case 'NORMAL':return 'countdown';
                case 'COMING':return 'CountDownGreen';
                case 'EXPIRED':;
                case 'SELLOUT':return 'CountDownGrey';
            }
        },
        statusUrl:function(){
            if(this.content.seckill.seckill_status == 'EXPIRED'){
                return host+'shop/static/images/sec_the_end.png'
            }else if(this.content.seckill.seckill_status == 'SELLOUT'){
                return host+'shop/static/images/sec_the_out.png'
            }
            
        },
        judgeShow:function(){
            if(this.hideOut && this.content.seckill.seckill_status == 'SELLOUT'){//售罄
                return false
            }
            if(this.hideEnd && this.content.seckill.seckill_status == 'EXPIRED'){//结束
                return false
            }
            return true
        }
    },
    template:'<div style="background-color: #ffffff;">\
    <div v-show="judgeShow"><div class="seckill-big-top img-wrap">\
    <img v-lazy="headUrl"/>\
    <div class="statusImage" v-show="secStatus"><img v-lazy="statusUrl"></div>\
    <div class="flex-box big-countdown" :class="secCountDown" v-show="showTimer"><p class="slogen">秒杀</p><p>距{{isComing}}仅剩</p><p class="countdown-timer">\
    <span class="sec-timer">{{days}}</span>天\
    <span class="sec-timer">{{hours}}</span>:\
    <span class="sec-timer">{{minutes}}</span>:\
    <span class="sec-timer">{{seconds}}</span></p></div></div>\
    <div class="seckill-big-bottom"><div class="seckill-big-title" v-show="showTitle">\
    <p class="seckill-title">{{content.product.title}}</p>\
    <p class="seckill-tip red"><span>立减{{content.seckill.seckill_discount_price}}元</span></p></div>\
    <div class="seckill-big-middle flex-box"><div class="red sec-price">\
    秒杀价： ￥<span>{{content.seckill.seckill_price_dollar}}</span>.{{content.seckill.seckill_price_cent}}</div>\
    <div class="btn-wrap" v-show="showBtn"><button :class="btnType" @click="goActivity">去抢购</button></div></div>\
    <div class="seckill-big-floor flex-box">\
    <p class="oprice">￥{{content.seckill.seckill_oprice}}</p><div class="stock-wrap" v-show="remanent">\
    <p class="stock" v-if="remanentStyle==1">剩余<span class="red">{{content.seckill.seckill_stock}}</span>件</p>\
    <div class="stock-process clearfix" v-if="remanentStyle==2"><span class="stock-process-tip">\
    已售{{persent}}</span><div class="process-wrap pull-right"><p class="process-content" :style="{width:persent}"></p>\
    </div></div></div></div></div>\
    </div>'
}

var secKillSmall = {
    props:secKillProps,
    data:function(){
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isComing:'结束'
        }
    },
    methods:{
        goActivity:function(){
            if(this.secStatus){
                return false
            }else{
                location.href = host+'shop/seckill/detail/'+wid+'/'+this.content.seckill.id
            }
        }
    },
    created:function(){
        var date = new Date(this.content.seckill.now_at.replace(/-/g,'/'));
        var newTime = date.getTime();//现在时间  时间戳
        var startTime = new Date(this.content.seckill.start_at.replace(/-/g,'/'));//秒杀开始时间
        var endTime = new Date(this.content.seckill.end_at.replace(/-/g,'/'));//秒杀结束时间
        var residueTime = 0;//剩余时间
        startTime = startTime.getTime();//时间戳
        endTime = endTime.getTime();
        this.days = '00';
        this.hours = '00';
        this.minutes = '00';
        this.seconds = '00';
        function getrtime(that,time){
            var EndTime= new Date(time);
            var t =EndTime.getTime() - newTime;
            if(t >= 0){
                var d=evenNum(Math.floor(t/1000/60/60/24));
                var h=evenNum(Math.floor(t/1000/60/60%24));
                var m=evenNum(Math.floor(t/1000/60%60));
                var s=evenNum(Math.floor(t/1000%60));
                that.days = d;
                that.hours = +h+d*24;
                that.minutes = m;
                that.seconds = s;
                setTimeout(function(){
                getrtime(that,time);
                newTime += 1000;
                },1000);
            }  
        }
        function evenNum( num ){
            num = num < 10 ? "0" + num : num;
            return num;
        }
        if(this.content.seckill.seckill_status == "COMING"){
            getrtime(this,startTime);
            this.isComing = '开始';
        }else{
            getrtime(this,endTime);
            this.isComing = '结束';
        }
    },
    computed:{
        btnType:function(){
            return judgeBtn(this.content.seckill.seckill_status,this.btnStyle)
        },
        persent:function(){
            var abc = this.content.seckill.seckill_sold_num + this.content.seckill.seckill_stock;
            abc = this.content.seckill.seckill_sold_num*100/abc;
            if(this.remanentStyle == 2){
                return Math.ceil(abc)+'%'
            }
        },
        headUrl:function(){
            return host+this.content.product.img
        },
        secStatus:function(){
            switch(this.content.seckill.seckill_status){
                case 'COMING':;
                case 'NORMAL':return false;
                case 'EXPIRED':;
                case 'SELLOUT':return true;
            }
        },
        secCountDown:function(){
            switch(this.content.seckill.seckill_status){
                case 'NORMAL':return 'countdown';
                case 'COMING':return 'CountDownGreen';
                case 'EXPIRED':;
                case 'SELLOUT':return 'CountDownGrey';
            }
        },
        statusUrl:function(){
            if(this.content.seckill.seckill_status == 'EXPIRED'){
                return host+'shop/static/images/sec_the_end.png'
            }else if(this.content.seckill.seckill_status == 'SELLOUT'){
                return host+'shop/static/images/sec_the_out.png'
            }
            
        },
        judgeShow:function(){
           // console.log(this.hideOut,this.hideEnd)
            if(this.hideOut && this.content.seckill.seckill_status == 'SELLOUT'){//售罄
                return false
            }
            if(this.hideEnd && this.content.seckill.seckill_status == 'EXPIRED'){//结束
                return false
            }
            return true
        }
    },
    template:'<div style="background-color: #ffffff;margin-right: 0" :class="{fr:(idx % 2 != 0)}" class="small-size samll_box" v-show="judgeShow">\
    <div class="seckill-small-top img-wrap small_box_img">\
    <img :src="headUrl"/><div class="statusImage" v-show="secStatus"><img :src="statusUrl"></div>\
    <div class="flex-box small-countdown" :class="secCountDown" v-show="showTimer"><p>距{{isComing}}仅剩</p><p class="countdown-timer">\
    <span class="sec-timer">{{hours}}</span>:<span class="sec-timer">{{minutes}}</span>:<span class="sec-timer">{{seconds}}</span></p></div></div>\
    <div class="seckill-big-bottom"><div class="seckill-big-title" v-show="showTitle"><p class="seckill-title">{{content.product.title}}</p>\
    <p class="seckill-tip red"><span>立减{{content.seckill.seckill_discount_price}}元</span></p></div>\
    <div class="seckill-big-middle flex-box">\
    <div class="red sec-price"> 秒杀价： ￥<span>{{content.seckill.seckill_price}}</span>\
    <p class="oprice">活动结束价￥{{content.seckill.seckill_oprice}}</p></div></div>\
    <div class="seckill-big-floor flex-box" style="font-size: 0"><div class="stock-wrap" v-show="remanent">\
    <p class="stock" v-if="remanentStyle==1">剩余<span class="red">{{content.seckill.seckill_stock}}</span>件</p>\
    <div class="stock-process clearfix" v-if="remanentStyle==2"><span class="stock-process-tip">已售{{persent}}</span>\
    <div class="process-wrap"><p class="process-content" :style="{width:persent}"></p></div>\
    </div></div><div class="btn-wrap" v-show="showBtn"><button :class="btnType" @click="goActivity">去抢购</button></div></div></div>'
}

var secKillList = {
    props:secKillProps,
    data:function(){
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isComing:'仅剩'
        }
    },
    created:function(){
        var date = new Date(this.content.seckill.now_at.replace(/-/g,'/'));
        var newTime = date.getTime();//现在时间  时间戳
        var startTime = new Date(this.content.seckill.start_at.replace(/-/g,'/'));//秒杀开始时间
        var endTime = new Date(this.content.seckill.end_at.replace(/-/g,'/'));//秒杀结束时间
        var residueTime = 0;//剩余时间
        startTime = startTime.getTime();//时间戳
        endTime = endTime.getTime();
        this.days = '00';
        this.hours = '00';
        this.minutes = '00';
        this.seconds = '00';
        function getrtime(that,time){
            var EndTime= new Date(time);
            var t =EndTime.getTime() - newTime;
            if(t >= 0){
                var d=evenNum(Math.floor(t/1000/60/60/24));
                var h=evenNum(Math.floor(t/1000/60/60%24));
                var m=evenNum(Math.floor(t/1000/60%60));
                var s=evenNum(Math.floor(t/1000%60));
                that.days = d;
                that.hours = +h+d*24;;
                that.minutes = m;
                that.seconds = s;
                setTimeout(function(){
                getrtime(that,time);
                newTime += 1000;
                },1000);
            }  
        }
        function evenNum( num ){
            num = num < 10 ? "0" + num : num;
            return num;
        }
        if(this.content.seckill.seckill_status == "COMING"){
            getrtime(this,startTime);
            this.isComing = '距开始';
        }else{
            getrtime(this,endTime);
        }
    },
    methods:{
        goActivity:function(){
            if(this.secStatus){
                return false
            }else{
                location.href = host+'shop/seckill/detail/'+wid+'/'+this.content.seckill.id
            }
        }
    },
    computed:{
        btnType:function(){
            return judgeBtn(this.content.seckill.seckill_status,this.btnStyle)
        },
        persent:function(){
            var abc = this.content.seckill.seckill_sold_num + this.content.seckill.seckill_stock;
            abc = this.content.seckill.seckill_sold_num/abc;
            if(this.remanentStyle == 2){
                return Math.ceil(abc)+'%'
            }
        },
        headUrl:function(){
            return host+this.content.product.img
        },
        secStatus:function(){
            switch(this.content.seckill.seckill_status){
                case 'COMING':;
                case 'NORMAL':return false;
                case 'EXPIRED':;
                case 'SELLOUT':return true;
            }
        },
        secCountDown:function(){
            switch(this.content.seckill.seckill_status){
                case 'NORMAL':return 'countdown';
                case 'COMING':return 'CountDownGreen';
                case 'EXPIRED':;
                case 'SELLOUT':return 'CountDownGrey';
            }
        },
        statusUrl:function(){
            if(this.content.seckill.seckill_status == 'EXPIRED'){
                return host+'shop/static/images/sec_the_end.png'
            }else if(this.content.seckill.seckill_status == 'SELLOUT'){
                return host+'shop/static/images/sec_the_out.png'
            }
            
        },
        judgeShow:function(){
           // console.log(this.hideOut,this.hideEnd)
            if(this.hideOut && this.content.seckill.seckill_status == 'SELLOUT'){//售罄
                return false
            }
            if(this.hideEnd && this.content.seckill.seckill_status == 'EXPIRED'){//结束
                return false
            }
            return true
        }
    },
    template:'<div style="background-color: #ffffff;" class="list-size flex-box" v-show="judgeShow">\
    <div class="seckill-list-top img-wrap"><img :src="headUrl"/><div class="statusImage" v-show="secStatus"><img :src="statusUrl"></div>\
    <div class="flex-box list-countdown" :class="secCountDown" v-show="showTimer"><p>{{isComing}}</p><p class="countdown-timer">\
    <span class="sec-timer">{{hours}}</span>:<span class="sec-timer">{{minutes}}</span>:<span class="sec-timer">{{seconds}}</span></p></div></div>\
    <div class="seckill-list-bottom flex-box"><div class="seckill-list-title" v-show="showTitle">\
    <p class="seckill-title">{{content.product.title}}</p><p class="seckill-tip red"><span>立减{{content.seckill.seckill_discount_price}}元</span></p></div>\
    <div class="seckill-list-content flex-box" style="padding-top: 0;height: 37px;position: relative;"><div class="red sec-price"> 秒杀价:￥<span>{{content.seckill.seckill_price}}</span></div>\
    <div class="btn-wrap" v-show="showBtn"><button style="position: absolute;bottom: 0;right: 0" :class="btnType" @click="goActivity">去抢购</button></div></div>\
    <div class="seckill-list-tip flex-box"><p class="oprice">活动结束价￥{{content.seckill.seckill_oprice}}</p><div class="stock-wrap" v-show="remanent">\
    <p class="stock" v-if="remanentStyle==1">剩余<span class="red">{{content.seckill.seckill_stock}}</span>件</p><div class="stock-process clearfix" v-if="remanentStyle==2">\
    <span class="stock-process-tip">已售{{persent}}</span><div class="process-wrap"><p class="process-content" :style="{width:persent}"></p></div>\
    </div></div></div></div></div>'
}

Vue.component('seckillList',{
    props:['list'],
    computed:{
        type:function(){
            // 加载哪个组件 我不知 后台不知 客户知
            switch(this.list.listStyle.toString()){
                case '1':return 'secKillBig';
                case '2':return 'secKillSmall';
                case '3':return 'secKillList';
            }
        }
    },
    methods:{
        isGo:function(num){
            var o = this.list.seckillList[num].seckill;
            if(o.seckill_status == "NORMAL" || o.seckill_status == "COMING"){
                location.href = host+'shop/seckill/detail/'+wid+'/'+ o.id
            }
        }
    },
    components:{
        secKillBig:secKillBig,
        secKillSmall:secKillSmall,
        secKillList:secKillList,
    },
    template:'<div class="seckill-list clearfix">\
        <a class="clearfix" v-for="(item,idx) in list.seckillList" :class="{pullLeft:(list.listStyle==2),smallSize:(list.listStyle==2)}" @click="isGo(idx)">\
            <component :is="type" :key="idx" :content="item"\
             :hideOut="list.hideOut"\
             :hideEnd="list.hideEnd"\
             :showBtn="list.showBtn"\
             :showTimer="list.showTimer"\
             :showTitle="list.showTitle"\
             :btnStyle="list.btnStyle"\
             :remanentStyle="list.remanentStyle"\
             :remanent="list.remanent"\
             :idx="idx"></component>\
        </a>\
    </div>'
})



//判断秒杀按钮选择何种样式
function judgeBtn(status,num){
    switch(status){
        case 'NORMAL':
        switch(num.toString()){
            case '1':return ['btn-red','btn-radius'];
            case '2':return ['btn-white','btn-radius'];
            case '3':return ['btn-no-radius','btn-red'];
            case '4':return ['btn-no-radius','btn-white']
        };
        case 'COMING':
        switch(num.toString()){
            case '1':return ['btn-red-green','btn-radius'];
            case '2':return ['btn-white-green','btn-radius'];
            case '3':return ['btn-no-radius','btn-red-green'];
            case '4':return ['btn-no-radius','btn-white-green']
        };
        case 'EXPIRED':;
        case 'SELLOUT':
        switch(num.toString()){
            case '1':return ['btn-red-grey','btn-radius'];
            case '2':return ['btn-white-grey','btn-radius'];
            case '3':return ['btn-no-radius','btn-red-grey'];
            case '4':return ['btn-no-radius','btn-white-grey']
        };;
    }
}
//add by 韩瑜 2018-9-20 商品分组模板组件
Vue.component('groupPage',{
    props:['list'],
    data:function(){
        return {
        	leftIndex:0,//商品分组左侧index
			    leftNav:"",//左侧列表
        }
    },
    created:function(){
      //商品分组模板左侧导航
	    this.list.classifyList[0].isActive = true
    },
    methods:{
      //商品分组模板页点击商品分组左侧
      chooseGroup:function(item,list,index){
      	var that = this
      	if(list.classifyList.length>0){
					for(var i = 0;i<list.classifyList.length;i++){
						list.classifyList[i]['isActive'] = false;
					}
		    }
      	that.leftIndex = index
      	item.isActive = true
		  },
		},
    components:{

    },
    template:
    '<div class="group_wrap" style="min-height: 323px;">\
          <div class="list_left">\
            <p class="list_left_item" :class="item.isActive ? \'active\' : \'\'" v-for="(item,index) in list.classifyList" @click="chooseGroup(item,list,index)">\
              <span v-text="item.category_name"></span>\
            </p>\
          </div>\
          <div class="list_right" >\
            <img class="banner" :src="host + list.classifyList[leftIndex].thumbnail" alt="" />\
            <p><span v-text="list.classifyList[leftIndex].category_name"></span></p>\
            <div class="list_right_warp" v-for="(ite,iteindex) in list.classifyList[leftIndex].subClassifyList">\
              <a :href="ite.linkUrl">\
                <div class="list_right_item">\
                  <img :src="imgUrl + ite.thumbnail +thump_200" alt="" />\
                  <span v-text="ite.category_name"></span>\
                </div>\
              </a>\
            </div>\
          </div>\
        </div>'
})
//end

//add by 韩瑜 2018-10-24 
//官网2组件
Vue.component('guanText',{
    props:['list'],
    data:function(){
        return {
					textList:[],
        }
    },
    created:function(){
    	if(this.list.lists.length && this.list.lists[0].lists.length){
    		for(var i = 0;i<this.list.lists[0].lists.length;i++){
    			this.textList.push(this.list.lists[0].lists[i])
    		}
    	}
    },
    methods:{
    	getTextList:function(kind,list){
        this.textList = [];
        if(list.lists.length){
            for(var n = 0;n<list.lists.length;n++){
                list.lists[n]['isActive'] = false;
            }
        }
        if(kind['lists'].length){
            for(var n = 0;n<kind['lists'].length;n++){
                this.textList.push(kind['lists'][n]);
            }
        }
        kind.isActive = true;
      },
		},
    components:{

    },
    template:
    '<div class="ti_list">\
		    <div class="swiper-container" v-if="list[\'slideLists\'].length">\
		        <div class="swiper-wrapper" style="height:auto;width:100%;">\
		            <a class="swiper-slide" style="text-align:center" v-for="item in list[\'slideLists\']">\
		                <img class="js-res-load" style="height:auto;width:100%" :src="item.cover">\
		                <h3 class="title" style="position:absolute;bottom:0;width:100%;background-color:rgba(0,0,0,0.4);color:#fff;line-height:30px;text-align:left;padding-left:10px" v-html="item.title"></h3>\
		            </a>\
		        </div>\
		        <div class="swiper-pagination guanwang-swiper-pagination"></div>\
		    </div>\
		    <div class="js-tabber-tags tabber tabber-bottom red clearfix tabber-n4  ">\
		        <div class="custom-tags-more js-show-all-tags"></div>\
		        <div id="J_tabber_scroll_wrap" class="custom-tags-scorll clearfix">\
		            <div id="J_tabber_scroll_con" class="custom-tags-scorll-con">\
		                <a href="javascript:;" v-for="(kind,innerIndex) in list[\'lists\']"  :class="kind.isActive ? \'current\':\'\'" :style="{\'width\':list.width + \'%\'}" @click="getTextList(kind,list)">{{kind.title}}</a>\
		            </div>\
		        </div>\
		    </div>\
		    <div>\
		        <ul>\
		            <li v-for="item in textList">\
		                <a :href="item.url">\
		                    <div class="image">\
		                        <img :src="item.cover">\
		                    </div>\
		                    <div class="ti_content">\
		                        <p class="image_title">{{item.title}}</p>\
		                        <p class="image_desc">{{item.digest}}</p>\
		                    </div>\
		                </a>\
		            </li>\
		        </ul>\
		    </div>\
		</div>'
})
//美妆小店头部组件
Vue.component('guanHeader',{
    props:['list'],
    data:function(){
        return {

        }
    },
    created:function(){

    },
    methods:{

		},
    components:{

    },
    template:
			'<div class="tpl-shop">\
        <div class="tpl-shop-header" :style="list.bg_image ? {backgroundImage:\'url(\'+list.bg_image+\')\'} : {backgroundColor:list.bg_color}">\
            <div class="tpl-shop-title"></div>\
            <div class="tpl-shop-avatar">\
                <img :src="list.logo" alt="头像">\
            </div>\
        </div>\
        <div class="tpl-shop-content">\
            <ul class="clearfix">\
                <li class="js-order">\
                    <a :href="\'/shop/order/index/\'+wid">\
                        <span class="count user"></span>\
                        <span class="text">我的订单</span>\
                    </a>\
                </li>\
            </ul>\
        </div>\
    </div>'
})
//富文本组件
Vue.component('richText',{
    props:['list'],
    data:function(){
        return {

        }
    },
    created:function(){

    },
    methods:{

		},
    components:{

    },
    template:
			'<div class="custom-richtext js-custom-richtext js-lazy-container" :style="{background:list.bgcolor}">\
	        <div class="rich_text_html" v-html = "list[\'content\']">\
	        </div>\
	    </div>'
})
//图片广告组件
Vue.component('imageAd',{
    props:['list'],
    data:function(){
        return {

        }
    },
    created:function(){

    },
    methods:{

		},
    components:{

    },
    template:
			'<div class="image_ad">\
	        <ul class="custom-image clearfix js-image-ad-seperated js-view-image-list js-lazy-container" v-if="list[\'advsListStyle\'] ==3">\
	            <li v-for = "image in list[\'images\']" v-if="list[\'advSize\']==1">\
	                <a :href="image.linkUrl ? image.linkUrl : \'javascript:void(0);\'">\
	                    <h3 class="title" v-html="image.title" v-if="image.title"></h3>\
	                    <img class="js-lazy js-view-image-item" :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{\'J_parseImg\':!image.linkUrl && (list[\'resize_image\']==undefined || (list[\'resize_image\']!=undefined&&list[\'resize_image\']==1))}">\
	                </a>\
	            </li>\
	            <li class="custom-image-small" v-for = "image in list[\'images\']" v-if="list[\'advSize\']==2">\
	                <a :href="image.linkUrl ? image.linkUrl : \'javascript:void(0);\'">\
	                    <div>\
	                        <h3 class="title" v-html="image.title" v-if="image.title"></h3>\
	                        <img class="js-lazy" :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{\'J_parseImg\':!image.linkUrl && (list[\'resize_image\']==undefined || (list[\'resize_image\']!=undefined&&list[\'resize_image\']==1))}">\
	                    </div>\
	                </a>\
	            </li>\
	        </ul>\
	        <div class="swiper-container" v-if="list[\'advsListStyle\'] ==2" :id="list[\'attr_id\']">\
	            <div class="swiper-wrapper" style="height:auto;width:100%;">\
	                <a class="swiper-slide" style="text-align:center" :href="image.linkUrl ? image.linkUrl : \'javascript:void(0);\'" v-for="image in list[\'images\']">\
	                    <img class="js-res-load" style="height:auto;width:100%" :src="image.FileInfo.path" :data-src="image.FileInfo.path" :class="{\'J_parseImg\':!image.linkUrl && (list[\'resize_image\']==undefined || (list[\'resize_image\']!=undefined&&list[\'resize_image\']==1))}">\
	                    <h3 class="title" style="position:absolute;bottom:0;width:100%;background-color:rgba(0,0,0,0.4);color:#fff;line-height:30px;text-align:left;" v-html="image.title" v-if="image.title"></h3>\
	                </a>\
	            </div>\
	            <div class="swiper-pagination">\
              </div>\
	        </div>\
	    </div>'
})

//end

//2018-10-24 邓钊 商品组件
Vue.component("goods",{
    props:{
        list:{
            type: Object,
            value: {}
        }
    },
    template:'          <div>' +
    '                    <!-- 一大两小 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-2 "  v-if="list[\'listStyle\']== 3 && list.goods.length" v-for="good in list[\'thGoods\']" style="visibility: visible;">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card big-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-show="good[0]">' +
    '                            <a :href="good[0].url">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                <div class="ziti_tips" v-if="good[0].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good[0][\'thumbnail\']">' +
    '                                </div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class="goods-title v-c" v-html="good[0][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[0][\'productDes\']"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good[0][\'price\']"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao " v-if="good[0][\'oprice\'] > 0" v-html="good[0][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[0][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">' +
    '                        </li>' +
    '                        <li class="js-goods-card goods-card small-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-if="good[1]">' +
    '                            <a :href="good[1].url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                <div class="ziti_tips" v-if="good[1].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" data-src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1][\'thumbnail\']+thump_400">' +
    '                                </div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class=" goods-title " v-html="good[1][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[1][\'productDes\']"></p>' +
    '                                    <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[1][\'oprice\'] > 0">' +
    '                                        <em v-html="good[1][\'price\']"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price" style=\'margin-bottom: 16px\' v-else>' +
    '                                        <em v-html="good[1][\'price\']"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao " v-if="good[1][\'oprice\'] != 0" v-html="good[1][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}"  v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>\n' +
    '                            </a>' +
    '                            <img v-if="good[1][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                        <li class="js-goods-card goods-card small-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-if="good[2]">' +
    '                            <a :href="good[2].url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                <div class="ziti_tips" v-if="good[2].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" data-src="" :src="good[2][\'thumbnail\']+thump_400">' +
    '                                </div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class=" goods-title " v-html="good[2][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[2][\'productDes\']"></p>' +
    '                                    <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[2][\'oprice\'] > 0">' +
    '                                        <em v-html="good[2][\'price\']"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price" style=\'margin-bottom: 16px\' v-else>' +
    '                                        <em v-html="good[2][\'price\']"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao" v-if="good[2][\'oprice\'] > 0" v-html="good[2][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>\n' +
    '                            </a>' +
    '                            <img v-if="good[2][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 一大两小 -->' +
    '                    <!-- 商品大图显示 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-0 " v-if="list[\'listStyle\']== 1" style="visibility: visible;">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card big-pic" v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a style=\'border: none\' :href="good.url" class="js-goods link clearfix">' +
    '                                <div class="photo-block">' +
    '                                <div class="ziti_tips" v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good[\'thumbnail\']">' +
    '                                </div>' +
    '                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class=" goods-title " v-html="good.name"></p>' +
    '                                    <p class="goods-sub-title c-black" :class="list[\'goodInfo\'] ? \'\' : \'hide\' " v-html="good.productDes"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good.price"></em></p>' +
    '                                    <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 商品大图显示 -->' +
    '                    <!-- 详细列表模式 -->' +
    '                    <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;"  v-if="list[\'listStyle\']== 4">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card" v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a :href="good.url" class="js-goods link clearfix"  >' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                <div class="ziti_tips" v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_300">' +
    '                                </div>' +
    '                                <div class="info" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class="goods-title" v-html="good.name"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good.price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                    <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\']"></div>' +
    '                                    <div class="js-goods-buy buy-response"></div>' +
    '                                </div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small small1">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 详细列表模式 -->' +
    '' +
    '                    <!-- 小图模式 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;" v-if="list[\'listStyle\']== 2">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card small-pic card " v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a :href="good.url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                <div class="ziti_tips" v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_400">' +
    '                                </div>' +
    '                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class=" goods-title " v-html="good.name"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good.productDes"></p>' +
    '                                    <div style="height:32px;" v-show="list.priceShow">' +
    '                                       <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[\'oprice\'] > 0">' +
    '                                           <em v-html="good.price"></em>' +
    '                                       </p>' +
    '                                       <p class="goods-price" style=\'margin-bottom: 0\' v-else>' +
    '                                           <em v-html="good.price"></em>' +
    '                                       </p>' +
    '                                       <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                    </div>' +
    '                                </div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\'] && list[\'cardStyle\'] != 4"></div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'cardStyle\'] == 4">我要抢购</div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 小图模式 -->' +
    '                </div>'
})
//2018-10-24 邓钊 商品搜索
Vue.component("search",{
    props:{
       list:{
           type:Object,
           value:{}
       },
       host:{
           type: String,
           value: ''
       },
       wid:[String,Number],
    },
    data(){
      return {
          url:''
      }
    },
    created(){
        this.url = this.host + 'shop/product/search/'+ this.wid
    },
    template:'<div class="custom-search" :style="{backgroundColor:list.bgColor}">' +
    '                    <form :action="url" method="GET" v-if="list.searchStyle == 1">' +
    '                        <input type="text" class="custom-search-input" name="title" :placeholder="list.searchName" value="">' +
    '                        <button type="submit" class="custom-search-button">搜索</button>' +
    '                    </form>' +
    '                    <form :action="url" method="GET" style="border-radius: 0;" v-if="list.searchStyle == 2">' +
    '                        <input type="text" class="custom-search-input" name="title" :placeholder="list.searchName" value="" style="border-radius: 0;">' +
    '                        <button type="submit" class="custom-search-button">搜索</button>' +
    '                    </form>' +
    '                    <form :action="url" method="GET" style="border-radius: 30px;" v-if="list.searchStyle == 3">' +
    '                        <input type="text" class="custom-search-input" name="title" :placeholder="list.searchName" value="" style="border-radius: 30px;">' +
    '                        <button type="submit" class="custom-search-button">搜索</button>' +
    '                    </form>' +
    '                </div>'
})
//2018-10-24 邓钊 商品列表
Vue.component("goodsList",{
    props:{
        list:{
            type: Object,
            value: {}
        }
    },
    created(){
        console.log(this.list)
        // this.$set(this.list)
    },
    template:'<div>' +
    '                    <!-- 一大两小 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-2 "  v-if="list[\'listStyle\']== 3 && list.goods.length" v-for="good in list[\'thGoods\']" style="visibility: visible;">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card big-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-show="good[0]">' +
    '                            <a class="link" :href="good[0].url">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                \t<div class="ziti_tips" v-if="good[0].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good[0][\'thumbnail\']">' +
    '                                </div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class="goods-title v-c" v-html="good[0][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[0][\'productDes\']"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good[0][\'price\']"></em></p>' +
    '                                    <p class="goods-price-taobao " v-if="good[0][\'oprice\'] > 0" v-html="good[0][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[0][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">' +
    '                        </li>' +
    '                        <li class="js-goods-card goods-card small-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-if="good[1]">' +
    '                            <a :href="good[1].url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                \t<div class="ziti_tips" v-if="good[1].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" data-src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/FqEKBL3zUtFZk1meW6aOxeL12Yoh.png?imageView2/2/w/280/h/280/q/75/format/webp" :src="good[1][\'thumbnail\']+thump_400">' +
    '                                </div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class=" goods-title " v-html="good[1][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[1][\'productDes\']"></p>' +
    '                                    <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[1][\'oprice\'] > 0">' +
    '                                        <em v-html="good[1].price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price" style=\'margin-bottom: 16px\' v-else>' +
    '                                        <em v-html="good[1].price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao " v-if="good[1][\'oprice\'] != 0" v-html="good[1][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}"  v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[1][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                        <li class="js-goods-card goods-card small-pic" v-bind:class="{\'card\':list[\'cardStyle\']==\'1\',\'normal\':list[\'cardStyle\']==\'3\'}" v-if="good[2]">' +
    '                            <a :href="good[2].url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                \t<div class="ziti_tips" v-if="good[2].is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" data-src="" :src="good[2][\'thumbnail\']+thump_400"></div>' +
    '                                <div class="info clearfix btn1" :class="[list.title, list.priceClass,list.hide_all]">' +
    '                                    <p class=" goods-title " v-html="good[2][\'name\']"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good[2][\'productDes\']"></p>' +
    '                                    <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[2][\'oprice\'] > 0">' +
    '                                        <em v-html="good[2].price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price" style=\'margin-bottom: 16px\' v-else>' +
    '                                        <em v-html="good[2].price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao " v-if="good[2][\'oprice\'] > 0" v-html="good[2][\'oprice\']"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy info-no-title" v-bind:class="{\'btn1\':list[\'btnStyle\']==\'1\',\'btn2\':list[\'btnStyle\']==\'2\',\'btn3\':list[\'btnStyle\']==\'3\',\'btn4\':list[\'btnStyle\']==\'4\'}" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[2][\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 一大两小 -->' +
    '                    <!-- 商品大图显示 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-0 " v-if="list[\'listStyle\']== 1" style="visibility: visible;">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card big-pic" v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a :href="good.url" class="js-goods link clearfix">' +
    '                                <div class="photo-block">' +
    '                                \t<div class="ziti_tips" v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good[\'thumbnail\']">' +
    '                                </div>' +
    '                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class=" goods-title " v-html="good.name"></p>' +
    '                                    <p class="goods-sub-title c-black" :class="list[\'goodInfo\'] ? \'\' : \'hide\' " v-html="good.productDes"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good.price"></em></p>' +
    '                                    <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\']"></div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 商品大图显示 -->' +
    '                    <!-- 详细列表模式 -->' +
    '                    <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;"  v-if="list[\'listStyle\']== 4">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card" v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a :href="good.url" class="js-goods link clearfix" >' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                \t<div class="ziti_tips" v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_300">' +
    '                                </div>' +
    '                                <div class="info" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class="goods-title" v-html="good.name"></p>' +
    '                                    <p class="goods-price">' +
    '                                        <em v-html="good.price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                    <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\']"></div>' +
    '                                    <div class="js-goods-buy buy-response"></div>' +
    '                                </div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small small1">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 详细列表模式 -->' +
    '' +
    '                    <!-- 小图模式 -->' +
    '                    <ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;" v-if="list[\'listStyle\']== 2">' +
    '                        <!-- 商品区域 -->' +
    '                        <!-- 展现类型判断 -->' +
    '                        <li class="js-goods-card goods-card small-pic card " v-for="good in list[\'goods\']" :class="[list.list_style,list.has_sub_title]">' +
    '                            <a :href="good.url" class="js-goods link clearfix">' +
    '                                <div class="photo-block" style="background-color: rgb(255, 255, 255);">' +
    '                                \t<div class="ziti_tips"  v-if="good.is_hexiao == 1">自提</div>' +
    '                                    <img class="goods-photo js-goods-lazy" :src="good.thumbnail+thump_400">' +
    '                                </div>' +
    '                                <div class="info clearfix" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]">' +
    '                                    <p class=" goods-title " v-html="good.name"></p>' +
    '                                    <p class="goods-sub-title c-black hide" v-html="good.productDes"></p>' +
    '                                    <p class="goods-price" style=\'float: none;margin-bottom: 0\' v-if="good[\'oprice\'] > 0">' +
    '                                        <em v-html="good.price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price" style=\'margin-bottom: 16px\' v-else>' +
    '                                        <em v-html="good.price"></em>' +
    '                                    </p>' +
    '                                    <p class="goods-price-taobao" v-if="good[\'oprice\'] > 0" v-html="good.oprice"></p>' +
    '                                </div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'showSell\'] && list[\'cardStyle\'] != 4"></div>' +
    '                                <div class="goods-buy" :class="[list.title, list.priceClass,list.hide_all,list.btnClass]" v-show="list[\'cardStyle\'] == 4">我要抢购</div>' +
    '                                <div class="js-goods-buy buy-response"></div>' +
    '                            </a>' +
    '                            <img v-if="good[\'is_selling\'] == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">' +
    '                        </li>' +
    '                    </ul>' +
    '                    <!-- 小图模式 -->' +
    '                </div>'
})
//2018-10-24 邓钊 优惠券
Vue.component("coupon",{
    props:{
        list:{
            type: Object,
            value: {}
        }
    },
    template: ' <ul class="custom-coupon clearfix" :class="{\'coupon-item-multi coupon-item-three\':list.couponList.length==3,\'coupon-item-multi coupon-item-two\':list.couponList.length==2,\'coupon-item-one\':list.couponList.length==1}">' +
    '              <li :class="{\'coupon-style1 coupon-color1\':!list.couponStyle,\'coupon-style1\':list.couponStyle==1,\'coupon-style2\':list.couponStyle==2,\'coupon-style3\':list.couponStyle==3,\'coupon-style4\':list.couponStyle==4,\'coupon-color1\':list.couponColor==1,\'coupon-color2\':list.couponColor==2,\'coupon-color3\':list.couponColor==3,\'coupon-color4\':list.couponColor==4,\'coupon-color5\':list.couponColor==5,\'coupon-disabled\':coupon.cls}" v-for="coupon in list.couponList">' +
    '                  <a :href="coupon.type == 0 ? coupon.url : \'javascript:void(0);\'">' +
    '                      <div class="cap-coupon__disabled-text-wrap" v-if="coupon.cls"><div class="cap-coupon__disabled-text" v-text="coupon.cls==\'achieved\'?\'已领取\':coupon.cls==\'overdue\'?\'已过期\':coupon.cls==\'over\'?\'已领完\':coupon.cls==\'invalid\'?\'已失效\':\'\'"></div></div>' +
    '                      <i class="coupon-icon coupon-left-icon"></i>' +
    '                      <div class="coupon-bg"></div>' +
    '                      <i class="coupon-icon coupon-right-icon"></i>' +
    '                      <div class="coupon-content">' +
    '                          <div class="coupon-content-left">' +
    '                              <div class="custom-coupon-price">' +
    '                                  <span>￥</span>' +
    '                                  <span class="custom-coupon-amount" v-html="coupon.amount"></span>' +
    '                              </div>' +
    '                              <div class="custom-coupon-desc">' +
    '                                  <div class="coupon-name-lg">优惠券</div>' +
    '                                  <div v-html="coupon.limit_desc"></div>' +
    '                              </div>' +
    '                          </div>' +
    '                          <div class="coupon-content-right">' +
    '                              立即领取' +
    '                          </div>' +
    '                          <i class="cap-coupon__dot-above" v-if="list.couponStyle==1"></i>' +
    '                          <i class="cap-coupon__dot-below" v-if="list.couponStyle==1"></i>' +
    '                      </div>' +
    '                  </a>' +
    '              </li>' +
    '          </ul>'
})
//2018-10-24 邓钊 会员卡
Vue.component("card",{
    props:{
        list:{
            type:Object,
            value:{}
        },
        host:{
            type: String,
            value:''
        }
    },
    data(){
        return {
            imgUrl:''
        }
    },
    created(){
        this.imgUrl = this.host + 'shop/images/cardbg.png'
    },
    template:'<ul class="custom-coupon card-coupon" v-if="list.type==\'card\' && list.cardList.length > 0">' +
    '                    <li v-for="card in list.cardList" class="member-card">' +
    '                        <a :href="card.url">' +
    '                            <img :src="card.card_img ? card.card_img : imgUrl">' +
    '                            <span class="card_title">{{card.name | substr}}</span>' +
    '                        </a>' +
    '                    </li>' +
    '                </ul>'
})
//2018-10-24 邓钊 文本链接
Vue.component("textLink",{
    props:{
        list:{
            type:Object,
            value:{},
        }
    },
    template:'        <ul class="custom-nav clearfix" v-if="list.type == \'textlink\'">' +
    '                    <li v-for="nav in list.textlink">' +
    '                        <a class="clearfix relative arrow-right" :href="nav.linkUrl">' +
    '                            <span class="custom-nav-title" v-html="nav.titleName"></span>' +
    '                        </a>' +
    '                    </li>' +
    '                </ul>'
})

//进入店铺组件
Vue.component('storeIn',{
    props:['list'],
    data:function(){
        return {

        }
    },
    created:function(){

    },
    methods:{

    },
    components:{

    },
    template:
      '<div class="custom-store block-item border">\
          <a class="custom-store-link clearfix" :href="list.url">\
              <div class="custom-store-img"></div>\
              <div class="custom-store-name" v-html="list.store_name"></div>\
          </a>\
      </div>'
})

//进入店铺组件
Vue.component('titleStyle',{
    props:['list'],
    data:function(){
        return {

        }
    },
    created:function(){

    },
    methods:{

    },
    components:{

    },
    template:
      '<div class="custom-title-noline" v-bind:style="{background:list.bgColor}">\
            <div class="custom-title wx_template" :class="{\'text-left\':list[\'showPosition\']==1,\'text-center\':list[\'showPosition\']==2,\'text-right\':list[\'showPosition\']==3}">\
            <h2 class="title">\
                <span v-html="list.titleName"></span>\
                <span class="custom-title-link" v-if = "list.titleStyle == 1 && list.linkTitle">\
                    <span class="c-gray-dark" v-if="list.linkUrl">-</span>\
                    <a :href="list.linkUrl ? list.linkUrl : \'javadcript:void(0);\' " v-html="list.linkTitle"></a>\
                </span>\
            </h2>\
            <p class="sub_title" v-if="list.titleStyle == 1" v-html="list.subTitle"></p>\
            <p class="sub_title" v-if="list.titleStyle == 2">\
                <span class="sub_title_date" v-html="list.date"></span>\
                <span class="sub_title_author" v-html="list.author"></span>\
                <a class="sub_title_link js-open-follow" :href="list.linkUrl ? list.linkUrl : \'javadcript:void(0);\' " v-html="list.wlinkTitle"></a>\
            </p>\
        </div>\
    </div>'
})

/**
 * @author:  韩瑜 2018-11-28 
 * @description: 商品分组模板组件
 * @param {type} 
 * @return: 
 * @Date: 2019-07-12 18:07:21
 * @update: 2019-07-12 魏冬冬（zbf5279@dingtalk.com）左侧导航增加市场价
 */
Vue.component('groupTemplate', {
  props: ['content'],
  data: function () {
	  return {
				isscroll: false,
		    data:{
		    left_nav:this.content ? this.content.left_nav : "",
		    top_nav:this.content ? this.content.top_nav : "",
		    },
		    group_type:this.content ? this.content.group_type : "",//1为左侧，2,为顶部
		    goods:[],
		    goodDetail:null,
		    index:null,
		    listStyle:this.content.listStyle?this.content.listStyle:0, //0 详细列表 1 小图
		    page:[],
		    allHeight:0
	  }
  },
  methods: {
		chooseKind: function(item,position,index){
      var that = this;
      setTimeout(function(){
          if(position == 1){
            if(that.data.left_nav.length>0){
              for(var i = 0;i<that.data.left_nav.length;i++){
                 that.data.left_nav[i]['isActive'] = false;
              }
            }
          }else if(position == 2){
            if(that.data.top_nav.length>0){
              for(var i = 0;i<that.data.top_nav.length;i++){
                 that.data.top_nav[i]['isActive'] = false;
                 that.goods = that.data.top_nav[i]['goods'];
              }
            }
          }
          this.index = index
          $('.custom-tag-list-goods>div').css({'display':'none'})
          $('.custom-tag-list-goods>div').eq(index).css({'display':'block'})
        that.goods = item.goods;
        item.isActive = true;
      },100)
    },
    getDate: function(goodId,name,price,thumbnail,stock){
      tool.spec.open({
        "type":1,
        "url":'/shop/product/getSku',
        'data':{pid:goodId,_token:$('meta[name="csrf-token"]').attr('content')},
        'initSpec':{'img':thumbnail,'price':price,'title':name,'stock':stock},
        "callback":function(data){
          if(data.index == 0){
            var postData = {'id':goodId,"num":data.data.num,'propid':data.data.spec_id,content:''}
            $.ajax({
              url:'/shop/cart/add/'+id,// 跳转到 action
              data:postData,
              type:'post',
              cache:false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:'json',
              success:function (response) {
                  if (response.status == 1){
                      $('#Xms3Sq4JR6').hide();
                      $('#LBqDHKuruf').hide();
                      tool.spec.close()
                      $('.goods-num').show().html(response.data.cartNum); 
                      tool.tip('已加入购物车,赶紧去结算吧!');
                  }else{
                      tool.tip(response.info);
                  }
              },
              error : function() {
                  // view("异常！");
                  tool.tip("异常！");
              }
          });
          }else if(data.index == 1){

            var postData = {'id':goodId,"num":data.data.num,'propid':data.data.spec_id,content:''}
            $.ajax({
              url:'/shop/cart/add/'+id+'?tag=1',// 跳转到 action
              data:postData,
              type:'post',
              cache:false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:'json',
              success:function (response) {
                if (response.status == 1){
                    window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']';
                }else{
                    tool.tip(response.info);
                }
              },
              error : function() {
                 
                  tool.tip("异常！");
              }
          });
          }    
      }});
    },
    getGroupGood:function(item,index){
        var _this=this
        var num = index;
        var page = this.page[index].page;
        this.$http.get("/shop/group/productGroupDetail?group_id="+ item.id +'&page=' + page + '&isNew=1').then(
            function(res){
            if(res.body.status == 1){
                if(res.body.data.length >= 0 &&res.body.data.length <15){
                    var data = res.body.data
                    for(var k = 0; k < data.length; k++){
                        //data[k].thumbnail = imgUrl + data[k].thumbnail
                        item.goods.push(data[k])
                    }
                 item.btnFlag = false;
                }if(res.body.data.length == 15){
                    var data = res.body.data
                    for(var k = 0; k < data.length; k++){
                        //data[k].thumbnail = imgUrl + data[k].thumbnail
                        item.goods.push(data[k])
                    }
                }
                this.page[num].page ++                          
            }              
            }
        )
    },
    getHeadGroupGood:function(){
	    var _this=this
	    if(_this.data.top_nav.length > 0){
        var list = _this.data.top_nav;
        var id = null;
        var num = null;
        var index = null;
        for(var i = 0; i < list.length;i++){
	        if(list[i].isActive){
            index = i 
            id = list[i].id
            num = this.page[i].page++
	        } 
        }
        this.$http.get("/shop/group/productGroupDetail?group_id="+ id +'&page=' + num + '&isNew=1').then(
	        function(res){
	          if(res.body.status == 1){
	              if(res.body.data.length >= 0 && res.body.data.length < 15){
	                  for(var j = 0; j < list.length; j++){
	                      console.log(list[j],'list999999')
	                      if(list[j].id == id){
	                          var data = res.body.data
	                          for(var k = 0; k < data.length; k++){
	                            list[j].goods.push(data[k])
	                          }
	                          list[j].btnFlagTop =false;
	                      }
	                  }
	              }
	              if(res.body.data.length == 15){
	                var data = res.body.data
	                for(var j = 0; j < list.length; j++){
	                    if(list[j].id == id){
	                        var data = res.body.data
	                        for(var k = 0; k < data.length; k++){
	                          list[j].goods.push(data[k])
	                        }
	                    }
	                }
	            	}
	          }
	        }
	      )
	    }
    }
  },
  mounted: function(){
    this.$nextTick(function(){
      //对DOM的操作放这
      //左侧商品栏
      var that = this;
      if (that.data.left_nav.length>0) {
        for(var i = 0; i < that.data.left_nav.length; i++){
	    			that.data.left_nav[i]['isActive'] = false;
	  				that.data.left_nav[0]['isActive'] = true;
            var obj = {
                id: that.data.left_nav[i].id,
                page:2
            }
            that.page.push(obj)
            var goods = that.data.left_nav[i].goods
             if ( goods.length <15) {
                that.$set(that.content.left_nav[i],'btnFlag',false)
            }else{
                that.$set(that.content.left_nav[i],'btnFlag',true)
            }
        }
      } else if((that.data.top_nav.length>0)){
          //头部商品
       	for(var i = 0; i < that.data.top_nav.length; i++){
       		that.data.top_nav[i]['isActive'] = false;
	  				that.data.top_nav[0]['isActive'] = true;
	        var obj = {
	            id: that.data.top_nav[i].id,
	            page:2
	        }
	        that.page.push(obj)
	        var goods = that.data.top_nav[i].goods
	         	if ( goods.length < 15) {
	          	that.data.top_nav[i].btnFlagTop = false
	        }
	        else if(goods.length == 15){
	          	that.data.top_nav[i].btnFlagTop = true
	        }
      	}
    	}
      
		  if(that.group_type == 1){
	        $('#container').css({'padding-bottom':0});
	        $('.content').css({'min-height':'auto'})
	        var clientH = document.documentElement.clientHeight || document.body.clientHegiht; //屏幕高度
	        that.allHeight = clientH + 'px'
		  }else if(that.group_type == 2){
		   	if(that.data.top_nav.length>0){
		    	that.goods = that.data.top_nav[0]['goods'];
		  	}
		  }
    })
  },
  template: '<div style="position:relative;margin-bottom: 50px;">'+
      '<div class="custom-tags js-custom-tags groupWrap" v-if="group_type == 2 && data.top_nav.length != 0" id="wrapTop">'+
          '<div class="js-tabber-tags tabber tabber-bottom red clearfix tabber-n4 groupTop">'+
              '<div class="custom-tags-more js-show-all-tags"></div>'+
              '<div id="J_tabber_scroll_wrap" class="custom-tags-scorll clearfix">'+
                  '<div id="J_tabber_scroll_con" class="custom-tags-scorll-con">'+
                      '<a data-tagname="tag-1" href="#wrapTop" v-for="(item, index) in data.top_nav" @click="chooseKind(item,2)" :class="item.isActive ? \'active\':\'\'" :style="{width:item.width}">{{item.name}}</a>'+
                  '</div>'+
              '</div>'+
          '</div>'+
          '<div class="js-goods-tag js-goods-tag-1 show groupList" data-alias="1b6gm5ocg" style="min-height:100px;" v-if="listStyle == 0">'+
              '<div class="js-list b-list">'+
                  '<ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;">'+
                      '<!-- 商品区域 -->'+
                      '<!-- 展现类型判断 -->'+
                      '<li class="js-goods-card goods-card normal" v-for="good in this.goods">'+
                          '<a :href="good.url" class="js-goods link clearfix">'+
                              '<div class="photo-block" style="background-color: rgb(255, 255, 255);">'+
                                  '<img class="goods-photo js-goods-lazy" v-lazy="good.thumbnail+thump_300"></div>'+
                              '<div class="info">'+
                                  '<p class="goods-title" v-html="good.name"></p>'+
                                  '<p class="goods-price">'+
                                      '<em v-html="good.price"></em></p>'+
                                  '<div class="goods-buy btn1"></div>'+
                                  '<div class="js-goods-buy buy-response"></div>'+
                              '</div>'+
                          '</a>'+
                          '<img v-if="good.is_selling == 0" style="position: absolute;top: 0;width:52px;left:0px;" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell">'+
                      '</li>'+
                  '</ul>'+
                  '<div style="text-align:center;" v-for="(item,index) in data.top_nav" v-if="item.isActive && item.btnFlagTop" >'+
                      '<button class="custom-tag-list-topbtn top_more" @click="getHeadGroupGood()"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                '</div>'+
              '</div>'+
          '</div>'+
          '<div v-if="listStyle == 1" class="groupList">' +
              '<!-- 小图模式 -->'+
              '<ul class="js-goods-list sc-goods-list pic clearfix size-1 " style="visibility: visible;">'+
                '<!-- 商品区域 -->'+
                '<!-- 展现类型判断 -->'+
                '<li class="js-goods-card goods-card small-pic card card" v-for="good in this.goods">'+
                    '<a :href="good.url" class="js-goods link clearfix">'+
                        '<div class="photo-block" style="background-color: rgb(255, 255, 255);">'+
                            '<img class="goods-photo js-goods-lazy" v-lazy="good.thumbnail+thump_400">'+
                        '</div>'+
                        '<div class="info clearfix info-title info-price btn1">'+
                            '<p class=" goods-title " v-html="good.name"></p>'+
                            '<p class="goods-sub-title c-black hide" v-html="good.productDes"></p>'+
                            '<p class="goods-price" style="float: none;margin-bottom: 0" v-if="good.oprice > 0">'+
                                '<em v-html="good.price"></em>'+
                            '</p>'+
                            '<p class="goods-price" style="margin-bottom: 16px" v-else>'+
                                '<em v-html="good.price"></em>'+
                            '</p>'+
                            '<p class="goods-price-taobao" v-if="good.oprice > 0" v-html="good.oprice">100</p>'+
                        '</div>'+
                        '<div class="goods-buy info-title info-price btn1"></div>'+
                        '<div class="js-goods-buy buy-response"></div>'+
                    '</a>'+
                    '<img v-if="good.is_selling == 0" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell.png" class="no_sell small">'+
                '</li>'+
              '</ul>'+
              '<div style="text-align:center;" v-for="(item,index) in data.top_nav" v-if="item.isActive && item.btnFlagTop" >'+
                      '<button class="custom-tag-list-topbtn top_more" @click="getHeadGroupGood()"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                '</div>'+
          '</div>'+
      '</div>'+
      '<!-- 左侧 -->'+
      '<div class="custom-tag-list clearfix js-custom-tag-list" v-if="group_type == 1 && data.left_nav.length != 0" v-bind:style="{minHeight:400+\'px\'}" id="LeftTop">'+
          '<div class="custom-tag-list-menu-block groupLeft" :style="{height:allHeight}">'+
              '<div style="height: 250px; display: none;"></div>'+
              '<ul class="custom-tag-list-side-menu js-side-menu">'+
                  '<li :class="item.isActive ? \'current\':\'\'" @click="chooseKind(item,1,index)" v-for = "(item,index) in data.left_nav">'+
                      '<a class="js-menu-tag" href="#LeftTop">'+
                          '<span>{{item.name}}</span>'+
                      '</a>'+
                  '</li>'+
              '</ul>'+
          '</div>'+
          '<div class="custom-tag-list-goods">'+
            '<div v-for="(item,index) in data.left_nav">'+
              '<p class="custom-tag-list-title" :id="item.href" v-if="index == this.index || index == 0">{{item.name}}</p>'+
              '<ul class="custom-tag-list-goods-list js-custom-goods-list" v-if="item.goods.length && index == this.index || index == 0">'+
                  '<li class="custom-tag-list-single-goods clearfix" v-for="good in item.goods">'+
                      '<a :href="good.url" class="custom-tag-list-goods-img">'+
                          '<img class="js-lazy" alt="" v-lazy="good.thumbnail+thump_200"><img v-if="good.is_selling == 0" class="no_sell" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/wdd/no_sell1.png"/></a>'+
                      '<div class="custom-tag-list-goods-detail">'+
                          '<a :href="good.url" class="custom-tag-list-goods-title" v-html="good.name"></a>'+
                          '<span class="custom-tag-list-goods-price" v-html="good.price"></span>'+
                          '<a :href="good.url" class="custom-tag-list-goods-oprice" v-if="good.is_price_negotiable == 0" v-text="\'市场价：\' + good.oprice"></a>'+
                          '<a class="custom-tag-list-goods-buy js-custom-tag-list-goods-buy" href="javascript:void(0)">'+
                              '<!--<span class="ajax-buy" @click="getDate(good.id,good.name,good.price,good.thumbnail,good.stock)"></span>-->'+
                              '<span class="ajax-loading"></span>'+
                          '</a>'+
                      '</div>'+
                  '</li>'+
                  '<div style="text-align:center;" v-if="item.btnFlag">'+
                  '<button class="custom-tag-list-btn left_more" @click="getGroupGood(item,index)"  style="font-size:13px;background:none;border:none;color:#999;height:30px;">加载更多</button>'+
                  '</div>'+
                  '<div style="text-align:center;" v-else>'+
                  '<button class="custom-tag-list-btn " style="font-size:13px;background:none;border:none;color:#999;height:30px;">没有更多了~</button>'+
                  '</div>'+
              '</ul>'+
              '<ul class="custom-tag-list-goods-list js-custom-goods-list" v-if="!item.goods.length && index == this.index ">'+
                 '<li class="no-goods-list" >此类下暂时没有商品</li>'+
              '</ul>'+
            '</div>'+
          '</div>'+
      '</div>'+
    '</div>',
})

