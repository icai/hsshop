
var tool = {}
/**
 ** 规格弹窗组件 @author txw @date 2017-07-04
 ** 需要引入 shop/static/css/tspec_common.css
 ** 说明：
 ** obj.type 1.单买弹窗 2.开团弹窗 3.加入购物车
 ** obj.callback 回调函数
 ** 使用 tool.spec.open({"type":1,"callback":buyCallBack});
 ** unActive 1  为非活动
 ** 关闭弹窗使用 tool.spec.close();
 **/

;(function(win, doc) {
  function t_spec() {
    this.id = '' //弹窗编号
    this.skuData = {}
    this.specDetail = null
    this.selectData = ['', '', ''] //选中的数据
    this.spec_id = '' //规格编号
    this.spec_img = '' //规格图片
    this.spec_price = 0 //规格价格
    this.spec_stock_num = 0 //规格库存
    this.spec_limit_num = 0 //规格限购数 0.无限制
    this.spec_buy_min = 1 //最小购买量
    this.spec_num = 1 //产品数量
    this.idEdit = true //
    this.spec_stock_sum = 0 //总库存
    this.dis_price = 0 //分销佣金
    this.fisrt_dis_price = 0 // 一级分销佣金
    this.sec_dis_price = 0 // 二级分销佣金
    /*
    * 打开规格弹窗  
    */
    this.open = function(obj) {
      // if(!obj.isEdit){   result：ios返回按钮 保存规格数据 resolve：这里不保存数据每次进入初始化数据  @author huoguanghui
      this.id = '' //弹窗编号
      this.specDetail = null
      this.selectData = ['', '', ''] //选中的数据
      this.spec_id = '' //规格编号
      this.spec_img = '' //规格图片
      this.spec_price = 0 //规格价格
      this.spec_stock_num = 0 //规格库存
      this.idEdit = true //
      this.spec_stock_sum = 0
      // }
      this.isShareLi = obj.isShareLi //是否是享立减商品
      this.reduce_total = obj.reduce_total
      this.shareCount = obj.shareCount?obj.shareCount:0 //享立减帮减价参与人数
      this.lowerPrice = obj.lowerPrice //保底金额
      this.shareUnitedAmount = obj.shareUnitedAmount?obj.shareUnitedAmount:0 // 享立减每次减价的人数
      this.pid = obj.pid || 0
      this.spec_limit_num = obj.limit_num || 0 //规格限购数 0.无限制
      this.spec_buy_min = obj.buy_min || 1 //最小购买量 默认为1
      this.spec_num = obj.buy_min || 1 // 产品数量设为最小购买数量
      this.spec_limit_type = obj.limit_type !== undefined ? obj.limit_type : 1 //-1无限购 0 每单 1每人（拼团功能使用） 其他默认 1
      this.spec_limit_text = this.spec_limit_type == 1 ? '每人' : '每单' //限购文案
      this.spec_surplus_num =
        obj.surplus_num !== undefined ? obj.surplus_num : this.spec_limit_num // 每人限购 剩余能买的数量 每单为-1
      this.initSpec = obj.initSpec
      this.spec_stock_num = obj.initSpec.stock //仓库默认值
      this.spec_stock_sum = obj.initSpec.stock //总库存
      this.btnTitle = obj.buyCar ? '加入购物车' : '下一步'
      this.activeTitle = obj.unActive ? '' : '单人购买价'
      this.noteList = obj.noteList || []
      this.id = new Date().getTime()
      this.setSpecInfo(obj)
      this.maskShow() //显示遮罩
      this.wholesale_flag = obj.initSpec.wholesale_flag
      this.wholesale_price = 0
      this.isDistribute = obj.isDistribute ? obj.isDistribute : 0
      this.rate = obj.rate ? obj.rate : 0
      this.rateSec = obj.rateSec ? obj.rateSec : 0
      this.dis_price = 0 //分销佣金
      this.noSkuDisPrice = obj.noSkuDisPrice ? obj.noSkuDisPrice : 0
      $('body').css('overflow', 'hidden')
      if (obj.initSpec.wholesale_flag == 1) {
        //如果有批发价
        this.wholesale_array = obj.initSpec.wholesale_array
      }
      //定义公用部分字符串 （规格、图片、价格、其他文字等共用部分）
      var html = '<div class="sku-layout-title name-card sku-name-card">'
      if (this.spec_img != '')
        html +=
          '<div class="thumb"><img class="js-goods-thumb" src="' +
          imgUrl +
          this.spec_img +
          '"></div>'
      else
        html +=
          '<div class="thumb"><img class="js-goods-thumb" src="' +
          imgUrl +
          obj.initSpec.img +
          '"></div>'
      html +=
        '<div class="detail goods-base-info clearfix"><p class="title c-black ellipsis">' +
        obj.initSpec.title +
        '</p>'
      html +=
        '<div class="goods-price clearfix">'
      /**
       * add by 韩瑜
       * date 2018-10-31
       * 分销商品规格弹窗显示佣金
       */
      // if(this.isDistribute == 1){
      //   if(this.specDetail.props.length>0){//是否有规格
      //     html += '<span class="distribute"></span>'
      //   }else{
      //     html += '<span class="distribute-price">分享可赚' + this.noSkuDisPrice + '</span>'
      //   }
      // }
      // end
      html +=
        '<div class="current-price pull-left">'
      html +=
        '<span class="origin-price font-size-12 c-red vertical-middle">' +
        this.activeTitle +
        '</span>'
      html += '<span class="font-size-14 c-red vertical-middle">¥</span>'
      if (this.spec_price != 0)
        html +=
          '<i class="js-goods-price price font-size-16 vertical-middle c-red">' +
          this.spec_price +
          '</i>'
      else
        html +=
          '<i class="js-goods-price price font-size-16 vertical-middle c-red">' +
          obj.initSpec.price +
          '</i></div></div>'
      if(this.isDistribute == 1){
          if(this.specDetail.props.length>0){//是否有规格
            html += '<div class="sku-distr-price"></div>'
          }else{
            html += '<div class="sku-distr-price">分享：一级赚'+(obj.initSpec.price*obj.rate/100).toFixed(2)+'元 / 二级赚'+(obj.initSpec.price*obj.rateSec/100).toFixed(2)+'元</div>'
          }
      }
      html += '</div>'

      html +=
        '<div class="js-cancel sku-cancel"><a class="cancel-img" href="javascript:void(0);" style="display:block;"></a></div></div><div class="adv-opts layout-content"style="max-height: 300px;">'
      if (obj.initSpec.wholesale_flag == 1) {
          html +=
              '<div class="wholesale_board"><div style="margin: 0 30px 10px 15px">批发价</div><ul class="wholesale_content">'
          for (var wi = 0; wi < this.wholesale_array.length; wi++) {
              html +=
                  '<li>' +
                  '起批量:' +
                  this.wholesale_array[wi].min +
                  '~' +
                  this.wholesale_array[wi].max +
                  '；单价：¥' +
                  this.wholesale_array[wi].price +
                  '</li>'
          }
          html += '</ul></div>'
      }
      html +=
        '<div class="goods-models js-sku-views block block-list border-top-0" id="chatpannel">'
      //便利规格
      var props = this.specDetail.props
      for (var i = 0; i < props.length; i++) {
        html +=
          '<dl class="clearfix block-item sku-list-container"><dt class="model-title sku-sel-title"><label>' +
          props[i].props.title +
          '：</label></dt>'
        html += '<dd><ul class="model-list sku-sel-list">'
        var values = props[i].values
        for (var j = 0; j < values.length; j++) {
          html +=
            '<li class="tag sku-tag pull-left ellipsis" data-num="' +
            i +
            '">' +
            values[j].title +
            '</li>'
        }
        html += '</ul></dd></dl>'
      }
      console.log(this.noteList)
      // 留言字段
      var len = this.noteList.length
      if (len) {
        html += '<dl class="clearfix block-item">'
        for (var i = 0; i < len; i++) {
          console.log(this.noteList[i])
          html +=
            '<dt class="sku-num sku-note" data-name="name_' +
            this.noteList[i]['id'] +
            '" data-required="' +
            this.noteList[i]['required'] +
            '"><label class="text-note-label">' +
            this.noteList[i]['title'] +
            '：</label>'
          switch (this.noteList[i]['type']) {
            case '0':
              html +=
                '<input type="text" class="txt-note-input" data-type="text" placeholder="请输入' +
                this.noteList[i]['title'] +
                '" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '1':
              html +=
                '<input type="number" class="txt-note-input" data-type="number" placeholder="请输入' +
                this.noteList[i]['title'] +
                '" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '2':
              html +=
                '<input type="text" class="txt-note-input" data-type="email" placeholder="请输入' +
                this.noteList[i]['title'] +
                '" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '3':
              html +=
                '<input type="number" class="txt-note-input" data-type="date" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '4':
              html +=
                '<input type="number" class="txt-note-input" data-type="time" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '5':
              html +=
                '<input type="text" class="txt-note-input" data-type="card" placeholder="请输入' +
                this.noteList[i]['title'] +
                '" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            case '6':
              // html += '<div>';
              html +=
                '<div class="upload-img"><div><img src="https://upx.cdn.huisou.cn/wscphp/xcx/images/upload.png"><input type="file" class="txt-note-input txt-note-file">'
              html +=
                '<input type="hidden" value="" data-type="file" name="name_' +
                this.noteList[i]['id'] +
                '">'
              // html += '';
              html += '</div></div>'
              break
            case '7':
              html +=
                '<input type="number" class="txt-note-input" data-type="mobile" placeholder="请输入' +
                this.noteList[i]['title'] +
                '" name="name_' +
                this.noteList[i]['id'] +
                '">'
              break
            default:
              console.log(this.noteList[i]['type'])
          }
          if (this.noteList[i]['type'] == 0) {
          }
          html += '</dt>'
        }
        html += '</dl>'
      }
      // 留言字段
      //购买数量
      html +=
        '<dl class="clearfix block-item" style="padding-bottom: 55px;"><dt class="sku-num pull-left"><label>购买数量：</label></dt><dd class="sku-quantity-contaienr" style="padding-bottom:10px;"><dl class="clearfix"><div class="quantity"><button class="minus" type="button"></button>'
      // html+='<input type="number" class="txt" value="'+this.spec_buy_min+'" pattern="[0-9]*">'
      if (this.spec_num != 1)
        html +=
          '<input type="number" class="txt" value="' +
          this.spec_buy_min +
          '" pattern="[0-9]*">'
      else
        html += '<input type="number" class="txt" value="1" pattern="[0-9]*">'
      html +=
        '<button class="plus"type="button"></button></div></dl></dd><dt class="other-info">'
      if (this.spec_stock_num != 0)
        html +=
          '<div class="stock" style="margin-right:0;">剩余<b>' +
          this.spec_stock_num +
          '</b>件</div>'
      else
        html +=
          '<div class="stock" style="margin-right:0;">剩余' +
          obj.initSpec.stock +
          '件 </div>'
      if (this.spec_limit_num != 0) {
        html +=
          '<span style="color:#F72F37;margin-left:10px;font-size:12px;">（' +
          this.spec_limit_text +
          '限购' +
          this.spec_limit_num +
          '件</span>'
        html +=
          '<span style="color:#F72F37;margin-left:10px;font-size:12px;">' +
          '最少购买' +
          this.spec_buy_min +
          '件）</span>'
      } else {
        html +=
          '<span style="color:#F72F37;margin-left:10px;font-size:12px;">' +
          '(最少购买' +
          this.spec_buy_min +
          '件）</span>'
      }
      html +=
        '</dt></dl><div class="block-item block-item-messages"style="display: none;"></div></div>'
      obj.html = html
      switch (obj.type) {
        case 1:
          this.singleBuy(obj)
          if (this.wholesale_flag == 1) {
            // this.patternHignLight(1,this.wholesale_array);
          }
          break
        case 2:
          this.groupBuy(obj)
          if (this.wholesale_flag == 1) {
            this.patternHignLight(1, this.wholesale_array)
          }
          break
        case 3:
          this.addCart(obj)
          if (this.wholesale_flag == 1) {
            this.patternHignLight(1, this.wholesale_array)
          }
          break
        default:
          this.singleBuy(obj)
          if (this.wholesale_flag == 1) {
            this.patternHignLight(1, this.wholesale_array)
          }
          break
      }
      $('.sku-layout').css('bottom', -$(window).scrollTop())
      this.setLoadElementHide()
      this.setNum()
      this.bindUpload() //绑定留言图片事件
      var sd = this.selectData
      for (var i = 0; i < sd.length; i++) {
        if (sd[i] != '') this.setElementHide(i, sd[i])
      }
      $('body').on('touchmove', '#t_mask' + this.id, function(e) {
        e.preventDefault()
      })

      //          $("body").on("touchmove",".sku-layout",function(e){
      //              e.preventDefault()
      //          });
      this.addEvent.push({ event: 'touchmove', name: '#t_mask' + this.id })
      this.addEvent.push({ event: 'touchmove', name: '.sku-layout' })
    }

    /*
        *  
        * 文件图片选择事件绑定
        * 
        */
    this.bindUpload = function() {
      var that = this
      this.addEvent.push({ event: 'change', name: '.txt-note-file' })
      $('body').on('change', '.txt-note-file', function(e) {
        // console.log($(this).files)
        // console.log(event.target.files[0])
        // alert($(this).index());
        var index = $(this).index()
        var files = e.target.files || e.dataTransfer.files
        if (!files.length) return
        var file = files[0]
        var fD = new FormData()
        fD.append('file', file)
        fD.append('token', $('meta[name="csrf-token"]').attr('content'))
        that.upload(fD, index)
      })
    }
    /*
        *  
        * 图片上传
        * fD表单数据，index判断上传位置
        */
    this.upload = function(fD, index) {
      var http = new XMLHttpRequest()
      http.onreadystatechange = function() {
        if (http.readyState == 4) {
          if ((http.status >= 200 && http.status < 300) || http.status == 304) {
            d = JSON.parse(http.response)
            // hstool.closeLoad();
            var imgSrc = APP_IMG_URL + '' + d.data.path
            $('.txt-note-file')
              .eq(index - 1)
              .prev('img')
              .attr('src', imgSrc)
            $('.txt-note-file')
              .eq(index - 1)
              .next('input')
              .val(d.data.path)
            // console.log(imgSrc);
          }
        }
      }
      http.open('post', '/shop/order/upfile/' + this.wid)
      http.send(fD)
    }
    /*
        *  
        * 留言提交
        * suc提交成功回调
        */
    this.submitNoteList = function(suc) {
      $.post('/shop/cart/saveRemark', this.noteListData, function(data) {
        if (data.code == 40000) {
          suc(data.list)
        }
      })
    }
    /*
        *  
        * 留言表单验证，和数据收集
        * suc验证成功回调
        */
    this.validateNote = function(suc) {
      var len = $('.sku-note').length
      if (len) {
        this.noteListData = {} //收集表单数据;
        for (var i = 0; i < len; i++) {
          var required = $('.sku-note')
            .eq(i)
            .data('required')
          var name = $('.sku-note')
            .eq(i)
            .data('name')
          var type = $('input[name=' + name + ']').data('type')
          var value = $('input[name=' + name + ']').val()
          if (required == 1 && value == '') {
            // alert(name)
            tool.tip(this.noteList[i]['title'] + '不能为空!')
            return false
            break
          }
          if (value != '' && type == 'mobile') {
            if (!/^[1][3,4,5,7,8][0-9]{9}$/.test(value)) {
              tool.tip(this.noteList[i]['title'] + '请填写正确的手机号!')
              return false
              break
            }
          }
          if (value != '' && type == 'email') {
            if (
              !/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/.test(
                value
              )
            ) {
              tool.tip(this.noteList[i]['title'] + '请填写正确的邮箱!')
              return false
              break
            }
          }
          this.noteListData[name] = value
          // if(value != '' && type == 'card'){
          //     if(!/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/.test(value)){
          //         tool.tip( name + '请填写正确的邮箱!');
          //         return false;
          //     }
          // }
        }
        this.noteListData._token = $('meta[name="csrf-token"]').attr('content')
        this.noteListData.pid = this.pid
        suc()
      } else {
        suc()
      }
    }
    /*
        * 单买弹窗 
        * 参数说明：
        * 1.obj.callback 回调函数
        */
    this.singleBuy = function(obj) {
      var _this = this
      var div = doc.createElement('div')
      div.id = this.id
      div.className = 'sku-layout sku-box-shadow popup'
      var html =
        '<div class="confirm-action content-foot clearfix"><div class="big-btn-2-1"><a href="javascript:;"class="js-mutiBtn-confirm cart big-btn orange-btn vice-btn">加入购物车</a><a href="javascript:;"class="js-mutiBtn-confirm confirm big-btn red-btn main-btn">立即购买</a></div></div></div>'

      div.innerHTML = obj.html + html

      $('body').append(div)
      $('body').on('click', '.js-mutiBtn-confirm', function() {
        //验证是否选择规格了
        if (_this.specDetail.stocks.length > 0 && _this.spec_id == '') {
          tool.tip('请选择商品规格!')
          return false
        }
        if (_this.validateNum()) {
          var index = $(this).index()
          var resultObj = {
            spec_id: _this.spec_id,
            sdata: _this.selectData,
            num: $('.txt').val()
          }
          var data = {
            status: 1,
            msg: '测试回调',
            pop_id: _this.id,
            data: resultObj,
            index: index
          }
          if (index == 0) {
            data.type = 1 //加入购物车
            obj.callback(data) //执行回调
          } else if (index == 1) {
            data.type = 2 //购买
            $(this)
              .html('数据提交中')
              .removeClass('js-mutiBtn-confirm')
            obj.callback(data) //执行回调
          }
        }
      })
      this.addEvent.push({ event: 'click', name: '.js-mutiBtn-confirm' })
      this.addPublicEvent()
    }

    /*
        * 开团弹窗 
        * 参数说明：
        * 1.obj.callback 回调函数
        */
    this.groupBuy = function(obj) {
      var _this = this
      var div = doc.createElement('div')
      div.id = this.id
      div.className = 'sku-layout sku-box-shadow popup'
      var html = '</div>';
      html += '<div class="confirm-action content-foot clearfix"><div class="big-btn-1-1"><a href="javascript:;" class="js-confirm-it big-btn red-btn main-btn">' +
        this.btnTitle + '</a></div>';
      div.innerHTML = obj.html + html
      $('body').append(div)
      $('body').on('click', '.js-confirm-it', function() {
        var that = $(this)
        if ($('.quantity .txt').val() <= 0) {
          tool.tip('请选择正确商品数量!')
          return false
        }
        // var index = that.index();
        //验证是否选择规格了
        if (_this.specDetail.stocks.length > 0 && _this.spec_id == '') {
          tool.tip('请选择商品规格!')
          return false
        }
        _this.validateNote(function() {
          if (_this.validateNum()) {
            //提交留言数据
            if (_this.noteList.length) {
              _this.submitNoteList(function(noteId) {
                var resultObj = {
                  spec_id: _this.spec_id,
                  sdata: _this.selectData,
                  num: $('.txt').val()
                }
                var data = {
                  status: 1,
                  msg: '测试回调',
                  pop_id: _this.id,
                  data: resultObj,
                  remark_no: noteId
                }
                that.html('数据提交中').removeClass('js-confirm-it')
                localStorage.setItem('remark_no', noteId)
                obj.callback(data) //执行回调
              })
            } else {
              var resultObj = {
                spec_id: _this.spec_id,
                sdata: _this.selectData,
                num: $('.txt').val()
              }
              var data = {
                status: 1,
                msg: '测试回调',
                pop_id: _this.id,
                data: resultObj,
                remark_no: ''
              }
              that.html('数据提交中').removeClass('js-confirm-it')
              localStorage.setItem('remark_no', '')
              obj.callback(data) //执行回调
            }
          }
        })
      })
      this.addEvent.push({ event: 'click', name: '.js-confirm-it' })
      this.addPublicEvent()
    }
    /*
        * 加入购物车
        */
    this.addCart = function(obj) {
      var _this = this
      var div = doc.createElement('div')
      div.id = this.id
      div.className = 'sku-layout sku-box-shadow popup'
      var html =
        '<div class="confirm-action content-foot clearfix"><div class="big-btn-1-1"><a href="javascript:;" class="js-confirm-it big-btn red-btn main-btn">加入购物车</a></div></div>'
      div.innerHTML = obj.html + html
      $('body').append(div)
      $('body').on('click', '.js-confirm-it', function() {
        if (_this.validateNum()) {
          var index = $(this).index()
          var resultObj = { spec_id: _this.spec_id, sdata: _this.selectData }
          var data = {
            status: 1,
            msg: '测试回调',
            pop_id: _this.id,
            data: resultObj
          }
          obj.callback(data) //执行回调
        }
      })
      this.addEvent.push({ event: 'click', name: '.js-confirm-it' })
      this.addPublicEvent()
    }
    /*
        * 添加公用事件
        */
    this.addPublicEvent = function() {
      var _this = this
      // 点击关闭按钮
      $('body').on('click', '.cancel-img', function() {
        _this.close()
      })
      this.addEvent.push({ event: 'click', name: '.cancel-img' })

      $('body').on('click', '.sku-tag', function() {
        //获取当前是第几层规格
        var pindex = $(this).attr('data-num')
        if (!$(this).hasClass('active')) {
          //当前元素未选中
          if (!$(this).hasClass('hide')) {
            //hide 状态不能点击
            var key = $(this).html()
            _this.setElementHide(pindex, key)
          }
        } else {
          //当前元素已选中
          $(this).removeClass('active')
          _this.spec_id = ''
          $('.sku-tag').each(function(index, el) {
            var data_num = $(this).attr('data-num')
            if (data_num > pindex) {
              $(this).removeClass('active')
            }
          })
          //如果全部没有选中则显示总库存
          if ($('.sku-tag.active').length == 0) {
            $('.sku-layout .stock').html('剩余' + _this.spec_stock_sum + '件')
          }
        }
        // 只能在第一个规格设置图片的方法
        if (pindex == 0) {
          var key1 = $(this).html()
          var stocks = _this.specDetail.stocks
          for (var i = 0; i < stocks.length; i++) {
            if (key1 == stocks[i].v1) {
              _this.spec_img = stocks[i].img
                ? stocks[i].img
                : _this.initSpec.img
              $('.sku-layout .thumb .js-goods-thumb').attr(
                'src',
                imgUrl + _this.spec_img
              )
            }
          }
        }
        //调用设置规格接口
        _this.getSpecInfo()
      })
      this.addEvent.push({ event: 'click', name: '.sku-tag' })
      // 数量-按钮点击事件
      $('body').on('click', '.quantity .minus', function() {
        var val = parseInt(_this.spec_num)
        val = val - 1
        if (val < _this.spec_buy_min) {
          tool.tip('至少购买' + _this.spec_buy_min + '件')
          return false
        }
        $('.quantity .txt').val(val)
        if (_this.wholesale_flag == 1) {
          _this.patternHignLight(val, _this.wholesale_array)
        }
        _this.spec_num = $('.quantity .txt').val()
      })
      this.addEvent.push({ event: 'click', name: '.quantity .minus' })
      // 数量+按钮点击事件
      $('body').on('click', '.quantity .plus', function() {
        var val = parseInt(_this.spec_num)
        if (_this.spec_limit_type == 1 && _this.spec_limit_num > 0) {
          //每人限购
          if (val + 1 > _this.spec_surplus_num) {
            tool.tip('该商品不能超过限购数量')
            return false
          }
        }
        if (val + 1 > _this.spec_stock_num) {
          tool.tip('商品数量不应超过库存量')
          return false
        }
        if (_this.spec_limit_num != 0) {
          if (val + 1 > _this.spec_limit_num) {
            tool.tip('每人限购' + _this.spec_limit_num + '件')
            return false
          }
        }

        $('.quantity .txt').val(val + 1)

        if (_this.wholesale_flag == 1) {
          _this.patternHignLight(val + 1, _this.wholesale_array)
        }

        _this.spec_num = $('.quantity .txt').val()
      })
      $('body').on('keypress', '.quantity .txt', function(e) {
        var e = e || event
        var val = $(this).val()
        return /[\d]/.test(String.fromCharCode(event.keyCode))
      })
      $('body').on('change', '.quantity .txt', function() {})
      this.addEvent.push({ event: 'click', name: '.quantity .plus' })
      //点击遮罩层
      $('body').on('click', '#t_mask' + this.id, function() {
        _this.close()
      })
      this.addEvent.push({ event: 'click', name: '.#t_mask' + this.id })
    }
    this.patternHignLight = function(val, arr) {
      var _this = this
      var highLight = null
      if (val >= arr[arr.length - 1].max) {
        highLight = arr.length - 1
      } else {
        for (var i = 0; i < arr.length; i++) {
          if (val >= arr[i].min && val <= arr[i].max) {
            highLight = i
            break
          }
        }
      }
      if (typeof highLight != 'object') {
        _this.wholesale_price = arr[highLight].price
        console.log(highLight)
        $('.wholesale_content li')
          .removeClass('selected')
          .eq(highLight)
          .addClass('selected')
      } else {
        $('.wholesale_content li').removeClass('selected')
      }
    }
    /**
     * 验证购买数
     * @return bool true 验证通过 false 验证没通过
     */
    this.validateNum = function() {
      var _this = this
      var val = $('.quantity .txt').val()
      if (val == '') {
        $('.quantity .txt').val('1')
        tool.tip('商品数量不能等于空')
        return false
      } else {
        if (parseInt(val) > _this.spec_stock_num) {
          if (_this.spec_limit_num == 0)
            $('.quantity .txt').val(_this.spec_stock_num)
          else $('.quantity .txt').val(_this.spec_limit_num)
          tool.tip('商品数量不应超过库存量')
          return false
        }
        if (_this.spec_limit_num != 0 && parseInt(val) > _this.spec_limit_num) {
          $('.quantity .txt').val(_this.spec_limit_num)
          tool.tip('每人限购' + _this.spec_limit_num + '件')
          return false
        }
        if (parseInt(val) < _this.spec_buy_min) {
          tool.tip('至少购买' + _this.spec_buy_min + '件')
          return false
        }
        _this.spec_num = $('.quantity .txt').val()
        return true
      }
    }
    /*
        * 设置加载时要隐藏的规格
        */
    this.setLoadElementHide = function() {
      var sku = this.skuData.sku1
      $('.sku-sel-list')
        .eq(0)
        .find('li')
        .each(function() {
          var key = $(this).html()
          var bl = false
          for (var i = 0; i < sku.length; i++) {
            if (sku[i].v1 == key) {
              bl = true
              break
            }
          }
          if (!bl) $(this).addClass('hide')
        })
    }
    /*
        * 设置只能输入正整数
        */
    this.setNum = function() {
      $('body').on('blur', '.txt', function() {
        if (!/^\d+$/.test(this.value)) {
          this.value = 1
        }
      })
      this.addEvent.push({ event: 'blur', name: '.txt' })
    }
    /*
        * 将数据处理成层级分明的数据格式
        */
    this.dataProcessing = function() {
      var stocks = this.specDetail.stocks
      var props = this.specDetail.props
      var sku_num = this.getSkuNum()
      var sku1 = []
      switch (sku_num) {
        case 1:
          for (var i = 0; i < stocks.length; i++) {
            if (stocks[i].stock_num > 0) {
              var obj = {
                id: stocks[i].id,
                k1: stocks[i].k1,
                v1: stocks[i].v1,
                img: stocks[i].img,
                stock_num: stocks[i].stock_num
              }
              sku1.push(obj)
            }
          }
          break
        case 2:
          var values1 = props[0].values //规格1的标题数量
          var values2 = props[1].values //规格2的标题数量
          for (var i = 0; i < values1.length; i++) {
            var obj = {}
            for (var j = 0; j < values2.length; j++) {
              for (var k = 0; k < stocks.length; k++) {
                var stock_num = parseInt(stocks[k].stock_num)
                if (
                  stock_num > 0 &&
                  values1[i].title == stocks[k].v1 &&
                  values2[j].title == stocks[k].v2
                ) {
                  if (!obj.k1) {
                    obj.k1 = stocks[k].k1
                    obj.v1 = stocks[k].v1
                    obj.img = stocks[k].img
                    obj.sku2 = []
                  }
                  var sku2 = {
                    id: stocks[k].id,
                    k2: stocks[k].k2,
                    v2: stocks[k].v2,
                    stock_num: stocks[k].stock_num
                  }
                  obj.sku2.push(sku2)
                }
              }
            }
            if (typeof obj.k1 != 'undefined') {
              sku1.push(obj)
            }
          }
          break
        case 3:
          var values1 = props[0].values //规格1的标题数量
          var values2 = props[1].values //规格2的标题数量
          var values3 = props[2].values //规格2的标题数量
          for (var i = 0; i < values1.length; i++) {
            var obj1 = {}
            for (var j = 0; j < values2.length; j++) {
              var obj2 = {}
              for (var n = 0; n < values3.length; n++) {
                for (var k = 0; k < stocks.length; k++) {
                  //第三层
                  var stock_num = parseInt(stocks[k].stock_num)
                  if (
                    stock_num > 0 &&
                    values1[i].title == stocks[k].v1 &&
                    values2[j].title == stocks[k].v2 &&
                    values3[n].title == stocks[k].v3
                  ) {
                    if (!obj1.k1) {
                      obj1.k1 = stocks[k].k1
                      obj1.v1 = stocks[k].v1
                      obj1.img = stocks[k].img
                      obj1.sku2 = []
                    }
                    if (!obj2.k2) {
                      obj2.k2 = stocks[k].k2
                      obj2.v2 = stocks[k].v2
                      obj2.sku3 = []
                    }
                    var sku3 = {
                      id: stocks[k].id,
                      k3: stocks[k].k3,
                      v3: stocks[k].v3,
                      stock_num: stocks[k].stock_num
                    }
                    obj2.sku3.push(sku3)
                  }
                }
              }
              if (typeof obj2.k2 != 'undefined') {
                obj1.sku2.push(obj2)
              }
            }
            if (typeof obj1.k1 != 'undefined') {
              sku1.push(obj1)
            }
          }
          break
      }
      this.skuData = { sku1: sku1 }
    }
    /*
        * 设置元素是否隐藏
        */
    this.setElementHide = function(pindex, key) {
      var _this = this
      $('.sku-sel-list')
        .eq(pindex)
        .find('li')
        .each(function() {
          if ($(this).html() == key)
            $(this)
              .addClass('active')
              .siblings()
              .removeClass('active')
        })
      var stocks = _this.specDetail.stocks
      var skus1 = _this.skuData.sku1
      ;(sku_num = _this.getSkuNum()), (_html = '')
      switch (pindex + '') {
        case '0': //第一层
          if (sku_num > 1) {
            for (var i = 0; i < skus1.length; i++) {
              if (skus1[i].v1 == key) {
                var skus2 = skus1[i].sku2
                if (skus2.length > 0) {
                  $('.sku-sel-list')
                    .eq(1)
                    .html('')
                  for (var j = 0; j < skus2.length; j++) {
                    _html =
                      '<li class="tag sku-tag pull-left ellipsis" data-num="1">' +
                      skus2[j].v2 +
                      '</li>'
                    $('.sku-sel-list')
                      .eq(1)
                      .append(_html)
                  }
                  $('.sku-sel-list')
                    .eq(2)
                    .find('li')
                    .removeClass('active')
                }
              }
            }
          }
          break
        case '1': //第二层
          if (sku_num > 2) {
            var key1 = $('.sku-tag.active')
              .eq(0)
              .html()
            for (var i = 0; i < skus1.length; i++) {
              if (skus1[i].v1 == key1) {
                var skus2 = skus1[i].sku2
                for (var j = 0; j < skus2.length; j++) {
                  if (skus2[j].v2 == key) {
                    var skus3 = skus2[j].sku3
                    $('.sku-sel-list')
                      .eq(2)
                      .html('')
                    for (var k = 0; k < skus3.length; k++) {
                      _html =
                        '<li class="tag sku-tag pull-left ellipsis" data-num="2">' +
                        skus3[k].v3 +
                        '</li>'
                      $('.sku-sel-list')
                        .eq(2)
                        .append(_html)
                    }
                  }
                }
              }
            }
          }
          break
      }
    }
    /*
        * 获取当前商品有几种规格
        */
    this.getSkuNum = function() {
      if (this.specDetail.stocks.length > 0) {
        if (this.specDetail.stocks[0].v3 != '') {
          return 3
        } else if (this.specDetail.stocks[0].v2 != '') {
          return 2
        } else {
          return 1
        }
      } else {
        return 1
      }
    }

    /*
        * 获取规格
        */
    this.getSpecInfo = function() {
      //var resultObj ={};
      var data = this.specDetail.props
      var is_specid = false
      for (var i = 0; i < data.length; i++) {
        var key = $('.sku-sel-list')
          .eq(i)
          .find('li.active')
          .html()
        key = key ? key : ''
        is_specid = key != '' ? true : false
        this.selectData[i] = key
      }
      if (is_specid) {
        //规格全部都选中
        var stocks = this.specDetail.stocks
        var d = this.selectData
        is_specid = false
        for (var i = 0; i < stocks.length; i++) {
          switch (data.length) {
            case 1:
              if (stocks[i].v1 == d[0]) is_specid = true
              break
            case 2:
              if (stocks[i].v1 == d[0] && stocks[i].v2 == d[1]) is_specid = true
              break
            case 3:
              if (
                stocks[i].v1 == d[0] &&
                stocks[i].v2 == d[1] &&
                stocks[i].v3 == d[2]
              )
                is_specid = true
              break
          }

          if (is_specid) {
            this.spec_id = stocks[i].id
            // this.spec_img = stocks[i].img ? stocks[i].img : this.initSpec.img;
            // $(".sku-layout .thumb .js-goods-thumb").attr("src", imgUrl+this.spec_img);
            this.spec_stock_num = stocks[i].stock_num
            $('.quantity .txt').val(this.spec_buy_min) //重置数量
            this.spec_num = this.spec_buy_min
            $('.sku-layout .stock').html('剩余' + stocks[i].stock_num + '件')
            
            /** 
             * author huakang
             * update 2018/06/26
             * 判断是否是享立减商品规格
             * */
            if(this.isShareLi){
              var shareAmount = this.shareCount*this.shareUnitedAmount;
              this.spec_price = (stocks[i].price-shareAmount)>this.lowerPrice?(stocks[i].price-shareAmount):this.lowerPrice
            }else{
            	this.spec_price = stocks[i].price
            }
            /**
             * add by 韩瑜
             * date 2018-10-31
             * 分销商品显示佣金
             */
            if(this.isDistribute == 1){
              this.fisrt_dis_price = (this.spec_price * this.rate / 100).toFixed(2)
              this.sec_dis_price = (this.spec_price * this.rateSec / 100).toFixed(2)
              $(".sku-distr-price").text('分享：一级赚'+this.fisrt_dis_price+'元 / 二级赚'+this.sec_dis_price+'元')
              // this.dis_price = (this.spec_price * this.rate / 100).toFixed(2)
              // $('.distribute').html('<span class="distribute-price">分享可赚'+ this.dis_price +'</span>')
            }
            // end
            if(typeof this.spec_price == 'number'){
              this.spec_price.toFixed(2)
            }
            $('.sku-layout .js-goods-price').html(this.spec_price)
            break
          } else {
            this.spec_id = ''
          }
        }
      }
    }
    /*
        * 获取商品信息 
        * id 产品 编号
        */
    this.setSpecInfo = function(obj) {
      var _this = this
      if (!this.specDetail) {
        $.ajax({
          type: 'post',
          url: obj.url,
          data: obj.data,
          async: false, //同步
          dataType: 'json',
          success: function(json) {
            if (json.status == 1) {
              _this.specDetail = json.data
              _this.dataProcessing()
            }
          },
          error: function() {
            console.log('异常')
          }
        })
      }
    }

    /*
        * this.addEvent 数组 [{event:"click",name:".div_class"},{},{}] 
        * 说明：
        * 1.event 要移除的事件 
        * 2.name 对应的名称 
        */
    this.addEvent = []
    /*
        * 移除事件绑定(弹窗移除时)
        */
    this.removeEvent = function() {
      var arr = this.addEvent
      for (var i = 0; i < arr.length; i++) {
        $('body').off(arr[i].event, arr[i].name)
      }
    }
    /*
        * 遮罩层 
        * 显示 和 隐藏 遮罩层功能
        */

    this.maskShow = function() {
      var mask =
        '<div id="t_mask' +
        this.id +
        '" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; z-index: 1000; transition: none 0.2s ease; opacity: 1; background-color: rgba(0, 0, 0, 0.8);"></div>'
      $('body').append(mask)
    }
    this.maskHide = function() {
      $('#t_mask' + this.id).remove()
    }
    /*
        * 移除弹窗
        */
    this.close = function() {
      $('video').css('display', 'block')
      $('body').css('overflow', 'auto')
      this.removeEvent()
      this.maskHide()
      $('#' + this.id).remove()
    }
  }
  win.tool.spec = new t_spec() //将对象实例给win对象遍可静态调用
})(window, document)

