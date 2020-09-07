function chose_mult_set_ini(select,values){
    if (values) {
        var arr = JSON.parse(values);
        var length = arr.length;
        var value = '';
        for(i=0;i<length;i++){
            value = arr[i];
            // console.log(value);
            $(select+' option'+"[value='"+value+"']").attr('selected','selected');
        }
        $(select).trigger("liszt:updated");
    }
}
app.controller('myCtrl',['$scope','$sce','$timeout','$http','commonServer',function($scope, $sce,$timeout,$http,commonServer) {
    $scope.wai_setting = true; //暂时显示图片广告外链，后期删除
    $scope.changeWaiLinkFlag = true
    $scope.baseInfo = true;
    $scope.pageSeting = {
        title:'分销客申请',
        desc:'',
        page_type:[],
        page_bgcolor:'#ffffff',
        share_title:'',
        share_desc:'',
        share_img:'',
        qq:''//客服QQ
    };//页面设置
    $scope._host = _host;
    $scope.host = imgUrl;
    $scope.goods_show = true;//商品选择判断是否为享立减商品
    $scope.xiangLinkPosition = 0; //记录选择享立减链接位置 1广告  2魔方
    $scope.editors = [];//循环列表
    $scope.index = commonServer.index;//editing当前索引值
    $scope.color = commonServer.color;//富文本设置背景颜色
    $scope.temp = commonServer.temp;//临时转存数组
    $scope.tempSure = commonServer.tempSure;//选择商品确定按钮
    $scope.chooseSureBtn = commonServer.chooseSureBtn; //选择广告图片确定按钮
    $scope.tempUploadImage = commonServer.tempUploadImage;//临时转存数组
    $scope.eventKind = commonServer.eventKind;//区分点击事件1，为添加广告多图，2为重新上传单图。
    $scope.advImageIndex = commonServer.advImageIndex //重新上传图片索引记录
    $scope.callPhoneIndex = commonServer.callPhoneIndex;//联系方式图片index
    $scope.callPhoneType = commonServer.callPhoneType;//联系方式图片index
    $scope.changeImange = commonServer.changeImange; //判断是否是member修改图片
    $scope.advsImagesIndex = commonServer.advsImagesIndex;//点击图片索引
    $scope.shopLinkPosition = commonServer.shopLinkPosition; //记录选择商品链接位置1为图片广告，2为标题
    $scope.choosePosition = 1;//1为图片广告，2为图片导航
    $scope.link_type = 1 //1.为微页面及分类2.商品及分类3.店铺主页4.会员主页
    $scope.choosePage = 1 //1为美妆小店，2为微页面
    $scope.changeImange = false;//默认选中图片不是改变背景
  
    /*@author huoguanghui start*/
    $scope.activity_list = [] //营销活动列表
    $scope.activityNavList = ["幸运大转盘","砸金蛋"];//营销活动导航列表
    $scope.activityIndex = 0;//营销活动 活动选择
    $scope.searchTitle = '';
    $scope.grounps = [];
	// add by 韩瑜 2018-8-28
    $scope.switchIndex = 1;//记录选择链接的index
    //end
    //商品及商品分组弹框
    $scope.productModal = {
        list : [],//商品或分组列表
        navList : ["已上架商品","商品分组"],//商品或分组列表
        navIndex : 0,//商品弹框导航下标
        new : [//新建
            {
                href: "/merchants/product/create",
                title: "新建商品"
            },
            {
                href: "/merchants/product/productGroup",
                title: "新建分组"
            }
        ]
    };
    
    //魔方数据展示
    $scope.xiangLinkPosition = 0; //记录选择享立减链接位置 1广告  2魔方
    $scope.spellLinkPosition = 0; //记录选择拼团链接位置          2魔方
    $scope.skillLinkPosition = 0; //记录选择秒杀链接位置   1营销  2魔方
    $scope.cube = {
        selectTelIndex:0,//当前选中模板  下标  默认第一个
        selectLayoutIndex:0,//当前选中布局下标   默认第一个
        template:[//魔方模板数据
            {
                position:[//模板布局信息  每个对象带便一个方块
                    {
                        width:1, //宽度为1个 li 宽度
                        height:1, //高度为1个 li 宽度
                        top:0,   //距顶部距离 li 宽度
                        left:0   //距左侧距离 li 宽度
                    },
                    {
                        width:1, 
                        height:1,
                        top:0,   
                        left:1   
                    }
                ],
                img:"mctsource/images/xcx/cubeTel1.png",//魔方模板图片
                title:"1行2个" //魔方模板名称
            },
            {
                position:[
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:0  
                    },
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:1  
                    },
                    {
                        width:1, 
                        height:1,
                        top:0,   
                        left:2  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel2.png",
                title:"1行3个"
            },
            {
                position:[
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:0  
                    },
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:1  
                    },
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:2  
                    },
                    {
                        width:1, 
                        height:1,
                        top:0,   
                        left:3  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel3.png",
                title:"1行4个"
            },
            {
                position:[
                    {
                        width:2,
                        height:2,
                        top:0,   
                        left:0  
                    },
                    {
                        width:2,
                        height:2,
                        top:0,   
                        left:2  
                    },
                    {
                        width:2,
                        height:2,
                        top:2,   
                        left:0  
                    },
                    {
                        width:2, 
                        height:2,
                        top:2,   
                        left:2   
                    }
                ],
                img:"mctsource/images/xcx/cubeTel4.png",
                title:"2左2右"
            },
            {
                position:[
                    {
                        width:2,
                        height:4,
                        top:0,   
                        left:0  
                    },
                    {
                        width:2,
                        height:2,
                        top:0,   
                        left:2  
                    },
                    {
                        width:2,
                        height:2,
                        top:2,   
                        left:2  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel5.png",
                title:"1左2右"
            },
            {
                position:[
                    {
                        width:4,
                        height:2,
                        top:0,   
                        left:0 
                    },
                    {
                        width:2,
                        height:2,
                        top:2,   
                        left:0  
                    },
                    {
                        width:2,
                        height:2,
                        top:2,   
                        left:2  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel6.png",
                title:"1上2下"
            },
            {
                position:[
                    {
                        width:2,
                        height:4,
                        top:0,   
                        left:0  
                    },
                    {
                        width:2,
                        height:2,
                        top:0,   
                        left:2  
                    },
                    {
                        width:1,
                        height:2,
                        top:2,   
                        left:2  
                    },
                    {
                        width:1,
                        height:2,
                        top:2,   
                        left:3  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel7.png",
                title:"1左3右"
            },
            {
                position:[
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:0  
                    },
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:1  
                    },
                    {
                        width:1,
                        height:1,
                        top:0,   
                        left:2  
                    },
                    {
                        width:1, 
                        height:1,
                        top:0,   
                        left:3  
                    },
                    {
                        width:1, 
                        height:1,
                        top:0,   
                        left:4  
                    }
                ],
                img:"mctsource/images/xcx/cubeTel9.png",
                title:"1行5个"
            }
            // {
            //     position:[],
            //     img:"mctsource/images/xcx/cubeTel8.png",
            //     title:"自定义"
            // },
        ],
        data:{//当前魔方数据
            position:[     //默认第一个模板数据
                {
                    width:1, //宽度为1个 li 宽度
                    height:1, //高度为1个 li 宽度
                    top:0,   //距顶部距离 li 宽度
                    left:0   //距左侧距离 li 宽度
                },
                {
                    width:1, 
                    height:1,
                    top:0,   
                    left:1   
                }
            ],//当前魔方布局数据
            content:[   //当前魔方添加内容
                {
                    type: 0,        //链接类型 1 商品 2 微页面 3享立减商品 4 拼团 5 秒杀  10 优惠券  默认0
                    id: 0,          //当前活动链接id
                    linkTitle:"",   //链接名称
                    img:"",         //选中图片
                    title: ""       //蒙版标题
                },
                {
                    type: 0,        //链接类型 1 商品 2 微页面 3享立减商品 4 拼团 5 秒杀  10 优惠券   默认0
                    id: 0,          //当前活动链接id
                    linkTitle:"",   //链接名称
                    img:"",         //选中图片
                    title: ""       //蒙版标题
                }
            ]  
        }
    }
    /*@author huoguanghui end*/

    
    $scope.textImageList = [];//图文回复列表
    // console.log($('#editor'))
    var ue = initUeditor('editor');//初始化编辑器
    bindEventEditor(ue,$scope);//初始化编辑器
    laydate.skin('molv'); //切换皮肤，请查看skins下面皮肤库
    // alert(1);
    var start = {
        elem: '#date',
        format: 'YYYY-MM-DD',
        min: '2009-06-16 23:59:59', //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        event: 'focus',
        istime: true,
        istoday: false,
        choose: function(datas) {
            $scope.$apply(function(){
                $scope.editors[$scope.index]['date'] = datas;
            })
        }
    };
    $('.chosen_select').chosen();
    $scope.couponList = [];//优惠券列表
    
    
    $scope.uploadImages = [] //选择图片数组
    $scope.page_template = page_template.template_info;
    // 拼接thgoods
    $scope.pageSeting.title = page_template.title;
    if($scope.pageSeting.title === undefined){
        $scope.pageSeting.title = '分销客申请';
    }
    $scope.pageSeting.desc = page_template.description;
    $scope.pageSeting.page_bgcolor = page_template.bg_color;
    $scope.pageSeting.page_bgcolor = $scope.pageSeting.page_bgcolor ? $scope.pageSeting.page_bgcolor : '#ffffff';
    if(page_template.share_img){
        $scope.pageSeting.share_img = imgUrl + page_template.share_img;
    }
    $scope.pageSeting.share_title = page_template.share_title;
    $scope.pageSeting.share_desc = page_template.share_desc;
    // console.log(typeof $scope.page_template);
    /*编辑功能*/
    if($scope.page_template != null){
        $scope.first_card = true;
        // console.log($scope.page_template.template_info);
        // console.log(typeof JSON.parse($scope.page_template));
        if(typeof $scope.page_template == 'string' && $scope.page_template != ''){
            $scope.editors = JSON.parse($scope.page_template);
        }else{
            if($scope.page_template != ''){
                $scope.editors = $scope.page_template;
            }
        }
        if($scope.editors.length>0 && typeof($scope.editors) != 'string'){
            angular.forEach($scope.editors,function(val,key){
                val.is_add_content = false;
                val.showRight = false;
                
                if(val.type=='image_ad'){
                    // console.log(val);
                    if(val.images.length>0){
                        angular.forEach(val.images,function(val1,key1){
                            val1.FileInfo.m_path = imgUrl + val1.FileInfo.m_path;
                            val1.FileInfo.path = imgUrl + val1.FileInfo.path;
                        })
                    }
                    if(typeof val.resize_image === undefined){
                        val.resize_image = 1;
                    }
                }
                if(val.type=='header'){
                    val.bg_image = imgUrl + val.bg_image;
                }
               
                // if(val.type == 'header'){
                //     val.logo = store.logo;
                // }
               
               
                //魔方功能  图片添加域名
                if(val.type=='cube'){
                    angular.forEach(val.content,function(val1,key){
                        if(val1.img != ''){
                            val1.img = imgUrl + val1.img;
                        }
                    })
                    // add by 黄新琴 2018/8/31
                    if (val.margin == undefined){
                       val.margin = 0;
                    }
                    if(typeof val.resize_image === undefined){
                        val.resize_image = 1;
                    }
                   
                }
				
                //公告去除 “公告：” updata by 邓钊 2018-8-28
				if(val.type == "notice"){
                    val.content = val.content.replace(/公告：/g, '');
                    val.colorBg = val.colorBg ? val.colorBg : '#ffffcc'
                    val.txtBg = val.txtBg ? val.txtBg : '#ff9900'
                }
            })
        }
        
    }
    //去除图片域名
    $scope.removeHost = function(src){
        src = src.replace(_host,'');
        src = src.replace(imgUrl,'');
        return src;
    }
    //添加或修改微页面信息
    $scope.processPage=function(isValid){
        $scope.iserror = true;
        if(isValid){
            if(!(($scope.pageSeting.share_img && $scope.pageSeting.share_title && $scope.pageSeting.share_desc) || (!$scope.pageSeting.share_title  && !$scope.pageSeting.share_desc && !$scope.pageSeting.share_img))){

                if(!$scope.pageSeting.share_img && $scope.pageSeting.share_title && $scope.pageSeting.share_desc){
                    tipshow("请填写分享图片","warn");
                    return false;
                }
                if(!$scope.pageSeting.share_title && $scope.pageSeting.share_img && $scope.pageSeting.share_desc){
                    tipshow("请填写分享标题","warn");
                    return false;
                }
                if(!$scope.pageSeting.share_desc && $scope.pageSeting.share_title && $scope.pageSeting.share_img){
                    tipshow("请填写分享内容","warn");
                    return false;
                }
                if($scope.pageSeting.share_img){
                    tipshow("请填写分享标题及内容","warn");
                    return false;
                }
                if($scope.pageSeting.share_title){
                    tipshow("请填写分享内容及图片","warn");
                    return false;
                }
                if($scope.pageSeting.share_desc){
                    tipshow("请填写分享标题及图片","warn");
                    return false;
                }
            }
            $('.btn_grounp button').attr('disabled','disabled');
            
            if(typeof $scope.editors == 'string' && $scope.editors == ''){
                $scope.editors = [];
            }
            $scope.postData = angular.copy($scope.editors);
            var keepGoing = true;//使angular foreach 退出循环  (魔方)
            //需后台插入的数据进行初始化 避免垃圾数据
            angular.forEach($scope.postData,function(val,key){
                if( keepGoing ){//相当于退出循环
                    if(val.type == "image_ad"){
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.FileInfo = [];
                                delete val1.id;
                            })
                        }
                    }
                   
                    if(val.type == 'header'){
                        if(val.logo !== ''){
                            val.logo = val.logo.replace(_host,'');
                            val.bg_image = val.bg_image.replace(_host,'');
                            val.logo = val.logo.replace(imgUrl,'');
                            val.bg_image = val.bg_image.replace(imgUrl,'');
                        }
                    }
                   
                    //魔方处理数据
                    if(val.type == 'cube'){
                        if(val.content.length>0){
                            angular.forEach(val.content,function(val1,key1){
                                if(val1.img != ''){
                                    val1.img = val1.img.replace(imgUrl,'');
                                }else{
                                    keepGoing = false;
                                    tipshow("请添加魔方图片","warn");
                                    $scope.editors[key].isPromptAddPic = true;
                                }
                            })
                        }
                    }
                }
            })
            //魔方数据不完整则退出
            if(!keepGoing){
                $('.btn_grounp button').attr('disabled',false);
                return;
            }
            $scope.postData = JSON.stringify($scope.postData);
            var data = {
                title:$scope.pageSeting.title,
                description:$scope.pageSeting.desc,
                bg_color:$scope.pageSeting.page_bgcolor,
                template_info:$scope.postData,
                status:1,
                _token:$('meta[name="csrf-token"]').attr('content'),
                share_title:$scope.pageSeting.share_title,
                share_desc:$scope.pageSeting.share_desc,
                share_img:$scope.pageSeting.share_img ? $scope.removeHost($scope.pageSeting.share_img): ''
            };
            if(page_template.id!=undefined){
                data.id=page_template.id;
            }
            
            $.ajax({
                type:"post",
                url:'/merchants/distribute/addApplyPage',
                data:data,
                success: function(msg){
                    if(msg.status==1){
                        tipshow(msg.info);
                        setTimeout(function(){
                            window.location.href = '/merchants/distribute/applyList';
                        },1000)
                    }else{
                        $('.btn_grounp button').removeAttr('disabled');
                        tipshow(msg.info,'warn');
                    }
                },
                error:function(msg){
                    $('.btn_grounp button').removeAttr('disabled');
                    layer.alert(msg.info);
                }
            });
        }
    }
   
     // 添加边框
    $scope.addboder = commonServer.addboder;
    // 减去边框
    $scope.removeboder = commonServer.removeboder;
    //初始化清除editing
    $scope.removeClassEditing = function(){
        commonServer.removeClassEditing($scope);
    }
    // 初始化右边栏
    $scope.index = 0
    $scope.initCartRight = function(){
        $scope.first_card = false;
        commonServer.initCartRight($scope.index,$scope,25);
    }
    $scope.addeditor = function(position){
        commonServer.addeditor($scope,ue,position);
    } 
    // 左侧每个列表
    $scope.tool = function($event,editor){
        $scope.first_card = false;
        commonServer.tool($event,editor,$scope,25,ue);
    }
    // 标题头部点击
    $scope.showPage = function(){
        $scope.first_card = true;
        if($scope.editors.length>=1){
            angular.forEach($scope.editors,function(val,key){
                val.showRight = false;
            })
        }
    }
    // 上传图片
    $scope.upload = function(){
        $scope.uploadShow = true;
        $('.webuploader-pick').next('div').css({
            'top': '19px',
            'width': '168px',
            'height': '44px',
            'left':'40%'
        })
    }
    //上传确定按钮
    $scope.uploadSureBtn = function(){
        commonServer.chooseAdvSureBtn($scope);
        $('#myModal-adv').hide();
        $('.modal-backdrop').hide();
        closeUploader();
    }
    // 返回选择图片
    $scope.showImage = function(){
        $scope.uploadShow = false; //判断上传可图片model显示
    }
  
    // 分割线
    $scope.separatorLine = function($index){
        commonServer.separatorLine($scope,$index);
    }
    //分割线样式切换
    $scope.lineType = function($index){
        commonServer.lineType($scope,$index);
    }
    $scope.lineArr = ['dotted','dashed','solid','double','']
    $scope.alignWay = ['left-ali','center-ali','right-ali']
    $scope.alignWay = ['left-ali','center-ali','right-ali']
    //标题对齐方式切换
    $scope.alignType = function($index){
        commonServer.alignType($scope,$index);
    }
    //描述对齐方式切换
    $scope.contAlign = function($index){
        commonServer.contAlign($scope,$index);
    }
   
    //显示model
    $scope.showModel = function(){
        commonServer.showModel($scope);
    }
    //添加分享图片
    $scope.addShareImages = function(){
        $scope.eventKind = 3;
        commonServer.addShareImages($scope);
    }
    //删除微页面分享图片
    $scope.removeShareImg = function(){
        $scope.pageSeting.share_img = '';
    }
    // 隐藏model
    $scope.hideModel = function(){
        commonServer.hideModel();
        hideModel($('#myModal'));
        hideModel($('#upload_model'));
        hideModel($('#myModal-adv'));
        hideModel($('#my_coupon_model'));
        hideModel($('#page_model'));
        hideModel($('#chooseShopModel'));
        hideModel($('#page_current_model'));
        hideModel($('#goodslist_model'));
        hideModel($('#component_model'));
        hideModel($('#kill_model'));
        hideModel($('#activity_model'));
        hideModel($('#text_image_model'));
        hideModel($('#my_card_model'));
        hideModel($('#my_card_model'));
        hideModel($('#qq_model'));
        hideModel($('#page_spell_model'));
        hideModel($('#spell_Modal'));
        hideModel($('#shareGoodModel'));
        hideModel($('#liGoodModel'));
        hideModel($('#wheel_model'));
        hideModel($('#scratchCard'));
        hideModel($('#page_model_card'));
        hideModel($('#cube_coupon_model'));
    }
    //选择商品
    $scope.choose = function($index,list){
        commonServer.choose($index,$scope,list)
    }
    
    //显示删除按钮
    $scope.showDelete = function($index){
        commonServer.showDelete($index,$scope);
    }
    //隐藏删除按钮
    $scope.hideDelete = function($index){
        commonServer.hideDelete($index,$scope)
    }
   

    //删除图片
    $scope.delete = function($index){
        commonServer.delete($index,$scope);
    }
  
    //删除模块

    $scope.deleteAll = function($index){
        commonServer.deleteAll($index,$scope);
    }

    // 广告图片添加
    $scope.addAdvImages = function(position){
        commonServer.addAdvImages($scope,position);
    }
    $scope.initchooseAdvImage = function(){
        commonServer.initchooseAdvImage($scope);
    }
    //点击添加广告弹出model
    $scope.addAdvs = function(){
        commonServer.addAdvs($scope);
    }
     // 广告图片分组点击
    $scope.chooseGroup = function(grounp){
        commonServer.chooseGroup($scope,grounp);
    }
    // 选择广告图片
    $scope.chooseImage = function(image,$index){
        commonServer.chooseImage(image,$index,$scope);
    }
    //选择广告图片确定按钮
    $scope.chooseAdvSureBtn = function(){
        commonServer.chooseAdvSureBtn($scope);
    }
    //广告图片重新上传
    $scope.reUpload = function($index){
        commonServer.reUpload($index,$scope);
    }

    //删除广告图片
    $scope.removeAdvImages = function($index){
        commonServer.removeAdvImages($index,$scope);
    }
   
    // 显示dropdown
    $scope.showDown = function($index,position){
        commonServer.showDown($index,position,$scope);
    }
    //隐藏dropDown
    $scope.hideDown = function($index){
        // $scope.editors[$scope.index]['images'][$index]['dropDown'] = false;
    }
    $scope.sureProverPosition = 1;//1为广告图片的，2为标题的


    $scope.sureProver = function(){
       commonServer.sureProver($scope)
    }
    $scope.cancelProver = function(){
       commonServer.cancelProver();
    }
    //添加标题
    $scope.addTitle = function(position){
        commonServer.addTitle($scope,position);
    }
    // 传统样式添加一个文本链接
    $scope.addLink = function(){
        commonServer.addLink($scope);
    }
    $scope.deleteLinkWb = function(){
        commonServer.deleteLinkWb($scope);
    }
    //绑定laydate
    $scope.bindDate = function(){
        commonServer.bindDate(start);
    }
    
    //公告添加
    $scope.addNotice = function(position){
        commonServer.addNotice($scope,position);
    }
   
    
    //商品分组添加
    $scope.addGoodGroup = function(position){
        commonServer.addGoodGroup($scope,position);
    }
    // 商品分组数量选择
    $scope.chooseNum = function(list,num){
        commonServer.chooseNum($scope,list,num);
        list.dropDown = false;
    }
    // 显示下拉选择框
    $scope.showDropDown = function(list){
        list.dropDown = true;
    }
    //选择分组确定
    $scope.chooseGroupSure = function(){
        commonServer.chooseGroupSure($scope);
    }
    // 删除一个选中分组
    $scope.deleteGroup = function($index){
        commonServer.deleteGroup($index,$scope);
    }
    // 重新选择分组
    $scope.changeGroup = function(position,list){
        commonServer.addShopGroup($scope,position,list);
    }
   
   
    /** 
     * 添加魔方组件
     * @param position  1添加 2 加内容
     */
    $scope.addCube = function(position){
        commonServer.addCube($scope,position)
    }
    /**
     * 选择魔方模板
     * @param index  当前元素下标
     * 设置模板下标 布局初始化为选中第一个
     * 修改魔方数据
     */
    $scope.selectedTel = function(index){
        commonServer.selectedTel($scope,index)
    }
    /**
     * 选择魔方布局
     * @param index  当前元素下标
     */
    $scope.selectedLayout = function(index){
        commonServer.selectedLayout($scope,index)
    }
     /**
     * 魔方滑动组件点击设置间距
     * add by 黄新琴 2018/8/31
     */
    $scope.changeMargin = function(event){
        commonServer.changeMargin(event,$scope)
    }
    /**
     * 魔方滑动组件滑动设置间距
     * add by 黄新琴 2018/8/31
     */
    $scope.sliderMove = function(e){
        if ($scope.canSlider) {
            var move = e.clientX - $scope.clientX,
                width = $('#slider-container').width();
            var moveMargin = Math.round(move/width*30);
            $scope.editors[$scope.index].margin = $scope.margin + moveMargin;
        }
    }
      /**
     * 魔方滑动组件允许滑动设置间距
     * add by 黄新琴 2018/8/31
     */
    $scope.enableSlider = function(e){
        $scope.canSlider = true;
        $scope.clientX = e.clientX;
        $scope.margin = $scope.editors[$scope.index].margin;
    }
     /**
     * 魔方滑动组件禁止滑动设置间距
     * add by 黄新琴 2018/8/31
     */
    $(document).on('mouseup',function(){
        $scope.canSlider = false;
    })
    /**
     * 魔方添加图片
     */
    $scope.cubeAddPic = function(){
        commonServer.addAdvs($scope);//打开弹框
        $scope.eventKind = 5;//设置添加图片的类型
    }


    $scope.choosePhoneImage = function(position){
        commonServer.addAdvs($scope);//打开弹框
        $scope.callPhoneIndex = this.$index;
        $scope.callPhoneType = position;
        $scope.eventKind = 6;//设置添加图片的类型
    }
    // 添加手机号
    $scope.addMobile = function(position){
        $scope.removeClassEditing();        
        if(position == 1){
            $scope.editors.push(
                {   
                    'showRight':true,
                    'cardRight':26, //3为富文本，4商品，5商品列表
                    'type':'mobile',
                    'editing':'editing',
                    'icon': _host + 'mctsource/images/phone_icon_updated.png',
                    'title':'联系我们', 
                    'lists':[],
                    "is_add_content":false,
                    "mobileStyle":1
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{   
                'showRight':true,
                'cardRight':26, //3为富文本，4商品，5商品列表
                'type':'mobile',
                'editing':'editing',
                'icon': _host + 'mctsource/images/phone_icon_updated.png',
                'title':'联系我们', 
                'lists':[],
                "is_add_content":false,
                "mobileStyle":1
            })
        }
        $scope.initCartRight();//初始化右边
    }
    // 添加一行手机号
    $scope.addMobileRow = function(){
        $scope.editors[$scope.index]['lists'].push({
            'area_code':'',
            'mobile':'',
            'close':false,
            'icon':imgUrl+'hsshop/image/static/icon02.png',
            'image':imgUrl+'hsshop/image/static/img03.png',
            'imageShadowShow':1
        })
    }
    // 删除一行手机号
    $scope.deleteMobileRow = function($index){
        $scope.editors[$scope.index]['lists'].splice($index,1)
    }
    // 移动到手机号事件
    $scope.hoverMobile = function(item){
        item.close = true;
    }
    //移除手机号事件
    $scope.outMobile = function(item){
        item.close = false;
    }

    /**
     * 魔方链接删除
     */
    $scope.deleteLink = function(){
        commonServer.deleteLink($scope);
    }
    /*魔方end*/

    
    
    
    // 微页面加内容
    $scope.addContent = function(event,$index,editor,top){
        commonServer.addContent(event,$index,editor,$scope,top);
    }

    // 修改标题确定
    $scope.sureChangeTitle = function(){
        if($('#title_input').val() == ''){
            tipshow('必须填写标题','warn');
            return;
        }
        if($('#title_input').val().length >10 ){
            tipshow('标题长度不能大于10个字','warn');
            return;
        }
        $scope.editors[$scope.index]['lists'][$scope.menuIndex]['title'] = $('#title_input').val();
        $('#changeTitleProver').hide();
        $('#title_input').val('');
    }
    // 修改标题取消
    $scope.cancelChnageTitle = function(){
        $('#title_input').val('');
        $('#changeTitleProver').hide();
    }
    $scope.safeApply = function(fn) {
        var phase = this.$root.$$phase;
        if (phase == '$apply' || phase == '$digest') {
            if (fn && (typeof(fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };
 
    
    //删除优惠券
    $scope.deleteCard = function($index){
        commonServer.deleteCard($scope,$index);
    }
 
    
    
    //微页面组件拖动
    $scope.onDropPageComplete = function(index, obj, evt){
        if(obj.cardRight == undefined && obj.type == undefined){
            return;
        }
        var otherObj = $scope.editors[index];
        var otherIndex = $scope.editors.indexOf(obj);
        $scope.editors[index] = obj;
        $scope.editors[otherIndex] = otherObj;
        $scope.initCartRight();
    }
    //广告图片拖动
    $scope.onDropAdvsComplete = function(index, obj, evt){
        if(obj.cardRight != undefined || obj.type != undefined ){
            return;
        }
        var otherObj = $scope.editors[$scope.index]['images'][index];
        var otherIndex = $scope.editors[$scope.index]['images'].indexOf(obj);
        $scope.editors[$scope.index]['images'][index] = obj;
        $scope.editors[$scope.index]['images'][otherIndex] = otherObj;
    }
  
    //监听temp有没有数据显示按钮
    $scope.$watch("temp",function(newVal,oldVal){
        if($scope.temp.length==0){
            $scope.tempSure = false;
        }else{
            $scope.tempSure = true;
        }
    },true)
    $scope.$watch("tempUploadImage",function(newVal,oldVal){
        if($scope.tempUploadImage.length==0){
            $scope.chooseSureBtn = false;
        }else{
            $scope.chooseSureBtn = true;
        }
    },true)
    $scope.$watch('titleStyle',function(newVal,oldVal){
        if($scope.tempUploadImage.length==0){
            $scope.chooseSureBtn = false;
        }else{
            $scope.chooseSureBtn = true;
        }
    },true)
    $scope.$watch('editors',function(newVal,oldVal){
        if ($scope.editors[$scope.index]) {
            if ($scope.editors[$scope.index].margin < 0 ){
                $scope.editors[$scope.index].margin = 0;
            }else if ($scope.editors[$scope.index].margin>30){
                $scope.editors[$scope.index].margin = 30;
            }
        }
       
    },true)
    // 指定商品模态框刷新
    $scope.refresh = function(){
        $scope.searchGoods();
    }
    uploader.on('uploadSuccess', function (file, response) {
        if (response.status == 1) {
            $scope.$apply(function () {
                response.data['FileInfo']['path'] = imgUrl + response.data['FileInfo']['path'];
                $scope.tempUploadImage.push(response.data);
            })
        }
    });
   
  
   
    
}])
