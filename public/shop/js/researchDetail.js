// step1 写着写着我写懵了 所以我打算动态添加组件
var PageContentArr = []
for(var i in data.rules){
    PageContentArr.push(data.rules[i])
}
//设计组件
PageContentArr.map(function(v,i){
    // case type => 组件名字
    switch(v.type){
        case 'text'         :v.pageType='Version'       ;break; //文本
        case 'rich_text'    :v.pageType='RichVersion'   ;break; //富文本
        case 'time'         :v.pageType='Timer'         ;break; //时间
        case 'phone'        :v.pageType='CallPhone'     ;break; //手机号
        case 'email'        :v.pageType='Email'         ;break; //email
        case 'image'        :v.pageType='SetImage'      ;break;//上传图片
        case 'address'      :v.pageType='SetAddress'    ;break;//地址
        case 'vote_text'    :v.pageType='SetVoteVersion';break;//文本投票
        case 'vote_image'   :v.pageType='SetVoteImage'  ;break;//图片投票
        case 'appoint_text' :v.pageType='AppointVersion';break;//文本预约
        case 'appoint_image':v.pageType='AppointImage'  ;break;//图片预约
        case 'line'         :v.pageType='SerLine'       ;break;//分割线
        case 'face_type'    :v.pageType='FaceType'      ;break;//外观样式
        case 'appoint_time' :v.pageType='AppointTime'   ;break;//预约时段
        case 'set_image'    :v.pageType='ImageSet'      ;break;//图片设置
        case 'num'          :v.pageType='SetNum'        ;break;//数字
        default             :v.pageType='default'
    }
    return v
})

!function(window,stock){
    var k = '';
    var requstData  = {};
    for(var i = 0,l = stock.length;i<l;i++){
        k = stock[i].id;
        requstData[k] = {};
        switch(stock[i].type){
            case 'time'://时间
            requstData[k].rule_time_type=stock[i].rule_time_type,
            requstData[k].start_time ='';
            if(requstData[k].rule_time_type == 1){
                requstData[k].end_time ='';
            };
            break;
            case 'text'://文本
            case 'num':
            requstData[k].val='';
            break;
            case 'appoint_time':
            case 'appoint_text':
            case 'appoint_image':
            case 'vote_text'://文本投票
            case 'vote_image'://图片投票
            requstData[k].min_options = stock[i].min_options
            requstData[k].max_options = stock[i].max_options
            requstData[k].option = []
            requstData[k].multiple = stock[i].multiple
            break;
            case 'address'://地域调查
            requstData[k].region = [];
            break;
            case 'image'://设置图片
            requstData[k].url = '';
            break;
        }
        requstData[k].type=stock[i].type;
        requstData[k].required = stock[i].required
    }
    var dd = {};
    for(var j in requstData){
        if(
            requstData[j].type != 'line' 
            && requstData[j].type != 'phone'
            && requstData[j].type != 'set_image'
            && requstData[j].type != 'face_type'
        ){
            dd[j] = requstData[j]
        }
    }
    window.requstData = dd;
}(window,PageContentArr)


//文本 done
var Version = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    methods:{
        sendMSG:function(e){
            this.$emit('ievent',this.content.id,e.target.value)
        }
    },
    template:'<div class="text" style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
                '<div class="res_title">'+
                '{{content.title}}<span class="res_tip_bitian" v-if="required">(必填)</span>'+
                '</div>'+
                '<p class="subtitle" v-if="content.subtitle">{{content.subtitle}}</p>'+
                '<textarea v-if="content.rule_text_height==1" @input="sendMSG($event)"></textarea>'+
                '<input type="text" v-if="content.rule_text_height==0" @input="sendMSG($event)"/>'+
            '</div>',
}

