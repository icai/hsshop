$(function() {
  var swiper = new Swiper('.swiper-container', {
    pagination: '.swiper-pagination',
    paginationClickable: true,
    autoplay: 5000,
    speed: 1000,
    observer: true, //修改swiper自己或子元素时，自动初始化swiper 解决个别不loop
    loop: true
  })
  var ways = 0 //1 为加入购物车   0 为立即购买
  $('.js-cancel').click(function() {
    $('#Xms3Sq4JR6').hide()
    $('#LBqDHKuruf').hide()
  })
  $('.js-buy-it').click(function() {
    $('video').css('display', 'none')
    var that = this
    // if (isBind) {
    //   tool.bingMobile(function() {
    //     isBind = 0
    //     buyIt(vm1.now_price, vm1.count, vm1.unitAmount,vm1.alllowerPrice,vm1.lowerPrice)
    //   })
    //   return
    // }
    buyIt(vm1.now_price,vm1.count, vm1.unitAmount, vm1.alllowerPrice,vm1.lowerPrice)
  })

    /**
     * @auther 邓钊
     * @desc 选择规格
     * @data 2018-10-22
     * @param
     * @return
     *
     * */
  $(".selectSku").on('click',function () {
      buyIt(vm1.now_price,vm1.count, vm1.unitAmount, vm1.alllowerPrice,vm1.lowerPrice)
  })
    //购买
  function buyIt(oPrice, shareCount, shareUnitedAmount,reduce_total,lowerPrice) {
    ways = 0;
    // 判断是否是分享者
    var isShareLi = false;
    if(vm1.isShare==1){
      isShareLi = true;
    }else{
      // 参与享立减则购买价格为原价
      oPrice = vm1.price
    }
    tool.spec.open({
      type: 2, //1为 多按钮  2 为单按钮
      callback: callback, //点击规格按钮返回数据
      url: '/shop/product/getSku', //获取规格接口
      data: {
        // 获取规格参数
        _token: $("meta[name='csrf-token']").attr('content'),
        pid: product.id
      },
      initSpec: {
        // 默认商品数据
        title: product.title,
        img: product.img,
        stock: product.stock,
        price: oPrice,
        wholesale_flag: product.wholesale_flag,
        wholesale_array: product.wholesale_array
      },
      unActive: 1, //非拼团活动
      isEdit: true, //点x  不保存数据
      buyCar: false, //按钮  为单按钮  加入购物车  可不写
      limit_num: 1,
      // buy_min: product.buy_min,
      isShareLi: isShareLi,
      shareCount: shareCount,
      shareUnitedAmount: shareUnitedAmount,
      reduce_total:reduce_total,
      lowerPrice:lowerPrice
    })
  }
  // 购物车点击
  $('#global-cart').click(function() {
    //规格选择后回调
    var that = $(this)
    // if (isBind) {
    //   tool.bingMobile(function() {
    //     isBind = 0
    //     window.location.href = '/shop/cart/index/' + that.data('id')
    //   })
    //   return
    // }
    window.location.href = '/shop/cart/index/' + $(this).data('id')
  })
  //规格选择后回调
  function callback(data) {
    if (!data.data.spec_id && product.sku_flag != 0) {
      tool.tip('请先选择规格')
      return false
    }
    tool.spec.close()
    var activityId = vm1.activityId
    window.location.href =
      '/shop/shareevent/order/waitsubmit?activityId=' +
      activityId +
      '&num=' +
      data.data.num +
      '&skuId=' +
      data.data.spec_id
    return
  }
  // 规格选择
  $('body').on('click', '#one li', function() {
    $(this)
      .siblings()
      .removeClass('active')
    $(this).addClass('active')
    $('#propid').val('')
    var val = $(this).html()
    var data = new Array()
    data = props[val]
    var secHtml = ''
    if (data) {
      for (i = 0; i < data.length; i++) {
        secHtml =
          secHtml +
          '<li class="tag sku-tag pull-left ellipsis">' +
          data[i]['prop_value2'] +
          '</li>'
      }
    }
    $('#sec').html(secHtml)
  })
  $('body').on('click', '#sec li', function() {
    $(this)
      .siblings()
      .removeClass('active')
    $(this).addClass('active')
    var sec = $(this).html()
    //获取sku ID
    var one = $('#one .active').html()
    var data = new Array()
    var sku = new Array()
    data = props[one]
    for (i = 0; i < data.length; i++) {
      if (data[i]['prop_value2'] == sec) {
        sku = data[i]
        break
      }
    }
    $('.stock').html('剩余' + sku['stock'] + '件')
    $('#propid').val(sku['id'])
    $('#propPrice').html(sku['price'])
    $('#img').attr('src', source + sku['img'])
  })

  // 数量增加
  $('.response-area-plus').click(function() {
    var This = $(this)
    $('.plus').attr('disabled', false)
    //限购数量  forbidden_buy值
    if ($('#buy_num').val() < 3) {
      This.siblings('input').val(
        parseInt(
          $(this)
            .siblings('input')
            .val()
        ) + 1
      )
      $('#num').val(
        $(this)
          .siblings('input')
          .val()
      )
      $('.plus').attr('disabled', true)
      return
    } else {
      tool.tip('每人限购' + 3)
    }
  })
  // 数量减少
  $('.response-area-minus').click(function() {
    if (
      $(this)
        .siblings('input')
        .val() <= 1
    ) {
      return
    }
    $(this)
      .siblings('input')
      .val(
        parseInt(
          $(this)
            .siblings('input')
            .val()
        ) - 1
      )
    $('#num').val(
      $(this)
        .siblings('input')
        .val()
    )
  })
  // tab切换
  $('.js-tabber button').click(function() {
    $('.js-tabber button').removeClass('active')
    $(this).addClass('active')
    $('.js-tabber-content>div').removeClass('hide')
    $('.js-tabber-content>div')
      .eq($(this).index())
      .addClass('hide')
  })
  // 评价区域点击事件
  $('.js-review-tabber .item').click(function() {
    $('.js-review-tabber .item')
      .children('button')
      .removeClass('active')
    $(this)
      .children('button')
      .addClass('active')
    $('.js-review-tabber-content')
      .children('.review-detail-container')
      .addClass('hide')
    $('.js-review-tabber-content')
      .children('.review-detail-container')
      .eq($(this).index())
      .removeClass('hide')
  })

  var page = 2
  $('body').on('click', '.more', function() {
    var obj = $(this)
    var wid = $('#wid').val()
    var str = 'page=' + page + '&pid=' + $('#pid').val()
    if (obj.data(status) != 0) {
      str = str + '&status=' + obj.data('status')
    }
    $.ajax({
      url: '/shop/product/evaluate/' + wid, // 跳转到 action
      data: str,
      type: 'post',
      cache: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json',
      success: function(response) {
        page = page + 1
        if (response.status == 1) {
          if (response.data == '') {
            obj.removeClass('more')
            obj.html('没有更多')
            return
          }
          $.each(eval(response.data), function(data, val) {
            if (val.is_hiden == 0) {
              var _html =
                '<a href="/shop/product/evaluateDetail/' +
                val.wid +
                '/?eid=' +
                val.id +
                '" class="js-review-item review-item block-item"> ' +
                '<div class="name-card"> ' +
                '<div class="thumb">' +
                ' <img src="' +
                val.member.headimgurl +
                '" alt="">' +
                '</div> ' +
                '<div class="detail"> <h3> ' +
                val.member.nickname +
                '</h3> ' +
                '<p class="font-size-12"> ' +
                val.created_at +
                '</p>' +
                '</div>' +
                ' </div> ' +
                '<div class="item-detail font-size-14 c-gray-darker"> ' +
                '<p>' +
                val.content +
                '</p> ' +
                '</div> <div class="other"> ' +
                '<span class="from">购买自：本店</span>' +
                ' <p class="pull-right"> ' +
                '<span class="js-like like-item "> ' +
                '<i class="like"></i>' +
                ' <i class="js-like-num">' +
                val.agree_num +
                '</i>' +
                '</span> ' +
                '<span class="js-add-comment"> ' +
                '<i class="comment"></i> ' +
                '<i class="js-comment-num"></i> ' +
                '</span> </p>' +
                ' </div>' +
                ' </a>'
            } else {
              var _html =
                '<a href="/shop/product/evaluateDetail/' +
                val.wid +
                '/?eid=' +
                val.id +
                '" class="js-review-item review-item block-item"> ' +
                '<div class="name-card"> ' +
                '<div class="thumb">' +
                ' <span class="center font-size-18 c-orange">匿</span>' +
                '</div> ' +
                '<div class="detail"> <h3>匿名</h3>' +
                '<p class="font-size-12"> ' +
                val.created_at +
                '</p>' +
                '</div>' +
                ' </div> ' +
                '<div class="item-detail font-size-14 c-gray-darker"> ' +
                '<p>' +
                val.content +
                '</p> ' +
                '</div> <div class="other"> ' +
                '<span class="from">购买自：本店</span>' +
                ' <p class="pull-right"> ' +
                '<span class="js-like like-item "> ' +
                '<i class="like"></i>' +
                ' <i class="js-like-num">' +
                val.agree_num +
                '</i>' +
                '</span> ' +
                '<span class="js-add-comment"> ' +
                '<i class="comment"></i> ' +
                '<i class="js-comment-num"></i> ' +
                '</span> </p>' +
                ' </div>' +
                ' </a>'
            }
            obj.before(_html)
          })
        } else {
          tool.tip(response.info)
        }
      },
      error: function() {
        // view("异常！");
        tool.tip('异常！')
      }
    })
  })

  var wid = $('#wid').val()
  function referCartNum() {
    $.get('/shop/cart/getNumber/' + wid, function(res) {
      if (res.data != 0) {
        var num = res.data
        $('.goods-num').html(num)
      } else {
        $('.goods-num').html('')
      }

      var value = sessionStorage.getItem('refer') || ''
      if (value != '') {
        sessionStorage.clear()
        window.location.reload()
      }
    })
  }

  //留言验证
  // 验证手机号
  function checkPhone() {
    var phone = $("[data-valid-type='tel']").val()
    if (!/^1[34578]\d{9}$/.test(phone)) {
      tool.tip('手机号码不正确')
      return false
    }
  }

  // 验证身份证
  function isCardNo() {
    var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/
    var card = $("[data-valid-type='id_no']").val()
    if (!pattern.test(card)) {
      tool.tip('身份证号码不正确')
      return false
    }
  }

  //验证邮箱
  function CheckMail() {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/
    var email = $("[data-valid-type='email']").val()
    if (!filter.test(email)) {
      tool.tip('邮箱格式不正确')
      return false
    }
  }
  //隐藏客服
  $('.weui-actionsheet_cancel').click(function() {
    $('.weui-mask').addClass('hide')
    $('.weui-actionsheet').addClass('hide')
  })
})
// 组件模块  @author huoguanghui
var key = []
var j = 0
var k = 0
var n = 0
var vm1 = new Vue({
  el: '#container',
  delimiters: ['[[', ']]'],
  props: ['list'],
  data: {
    lists: [], //商品详情数据
    productTemplate: [], //商品模板数据
    productAd: [], //商品广告数据
    productAdPosition: 1, //商品广告位置  1 头部 2 底部
    commentList: [], //评论数
    wid: $('#wid').val(), //店鋪id
    goodData: null,
    host: host,
    product_price: '', //商品价格
    activityId: '', //活动id
    shareid: '',
    showRight: true,
    oprice: '',
    product_productName: '',
    product_subtitle: '',
    product_oprice: '',
    product_img: '',
    shopId: wid,
    product_image_url: '',
    recommend_product_title: '',
    recommend_product_price: '',
    recommend_product_lowerPrice: '',
    recommend_product_id: '',
    recommend_product: '',
    recommend_product_img: '',
    imgUrl: imgUrl,
    productStatus: 0, //商品状态 0已售罄 1 正常销售
    days: '00',
    hours: '00',
    minutes: '00',
    seconds: '00',
    timeOver: 0, //0未开始  1已开始 2已结束
    mid: mid, //分享者id
    cTime: '',
    etime: '',
    count: '', //立减次数
    total: '', //头像数
    alllowerPrice: '', //总助减金额
    lowerPrice:0,
    headImgUrl: '', //助减者头像
    price: '', //商品价格
    now_price: '', //当前价格
    shareDialogShow: false, //分享弹框开关
    hiddenScroll: false, //页面是否可以滚动 false不可 true可以
    rulshow: false, //规则弹框开关
    ruleTitle: '', //规则标题
    ruleContent: '', //规则内容
    ruleImg: '',
    memberInfo: '', //头像个数
    share_title: '', //分享标题
    productId: '', //商品ID
    share_img: '', //分享图片
    member: '', //点击者
    headURL: '',
    isExpire: 0, //活动是否结束
    theActEnd: false, //活动结束
    theNotStart: false, //活动尚未开始
    popShadow: true, //弹窗背景
    actDetail: null, //活动详情
    isShare: '', //是否是分享者
    popFlag: false, //弹窗
    isComplete: false, //享立减是否完成
    unitAmount: 0, //祝减金额
    sharer: '', //分享者昵称
    shareMember:false,//参与者弹框开关
    shareDetail: {}, //showproductdetail回调的所有参数
    topTip: null, // 参与者数据
    topTipList: null, //参与者单个信息
		productDetailImage:'',//商品详情内容
		isFavorite:false,//是否收藏
		title:'',
		price:'',
  },
  created: function() {
    var that = this
    /**
     * author -> 华亢 date -> 2018/6/28
     * aim_at -> 微页面享立减弹幕效果 -> 获取成员数据
     * params
     */
    var urlTip = '/shop/shareevent/showRecord'
    this.$http.get(urlTip).then(function(res) {
      this.topTip = res.body.data
      var topTipIndex = 0
      var that = this
      hstool.closeLoad()
      setInterval(function() {
        if (topTipIndex >= that.topTip.length) {
          topTipIndex = 0
        }
        that.topTipList = that.topTip[topTipIndex]
        that.topTipList.sec = parseInt(Math.random() * 10) + 1
        setTimeout(function() {
          that.topTipList = null
          topTipIndex++
        }, 4000)
      }, 15000)
    })
    this.$http
      .get('/shop/product/evaluate/' + wid + '?&pid=' + $('#pid').val())
      .then(
        function(res) {
          var data = res.body
          if (data.status == 1) {
            var len = data.data.length > 2 ? 2 : data.data.length
            for (var i = 0; i < len; i++) {
              that.commentList.push(data.data[i])
            }
          }
        },
        function(res) {
          // 处理失败的结果
        }
      )

    //商品详情数据
    if (product.content) {
      var productDetail = JSON.parse(product.content)
      if (productDetail.length > 0) {
        componentAssign(this.lists, productDetail)
        this.$nextTick(function() {
          setTimeout(function() {
            $('.custom-richtext img').removeAttr('width')
            $('.custom-richtext img').removeAttr('height')
            $('.custom-richtext video').attr('width', '100%')
            $('.custom-richtext video').attr('height', 'auto')
          }, 1000)
        })
      }
    }
    //公共广告数据
    if (micro_page_notice.errCode == 0 && micro_page_notice.data.length > 0) {
      var ad = micro_page_notice.data.noticeTemplateData
      if (ad) {
        ad = JSON.parse(ad)
        componentAssign(this.productAd, ad)
        this.productAdPosition = micro_page_notice.data.position
        var swiper = new Swiper('.swiper-container', {
          pagination: '.swiper-pagination',
          paginationClickable: true,
          autoplay: 5000,
          speed: 1000,
          loop: true
        })
        this.$nextTick(function() {
          setTimeout(function() {
            $('.custom-richtext img').removeAttr('width')
            $('.custom-richtext img').removeAttr('height')
          }, 1000)
        })
      }
    }

    //商品模板数据
    if (productModel.product_template_info) {
      var productTemplate = JSON.parse(productModel.product_template_info)
      if (productTemplate.length > 0) {
        componentAssign(this.productTemplate, productTemplate)
        this.$nextTick(function() {
          setTimeout(function() {
            $('.custom-richtext img').removeAttr('width')
            $('.custom-richtext img').removeAttr('height')
          }, 1000)
        })
      }
    }

    /**
     * 组件赋值
     * 参数 赋值对象 赋值模板
     * 用到对象  商品的富文本自定义组件    商品页模板  广告业模板
     */
    function componentAssign(obj, template) {
      var content = template //模板遍历赋值
      for (var i = 0; i < content.length; i++) {
        if (content[i] != undefined) {
          if (content[i]['type'] == 'shop_detail') {
            //图片家域名
            content[i]['content'] = content[i]['content'].replace(
              /<img [^>]*src=['"]([^'"]+)[^>]*>/gi,
              function(match, capture) {
                if (capture.indexOf('http') == -1) {
                  var newSrc =
                    CDN_IMG_URL.substr(0, CDN_IMG_URL.length - 1) + capture
                  match = match.replace(capture, newSrc)
                }
                return match
              }
            )
            // 视频添加域名
            content[i]['content'] = content[i]['content'].replace(
              /<video [^>]*src=['"]([^'"]+)[^>]*>/gi,
              function(match, capture) {
                if (capture.indexOf('http') == -1) {
                  var newSrc =
                    CDN_IMG_URL.substr(0, CDN_IMG_URL.length - 1) + capture
                  match = match.replace(capture, newSrc)
                }
                return match
              }
            )
          }
          if (content[i]['type'] == 'rich_text') {
            //图片家域名
            content[i]['content'] = content[i]['content'].replace(
              /<img [^>]*src=['"]([^'"]+)[^>]*>/gi,
              function(match, capture) {
                if (capture.indexOf('http') == -1) {
                  var newSrc =
                    CDN_IMG_URL.substr(0, CDN_IMG_URL.length - 1) + capture
                  match = match.replace(capture, newSrc)
                }
                return match
              }
            )
            // 视频添加域名
            content[i]['content'] = content[i]['content'].replace(
              /<video [^>]*src=['"]([^'"]+)[^>]*>/gi,
              function(match, capture) {
                if (capture.indexOf('http') == -1) {
                  var newSrc =
                    CDN_IMG_URL.substr(0, CDN_IMG_URL.length - 1) + capture
                  match = match.replace(capture, newSrc)
                }
                return match
              }
            )
          }
          if (content[i]['type'] == 'header') {
            content[i]['order_link'] = '/shop/order/index/' + id
          }
          if (content[i]['type'] == 'goods') {
            if (
              content[i]['cardStyle'] == '3' &&
              content[i]['listStyle'] != 4
            ) {
              content[i]['btnStyle'] = '0'
            }
            // 判断商品名显示
            if (content[i]['goodName']) {
              content[i].title = 'info-title'
            } else {
              content[i].title = 'info-no-title'
            }
            // 判断商品名显示
            // alert(content[i]['priceShow']);
            // 判断价格显示
            if (content[i]['priceShow']) {
              content[i].priceClass = 'info-price'
            } else {
              content[i].priceClass = 'info-no-price'
            }
            if (!content[i]['goodName'] && !content[i]['priceShow']) {
              content[i].hide_all = 'hide'
            }
            // 按钮显示样式
            if (content[i]['btnStyle'] == 1) {
              content[i].btnClass = 'btn1'
            } else if (content[i]['btnStyle'] == 2) {
              content[i].btnClass = 'btn2'
            } else if (content[i]['btnStyle'] == 3) {
              content[i].btnClass = 'btn3'
            } else if (content[i]['btnStyle'] == 4) {
              content[i].btnClass = 'btn4'
            } else {
              content[i].btnClass = 'btn0'
            }

            // 判断是否有商品简介
            if (content[i]['goodInfo']) {
              content[i].has_sub_title = 'has-sub-title'
            }
            if (content[i]['cardStyle'] == 1) {
              content[i].list_style = 'card'
            } else if (content[i]['cardStyle'] == 3) {
              content[i].list_style = 'normal'
            } else if (content[i]['cardStyle'] == 4) {
              content[i].list_style = 'promotion'
            }
            if (content[i].goods == undefined) {
              content[i].goods = []
            }
            if (content[i]['goods'].length > 0) {
              content[i]['thGoods'] = []
              for (var j = 0; j < content[i]['goods'].length; j++) {
                content[i]['goods'][j]['thumbnail'] =
                  imgUrl + content[i]['goods'][j]['thumbnail']
                if (content[i].thGoods.length > 0) {
                  if (
                    content[i]['thGoods'][content[i]['thGoods'].length - 1]
                      .length >= 3
                  ) {
                    content[i]['thGoods'].push([])
                    content[i]['thGoods'][
                      content[i]['thGoods'].length - 1
                    ].push(content[i]['goods'][j])
                  } else {
                    content[i]['thGoods'][
                      content[i]['thGoods'].length - 1
                    ].push(content[i]['goods'][j])
                  }
                } else {
                  content[i]['thGoods'][0] = []
                  content[i]['thGoods'][0].push(content[i]['goods'][j])
                }
              }
            }
          }
          if (content[i]['type'] == 'goodslist') {
            if (
              content[i]['cardStyle'] == '3' &&
              content[i]['listStyle'] != 4
            ) {
              content[i]['btnStyle'] = '0'
            }
            // 判断商品名显示
            if (content[i]['goodName']) {
              content[i].title = 'info-title'
            } else {
              content[i].title = 'info-no-title'
            }
            // 判断商品名显示
            // 判断价格显示
            if (content[i]['priceShow']) {
              content[i].priceClass = 'info-price'
            } else {
              content[i].priceClass = 'info-no-price'
            }
            if (!content[i]['goodName'] && !content[i]['priceShow']) {
              content[i].hide_all = 'hide'
            }
            // 按钮显示样式
            if (content[i]['btnStyle'] == 1) {
              content[i].btnClass = 'btn1'
            } else if (content[i]['btnStyle'] == 2) {
              content[i].btnClass = 'btn2'
            } else if (content[i]['btnStyle'] == 3) {
              content[i].btnClass = 'btn3'
            } else if (content[i]['btnStyle'] == 4) {
              content[i].btnClass = 'btn4'
            } else {
              content[i].btnClass = 'btn0'
            }

            // 判断是否有商品简介
            if (content[i]['goodInfo']) {
              content[i].has_sub_title = 'has-sub-title'
            }
            if (content[i]['cardStyle'] == 1) {
              content[i].list_style = 'card'
            } else if (content[i]['cardStyle'] == 3) {
              content[i].list_style = 'normal'
            } else if (content[i]['cardStyle'] == 4) {
              content[i].list_style = 'promotion'
            }
            if (content[i].goods == undefined) {
              content[i].goods = []
            }
            if (content[i]['goods'].length > 0) {
              content[i]['thGoods'] = []
              for (var j = 0; j < content[i]['goods'].length; j++) {
                content[i]['goods'][j]['thumbnail'] =
                  imgUrl + content[i]['goods'][j]['thumbnail']
                if (content[i].thGoods.length > 0) {
                  if (
                    content[i]['thGoods'][content[i]['thGoods'].length - 1]
                      .length >= 3
                  ) {
                    content[i]['thGoods'].push([])
                    content[i]['thGoods'][
                      content[i]['thGoods'].length - 1
                    ].push(content[i]['goods'][j])
                  } else {
                    content[i]['thGoods'][
                      content[i]['thGoods'].length - 1
                    ].push(content[i]['goods'][j])
                  }
                } else {
                  content[i]['thGoods'][0] = []
                  content[i]['thGoods'][0].push(content[i]['goods'][j])
                }
              }
            }
          }
          // 标题
          if (content[i]['type'] == 'title') {
            if (content[i]['titleStyle'] == 2) {
              content[i]['bgColor'] = '#fff'
            }
          }
          //商品分组
          if (content[i]['type'] == 'good_group') {
            if (content[i]['top_nav'].length > 0) {
              for (var z = 0; z < content[i]['top_nav'].length; z++) {
                content[i]['top_nav'][z]['href'] = 'top_nav_' + randomString(12)
                content[i]['top_nav'][z]['isActive'] = false
                content[i]['top_nav'][z]['width'] = content[i]['width'] + '%'
                if (z == 0) {
                  content[i]['top_nav'][z]['isActive'] = true
                }
                if (
                  content[i]['group_type'] == 2 &&
                  content[i]['top_nav'][z]['goods'].length > 0
                ) {
                  for (
                    var j = 0;
                    j < content[i]['top_nav'][z]['goods'].length;
                    j++
                  ) {
                    content[i]['top_nav'][z]['goods'][j]['thumbnail'] =
                      imgUrl + content[i]['top_nav'][z]['goods'][j]['thumbnail']
                    if (
                      content[i]['top_nav'][z]['goods'][j][
                        'is_price_negotiable'
                      ] == 1
                    ) {
                      content[i]['top_nav'][z]['goods'][j]['price'] =
                        content[i]['top_nav'][z]['goods'][j]['price']
                    } else {
                      content[i]['top_nav'][z]['goods'][j]['price'] =
                        '￥' + content[i]['top_nav'][z]['goods'][j]['price']
                    }
                  }
                }
              }
            }
            if (content[i]['left_nav'].length > 0) {
              for (var z = 0; z < content[i]['left_nav'].length; z++) {
                content[i]['left_nav'][z]['href'] =
                  'top_nav_' + randomString(12)
                content[i]['left_nav'][z]['isActive'] = false
                if (z == 0) {
                  content[i]['left_nav'][z]['isActive'] = true
                }
                if (
                  content[i]['group_type'] == 1 &&
                  content[i]['left_nav'][z]['goods'].length > 0
                ) {
                  for (
                    var j = 0;
                    j < content[i]['left_nav'][z]['goods'].length;
                    j++
                  ) {
                    content[i]['left_nav'][z]['goods'][j]['thumbnail'] =
                      imgUrl +
                      content[i]['left_nav'][z]['goods'][j]['thumbnail']
                    if (
                      content[i]['left_nav'][z]['goods'][j][
                        'is_price_negotiable'
                      ] == 1
                    ) {
                      content[i]['left_nav'][z]['goods'][j]['price'] =
                        content[i]['left_nav'][z]['goods'][j]['price']
                    } else {
                      content[i]['left_nav'][z]['goods'][j]['price'] =
                        '￥' + content[i]['left_nav'][z]['goods'][j]['price']
                    }
                  }
                }
              }
            }
          }
          obj.push(content[i])
          if (content[i]['type'] == 'image_ad') {
            if (content[i].images.length > 0) {
              for (var j = 0; j < content[i].images.length; j++) {
                obj[i].images[j]['FileInfo']['path'] =
                  imgUrl + obj[i].images[j]['FileInfo']['path']
              }
            }
          }
          if (content[i]['type'] == 'image_link') {
            if (content[i]['images'].length > 0) {
              for (var j = 0; j < content[i]['images'].length; j++) {
                content[i]['images'][j]['thumbnail'] =
                  imgUrl + content[i]['images'][j]['thumbnail']
              }
            }
          }
        }
      }
    }
    /**
     * date 2018/6/25
     * author 韩瑜
     * @param {num} activityId 活动id
     * @param {num} shareId 分享者id
     * @param {num} wid 店铺id
     * @param {object} res 商品详情接口数据
     */
    var str = activityId
    //获取activityId
    this.activityId = str
    this.productrequest(str)
    this.moreproductrequest(str)
  },
  methods: {
    //刷新享立减进度
    min_refresh: function() {
      location.reload()
    },
    //底部主页按钮
    index_to: function() {
      var activityId = activityId
      window.location.href = '/shop/index/' + wid
    },
    //分享弹框
    dialogShow: function() {
      var that = this
      that.hiddenScroll = true
      that.shareDialogShow = true
    },
    shareCancle: function() {
      var that = this
      that.hiddenScroll = false
      that.shareDialogShow = false
    },
    //规则弹框
    rul_show: function() {
      var that = this
      that.rulshow = true
    },
    rul_show_no: function() {
      var that = this
      that.rulshow = false
    },
    //参与者弹框
    shareMemberClick:function(){
    	var that = this;
    	that.shareMember = true;
    	console.log(that.shareMember)
    },
    shareMemberCancle:function(){
    	var that = this;
    	that.shareMember = false;
    },
    //获取商品页面信息
    productrequest: function(activityId) {
      var that = this
      $.ajax({
        url: '/shop/shareevent/product/showproductdetail',
        type: 'GET',
        data: {
          activityId: activityId,
          shareId: shareId
        },
        success: function(res) {
        	console.log(res)
          // 享立减活动不存在
          if (res.errCode == '-104') {
            tool.tip(res.errMsg)
            setTimeout(function() {
              location.href = '/shop/index/' + wid
            }, 500)
          }
          //add by 韩瑜 2018-9-6 是否收藏
          that.isFavorite = res.data.isFavorite
          that.product_productName = res.data.product.productName
          that.product_img = res.data.product.activityImg
          that.productId = res.data.product.productId
          //end
          that.shareDetail = res.data
          that.productDetailImage = JSON.parse(res.data.product.content)[0].content;
          that.product_subtitle = res.data.product.subtitle
          that.count = parseFloat(res.data.member.total) //助减人数
          that.unitAmount = res.data.unitAmount //助减单价
          that.total = res.data.member.total
          that.alllowerPrice = res.data.reduce_total //助力总价
          that.lowerPrice = res.data.lowerPrice //保底金额
          that.headImgUrl = res.data.headImgUrl
          that.isShare = res.data.isShare
          that.sharer = res.data.sharer
          that.price = res.data.product.price
          that.unitAmount = res.data.unitAmount
          /**
           * author 华亢 update 2018/06/17
           * 若价格低于保底价，则单价为保底价
           */

          that.now_price =
            that.price - that.alllowerPrice <= res.data.lowerPrice
              ? res.data.lowerPrice
              : (that.price - that.alllowerPrice).toFixed(2)
          that.ruleTitle = res.data.ruleTitle
          that.ruleContent = res.data.ruleContent
          that.ruleImg = res.data.ruleImg ? imgUrl + res.data.ruleImg : _host + 'shop/images/aictive_rure.jpg'
          that.memberInfo = res.data.member.memberInfo
          that.share_title = res.data.share_title
          that.share_img = res.data.share_img
          that.isExpire = res.data.isExpire
          that.member = res.data.member.memberInfo
          that.errCode = res.data.errCode
          that.errMsg = res.data.errMsg
          // headURL
          that.headURL = res.data.headImgUrl
            ? res.data.headImgUrl
            : host + 'static/images/customer_service.jpg'
          
          //享立减助减过，且没到保底价
          if (res.errCode == 0) {
            that.comeFromLink(res.data)
          }
          //倒计时弹窗
          that.isStart((+res.data.startTime) - (+res.data.currentTime));
          //秒杀倒计时
          that.date = res.data.currentTime * 1000
          var newTime = that.date //现在时间  时间戳
          that.endTime = res.data.endTime * 1000
          that.startTime = res.data.startTime * 1000
          that.days = '00'
          that.hours = '00'
          that.minutes = '00'
          that.seconds = '00'
          
          //单转双
          function evenNum(num) {
            num = num < 10 ? '0' + num : num
            return num
          }
          //倒计时函数
          function getrtime(that) {
            var time;
            if (that.startTime > newTime){
              that.timeOver = 0;
              time = that.startTime;
            } else if(that.startTime <= newTime && that.endTime>=newTime){
              that.timeOver = 1;
              time = that.endTime;
            } else {
              that.timeOver = 2;
              return;
            }
            var EndTime = new Date(time)

            var t = EndTime.getTime() - newTime
            if (t >= 0) {
              var d = evenNum(Math.floor(t / 1000 / 60 / 60 / 24))
              var h = evenNum(Math.floor((t / 1000 / 60 / 60) % 24))
              var m = evenNum(Math.floor((t / 1000 / 60) % 60))
              var s = evenNum(Math.floor((t / 1000) % 60))
              that.days = d
              that.hours = h
              that.minutes = m
              that.seconds = s
              setTimeout(function() {
                getrtime(that)
                newTime += 1000
              }, 1000)
            }
          }
          getrtime(that)
        }
      })
    },
    moreproductrequest: function(activityId) {
      var that = this
      $.ajax({
        type: 'GET',
        url: '/shop/share/more',
        data: {
          activityId: activityId
        },
        success: function(res) {
          var recommend_product = []
          for (var i in res.data) {
            recommend_product.push(res.data[i])
          }
          that.recommend_product = recommend_product
        }
      })
    },
    back_index: function() {
      if (this.theActEnd || this.theNotStart) {
        window.location.href = '/shop/index/' + wid
      }
    },
    /**
     * date 2018/6/25
     * author 华亢
     * @param {*} endTime 结束时间 string 时间戳
     */
    isEnd: function(endTime) {
      //活动结束
      // var currentTime = Date.parse(new Date()) / 1000
      if (endTime == 1) {
        this.popShadow = true
        this.theActEnd = true
        this.popFlag = true
      }
    },
    /**
     * date 2018/9/20
     * author 黄新琴
     * @param {*} flag 活动是否开始 0 尚未开始
     */
    isStart: function(flag) {
      if (flag > 0 ) {
        this.popShadow = true
        this.theNotStart = true
        this.popFlag = true
      } else {
        this.isEnd(this.isExpire)
      }
    },
    comeFromLink: function(param) {
      if (param.isExpire == 1) {
        return false
      }
      var judge = param.product.price - param.unitAmount * param.member.total
      this.popShadow = true
      this.popFlag = true
      if (param.isShare == 0 && param.lowerPrice < judge) {
        // 被分享者且活动并没有结束，且没到保底价
        this.isComplete = false
      } else if (param.lowerPrice >= judge) {
        this.isComplete = true
      } else if (param.isShare == 1 && param.lowerPrice < judge) {
        // 分享者，没到保底价
        this.popShadow = false
        this.popFlag = false
      }
    },
    shadowClose: function() {
      //弹窗背景关闭
      this.popShadow = false
    },
    completeClose: function() {
      // 享立减完成弹窗关闭
      this.popFlag = false
    },
    allClose: function() {
      // 全部关闭
      this.shadowClose()
      this.completeClose()
    },
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
				type: 3,
	    	relativeId: activityId,
	    	_token: $("meta[name='csrf-token']").attr("content"),
	    	title: this.product_productName,
	    	price: this.lowerPrice,
	    	image: this.product_img,
	    	share_product_id:this.productId,
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
				type: 3,
		    	relativeId: activityId,
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

//生成随机字符串
function randomString(len) {
  len = len || 32
  var $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  var maxPos = $chars.length
  var pwd = ''
  for (i = 0; i < len; i++) {
    //0~32的整数
    pwd += $chars.charAt(Math.floor(Math.random() * (maxPos + 1)))
  }
  return pwd
}