/*
* 1.本对象支持多图上传
* 2.图片ajax上传 到服务器需要很长的响应事件，所以这里取的是本地图片的base64用来显示图片效果
* 3.点击提交时，图片有可能还在未在服务器处理完成并返回对应参数，所以提交数据时应该判断所有上传的图片是否都已返回
* 4.封装一个图片是否全部上传完成并返回对应数据的方法。getUpLoadInfo()
* 5.提交数据时定时调用getUploadInfo()方法直到全部图片返回数据时 才能提交
*/
;(function(win) {
  function tAjaxFile(config) {
    //    用于压缩图片的canvas
    var canvas = document.createElement('canvas')
    var ctx = canvas.getContext('2d')
    //    瓦片canvas
    var tCanvas = document.createElement('canvas')
    var tctx = tCanvas.getContext('2d')
    var that = this
    var fileEl = config.el
    that.fileNum = 0 //当前上传文件数
    that.finishNum = 0 //上传文件完成数
    that.resultData = [] //上传完成返回的数据
    that.suffix = '' //文件后缀
    that.config = {}
    that.config.el = fileEl //js对象
    that.config.maxsize = 700000 //图片大小最大值
    that.config.maxnum = 100 //最多能选择多少张图片 默认100张
    that.index = 0 //上传图片索引
    that.init = function(config) {
      for (var key in config) {
        that.config[key] = config[key]
      }
    }
    that.init(config)
    fileEl.onchange = function(e) {
      if (!this.files.length) {
        if (typeof config.done !== 'undefined') {
          var _resultObj = { status: 0, msg: '没有选择图片！' }
          config.done(_resultObj)
        }
        return
      }
      var files = Array.prototype.slice.call(this.files)
      if (files.length > that.config.maxnum) {
        if (typeof config.done !== 'undefined') {
          var _resultObj = {
            status: 0,
            msg: '最多只可上传' + that.config.maxnum + '张图片！'
          }
          config.done(_resultObj)
        }
        return
      }
      //图片上传累加，因为多次上传图片时上一次还没上传完成的时候也可以继续上传图片,所以要记录所有上传的图片数量.
      that.fileNum += files.length
      files.forEach(function(file, i) {
        if (!/\/(gif|jpeg|png)+$/i.test(file.type)) {
          alert('只支持gif、jpeg、png格式图片')
          return //判断
        }
        //文件后缀
        that.suffix = file.name.split('.')[1]
        var reader = new FileReader()
        reader.onloadstart = function(e) {
          // console.log("开始读取....");
        }
        reader.onprogress = function(e) {
          // console.log("正在读取中....");
        }
        reader.onabort = function(e) {
          // console.log("中断读取....");
        }
        reader.onerror = function(e) {
          // console.log("读取异常....");
        }
        reader.onload = function(e) {
          // console.log("读取完成....");
          var result = this.result
          if (typeof config.done !== 'undefined') {
            //本地图片读取base64完成执行回调函数
            var _resultObj = {
              status: 1,
              msg: '本地图片读取base64完成！',
              base64: result
            }
            config.done(_resultObj)
          }
          var img = new Image()
          img.src = result
          //如果图片大小小于maxsize，则直接上传
          if (result.length <= that.config.maxsize) {
            that.upload(result, file.type)
          } else {
            //图片加载完毕之后进行压缩，然后上传
            if (img.complete) {
              result = that.compress(img)
              that.upload(result, file.type)
            } else {
              img.onload = function() {
                result = that.compress(img)
                that.upload(result, file.type)
              }
            }
          }
        }
        reader.readAsDataURL(file)
      })
    }

    /*
        * 图片压缩方法
        */
    that.compress = function(img) {
      if (that.suffix == 'gif') return img.src
      var initsize = img.src.length
      var width = img.width
      var height = img.height
      var ratio = (width * height) / 4000000
      if (ratio > 1) {
        ratio = Math.sqrt(ratio)
        width /= ratio
        height /= ratio
      } else {
        ratio = 1
      }
      canvas.width = width
      canvas.height = height
      //铺底色
      ctx.fillStyle = '#fff'
      ctx.fillRect(0, 0, canvas.width, canvas.height)
      var count = (width * height) / 1000000
      if (count > 1) {
        //计算要分成多少块瓦片
        count = ~~(Math.sqrt(count) + 1)
        //计算每块瓦片的宽和高
        var nw = ~~(width / count)
        var nh = ~~(height / count)

        tCanvas.width = nw
        tCanvas.height = nh
        for (var i = 0; i < count; i++) {
          for (var j = 0; j < count; j++) {
            tctx.drawImage(
              img,
              i * nw * ratio,
              j * nh * ratio,
              nw * ratio,
              nh * ratio,
              0,
              0,
              nw,
              nh
            )
            ctx.drawImage(tCanvas, i * nw, j * nh, nw, nh)
          }
        }
      } else {
        ctx.drawImage(img, 0, 0, width, height)
      }
      //进行最小压缩
      var ndata = canvas.toDataURL('image/jpeg', 0.6)
      // console.log("压缩前：" + initsize);
      // console.log("压缩后：" + ndata.length);
      // console.log("压缩率：" + ~~(100 * (initsize - ndata.length) / initsize) + "%");
      tCanvas.width = tCanvas.height = canvas.width = canvas.height = 0
      return ndata
    }
    /*
        *  图片上传，将base64的图片转成二进制对象，塞进formdata上传
        */
    that.upload = function(basestr, type) {
      var text = window.atob(basestr.split(',')[1]) //函数用来解码一个已经被base-64编码过的数据
      var buffer = new Uint8Array(text.length)
      var pecent = 0,
        loop = null
      for (var i = 0; i < text.length; i++) {
        buffer[i] = text.charCodeAt(i)
      }
      var blob = getBlob([buffer], type)
      //原生ajax提交图片
      var xhr = new XMLHttpRequest()
      var formdata = getFormData()
      formdata.append('file', blob, 'image.' + that.suffix)
      formdata.append('_token', that.config._token)
      xhr.open('post', '/shop/order/upfile/' + that.config.wid)
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
          var res = JSON.parse(xhr.responseText)
          if (res.status == 1) {
            that.resultData.push(res.data)
          } else {
            that.resultData.push(res)
          }
          //上传完成 完成数加1
          that.finishNum++
        }
      }
      xhr.send(formdata)
    }
    /*
        * 判断所有图片是否上传完成并返回对应数据.
        * @return status=0表示未完成 =1 表示完成 data 表示上传完成返回的数据
        */
    that.getUploadInfo = function() {
      var obj = { status: 0, data: [] }
      if (that.finishNum == that.fileNum) {
        //所有图片都处理完成
        obj.status = 1
        obj.data = that.resultData
      }
      return obj
    }

    /**
     * 获取blob对象的兼容性写法
     * @param buffer
     * @param format
     * @returns {*}
     */
    function getBlob(buffer, format) {
      try {
        return new Blob(buffer, { type: format })
      } catch (e) {
        var bb = new (window.BlobBuilder ||
          window.WebKitBlobBuilder ||
          window.MSBlobBuilder)()
        buffer.forEach(function(buf) {
          bb.append(buf)
        })
        return bb.getBlob(format)
      }
    }
    /**
     * 获取formdata
     */
    function getFormData() {
      var isNeedShim =
        ~navigator.userAgent.indexOf('Android') &&
        ~navigator.vendor.indexOf('Google') &&
        !~navigator.userAgent.indexOf('Chrome') &&
        navigator.userAgent.match(/AppleWebKit\/(\d+)/).pop() <= 534
      //return  new FormDataShim();
      return isNeedShim ? new FormDataShim() : new FormData()
    }
    /**
     * formdata 补丁, 给不支持formdata上传blob的android机打补丁
     * @constructor
     */
    function FormDataShim() {
      // console.warn('using formdata shim');
      var o = this,
        parts = [],
        boundary =
          Array(21).join('-') +
          (+new Date() * (1e16 * Math.random())).toString(36),
        oldSend = XMLHttpRequest.prototype.send
      this.append = function(name, value, filename) {
        parts.push(
          '--' +
            boundary +
            '\r\nContent-Disposition: form-data; name="' +
            name +
            '"'
        )
        if (value instanceof Blob) {
          parts.push(
            '; filename="' +
              (filename || 'blob') +
              '"\r\nContent-Type: ' +
              value.type +
              '\r\n\r\n'
          )
          parts.push(value)
        } else {
          parts.push('\r\n\r\n' + value)
        }
        parts.push('\r\n')
      }
    }
  }
  win.tAjaxFile = tAjaxFile
})(window)

