$(function(){   
    var mySwiper = new Swiper('.swiper-container', {
      autoplay: 3000,//可选选项，自动滑动
      pagination: '.swiper-pagination',
      loop : true,
  });
  handleSeckillStatus(status,wid,product.product.id);
  //根据活动状态作出各种处理
  function handleSeckillStatus(status,wid,pid){
      status = status?parseInt(status) : 2; 
      switch(status){
          case 1: //未开始 显示原价购买
              $(".js-panic-buy-it").parent().parent().addClass('hide');
              $(".js-buy-it").parent().parent().removeClass('hide');
              $('.goods-activity').addClass('theme-orange');
              $(".countdown-title").html("距开始仅剩");
              getrtime(stime);//倒计时
              break;
          case 2: //进行中
              $(".js-buy-it").parent().parent().addClass('hide');
              $(".js-panic-buy-it").parent().parent().removeClass('hide');
              $('.goods-activity').removeClass('theme-orange');
              $(".countdown-title").html("距结束仅剩");
              getrtime(overtime);//倒计时
              break;
          case 3: //已结束 
              location.href="/shop/product/detail/"+wid+"/"+pid;
              break;
          case 4: //失效
              location.href="/shop/product/detail/"+wid+"/"+pid;
              break;
      }
  }

  //设置sku标题
  function setSkuTitle(obj){
      var str ="";
      if(obj.k1){
          str +='<span class="sku-item">'+obj.k1+'</span>';
          if(obj.k2){
              str +='<span class="sku-item">'+obj.k2+'</span>';
              if(obj.k3){
                  str +='<span class="sku-item">'+obj.k3+'</span>';
              }
          }
      }
      $(".js-sku-value").html(str);
  }
  //懒加载
  $('.lazyload').picLazyLoad({
      threshold: 200,
      effect : "fadeIn"
  });
    //商品详情和销量和评价
    $('.js-tabber button').click(function(){
        var index =$(this).index();
      $(this).addClass("active").siblings().removeClass('active');
        if(index==0){
            $(".js-tabber-content .js-part").eq(0).addClass("hide");
            $(".js-tabber-content .js-part").eq(1).removeClass("hide");
        }else{
          showEvaluate("all");
            $(".js-tabber-content .js-part").eq(0).removeClass("hide");
            $(".js-tabber-content .js-part").eq(1).addClass("hide"); 
        }
    });
    //评论类型点击事件
    $(".js-cancal-disable-link").click(function(){
        var type_name = $(this).attr("data-reviewtype"); 
        showEvaluate(type_name); 
    }); 

  	//原价购买按钮点击事件
  	$(".js-buy-it").click(function(){ 
        var showPrice = product.product.showPrice;
        tool.spec.open({
            "type":1,
            "callback":singleBuy,
            "url": "/shop/product/getSku",  //获取规格接口
            "data":{
                "_token": $("meta[name='csrf-token']").attr("content"),
                "pid": product.product.id
            }, 
            "initSpec": {    // 默认商品数据
                "title": product.product.title,
                "img": product.product.img,
                "stock": product.product.stock,
                "price": showPrice,
            },
            "unActive":1,   //非拼团活动
            "isEdit":true,  //点x  不保存数据
            "buyCar": false  //按钮  为单按钮  加入购物车  可不写
        });   
  	});
    //立即抢购
    $(".js-panic-buy-it").click(function(){  
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         goBuy();
        //     })
        //     return;
        // }
        goBuy(); 

    });

    /**
     * @auther 邓钊
     * @desc 选择规格
     * @data 2018-10-22
     * @param
     * @return
     *
     * */
    $(".selectSku").click(function(){
        goBuy();
    });

  var goBuy = function(){
      $('video').css('display','none');
      var showPrice = seckill.price_range;
      tool.spec.open({ 
          "type":2,
          "callback":buyCallBack,
          "url": "/shop/seckill/getSku",  //获取规格接口 
          "data":{
              "_token": $("meta[name='csrf-token']").attr("content"),
              sid:seckill.id
          }, 
          "initSpec": {    // 默认商品数据
              "title": seckill.title,
              "img": product.product.img,
              "stock": seckill.stock_sum,
              "price": showPrice,
          },
          "limit_num":seckill.limit_num,
          "unActive":1,   //非拼团活动
          "isEdit":true,  //点x  不保存数据
          "buyCar": false  //按钮  为单按钮  加入购物车  可不写
      });
  }

  $(".joingroups-link").click(function(){
      $.ajax({
          url:' /shop/grouppurchase/getGroups/'+rule.id,// 跳转到 action
          data:{"page":cou_page},
          type:'get',
          cache:false, 
          dataType:'json',
          success:function (res) { 
              console.log(res);
              if(res.status==1){
                  var data = res.data;
                  var html ="";
                  for(var i=0;i<data.length;i++){
                      html+='<div class="block-item name-card name-card-3col joingroup-name-card">';
                      html+='<div class="thumb"><img class="circular" src="'+data[i].headimgurl+'"></div>';
                      html+='<div class="detail"><h3 class="joingroup-title">'+data[i].nickname+'<span class="c-gray-dark title-tip">开团</span></h3>';
                      html+='<div class="joingroup-desc js-joingroup-desc">剩余<div class="js-joingroup-countdown joingroup-countdown" data-seconds="72444">';
                      html+='<span class="c-red">'+data[i].end_time+'</span></div>结束，仅差<span class="c-red">'+data[i].num+'</span>人</div></div>';
                      html+='<div class="right-col"><a href="/shop/grouppurchase/groupon/'+data[i].id+'" class="tag tag-red tag-joingroup">去凑团</a></div>';
                  }
                  $(".all-joingroups").before(html);
              }
              cou_page++;
          },
          error : function() { 
              tool.tip("异常！");
          }
      });
  });

  
  var cou_page = 2; //秒杀原价购买
  function singleBuy(res){   
      if(res.status==1){
          var data = {
              "id": product.product.id,
              "num": res.data.num,
              "propid": res.data.spec_id,
              "content": ""//留言  to do something
          }
          if(res.type==1){ //加入购物车 
              $.ajax({
                  url:'/shop/cart/add/'+wid,// 跳转到 action
                  data:data,
                  type:'post',
                  cache:false,
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  dataType:'json',
                  success:function (response) { 
                      if (response.status == 1){ 
                          $('.goods-num').show().html(response.data.cartNum);
                          tool.spec.close(); 
                          tool.tip('已加入购物车,赶紧去结算吧!');
                      }else{
                          tool.tip(response.info);
                      }
                  },
                  error : function() { 
                      tool.tip("异常！");
                  }
              });
          }else{ //购买
              $.ajax({
                  url:'/shop/cart/add/'+wid+'?tag=1',// 跳转到 action
                  data:data,
                  type:'post',
                  cache:false,
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  dataType:'json',
                  success:function (response) {
                      if (response.status == 1){
                          window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']';
                          tool.spec.close();
                      }else{
                          tool.tip(response.info);
                          $(".main-btn").html("下一步").addClass('js-mutiBtn-confirm');
                          tool.spec.close();
                      }
                  },
                  error : function() { 
                      tool.tip("异常！");
                  }
              });
          }
      } 
  }

  function buyCallBack(data){   
      if(data.status==1){  
          var data = {
              "id": product.product.id,
              "num": data.data.num,
              "propid": data.data.spec_id,
              "content": "",//留言  to do something
              "seckillID": seckill.id,
              "tag": 1
          } 
          $.ajax({
              url:'/shop/cart/add/'+wid,// 跳转到 action
              data:data,
              type:'post',
              cache:false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:'json',
              success:function (response) {
                  if (response.status == 1){
                      window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']'; 
                      tool.spec.close();
                  }else{
                      tool.tip(response.info);
                      $(".main-btn").html("下一步").addClass('js-mutiBtn-confirm');
                      tool.spec.close();
                  }
              },
              error:function() { 
                  tool.tip("异常！");
              }
          });
      }

  }

  //显示对应评论
  function showEvaluate(type_name){
      $(".js-review-part").each(function(index,el){
          var type1 = $(this).attr("data-reviewtype");
          var t_status ="";
          switch(type_name){
              case "all": //全部
                  t_status ="";
                  break;
              case "good": //好评
                  t_status = 1;
                  break;
              case "middle": //中评
                  t_status = 2;
                  break;
              case "bad": //差评
                  t_status = 3;
                  break;
              default:
                  t_status="";
          }
          var obj = {};
          obj.wid = wid;
          obj.pid = product.product.id;
          obj.status = t_status;
          if(type_name == type1){
              getProductEvaluate(obj,this);
              $(this).removeClass('hide'); 
              $(".js-cancal-disable-link").eq(index).addClass("active");
          }else{ 
              $(this).addClass('hide');
              $(".js-cancal-disable-link").eq(index).removeClass("active"); 
          }
      });
  }
  //获取商品评价
  function getProductEvaluate(obj,_this){
      $.ajax({
          url:"/shop/product/evaluate/"+obj.wid,
          data:{"pid":obj.pid,"status":obj.status},
          type:"get",
          dataType:"json",
          success:function(json){
              if(json.status==1){
                  var str ="";
                  var data = json.data;
                  if(data.length>0){
                      for(var i=0;i<data.length;i++){ 
                          str +='<div class="js-list b-list">'; 
                          str +='<a href="/shop/product/evaluateDetail/'+data[i].wid+'?eid='+data[i].id+'" class="js-review-item review-item block-item">';
                          str +='<div class="name-card"><div class="thumb">';
                          if(data[i].is_hiden==1){ //匿名
                              str +='<span class="center font-size-18 c-orange">匿</span>';
                          }else{
                              str +='<img src="'+data[i].member.headimgurl+'" alt="">';
                          }   
                          
                          str +='</div>';
                          str +='<div class="detail"><h3>'+data[i].member.nickname+'</h3><p class="font-size-12">'+data[i].created_at+'</p></div></div>';
                          str +='<div class="item-detail font-size-14 c-gray-darker"><p>'+data[i].content+'</p>';
                          //评论图片结束
                          str +='</div><div class="other"><span class="from">购买自：本店</span><p class="pull-right">'
                          str +='<span class="js-like like-item "><i class="like"></i><i class="js-like-num">'+data[i].agree_num+'</i></span>';
                          str +='<span class="js-add-comment"><i class="comment"></i><i class="js-comment-num"></i></span></p></div></a>';
                          str +='</div>';   
                      } 
                      $(_this).html(str);
                  }else{
                      $(_this).html('<div class="list-finished">暂无评论</div>');
                  }
                  

              }
              console.log(json);
          },
          error:function(){
              console.log("异常");
          }
      });
  }   
  //倒计时函数
  function getrtime(time){
      var EndTime= new Date(time);
      var t = EndTime.getTime() - NowTime;
      if(t>=0){
          var d=Math.floor(t/1000/60/60/24);
          var h=Math.floor(t/1000/60/60%24);
          var m=Math.floor(t/1000/60%60);
          var s=Math.floor(t/1000%60);
          if(d>0){
              $(".js-span-d").html(d);
              $(".js-span-d").show();
              $(".js-i-d").show();
          }else{
              $(".js-span-d").hide();
              $(".js-i-d").hide(); 
          }
          
          $(".js-span-h").html(h);
          $(".js-span-m").html(m);
          $(".js-span-s").html(s); 
          setTimeout(function(){
              getrtime(time);
              NowTime += 1000;
          },1000);
      }
  }
});


