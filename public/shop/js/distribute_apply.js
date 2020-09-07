// 分割线
var SerLine = {
    props:['list'],
    data:function(){
        return {
            separatorLine:'',
            LineType:'text-left',
            subType:'text-left'
        }
    },
    created:function(){
        switch(this.list.rule_title_idx){
            case 0 :this.LineType = 'text-left';break;
            case 1 :this.LineType = 'text-center';break;
            case 2 :this.LineType = 'text-right'; break; 
        }
        switch(this.list.rule_line_idx){
            case 0 :this.separatorLine = 'dotted';break;
            case 1 :this.separatorLine = 'dashed';break;
            case 2 :this.separatorLine = 'solid';break; 
            case 3 :this.separatorLine = 'double';break; 
            default :this.separatorLine = '';break; 
        }
        switch(this.list.rule_desc_idx){
            case 0 :this.subType = 'text-left';break;
            case 1 :this.subType = 'text-center';break;
            case 2 :this.subType = 'text-right'; break; 
        }

    },
    template:'<div class="line">'+
                '<p :class="[LineType,separatorLine,\'line_title\']">{{list.title}}</p>'+
                '<p :class="[\'line_subtitle\',subType]">{{list.subtitle}}</p>'+
             '</div>'
}
// 魔方
var Cube = {
    props:["list","wid"],
    data: function () {
        return {
            screenWidth:0,
            height:0,
        }
    },
    created:function(){
        var list = this.list;
        this.screenWidth = $(window).width() > 540 ? 540-20 : $(window).width()-20;
        var screenWidth = this.screenWidth;
        if (list.margin == undefined) {
            list.margin = 0;
        }
        //魔方各例定位，宽高数据处理
        for( var i = 0;i < list.position.length;i ++ ){
            if( list.telType == 0 || list.telType == 1 || list.telType == 2 || list.telType == 7 ){//魔方特例
                this.height = (screenWidth-(list.position.length-1)*list.margin)/list.position.length*list.aspectRatio + 'px';
                list.position[i].top    = 0;
                list.position[i].left   = (list.position[i].left == 0 ? 0 : (list.position[i].left*(screenWidth-(list.position.length-1)*list.margin)/list.position.length+list.position[i].left*list.margin)) + 'px';
                list.position[i].width  = (screenWidth-(list.position.length-1)*list.margin)/list.position.length + 'px';
                list.position[i].height = (screenWidth-(list.position.length-1)*list.margin)/list.position.length*list.aspectRatio + 'px';
            }else{//魔方普通
                this.height = screenWidth + 'px';
                list.position[i].top    = (list.position[i].top == 0 ? 0 : list.position[i].top*(screenWidth-list.margin)/4+list.position[i].top/2*list.margin) +'px';
                list.position[i].left   = (list.position[i].left == 0 ? 0 : list.position[i].left*(screenWidth-list.margin)/4+list.position[i].left/2*list.margin) +'px';
                list.position[i].width  = (list.position[i].width == 4 ? screenWidth : list.position[i].width == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
                list.position[i].height = (list.position[i].height == 4 ? screenWidth : list.position[i].height == 2 ? (screenWidth-list.margin)/4*2 : (screenWidth-list.margin)/4-list.margin/2) +'px';
            }
        }
    },
    template:   '<div class="cube">'+
                    '<div class="row" :style="{height:height}">'+
                        '<div class="cube-row" v-for="(list1,index1) in list.position" :style="{top:list1.top,left: list1.left,width: list1.width,height: list1.height}">'+
                            '<a href="javascript:void(0);" :data-src="imgUrl + list.content[index1].img" :style="{backgroundImage: \'url(\'+imgUrl + list.content[index1].img+\')\'}" :class="{\'J_parseImg\':list.content[index1].type == 0 && (list.resize_image==undefined || (list.resize_image!=undefined && list.resize_image ==1))}"></a>'+
                            '<div class="cube-title" v-if="list.addTitle && list.content[index1].title">{{list.content[index1].title}}</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'
}
var Notice =  {
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
}
// 联系方式组件
var Cmobile = {
    props:["list"],
    data:function(){
        return {
            tel:'tel:',
            decor:'#mp.weixin.qq.com'
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
                <p class="mobile-title">联系方式</p>\
                <div>\
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
                <div v-if="!list.mobileStyle">\
                <div class="mobile-content" v-for="(item,index) in list.lists">\
                <a :href="tel+item.area_code+item.mobile"><img :src="list.icon" class="mobile-icon"/><span class="calling">{{item.area_code?item.area_code+"-":""}}{{item.mobile}}</span>\
                </a>\
                </div>\
                </div>\
            </div>'
}
var app = new Vue({
    el:'#container',
    data:{
        lists:[],
        list1:data,
        host:host,
        _host:_host,
        imgUrl:imgUrl,
        bg_color:data.bg_color,
        wid:wid,
        showModel:false,
        modelInfo:'',
        isBind:isBind,
        applySuccess:false,
        isDistribute:isDistribute
    },
    created:function(){
        this.lists = JSON.parse(data.template_info);
    },
    components:{
        'serline'  :  SerLine,
        'cube'     :  Cube,
        'notice'   :  Notice,
        'cmobile'  :  Cmobile
    },
    methods:{
        // 提交申请
        applyDistribue:function(){
            var that = this;
            if(isBind){
                tool.bingMobile(function(){
                    applyAjax(that)
                })
            }else{
                applyAjax(that)
            }
            
        },
        goMymoney:function(){
            window.location.href = "/shop/distribute/wealth"
        }
    }
})
function applyAjax(that){
    $.ajax({
        url:'/shop/distribute/apply?id='+that.list1.id,
        type:'POST',
        data:{},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(res){
            console.log(res)
            that.modelInfo = res.info;
            that.showModel = true
            that.applySuccess = res.status == 1 ? true : false;
            setTimeout(() => {
                window.location.href = '/shop/index/' + that.wid
            }, 2000);
        }
    })
}