tool.confirm = function(info, success, cancel) {
  var html =
    '<div id="TPHbVnTMoH" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="v8Pmx5et6t" class="popout-confirm popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px);-webkit-transform: translate3d(-50%, -50%, 0px);-moz-transform: translate3d(-50%, -50%, 0px);-o-transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;">'
  html +=
    '<div class="confirm-content font-size-14" style="line-height: 20px; padding: 5px 5px 10px;">' +
    info +
    '</div><hr style="margin: 9px -15px 10px;">'
  html +=
    '<div class="btn-2-1"><p class="js-cancel center font-size-16 js_confirm_cancel" style="padding-top: 5px;">取消</p></div><div class="btn-2-1"><p class="js-ok center c-green font-size-16 js_confirm_ok" style="padding-top: 5px;">确定</p></div></div>'
  if ($('#TPHbVnTMoH') && $('#v8Pmx5et6t')) {
    $('#TPHbVnTMoH').remove()
    $('#v8Pmx5et6t').remove()
  }
  $('body').append(html)
  $('.js_confirm_ok').click(function() {
    success()
    $('#TPHbVnTMoH').hide()
    $('#v8Pmx5et6t').hide()
  })
  $('.js_confirm_cancel').click(function() {
    $('#GkLmo6UNYU .js-address-fm').show()
    $('#TPHbVnTMoH').hide()
    $('#v8Pmx5et6t').hide()
  })
}
tool.phone = function(success) {
  var html = ''
  html +=
    '<div id="0Vnog1babh" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>'
  html +=
    '<div id="M0drbS24ZP" class="popout-box" style="overflow: hidden; visibility: visible; display: block; opacity: 1; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px);-webkit-transform: translate3d(-50%, -50%, 0px);-moz-transform: translate3d(-50%, -50%, 0px);-o-transform: translate3d(-50%, -50%, 0px); border-radius: 4px; background: white; width: 270px; padding: 15px;">'
  html +=
    '<form class="js-login-form popout-login" method="GET" action=""><div class="header c-green center">'
  html +=
    '<h2>请填写您的手机号码</h2></div><fieldset class="wrapper-form font-size-14"><div class="form-item">'
  html +=
    '<label for="phone">手机号</label><input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="" value="">'
  html +=
    '</div><div class="js-help-info font-size-12 error c-orange"></div></fieldset>'
  html +=
    '<div class="action-container"><a class="js-confirm btn btn-green btn-block font-size-14 phone_confirm">确认手机号码</a></div></form></div>'
  if ($('#0Vnog1babh') && $('#M0drbS24ZP')) {
    $('#0Vnog1babh').remove()
    $('#M0drbS24ZP').remove()
  }
  $('body').append(html)
  $('.phone_confirm').click(function() {
    success()
    $('#0Vnog1babh').hide()
    $('#M0drbS24ZP').hide()
  })
}
// 获取url参数值
tool.getParams = function(name) {
  var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i')
  var r = window.location.search.substr(1).match(reg)
  if (r != null) return unescape(r[2])
  return null
}
tool.login = function(success) {
  var html = ''
  html +=
    '<div id="uggE9pVEmv" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="s4X11yDha3" class="popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px);-webkit-transform: translate3d(-50%, -50%, 0px);-moz-transform: translate3d(-50%, -50%, 0px);-o-transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;">'
  html +=
    '<form class="js-login-form popout-login" method="GET" action=""><div class="header c-green center">'
  html +=
    '<h2>该号码注册过，请直接登录</h2></div><fieldset class="wrapper-form font-size-14"><div class="form-item">'
  html +=
    '<label for="phone">手机号</label><input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="请输入你的手机号" disabled="disabled" value="15236271883"></div>'
  html +=
    '<div class="form-item"><label for="password">密码</label><input id="passsword" name="password" type="password" autocomplete="off" placeholder="请输入登录密码"></div><div class="js-help-info font-size-12 error c-orange"></div><div class="bottom-tips font-size-12">'
  html +=
    '</fieldset><div class="action-container"><button type="button" class="js-confirm btn btn-green btn-block font-size-14 login_confirm">确认</button></div><div class="bottom-tips font-size-12">'
  html +=
    '<span class="c-orange">如果您忘了密码，请</span><a href="javascript:;" class="js-change-pwd c-blue">点此找回密码</a><a href="javascript:;" class="js-change-phone c-blue pull-right">更换手机号</a></div>'
  html += '</form></div>'
  if ($('#uggE9pVEmv') && $('#s4X11yDha3')) {
    $('#uggE9pVEmv').remove()
    $('#s4X11yDha3').remove()
  }
  $('body').append(html)
  $('.login_confirm').click(function() {
    success()
  })
}
tool.tip = function(info) {
  if (timer) return
  var html =
    '<div class="motify" id="motify_tip"><div class="motify-inner">' +
    info +
    '</div></div>'
  if ($('#motify_tip')) {
    $('#motify_tip').remove()
  }
  $('body').append(html)
  $('#motify_tip').show()
  var timer = setTimeout(function() {
    $('#motify_tip').remove()
  }, '2000')
}
tool.add_address = function(
  success,
  data,
  name,
  mobile,
  provice,
  city,
  district,
  address,
  code
) {
  var name = name || ''
  var mobile = mobile || ''
  var provice = provice || ''
  var city = city || ''
  var district = district || ''
  var address = address || ''
  var code = code || ''
  console.log(data)
  var html = ''
  html +=
    '<div id="7yBNPekNdX" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="GkLmo6UNYU" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px);-webkit-transform: translate3d(0px, 0px, 0px);-o-transform: translate3d(0px, 0px, 0px);-moz-transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">'
  if (mobile != '') {
    html +=
      '<form class="js-address-fm address-ui address-fm"><h4 class="address-fm-title">编辑收货地址</h4>'
  } else {
    html +=
      '<form class="js-address-fm address-ui address-fm"><h4 class="address-fm-title">新增收货地址</h4>'
  }
  html +=
    '<div class="js-address-cancel cancel-img"></div><div class="block form" style="margin:0;"><div class="block-item no-top-border">'
  html +=
    '<label>收货人</label><input type="text" name="user_name" value="' +
    name +
    '" placeholder="名字">'
  html +=
    '</div><div class="block-item"><label>联系电话</label><input type="tel" name="tel" value="' +
    mobile +
    '" placeholder="手机或固定电话"></div><div class="block-item"><label>选择地区</label>'
  html += '<div class="js-area-select area-layout"><span>'
  html += '<select class="js-province address-province">'
  if (data[-1].length > 0) {
    for (var i = 0; i < data[-1].length; i++) {
      html +=
        '<option value="' +
        data[-1][i]['id'] +
        '">' +
        data[-1][i]['title'] +
        '</option>'
    }
  }
  html += '</select>'
  html += '</span><span><select class="js-city address-city">'
  if (data[1].length) {
    html += '<option value="" selected="selected">选择城市</option>'
    for (var i = 0; i < data[1].length; i++) {
      html +=
        '<option value="' +
        data[1][i]['id'] +
        '">' +
        data[1][i]['title'] +
        '</option>'
    }
  }
  html += '</select></span>'
  html +=
    '<span><select class="js-county address-county"><option value="-1">选择地区</option></select></span>'
  html +=
    '</div></div> <div class="block-item"><label>详细地址</label><div class="address-detail-wrap "> <textarea type="text" value="" class="js-address-detail address-detail" name="address_detail" placeholder="如街道，楼层，门牌号等" rows="1">' +
    address +
    '</textarea>'
  html +=
    '<i class="cancel-input-icon js-cancel-input hide"></i><i class="cancel-input-icon-trigger js-cancel-input hide"></i><div class="address-prompt js-address-prompt"></div>'
  html +=
    '</div></div><div class="block-item"><label>邮政编码</label><input type="tel" maxlength="6" name="postal_code" value="' +
    code +
    '" placeholder="邮政编码(选填)"></div></div>'
  html +=
    '<div class="action-container"><a class="js-address-save btn btn-block btn-green">保存</a>'
  if (mobile != '') {
    html +=
      '<a class="js-address-delete btn btn-block btn-white">删除收货地址</a>'
  }
  html += '</div></form></div>'
  if ($('#7yBNPekNdX') && $('#GkLmo6UNYU')) {
    $('#7yBNPekNdX').remove()
    $('#GkLmo6UNYU').remove()
  }
  $('body')
    .off('click', '.js-address-cancel')
    .on('click', '.js-address-cancel', function() {
      $('#7yBNPekNdX').remove()
      $('#GkLmo6UNYU').remove()
    })
  $('body')
    .off('click', '.js-address-save')
    .on('click', '.js-address-save', function() {
      success()
      $('#7yBNPekNdX').remove()
      $('#GkLmo6UNYU').remove()
    })
  $('body').append(html)
}
tool.comment = function(success, info) {
  var info = info || '我有话要说'
  var html =
    '<div id="HvjnDOnmmN" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="V2XiaCcqGI" class="add-comment-form popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px);-webkit-transform: translate3d(0px, 0px, 0px);-moz-transform: translate3d(0px, 0px, 0px);-o-transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">'
  html +=
    '<div class="top-title clearfix"><span class="js-cancel pull-left cancel_comment">取消</span><span class="js-submit pull-right c-blue js_submit_comment">发送</span><p class="title center font-size-18">评论</p></div>'
  html +=
    '<div class="comment-detail"><textarea name="comment-text" class="js-comment-detail comment-text" placeholder="' +
    info +
    '"></textarea></div></div>'
  if ($('#HvjnDOnmmN') && $('#V2XiaCcqGI')) {
    $('#HvjnDOnmmN').remove()
    $('#V2XiaCcqGI').remove()
  }
  $('body').append(html)
  $('.cancel_comment').click(function() {
    $('#HvjnDOnmmN').remove()
    $('#V2XiaCcqGI').remove()
  })
  $('.js_submit_comment').click(function() {
    success()
    $('#HvjnDOnmmN').remove()
    $('#V2XiaCcqGI').remove()
  })
}