//时间 done
var Timer = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    methods:{
        sendTime:function(e){
            if(this.content.rule_time_type == 1){
                this.$emit('ievent',this.content.id,'endTime','startTime');
            }else{
                this.$emit('ievent',this.content.id,e.target.name);
            }
        }
    },
    computed:{
        sure:function(){
            if(this.content.rule_time_type == 0){
                return 'single'
            }else{
                return 'double'
            }
        }
    },
    created:function(){
        
    },
    mounted:function() {
        var dateS = new Date()
        new Mdate(this.content.id+'TimeStart',{
            beginYear: dateS.getFullYear(),
            beginMonth: dateS.getMonth()+1,
            beginDay: dateS.getDate(),
            endYear: dateS.getFullYear()+1,
            endMonth: "12",
            endDay: "31",
            format: "-"
        })
        if(this.content.rule_time_type == 1){
            var date = new Date()
            new Mdate(this.content.id+'TimeEnd',{
                beginYear: date.getFullYear(),
                beginMonth: date.getMonth()+1,
                beginDay: date.getDate(),
                endYear: date.getFullYear()+1,
                endMonth: "12",
                endDay: "31",
                format: "-"
            })
        }
    },
    template:`
    <div class='dateTime' :id="content.id" style="0 0.3rem 0.4rem 0.3rem">
            <div class='res_title'>
            {{content.title}}<span class='res_tip_bitian' v-if="required">(必选)</span></div>
			<div class='date_box'>
				<div class='date_inp'>
					<input :id="content.id+'TimeStart'" type="botton" name="startTime" :class="sure"  @click.prevent="sendTime($event)" readonly="readonly"/>
					<span></span>
				</div>
				<div class='fengefu' v-if="content.rule_time_type == 1"></div>
				<div class="date_inp" v-if="content.rule_time_type == 1">
					<input type="botton" name="endTime" class="double" :id="content.id+'TimeEnd'" @click.prevent="sendTime($event)" readonly="readonly"/>
					<span></span>
				</div>
			</div>
		</div>`,
}

//手机号 done
var CallPhone = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    template:'<div class="phone" style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
                '<div class="res_title">'+
                '<span class="res_tip_bitian" v-if="required">*</span>{{content.title}}'+
                '</div>'+
                "<div class='phone_box'>"+
                    '<a :href="\'tel:\'+content.rule_phone_value">{{content.rule_phone_value}}</a>'+
                '</div>'+
            '</div>'
}

//上传图片 done
//1. 图片预览
//2. 图片上传
var SetImage = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            fileName:'file'+this.content.id,
            defaultPIC:source+'shop/images/uploadPic.png',
            tipAboutPIC:'选择图片'
        }
    },
    methods:{
        changePIC:function(){
            $('#'+this.fileName).click()
            
        },
        hasChanged:function(){
            hstool.load();
            var _this = this;
            var file = $('#'+this.fileName).prop('files')[0];
            if(file){
                if (/image/.test(file.type)){
                    var data = new FormData();
                    data.append('file',file);
                    this.upLoadPic(data);
                }else{
                    alert("You must select a valid image file!");
                }
            }
        },
        upLoadPic:function(data){
            var _this = this;
            $.ajax({
                url:'/shop/order/upfile/'+wid,
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData : false,
                contentType : false,
                async:false,
                data:data,
                success:function(res){
                    var req = JSON.parse(res);
                    _this.defaultPIC = source+req.data.path;
                    hstool.closeLoad();
                    if(req.status == 1){
                        _this.$emit('ievent',_this.content.id,req.data.path)
                    }else{
                        alert('上传失败')
                    }
                }
            })
        }
    },
    template:'<div class="SetImage" style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
        '<div class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">(必填)</span>'+
        '</div>'+
        '<p class="subtitle">{{content.subtitle}}</p>'+
        '<a href="javasrcipt:;" class="logoChange" @click="changePIC">'+
            '<img :src="defaultPIC" alt="Image preview" class="uploadPicture"/>'+
        '</a>'+
        '<input type="file" :id="fileName" name="file" style="display:none" accept="image/*" @change="hasChanged"/>'+
    '</div>'
}

//地址 done
$(function () {
    var $distpicker = $('#distpicker');
    $("#distpicker2").distpicker({
        autoSelect: false
      });
  });

