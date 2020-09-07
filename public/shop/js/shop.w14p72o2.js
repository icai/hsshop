$(function() {
  var couponPrice = 0 //优惠券价格
  var val = '' //积分用合计金额变量
  //是否使用积分
  var activityId = getQueryString('activityId')
  var num = getQueryString('num')
  var skuId = getQueryString('skuId')
  window.onload = function() {
    $('.no_address_data').attr({
      href:
        '/shop/member/addAddress?come=good&activityId=' +
        activityId +
        '&num=' +
        num +
        '&skuId=' +
        skuId
    })
  }

  $('.shareEventFix').attr({
    href:
      '/shop/member/showAddress?come=good&activityId=' +
      activityId +
      '&num=' +
      num +
      '&skuId=' +
      skuId
  })
  $('.js-integral-switcher').click(function() {
    var is_on = $(this).attr('data-is-on')
    if ($(this).hasClass('ui-switcher-off')) {
      $(this).removeClass('ui-switcher-off')
      $(this).addClass('ui-switcher-on')
      $(this).attr('data-is-on', 1)
      $('.order-total-integral').show()
      var last_amount = $('#last_amount').attr('data-price')
      var span_dxprice = $('#span_dxprice').html()
      var result = (parseFloat(last_amount) - parseFloat(span_dxprice)).toFixed(
        2
      )
    } else {
      $(this).removeClass('ui-switcher-on')
      $(this).addClass('ui-switcher-off')
      $(this).attr('data-is-on', 0)
      $('.order-total-integral').hide()
      var last_amount = $('#last_amount').attr('data-price')
    }
    computeTotal()
  })
  //短信通知收件人
  $('.js-msg-switcher').click(function() {
    if ($(this).hasClass('ui-switcher-off')) {
      $(this).removeClass('ui-switcher-off')
      $(this).addClass('ui-switcher-on')
      postData.is_send = 1
    } else {
      $(this).removeClass('ui-switcher-on')
      $(this).addClass('ui-switcher-off')
      postData.is_send = 0
    }
  })
  var longitude, latitude
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          longitude = position.coords.longitude
          latitude = position.coords.latitude
        },
        function(e) {
          var msg = e.code
          var dd = e.message
        }
      )
    }
  }
  // 自提、配送按钮切换
  var youhui = goods_price - pay_price + freight
  $('.J_switch-method-btn').click(function() {
    $(this)
      .addClass('active')
      .siblings('.active')
      .removeClass('active')
    $('.J_self-fetch-invalid').addClass('hide')
    if ($(this).data('type') == 'express') {
      $('.J_express').removeClass('hide')
      $('.J_self-fetch').addClass('hide')
      $('.J_fetch-method').text('快递发货')
      $('.J_freight').text('￥' + $('.J_freight').data('freight'))
      $('.J_hexiao-express').addClass(' js-express-info arrow')
      $('.J_ziti-price').text('￥' + goods_price_temp)
      goods_price = goods_price_temp
      $('.J_ziti-price-last').data('price', pay_price)
      computeTotal()

      if (distributionData.status == 2) {
        var html = '',
          item
        var data = distributionData.data.Logistics.concat(
          distributionData.data.ziti
        )
        for (var i = 0; i < data.length; i++) {
          item = data[i]
          html +=
            '<div class="js-goods-item order-goods-item clearfix block-list" data-id = "' +
            item.cart_id +
            '">'
          html +=
            '<div class="name-card name-card-goods clearfix block-item"><a href="javascript:;" class="thumb"><img class="js-view-image" src="' +
            item.img_path +
            '"></a>'
          html +=
            '<div class="detail"><div class="clearfix detail-row"><div class="right-col text-right"><div class="price">￥<span>' +
            item.price +
            '</span></div></div>'
          html +=
            '<div class="left-col"><a href="javascript:;"><h3 class="l2-ellipsis">' +
            item.product_name +
            '</h3></a></div></div>'
          html +=
            '<div class="clearfix detail-row"><div class="right-col"><div class="num c-gray-darker">×<span class="num-txt">' +
            item.num +
            '</span></div></div>'
          html +=
            '<div class="left-col"><p class="c-gray-darker sku">' +
            item.attr +
            '</p></div></div>'
          html +=
            '<div class="clearfix detail-row"><div class="right-col"><div class="goods-action"></div></div><div class="left-col"></div></div></div></div>'
        }
        $('.js-goods-list').html(html)
      }
    } else if ($(this).data('type') == 'self-fetch') {
      $('.J_self-fetch').removeClass('hide')
      $('.J_express').addClass('hide')
      $('.J_fetch-method').text('到店自提')
      $('.J_freight').text('￥0.00')
      $('.J_hexiao-express').removeClass(' js-express-info arrow')
      var price = 0
      if (distributionData.status == 2) {
        var html = '',
          item
        var data = distributionData.data.ziti
        for (var i = 0; i < data.length; i++) {
          item = data[i]
          price += item.price * item.num
          html +=
            '<div class="js-goods-item order-goods-item clearfix block-list" data-id = "' +
            item.cart_id +
            '">'
          html +=
            '<div class="name-card name-card-goods clearfix block-item"><a href="javascript:;" class="thumb"><img class="js-view-image" src="' +
            item.img_path +
            '"></a>'
          html +=
            '<div class="detail"><div class="clearfix detail-row"><div class="right-col text-right"><div class="price">￥<span>' +
            item.price +
            '</span></div></div>'
          html +=
            '<div class="left-col"><a href="javascript:;"><h3 class="l2-ellipsis">' +
            item.product_name +
            '</h3></a></div></div>'
          html +=
            '<div class="clearfix detail-row"><div class="right-col"><div class="num c-gray-darker">×<span class="num-txt">' +
            item.num +
            '</span></div></div>'
          html +=
            '<div class="left-col"><p class="c-gray-darker sku">' +
            item.attr +
            '</p></div></div>'
          html +=
            '<div class="clearfix detail-row"><div class="right-col"><div class="goods-action"></div></div><div class="left-col"></div></div></div></div>'
        }
        goods_price = price
        $('.js-goods-list').html(html)
        if (distributionData.data.Logistics.length) {
          var data = distributionData.data.Logistics
          var item,
            item2,
            html2 = ''
          var html =
            '<p class="invalid-tips">以下商品无法一起购买，点击查看原因</p><div class="invalid-box J_invalid-box"><div class="invalid-imgs">'
          for (var i = 0; i < data.length; i++) {
            if (i < 3) {
              item = data[i]
              html += '<img src="' + item.img_path + '">'
            }
            item2 = data[i]
            html2 +=
              '<div class="invalid-item"><div class="item-img-box"><img src="' +
              item2.img_path +
              '" class="item-img"><div class="invalid-icon">失效</div></div><div class="item-desc-box">'
            html2 += '<p class="invalid-title">' + item2.product_name + '</p>'
            html2 +=
              '<p class="invalid-spec">' +
              item2.attr +
              '</p><p class="invalid-reason">当前商品不支持自提</p></div><div class="item-price-box">'
            html2 +=
              '<p class="item-price">¥' +
              item2.price +
              '</p><p class="item-count">' +
              item2.num +
              '件</p></div></div>'
          }
          html +=
            '</div><div class="invalid-num">共' +
            data.length +
            '件</div> </div>'
          $('.J_self-fetch-invalid')
            .html(html)
            .removeClass('hide')
          $('.invalid-container').html(html2)
        }
        $('.J_ziti-price-last').data('price', (price - youhui).toFixed(2))
      } else if (distributionData.status == 1) {
        price = goods_price
      }
      $('.J_ziti-price').text('￥' + price.toFixed(2))
      if (price - youhui < 0) {
        $('.J_ziti-price-last').text('￥0.00')
      } else {
        $('.J_ziti-price-last').text('￥' + (price - youhui).toFixed(2))
      }
      computeTotal()
    }
  })
  $('.J_choose-addr').click(function() {
    getLocation()
    $('.addr-popup').removeClass('hide')
    var data = {}
    data._token = $('meta[name="csrf-token"]').attr('content')
    data.longitude = longitude
    data.latitude = latitude
    $.post('/shop/zitiList', data, function(res) {
      if (res.errCode == 0) {
        var addrs = res.data.datas
        if (addrs.length > 0) {
          var html = '',
            item,
            addr
          for (var i = 0; i < addrs.length; i++) {
            item = addrs[i]
            addr = item.province + item.city + item.area + item.address
            html +=
              '<div class="addr-item" data-id="' +
              item.id +
              '" data-addr="' +
              addr +
              '"><span class="radio-btn" ></span><div class="addr-wraper">'
            html += '<p class="addr-distance">' + item.title
            if (+item.distance > 0) {
              html += '，约 ' + item.distance + ' km'
            }
            html +=
              '</p><p class="addr-detail">地址：' + addr + '</p></div></div>'
          }
          $('.addr-container').html(html)
        } else {
          tool.tip('暂无数据')
          $('.addr-container').html('')
        }
        var citys = res.data.citys
        if (citys.length > 0) {
          var html = '',
            item
          for (var i = 0; i < citys.length; i++) {
            item = citys[i]
            html += '<option value="' + item.id + '">' + item.city + '</option>'
          }
          $('.addr-city').html(html)
        }
      } else {
        tool.tip(res.errMsg)
      }
    })
  })
  $('.J_self-fetch-invalid').on('click', '.J_invalid-box', function() {
    $('#invalid-reason-wrap').show()
    $('#invalid-reason').show()
  })
  $('.js-invalid-cancel').click(function() {
    $('#invalid-reason-wrap').hide()
    $('#invalid-reason').hide()
  })
  $('#invalid-reason-wrap').click(function() {
    $('#invalid-reason-wrap').hide()
    $('#invalid-reason').hide()
  })

  //团长优惠按钮点击事件
  $('.group-ellipsis').click(function() {
    $('.group-popup').show()
    var html =
      '<div class="t-mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 999; transition: none 0.2s ease; opacity: 1;"></div>'
    $('body').append(html)
  })

  //点击遮罩事件
  $('body').on('click', '.t-mask', function() {
    $('.group-popup').hide()
    $(this).remove()
  })

  $('body').on('click', '.cancel-img', function() {
    $('.group-popup').hide()
    $('.t-mask').remove()
  })

  // 提交订单点击
  var postData = {
    cart_id: [],
    is_send: 1,
    express_no: 1,
    coupon_id: 0,
    remark: ''
  }
  for (var i = 0; i < $('.js-goods-item').length; i++) {
    postData.cart_id.push($($('.js-goods-item')[i]).data('id'))
  }
  //提交订单
  $('.commit-bill-btn').click(function() {
    if (distributionData.status == 1 || distributionData.status == 2) {
      if ($('.J_self-fetch-btn').hasClass('active')) {
        if ($('.js_name-container').val() == '') {
          tool.tip('请输入提货人姓名')
          return
        }
        if ($('.js_phone-container').val() == '') {
          tool.tip('请输入提货人手机号')
          return
        } else {
          var reg = /^1[3,4,5,7,8]\d{9}$/
          if (!reg.test($('.js_phone-container').val() + '')) {
            tool.tip('手机号格式不正确！')
            return
          }
        }
        if ($('.J_choose-addr').data('id') == '') {
          tool.tip('请输入提货地址')
          return
        }
        if ($('.J_choose-time').data('time') == '') {
          tool.tip('请输入提货时间')
          return
        }
      }
    }
    // update by 黄新琴  2018-7-30 9:25 判断当前环境，如果是支付宝小程序打开，调用支付宝支付或者余额支付
    if (reqFrom == 'aliapp') {
      var str ='<nav class="sel-pay-wrap">';
      if (parseFloat(balance) >= parseFloat(pay_price)) {
        str +=
          '<a href="javascript:;" id="alipayYuerzf">储值余额支付（剩余￥' +
          balance +
          '）</a>'
        str += '<a href="javascript:;" id="alipay">支付宝支付</a>'
      } else {
        str += '<a href="javascript:;" id="alipay">支付宝支付</a>'
        str +=
          '<a href="javascript:;" id="alipayYuerzf" class="disabled">储值余额支付（余额不足）</a>'
      }
      str +='</nav>';
    } 
    // add by 韩瑜 2018-10-17 百度支付
    else if (reqFrom == 'baiduapp') {
      if (parseFloat(balance) >= parseFloat(pay_price)) {
        str +=
          '<a href="javascript:;" id="yuerzf">储值余额支付（剩余￥' +
          balance +
          '）</a>'
        str += '<a href="javascript:;" id="baidupay">百度收银台支付</a>'
      } else {
        str += '<a href="javascript:;" id="baidupay">百度收银台支付</a>'
        str +=
          '<a href="javascript:;" id="yuerzf" class="disabled">储值余额支付（余额不足）</a>'
      }
    }
    else {
      var str ='<nav class="sel-pay-wrap order_pay"><div class="order_pay_title pay_bottom">选择支付方式</div>';
      if (parseFloat(balance) >= parseFloat(pay_price)) {
        str += '<div class="order_balance_pay pay_bottom" id="yuerzf">'+
            '<div data-id="1">'+
            '<div class="balance_img"></div>'+
            '<a href="javascript:;">储值余额支付（剩余￥' + balance + '）</a>'+
            '</div>'+
            '<div class="order_pay_way" data-id="2">'+
            '<div class="ap-weixuan hide"></div>'+
            '<div class="dui"></div>'+
            '</div>'+
            '</div>'+
            '<div class="order_balance_pay pay_bottom" id="weixinzf">'+
            '<div data-id="2">'+
            '<div class="balance_img weixin_img"></div>'+
            '<a href="javascript:;">微信支付</a>'+
            '</div>'+
            '<div class="order_pay_way" data-id="1">'+
            '<div class="ap-weixuan"></div>'+
            '<div class="dui hide"></div>'+
            '</div>'+
            '</div>'
      } else {
        str += '<div class="order_balance_pay pay_bottom" id="weixinzf">'+
          '<div data-id="2">'+
          '<div class="balance_img weixin_img"></div>'+
          '<a href="javascript:;">微信支付</a>'+
          '</div>'+
          '<div class="order_pay_way" data-id="2">'+
          '<div class="ap-weixuan hide"></div>'+
          '<div class="dui"></div>'+
          '</div>'+
          '</div>'+
          '<div class="order_balance_pay pay_bottom" id="yuerzf">'+
          '<div data-id="1">'+
          '<div class="balance_img"></div>'+
          '<a href="javascript:;">储值余额支付（剩余￥0元）</a>'+
          '</div>'+
          '<div class="order_pay_way" data-id="1">'+
          '<div class="ap-weixuan"></div>'+
          '<div class="dui hide"></div>'+
          '</div>'+
          '</div>'
      }
      str += '<div class="confirm_btn"><p>确认</p></div></nav>';
    }

    $('body').append("<div class='sel-mask'></div>")
    $('body').append(str);
      //余额支付
      $(".confirm_btn").on("click",function(e){
          e.stopPropagation();
          if(payFlag){
              payFlag = false
              var box = $(".order_pay_way")
              for(var i = 0; i < box.length; i++){
                  var id = $(box[i]).attr('data-id')
                  if(id == 2){
                      var pay_id = $(box[i]).siblings('div').attr('data-id')
                      if(pay_id == 1){
                          if(parseFloat(balance) < parseFloat($('#last_amount').text().substr(1,$('#last_amount').text().length))){
                              tool.tip('余额不足');
                              return;
                          }
                          publicBay(3)
                      }else if(pay_id == 2){
                          publicBay(1)
                      }
                  }
              }
          }
      });
      //支付按钮点击监听
      $("#weixinzf").on("click",function(e){
          e.stopPropagation();
          var id = $(this).children('.order_pay_way').attr('data-id')
          if(id == 2){
              return false
          }
          $(this).children('.order_pay_way').attr('data-id','2').children('.dui').removeClass("hide").siblings('.ap-weixuan').addClass("hide")
          $(this).siblings('div').children('.order_pay_way').attr('data-id','1').children('.dui').addClass("hide").siblings('.ap-weixuan').removeClass("hide")
      });
      $("#yuerzf").on("click",function(e){
          e.stopPropagation();
          var id = $(this).children('.order_pay_way').attr('data-id')
          if(id == 2){
              return false
          }
          $(this).children('.order_pay_way').attr('data-id','2').children('.dui').removeClass("hide").siblings('.ap-weixuan').addClass("hide")
          $(this).siblings('div').children('.order_pay_way').attr('data-id','1').children('.dui').addClass("hide").siblings('.ap-weixuan').removeClass("hide")
      });
  })
  //移除支付弹窗
  $('body').on('click', '.sel-mask', function(event) {
    $(this).remove()
    $('.sel-pay-wrap').remove()
  })


  //add by 黄新琴  2018-7-30  09:15   支付宝支付按钮点击监听
  $('body').on('click', '#alipay', function(e) {
    e.stopPropagation()
    publicBay(4)
  })
  
  //add by 韩瑜  2018-10-17 百度收银台支付
  $('body').on('click', '#baidupay', function(e) {
    e.stopPropagation()
    publicBay(5)
  })