// 自定义弹窗
tool.custom = function(
  title,
  content,
  customCancelname,
  sureBtn,
  cancelHrefUrl,
  sureHrefUrl
) {
  // if(typeof(customCancelname) != "string" || customCancelname == ""){
  customCancelname = customCancelname || '取消'
  sureBtn = sureBtn || '确定'
  cancelHrefUrl = cancelHrefUrl || '##'
  sureHrefUrl = sureHrefUrl || '##'
  // }
  var html = ''
  html +=
    '<div id="mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="content" class="popout-confirm popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: calc(50% - 40px); left: 50%; transform: translate3d(-50%, -50%, 0px); -webkit-transform: translate3d(-50%, -50%, 0px); -moz-transform: translate3d(-50%, -50%, 0px); -o-transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px;opacity: 1;">'
  html +=
    '<div class="center" style="padding: 15px 15px 5px 10px">' +
    title +
    '</div>'
  html +=
    '<div class="confirm-content font-size-14 c-gray-dark" style="line-height: 20px; padding: 15px;">' +
    content +
    '</div>'
  html +=
    '<div class="btn-1" style="border-top: 1px solid #d5d5d5;padding: 10px;">'
  html +=
    '<p class="js-cancel center font-size-16 js_confirm_cancel" style="padding-top: 5px;display: inline-block;float: left;width: 50%">' +
    customCancelname +
    '</p>'
  html +=
    '<p class="js-ok center c-green font-size-16 js_confirm_ok" style="padding-top: 5px;display: inline-block;width: 50%">' +
    sureBtn +
    '</p>'
  html += '</div></div>'
  $('body').append(html)
  $('#mask').on('click', function() {
    $('#mask').remove()
    $('#content').remove()
  })
  $('.js-cancel').on('click', function() {
    $('#mask').remove()
    $('#content').remove()
    location.href = cancelHrefUrl
  })
  $('.js-ok').on('click', function() {
    $('#mask').remove()
    $('#content').remove()
    window.location.href = sureHrefUrl
  })
}
// 判断是否为空字符串为空返回true;
tool.isNull = function(str) {
  if (str == '') return true
  var regu = '^[ ]+$'
  var re = new RegExp(regu)
  return re.test(str)
}
// 验证是否为手机号
tool.istel = function(str) {
  if (
    !/^1[34578]\d{9}$/.test(str) &&
    !/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/.test(str)
  ) {
    return false
  } else {
    return true
  }
}
// 验证手机号码是否正确
tool.isPhone = function(str) {
  if (!/^1[34578]\d{9}$/.test(str)) {
    return false
  } else {
    return true
  }
}

