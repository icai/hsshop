// step1 写着写着我写懵了 所以我打算动态添加组件
var PageContentArr = []
for(var i in data.records){
    PageContentArr.push(data.records[i])
}
//设计组件
PageContentArr.map((v,i)=>{
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
    if(v.content.match(/^[\{\[]/g)){
        v.content = JSON.parse(v.content)
    }
    return v
})

console.log(PageContentArr)
//文本 done
var Version = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    template:`<div class="text" style="padding:0.4rem">
                <div class="res_title">
                <span class="res_tip_bitian" v-if="required">*</span>{{content.title}}
                </div>
                <p class="subtitle">{{content.subtitle}}</p>
                <textarea disabled>{{content.content}}</textarea>
            </div>`,
}

//时间 done
var Timer = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,

        }
    },
    methods:{
        sendMSG:function(e){
            this.$emit('ievent',this.content.id,e.target.name)
        }
    },
    computed:{
        sure:function(){
            if(!this.content.content.end_time){
                return 'single'
            }else{
                return 'double'
            }
        }
    },
    template:`
    <div class='dateTime' :id="content.id" style="padding:0.4rem">
            <div class='res_title'>
            <span class='res_tip_bitian' v-if="required">*</span>
            {{content.title}}</div>
			<div class='date_box'>
				<div class='date_inp'>
					<input type="text" name="startTime" :class="sure" :value="content.content.start_time" disabled/>
					<span></span>
				</div>
				<div class='fengefu' v-show="content.content.end_time"></div>
				<div class="date_inp" v-show="content.content.end_time">
					<input type="text" name="endTime" class="double" :value="content.content.end_time" disabled/>
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
    template:`<div class="phone" style="padding:0.4rem">
                <div class="res_title">
                <span class="res_tip_bitian" v-if="required">*</span>{{content.title}}
                </div>
                <div class='phone_box'>
                    {{content.rule_phone_value}}
                </div>
            </div>`
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
            defaultPIC:`${host}shop/images/uploadPic.png`,
            tipAboutPIC:'选择图片'
        }
    },
    created:function(){
        this.defaultPIC = host+this.content.content
    },
    template:`
    <div class="SetImage" style="padding:0.4rem">
        <div class="res_title">
        <span class="res_tip_bitian" v-if="required">*</span>{{content.title}}
        </div>
        <p class="subtitle">{{content.subtitle}}</p>
        <a href="javasrcipt:;" class="logoChange">
            <img :src="defaultPIC" alt="Image preview" class="uploadPicture"/>
        </a>
    </div>`
}

//地址 done
var SetAddress = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    template:`<div class='address' v-cloak style="padding:0.4rem">
        <p class="res_title">
        <span class="res_tip_bitian" v-if="required">*</span>{{content.title}}
        </p>
        <p class="subtitle">{{content.subtitle}}</p>
        <div class="distpicker">
            <div class="form-group">
                <input class="form-control provice" type="text" :value="content.content.region[0]" disabled/>
            </div>
            <div class="form-group">
                <input class="form-control city" type="text" :value="content.content.region[1]" disabled/>
            </div>
            <div class="form-group">
                <input class="form-control dist" type="text" :value="content.content.region[2]" disabled/>
            </div>
        </div>
    </div>`
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
        
    },
    computed:{
        inputType:function(){
            switch(this.content.multiple){
                case 0:return 'radio';break;
                case 1:return 'checkbox';break
            }
        },
    },
    template:`
    <div class='set_vote_text'>
        <p class="res_title">
            {{content.title}}
        </p>
        <p class="subtitle">{{content.subtitle}}</p>
        <div class='appoint_box'>
            <ul>
                <li v-for="(item,idx) in content.content" :key="idx">
                    <input :type="inputType" :value="item.id" checked/>
                    <label :for="item.id">{{item.title}}</label>
                </li>
            </ul>
        </div>
    </div>`
}

//图片投票
var SetVoteImage = {
    props:['content'],
    data:function(){
        return {
            host:host,
            option:[],
        }
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
    template:`
    <div class='set_vote_image'>
        <div class="res_title">
        {{content.title}}
        </div>
        <p class="subtitle">{{content.subtitle}}</p>
        <div :class="{image_option:true,'flex-box':true}">
            <div v-for="(item,idx) in content.content" :key="idx" :class="{'image-height-box':true}">
                <label>
                    <div class="image_box">
                        <img :src="host+item.image" />
                        <p>
                        <input :type="inputType" :name="content.id" :value="item.id" @click="getResult($event,inputType)" checked disabled/>
                        <b>{{item.title}}</b></p>
                    </div>
                </label>
            </div>
        </div>
    </div>`
}

//文本预约 done
var AppointVersion = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
        }
    },
    created:function(){
       console.log(this.content,2222)
    },
    computed:{
        inputType:function(){
            switch(this.content.rule_appoint_type){
                case 0:return 'radio';break;
                case 2:return 'checkbox';break
            }
        },
        
    },
    template:`<div class='appoint_text' style="padding:0.4rem">
    <p class="res_title">
        {{content.title}}
    </p>
    <p class="subtitle">{{content.subtitle}}</p>
    <div class='appoint_box' v-if="content.rule_appoint_type != 1">
        <ul>
            <li v-for="(item,idx) in content.content" :key="idx">
                <input :type="inputType" :id="item.id" :name="content.id" :value="item.id" checked disabled/>
                <label :for="item.id">{{item.title}}</label>
            </li>
        </ul>
    </div>
    <div class="selectBox" v-else="content.rule_appoint_type == 1">
        <select>
            <option>{{content.content[0].title}}</option>
        </select>
    </div>