// 组件模块  @author huoguanghui
var key = [];
var j = 0;
var k = 0;
var n = 0;
new Vue({
  el: '#container',
  delimiters: ['[[', ']]'], 
  data:{
      lists:[],//商品详情数据    
      host: host,
      shopId:shop_id,//商品id
      isFavorite:'',//是否收藏
  },
  created: function () {
      var that = this;
      //商品详情数据  
      if(product.product.content){ 
          var productDetail = JSON.parse(product.product.content);
          if(productDetail.length > 0){
              componentAssign(this.lists,productDetail);
              setTimeout(function(){
                  $('img').removeAttr('width');
                  $('img').removeAttr('height');
                  $('video').attr('width','100%');
                  $('video').attr('height','auto');
              },1000)
          }
      }
      /**
       * 组件赋值
       * 参数 赋值对象 赋值模板
       * 用到对象  商品的富文本自定义组件    商品页模板  广告业模板
       */
      function componentAssign(obj,template){
          var content = template;//模板遍历赋值
          for(var i =0;i < content.length;i ++){
              if(content[i] != undefined){
                  if(content[i]['type'] == 'shop_detail'){
                      //图片家域名
                      content[i]['content'] = content[i]['content'].replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                        if(capture.indexOf('http') == -1){
                          var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                          match = match.replace(capture,newSrc)
                        }
                        return match
                      });
                      // 视频添加域名
                      content[i]['content'] = content[i]['content'].replace(/<video [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                        if(capture.indexOf('http') == -1){
                          var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                          match = match.replace(capture,newSrc)
                        }
                        return match
                      });
                  }
                  if(content[i]['type'] == 'rich_text'){
                      //图片家域名
                      content[i]['content'] = content[i]['content'].replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                        if(capture.indexOf('http') == -1){
                          var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                          match = match.replace(capture,newSrc)
                        }
                        return match
                      });
                      // 视频添加域名
                      content[i]['content'] = content[i]['content'].replace(/<video [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                        if(capture.indexOf('http') == -1){
                          var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                          match = match.replace(capture,newSrc)
                        }
                        return match
                      });
                  }
                  if(content[i]['type'] == 'header'){
                      content[i]['order_link'] = '/shop/order/index/'+id;
                  }
                  if(content[i]['type'] == 'goods'){
                      if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                          content[i]['btnStyle'] = '0';
                      }
                      // 判断商品名显示
                      if(content[i]['goodName']){
                          content[i].title = 'info-title';
                      }else{
                          content[i].title = 'info-no-title'
                      }
                      // 判断商品名显示
                      // alert(content[i]['priceShow']);
                      // 判断价格显示
                      if(content[i]['priceShow']){
                          content[i].priceClass = 'info-price';
                      }else{
                          content[i].priceClass = 'info-no-price'
                      }
                      if(!content[i]['goodName'] && !content[i]['priceShow']){
                          content[i].hide_all = 'hide';
                      }
                      // 按钮显示样式
                      if(content[i]['btnStyle'] == 1){
                          content[i].btnClass = 'btn1';
                      }else if(content[i]['btnStyle'] == 2){
                          content[i].btnClass = 'btn2';
                      }else if(content[i]['btnStyle'] == 3){
                          content[i].btnClass = 'btn3';
                      }else if(content[i]['btnStyle'] == 4){
                          content[i].btnClass = 'btn4';
                      }else{
                          content[i].btnClass = 'btn0';
                      }

                      // 判断是否有商品简介
                      if(content[i]['goodInfo']){
                          content[i].has_sub_title = 'has-sub-title';
                      }
                      if(content[i]['cardStyle'] == 1){
                          content[i].list_style = 'card';
                      }else if(content[i]['cardStyle'] == 3){
                          content[i].list_style = 'normal';
                      }else if(content[i]['cardStyle'] == 4){
                          content[i].list_style = 'promotion';
                      }
                      if(content[i].goods == undefined){
                          content[i].goods = [];
                      }
                      if(content[i]['goods'].length>0){
                          content[i]['thGoods'] = [];
                          for(var j =0; j< content[i]['goods'].length;j++){
                              content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
                              if(content[i].thGoods.length > 0){
                                  if(content[i]['thGoods'][content[i]['thGoods'].length - 1].length>=3){
                                      content[i]['thGoods'].push([]);
                                      content[i]['thGoods'][content[i]['thGoods'].length-1].push(content[i]['goods'][j])
                                  }else{
                                      content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                  }
                              }else{
                                  content[i]['thGoods'][0] = [];
                                  content[i]['thGoods'][0].push(content[i]['goods'][j])
                              }
                          }
                      }
                  }
                  if(content[i]['type'] == 'goodslist'){
                      if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                          content[i]['btnStyle'] = '0';
                      }
                      // 判断商品名显示
                      if(content[i]['goodName']){
                          content[i].title = 'info-title';
                      }else{
                          content[i].title = 'info-no-title'
                      }
                      // 判断价格显示
                      if(content[i]['priceShow']){
                          content[i].priceClass = 'info-price';
                      }else{
                          content[i].priceClass = 'info-no-price'
                      }
                      if(!content[i]['goodName'] && !content[i]['priceShow']){
                          content[i].hide_all = 'hide';
                      }
                      // 按钮显示样式
                      if(content[i]['btnStyle'] == 1){
                          content[i].btnClass = 'btn1';
                      }else if(content[i]['btnStyle'] == 2){
                          content[i].btnClass = 'btn2';
                      }else if(content[i]['btnStyle'] == 3){
                          content[i].btnClass = 'btn3';
                      }else if(content[i]['btnStyle'] == 4){
                          content[i].btnClass = 'btn4';
                      }else{
                          content[i].btnClass = 'btn0';
                      }

                      // 判断是否有商品简介
                      if(content[i]['goodInfo']){
                          content[i].has_sub_title = 'has-sub-title';
                      }
                      if(content[i]['cardStyle'] == 1){
                          content[i].list_style = 'card';
                      }else if(content[i]['cardStyle'] == 3){
                          content[i].list_style = 'normal';
                      }else if(content[i]['cardStyle'] == 4){
                          content[i].list_style = 'promotion';
                      }
                      if(content[i].goods == undefined){
                          content[i].goods = [];
                      }
                      if(content[i]['goods'].length>0){
                          content[i]['thGoods'] = [];
                          for(var j =0; j< content[i]['goods'].length;j++){
                              content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
                              if(content[i].thGoods.length > 0){
                                  if(content[i]['thGoods'][content[i]['thGoods'].length - 1].length>=3){
                                      content[i]['thGoods'].push([]);
                                      content[i]['thGoods'][content[i]['thGoods'].length-1].push(content[i]['goods'][j])
                                  }else{
                                      content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                  }
                              }else{
                                  content[i]['thGoods'][0] = [];
                                  content[i]['thGoods'][0].push(content[i]['goods'][j])
                              }
                          }
                      }
                  }
                  // 标题
                  if(content[i]['type'] == 'title'){
                      if(content[i]['titleStyle'] == 2){
                          content[i]['bgColor'] = '#fff';
                      }
                  }
                  //商品分组
                  if(content[i]['type'] == 'good_group'){
                      if(content[i]['top_nav'].length > 0){
                          for(var z = 0;z<content[i]['top_nav'].length;z++){
                              content[i]['top_nav'][z]['href'] = 'top_nav_'+ randomString(12);
                              content[i]['top_nav'][z]['isActive'] =  false;
                              content[i]['top_nav'][z]['width'] =  content[i]['width'] + '%';
                              if(z == 0){
                                  content[i]['top_nav'][z]['isActive'] =  true;
                              }
                              if(content[i]['group_type'] == 2 && content[i]['top_nav'][z]['goods'].length>0){
                                  for(var j = 0;j<content[i]['top_nav'][z]['goods'].length;j++){
                                      content[i]['top_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['top_nav'][z]['goods'][j]['thumbnail'];
                                      if(content[i]['top_nav'][z]['goods'][j]['is_price_negotiable'] == 1){
                                          content[i]['top_nav'][z]['goods'][j]['price'] = content[i]['top_nav'][z]['goods'][j]['price'];
                                      }else{
                                          content[i]['top_nav'][z]['goods'][j]['price'] = '￥' + content[i]['top_nav'][z]['goods'][j]['price'];
                                      }
                                  }
                              }
                          }
                          
                      }
                      if(content[i]['left_nav'].length > 0){
                          // console.log(content[i]['left_nav']);
                          for(var z = 0;z<content[i]['left_nav'].length;z++){
                              content[i]['left_nav'][z]['href'] = 'top_nav_'+ randomString(12);
                              content[i]['left_nav'][z]['isActive'] =  false;
                              if(z == 0){
                                  content[i]['left_nav'][z]['isActive'] =  true;
                              }
                              if(content[i]['group_type'] == 1 && content[i]['left_nav'][z]['goods'].length>0){
                                  for(var j = 0;j<content[i]['left_nav'][z]['goods'].length;j++){
                                      content[i]['left_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['left_nav'][z]['goods'][j]['thumbnail'];
                                      // content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                      if(content[i]['left_nav'][z]['goods'][j]['is_price_negotiable'] == 1){
                                          content[i]['left_nav'][z]['goods'][j]['price'] = content[i]['left_nav'][z]['goods'][j]['price'];
                                      }else{
                                          content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                      }
                                  }
                              }
                          }
                          
                      }
                  }
                  obj.push(content[i]);
                  if(content[i]['type'] == 'image_ad'){
                      console.log(content[i])
                      if(content[i].images.length>0){
                          for(var j = 0;j<content[i].images.length;j++){
                              obj[i].images[j]['FileInfo']['path'] = imgUrl + obj[i].images[j]['FileInfo']['path'];
                          }
                      }
                      
                  }
                  if(content[i]['type'] == 'image_link'){
                      if(content[i]['images'].length > 0){
                          for(var j=0;j<content[i]['images'].length;j++){
                              content[i]['images'][j]['thumbnail'] = imgUrl + content[i]['images'][j]['thumbnail'];
                          }
                      }
                  }
              }
          }
      }
      //add by 韩瑜 2018-9-6 是否收藏
      this.$http.get('/shop/member/isFavorite?type=1&relativeId='+seckill.id).then(function(res){
          console.log(res)
          this.isFavorite = res.body.data.isFavorite
      })
  },
  methods:{
      /* add by 韩瑜
       * date 2018-9-6
       * 收藏点击事件
       */
      collect:function(){
          this.isFavorite = true
          $('.iscollecttip').show()
          setTimeout(function () {
              $('.collecttip').hide()
          },1500)
          this.$http.post('/shop/member/favorite',{
              type: 1,
              relativeId: seckill.id,
              _token: $("meta[name='csrf-token']").attr("content"),
              title: seckill.title,
              price: seckill.price_range,
              image: product.product.img
          }).then(function(res){
              if(res.body.status != 1){
                  this.isFavorite = false
              }
          })
      },
      //取消收藏
      collectcancel:function(){
          this.isFavorite = false
          $('.nocollecttip').show()
          setTimeout(function () {
              $('.collecttip').hide()
          },1500)
          this.$http.post('/shop/member/cancelFavorite',{
              type: 1,
              relativeId: seckill.id,
              _token: $("meta[name='csrf-token']").attr("content"),
          }).then(function(res){
              if(res.body.status != 1){
                  this.isFavorite = true
              }
          })
      },
      //end
  }
})
function randomString(len) {  
　　len = len || 32;  
　　var $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';  
　　var maxPos = $chars.length;  
　　var pwd = '';  
　　for (i = 0; i < len; i++) {  
      //0~32的整数  
　　　　pwd += $chars.charAt(Math.floor(Math.random() * (maxPos+1)));  
　　}  
　　return pwd;  
}