/*
*通告  @author huoguanghui
*type 0一个按钮 1两个按钮
*title  标题
*content 内容
*sureTitle 确定按钮名字
*sureBtn 确定按钮方法
*cancleTitle 取消名字
*cancleBtn 取消方法
*/
tool.notice = function(
  type,
  title,
  content,
  sureTitle,
  sureBtn,
  cancleTitle,
  cancleBtn
) {
  sureTitle = sureTitle || '确定'
  cancleTitle = cancleTitle || '取消'
  //console.log(cancleTitle)
  var html = ''
  html +=
    '<div id="mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="content" class="popout-confirm popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: calc(50% - 40px); left: 50%; transform: translate3d(-50%, -50%, 0px); -webkit-transform: translate3d(-50%, -50%, 0px); -moz-transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 90%;opacity: 1;">'
  if(title){
    html +=
      '<div class="center" style="padding: 15px 15px 5px 10px;font-size: 16px">' +
      title +
      '</div>'
  }
  html +=
    '<div class="confirm-content text-danger" style="font-size:14px; padding: 30px 10px;text-align:center;line-height: 20px;">' +
    content +
    '</div>'
  html +=
    '<div class="btn-1" style="border-top: 1px solid #DCDCDC;height:42px;line-height:42px;">'
  if (type == 0) {
    html +=
      '<p class="js-ok center text-primary font-size-16 js_confirm_ok" style="display: inline-block;width: 100%">' +
      sureTitle +
      '</p>'
  } else {
    html +=
      '<p class="js-cancel center text-info font-size-16 js_confirm_cancel" style="display: inline-block;float: left;width: 50%;box-sizing: border-box;border-right:1px solid #DCDCDC">' +
      cancleTitle +
      '</p>'
    html +=
      '<p class="js-ok center text-primary font-size-16 js_confirm_ok" style="display: inline-block;width: 50%">' +
      sureTitle +
      '</p>'
  }
  html += '</div></div>'
  $('body').append(html)
  $('.js-ok').on('click', function() {
    if (sureBtn) {
      sureBtn()
    }
    $('#mask').remove()
    $('#content').remove()
  })
  $('.js-cancel').on('click', function() {
    if (cancleBtn) {
      cancleBtn()
    }
    $('#mask').remove()
    $('#content').remove()
  })
}
tool.hitEgg = function(obj) {
  var html = ''
  html +=
    '<div id="mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>'
  html +=
    '<div id="content" class="popout-confirm popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: calc(50% - 40px);top: -webkit-calc(50% - 40px);top: -moz-calc(50% - 40px);top: -o-calc(50% - 40px); left: 50%; transform: translate3d(-50%, -50%, 0px); -webkit-transform: translate3d(-50%, -50%, 0px); -moz-transform: translate3d(-50%, -50%, 0px); -o-transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px;opacity: 1;">'
  html +=
    '<div class="confirm-content font-size-14" style="line-height: 20px; padding: 15px 30px; text-align:center;">' +
    obj.content +
    '</div>'
  html +=
    '<div class="btn-1" style="border-top: 1px solid #d5d5d5;padding: 10px 20px;">'
  if (obj.type == 0) {
    html +=
      '<p class="js-ok center font-size-14 js_confirm_ok" style="padding-top: 5px;display: inline-block;width: 100%;background:#fb2e3d;line-height:30px;border-radius:5px;color:#fff;">' +
      obj.sureTitle +
      '</p>'
  } else {
    html +=
      '<p class="js-cancel center font-size-16 js_confirm_cancel" style="padding-top: 5px;display: inline-block;float: left;width: 50%">取消</p>'
    html +=
      '<p class="js-ok center c-green font-size-16 js_confirm_ok" style="padding-top: 5px;display: inline-block;width: 50%">' +
      obj.sureTitle +
      '</p>'
  }
  html += '</div></div>'
  $('body').append(html)
  $('.js-ok')
    .off()
    .on('click', function() {
      if (obj.sureBtn) {
        obj.sureBtn()
      }
      $('#mask').remove()
      $('#content').remove()
    })
  $('.js-cancel')
    .off()
    .on('click', function() {
      if (obj.cancelBtn) {
        obj.cancelBtn()
      }
      $('#mask').remove()
      $('#content').remove()
    })
}