//余额支付
    $("body").on("click","#alipayYuerzf",function(e){
        e.stopPropagation();
        if(!$(this).hasClass("disabled")){
            tool.confirm("<p style='text-align:center;font-size:16px;'>确定支付？</p>",function(){
                publicBay(3);
            });
        }
    });
    var payFlag = true
  /**
   * @param  支付方式 1.微信支付 3.余额支付 4.支付宝小程序--支付宝支付 5.百度收银台支付
   * @return null
   * update by 黄新琴 2018-7-30 9:10
   */
  function publicBay(payment) {
    postData.cart_id = []
    for (var i = 0; i < $('.js-goods-item').length; i++) {
      postData.cart_id.push($($('.js-goods-item')[i]).data('id'))
    }
    var data = {
      cart_id: '[' + postData.cart_id.toString() + ']',
      is_send: postData.is_send,
      express_no: 1,
      coupon_id: postData.coupon_id,
      remark: $('.js-msg-container').val(),
      _token: $('meta[name="csrf-token"]').attr('content')
    }
    data.address_id = $('input[name="address_id"]').val()
    var jf_is_on = $('.js-integral-switcher').attr('data-is-on')
    if (jf_is_on == '1') {
      // 积分
      var span_point = +$('#span_jf').html()
        data.point = span_point;
    }
    // 自提订单
    if ($('.J_self-fetch-btn').hasClass('active')) {
      data.isHexiao = 1
      data.zitiContact = $('.js_name-container').val()
      data.zitiPhone = $('.js_phone-container').val()
      data.zitiId = $('.J_choose-addr').data('id')
      data.zitiDatetime = $('.J_choose-time').data('time')
    }
    var GetData = {
      addressId: data.address_id,
      activityId: activityId,
      skuId: skuId,
      remark_no: data.remark,
      num: num,
      _token: data._token
    }
    $.ajax({
      type: 'POST',
      url: '/shop/shareevent/order/submit',
      data: GetData,
      dataType: 'json',
      success: function(msg) {
        if (msg.errCode == 0) {
          var order_id = msg.data.orderNo;
          // add by 黄新琴 payment为4唤起支付宝支付
          if (payment == 4) {
            my.navigateTo({url:'/pages/shop/alipay/alipay?id='+order_id});
          } 
          // add by 韩瑜 百度收银台支付跳转
          else if (payment == 5) {
          	swan.webView.navigateTo({url:'/pages/baidupay/baidupay?id=' + order_id});
          }
          else {
            window.location.href = '/shop/pay/index?id=' + order_id + '&payment=' + payment;
          }
        } else if (msg.errCode == -5) {
          tool.tip(msg.errMsg)
        } else if (msg.errCode == -7) {
          tool.tip('下单失败')
        } else {
          tool.tip(msg.errMsg)
        }
      },
      error: function(msg) {
        tool.tip('生成订单失败！')
      }
    })
  }
  /**
   * @param  获取url参数
   * @return url params
   */
  function GetRequest(name) {
    var url = location.search //获取url中"?"符后的字串
    var theRequest = new Object()
    if (url.indexOf('?') != -1) {
      var str = url.substr(1)
      strs = str.split('&')
      for (var i = 0; i < strs.length; i++) {
        theRequest[strs[i].split('=')[0]] = strs[i].split('=')[1]
      }
    }
    return theRequest[name]
  }

  /*
    * 统计计算合计方法
    *
    */
  function computeTotal() {
    //合计金额=商品金额+运费-优惠-抵现金额
    var result = parseFloat(goods_price) + parseFloat(freight)
    var result1 = parseFloat(use_point_amount)
    var dx_price = no_coupon_bonus_points || 0 //抵现金额 默认使用不使用优惠券的积分抵现金额
    var coupon_amount
    //判断是否使用优惠
    if ($('.couponItem.active').length > 0) {
      //使用优惠券
      coupon_amount = couponPrice
      result -= parseFloat(coupon_amount)
      result1 = parseFloat(use_point_amount) - parseFloat(coupon_amount)
      dx_price = bonus_points || 0
    }
    var is_on = $('.js-integral-switcher').attr('data-is-on') //积分开关
    var alt
    if (result1 > 0) {
      // 大于0的时候才掉积分借口
      $.ajax({
        //积分抵现接口
        type: 'get',
        url: '/shop/point/showPoint',
        data: { amount: result1 },
        async: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
          if (res.status == 1) {
            $('#span_jf').text(res.data.point)
            $('#span_dxprice').text(res.data.amount)
            $('.zx_darker').html('-￥' + res.data.amount)
            if (is_on) {
              //开启>计算总金额
              dx_price = $('#span_dxprice').text()
              coupon_amount = couponPrice || 0
              result = parseFloat(result - parseFloat(dx_price))
            }
          }
        },
        error: function() {
          console.log('失败')
        }
      })
    } else {
      $('#span_jf').text(0)
      $('#span_dxprice').text(0)
      $('.zx_darker').html('-￥' + 0)
      if (is_on) {
        //开启>计算总金额
        dx_price = $('#span_dxprice').text()
        coupon_amount = couponPrice || 0
        result = parseFloat(result - parseFloat(dx_price))
      }
    }
    if ($('.js-integral-switcher').hasClass('ui-switcher-off')) {
      //关闭>计算总金额
      result = parseFloat(goods_price) + parseFloat(freight)
      if ($('.noCoupon').hasClass('hide')) {
        //使用优惠券
        var coupon_amount = couponPrice || 0
        result -= parseFloat(coupon_amount)
        dx_price = bonus_points || 0
      }
    }
    if (result <= 0) {
      result = '0.00'
    } else {
      result = result.toFixed(2)
    }
    val = result
    if ($('.J_self-fetch-btn').hasClass('active')) {
      if ((result - parseFloat(freight)).toFixed(2) < 0) {
        $('.js-price').html('￥0.00')
      } else {
        $('.js-price').html('￥' + (result - parseFloat(freight)).toFixed(2))
      }
    } else {
      $('.js-price').html('￥' + result)
    }

    if (distributionData.status == 2 || distributionData.status == 1) {
      youhui = result - goods_price_temp - freight
    }
  }

  // 支付背景点击
  $('#XkYNfCpz6p').click(function() {
    $(this).hide()
    $('#ltFCNUqxmZ').hide()
  })
  // 微信支付点击
  $('.btn-wxwappay').click(function() {
    $('#M0OoCHUeQQ').show()
    $('#FiG1ldky0n').show()
  })
  // 微信支付取消
  $('.js-cancel-wechat-pay').click(function() {
    window.location.reload()
  })
  // 微信支付完成点击
  $('.js-ok-wechat-pay').click(function() {
    window.location.reload()
  })
  // 使用优惠券点击
  $('.js-change-coupon').click(function() {
    $('#Z0Coldw5h6').show()
    $('#lf1SYltCN4').show()
  })
  // 优惠券背景点击
  $('#Z0Coldw5h6').click(function() {
    $(this).hide()
    $('#lf1SYltCN4').hide()
  })
  // 确定使用优惠券点击
  $('.js-confirm-use-coupon').click(function() {
    $('.js-coupon-list .coupon-item').each(function(key, val) {
      if ($(this).hasClass('active')) {
        var title = $(this)
          .find('.coupon-info')
          .children('p')
          .eq(0)
          .children('span')
          .text()
        $('.coupon_list span').html(title)
      }
    })
    $('#Z0Coldw5h6').hide()
    $('#lf1SYltCN4').hide()
  })

  // 快递发货点击
  $('.js-express-info').click(function() {
    $('#P9bxm4G8NL').show()
    $('#ko7oBEIP8n').show()
  })
  // 快递发货背景点击
  $('#P9bxm4G8NL').click(function() {
    $(this).hide()
    $('#ko7oBEIP8n').hide()
  })
  // 快递发货关闭点击
  $('body').on('click', '.cancel-img', function() {
    $('#P9bxm4G8NL').hide()
    $('#ko7oBEIP8n').hide()
    $('#GkLmo6UNYU').hide()
    $('#7yBNPekNdX').hide()
  })
  var patrn = /[`~!@#$%^&*()_\-+=<>?:"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]/im //特殊符号处理
  var address_id = 0
  var index = 0 //记录点击地址那个选项
  // 省市区三级联动
  /* 选择省 拉取后台城市数据并展示 重置地区数据 */
  $('body').on('change', '.address-province', function() {
    var id = $(this).val()
    if (id) {
      var city_arr = regions_datas[id]
      var str_city = '<option value="">选择城市</option>'
      for (var i = 0; i < city_arr.length; i++) {
        str_city +=
          '<option value="' +
          city_arr[i]['id'] +
          '">' +
          city_arr[i]['title'] +
          '</option>'
      }
      $('.address-city').html(str_city)
    } else {
      $('.address-city').html('<option value="">选择城市</option>')
    }
    $('.address-county').html('<option value="">选择地区</option>')
  })
  /* 选择城市 拉取后台地区数据并展示 */
  $('body').on('change', '.address-city', function() {
    var id = $(this).val()
    if (id) {
      var town_arr = regions_datas[id]
      var str_town = '<option value="">选择地区</option>'
      for (var i = 0; i < town_arr.length; i++) {
        str_town +=
          '<option value="' +
          town_arr[i]['id'] +
          '">' +
          town_arr[i]['title'] +
          '</option>'
      }
      $('.address-county').html(str_town)
    } else {
      $('.address-county').html('<option value="">选择地区</option>')
    }
  })

  // 选择地址弹框事件
  $('.J_search-input').focus(function() {
    $('.calcel-btn').show()
  })
  $('.calcel-btn').click(function() {
    $('.J_search-input').val('')
  })
  $('.search-icon').click(function() {
    $('.search-box').show()
    $('.select-box').hide()
  })
  $('.addr-container').on('click', '.addr-item', function() {
    $(this)
      .children('.radio-btn')
      .addClass('active-radio')
      .parent()
      .siblings()
      .children('.radio-btn')
      .removeClass('active-radio')
    $('.addr-popup').addClass('hide')
    $('.J_choose-addr')
      .data('id', $(this).data('id'))
      .text($(this).data('addr'))
  })
  $('.J_search').click(function() {
    var key = $('.J_search-input').val()
    var data = {}
    data._token = $('meta[name="csrf-token"]').attr('content')
    data.longitude = longitude
    data.latitude = latitude
    // if (key!='') {
    data.keyword = key
    $.post('/shop/zitiList', data, function(res) {
      if (res.errCode == 0) {
        var addrs = res.data.datas
        if (addrs.length > 0) {
          var html = '',
            item,
            addr
          for (var i = 0; i < addrs.length; i++) {
            item = addrs[i]
            addr = item.province + item.city + item.area + item.address
            html +=
              '<div class="addr-item" data-id="' +
              item.id +
              '" data-addr="' +
              addr +
              '"><span class="radio-btn" ></span><div class="addr-wraper">'
            html += '<p class="addr-distance">' + item.title
            if (+item.distance > 0) {
              html += '，约 ' + item.distance + ' km'
            }
            html +=
              '</p><p class="addr-detail">地址：' + addr + '</p></div></div>'
          }
          $('.addr-container').html(html)
        } else {
          tool.tip('暂无数据')
          $('.addr-container').html('')
        }
      } else {
        tool.tip(res.errMsg)
      }
    })
  })
  $('.js_phone-container').blur(function() {
    var reg = /^1[3,4,5,7,8]\d{9}$/
    if (!reg.test($(this).val() + '')) {
      tool.tip('手机号格式不正确！')
    }
  })
  $('.addr-city').change(function() {
    var index = $(this)[0].selectedIndex
    var id = $(this)[0].options[index].value
    var data = {}
    data._token = $('meta[name="csrf-token"]').attr('content')
    data.id = id
    data.longitude = longitude
    data.latitude = latitude
    $.post('/shop/zitiList', data, function(res) {
      if (res.errCode == 0) {
        var addrs = res.data.datas
        if (addrs.length > 0) {
          var html = '',
            item,
            addr
          for (var i = 0; i < addrs.length; i++) {
            item = addrs[i]
            addr = item.province + item.city + item.area + item.address
            html +=
              '<div class="addr-item" data-id="' +
              item.id +
              '" data-addr="' +
              addr +
              '"><span class="radio-btn" ></span><div class="addr-wraper">'
            html += '<p class="addr-distance">' + item.title
            if (+item.distance > 0) {
              html += '，约 ' + item.distance + ' km'
            }
            html +=
              '</p><p class="addr-detail">地址：' + addr + '</p></div></div>'
          }
          $('.addr-container').html(html)
        } else {
          tool.tip('暂无数据')
          $('.addr-container').html('')
        }
      } else {
        tool.tip(res.errMsg)
      }
    })
  })
  // 从字符串中取数字
  function getNum(text, str) {
    var value = text.split(str)
    return value[1]
  }

  var isiphone = IsPC()
  if (!isiphone) {
    // 监听小键盘弹起
    var winHeight = $(window).height() //获取当前页面高度
    $(window).resize(function() {
      var thisHeight = $(this).height()
      if (winHeight - thisHeight > 50) {
        //当软键盘弹出，在这里面操作
        $('.action-container').hide()
      } else {
        //当软键盘收起，在此处操作
        $('.action-container').show()
      }
    })
  }
  function IsPC() {
    var userAgentInfo = navigator.userAgent
    var Agents = [
      'Android',
      'iPhone',
      'SymbianOS',
      'Windows Phone',
      'iPad',
      'iPod'
    ]
    var flag = true
    for (var v = 0; v < Agents.length; v++) {
      if (userAgentInfo.indexOf(Agents[v]) > 0) {
        flag = false
        break
      }
    }
    return flag
  }

  var cartIDArr = '[' + postData.cart_id.toString() + ']'
  getFreight() //获取运费数据
  function getFreight() {
    $.ajax({
      url: '/shop/shareevent/order/feight',
      data: {
        productId: productId,
        skuId: getQueryString('skuId'),
        addressId: $('input[name="address_id"]').val()
      },
      methods: 'get',
      success: function(res) {
        // return false
        $('.freight').html('¥' + res.data)
        //切换收货地址更新运费
        $('.sum-freight').html('+¥' + res.data)
        // 切换地址时候 合计的显示
        $('#last_amount').text('￥'+((+productAmount)+(+res.data)).toFixed(2)) 
      }
    })
  }
  /**
   * author->huakang date->2018/06/22
   * @param key 想取得urlQueryString参数名
   * @type string
   * @returns value 想取得urlQueryString参数值
   */
  function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i')
    var reg_rewrite = new RegExp('(^|/)' + name + '/([^/]*)(/|$)', 'i')
    var r = window.location.search.substr(1).match(reg)
    var q = window.location.pathname.substr(1).match(reg_rewrite)
    if (r != null) {
      return unescape(r[2])
    } else if (q != null) {
      return unescape(q[2])
    } else {
      return null
    }
  }
  $(".name-card-goods").click(function(){
    window.location.href="/shop/product/detail/"+ wid +"/"+ productId + "?activityId=" + activityId
  })




})