var SetAddress = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    methods:{
        sendMSG:function(e){
            var type = e.target._prevClass;
            if(type.match(/dist/g)){
                this.$emit('ievent',this.content.id,'dist',e.target.value)
            }else{
                this.$emit('ievent',this.content.id,'dist','')
                if(type.match(/city/g)){
                    this.$emit('ievent',this.content.id,'city',e.target.value)
                }else{
                    this.$emit('ievent',this.content.id,'city','')
                    if(type.match(/provice/g)){
                        this.$emit('ievent',this.content.id,'provice',e.target.value)
                    }
                }
            }
        }
        
    },
    created:function(){
        
    },
    mounted:function() {
        var $distpicker = $('#distpicker');
        $(".distpicker").distpicker({
            autoSelect: false
        });
    },
    template:'<div class=\'address\' v-cloak style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
        '<p class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">(必填)</span>'+
        '</p>'+
        '<p class="subtitle" v-if="content.subtitle">{{content.subtitle}}</p>'+
        '<div class="distpicker">'+
            '<div class="form-group">'+
                '<select class="form-control provice" @change="sendMSG($event)"></select>'+
            '</div>'+
            '<div class="form-group">'+
                '<select class="form-control city" @change="sendMSG($event)"></select>'+
            '</div>'+
            '<div class="form-group">'+
                '<select class="form-control dist" @change="sendMSG($event)"></select>'+
            '</div>'+
        '</div>'+
    '</div>'
}

//文本投票 done
var SetVoteVersion = {
    props:['content','req'],
    data:function(){
        return {
            required:this.content.required,
            option:[],
            max_options:'',
            min_options:''
        }
    },
    methods:{
        getResult:function(e,t){
            switch(t){
                case 'radio': 
                this.option=[{"id":e.target.value}]
                ;break;
                case 'checkbox':
                var a = $('input[name="'+this.content.id+'"]:checked');
                if(a.length>this.max_options){
                    layer.msg('只能选择'+this.max_options+'个选项哦')
                    $('input[value="'+e.target.value+'"]').prop("checked", false)
                    return
                }
                this.checkSelArr({"id":e.target.value});
                break;
            }
            this.$emit('ievent',this.content.id,this.option)
        },
        checkSelArr:function(selected){
            var len=this.option.length;
            for(var i=0;i<len;i++){
               if(selected.id == this.option[i].id){
                    this.option.splice(i,1);
                    return;//利用函数的返回功能中断push操作
               }
            }
            this.option.push(selected)
        }
    },
    computed:{
        inputType:function(){
            switch(this.content.multiple){
                case 0:return 'radio';break;
                case 1:return 'checkbox';break
            }
        },
    },
    created:function(){
        this.max_options = this.content.max_options;
        this.min_options = this.content.min_options;
    },
    mounted:function() {

    },
    template:"<div class='set_vote_text'>"+
        '<p class="res_title">'+
            '{{content.title}}<span class="res_tip_bitian" v-if="required">(必填)</span>'+
        '</p>'+
        '<p class="subtitle">{{content.subtitle}}</p>'+
        "<div class='appoint_box'>"+
            '<ul>'+
                '<li v-for="(item,idx) in content.sub_rules" :key="idx">'+
                    '<input :type="inputType" :id="item.id" :name="content.id" :value="item.id" @click="getResult($event,inputType)"/>'+
                    '<label :for="item.id">{{item.title}}</label>'+
                '</li>'+
            '</ul>'+
        '</div>'+
    '</div>'
}