</div>`
}

//图片预约 done
var AppointImage = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
            host:host
        }
    },
    template:`
    <div class="appoint_image">
        <div class="res_title">
        {{content.title}}
        </div>
        <p class="subtitle">{{content.subtitle}}</p>
        <div class="image_option">
            <div v-for="(item,idx) in content.content" :key="idx">
                <label>
                    <div class="image_box">
                        <img :src="host+item.image" />
                        <p>
                        <input type="radio" :name="content.id" :value="item.id" checked>
                        <b>{{item.title}}</b></p>
                    </div>
                </label>
            </div>
        </div>
    </div>`
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
    template:`<div class="line">
        <p :class="[LineType,separatorLine,'title']">{{content.title}}</p>
        <p :class="[subType,'sub-title']">{{content.subtitle}}</p>
    </div>`
}

//外观样式 done
var FaceType = {
    props:['content'],
    template:`<div class="face-type" :style="{backgroundColor:content.bg_color}">
    <p class="center subTitle">{{content.subtitle}}</p>
    <p class="center title">{{content.title}}</p>
    </div>`
}

//预约时段 DONE
var AppointTime = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required,
        }
    },
    methods:{
       
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
    },
    template:`<div class='appoint_text' style="padding:0.4rem">
    <p class="res_title">
        {{content.title}}
    </p>
    <p class="subtitle">{{content.subtitle}}</p>
    <div class='appoint_box' v-if="content.rule_appoint_type != 1">
        <ul>
            <li v-for="(item,idx) in content.content" :key="idx">
                <input :type="inputType" :id="item.id" :name="content.id" :value="item.id" checked disabled/>
                <label :for="item.id">{{item.title}}</label>
            </li>
        </ul>
    </div>
    <div class="selectBox" v-else="content.rule_appoint_type == 1">
        <select>
            <option>{{content.content[0].title}}</option>
        </select>
    </div>
</div>`
}

//图片设置 done
var ImageSet = {
    props:['content'],
    template:`<div class='image'>
        <img :src="host+content.sub_rules[0].image"/>
    </div>`
}

//数字 done
var SetNum = {
    props:['content'],
    data:function(){
        return {
            required:this.content.required
        }
    },
    template:`<div class="set_num" style="padding:0.4rem">
            <div class="res_title">
            {{content.title}}
            </div>
            <p class="subtitle">{{content.subtitle}}</p>
            <div class="num-content"><input type="text"  :value="content.content"/><span>{{content.unit}}</span></div>
        </div>`
}

var app = new Vue({
    delimiters:['${{','}}'],
    el:'#app',
    data:{
        title:PageContent.title,
        content:PageContentArr,
        template_id:PageContent.template_id,
        host:host,
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
