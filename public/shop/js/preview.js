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
    showLink:1,
    bg_color:'',
    textList:[],
    host:host,
    _host:_host,
    imgUrl:imgUrl,
    videoUrl:videoUrl,
    wid:wid
  },
  methods: {
      showSub:function(menu,index){
        for(var i=0;i<this.footer.menu.length;i++){
            if(i != index){
                this.footer.menu[i].submenusShow = false;
            }
        }
        if(menu.submenus.length>0){
            // menu.submenusShow = menu.submenusShow ? false:true;
            if(menu.submenusShow){
                menu.submenusShow = false;
            }else{
                menu.submenusShow = true;
            }
        }
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
      }
  },
  
  beforeCreate: function () {
    var that = this;
    this.$http.get("/shop/page/previewPage/"+ wid +'/'+ id).then(
        function (res) {
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
            console.log(res);
            // 处理成功的结果
            if(res.body.data.container != ''){
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
            if(res.body.data.footer != '' && this.lists[0]['type'] != 'bingbing'){
                var footer = JSON.parse(res.body.data.footer);
                this.footer = footer;
                for(var i =0;i< this.footer.menu.length;i++){
                    if(APP_HOST  + this.footer.menu[i]['linkUrl'].substr(1,this.footer.menu[i]['linkUrl'].length) == location.href){
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['iconActive'] + ')',backgroundSize: '64px 50px'};
                    }else{
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                    }
                }
            }else{
                this.footer = [];
            }
            // console.log(this.lists[0]['type']);
        },function (res) {
        // 处理失败的结果
        }
    );
  },
  created: function () {
    // `this` 指向 vm 实例
    console.log(2);
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
// 点击图片查看大图 add by 黄新琴 2018/9/3
$('body').on('click','.J_parseImg',function(){
    var nowImgurl = $(this).data('src');
    wx.previewImage({
        "urls":[nowImgurl],
        "current":nowImgurl
    });
});
/*
* @auther 黄新琴
* @desc 富文本图片点击放大
* @date 2018-10-18
* */
$('body').on('click','.js-custom-richtext',function(){
    var imgs = [];
    var imgObj = $(this).find('img');
    for(var i=0; i<imgObj.length; i++){
        imgs.push(imgObj.eq(i).attr('src'));
        imgObj.eq(i).click(function(){
            var nowImgurl = $(this).attr('src');
            wx.previewImage({
                "urls":imgs,
                "current":nowImgurl
            });
        });
    }
});