//图片投票
var SetVoteImage = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            host:host,
            option:[],
            max_options:'',
            min_options:''
        }
    },
    methods:{
        getResult:function(e,t){
            switch(t){
                case 'radio': 
                this.option=[{id:e.target.value}]
                ;break;
                case 'checkbox':
                var a = $('input[name="'+this.content.id+'"]:checked');
                if(a.length>this.max_options){
                    layer.msg('只能选择'+this.max_options+'个选项哦')
                    $('input[value="'+e.target.value+'"]').prop("checked", false)
                    return
                }
                this.checkSelArr({"id":e.target.value});
                break;
            }
            this.$emit('ievent',this.content.id,this.option)
        },
        checkSelArr:function(selected){
            var len=this.option.length;
            for(var i=0;i<len;i++){
               if(selected.id == this.option[i].id){
                    this.option.splice(i,1);
                    return;//利用函数的返回功能中断push操作
               }
            }
            this.option.push(selected)
        }
    },
    created:function(){
        this.max_options = this.content.max_options;
        this.min_options = this.content.min_options;
        
    },
    computed:{
        inputType:function(){
            switch(this.content.multiple){
                case 0:return 'radio';break;
                case 1:return 'checkbox';break
            }
        },
        isImageType:function(){
            if(this.content.rule_image_type == 1){
                return true
            }
        },
        HeightEql:function(){
            if(this.content.rule_image_type == 1){
                return true
            }
        }
    },
    template:"<div class='set_vote_image'>"+
        '<div class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">(必选)</span>'+
        '</div>'+
        '<p class="subtitle">{{content.subtitle}}</p>'+
        '<div :class="{image_option:true,\'flex-box\':isImageType}">'+
            '<div v-for="(item,idx) in content.sub_rules" :key="idx" :class="{\'image-height-box\':HeightEql}">'+
                '<label>'+
                    '<div class="image_box">'+
                        '<img :src="source+item.image" />'+
                        '<p>'+
                        '<input :type="inputType" :name="content.id" :value="item.id" @click="getResult($event,inputType)">'+
                        '<b>{{item.title}}</b></p>'+
                    '</div>'+
                '</label>'+
            '</div>'+
        '</div>'+
    '</div>'
}

/* <strong>
    <input :type="inputType" :name="content.id" :value="item.id" @click="getResult($event,inputType)">
    <span>选这个</span>
</strong> */

//文本预约 done
var AppointVersion = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            option:[]
        }
    },
    methods:{
        getResult:function(e,t){
            switch(t){
                case 'select':
                case 'radio': 
                this.option=[{id:e.target.value}]
                ;break;
                case 'checkbox': 
                this.checkSelArr({"id":e.target.value});
                break;
            }
            this.$emit('ievent',this.content.id,this.option)
        },
        checkSelArr:function(selected){ 
            var len=this.option.length;
            for(var i=0;i<len;i++){
               if(selected.id == this.option[i].id){
                    this.option.splice(i,1);
                    return;//利用函数的返回功能中断push操作
               }
            };
            this.option.push(selected)
         }
    },
    created:function(){
        //下拉框的默认值  
        if(this.content.rule_appoint_type == 1){
            // that记录当前this对象，解决this指向的问题 add by 魏冬冬 2019-09-17
            var that = this;
            var defaultVal = this.content.sub_rules.filter(function(v){return v.title == that.content.rule_appoint_default});
            this.option = defaultVal[0]?defaultVal[0].id:{id:this.content.sub_rules[0].id};
            this.$emit('ievent',this.content.id,this.option)
        }
    },
    computed:{
        inputType:function(){
            switch(this.content.rule_appoint_type){
                case 0:return 'radio';break;
                case 2:return 'checkbox';break
            }
        },
        
    },
    mounted:function() {
        
    },
    template:"<div class='appoint_text' style=\"padding:0.2rem 0.3rem 0.2rem 0.3rem\">"+
    '<p class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">(必填)</span>'+
    '</p>'+
    '<p class="subtitle">{{content.subtitle}}</p>'+
    '<div class=\'appoint_box\' v-if="content.rule_appoint_type != 1">'+
        '<ul>'+
            '<li v-for="(item,idx) in content.sub_rules" :key="idx">'+
                '<input :type="inputType" :id="item.id" :name="content.id" :value="item.id" @click="getResult($event,inputType)"/>'+
                '<label :for="item.id">{{item.title}}</label>'+
            '</li>'+
        '</ul>'+
    '</div>'+
    '<div class="selectBox" v-else="content.rule_appoint_type == 1" v-model="option">'+
        '<select @change="getResult($event,\'select\')">'+
            '<option v-for="(item,idx) in content.sub_rules" :key="idx"'+ 
            ':value="item.id">'+
            '{{item.title}}'+
            '</option>'+
        '</select>'+
    '</div>'+