/*
loading加载状态
*/
var hstool = (function() {
  var hstool = {}
  hstool.config = {
    //默认配置
    type: 0, //类型 0.msg 1.tips提示框 2.选择商品
    title: '信息', //标题
    opacity: 0.7, //遮罩层透明度
    message: '',
    zIndex: 19891014,
    time: 0, //0表示不自动关闭
    content: '',
    isMask: false, //是否添加点击遮罩层事件
    done: null, //完成操作的回调函数
    host: '', //域名
    area: [], //区域 参数 width,height
    skin: 'default' //皮肤设定 后期扩展使用
  }
  /*
    * 初始化参数
    */
  hstool.init = function(config) {
    for (var key in config) {
      this.config[key] = config[key]
    }
  }
  /*
    * loading 加载层
    * 
    */
  hstool.load = function(config) {
    config = config || {}
    var that = this
    that.init(config)
    $('body').append('<div class="hstool-dialog-loading"></div>')
    $('.hstool-dialog-loading').css({ 'z-index': that.config.zIndex + 2 })
    if (that.config.time > 0) {
      setTimeout(function() {
        that.closeLoad()
      }, that.config.time)
    }
  }
  /*
    * 关闭加载层 
    */
  hstool.closeLoad = function() {
    $('.hstool-dialog-loading').remove()
  }
  return hstool
})()
// hstool.closeLoad();
/*
* 绑定手机号
*/
tool.bingMobile = function(sucCallback) {
  // alert(3)
  if ($('#SvixZzpA0X').length > 0 && $('#NyAf3g3520').length > 0) {
    $('#SvixZzpA0X').show()
    $('#NyAf3g3520').show()
    $('input[name="phone"]').val('')
    $('input[name="code"]').val('')
    $('body').css('position', 'fixed')
  } else {
    var timer = null
    var html =
      '<div id="SvixZzpA0X" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>'
    html +=
      '<div id="NyAf3g3520" class="popout-box" style="overflow: hidden; visibility: visible; display: block; opacity: 1; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); border-radius: 4px; background: white; width: 80%; padding: 15px;">'
    html +=
      '<div class="js-login-form popout-login"><div class="header c-green center"><h2>绑定手机号</h2><img class="close_bind_phone" src="https://upx.cdn.huisou.cn/wscphp/xcx/images/xcxImg/x.png"></div>'
    html +=
      '<div class="bind-phone-info"> 为响应国家法律要求，为保障用户安全及正常使用，即日起使用该平台需要绑定手机号，请您务必绑定。</div>'
    html +=
      '<fieldset class="wrapper-form font-size-14"><div class="form-item"><label for="phone">手机号</label><input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="请输入手机号" value=""></div><div class="js-help-info font-size-12 error c-orange"></div></fieldset>'
    html +=
      '<fieldset class="wrapper-form font-size-14"><div class="form-item"><label for="phone">验证码</label><input  id="code" name="code" type="text" autocomplete="off" placeholder="请输入验证码" value="" style="width:69%"><button class="js-auth-code tag btn-auth-code font-size-12 tag-green" data-span="获取验证码">获取验证码</button></div>'
    html +=
      '<div class="js-help-info font-size-12 error c-orange"></div></fieldset>'
    html +=
      '<div class="action-container"><input type="button" class="js-confirm btn btn-green bind-phone-submit btn-block bindMobileSure" value="确定" style="cursor:pointer"></div></div></div>'
    $('body').append(html)

    $('body').css('position', 'fixed')
    //关闭弹窗
    // $('body').on('click', '#SvixZzpA0X', function() {
    //   $('#SvixZzpA0X').hide()
    //   $('#NyAf3g3520').hide()
    //   clearInterval(timer)
    //   $('.js-auth-code').html('获取验证码')
    //   $('.js-auth-code').removeAttr('disabled')
    //   $('body').css('position', 'relative')
    // })
    $('body').on('click', '.close_bind_phone', function() {
      $('#SvixZzpA0X').hide()
      $('#NyAf3g3520').hide()
      clearInterval(timer)
      $('.js-auth-code').html('获取验证码')
      $('.js-auth-code').removeAttr('disabled')
      $('body').css('position', 'relative')
    })
    //发送验证码
    $('body').on('click', '.js-auth-code', function() {
      if ($('input[name="phone"]').val() == '') {
        tool.tip('手机号不能为空！')
        return
      }
      if (!/^1[3|4|5|7|8|9][0-9]{9}$/.test($('input[name="phone"]').val())) {
        tool.tip('手机号码错误！')
        return
      }
      $(this).attr('disabled', 'disabled')
      var time = 60
      var that = $(this)
      $(this).html(time + 's')
      time--
      timer = setInterval(function() {
        if (time == 0) {
          clearInterval(timer)
          that.html('获取验证码')
          that.removeAttr('disabled')
          return
        }
        that.html(time + 's')
        time--
      }, 1000)
      //发送验证码请求
      $.get(
        '/shop/bindmobile/sendCode',
        { phone: $('input[name="phone"]').val(), SMS_code: 2 },
        function(data) {
          tool.tip(data.info)
        }
      )
    })
  }
  $('body')
    .off('click', '.bindMobileSure')
    .on('click', '.bindMobileSure', function() {
      if ($('input[name="phone"]').val() == '') {
        tool.tip('手机号不能为空！')
        return
      }
      if (!/^1[3|4|5|7|8|9][0-9]{9}$/.test($('input[name="phone"]').val())) {
        tool.tip('手机号码错误！')
        return
      }
      if ($('input[name="code"]').val() == '') {
        tool.tip('验证码不能为空！')
        return
      }
      var _token = $('meta[name="csrf-token"]').attr('content')
      $.post(
        '/shop/bindmobile/index',
        {
          phone: $('input[name="phone"]').val(),
          code: $('input[name="code"]').val(),
          _token: _token
        },
        function(data) {
          tool.tip(data.info)
          if (data.status) {
            sucCallback()
          }
        }
      )
      $('#SvixZzpA0X').hide()
      $('#NyAf3g3520').hide()
      // alert(3);
    })
  // $('body').off('click.bindMobileSure').on('click','.bindMobileSure',function(){
  //     alert(3);
  // })
}
// 解析微页面店铺主页组件
tool.componentAssign = function(obj, content) {
  var lists = []
  for (var i in content) {
    if (content[i] != undefined) {
      if (content[i]['type'] == 'coupon') {
        var len = content[i]['couponList'].length
        if (len) {
          for (var j = 0; j < len; j++) {
            if (content[i]['couponList'][j]['type'] == 0) {
              content[i]['couponList'][j]['cls'] = ''
            } else if (content[i]['couponList'][j]['type'] == 1) {
              content[i]['couponList'][j]['cls'] = 'invlid'
            } else if (content[i]['couponList'][j]['type'] == 2) {
              content[i]['couponList'][j]['cls'] = 'overdue'
            } else if (content[i]['couponList'][j]['type'] == 3) {
              content[i]['couponList'][j]['cls'] = 'over'
            }
          }
        }
      } else if (content[i]['type'] == 'bingbing') {
        $('body').addClass('full-screen auto-footer-off')
        if (content[i]['lists'].length > 0) {
          for (var j = 0; j < content[i]['lists'].length; j++) {
            content[i]['lists'][j]['icon'] =
              imgUrl + content[i]['lists'][j]['icon']
          }
        }
        lists.push(content[i])
        // alert($('.swiper-slide').width());
        obj.bingbing = true
        $('footer').remove()
        break
      } else if (content[i]['type'] == 'imageTextModel') {
        $('body').addClass('full-screen auto-footer-off')
        if (
          content[i]['lists'][0] != undefined &&
          content[i]['lists'][0]['lists'].length
        ) {
          for (var n = 0; n < content[i]['lists'][0]['lists'].length - 1; n++) {
            if (n == 0) {
              content[i]['lists'][n]['isActive'] = true
            } else {
              if (n < content[i]['lists'].length) {
                content[i]['lists'][n]['isActive'] = false
              }
            }
            obj.textList.push(content[i]['lists'][0]['lists'][n])
          }
        }
        lists.push(content[i])
        // alert($('.swiper-slide').width());
        setTimeout(function() {
          var swiper = new Swiper('.swiper-container', {
            paginationClickable: true,
            autoplay: 2000,
            loop: true
          })
        }, 1000)
        // obj.bingbing = true;
        $('footer').remove()
        break
      } else if (content[i]['type'] == 'rich_text') {
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
      } else if (content[i]['type'] == 'header') {
        if (content[i]['logo'].indexOf(imgUrl) >= 0) {
          content[i]['bg_image'] = content[i]['bg_image']
          content[i]['logo'] = content[i]['logo']
        } else {
          content[i]['logo'] = imgUrl + content[i]['logo']
          content[i]['bg_image'] = imgUrl + content[i]['bg_image']
        }
        content[i]['order_link'] = '/shop/order/index/' + id
      } else if (content[i]['type'] == 'goods') {
        if (content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4) {
          content[i]['btnStyle'] = '0'
        }
        // 判断商品名显示
        if (content[i]['goodName'] || content[i]['listStyle'] == 4) {
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
                content[i]['thGoods'][content[i]['thGoods'].length - 1].push(
                  content[i]['goods'][j]
                )
              } else {
                content[i]['thGoods'][content[i]['thGoods'].length - 1].push(
                  content[i]['goods'][j]
                )
              }
            } else {
              content[i]['thGoods'][0] = []
              content[i]['thGoods'][0].push(content[i]['goods'][j])
            }
          }
        }
      } else if (content[i]['type'] == 'goodslist') {
        if (content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4) {
          content[i]['btnStyle'] = '0'
        }
        // 判断商品名显示
        if (content[i]['goodName'] || content[i]['listStyle'] == 4) {
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
                content[i]['thGoods'][content[i]['thGoods'].length - 1].push(
                  content[i]['goods'][j]
                )
              } else {
                content[i]['thGoods'][content[i]['thGoods'].length - 1].push(
                  content[i]['goods'][j]
                )
              }
            } else {
              content[i]['thGoods'][0] = []
              content[i]['thGoods'][0].push(content[i]['goods'][j])
            }
          }
        }
        // if(content[i].thGoods.length>0){
        //     for(var j =0; j< content[i]['thGoods'].length;j++){
        //         for(var n = 0;n<content[i]['thGoods'][j].length;n++){
        //             content[i]['thGoods'][j][n]['thumbnail'] = imgUrl + content[i]['thGoods'][j][n]['thumbnail'];
        //         }
        //     }
        // }
      } else if (content[i]['type'] == 'title') {
        if (content[i]['titleStyle'] == 2) {
          content[i]['bgColor'] = '#fff'
        }
      } else if (content[i]['type'] == 'good_group' || content[i]['type'] == 'group_template') {
        console.log(content[i])
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
                  content[i]['top_nav'][z]['goods'][j]['is_price_negotiable'] ==
                  1
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
          // console.log(content[i]['left_nav']);
          for (var z = 0; z < content[i]['left_nav'].length; z++) {
            content[i]['left_nav'][z]['href'] = 'top_nav_' + randomString(12)
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
                  imgUrl + content[i]['left_nav'][z]['goods'][j]['thumbnail']
                // content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
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
      } else if (content[i]['type'] == 'marketing_active') {
        if (content[i]['content'].length == 0) {
          continue
        }
        var sku = content[i]['content'][0]['sku']
        var seckill_price = sku[0]['seckill_price'] //秒杀价格
        var seckill_stock = 0 //秒杀库存
        for (var j = 0; j < sku.length; j++) {
          seckill_price =
            parseFloat(seckill_price) <= parseFloat(sku[j]['seckill_price'])
              ? seckill_price
              : sku[j]['seckill_price'] //秒杀价格取最小
          seckill_stock += parseInt(sku[j]['seckill_stock'])
        }
        content[i].min_seckill_price = seckill_price
        content[i].total_seckill_stock = seckill_stock
      } else if (content[i]['type'] == 'image_ad') {
        //console.log(content[i])
        if (content[i].images.length > 0) {
          for (var j = 0; j < content[i].images.length; j++) {
            if (content[i].images[j]['FileInfo']['path'].indexOf(imgUrl) >= 0) {
              content[i].images[j]['FileInfo']['path'] =
                content[i].images[j]['FileInfo']['path']
            } else {
              content[i].images[j]['FileInfo']['path'] =
                imgUrl + content[i].images[j]['FileInfo']['path']
            }
          }
        }
        if (content[i]['advsListStyle'] == 2) {
          setTimeout(function() {
            var swiper = new Swiper('.swiper-container', {
              pagination: '.swiper-pagination',
              paginationClickable: true,
              autoplay: 2000,
              loop: true
            })
          }, 1000)
        }
      } else if (content[i]['type'] == 'image_link') {
        if (content[i]['images'].length > 0) {
          for (var j = 0; j < content[i]['images'].length; j++) {
            content[i]['images'][j]['thumbnail'] =
              imgUrl + content[i]['images'][j]['thumbnail']
          }
        }
      } else if (content[i]['type'] == 'spell_goods') {
        if (content[i]['groups'].length) {
          for (var j = 0; j < content[i]['groups'].length; j++) {
            if (content[i]['groups'][j]['member'] == undefined) {
              content[i]['groups'][j]['member'] = []
            }
          }
        }
      }
      lists.push(content[i])
    }
  }
  return lists
}
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
/**
 * @author: 魏冬冬（zbf5279@dingtalk.com）
 * @description: 初始化未读消息数量socket
 * @param: ''
 * @return: {void}
 * @Date: 2019-10-09
 */
