Vue.http.options.emulateJSON = true
var app = new Vue({
  el: '#app',
  data: {
    click_index: 0, //是否选为默认地址的下标
    isShowAndroidBounces: false, //控制删除地址弹框是否显示
    Del_index: '', //得到当前删除的下标
    Del_id: '', //删除地址的id
    address_list_data: '',
    sku_id: '', //规格id
    pid: '', // 产品id
    rule_id: '', //团购的规则id
    num: '', //数量
    limit_num: null, //限购数量
    groups_id: '',
    skuId: '',
    activityId: '',
    wid:'',//店铺id
  },

  created: function() {
    var that = this
    var Request = new Object()
    Request = GetRequest()
    console.log(Request)
    this.activityId = Request.activityId
    this.skuId = Request.skuId
    this.sku_id = Request.sku_id
    this.pid = Request.pid
    this.rule_id = Request.rule_id
    this.num = Request.num
    this.limit_num = Request.limit_num
    this.groups_id = GetQueryString('groups_id')
    this.come = Request.come
    this.cart_id = Request.cart_id
    //add by 韩瑜 2018-8-10 获取wid
    this.wid = Request.wid
    this.giftid = Request.giftid
    this.activity_id = Request.activity_id
    //end
    that.$http
      .get('/shop/member/addressList/?token=' + _token)
      .then(function(res) {
        that.address_list_data = res.body.data.data
        for (var i = 0; i < res.body.data.data.length; i++) {
          if (res.body.data.data[i].type == 1) {
            this.click_index = i
          }
        }
        //如果只存在一个地址时 把他变为默认地址
        if (that.address_list_data.length == 1) {
          that.$http
            .post('/shop/member/addressDefault', {
              _token: _token,
              id: that.address_list_data[0].id
            })
            .then(function(res) {})
        }
      })
//    add by 韩瑜 2018-8-16 来源为大转盘奖品时,隐藏默认/修改/删除按钮
    if (that.come == 'gift1' || that.come == 'gift2') {
    	$('.operation').hide();
		}
//  end
  },
  methods: {
    //选择默认地址
    selected_default: function(click_index, id) {
      var that = this
      this.click_index = click_index
      that.$http
        .post('/shop/member/addressDefault', {
          _token: _token,
          id: id
        })
        .then(function(res) {
          if (res.body.status == 1) {
            if (!that.limit_num) {
              that.limit_num = ''
            }
            if (that.come == 'good') {
              if (that.activityId) {
                location.href =
                  '/shop/shareevent/order/waitsubmit?activityId=' +
                  that.activityId +
                  '&num=' +
                  that.num +
                  '&skuId=' +
                  that.skuId
              } else {
                location.href =
                  '/shop/order/waitPayOrder?cart_id=' + that.cart_id
              }
            } 
            //add by 韩瑜 2018-8-10
            //来源为会员主页的时候 点击默认返回会员主页
            else if(that.come == 'member'){
            	location.href = '/shop/member/index/' + that.wid
            }
            //end
            else {
              location.href =
                '/shop/web/groups/getSettlementInfo?pid=' +
                that.pid +
                '&sku_id=' +
                that.sku_id +
                '&num=' +
                that.num +
                '&rule_id=' +
                that.rule_id +
                '&limit_num=' +
                that.limit_num +
                '&groups_id=' +
                that.groups_id
            }
          }
        })
    },
    //弹框开启和关闭
    close_androidBounces_open: function(del_index, id) {
      this.isShowAndroidBounces = !this.isShowAndroidBounces
      this.Del_index = del_index
      this.Del_id = id
    },
    //确定删除后执行的操作
    onConfirm: function() {
      var that = this
      that.address_list_data.splice(that.Del_index, 1)
      console.log(that.click_index, that.Del_index)
      if (
        that.click_index == that.Del_index &&
        that.address_list_data.length != 0
      ) {
        that.click_index = 0
        that.$http
          .post('/shop/member/addressDefault', {
            _token: _token,
            id: that.address_list_data[0].id
          })
          .then(function(res) {})
      }
      this.isShowAndroidBounces = !this.isShowAndroidBounces
      that.$http
        .post('/shop/member/addressDel', {
          id: that.Del_id,
          _token: _token
        })
        .then(function(res) {
          if (res) {
            that.getData()
          }
        })
    },
    //前往新增页面新增地址
    newAddress: function() {
      var that = this
      if (!that.limit_num) {
        that.limit_num = ''
      }
      if (that.come == 'good') {
        if (that.activityId) {
          location.href =
            '/shop/member/addAddress?come=good&activityId=' +
            that.activityId +
            '&num=' +
            that.num +
            '&skuId=' +
            that.skuId
        } else {
          location.href =
            '/shop/member/addAddress?come=good' + '&cart_id=' + that.cart_id
        }
      }
      //add by 韩瑜 218-8-10
      //来源为会员主页时，来源修改为member
      else if(that.come == 'member'){
      	location.href =
            '/shop/member/addAddress?come=member' + '&cart_id=' + that.cart_id + '&wid=' + that.wid
      }
      //add by 韩瑜 218-8-20
      //来源为大转盘赠品时，来源修改为gift1
      else if(that.come == 'gift1'){
      	location.href =
            '/shop/member/addAddress?come=gift1' + '&activity_id=' + that.activity_id + '&wid=' + that.wid + '&giftid=' + that.giftid 
      }
      //来源为砸金蛋赠品时，来源修改为gift2
      else if(that.come == 'gift2'){
      	location.href =
            '/shop/member/addAddress?come=gift2' + '&activity_id=' + that.activity_id + '&wid=' + that.wid + '&giftid=' + that.giftid 
      }
      //end
      else {
        if (that.address_list_data == '') {
          location.href =
            '/shop/member/addAddress?pid=' +
            that.pid +
            '&sku_id=' +
            that.sku_id +
            '&num=' +
            that.num +
            '&rule_id=' +
            that.rule_id +
            '&limit_num=' +
            that.limit_num +
            '&first_add=1' +
            '&groups_id=' +
            that.groups_id
        } else {
          location.href =
            '/shop/member/addAddress?pid=' +
            that.pid +
            '&sku_id=' +
            that.sku_id +
            '&num=' +
            that.num +
            '&rule_id=' +
            that.rule_id +
            '&limit_num=' +
            that.limit_num +
            '&groups_id=' +
            that.groups_id
        }
      }
    },
    //编辑地址
    editor_address: function(address_id) {
      var that = this
      if (!that.limit_num) {
        that.limit_num = ''
      }
      if (that.come == 'good') {
        if (that.activityId) {
          location.href =
            '/shop/member/addAddress?id=' +
            address_id +
            '&come=good' +
            '&activityId=' +
            that.activityId +
            '&num=' +
            that.num +
            '&skuId=' +
            that.skuId
        } else {
          location.href =
            '/shop/member/addAddress?id=' +
            address_id +
            '&come=good' +
            '&cart_id=' +
            that.cart_id
        }
      }
      //add by 韩瑜 2018-8-10
      //来源为会员主页时，编辑跳转
      else if(that.come == 'member'){
      	location.href = '/shop/member/addAddress?id=' + address_id + '&come=member' + '&wid=' + that.wid
      }
      //end
      else {
        if (that.address_list_data.length == 1) {
          location.href =
            '/shop/member/addAddress?id=' +
            address_id +
            '&pid=' +
            that.pid +
            '&sku_id=' +
            that.sku_id +
            '&num=' +
            that.num +
            '&rule_id=' +
            that.rule_id +
            '&limit_num=' +
            that.limit_num +
            '&first_add=1' +
            '&groups_id=' +
            that.groups_id
        } else {
          location.href =
            '/shop/member/addAddress?id=' +
            address_id +
            '&pid=' +
            that.pid +
            '&sku_id=' +
            that.sku_id +
            '&num=' +
            that.num +
            '&rule_id=' +
            that.rule_id +
            '&limit_num=' +
            that.limit_num +
            '&groups_id=' +
            that.groups_id
        }
      }
    },
    //请求数据
    getData: function() {
      var that = this
      var Request = new Object()
      Request = GetRequest()
      this.sku_id = Request.sku_id
      this.pid = Request.pid
      this.rule_id = Request.rule_id
      this.num = Request.num
      this.limit_num = Request.limit_num
      that.$http
        .get('/shop/member/addressList/?token=' + _token)
        .then(function(res) {
          that.address_list_data = res.body.data.data
          for (var i = 0; i < res.body.data.data.length; i++) {
            if (res.body.data.data[i].type == 1) {
              this.click_index = i
            }
          }
        })
    },
    default_address: function() {
      var that = this
      //如果只存在一个地址时 把他变为默认地址
      if (that.address_list_data.length == 1) {
        that.$http
          .post('/shop/member/addressDefault', {
            _token: _token,
            id: that.address_list_data[0].id
          })
          .then(function(res) {})
      }
    },
    chooseAddress: function(val) {
      var that = this
      if (this.come == 'good') {
        if (that.activityId) {
          location.href =
            '/shop/shareevent/order/waitsubmit?activityId=' +
            that.activityId +
            '&num=' +
            that.num +
            '&skuId=' +
            that.skuId +
            '&address_id=' +
            val.id
        } else {
          location.href =
            '/shop/order/waitPayOrder?cart_id=' +
            this.cart_id +
            '&address_id=' +
            val.id
        }
      }
      //add by 韩瑜 2018-8-10
      //来源为会员主页时，不跳转
      else if(this.come == 'member'){
      	return false;
      }
      //add by 韩瑜 2018-8-19
      //来源为大转盘赠品修改地址时，回到赠品地址修改页
      else if(this.come == 'gift1'){
      	that.$http.post('/shop/activity/setAwardAddress/'+ that.wid, {
      		type:1,//1大转盘，2砸金蛋，3刮刮卡
      		activityId:that.activity_id,
        	addressId:val.id,
        	isConfirm:0,
        	_token: _token
        })
        .then(function(res) {
					location.href = '/shop/activity/method/'+ that.giftid + '/1';
        }) 	
      }
      else if(this.come == 'gift2'){
      	that.$http.post('/shop/activity/setAwardAddress/'+ that.wid, {
      		type:2,//1大转盘，2砸金蛋，3刮刮卡
      		activityId:that.activity_id,
        	addressId:val.id,
        	isConfirm:0,
        	_token: _token
        })
        .then(function(res) {
					location.href = '/shop/activity/method/'+ that.giftid + '/2';
        }) 	
      }
      else {
        location.href =
          '/shop/web/groups/getSettlementInfo?pid=' +
          this.pid +
          '&sku_id=' +
          this.sku_id +
          '&num=' +
          this.num +
          '&rule_id=' +
          this.rule_id +
          '&limit_num=' +
          this.limit_num +
          '&address_id=' +
          val.id +
          '&groups_id=' +
          this.groups_id
      }
    }
  }
})
//获取url中"?"符号后的字符
function GetRequest() {
  var url = location.search //获取url中"?"符后的字串
  var theRequest = new Object()
  if (url.indexOf('?') != -1) {
    var str = url.substr(1)
    strs = str.split('&')
    for (var i = 0; i < strs.length; i++) {
      theRequest[strs[i].split('=')[0]] = strs[i].split('=')[1]
    }
  }
  return theRequest
}
//获取url_参数
function GetQueryString(name) {
  var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)')
  var r = window.location.search.substr(1).match(reg)
  if (r != null) return unescape(r[2])
  return null
}