'</div>'
}

//图片预约 done
var AppointImage = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            host:host,
            option:[]
        }
    },
    methods:{
        choiceRadio:function(e){
            this.option =[{id:e.target.value}];
            this.$emit('ievent',this.content.id,this.option)
        }
    },
    template:'<div class="appoint_image">'+
        '<div class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">必选</span>'+
        '</div>'+
        '<p class="subtitle">{{content.subtitle}}</p>'+
        '<div class="image_option">'+
            '<div v-for="(item,idx) in content.sub_rules" :key="idx">'+
                '<label>'+
                    '<div class="image_box">'+
                        '<img :src="source+item.image" />'+
                        '<p>'+
                        '<input type="radio" :name="content.id" :value="item.id" @click="choiceRadio($event)">'+
                        '<b>{{item.title}}</b></p>'+
                    '</div>'+
                '</label>'+
            '</div>'+
        '</div>'+
    '</div>'
}

//分割线 done
var SerLine = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            separatorLine:'',
            LineType:'left',
            subType:'left'
        }
    },
    mounted:function() {
        
        switch(this.content.rule_line_idx){
            case 0:this.separatorLine = 'dotted';break;
            case 1:this.separatorLine = 'dashed';break;
            case 2:this.separatorLine = 'solid';break;
            case 3:this.separatorLine = 'double';break;
            default:this.separatorLine='';break
        }
        switch(this.content.rule_title_idx){
            case 0:this.LineType='left';break;
            case 1:this.LineType='center';break;
            case 2:this.LineType='right';break;
        }
        switch(this.content.rule_desc_idx){
            case 0:this.subType='subLeft';break;
            case 1:this.subType='subCenter';break;
            case 2:this.subType='subRight';break;
        }
    },
    template:'<div class="line">'+
        '<p :class="[LineType,separatorLine,\'title\']">{{content.title}}</p>'+
        '<p :class="[subType,\'sub-title\']">{{content.subtitle}}</p>'+
    '</div>'
}

//外观样式 done
var FaceType = {
    props:['content'],
    template:'<div class="face-type" :style="{backgroundColor:content.bg_color}">'+
    '<p class="center subTitle">{{content.subtitle}}</p>'+
    '<p class="center title">{{content.title}}</p>'+
    '</div>'
}

//预约时段 DONE
var AppointTime = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            option:[]
        }
    },
    methods:{
        getResult:function(e,t){
            switch(t){
                case 'select':
                case 'radio': 
                this.option=[{id:e.target.value}]
                ;break;
                case 'checkbox': 
                this.checkSelArr({id:e.target.value})
                break;
            }
            this.$emit('ievent',this.content.id,this.option)
        },
        checkSelArr:function(selected){ 
            var len=this.option.length;
            for(var i=0;i<len;i++){
               if(selected.id == this.option[i].id){
                    this.option.splice(i,1);
                    return;//利用函数的返回功能中断push操作
               }
            };
            this.option.push(selected)
         }
    },
    computed:{
        inputType:function(){
            switch(this.content.rule_appoint_type){
                case 0:return 'radio';break;
                case 2:return 'checkbox';break
            }
        },
    },
    created:function(){
        //下拉框的默认值
        var _this=this;
        if(this.content.rule_appoint_type == 1){
            var defaultVal = this.content.sub_rules.filter(function(v){return v.title == _this.content.rule_appoint_default});
            this.option = defaultVal[0]?defaultVal[0].id:{id:this.content.sub_rules[0].id};
            this.$emit('ievent',this.content.id,this.option)
        }
    },
    template:'<div class=\'appoint_time\' style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
    '<p class="res_title">'+
        '{{content.title}}<span class="res_tip_bitian" v-if="required">(必选)</span>'+
    '</p>'+
    '<p class="subtitle">{{content.subtitle}}</p>'+
    '<div class=\'time_box\' v-if="content.rule_appoint_type != 1">'+
        '<ul>'+
            '<li v-for="(item,idx) in content.sub_rules" :key="idx">'+
                '<input :type="inputType" :id="item.id" :name="content.id" :value="item.id" @click="getResult($event,inputType)" v-if="idx != 0"/>'+
                '<label :for="item.id" v-if="idx != 0">{{item.title}}</label>'+
            '</li>'+
        '</ul>'+
    '</div>'+
    '<div class="selectBox" v-else="content.rule_appoint_type == 1" v-model="option">'+
        '<select @change="getResult($event,\'select\')">'+
            '<option v-for="(item,idx) in content.sub_rules" :key="idx"'+ 
            ':value="item.id">'+
            '{{item.title}}'+
            '</option>'+
        '</select>'+
    '</div>'+
