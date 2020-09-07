// create by 赵彬 2018-8-7
Vue.component('deta-time',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
      
    },
    mounted: function(){
        
    },
    template: '<div class="control-group"><div class="detaTime_con">'+
                '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
                '<input type="date">'+
                '<span v-if="content.rule_time_type == 1">-</span>'+
                '<input type="date" v-if="content.rule_time_type == 1"">'+
                '</div></div>',
  });
  Vue.component('text-box',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: '<div class="control-group"><div class="detaTime_con">'+ 
                '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
                '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
                '<input type="text" v-if="content.rule_text_height == 0" class="detaTime_box">'+
                '<textarea v-if="content.rule_text_height == 1" class="text_textarea detaTime_box"></textarea>'+
                '</div></div>',
  })
  Vue.component('tel',{
    props: ['title','phone'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: "<div class='control-group'><div class='detaTime_con'>"+
        "<p class='detaTime_title'>{{title}}</p>"+
        "<p>{{phone}}</p>"+
    "</div></div>",
  })
  Vue.component('txtbooking',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: "<div class='control-group'><div class='detaTime_con'>"+
        "<p class='detaTime_title'><em v-if='content.required'>*</em>{{content.title}}</p>"+
        "<p class='detaTime_title_a' v-if='content.subtitle'>{{content.subtitle}}</p>"+
        "<!--下拉框-->"+
        "<div v-if='content.rule_appoint_type == 1'>"+
        "<select class='res_select'>"+
        "<option v-for='item in content.sub_rules'>{{item.title}}</option>"+
        "</select>"+
        "</div>"+
        "<!--单选框--><div class='choices' v-if='content.rule_appoint_type == 0 || content.rule_appoint_type == 2'>"+
            "<ul><li class='vote_option' v-for='(item,index) in content.sub_rules' >"+
                    "<input v-if='content.rule_appoint_type == 0' type='radio' :value='index' :name='content.id'>"+
                    "<input v-if='content.rule_appoint_type == 2' type='checkbox'>"+
                    "<span >{{item.title}}</span>"+
                "</li>"+
            "</ul></div></div></div>",
  })
  Vue.component('imgbooking',{
    props: ['content','imgurl'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: '<div class="control-group"><div class="detaTime_con">'+
        '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
        '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
        '<!--图片单列-->'+
        '<div class="choices choices_div">'+
            '<div class="gallery" v-for="item in content.sub_rules">'+
                '<div class="pic_wrapper">'+
                    '<div class="picture">'+
                        '<a class="cover" href="javascript:;">'+
                            '<img :src="imgurl+item.image" style="width: 100%">'+
                        '</a>'+
                        '<div class="opts">'+
                            '<div class="inner">'+
                                '<label class="w_radio">'+
                                    '<input type="radio" :name="content.id">'+
                                    '<span>选这个</span>'+
                                '</label>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<p class="caption" v-if="item.title"><span class="inner">{{item.title}}</span></p>'+
            '</div></div></div></div>',
  })
  Vue.component('upload',{
    props: ['content'],
    data: function () {
      return {
          showImg:false,
          imgSrc:''
      }
    },
    methods: {
        upload:function(){
            console.log(111)
            document.getElementById('fileInput').click()
        },
        fileSelected:function(e){
            var that = this;
            this.showImg = true;
            var reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]); 
            reader.onload = function(e){ 
                console.log(that.showImg)
                that.imgSrc = this.result
              //  that.parent().prev().attr('src',this.result);
            }
        },
        deleteImg:function(){
            this.showImg = false
        }
    },
    mounted: function(){
        
    },
    template: '<div class="control-group">'+
                '<div class="detaTime_con">'+
                    '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
                    '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
                    '<div v-if="!showImg" class="fileinput-button">'+
                        '<a class="fileinput-button-icon" href="javascript:void(0);" @click="upload()">+</a>'+
                        '<input id="fileInput"  type="file" style="display:none" @change="fileSelected(event)"/>'+
                    '</div>'+
                    '<div class="imgBox" v-if="showImg">'+
                        '<img :src="imgSrc" alt="" style="maxWidth:100%;height:auto">'+
                        '<span class="delete" @click="deleteImg()">×</span>'+
                    '</div>'+
                    
                '</div>'+
            '</div>',
  })
  Vue.component('separator-line',{
    props: ['content'],
    data: function () {
      return {
          separatorLine:''
      }
    },
    methods: {

    },
    mounted: function(){
        if(this.content.rule_line_idx == 0){
            this.separatorLine = 'dotted'
        }else if(this.content.rule_line_idx == 1){
            this.separatorLine = 'dashed'
        }else if(this.content.rule_line_idx == 2){
            this.separatorLine = 'solid'
        }else if(this.content.rule_line_idx == 3){
            this.separatorLine = 'double'
        }
    },
    template: '<div class="control-group"><div class="detaTime_con">'+
        '<p class="detaTime_title" :class="[{left:content.rule_title_idx == 0,center:content.rule_title_idx == 1,right:content.rule_title_idx == 2},separatorLine]">{{content.title}}</p>'+
        '<div style="width: 100%;" class="detaTime_title_a line-subtitle" :class="{left:content.rule_desc_idx == 0,center:content.rule_desc_idx == 1,right:content.rule_desc_idx == 2}" v-if="content.subtitle">{{content.subtitle}}</div>'+
        '</div></div>',
  })
  Vue.component('timebooking',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: "<div class='control-group'><div class='detaTime_con time_box'>"+
    "<p class='detaTime_title'><em v-if='content.required'>*</em>{{content.title}}</p>"+
    "<p class='detaTime_title_a' v-if='content.subtitle'>{{content.subtitle}}</p>"+
    "<!--下拉框-->"+
    "<div class='drop_box' v-if='content.rule_appoint_type == 1'>"+
        "<select class='res_select'>"+
        "<option v-for='item in content.sub_rules'>{{item.title}}</option>"+
        "</select>"+
    "</div>"+
    "<!--单选框--><div class='choices' v-if='content.rule_appoint_type == 0 || content.rule_appoint_type == 2'>"+
        "<ul style='overflow:hidden'><li class='vote_option' v-for='(item,index) in content.sub_rules' v-if='index != 0' style='float:left;width:50%'>"+
                "<input v-if='content.rule_appoint_type == 0' type='radio' :value='index' :name='content.id'>"+
                "<input v-if='content.rule_appoint_type == 2' type='checkbox'>"+
                "<span >{{item.title}}</span>"+
            "</li>"+
        "</ul></div></div></div>",
  })
  Vue.component('num',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: '<div class="control-group"><div class="detaTime_con">'+
        '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
        '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
        '<div class="detaTime_box">'+
            '<input type="text">'+
            '<span class="detaTime_unit">{{content.unit}}</span>'+
        '</div></div></div>',
  })
  Vue.component('face-type',{
    props: ['content'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: '<div class="control-group" :style="{backgroundColor:content.bg_color}">'+
    '<div class="detaTime_con center">'+
        '<p class="detaTime_title" style="color: rgb(192, 192, 192);font-size: 24px">{{content.title}}</p>'+
        '<p class="detaTime_title_a" style="color: rgb(192, 192, 192)" v-if="content.subtitle">{{content.subtitle}}</p>'+
    '</div></div>',
  })
  Vue.component('img-set',{
    props: ['content','imgurl'],
    data: function () {
      return {
      }
    },
    methods: {
    },
    mounted: function(){
        
    },
    template: '<div class="control-group" :style="{backgroundColor:content.bgColor}">'+
    '<div class="detaTime_con center img_box">'+
        '<img width="100%" height="auto" :src="imgurl+content.sub_rules[0].image" v-if="content.sub_rules[0].rule_image_flag">'+
        '<p class="detaTime_title" style="font-size: 24px;" v-else>{{content.title}}</p>'+
    '</div></div>',
  })
  Vue.component('text-vote',{
      props:['content'],
      data:function(){
          return {
            other:'other',
            option:'option'
          }
      },
      methods:{

      },
      template:'<div class="control-group">'+
                '<div class="detaTime_con">'+
                    '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
                    '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
                    '<div class="choices">'+
                        '<ul>'+
                            '<li class="vote_option" v-for="(item,index) in content.sub_rules">'+
                                '<input v-show="item.type==option" v-if="content.multiple == 0" type="radio" :name="content.id">'+
                                '<input v-show="item.type==option" v-if="content.multiple == 1" type="checkbox">'+
                                '<span v-show="item.type==option">{{item.title}}</span>'+
                                '<span v-if="item.type==other">其他:</span>'+
                                '<input v-if="item.type==other" class="option_qita" type="text">'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+
                '</div>'+
            '</div>'
  })
  Vue.component('img-vote',{
      props:['content','imgurl'],
      data:function(){
          return {}
      },
      template:'<div class="control-group"><div class="detaTime_con">'+
          '<p class="detaTime_title"><em v-if="content.required">*</em>{{content.title}}</p>'+
          '<p class="detaTime_title_a" v-if="content.subtitle">{{content.subtitle}}</p>'+
          '<!--图片单列-->'+
          '<div class="choices choices_div" v-if="content.rule_image_type == 0">'+
              '<div class="gallery" v-for="item in content.sub_rules"><div class="pic_wrapper">'+
                      '<div class="picture">'+
                          '<a class="cover" href="javascript:;">'+
                              '<img :src="imgurl + item.image" style="width: 100%">'+
                          '</a>'+
                          '<div class="opts">'+
                              '<div class="inner">'+
                                  '<label class="w_radio">'+
                                      '<input v-if="content.multiple == 0" type="radio" :name="content.id">'+
                                      '<input v-if="content.multiple == 1" type="checkbox">'+
                                      '<span>选这个</span>'+
                                  '</label>'+
                              '</div>'+
                          '</div>'+
                      '</div>'+
                  '</div>'+
                  '<p class="caption" v-if="item.title">'+
                      '<span class="inner">{{item.title}}</span>'+
                  '</p>'+
              '</div>'+
          '</div>'+
          '<!--图片多列-->'+
          '<div class="choices choices_div" v-if="content.rule_image_type == 1">'+
              '<div class="mini gallery" v-for="item in content.sub_rules">'+
                  '<div class="pic_wrapper">'+
                      '<div class="picture">'+
                          '<a class="cover" href="javascript:;">'+
                              '<img :src="imgurl + item.image" style="width: 100%">'+
                          '</a>'+
                          '<div class="opts">'+
                              '<div class="inner">'+
                                  '<label class="w_radio">'+
                                      '<input v-if="content.multiple == 0" type="radio" :name="content.id">'+
                                      '<input v-if="content.multiple == 1" type="checkbox">'+
                                      '<span>选这个</span>'+
                                  '</label>'+
                              '</div>'+
                          '</div>'+
                      '</div>'+
                  '</div>'+
                  '<p class="caption" v-if="item.title">'+
                      '<span class="inner">{{item.title}}</span>'+
                  '</p>'+
              '</div>'+
          '</div>'+
      '</div>'+
  '</div>'
  })