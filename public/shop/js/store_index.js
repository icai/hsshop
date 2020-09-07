/* 
 * add by 韩瑜 
 * date 2018-10-26
 * 微页面图片懒加载
 */
Vue.use(VueLazyload,{
    preLoad: 1.3,
    error: _host + 'shop/images/lazyload1.gif',
    loading: _host + 'shop/images/lazyload1.gif',
    attempt: 1
})
//end
hstool.load();
var key = [];
var j = 0;
var k = 0;
var iarr = [];
var n = 0;
Vue.filter("substr", function(value) {
    console.log(value.length);
    if(value.length > 5){
        value = value.substr(0,5) + '...';
    }else{
        value = value;
    }
    return value;
});
new Vue({
  el: '#container',
  data:{
    header:[],
    lists:[],   
    footer:{},
    goodData:null,
    bingbing:false,
    bg_color:'',
    textList:[],
    qq:'',
    url:'',
    telphone:'',
    kefuShow:false,
    host:host,
    _host:_host,
    imgUrl:imgUrl,
    videoUrl:videoUrl,
    wid:wid,
    topNav:[],
    topNav_index:0,
    topNav_flag:false,
    topNav_color:{
        background_font_color:'#fff',
        checked_font_color:'red',
        font_color:'#ddd'
    },
    checked_font_color:{
        borderBottom:'2px solid red',
        color:'red'
    },
    is_show:'',//红包弹窗，0: 弹窗展示, 1: 右下角图标展示, 2: 不展示
    activity_title:'',//红包名称
    bonusShow:false,//红包弹窗出现开关
    bonusShow_tip:false,//红包提示
    leftIndex:0,//商品分组左侧index
    leftNav:"",//左侧列表
  },
  methods: {
      //底部点击
      showSub:function(menu,index){
        for(var i=0;i<this.footer.menu.length;i++){
            if(i != index){
                this.footer.menu[i].submenusShow = false;
            }
        }
        if(menu.submenus.length>0){
            if(menu.submenusShow){
                menu.submenusShow = false;
            }else{
                menu.submenusShow = true;
            }
        }
      },
      // 设置加入购物弹窗data
      setGoodData:function(data){
        // console.log(data);
        this.goodData = data;
        console.log(this.goodData);
      },
      hideGoodModel:function(){
        this.goodData = null;
      },
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
      //显示客服
      showKeFu:function(){
        this.kefuShow = true;
      },
      // 隐藏客服
      hideKeFu:function(){
        this.kefuShow = false;
      },
      //头部导航
      getUrl:function(item,index){
          var pageId = item.pageId
          var that = this;
          this.topNav_index = index
          if(item.pageId == 0){
              var times = Date.parse(new Date())
              window.location.href='/shop/index/' + wid + '?date=' + times
          }
          this.$http.get("/shop/microPage/indexPage/"+ wid +'/'+ pageId).then(
              function (res) {
                  that.header=[],
                  that.lists=[],
                  that.footer={},
                  that.goodData=null,
                  that.bingbing=false,
                  that.bg_color='',
                  that.textList=[],
                  that.qq='',
                  that.url='',
                  that.telphone='',
                  that.kefuShow=false,
                  hstool.closeLoad();
                  that.qq = res.body.data.qq;
                  that.telphone = 'tel:' + res.body.data.telphone;
                  that.url = 'http://wpa.qq.com/msgrd?v=3&uin='+ res.body.data.qq +'&site=qq&menu=yes';
                  $('title').html(res.body.data.title);
                  this.showLink = res.body.data.isWebsite;
                  /**
                   * updata 2019-1-16 邓钊
                   * */
                  this.bg_color = res.body.data.bgcolor;
                  // if(this.showLink > 0){
                  //     this.bg_color = 'transparent';
                  // }else{
                  //     this.bg_color = res.body.data.bgcolor;
                  // }
                  // console.log(this.bg_color);
                  /**end**/
                  if(this.showLink == 1){
                      $('.showLink').removeClass('showLink');
                  }
                  if(res.body.data.container != ''){
                      console.log(res.body.data)
                      var content = JSON.parse(res.body.data.container);
                      if(content.length>0){
                          for(var i = 0;i<content.length;i++){
                              if(content[i]['type'] == 'model'){
                                  if(typeof(content[i].template_data) == 'string'){
                                      var model_data = JSON.parse(content[i].template_data);
                                  }else{
                                      var model_data = content[i].template_data;
                                  }
                                  var data = content.slice(0,i);
                                  data = data.concat(model_data);
                                  var content = content.splice(i+1,content.length);
                                  content = data.concat(content);
                              }
                          }
                      }
                  }
                  if(res.body.data.header != ''){
                      var header = JSON.parse(res.body.data.header);
                      console.log(header);
                      if(header.length>0){
                          for(var i = 0;i<header.length;i++){
                              if(header[i]['type'] == 'model'){
                                  if(typeof header[i].template_data == 'string'){
                                      var model_data = JSON.parse(header[i].template_data);
                                  }else{
                                      model_data = header[i].template_data;
                                  }
                                  var data = header.slice(0,i);
                                  data = data.concat(model_data);
                                  var new_header = header.splice(i+1,header.length);
                                  header = data.concat(new_header);
                              }
                              //add by 邓钊 2018-8-3 thumbnailFlag 控制图片导航的显示  0隐藏 1显示
                              if(header[i]['type'] == 'image_link'){
                                  var imgs = header[i].images
                                  for(var j = 0; j < imgs.length; j++){
                                      if(!imgs[j].thumbnail){
                                          imgs[j].thumbnailFlag = 0
                                      }else{
                                          imgs[j].thumbnailFlag = 1
                                      }
                                  }
                              }
                              //end
                          }
                      }
                      content = header.concat(content);
                      // console.log(content);
                  }
                  //解析组件
                  that.lists = tool.componentAssign(that,content);
                  that.$nextTick(function(){
                    $('.custom-richtext video').attr('width','100%');
                    $('.custom-richtext video').attr('height','auto');
                  })
                    // update 2018/7/12 华亢
                    // 如果商品分组是平铺，则其他组件都不显示
                    var arr = [];
                    for(var i=0,l=that.lists.length;i<l;i++){
                        if(that.lists[i]['type'] == 'good_group'){
                            if(that.lists[i]['group_pinpu'] == 1){
                                arr.push(that.lists[i]);
                                that.lists = arr;
                                break;
                            }
                        }
                    }
                    //主页切换导航栏 商品分组样式修改
                    var searchDom = setTimeout(function(){
                        if($('.custom-tag-list')){
                            if(arr.length){
                                // 平铺
                                var minHeight = $(window).height()+'px';
                                $('.custom-tag-list').css({height:minHeight})
                                
                            }else{
                                //不是平铺
                                var listHight = $('.custom-tag-list-menu-block').height();
                                $('.custom-tag-list').css({height:listHight+'px'})
                            }
                            clearTimeout(searchDom)
                        }
                    },0)
                  if(that.lists.length){
                    //如果最后一个组件是商品分组，去掉底部
                    if(this.lists[this.lists.length - 1]['type'] == 'good_group'){
                        $('footer').remove();
                        $('#container').css('paddingBottom','60px');
                    }
                  }
                  if(res.body.data.footer != '' && this.lists[0]['type'] != 'bingbing'){
                      var footer = JSON.parse(res.body.data.footer);
                      this.footer = footer;
                      for(var i =0;i< this.footer.menu.length;i++){
                          if(APP_HOST  + this.footer.menu[i]['linkUrl'].substr(1,this.footer.menu[i]['linkUrl'].length) == location.href){
                              footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['iconActive'] + ')',backgroundSize: '64px 50px'};
                          }else{
                              footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                          }
                          this.footer.menu[i].submenusShow = false;
                      }
                      console.log(footer);
                  }else{
                      this.footer = [];
                  }
              },function (res) {
                  // 处理失败的结果
              }
          );
         
      },
      //add by 韩瑜 2018-8-2 拆红包
      getBouns: function() {
        var that = this;
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         that.getBouns2(that);
        //     })
        //     return;
        // }
        that.getBouns2(that);
      },
      getBouns2:function(that){
      	var that = this;
      	this.$http.post('/shop/activity/bonusUnpack/'+wid,{_token:$('meta[name="csrf-token"]').attr('content')}).then(
      		function(res){
      			if(res.body.status == 1){
      				window.location.href = host + 'shop/activity/bonusDetail/' + wid
      			}
      		}
      	)
      },
      //关闭红包弹窗
      closeBouns:function(){
      	var that = this;
      	that.bonusShow_tip = true
	  	that.bonusShow = false
      	this.$http.post('/shop/activity/bonusClose/'+wid,{_token:$('meta[name="csrf-token"]').attr('content')}).then(
	      	function(res){
	  			console.log(res,'close')
	  			if(res.body.status == 1){
	  				that.bonusShow_tip = true
	  				that.bonusShow = false
	  			}
	  		}
	    )
      },
      //点击右下角红包图标
      showBonus:function(){
      	this.bonusShow_tip = false;
      	this.bonusShow = true;
      }
      //end
  },
  beforeCreate: function () {
    var that = this;
    this.$http.get("/shop/indexStore/"+ id).then(
        function (res) {
            hstool.closeLoad();
            // 处理成功的结果
            /** 
             * author 华亢 at 2018/8/27
             * toDO 为头部导航 染上绚烂的颜色
            */
           if(res.body.data.color_setting){
                that.topNav_color = JSON.parse(res.body.data.color_setting);
                that.checked_font_color = {
                    borderBottom:'2px solid '+ that.topNav_color.checked_font_color,
                    color:that.topNav_color.checked_font_color
                }
           }
           console.log(that.topNav_color,'this is twice')
            if(res.body.data.topNav){
                that.topNav_flag = true;
                that.topNav = JSON.parse(res.body.data.topNav);
                that.$nextTick(function () {
                    // DOM 现在更新了
                    // `this` 绑定到当前实例
                    var ulNav = this.$refs.topNav_ul.children
                    var numNav = 0
                    for(var i = 0; i < ulNav.length; i++ ){
                        console.log(ulNav[i].offsetWidth);
                        numNav += ulNav[i].offsetWidth
                    }
                    this.$refs.topNav_ul.style.width = numNav + 10 + 'px'
                })
            }
            that.qq = res.body.data.qq;
            that.telphone = 'tel:' + res.body.data.telphone;
            that.url = 'http://wpa.qq.com/msgrd?v=3&uin='+ res.body.data.qq +'&site=qq&menu=yes';
            $('title').html(res.body.data.title);
            this.showLink = res.body.data.isWebsite;
            /**
             * updata 2019-1-16 邓钊
             * */
            this.bg_color = res.body.data.bgcolor;
            // if(this.showLink > 0){
            //     this.bg_color = 'transparent';
            // }else{
            //     this.bg_color = res.body.data.bgcolor;
            // }
            // console.log(this.bg_color);
            /**end**/
            if(this.showLink == 1){
                $('.showLink').removeClass('showLink');
            }
            if(res.body.data.container != ''){
                var content = JSON.parse(res.body.data.container);
                console.log(res.body.data.container);
                if(content.length>0){
                    for(var i = 0;i<content.length;i++){
                        if(content[i]['type'] == 'model'){
                            if(typeof(content[i].template_data) == 'string'){
                                var model_data = JSON.parse(content[i].template_data);
                            }else{
                                var model_data = content[i].template_data;
                            }
                            var data = content.slice(0,i);
                                data = data.concat(model_data);
                            var content = content.splice(i+1,content.length);
                            content = data.concat(content);
                        }
                        if (content[i]['type'] == 'good_group') {
                        }
                    }
                }
            }
            if(res.body.data.header != ''){
                var header = JSON.parse(res.body.data.header);
                if(header.length>0){
                    for(var i = 0;i<header.length;i++){
                        if(header[i]['type'] == 'model'){
                            if(typeof header[i].template_data == 'string'){
                                var model_data = JSON.parse(header[i].template_data);
                            }else{
                                model_data = header[i].template_data;
                            }
                            var data = header.slice(0,i);
                                data = data.concat(model_data);
                            var new_header = header.splice(i+1,header.length);
                            header = data.concat(new_header);
                        }
                        //add by 邓钊 2018-8-3 thumbnailFlag 控制图片导航的显示  0隐藏 1显示
                        if(header[i]['type'] == 'image_link'){
                            var imgs = header[i].images
                            for(var j = 0; j < imgs.length; j++){
                                if(!imgs[j].thumbnail){
                                    imgs[j].thumbnailFlag = 0
                                }else{
                                    imgs[j].thumbnailFlag = 1
                                }
                            }
                        }
                        //end
                    }
                }
                content = header.concat(content);
                // console.log(content);
            }
            that.lists = tool.componentAssign(that,content);
            console.log(that.lists)
            that.$nextTick(function(){
              $('.custom-richtext video').attr('width','100%');
              $('.custom-richtext video').attr('height','auto');
            })
            // update 2018/7/12 华亢
            // 如果商品分组是平铺，则其他组件都不显示
            var arr = [];
            for(var i=0,l=that.lists.length;i<l;i++){
                if(that.lists[i]['type'] == 'good_group'){
                    if(that.lists[i]['group_pinpu'] == 1){
                        arr.push(that.lists[i]);
                        that.lists = arr;
                        break;
                    }
                }
            }
            //如果最后一个组件是商品分组，去掉底部
            if(this.lists[this.lists.length - 1]['type'] == 'good_group'){
               $('footer').remove();
               $('#container').css('paddingBottom','60px');
            }
            if(res.body.data.footer != '' && this.lists[0]['type'] != 'bingbing'){
                var footer = JSON.parse(res.body.data.footer);
                this.footer = footer;
                for(var i =0;i< this.footer.menu.length;i++){
                    if(APP_HOST + this.footer.menu[i]['linkUrl'].substr(1,this.footer.menu[i]['linkUrl'].length) == location.href){
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['iconActive'] + ')',backgroundSize: '64px 50px'};
                    }else{
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                    }
                    this.footer.menu[i].submenusShow = false;
                }
            }else{
                this.footer = [];
            }
            for(var i = 0; i < this.lists.length;i++){
                //add by 韩瑜 2018-11-29 商品分组模板监听滚动
                if(this.lists[i].type == "group_template"){
                	$('footer').remove();
                    this.groupList = this.lists[i]
                    $(window).scroll(function(){
				        var scrollH = document.documentElement.scrollHeight; //底部高度
				        var clientH = document.documentElement.clientHeight || document.body.clientHegiht; //屏幕高度
				        var topH = document.documentElement.scrollTop || document.body.scrollTop //页面被卷曲的高度
				        if (scrollH == topH + clientH){
				            //加载新数据
							$(".left_more").click();
							$(".top_more").click();
				        }
						if(topH > 35){
							$('.groupTop').addClass('groupTopFixed')
							$('.groupLeft').addClass('groupLeftFixed')
							$('.groupList').css('margin-top','40px')
						}else{
							$('.groupTop').removeClass('groupTopFixed')
							$('.groupLeft').removeClass('groupLeftFixed')
							$('.groupList').css('margin-top','0')
						}
				   });
                }
                //end
            }
        },function (res) {
        // 处理失败的结果
        }
    );
//  add by 韩瑜 2018-8-2 红包弹窗
    this.$http.get('/shop/activity/bonusShow/' + wid).then(
    	function (res) {
    		if(res.body.status == 1){
	    		that.activity_title = res.body.data.activity_title
	    		if(res.body.data.is_show == 0){
	    			that.bonusShow = true
	    		}else if(res.body.data.is_show == 1){
	    			that.bonusShow_tip = true;
	    			that.bonusShow = false;
	    		}else{
	    			that.bonusShow_tip = false;
	    			that.bonusShow = false;
	    		}
    		}
    	}
    );
  },
  mounted:function (){

  }
})

//生成随机字符串
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