'</div>'
}

//图片设置 done
var ImageSet = {
    props:['content'],
    template:"<div class='image'>"+
        '<img :src="source+content.sub_rules[0].image"/>'+
    '</div>'
}

//数字 done
var SetNum = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    methods:{
        sendMSG:function(e){
            this.$emit('ievent',this.content.id,e.target.value)
        }
    },
    template:'<div class="set_num" style="padding:0.2rem 0.3rem 0.2rem 0.3rem">'+
            '<div class="res_title">'+
            '<span class="res_tip_bitian" v-if="required">*</span>{{content.title}}'+
            '</div>'+
            '<p class="subtitle">{{content.subtitle}}</p>'+
            '<div class="num-content"><input type="text" @input="sendMSG($event)"/><span>{{content.unit}}</span></div>'+
        '</div>'
}

var app = new Vue({
    delimiters:['${','}'],
    el:'#app',
    data:{
        title:PageContent.title,
        content:PageContentArr,
        template_id:PageContent.template_id,
        host:_host,
        background_color:data.background_color?data.background_color:'white',
        btnText:data.submit_button_title?data.submit_button_title:'提交',
        btnBackColor:data.submit_button_color?data.submit_button_color:'white',
        requstData:requstData,//设置初始与提交进行数据对比的中间件temp
        timerVali:{},//时间组件用于储存验证信息
        typeId:PageContent.type
    },
    beforeCreate:function(){
        console.log(this.content);
        hstool.load()
    },
    mounted:function(){
        console.log(this.content);
        hstool.closeLoad()
    },
    methods:{
        submitTrans:function(data){
            var _this = this
            $.ajax({
                url:'/shop/activity/researchSubmit/'+wid,
                data:{id:_this.content[0].activity_id,data:JSON.stringify(data)},
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    var info = ''
                    switch(_this.typeId){
                        case 0:info='报名成功';break;
                        case 1:info='预约成功';break;
                        case 2:info='投票成功';break;
                        default:break;
                    }
                    if(res.status == 1){
                        layer.open({
                            type: 1, 
                            title:'提示',
                            content: info,
                            area:['6rem','3.6rem'],
                            shadeClose:true,
                            closeBtn:0
                            ,btn: [ '前去查看','暂不前往']
                            ,yes: function(index, layero){
                                
                                location.href=`${_this.host}shop/member/researchDetail/${wid}/${actId}/${res.data.times}`
                            }
                            ,btn2: function(index, layero){
                                //按钮【按钮二】的回调
                            }
                        });
                    }else{
                        layer.msg(res.info)
                    }
                }
            })
        },
        submitForm:function(){
            //验证值
            var validate = this.requstData;
            for(var i in validate){
                switch(validate[i].type){
                    case 'appoint_text':
                    case 'appoint_image':
                    case 'appoint_time':
                    case 'vote_text':
                    case 'vote_image':
                    if(validate[i].required){
                        if(validate[i].multiple == 0 && validate[i].option.length == 0 && layer.msg('请完善必填信息')){
                            return
                        }
                        if(validate[i].multiple == 1){
                            if(validate[i].min_options > validate[i].option.length){
                                layer.msg('最少选择'+validate[i].min_options+'个选项哦')
                                return 
                            }
                        }
                    }else{
                        if(validate[i].multiple == 1){
                            if(validate[i].option && validate[i].option.length !=0 ){
                                if(validate[i].min_options > validate[i].option.length){
                                    layer.msg('最少选择'+validate[i].min_options+'个选项哦')
                                    return 
                                }
                            }
                        }
                    }
                    ;break;
                    case 'text':
                    case 'num':
                    if(validate[i].required){
                        if(validate[i].val == ''){
                            layer.msg('请完善必填信息')
                            return
                        }
                    };
                    break;
                    case 'time':
                    
                    if(validate[i].required){
                        if(!this.timerVali[i]){
                            layer.msg('请完善时间信息')
                            return
                        }
                        validate[i].start_time = $('#'+this.timerVali[i].start_time).val();
                        if(!validate[i].start_time){
                            layer.msg('请完善时间信息')
                            return
                        }
                        if(validate[i].rule_time_type == 1){
                            validate[i].end_time = $('#'+this.timerVali[i].end_time).val()
                            if(!validate[i].end_time){
                                layer.msg('请完善时间信息')
                                return
                            }
                        }
                    }else{
                        if(this.timerVali[i].start_time){
                            validate[i].start_time = $('#'+this.timerVali[i].start_time).val();
                            validate[i].end_time = $('#'+this.timerVali[i].end_time).val()
                        }
                    }
                    ;break;
                    case 'address':
                    if(validate[i].required){
                        if(validate[i].region.length<3){
                            layer.msg('请完善地区信息')
                            return
                        }else{
                            if(validate[i].region[2] == ''){
                                layer.msg('请完善地区信息')
                                return
                            }
                        }
                    }
                    break;
                    case 'image':
                    if(validate[i].required && validate[i].url == ''){
                        layer.msg('请完善图片信息')
                        return 
                    }
                    break;
                }
            }
            this.submitTrans(validate)
        },
        getTaZe:function(data1,data2){
            //接受值
            // console.log(data);
            var data = [];
            data.push(data1);
            data.push(data2);
            switch(this.requstData[data[0]].type){
                case 'appoint_text':
                case 'appoint_image':
                case 'appoint_time':
                case 'vote_image':
                case "vote_text": 
                this.requstData[data[0]].option = data[1]
                ;break;
                case 'num':
                case 'text':
                this.requstData[data[0]].val = data[1];
                break;
                case "time":
                this.timerVali[data[0]] = {};
                if(data[1] == 'endTime'){
                    this.timerVali[data[0]].end_time = data[0]+'TimeEnd';
                    this.timerVali[data[0]].start_time = data[0]+'TimeStart';
                    
                }else{
                    this.timerVali[data[0]].start_time = data[0]+'TimeStart';
                };
                break;
                case 'address':
                if(data[1]=='provice'){
                    this.requstData[data[0]].region[0] = data[2]
                }else if(data[1]=='city'){
                    this.requstData[data[0]].region[1] = data[2]
                }else{
                    this.requstData[data[0]].region[2] = data[2]
                }
                break;
                case 'image':
                this.requstData[data[0]].url = data[1]
                ;break;
            }
            
        }
    },
    components:{
        'Version'           :Version,
        "Timer"             :Timer,
        "CallPhone"         :CallPhone,
        "SetImage"          :SetImage,
        "SetAddress"        :SetAddress,
        "SetVoteVersion"    :SetVoteVersion,
        "SetVoteImage"      :SetVoteImage,
        "AppointVersion"    :AppointVersion,
        "AppointImage"      :AppointImage,
        "SerLine"           :SerLine,
        "FaceType"          :FaceType,
        "AppointTime"       :AppointTime,
        "ImageSet"          :ImageSet,
        "SetNum"            :SetNum,
    }
})