tool.initSocket = function(options) {
  var socketUrl = '';
  if (window.location.host == 'www.huisou.cn') {
    // 线上
    socketUrl = "wss://hsim.huisou.cn:9082";
  } else {
    // 测试环境
    socketUrl = "wss://kf.huisou.cn:9082";
  }
  var socket = io.connect(socketUrl);
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 未读消息socket链接
   * @param {String} res 成功回调参数 
   * @return: {void}
   * @Date: 2019-10-09 09:42:23
   */
  socket.on("userContSuc", function(res) {
      if (res === 'userContSuc') {
        socket.emit('userListenJoin', {shopId:options.shopId,userId:options.userId,joinWay: options.joinWay,sign: options.sign});
      }
  })
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 监听未读消息事件
   * @param {String} res 消息数量
   * @return: 
   * @Date: 2019-10-09 09:44:23
   */
  socket.on('wscUserUnReadMsgNum', function(res) {
    options.msgCallBack && options.msgCallBack(res)
  })
}
// $('.ceshi').click(function(){
//     alert(3);
//     tool.bingMobile();
// })
// <div id="SvixZzpA0X" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>
// <div id="HV1IzPOC55" class="popout-box" style="overflow: hidden; position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;">
//     <div class="js-login-form popout-login">
//         <div class="header c-green center">
//             <div class="h2">绑定手机号</div>
//         </div>
//         <div class="wrapper-form font-size-14 fieldset">
//             <div class="form-item">
//                 <span class="label">手机号</span>
//                 <input id="phone" name="phone" type="number" maxlength="11" placeholder="请输入你的手机号" />
//             </div>
//             <div class="form-item js-image-verify hide">
//                 <span class="label">身份校验</span>
//                 <input id="verifycode" name="verifycode" class="js-verify-code item-input" type="number" style="width:178px" maxlength="6" placeholder="输入右侧数字" />
//                 <image class="js-verify-image verify-image" src=""></image>
//             </div>
//             <div class="form-item">
//                 <span class="label">验证码</span>
//                 <input id="code" name="code" type="span" style="width:127px" maxlength="6" placeholder="输入短信验证码" bindtap="bindCode" />
//                 <span class="js-auth-code tag btn-auth-code font-size-12 tag-green" data-span="获取验证码">获取验证码</span>
//                 <!-- <span class="js-auth-code tag btn-auth-code font-size-12 tag-green" data-span="获取验证码">等待60秒</span> -->
//             </div>
//             <div class="js-help-info font-size-12 error c-orange"></div>
//         </div>
//         <div class="action-container">
//             <button type="button" class="js-confirm btn btn-green btn-block font-size-14" bindtap="bindMobile">确定</button>
//         </div>
//     </div>
// </div>
