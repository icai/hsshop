hstool.load();
var key = [];
var j = 0;
var k = 0;
var iarr = [];
var n = 0;
Vue.filter("substr", function(value) {
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
    distribute:1,//是否显示分销客
    data_toggleWealth: false,//财富眼默认未开启
    wealth_info: '打开就知道哪里财富赚多多！',
    homeModule:[],
    Newcard:0, //新会员卡标识
    isBind:isBind, // 是否绑定手机号0不需要1需要
    takeAwayConfig:takeAwayConfig   //外卖店铺
  },
    methods: {
      //登录操作弹窗绑定手机号弹窗
      bindMobile:function(){
        var that = this;
        if(isBind){
            tool.bingMobile(function(){
                that.isBind = 0;
            })
        }
      },
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
      toggleWealth: function(){
        if(is_overdue == 1){
            tool.tip('店铺已打烊，无法操作');
            return false;
        }
        if(this.data_toggleWealth){//开启财富眼

            this.$http.get('/shop/member/isOpenWeath').then(function(success){
                console.log(success)
                if(success.body.status == 1){
                    this.wealth_info ="快去分享奖励丰厚的商品吧！";
                }
                tool.tip(success.body.info)
            })
        }else{//关闭财富眼

            this.$http.get('/shop/member/isOpenWeath').then(function(success){
                if(success.body.status == 1){
                    this.wealth_info ="打开就知道哪里财富赚多多！";
                }
                tool.tip(success.body.info)
            })
        }
     }
  },
  mounted: function(){
     if(is_open){
        this.data_toggleWealth = true;
        this.wealth_info = "快去分享奖励丰厚的商品吧！";
    }else{
        this.data_toggleWealth = false;
        this.wealth_info ="打开就知道哪里财富赚多多！";
    }
    if(takeAwayConfig == 1){
        
    }
  },
  beforeCreate: function () {
    var that = this;
    this.$http.get("/shop/member/indexHome/"+ id).then(
        function (res) {
        	  hstool.closeLoad();
            // 处理成功的结果
            console.log(JSON.parse(res.body.data.homeModule));
            this.homeModule = JSON.parse(res.body.data.homeModule)
            $('title').html(res.body.data.title);
            if(res.body.data.container != ''){
                var content = JSON.parse(res.body.data.container);
                console.log(content)
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
                if(header.length>0){
                    for(var i = 0;i<header.length;i++){
                        if(header[i]['type'] == 'model'){
                            if(typeof(header[i].template_data) == 'string'){
                                var model_data = JSON.parse(header[i].template_data);
                            }else{
                                var model_data = header[i].template_data;
                            }
                            var data = header.slice(0,i);
                                data = data.concat(model_data);
                            var header = header.splice(i+1,header.length);
                            header = data.concat(header);
                        }
                    }
                }
                content = header.concat(content);
            }
            for(var i in content){
                // 会员主页
                if(content[i]['type'] == 'member'){
                    content[i]['thumbnail'] = imgUrl + content[i]['thumbnail']
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
                if(content[i]['type'] == 'goods'){
                    if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                        content[i]['btnStyle'] = '0';
                    }
                    // 判断商品名显示
                    if(content[i]['goodName'] || content[i]['listStyle'] == 4){
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
                    if(content[i]['goodInfo'] || content[i]['listStyle'] == 4){
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
                            content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail']
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
                if(content[i]['type'] == 'spell_goods'){
                    if(content[i]['groups'].length){
                        for(var j = 0;j<content[i]['groups'].length;j++){
                            if(content[i]['groups'][j]['member'] == undefined){
                                content[i]['groups'][j]['member'] = [];
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
                                    content[i]['top_nav'][z]['goods'][j]['price'] = '￥' + content[i]['top_nav'][z]['goods'][j]['price'];
                                }
                            }
                        }
                        
                    }
                    if(content[i]['left_nav'].length > 0){
                        for(var z = 0;z<content[i]['left_nav'].length;z++){
                            content[i]['left_nav'][z]['href'] = 'top_nav_'+ randomString(12);
                            content[i]['left_nav'][z]['isActive'] =  false;
                            if(z == 0){
                                content[i]['left_nav'][z]['isActive'] =  true;
                            }
                            if(content[i]['group_type'] == 1 && content[i]['left_nav'][z]['goods'].length>0){
                                for(var j = 0;j<content[i]['left_nav'][z]['goods'].length;j++){
                                    content[i]['left_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['left_nav'][z]['goods'][j]['thumbnail'];
                                    content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                }
                            }
                        }
                        
                    }
                }
                this.lists.push(content[i]);
                if(content[i]['type'] == 'image_ad'){
                    //console.log(content[i])
                    if(content[i].images.length>0){
                        for(var j = 0;j<content[i].images.length;j++){
                            content[i].images[j]['FileInfo']['path'] = imgUrl + content[i].images[j]['FileInfo']['path'];
                        }
                    }
                    if(content[i]['advsListStyle'] == 2){
                        setTimeout(function(){
                            var swiper = new Swiper('.swiper-container', {
                                pagination: '.swiper-pagination',
                                paginationClickable: true,
                                autoplay:2000,
                                loop:true
                            });
                        },1000)
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
            console.log(this.footer.menu)
            // console.log(this.lists[0]['type']);
        },function (res) {
        // 处理失败的结果
        }
    );
    // add by zhaobin 2018-9-12
    // 是否有新会员卡标识
    this.$http.get('/shop/member/newMemberCard').then(
        function(res){
            // console.log(res.body.data.is_new)
            this.Newcard = res.body.data.is_new
        },function(res){

        }
    )
    // end
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