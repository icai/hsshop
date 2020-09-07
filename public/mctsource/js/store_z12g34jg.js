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
    $scope.picNumber= true; 
    $scope.permission = permission; //权限
    $scope.wai_setting = true; //显示外链
    $scope.changeWaiLinkFlag = true
    $scope.store_url = store.url;//店铺主页
    $scope.cart_url = store.cart_url;//购物车地址
    $scope.member_url = store.member_url;//会员主页
    $scope.baseInfo = true;
    $scope.pageSeting = {
        title:'微页面标题',
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
    $scope.is_custom = 1;//自定义模块默认可以添加
    $scope.shop_url = store.url //店铺主页url;
    $scope.member_url = store.member_url;//会员主页URL
    /*@author huoguanghui start*/
    $scope.activity_list = [] //营销活动列表
    $scope.activityNavList = ["幸运大转盘","砸金蛋"];//营销活动导航列表
    $scope.activityIndex = 0;//营销活动 活动选择
    $scope.searchTitle = '';
    $scope.grounps = [];
	// add by 韩瑜 2018-8-28
    $scope.switchIndex = 1;//记录选择链接的index
    //end
    // add by 韩瑜 2018-9-18 商品分组模板
    $scope.GroupLinkType = 1; //判断链接类型
    $scope.navindex = 0;//左侧分类点击记录index
    $scope.p_index = 0;//右侧删除记录父元素index
    //end
    //add by 倪凯嘉 2018-10-23 分享添加图片
    $scope.addPic="+添加图片";//分享图片加图或者修改
    $scope.classShow=true;//分享图片显示样式
    //end
    $scope.isCardImg = false; // 是否是会员卡封面图裁剪框
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
    //视频功能
    $scope.video = {
        checkedIndex:-1,//视频选中下标
        checkedItem:null,//视频选中对象
        groupingIndex:0,//分组下标
        groupList:[],//视频弹框分组
        groupingId:0,//分组id
        modeosearchTitle: "",//视频模态框搜索
        modelVideoList:[],//模态框视频列表
    }
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
                        height:1,
                        top:0,   
                        left:2  
                    },
                    {
                        width:2,
                        height:1,
                        top:1,   
                        left:2
                    }
                ],
                img:"mctsource/images/xcx/cubeTel10.png",
                title:"1左2右"
            },
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

    $scope.group_url = '/shop/grouppurchase/index/' + store.id;
    $scope.community_url = '/shop/microforum/forum/index/'+ store.id;//微社区url
    $scope.sign_url = '/shop/point/sign/'+ store.id;//微社区url 
    $scope.textImageList = [];//图文回复列表
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
    $scope.goodList = [
        {
            "name":"实物商品（购买时需填写收货地址，测试商品，不发货，不退款",
            "thumbnail":"https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/554c17f049181649f35168d8fb367663.jpg",
            "info":"这是商品通知",
            "price":"￥1",
            "timeDay":"2016-09-22",
            "timestamp":"15:57:27",
            "url":''  
        }
    ];
    $scope.QQlist = [] //QQ客服列表
    $scope.uploadImages = [] //选择图片数组
    // console.log(page_template.template_info);
    // console.log(page_template.template_info);
    // console.log(typeof page_template.template_info);
    $scope.page_template = page_template.template_info;
    $scope.is_custom = page_template.is_custom;
    // 拼接thgoods
    $scope.pageSeting.title = page_template.title;
    if($scope.pageSeting.title === undefined){
        $scope.pageSeting.title = '微页面标题';
    }
    $scope.pageSeting.desc = page_template.description;
    $scope.pageSeting.page_bgcolor = page_template.bg_color;
    $scope.pageSeting.page_bgcolor = $scope.pageSeting.page_bgcolor ? $scope.pageSeting.page_bgcolor : '#ffffff';
    if(page_template.share_img){
        $scope.pageSeting.share_img = imgUrl + page_template.share_img;
    }
    $scope.pageSeting.share_title = page_template.share_title;
    $scope.pageSeting.share_desc = page_template.share_desc;
    $scope.pageSeting.qq = page_template.qq
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
        	console.log($scope.editors,'$scope.editors')
            angular.forEach($scope.editors,function(val,key){
                val.is_add_content = false;
                val.showRight = false;
                if(val.type == 'goods'){
                    val.thGoods = [];
                    val.goods = val.goods == undefined ? []:val.goods; 
                    if(val.goods.length>0){
                        angular.forEach(val.goods,function(val1,key1){
                            val1.thumbnail = imgUrl + val1.thumbnail;
                            if(val.thGoods.length > 0){
                                if(val.thGoods[val.thGoods.length - 1].length>=3){
                                    $scope.editors[key]['thGoods'].push([]);
                                    $scope.editors[key]['thGoods'][val.thGoods.length-1].push(val1)
                                }else{
                                    val.thGoods[val.thGoods.length - 1].push(val1)
                                }
                            }else{
                                val.thGoods[0] = [];
                                val.thGoods[0].push(val1)
                            }
                        })
                    }else{
                        val.thGoods = [
                            [
                                {
                                    'thumbnail':_host + 'static/images/product_img_1.jpg',
                                    'name':'这里显示商品名称',
                                    'info':'这里显示商品通知',
                                    'price':'￥1'
                                },
                                {
                                    'thumbnail':_host + 'static/images/product_img_1.jpg',
                                    'name':'这里显示商品名称',
                                    'info':'这里显示商品通知',
                                    'price':'￥2'
                                },
                                {
                                    'thumbnail':_host + 'static/images/product_img_1.jpg',
                                    'name':'这里显示商品名称',
                                    'info':'这里显示商品通知',
                                    'price':'￥3'
                                }
                            ]
                        ]
                    }
                    // if(val.thGoods.length>0){
                    //     angular.forEach(val.thGoods,function(val1,key1){
                    //         angular.forEach(val1,function(val2,key2){
                    //             val2.thumbnail = _host + val2.thumbnail;
                    //         })
                    //     })
                    // }
                }
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
                if(val.type=="image_link"){
                    if(val.images.length > 0){
                        angular.forEach(val.images,function(val1,key1){
                            if(val1.thumbnail){
                                val1.thumbnail = imgUrl + val1.thumbnail;
                            }
                        })
                    }
                }
                if(val.type == "coupon"){
                    //优惠券老数据做兼容2018-8-6 add by 魏冬冬
                    if(!val.couponStyle){
                        val.couponStyle = 1;
                    }
                    if(!val.couponColor){
                        val.couponColor = 1;
                    }
                    //end
                    if(val.coupons_id.length){
                        // 最简单数组去重法 
                        function unique(array) {
                            var n = []; //一个新的临时数组 
                            //遍历当前数组 
                            for (var i = 0; i < array.length; i++) {
                                //如果当前数组的第i已经保存进了临时数组，那么跳过， 
                                //否则把当前项push到临时数组里面 
                                if (n.indexOf(array[i]) == -1) n.push(array[i]);
                            }
                            return n;
                        }
                        val.coupons_id = unique(val.coupons_id);
                    }
                }
                // 会员卡封面图老数据兼容
                if (val.type == "card") {
                    for (var i = 0; i < val.cardList.length; i++) {
                        if (val.cardList[i].card_img == undefined) {
                            val.cardList[i].card_img = ''
                        }
                    }
                }
                if(val.type == "goodslist"){
                    val.thGoods = [
                        [
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥1'
                            },
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥2'
                            },
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥3'
                            }
                        ]
                    ]
                }

                if(val.type == 'header'){
                    val.logo = store.logo;
                }
                if(val.type == 'bingbing'){
                    val.bg_image = imgUrl + val.bg_image;
                    if(val.lists.length>0){
                        angular.forEach(val.lists,function(val1,key){
                            if(val1.icon != ''){
                                val1.icon = imgUrl + val1.icon;
                            }
                            if(val1.bg_image != ''){
                                val1.bg_image = imgUrl + val1.bg_image;
                            }
                        })
                    }
                }
                //秒杀活动
                if(val.type=="marketing_active"){
                    if(val['content'].length==0){
                        return;
                    }
                    var seckill_stock = 0;//秒杀库存
                    for(var j = 0;j < val['content'][0]['sku'].length;j ++){
                        seckill_stock += parseInt(val['content'][0]['sku'][j]['seckill_stock']);
                    }
                    if(seckill_stock > 0){
                        val['content'][0]['productStatus'] = false;
                    }else{
                        val['content'][0]['productStatus'] = true;
                    }
                    // console.log(val['content'][0]);
                    var endTime= new Date(val['content'][0]['end_at'].replace(/-/g,'/'));
                    var nowTime = new Date(val['content'][0]['now_at'].replace(/-/g,'/'));
                    var t =endTime.getTime() - nowTime.getTime();
                    if(t <= 0){
                       val['content'][0]["productStatusSrc"] ="static/images/end.png"; 
                       val['content'][0]['productStatus'] = true;//活动结束
                    }else{
                        val['content'][0]["productStatusSrc"] ="static/images/sellOut.png";  
                    }
                    if(val['content'][0].invalidate_at != "0000-00-00 00:00:00"){
                        val['content'][0]['productStatus'] = true;//活动结束
                        val['content'][0]["productStatusSrc"] ="static/images/end.png"; 
                    }
                }
                // 秒杀
                if(val.type == "seckill_list"){
                    var oProto = val.seckillList;
                    var o = val.seckillList;
                    for(var i = 0,l= oProto.length;i<l;i++){
                        o[i].product = oProto[i].product;
                        o[i].seckill_price = oProto[i].seckill.seckill_price;
                        o[i].seckill_oprice = oProto[i].seckill.seckill_oprice;
                        o[i].seckill_sold_num = oProto[i].seckill.seckill_sold_num;
                        o[i].seckill_stock = oProto[i].seckill.seckill_stock;                        
                        o[i].id = oProto[i].seckill.id;
                    }
                    val.killList = o;
                }
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
				//author 华亢 享立减后台缩略图显示
				if(val.type == "share_goods"){
					var activitys = val.activitys
					for(var i = 0; i < activitys.length; i++){
						activitys[i].thumbnail = imgUrl+ activitys[i].thumbnail
					}
				}
                //公告去除 “公告：” updata by 邓钊 2018-8-28
				if(val.type == "notice"){
                    val.content = val.content.replace(/公告：/g, '');
                    val.colorBg = val.colorBg ? val.colorBg : '#ffffcc'
                    val.txtBg = val.txtBg ? val.txtBg : '#ff9900'
              	}
				/**
				 * 商品分组模板不能添加其他组件 
				 * add by 韩瑜 
				 * date 2018-11-28
				 */
				if(val.type == 'group_template'){
                    $scope.is_custom = 0;
                }
				//end
            })
        }
        
    }
    //去除图片域名
    $scope.removeHost = function(src){
        src = src.replace(_host,'');
        src = src.replace(imgUrl,'');
        return src;
    }
    //商品模块，会员卡，优惠券，必须要选择才能保持
    $scope.initModel = function(key){
        $scope.first_card = false;
        $scope.editors[key]['editing'] = 'editing';
        $scope.editors[key]['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        var ele = $('.app-field').eq(key); 
        ele.addClass('editing').css('border','2px dashed rgba(255,0,0,0.5)');
        $('.card_right_list').css('margin-top',ele.offset().top - 89);
        $(document).scrollTop(ele.offset().top - 30);
        $scope.index = key;
        $scope.editors[key].showRight = true;
    }
    var keepGoing = true;//使angular foreach 退出循环  (魔方)
    // 处理模板数据
    $scope.initData = function(data){
        angular.forEach(data,function(val,key){
            if( keepGoing ){//相当于退出循环
                if(val.type == 'goods'){
                   val.goods = [];
                   val.thGoods = [];
                   /**
                    * author 华亢 date 2018/06/29
                    * 商品排序-> 更改商品排序提交后，数据顺序也修改
                   */
                    var arr = [];
                    angular.forEach($scope.editors[key]['goods'],function(val1,key1){
                        arr.push(val1.id)
                    })
                    if(arr.length === 0){
                       msg('请选择商品！',key);
                    }
                    val.products_id = arr;
                }
                if( val.type == 'seckill_list'){
                    if(val.seckillIds.length === 0){
                       msg('请选择秒杀！',key);
                    }
                    val.killList = [];
                    val.seckillList = [];
                }
                if(val.type == 'coupon'){
                    if(val.coupons_id.length === 0){
                       msg('请选择优惠券！',key);
                    }
                    val.couponList = [];
                }
                if(val.type == "image_ad"){
                    if(val.images.length>0){
                        angular.forEach(val.images,function(val1,key1){
                            val1.FileInfo = [];
                            delete val1.id;
                        })
                    }else{
                       msg('请选择图片广告！',key);
                    }
                }
                if(val.type == 'goodslist'){
                    if(!val.group_id){
                        msg('请选择商品列表！',key);
                    }
                    val.goods = [];
                    val.thGoods = [];
                }
                if(val.type == 'image_link'){
                    if(val.images.length>0){
                        angular.forEach(val.images,function(val1,key1){
                            val1.thumbnail = val1.thumbnail.replace(_host,'');
                            val1.thumbnail = val1.thumbnail.replace(imgUrl,'');
                        })
                    }
                    if(!val.images[0]['thumbnail']){
                        msg('请选择图片导航！',key);
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
                if(val.type == "good_group"){
                    if(val.group_type == 1){
                        val.top_nav = [];
                        if(val.left_nav.length === 0){
                            msg('请选择商品分组！',key);
                        }
                    }else if(val.group_type == 2){
                        val.left_nav = [];
                        if(val.top_nav.length === 0){
                            msg('请选择商品分组！',key);
                        }
                    }
                }
                if(val.type == 'bingbing'){
                    val.bg_image = val.bg_image.replace(imgUrl,'');
                    if(val.lists.length>0){
                        angular.forEach(val.lists,function(val1,key1){
                            if(val1.bg_image != ''){
                                val1.bg_image = val1.bg_image.replace(_host,'');
                                val1.bg_image = val1.bg_image.replace(imgUrl,'');
                            }
                            if(val1.icon != ''){
                                val1.icon = val1.icon.replace(_host,'');
                                val1.icon = val1.icon.replace(imgUrl,'');
                            }
                        })
                    }
                }
                // if(val.type == 'marketing_active'){
                //     if(val.content.length > 0){
                //         val.content[0]["product"] = {};
                //         val.content[0]["sku"] = [];
                //     }
                // }
                if(val.type == 'card'){
                    if(val.card_ids.length === 0){
                        msg('请选择会员卡！',key);
                    }
                    // val.cardList = [];
                }
                if(val.type == 'mobile'){
                    angular.forEach(val.lists,function(val1,key1){
                        if(val1.mobile == ''){
                            msg('请填写联系方式！',key);
                        }
                    })
                }
                if(val.type == 'spell_title'){
                    val.default = {};
                    if(val.pages.length){
                        val.default.id = val.pages[0]['id'];
                    }else{
                        val.default.id = 0;
                    }
                }
                //魔方处理数据
                if(val.type == 'cube'){
                    if(val.content.length>0){
                        angular.forEach(val.content,function(val1,key1){
                            if(val1.img != ''){
                                val1.img = val1.img.replace(imgUrl,'');
                            }else{
                                msg('请添加魔方图片！',key);
                                $scope.editors[key].isPromptAddPic = true;
                            }
                        })
                    }
                }
                if(val.type == "share_goods"){
                    if(val.activity_id.length === 0){
                        msg('请选择商品图片！',key);
                    }
                }
            }
        })
    }
    function msg(msg,key){
       $scope.msg = msg;
       $scope.key = key;
       keepGoing = false;
    }
    //添加或修改微页面信息
    $scope.processPage=function(isValid,isshow){
        $scope.iserror = true;
        var is_show = isshow;
        // console.log(isValid);
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
            id=0;
            if(page_template.id!=undefined){
                id=page_template.id;
            }
           
            if(typeof $scope.editors == 'string' && $scope.editors == ''){
                $scope.editors = [];
            }
            $scope.postData = angular.copy($scope.editors);
            keepGoing = true;
            $scope.msg = ''; //模块没有选择数据提示信息
            $scope.key = 0; //没有选择数据模块的索引
            //需后台插入的数据进行初始化 避免垃圾数据
            //信息提示
            $scope.initData($scope.postData);
            angular.forEach($scope.postData,function(val,key){
                if( keepGoing ){//相当于退出循环
                    if(val.type == 'goods'){
                       val.goods = [];
                       val.thGoods = [];
                       /**
                        * author 华亢 date 2018/06/29
                        * 商品排序-> 更改商品排序提交后，数据顺序也修改
                       */
                        var arr = [];
                        angular.forEach($scope.editors[key]['goods'],function(val1,key1){
                            arr.push(val1.id)
                        })
                        val.products_id = arr;
                    }
                    if( val.type == 'seckill_list'){
                        val.killList = [];
                        val.seckillList = [];
                    }
                    if(val.type == 'coupon'){
                        val.couponList = [];
                    }
                    if(val.type == "image_ad"){
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.FileInfo = [];
                                delete val1.id;
                            })
                        }
                    }
                    if(val.type == 'goodslist'){
                        val.goods = [];
                        val.thGoods = [];
                    }
                    if(val.type == 'image_link'){
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.thumbnail = val1.thumbnail.replace(_host,'');
                                val1.thumbnail = val1.thumbnail.replace(imgUrl,'');
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
                    if(val.type == "good_group" || val.type == "group_template"){
                        if(val.group_type == 1){
                            val.top_nav = [];
                        }else if(val.group_type == 2){
                            val.left_nav = [];
                        }
                    }
                    if(val.type == 'bingbing'){
                        val.bg_image = val.bg_image.replace(imgUrl,'');
                        if(val.lists.length>0){
                            angular.forEach(val.lists,function(val1,key1){
                                if(val1.bg_image != ''){
                                    val1.bg_image = val1.bg_image.replace(_host,'');
                                    val1.bg_image = val1.bg_image.replace(imgUrl,'');
                                }
                                if(val1.icon != ''){
                                    val1.icon = val1.icon.replace(_host,'');
                                    val1.icon = val1.icon.replace(imgUrl,'');
                                }
                            })
                        }
                    }
                    if(val.type == 'marketing_active'){
                        if(val.content.length > 0){
                            val.content[0]["product"] = {};
                            val.content[0]["sku"] = [];
                        }
                    }
                    // if(val.type == 'card'){
                    //     val.cardList = [];
                    // }
                    if(val.type == 'spell_title'){
                        val.default = {};
                        if(val.pages.length){
                            val.default.id = val.pages[0]['id'];
                        }else{
                            val.default.id = 0;
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
                    //商品分组模板
                    if(val.type == 'group_page'){
                    	console.log('这里清除数据')
                    }
                }
            })
            //魔方数据不完整则退出
            if(!keepGoing){
                if($scope.msg){
                    tipshow($scope.msg,'warn');
                    $scope.initModel($scope.key)
                }
                $('.btn_grounp button').removeAttr('disabled');
                return;
            }
            // return;
            $scope.postData = JSON.stringify($scope.postData);
            if(id>0){
                $.ajax({
                    type:"post",
                    url:'/merchants/store/updatePage',
                    data:{
                        id:id,
                        page_title:$scope.pageSeting.title,
                        page_description:$scope.pageSeting.desc,
                        page_type:$scope.pageSeting.page_type,
                        page_bgcolor:$scope.pageSeting.page_bgcolor,
                        data:$scope.postData,
                        status:1,
                        is_show:1,
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        is_show:is_show,
                        share_title:$scope.pageSeting.share_title,
                        share_desc:$scope.pageSeting.share_desc,
                        share_img:$scope.pageSeting.share_img ? $scope.removeHost($scope.pageSeting.share_img): '',
                        qq:$scope.pageSeting.qq
                    },
                    success: function(msg){
                        if(msg.errCode==0){
                            tipshow('修改成功！');
                            setTimeout(function(){
                                window.location.href = '/merchants/store';
                            },1000)
                        }else{
                            $('.btn_grounp button').removeAttr('disabled');
                            tipshow('修改失败！','warn');
                        }
                    },
                    error:function(msg){
                        $('.btn_grounp button').removeAttr('disabled');
                        layer.alert('修改失败！');
                    }
                });
                 
            }else{
                $scope.page_type = '';
                if($scope.pageSeting.page_type.length>0){
                    $scope.page_type = '['
                    angular.forEach($scope.pageSeting.page_type,function(val,key){
                        $scope.page_type += val + ',' ;
                    })
                    $scope.page_type = $scope.page_type.substring(0,$scope.page_type.length- 1);
                    $scope.page_type += ']';
                }
                $.ajax({
                    type:"post",
                    url:'/merchants/store/insertPage',
                    data:{
                        page_title:$scope.pageSeting.title,
                        page_description:$scope.pageSeting.desc,
                        page_type:$scope.page_type,
                        page_bgcolor:$scope.pageSeting.page_bgcolor,
                        data:$scope.postData,
                        status:1,
                        is_show:1,
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        template_id:page_template.template_id,
                        share_title:$scope.pageSeting.share_title,
                        share_desc:$scope.pageSeting.share_desc,
                        share_img:$scope.pageSeting.share_img,
                        qq:$scope.pageSeting.qq,
                    },
                    success: function(msg){
                        if(msg.errCode==0){
                            tipshow('添加成功！');
                            setTimeout(function(){
                                window.location.href = '/merchants/store';
                            },1000)
                        }else{
                            tipshow('添加失败！','warn');
                        }
                    },
                    error:function(msg){
                        tipshow('添加失败','warn');
                    }
                });     
            }
        }
    }
    // 预览效果
    $scope.previewPage = function(isValid,isshow){
        $scope.iserror = true;
        var is_show = isshow;
        if(isValid){
            $('.btn_grounp button').attr('disabled','disabled');
            id=0;
            if(page_template.id!=undefined){
                id=page_template.id;
            }
            if(typeof $scope.editors == 'string' && $scope.editors == ''){
                $scope.editors = [];
            }
            $scope.postData = angular.copy($scope.editors);
            keepGoing = true;
            $scope.initData($scope.postData);
            if(!keepGoing){
                $('.btn_grounp button').attr('disabled',false);
                return;
            }
            $scope.postData = JSON.stringify($scope.postData);
            if($scope.pageSeting.share_desc){
                $scope.pageSeting.share_desc = $scope.pageSeting.share_desc.replace(_host,'');
            }
            if(id>0){
                $.ajax({
                    type:"post",
                    url:'/merchants/store/updatePage',
                    data:{
                        id:id,
                        page_title:$scope.pageSeting.title,
                        page_description:$scope.pageSeting.desc,
                        page_type:$scope.pageSeting.page_type,
                        page_bgcolor:$scope.pageSeting.page_bgcolor,
                        data:$scope.postData,
                        status:1,
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        share_title:$scope.pageSeting.share_title,
                        share_desc:$scope.pageSeting.share_desc,
                        share_img:$scope.pageSeting.share_img,
                        qq:$scope.pageSeting.qq
                    },
                    success: function(msg){
                        if(msg.errCode==0){
                            // layer.alert('修改成功！',function(){
                            //     window.location.href = '/merchants/store';
                            // });
                            tipshow('修改成功！');
                            setTimeout(function(){
                                window.location.href = '/shop/page/preview/'+ store.id +'/'+id;
                            },1000)
                        }else{
                            $('.btn_grounp button').removeAttr('disabled');
                            tipshow('修改失败！','warn');
                        }
                    },
                    error:function(msg){
                        $('.btn_grounp button').removeAttr('disabled');
                        layer.alert('修改失败！');
                    }
                });
            }else{
                $scope.page_type = '';
                if($scope.pageSeting.page_type.length>0){
                    $scope.page_type = '['
                    angular.forEach($scope.pageSeting.page_type,function(val,key){
                        // $scope.page_type.push(parseInt(val));
                        $scope.page_type += val + ',' ;
                    })
                    $scope.page_type = $scope.page_type.substring(0,$scope.page_type.length- 1);
                    $scope.page_type += ']';
                }
                $.ajax({
                    type:"post",
                    url:'/merchants/store/insertPage',
                    data:{
                        page_title:$scope.pageSeting.title,
                        page_description:$scope.pageSeting.desc,
                        page_type:$scope.page_type,
                        page_bgcolor:$scope.pageSeting.page_bgcolor,
                        data:$scope.postData,
                        status:1,
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        is_show:0,
                        share_title:$scope.pageSeting.share_title,
                        share_desc:$scope.pageSeting.share_desc,
                        share_img:$scope.pageSeting.share_img,
                        qq:$scope.pageSeting.qq
                    },
                    success: function(msg){
                        if(msg.errCode==0){
                            tipshow('添加成功！');
                            setTimeout(function(){
                                window.location.href = '/shop/page/preview/'+ store.id +'/'+ msg.data;
                            },1000)
                        }else{
                            tipshow('添加失败！','warn');
                        }
                    },
                    error:function(msg){
                        tipshow('添加失败','warn');
                    }
                });
            }
        }
    }
    //添加头部
    $scope.addheader = function(){
        $scope.editors.push({
            'showRight':true,
            'cardRight':17,
            'type':'header',
            'editing':'editing',
            'store_name':'',
            'logo':store.logo,
            'bg_image':'',
            'bg_color':'#EF483F',
            'order_link':'/shop/order/index/'+store.id
        })
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
    /**
     * @author:
     * @description: 上传图片
     * @param {type} 
     * @return: void
     * @Date: 2019-10-25 10:23:08
     * update: 戴江淮（npr5778@dingtalk.com）2019-10-25 增加会员卡图片上传类型
     */
    $scope.upload = function () {
        if ($scope.eventKind == 9) {
            $scope.hideModel();
            getCropper(3, 700/380, function (blob, img_file) {
                cutImgUpload(blob, img_file)
            })
        } else {
            $scope.uploadShow = true;
            $('.webuploader-pick').next('div').css({
                'top': '19px',
                'width': '168px',
                'height': '44px',
                'left':'40%'
            })
        }
    }
    /**
     * @auther 戴江淮（npr5778@dingtalk.com）
     * @param blob 裁剪后的图片资源
     * @param img_file 原始的图片资源
     * @description 图片裁剪后提交到后台
     * @update 2019-10-23 17:50:54
     * @return
     */
    function cutImgUpload (blob, img_file, flag) {
        var formData = new FormData();
        formData.append("file", blob)
        $('.modal-backdrop').hide();
        if (img_file && !flag) {
            formData.append("type", img_file.type)
            formData.append("lastModifiedDate", img_file.lastModifiedDate)
            formData.append("size", img_file.size)
        }
        formData.append("name", img_file.name) // update by 倪凯嘉 上传图片时增加图片名称 2019-09-29
        // formData.append("classifyId", classifyId)
        $.ajax({
            url: '/merchants/myfile/upfile',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function (res) {
                try {
                    var data = JSON.parse(res)
                    if (data.status === 1) {
                        tipshow("图片上传成功！");
                        var pictureSrc = imgUrl + data.data.FileInfo.path;
                        $scope.$apply(function () {
                            $scope.editors[$scope.index].cardList[$scope.advImageIndex]['card_img'] = pictureSrc
                        })
                    } else {
                        tipshow('图片上传失败，请重新上传图片','warm');
                    }
                } catch (err) {
                    tipshow('图片上传失败，请重新上传图片','warm');
                }
            },
            error:function () {
                tipshow('图片上传失败，请重新上传图片','warm');
            }
        })
    }
    //上传确定按钮
    $scope.uploadSureBtn = function() {
        commonServer.chooseAdvSureBtn($scope);
        $('#myModal-adv').hide();
        $('.modal-backdrop').hide();
        closeUploader();
    }
    // 返回选择图片
    $scope.showImage = function(){
        $scope.uploadShow = false; //判断上传可图片model显示
    }
    // 优惠券添加
    $scope.addCoupon = function(position){
       commonServer.addCoupon($scope,position);
    }
    // 删除一个优惠券
    $scope.deleteCoupon = function($index){
        commonServer.deleteCoupon($scope,$index);
    }
    //crmember右侧修改背景图
    $scope.changeBg = function(){
        commonServer.changeBg($scope);
    }
    // 添加商品
    $scope.addgoods = function(position){
        commonServer.addgoods($scope,position);
    } 
    //显示优惠券弹窗
    $scope.showCouponModel = function(){
        commonServer.showCouponModel($scope);
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
        $scope.addPic="+添加图片";
        $scope.classShow=true;
    }
    /** 
     * author 华亢 at 2018/08/13
     * 微页面添加外链（图片广告）-> 添加option 享立减
    */
    //选择享立减商品
    $scope.choose_shareGoods = function($index,position){
        $scope.goods_show=false;
        $scope.link_type=8;
        commonServer.choose_shareGoods($index,position,$scope)
    }
    //选择享立减商品确认
    $scope.choose_shareGoods_sure = function($index,list){
        $scope.link_type=8;
        commonServer.chooseShopLink_shareGoods($index,$scope,list)
    }
    /*
    *@author huoguanghui
    *商品及分类模态框编写
    */
    // 链接选择商品和分类
    $scope.chooseShop = function($index,position){
        commonServer.showShopModel($index,position,$scope);
    }
    //切换商品及分类
    $scope.switchProductNav = function($index){
        commonServer.switchProductNav($index,$scope);
    }
    //选择营销活动搜索
    $scope.searchProductList = function(){
        commonServer.searchProductList($scope);
    }
   
    //图片广告选择微页面链接极其分类弹窗
    $scope.choosePageLink = function($index,position){
        commonServer.choosePageLink($index,position,$scope)
    }
    // 图片广告选择微页面链接确定
    $scope.choosePageLinkSure = function($index,list){
        commonServer.choosePageLinkSure($index,list,$scope);
    }
    //搜索微页面
    $scope.searchPage = function(){
        commonServer.searchPage($scope);
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
        hideModel($('#page_model_shareEvent'));
    }
    //选择商品
    $scope.choose = function($index,list){
        commonServer.choose($index,$scope,list)
    }
    //图片广告选择商品链接
    $scope.chooseShopLink = function($index,list){
        commonServer.chooseShopLink($index,$scope,list)
    }
    // 商品弹窗搜索
    $scope.searchGoods = function(){
        commonServer.searchGoods($scope);
    }
    //选择优惠券
    $scope.chooseCoupon = function($index,list){
        commonServer.chooseCoupon($index,list,$scope);
    }
    //魔方选择优惠券
    $scope.chooseCubeCoupon = function($index,list){
        commonServer.chooseCubeCoupon($index,list,$scope);
    }
    //确定选择商品
    $scope.chooseSure =function(position){
        commonServer.chooseSure($scope,position);
    }
    //营销活动选择弹窗
    $scope.chooseActivity = function($index,position){
        commonServer.chooseActivity($index,position,$scope);
    }
    //营销活动nav切换
    $scope.switchNav = function($index){
        commonServer.switchNav($index,$scope);
    }
    //营销活动选择确定
    $scope.chooseActivitySure = function($index,list){
        commonServer.chooseActivitySure($index,$scope,list);
    }
    //选择营销活动搜索
    $scope.searchActivity = function(){
        commonServer.searchActivity($scope);
    }
    //add by 韩瑜 2018-8-10
    //营销活动新建
    $scope.newActivity = function(){
        commonServer.newActivity($scope);
    }
    //刷新营销活动列表
    $scope.flushActivity = function($index,position){
        commonServer.chooseActivity($index,position,$scope);
    }
    //end
     //微预约添加
    $scope.chooseAppoint = function($index,position){
        commonServer.chooseAppoint($index,position,$scope);
    }
    //微预约添加确定
    $scope.chooseAppointSure = function($index,list){
        commonServer.chooseAppointSure($index,$scope,list);
    }
    //微预约弹窗搜索
    $scope.searchAppoint = function(){
        commonServer.searchAppoint($scope);
    }
    // 选择优惠券确定按钮
    $scope.chooseCouponSure = function(){
        commonServer.chooseCouponSure($scope);
    }
    // 优惠券弹窗搜索
    $scope.searchCoupon = function(){
        commonServer.searchCoupon($scope);
    }
     // 魔方优惠券弹窗搜索
     $scope.searchCubeCoupon = function(){
        commonServer.searchCubeCoupon($scope);
    }
    //显示删除按钮
    $scope.showDelete = function($index){
        commonServer.showDelete($index,$scope);
    }
    //隐藏删除按钮
    $scope.hideDelete = function($index){
        commonServer.hideDelete($index,$scope)
    }
    //显示删除拼团按钮
    $scope.showGroupDelete = function($index){
        // commonServer.showGroupDelete($index,$scope);
        $scope.editors[$scope.index]['groups'][$index]['deleteBtn'] = true;
    }
    //隐藏删除拼团按钮
    $scope.hideGroupDelete = function($index){
        $scope.editors[$scope.index]['groups'][$index]['deleteBtn'] = false;
    }
    //删除图片
    $scope.delete = function($index){
        commonServer.delete($index,$scope);
    }
    //删除拼团图片
    $scope.deleteGroupShop = function($index){
        $scope.editors[$scope.index]['groups_id'].splice($index,1);
        $scope.editors[$scope.index]['groups'].splice($index,1);
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
    var img_index;
    $scope.chooseImage = function(image,$index){
        console.log(image,'------------hhhhhhhhh')
        commonServer.chooseImage(image,$index,$scope);
        img_index = image
    }
    /**
     * @author: 
     * @description: 选择广告图片确定按钮
     * @param {type} 
     * @return: 
     * @Date: 2019-10-24 17:22:30
     * update: 戴江淮（npr5778@dingtalk.com）2019-10-24 会员卡上传封面图片时加上判断
     */
    $scope.chooseAdvSureBtn = function () {
        if ($scope.eventKind == 9) {
            var img_size = img_index.FileInfo.img_size.split('x')
            var url = $scope.tempUploadImage[0]['FileInfo']['path']
            if ((img_size[0] / img_size[1]).toFixed(2) >= 1.83 && (img_size[0] / img_size[1]).toFixed(2) <= 1.85) {
                if ($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl) >= 0) {
                    //去除域名
                    url = url.replace(imgUrl, "")
                }
                $scope.editors[$scope.index].cardList[$scope.advImageIndex]['card_img'] = imgUrl + url
                $scope.hideModel();
            } else {
                console.log('尺寸不对')
                $scope.hideModel();
                getCropper(3, 700/380, function (blob, img_file) {
                    cutImgUpload(blob, img_file)
                }, 1, url, img_index.FileInfo)
            }
        } else {
            commonServer.chooseAdvSureBtn($scope);
        }
    }
    //广告图片重新上传
    $scope.reUpload = function($index){
        commonServer.reUpload($index,$scope);
    }

    //删除广告图片
    $scope.removeAdvImages = function($index){
        commonServer.removeAdvImages($index,$scope);
    }
    // 选择链接
    $scope.chooseLinkUrl = function($event,$index,position,url,linktype){
        commonServer.chooseLinkUrl($event,$index,position,url,linktype,$scope);
    }
    //链接删除
    $scope.removeLink = function($index,position){
        commonServer.removeLink($index,position,$scope);
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
    //自定义link
    $scope.customLink = function($event,$index,position){
       commonServer.customLink($event,$index,position,$scope);
    }

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
    //添加店铺导航
    $scope.addShop = function(position){
        commonServer.addShop($scope,position);
    }
    //公告添加
    $scope.addNotice = function(position){
        commonServer.addNotice($scope,position);
    }
    // 商品添加
    $scope.addSearch = function(position){
        commonServer.addSearch($scope,position);
    }
    //商品列表添加
    $scope.addGoodsList = function(position){
        commonServer.addGoodsList($scope,position);
    }
    // 商品列表选择商品分组
    $scope.position = 0 //记录商品位置1为商品列表，2位商品分组
    $scope.chooseShopGroup = function(position){
        commonServer.addShopGroup($scope,position);
    }
    //商品分组选择确定按钮
    $scope.chooseShopGroupSure = function($index,list){
        commonServer.chooseShopGroupSure($index,list,$scope);
    }
    // 商品分组搜索
    $scope.searchShopGroup = function(){
        commonServer.searchShopGroup($scope);
    }
    // 添加自定义模块
    $scope.addModel = function(position){
        commonServer.addModel($scope,position);
    }
    // 显示自定义弹窗提示
    $scope.showComponentModel = function(){
        commonServer.showComponentModel($scope);
    }
    //自定义模块选择
    $scope.chooseComponent = function($index,list){
        commonServer.chooseComponent($scope,$index,list);
    }
    // 自定义模块搜索
    $scope.searchComponent = function(){
        commonServer.searchComponent($scope);
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
    // 添加图片导航
    $scope.addLinkImages = function(position){
        commonServer.addLinkImages($scope,position);
    }
    // 选择图片导航弹出model
    $scope.chooseLinkImage = function($index){
        commonServer.chooseLinkImage($scope,$index);
    }
    //添加文本导航
    $scope.addtextLink = function(position){
        commonServer.addtextLink($scope,position);
    }
    //添加一个文本链接
    $scope.addOneTextLink = function(){
        commonServer.addOneTextLink($scope);
    }
    //删除一个文本链接
    $scope.deleteOneTextLink = function($index){
        commonServer.deleteOneTextLink($index,$scope);
    }
    /**
     * 魔方功能
     * @author huoguanghui
     * @created 2017年12月27日16:19:50
     */
    /**
     * 拼团外链
     * @author  huoguanghui
     */
    $scope.choose_spells = function($index,position){
        $scope.goods_show=false;
        commonServer.choose_spells($index,position,$scope)
    }
    //拼团链接选取 @author huoguanghui
    $scope.chooseSpell_sure = function($index,list){
        commonServer.chooseSpell_sure($index,list,$scope)
    }
    /**
     * 选择签到活动
     * @author  huoguanghui
     */
    $scope.chooseSign = function(){
        commonServer.chooseSign($scope)
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
    /**
     * 魔方添加商品
     * @param index 函数需要未使用，position 8 代表魔方类型
     */
    $scope.cubeAddProduct = function($index,position){
        commonServer.showShopModel($index,position,$scope);
    }
    /**
     * 魔方添加优惠券
     * @param index 函数需要未使用，position 8 代表魔方类型
     * add buy 黄新琴  2018-07-18
     */
    $scope.cubeAddCoupon = function($index,position){
        commonServer.showCubeCouponModel($index,position,$scope);
    }
   
    /** 
     * 魔方添加微页面
     * @param index 函数需要未使用，position 8 代表魔方类型
     */
    $scope.cubeAddPage = function($index,position){
        micropageModel($index,position,$scope)
    }
    micropageModel =  function($index,position,$scope){
        $scope.temp = [];
        // $scope.pageId = [];
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.link_type = 1;
        var wid = $('#wid').val();
        $.get('/merchants/store/selectPage?page=1', function(data) {
            $scope.pageList = [];
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.page_title,
                        "url":val.url,
                        "created_at":val.create_time
                    })
                })
            })
            
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                // alert(totalCount)
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/store/selectPage?page=' + page,function(response){
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.page_title,
                                            "url":val.url,
                                            "created_at":val.create_time
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#page_model'),$('#page-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
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
    /*
    *todo 自定义组件营销活动模块
    *@author huoguanghui
    *@func 1.添加营销活动
    *@func 2.添加秒杀弹框
    *@func 3.秒杀活动搜索
    *@func 4.秒杀活动选择
    *@func 5.秒杀列表刷新
    *@func 6.删除活动
    */
    //添加营销活动
    $scope.addActive = function(position){
        commonServer.addActive($scope,position);
    }
    //添加秒杀弹框
    $scope.killModelShow = function(position){
        $scope.skillLinkPosition = position;
        commonServer.killModelShow($scope);
    }
    //秒杀活动搜索
    $scope.searchKill = function(){
        commonServer.searchKill($scope);
    }
    //秒杀活动选择
    $scope.chooseKill = function(index,list){
        commonServer.chooseKill(index,list,$scope);
    }
    //秒杀列表刷新
    $scope.killRefresh = function(){
        commonServer.killRefresh($scope);
    }
    //删除活动
    $scope.deleteActive = function(){
        commonServer.deleteActive($scope);
    }
    // 选择秒杀活动
    $scope.chooseKillItem = function($index,list){
        commonServer.chooseKillItem($index,$scope,list)
    }
    // 确认秒杀活动
    $scope.sureKill =function(position){
        commonServer.sureKill($scope,position);
    }
    //显示秒杀删除
    $scope.showKillDelete = function($index){
        $scope.editors[$scope.index]['killList'][$index]['deleteBtn'] = true;
    }
    //隐藏秒杀删除
    $scope.hideKillDelete = function($index){
        $scope.editors[$scope.index]['killList'][$index]['deleteBtn'] = false;
    }
    //删除秒杀活动
    $scope.deleteKillGood = function($index){
        $scope.editors[$scope.index]['seckillIds'].splice($index,1);
        $scope.editors[$scope.index]['killList'].splice($index,1);
    };
    //美妆小店修改店铺logo
    $('#logo_input').change(function(){
        var that = $(this);
        // var reader = new FileReader();
        // reader.readAsDataURL(this.files[0]); 
        // reader.onload = function(e){ 
        //     that.parent().prev().attr('src',this.result);
        // }
        var formData = new FormData();
        formData.append('file', $('#logo_input')[0].files[0]);
        if($('#logo_input')[0].files[0].size > 102400){
                tipshow("图片大小不能超过100k","warn");
                return;
        }
        $.ajax({
            url: '/merchants/myfile/upfile',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res) {
                var id = $('#wid').val();
                res = JSON.parse(res);
                var logo = res.data.FileInfo['path'];
                $scope.$apply(function(){
                    $scope.editors[$scope.index]['logo'] = imgUrl + logo;
                })
                
                $.post('/merchants/currency/index',{id:id,logo:logo,_token:$('meta[name="csrf-token"]').attr('content')},function(data){
                   
                },'json')
            },
            error:function(){

            }
        })
    })
    // 美妆小店选择图片
    //冰冰模板添加
    $scope.addBingBing = function(){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':18, //3为富文本，4商品，5商品列表
            'type':'bingbing',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'chooseLink':false,
            'dropDown':false,
            'linkName':'',//链接名字
            'linkUrl':'',
            'bg_image':'',
            'lists':[
                {title:'3455',linkName:'345435',linkUrl:'dfgfd',icon: _host + 'mctsource/images/01@2x.png',desc:'ertert',bg_image:'',tag:'ryrty',dropDown:false}
            ]
        })
        $scope.initCartRight();//初始化右边
    }
    $scope.changeBg = function(){
        $scope.changeImange = true;
        $scope.choosePage = 3 //冰冰模板冰冰背景修改
        commonServer.changeBg($scope);
    }
    //冰冰模板改变图标
    $scope.changeIcon = function($index){
        $scope.advImageIndex = $index;
        $scope.changeImange = true;
        $scope.choosePage = 4 //冰冰模板冰冰修改图标
        commonServer.changeBg($scope);
    }
    //冰冰模板删除图标
    $scope.deleteIcon = function($index){
        // alert(2);
        $scope.editors[$scope.index]['lists'][$index]['icon'] = '';
    }
    // 改变冰冰模板小图bg
    $scope.changeSmallBg = function($index){
        $scope.advImageIndex = $index;
        $scope.changeImange = true;
        $scope.choosePage = 5 //冰冰模板冰冰修改小图背景
        commonServer.changeBg($scope);
    }
    //删除冰冰模板小图bg
    $scope.deleteSmallBg = function($index){
        $scope.editors[$scope.index]['lists'][$index]['bg_image'] = '';
    }
    // 冰冰模板添加一个小模板
    $scope.addSmallList = function(){
        $scope.editors[$scope.index]['lists'].push({title:'3455',linkName:'345435',linkUrl:'dfgfd',icon: _host + 'mctsource/images/01@2x.png',desc:'ertert',bg_image:'',date:'ryrty'})
    }
    //冰冰模板删除一个小模板
    $scope.deleteSmallList = function($index){
        $scope.editors[$scope.index]['lists'].splice($index,1);
    }
    // 微页面加内容
    $scope.addContent = function(event,$index,editor,top){
        commonServer.addContent(event,$index,editor,$scope,top);
    }
    // 添加图文模板
    $scope.imageTextModel = function(){
        $scope.editors.push({
            'showRight':true,
            'cardRight':20, //3为富文本，4商品，5商品列表
            'type':'imageTextModel',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'slideLists':[],
            'lists':[],
            'width':0,
            'is_add_content':false
        })
    }
    //图文模板选择
    $scope.choose_text_image = function($index,list){
        if($('.choose_btn_'+$index).hasClass('btn-primary')){
            $('.choose_btn_'+$index).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+$index).html('选取'); //改变按钮显示状态
            $scope.temp.splice($scope.temp.indexOf(list),1);//清除数据
        }else{
            $('.choose_btn_'+$index).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+$index).html('取消'); //改变按钮显示状态
            $scope.temp.push(list);//添加数据
        }
    }
    //图文模板选择确定按钮点击
    $scope.choose_text_image_sure = function(){
        if($scope.temp.length){
            if($scope.chooseTextImagePosition == 1){
                angular.forEach($scope.temp,function(val,key){
                    if(val.cover){
                        val.cover = imgUrl + val.cover.substring(1,val.cover.length);
                    }
                    $scope.editors[$scope.index]['slideLists'].push(val);
                })
            }else{
                angular.forEach($scope.temp,function(val,key){
                    if(val.cover){
                        val.cover = imgUrl + val.cover.substring(1,val.cover.length);
                    }
                    $scope.editors[$scope.index]['lists'][$scope.textImageIndex]['lists'].push(val);
                })
            }
        }
        hideModel($('#text_image_model'));
    }
    //删除图文模板幻灯片
    $scope.removeSlide = function($index){
        $scope.editors[$scope.index]['slideLists'].splice($index,1);
    }
    //删除一条图文
    $scope.removeTextImage = function($index,outindex){
        $scope.editors[$scope.index]['lists'][outindex]['lists'].splice($index,1);
    }
    //删除一个图文分类
    $scope.removemenus = function($index){
        $scope.editors[$scope.index]['lists'].splice($index,1);
    }
    //修改图文素材名称
    $scope.menuIndex = 0;
    $scope.changeTiTitle = function(menu,$index){
        $scope.menuIndex = $index;
        $('#changeTitleProver').show();
        $('#changeTitleProver').css('top',$('#menus_'+$index).offset().top-10);
        $('#changeTitleProver').css('left',$('#menus_'+$index).offset().left-$('#changeTitleProver').width()-5);
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
    //官网图文模板
    $scope.chooseTextImagePosition = 1;//1为幻灯片区域选择2,为图文区域选择
    $scope.addTextImageSlide = function(){
        var page = 1;
        $scope.textImageList = [];
        $scope.temp = [];
        $scope.chooseTextImagePosition = 1;
        $http({
            method:'GET',
            url:'/merchants/wechat/materialGetSingle?size=5&page='+page
        }).success(function(data){
            if(data.data.data.length){
                angular.forEach(data.data.data,function(val,key){
                    $scope.textImageList.push(val);
                })
                var totalCount = data.data.page.total, showCount = 10,
                limit = data.data.page.perSize;
                // alert(totalCount)
                $('.myModalPage').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/wechat/materialGetSingle?size=5&page=' + page + '&title' + $scope.searchTitle,function(response){
                            if(response.status == 1){
                                $scope.textImageList = [];
                                angular.forEach(response.data.data,function(val1,key1){
                                    $scope.$apply(function(){
                                        $scope.textImageList.push(val1);
                                    })
                                })
                            }
                        })
                    }
                });
            }
        });
        $('#text_image_model').show();
        $('.modal-backdrop').show();
    }
    // 官网模板添加图文分类
    $scope.textImageIndex = 0;
    $scope.addTextImage = function($index){
        var page = 1;
        $scope.textImageList = [];
        $scope.temp = [];
        $scope.chooseTextImagePosition = 2;
        $scope.textImageIndex = $index;
        $http({
            method:'GET',
            url:'/merchants/wechat/materialGetSingle?size=5&page='+page
        }).success(function(data){
            if(data.data.data.length){
                angular.forEach(data.data.data,function(val,key){
                    $scope.textImageList.push(val);
                })
                var totalCount = data.data.page.total, showCount = 10,
                limit = data.data.page.perSize;
                // alert(totalCount)
                $('.myModalPage').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/wechat/materialGetSingle?size=5&page=' + page,function(response){
                            if(response.status == 1){
                                $scope.textImageList = [];
                                angular.forEach(response.data.data,function(val1,key1){
                                    $scope.$apply(function(){
                                        $scope.textImageList.push(val1);
                                    })
                                })
                            }
                        })
                    }
                });
            }
        })
        $('#text_image_model').show();
        $('.modal-backdrop').show();
    }
    // 图文模板搜索
    $scope.searchTextNews = function(){
        var page = 1;
        $scope.textImageList = [];
        $scope.temp = [];
        $http({
            method:'GET',
            url:'/merchants/wechat/materialGetSingle?size=5&page='+ page + '&title=' + $scope.searchTitle
        }).success(function(data){
            if(data.data.data.length){
                angular.forEach(data.data.data,function(val,key){
                    $scope.textImageList.push(val);
                })
                var totalCount = data.data.page.total, showCount = 10,
                limit = data.data.page.perSize;
                // alert(totalCount)
                $('.myModalPage').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/wechat/materialGetSingle?size=5&page=' + page + '&title=' + $scope.searchTitle,function(response){
                            if(response.status == 1){
                                $scope.textImageList = [];
                                angular.forEach(response.data.data,function(val1,key1){
                                    $scope.$apply(function(){
                                        $scope.textImageList.push(val1);
                                    })
                                })
                            }
                        })
                    }
                });
            }
        })
    }
    //官网模板添加一行图文分类
    $scope.addTextImageKind = function(){
        $scope.editors[$scope.index]['lists'].push({
            'title':'标题',
            'lists':[]
        })
        $scope.editors[$scope.index]['width'] = 100/$scope.editors[$scope.index]['lists'].length;
    }
    //会员卡添加
    $scope.addCard = function(position){
        commonServer.addCard($scope,position);
    }
    //添加会员卡model弹窗
    $scope.showCardModel = function(){
        commonServer.showCardModel($scope);
    }
    //选取会员卡
    $scope.chooseCard = function($index,list){
        commonServer.chooseCard($index,list,$scope);
    }
    //选择会员卡确定
    $scope.chooseCardSure = function(){
        commonServer.chooseCardSure($scope);
    }
    //删除优惠券
    $scope.deleteCard = function($index){
        commonServer.deleteCard($scope,$index);
    }
    /**
     * @author: 戴江淮（npr5778@dingtalk.com）
     * @description: 添加会员卡封面图片
     * @param {number} 索引 [$index] 
     * @return: 
     * @Date: 2019-10-23 16:26:05
     */
    $scope.addCouponImg = function($index) {
        $scope.eventKind = 9;
        commonServer.addCouponImg($scope, $index);
    }
    // 优惠券弹窗搜索
    $scope.searchCard = function(){
        commonServer.searchCard($scope);
    }
    //添加客服QQ弹窗
    $scope.addQQ = function(){
        $scope.QQlist = [];
        $http.get('/merchants/currency/getListForAjax?page='+1+'&title=').success(function(data){
            if(data.data.data.length){
                for(var i =0;i<data.data.data.length;i++){
                    $scope.QQlist.push(data.data.data[i]);
                }   
            }
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
                $('.qq_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/currency/getListForAjax?page='+page+'&title=').success(function(response) {
                            
                            if(response.status ==1){
                                $scope.QQlist = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.QQlist.push(val);
                                })
                            }
                        });
                    }
                });
            // console.log($scope.QQlist);
        })
        showModel($('#qq_model'),$('#qq_model_model_dialog'));     
    }
    //选择QQ
    $scope.chooseQQ = function($index,list){
        $scope.pageSeting.qq = list.qq;
        hideModel($('#qq_model'));
    }
    //改变QQ
    $scope.changeQQ = function(){
        $scope.QQlist = [];
        $http.get('/merchants/currency/getListForAjax?page='+1+'&title=').success(function(data){
            if(data.data.data.length){
                for(var i =0;i<data.data.data.length;i++){
                    $scope.QQlist.push(data.data.data[i]);
                }   
            }
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
                $('.qq_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/currency/getListForAjax?page='+page+'&title=').success(function(response) {
                            
                            if(response.status ==1){
                                $scope.QQlist = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.QQlist.push(val);
                                })
                            }
                        });
                    }
                });
            // console.log($scope.QQlist);
        })
        showModel($('#qq_model'),$('#qq_model_model_dialog'));
    }
    //搜索QQ
    $scope.searchQQ = function(){
        $scope.QQlist = [];
        $http.get('/merchants/currency/getListForAjax?page='+1+'&qq='+$scope.searchTitle).success(function(data){
            if(data.data.data.length){
                for(var i =0;i<data.data.data.length;i++){
                    $scope.QQlist.push(data.data.data[i]);
                }   
            }
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
                $('.qq_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/currency/getListForAjax?page='+page+'&qq='+$scope.searchTitle).success(function(response) {
                            
                            if(response.status ==1){
                                $scope.QQlist = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.QQlist.push(val);
                                })
                            }
                        });
                    }
                });
            // console.log($scope.QQlist);
        })
        showModel($('#qq_model'),$('#qq_model_model_dialog'));
    }
    //拼团商品列表添加
    $scope.addSpellGoods = function(position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':22, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'spell_goods',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'groups':[],
                    'groups_id':[],
                    'style':1,
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':22, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'spell_goods',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'groups':[],
                'groups_id':[],
                'style':1,
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    $scope.isSpell = true
    //图片广告拼团
    $scope.chooseSpellModel = function($index,position){
        $scope.temp = [];
        // $scope.pageId = [];
        $scope.pageList = [];
        $scope.searchTitle = '';
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.link_type = 1;
        var wid = $('#wid').val();
        $.get('/merchants/grouppurchase/groupList?pageSize=6', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.title,
                        "url": host + 'shop/grouppurchase/detail/'+val.id+'/'+wid,
                        "created_at":val.created_at
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/grouppurchase/groupList?page='+page+'&pageSize=6',function(response){
                        if(response.status == 1){
                            $scope.pageList = [];
                            angular.forEach(response.data[0].data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "url": host + 'shop/grouppurchase/detail/'+val.id+'/'+wid,
                                        "created_at":val.created_at
                                    })
                                })
                            })
                        }
                    })
                }
            });
            showModel($('#page_model_pintuan'),$('#page-dialog_pintuan'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    //图片广告拼团确定
    $scope.chooseSpellModelSure = function($index,list){
        commonServer.chooseSpellModelSure($index,list,$scope);
    }
    //选择拼团列表model
    $scope.showSpellModel = function(){
        $scope.goods_show = true;
        $http.get('/merchants/grouppurchase/groupList?pageSize=6').then(function(data){
            
            if(data.data.data[0].data.length){
                $scope.spellGoodList = [];  
                angular.forEach(data.data.data[0].data,function(val,key){
                    $scope.spellGoodList.push(val);
                })
            }
            // console.log(data.data);
             var totalCount = data.data.data[0].total, showCount = 10,
                limit = data.data.data[0].per_page;
                $('.spell_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/grouppurchase/groupList?page='+page+'&pageSize=6').success(function(data) {
                           $scope.spellGoodList = [];  
                            angular.forEach(data.data[0].data,function(val,key){
                                $scope.spellGoodList.push(val);
                            })
                        });
                    }
                });
            showModel($('#spell_Modal'),$('#spell-modal-dialog'));
        })
    }
    //选择拼团列表
    $scope.chooseSpell = function($index,list){
        if($('.choose_btn_'+list.id).hasClass('btn-primary')){
            $('.choose_btn_'+list.id).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('选取'); //改变按钮显示状态
            angular.forEach($scope.temp,function(val,key){
                if(val.id == list.id){
                    $scope.temp.splice(key,1);//清除数据
                }
            })
        }else{
            $('.choose_btn_'+list.id).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('取消'); //改变按钮显示状态
            $scope.temp.push({'id':list.id,'name':list.title,'rectangle_image':list.img,'square_image':list.img2,'price':list.min,'groups_num':list.groups_num,'label':list.label,'subtitle':list.subtitle});//添加数据
        }
    }
    //选择拼团商品确定
    $scope.chooseSpellSure = function(){
         hideModel($('#spell_Modal'));//隐藏Model
        if($scope.advsNum == 5){
            $scope.advsNum = null
            // console.log($scope.temp);
            commonServer.choosePageLinkSure(1,$scope.temp,$scope)
        }else{
            if($scope.temp.length>0){
                // $scope.editors[$scope.index].products_id = [];
                // var num = $scope.editors[$scope.index]['goods'].length;//记录删除唯一标识
                for(var i=0;i<$scope.temp.length;i++){
                    // num ++;
                    $scope.editors[$scope.index].groups_id.push($scope.temp[i].id);
                    // $scope.temp[i]['delete_id'] = num;
                    $scope.editors[$scope.index]['groups'].push($scope.temp[i]);//合并数组
                }
            }
        }
        $scope.temp = [];//去除数据
    }
    //拼团分类添加
    $scope.addSpellTitle = function(position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':23, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'spell_title',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'pages':[],
                    'pages_id':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':23, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'spell_title',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'pages':[],
                'pages_id':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //拼团分类选择弹窗
    $scope.showSpellTitleModel = function(){
        $scope.temp = [];
        // $scope.pageId = [];
        $scope.pageList = [];
        $scope.searchTitle = '';
        var wid = $('#wid').val();
        $.get('/merchants/xcx/micropage/select?page=1', function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.title,
                        "url":val.url,
                        "created_at":val.create_time
                    })
                })
            })
            
            
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                // alert(totalCount)
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/xcx/micropage/select?page=' + page,function(response){
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.page_title,
                                            "url":val.url,
                                            "created_at":val.created_at
                                        })
                                    })
                                })
                                // console.log($scope.pageList);
                            }
                        })
                    }
                });
            showModel($('#page_spell_model'),$('#page-spell-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    //选择拼团分类微页面
    $scope.chooseSpellPageLinkSure = function($index,list){
        if($('.choose_btn_'+list.id).hasClass('btn-primary')){
            $('.choose_btn_'+list.id).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('选取'); //改变按钮显示状态
            angular.forEach($scope.temp,function(val,key){
                if(val.id == list.id){
                    $scope.temp.splice(key,1);//清除数据
                }
            })
        }else{
            $('.choose_btn_'+list.id).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('取消'); //改变按钮显示状态
            $scope.temp.push({'id':list.id,'name':list.name});//添加数据
        }
    }
    //选择拼团分类微页面确定
    $scope.chooseSpellPageSure = function(){
        hideModel($('#page_spell_model'));//隐藏Model
        if($scope.temp.length>0){
            // $scope.editors[$scope.index].products_id = [];
            // var num = $scope.editors[$scope.index]['goods'].length;//记录删除唯一标识
            for(var i=0;i<$scope.temp.length;i++){
                $scope.editors[$scope.index]['pages'].push($scope.temp[i]);//合并数组
                $scope.editors[$scope.index]['pages_id'].push($scope.temp[i]['id']);
            }
        }
        $scope.temp = [];//去除数据
    }
    //拼团分类微页面选择删除微页面
    $scope.deleteSpellPage = function($index){
        $scope.editors[$scope.index]['pages'].splice($index,1);
        $scope.editors[$scope.index]['pages_id'].splice($index,1);
    }
    //拼团商品搜索
    $scope.searchSpell = function(){
        commonServer.searchSpell($scope);
    }
    /**
     * 视频模块
     * @author huoguanghui
     */
    //新增视频模块
    $scope.addVideo =function(position){
        commonServer.addVideo($scope,position);
    };
    //打开视频弹框
    $scope.openVideoModel = function(){
       commonServer.openVideoModel($scope);
    }
    //隐藏视频弹框
    $scope.hideVideoModel = function(){
        commonServer.hideVideoModel($scope);
    }
    //选择视频
    $scope.checkedVideoItem = function(item,index){
        commonServer.checkedVideoItem($scope,item,index)
    }
    //确认试用视频
    $scope.sureUseVideo = function(){
        commonServer.sureUseVideo($scope);
    }
    //切换视频分组
    $scope.switchVideoGroup = function(item,index){
        commonServer.switchVideoGroup($scope,item,index);
    }
    //搜索功能
    $scope.videoSearch = function(e){
        commonServer.videoSearch(e);
    }
    //上传视频
    $scope.uploadVideo = function(){
        $scope.isEditor = false;
        initUploadVideo();
        $('.upload_video').show();
        $('.zent-dialog-r-backdrop').show();
    }
    //关闭上传视频弹窗
    $scope.closeUploadVideo = function(){
        $('.upload_video').hide();
        $('.zent-dialog-r-backdrop').hide();
    }
    $('#upload_video').change(function(e){
        var formData = new FormData(),
            sucFormData = {};
        var size = ($(this)[0].files[0]['size']/1204/1024).toFixed(1);
        if(size>30){
            tipshow('上传文件不能大于30M','warn');
            return;
        }
        formData.append('file', $(this)[0].files[0]);
        formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
        formData.append('file_mine', 2);
        $('.rc-video-upload__progress-item-detail-name').html($(this)[0].files[0]['name']);
        $('input[name="video_name"]').val($(this)[0].files[0]['name']);
        if($(this)[0].files[0]['name'].length>10){
            $('.video_name').addClass('has-error');
        }
        // formData.append('file_mine', 2);
        var filename = hex_md5(new Date().getTime() + parseInt(10000*Math.random())+$(this)[0].files[0]['type'].split("/")[0]) + '.' + $(this)[0].files[0]['type'].split("/")[1];
        // alert(SparkMD5(filename));
        sucFormData['path'] = filename;
        sucFormData['type'] = $(this)[0].files[0]['type'];
        sucFormData['size'] = $(this)[0].files[0]['size'];
        sucFormData['_token'] = $('meta[name="csrf-token"]').attr('content'); 
        $('.add_video').hide();
        $.get('/merchants/store/getVideoSign',{save_key:filename},function(data){
            if(typeof data == 'string'){
                data = JSON.parse(data);
            }
            formData.append('policy', data.policy);
            formData.append('authorization', data.authorization);
            // formData.append('save-key', '/huisoucn/upload/test.mp4');
            $.ajax({
                url: 'https://v0.api.upyun.com/huisoucn',
                type: 'POST',
                data: formData,
                dataType:'json',
                cache: false,
                processData: false,
                contentType: false,
                xhr:   function() {
                  var xhr = $.ajaxSettings.xhr(e);
                  if(xhr.upload){
                    $('.rc-video-upload__progress').show();
                    xhr.upload.onprogress = function (e) {
                        var position = e.position || e.loaded, total = e.totalSize || e.total;
                        // var total = event.total,
                        // position = event.loaded || event.position,
                        percent = '';
                        var html = '已上传：' + (position/1204/1024).toFixed(2) + '/' + '共' + (total/1204/1024).toFixed(2) + 'MB';
                        $('.rc-video-upload__progress-item-detail-total').html(html);
                      
                    if(event.lengthComputable){
                        // console.log(position);
                        percent = Math.ceil(position / total * 100)
                        if(percent == 100 && $('input[name="aggree_input"]').is(':checked') && !$('.video_name').hasClass('has-error')){
                            $('.zent-btn').removeAttr('disabled');
                            $('.zent-btn').addClass('zent-btn-primary')
                        }
                        $('.rc-video-upload__progress-item-progress').css('width',percent + '%');
                      }
                    };
                  }
                  return xhr;
                }
            }).done(function(data, textStatus) {
                // console.log(data);
                $.post('/merchants/myfile/setUpxVideo',sucFormData,function(data){
                    if(typeof data == 'string'){
                        data = JSON.parse(data);
                    }
                    $scope.video = data.data;
                    $scope.video['FileInfo']['path'] = videoUrl + $scope.video['FileInfo']['path'];
                    $('input[name="id"]').val(data.data.id);
                    $('input[name="video_url"]').val(data.data.FileInfo.path);
                })
                // alert('upload success');
            }).fail(function(res, textStatus, error) {
                try {
                    var body = JSON.parse(res.responseText);
                    alert('error: ' + body.message);
                } catch(e) {
                    // console.error(e);
                }
            });
        })
    })
    //重新上传视频
    $scope.reUploadVideo = function(){
        $('.rc-video-upload__progress').hide();
        $('input[name="video_name"]').val('');
        $('.add_video').show();
        $('#upload_video').val('');
        $('.video_name').removeClass('has-error');
        $('.rc-video-upload__progress-item-progress').css('width', '0px');
    }
    // //获取分组
    $.get('/merchants/myfile/getClassify',{file_mine:2},function(data){
        if(data.data.length){
            for(var i=0;i<data.data.length;i++){
                $scope.$apply(function(){
                    $scope.grounps.push(data.data[i]);
                })
            }
        }
    })
    //同意点击
    $('input[name="aggree_input"]').click(function(){
        if(!$scope.isEditor){
            if($(this).is(':checked') && $('#upload_video')[0].files[0] && !$('.video_name').hasClass('has-error')){
                btnStatus(2);
            }else{
                btnStatus(1);
            }
        }else{
            if($(this).is(':checked') && !$('.video_name').hasClass('has-error')){
                btnStatus(2);
            }else{
                btnStatus(1);
            }
        }
        
    })
    //商品名称失焦
    $('input[name="video_name"]').keydown(function(){
        if($(this).val().length>10){
            $('.video_name').addClass('has-error');
            btnStatus(1);
        }else{  
            $('.video_name').removeClass('has-error');
            if(!$scope.isEditor){
                if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                    btnStatus(2);
                }
            }else{
                if($('input[name="aggree_input"]').is(':checked')){
                    btnStatus(2);
                }
            }
        }
    })
    //商品名称聚焦
    $('input[name="video_name"]').focus(function(){
        if($(this).val().length<10){
            if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                btnStatus(2);
            }
        }
    })
    function btnStatus(type){
        //type1为禁止点击2为允许点击
        if(type == 1){
            $('.zent-btn').attr('disabled','disabled');
            $('.zent-btn').removeClass('zent-btn-primary');
            $('.zent-btn').addClass('zent-btn-disabled');
        }else if(type == 2){
            $('.zent-btn').removeAttr('disabled','disabled');
            $('.zent-btn').addClass('zent-btn-primary');
            $('.zent-btn').removeClass('zent-btn-disabled');
        }
    }
    //封面修改 
    // by 崔源 2018.10.23
    $('#upload_image').change(function(e){
        var formData = new FormData();
        var reader = new FileReader();
        reader.readAsDataURL(this.files[0]);
        formData.append('file', $(this)[0].files[0]);
        formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
        formData.append('file_mine', 1);
		if(this.files[0].size > 3145728){
			tipshow("图片不能超过3M","warn");
			return;
		}
        reader.onload = function(e){
            var image = new Image();
            image.src = e.target.result;
			image.onload=function(){
				if (image.width>=310) {
                    $.ajax({
                        url:'/merchants/myfile/upfile',
                        type: 'POST',
                        cache: false,
                        processData: false,
                        dataType:'json',
                        data: formData,
                        contentType: false
                    }).done(function(res) {                  
                        //拼接返回的视频地址，这里的vframe/jpb/offset/1是七牛的视频截取图片的接口          
                        // $('.v-box').find('img').attr('src',vistUrl+res.hash+'?vframe/jpg/offset/1').show();                    
                        // $('#videoForm').val(''); 
                        $('.image_views').css('display','inline-block');
                        $('.image_views img').attr('src',imgUrl + res.data.FileInfo.path);              
                        $('input[name="image_url"]').val(res.data.FileInfo.path);
                        $('.add-goods1 i').html('修改图片');
                        $('.add-goods1').addClass('add-goods2').removeClass('add-goods1');
                    }).fail(function(res) {
                        // console.log(res);
                    });
                }else{
                    tipshow("图片尺寸不符合，请重新上传图片","warn");
							return;
                }
        }}
        // var size = $(this)[0].files[0]['size']/1204/1024.toFixed(1);
        // console.log($(this)[0].files[0]);   
    })
    //保存视频表单
    $('.video_btn').click(function(){
        var formData = {};
        formData.name = $('input[name="video_name"]').val();
        formData.id = $('input[name="id"]').val();
        formData.classifyId = $('select[name="grounp"]').val();
        formData.file_cover = $('input[name="image_url"]').val();
        formData._token = $('meta[name="csrf-token"]').attr('content');
        // if($scope.isEditor){
        //     formData.id = $('input[name="id"]').val();
        // }
        $.post('/merchants/myfile/modifyVedio',formData,function(data){
            if(data.status == 1){
                tipshow(data.info);
                $('.upload_video').hide();
                $('.zent-dialog-r-backdrop').hide();
                // console.log($scope.video);
                if($scope.isEditor){
                    //编辑
                    angular.forEach($scope.videos,function(val,key){
                        if(val.id == $scope.video.id){
                            $scope.$apply(function(){
                                val.FileInfo.name = formData.name;
                                if(formData.file_cover.indexOf(imgUrl) != -1){
                                    val.file_cover = formData.file_cover;
                                }else{
                                    val.file_cover = imgUrl + formData.file_cover;
                                }
                            })
                        }
                    });
                }else{
                    // return;
                    //新增
                    $scope.active_id = 0;
                    angular.forEach($scope.grounps,function(val,key){
                        if(val.isactive){
                            $scope.active_id = val.id
                        }
                    })
                    // alert($scope.active_id);
                    // alert($scope.video.file_classify_id);
                    if($scope.active_id != $('select[name="grounp"]').val()){
                        return;
                    }
                }
            }else{
                tipshow(data.info,'warn');
            }
        })
    })
    //初始化上传
    function initUploadVideo(){
        $('.rc-video-upload__progress').hide();
        $('input[name="video_name"]').val('');
        $('.add_video').show();
        $('#upload_video').val('');
        $('.image_views').css('display','none');
        $('.image_views img').attr('src','');
        $('input[name="aggree_input"]').removeAttr("checked");
        $('.video_name').removeClass('has-error');
        $('.zent-btn').removeClass('zent-btn-primary');
        $('.zent-btn').attr('disabled');
        $('.rc-video-upload__progress-item-progress').css('width', '0px');
    }
    /**
     * 视频模块  end
     */
    // 拼团商品拖动
    $scope.onDropComplete = function (index, obj, evt) {
        if(obj.cardRight != undefined || obj.type != undefined ){
            return;
        }
        var otherObj = $scope.editors[$scope.index]['groups'][index];
        var otherIndex = $scope.editors[$scope.index]['groups'].indexOf(obj);
        $scope.editors[$scope.index]['groups'][index] = obj;
        $scope.editors[$scope.index]['groups'][otherIndex] = otherObj;

        var otherObj1 = $scope.editors[$scope.index]['groups_id'][index];
        var otherIndex1 = $scope.editors[$scope.index]['groups_id'].indexOf(obj.id);
        $scope.editors[$scope.index]['groups_id'][index] = obj.id;
        $scope.editors[$scope.index]['groups_id'][otherIndex] = otherObj1;
    }
    //商品组件拖动
    $scope.onDropShopComplete = function(index, obj, evt){
        if(obj.cardRight != undefined || obj.type != undefined){
            return;
        }
        var otherObj = $scope.editors[$scope.index]['goods'][index];
        var otherIndex = $scope.editors[$scope.index]['goods'].indexOf(obj);
        $scope.editors[$scope.index]['goods'][index] = obj;
        $scope.editors[$scope.index]['goods'][otherIndex] = otherObj;
        var editor = $scope.editors[$scope.index];
        editor.thGoods = [];
        angular.forEach($scope.editors[$scope.index]['goods'],function(val,key){
            if(editor.thGoods.length > 0){
                if(editor.thGoods[editor.thGoods.length - 1].length>=3){
                    editor['thGoods'].push([]);
                    editor['thGoods'][editor.thGoods.length-1].push(val)
                }else{
                    editor.thGoods[editor.thGoods.length - 1].push(val)
                }
            }else{
                editor.thGoods[0] = [];
                editor.thGoods[0].push(val)
            }
        })
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
    // 图片广告选择外链
    $scope.changeWaiLink = function($index,position,$event){
        commonServer.changeWaiLink($index,position,$event,$scope);
    }
    // 图片广告选择自定义链接取消
    $scope.cancelSetLink = function(){
        commonServer.cancelSetLink();
    }
    // 图片广告选择自定义链接确定
    $scope.sureSetLink = function(){
        commonServer.sureSetLink($scope);
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
    //添加留言板
    $scope.addResearch = function (position) {
        commonServer.addResearch($scope,position)
    }
    //添加投票活动
    $scope.addResearchVote = function (position) {
        commonServer.addResearchVote($scope,position)
    }
    //添加报名活动
    $scope.addResearchSign = function (position) {
        commonServer.addResearchSign($scope,position)
    }
    //添加预约活动
    $scope.addResearchAppoint = function (position) {
        commonServer.addResearchAppoint($scope,position)
    }
    /** 
     * author 华亢 at 2018/08/28
     * toDo local - create The activity of secondkill
    */
    $scope.addSecondKill = function (position) {
        commonServer.addSecondKill($scope,position)
    }
    //显示留言活动列表
    // type：0->报名 1->预约 2-> 投票
    $scope.showResearchModel = function (position,type) {
        commonServer.showResearchModel($scope,position,type)
    }
    //选取留言活动
    $scope.chooseResearch = function (index,list) {
        commonServer.chooseResearch(index,list,$scope)
    }
    //删除留言活动
    $scope.deleteResearch = function () {
        commonServer.deleteResearch($scope)
    }
    //搜索留言
    $scope.searchRes = function () {
        commonServer.searchRes($scope)
    }
    //add by 韩瑜 2018-9-13
    //清空搜索框内文字
    $scope.clearsearch = function () {
        commonServer.clearsearch($scope)
    }
    //清空搜索框背景颜色
    $scope.clearcolor = function () {
        commonServer.clearcolor($scope)
    }
    /* 2018-9-17
     * 商品分组模板右侧banner添加图片
     */
    $scope.GroupAddPic = function(p_index){
        commonServer.addAdvs($scope,p_index,0);//打开弹框
        $scope.eventKind = 7;//设置添加图片的类型
    }
    //商品分组模板右侧item添加图片
    $scope.GroupAddItemPic = function(p_index,$index){
        commonServer.addAdvs($scope,p_index,$index);//打开弹框
        $scope.eventKind = 8;//设置添加图片的类型
    }
    //添加大分类
    $scope.addBigGroup = function(){
        commonServer.addBigGroup($scope);
    }
    //添加小分类
    $scope.addSmallGroup = function($index){
        commonServer.addSmallGroup($scope,$index);
    }
    //商品分组模板显示微页面弹窗
    $scope.GroupChoosePageLink = function(p_index,$index,position){
        commonServer.GroupChoosePageLink($scope,p_index,$index,position);
    }
    //商品分组模板显示商品弹窗
    $scope.GroupChooseShop = function(p_index,$index,position){
        commonServer.GroupChooseShop($scope,p_index,$index,position);
    }
    //商品分组模板显示优惠券弹窗
    $scope.GroupCouponModel = function(p_index,$index,position){
        commonServer.GroupCouponModel($scope,p_index,$index,position);
    }
    //商品分组模板显示秒杀弹窗
    $scope.GroupKillModel = function(p_index,$index,position){
        commonServer.GroupKillModel($scope,p_index,$index,position);
    }
    //商品分组模板显示拼团弹窗
    $scope.GroupSpellModel = function(p_index,$index,position){
        commonServer.GroupSpellModel($scope,p_index,$index,position);
    }
    //商品分组模板选择享立减链接
    $scope.GroupShareEventModel = function(p_index,$index){
        commonServer.GroupShareEventModel($scope,p_index,$index);
    }
    //商品分组模板选择享立减链接确定
    $scope.GroupShareEventSure = function($index,list){
        commonServer.GroupShareEventSure($scope,$index,list);
    }
    //商品分组模板显示营销活动弹窗
    $scope.GroupChooseActivity = function(p_index,$index,position){
        commonServer.GroupChooseActivity($scope,p_index,$index,position);
    }
    //商品分组模板选择店铺主页、会员主页、购物车链接
    $scope.GroupLinkUrl = function($event,p_index,$index,url,type){
        commonServer.GroupLinkUrl($event,p_index,$index,url,type,$scope);
    }
    //商品分组模板选择外链
    $scope.GroupOutLink = function(p_index,$index,$event){
        commonServer.GroupOutLink(p_index,$index,$event,$scope);
    }
    //商品分组模板选择外链确定
    $scope.GroupSureSetLink = function(){
        commonServer.GroupSureSetLink($scope);
    }
    //商品分组模板删除链接
    $scope.deleteGroupLink = function(p_index,$index){
        commonServer.deleteGroupLink($scope,p_index,$index);
    }
    //商品分组模板左侧分类点击
    $scope.leftNav = function(list,obj,$index){
    	$scope.navindex = $index
    	if(obj.length>0){
			for(var i = 0;i<obj.length;i++){
				obj[i]['isActive'] = false;
			}
        }
    	list.isActive = true
    }
    //左侧分类首个高亮
    for(i=0;i<$scope.editors.length;i++){
    	if($scope.editors[i].type == 'group_page'){
    		$scope.editors[i].classifyList[0].isActive = true
    	}
    }
    //end
    //add by 韩瑜 2018-9-14
    //商品分组模板页删除分类
    $scope.delBigGroup = function($index){
    	$scope.editors[$scope.index]['classifyList'].splice($index,1);
    	$scope.p_index = $index
    	for(i=0;i<$scope.editors.length;i++){
	    	if($scope.editors[i].type == 'group_page' && $scope.editors[i].classifyList[0]){
	    		$scope.editors[i].classifyList[0].isActive = true
	    	}
	    }
    }
    //商品分组模板页删除分类内元素
    /**
     * @description: 删除内元素分类下标改为传参进来的key
     * @update: 倪凯嘉（nikaijia@dingtalk.com）2020-02-29
     */  
    $scope.delSmallGroup = function(key,$index){
    	$scope.editors[$scope.index]['classifyList'][key]['subClassifyList'].splice($index,1);
    }
    //end
    /**
     * author huakang date 2018/6/19
     * @params position:位置信息
     */
    //享立减商品添加
    $scope.addShareGoods = function(position){
    	$scope.removeClassEditing();        
        if(position == 1){
            $scope.editors.push(
                {   
                    'showRight':true,
                    'cardRight':27, //3为富文本，4商品，5商品列表
                    'type':'share_goods',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'listStyle':4, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                    'cardStyle':1,
                    'btnStyle':1, //分四种情况
                    'goodName':false, //默认不显示
                    'goodInfo':false,
                    'priceShow':true,//默认显示
                    'nodate':true,
                    'activitys':[],
                    'thGoods':[
                        [
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥1'
                            },
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥2'
                            },
                            {
                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                'name':'这里显示商品名称',
                                'info':'这里显示商品通知',
                                'price':'￥3'
                            }
                        ]
                    ],
                    'activity_id':[],
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{   
                'showRight':true,
                'cardRight':27, //3为富文本，4商品，5商品列表
                'type':'share_goods',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'listStyle':4, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                'cardStyle':1,
                'btnStyle':1, //分四种情况
                'goodName':false, //默认不显示
                'goodInfo':false,
                'priceShow':true,//默认显示
                'nodate':true,
                'activitys':[],
                'thGoods':[
                    [
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'info':'这里显示商品通知',
                            'price':'￥1'
                        },
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'info':'这里显示商品通知',
                            'price':'￥2'
                        },
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'info':'这里显示商品通知',
                            'price':'￥3'
                        }
                    ]
                ],
                'activity_id':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();//初始化右边
    }
   
    /** 
     * update at 2018/08/13 by 华亢
     * 外链享立减需要一个参数传递
    */
    //添加享立减商品弹窗
    $scope.showShareModel = function(){ 
    	$scope.goods_show = true;
        $scope.searchTitle = '';
        $scope.goodList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page=1&title='+ $scope.searchTitle, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.goodList.push({
                        "id":val.id,
                        "buttonTitle":val.button_title,
                        "subtitle":val.subtitle,
                        "title":val.title,
                        "name":val.title,
                        "thumbnail":imgUrl + val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_time,
                        "product_id":val.product_id,
                        "url":'/shop/product/detail/'+val.wid+'/'+val.product_id+'?activityId='+val.id
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.share_good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                // console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.goodList.push({
                                            "id":val.id,
                                            "buttonTitle":val.button_title,
                                            "subtitle":val.subtitle,
                                            "title":val.title,
                                            "name":val.title,
                                            "thumbnail":imgUrl + val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_time,
                                            "product_id":val.product_id,
                                            "url":'/shop/product/detail/'+val.wid+'/'+val.product_id+'?activityId='+val.id  
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#shareGoodModel'),$('#share-modal-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    //享立减商品选择确定
    $scope.chooseShareGoodSure = function(){
        hideModel($('#shareGoodModel'));//隐藏Model
        if($scope.editors[$scope.index]['nodate']==true){
            $scope.editors[$scope.index]['thGoods'] = [];
            $scope.editors[$scope.index]['activitys'] = [];
        }
        if($scope.temp.length>0){
            // $scope.editors[$scope.index].products_id = [];
            // var num = $scope.editors[$scope.index]['goods'].length;//记录删除唯一标识
            for(var i=0;i<$scope.temp.length;i++){
                // num ++;
                $scope.editors[$scope.index].activity_id.push($scope.temp[i].id);
                // $scope.temp[i]['delete_id'] = num;  
                if($scope.editors[$scope.index]['activitys'].length == 0){
                    $scope.editors[$scope.index]['thGoods'] = [];
                }
                if($scope.editors[$scope.index]['thGoods'].length != 0){
                    if($scope.editors[$scope.index]['thGoods'][$scope.editors[$scope.index]['thGoods'].length-1].length<3){
                        $scope.editors[$scope.index]['thGoods'][$scope.editors[$scope.index]['thGoods'].length-1].push($scope.temp[i]);
                    }else{
                        $scope.editors[$scope.index]['thGoods'].push([$scope.temp[i]]);
                    }
                }else{
                    $scope.editors[$scope.index]['thGoods'].push([$scope.temp[i]]);
                }
                $scope.editors[$scope.index]['activitys'].push($scope.temp[i]);//合并数组
            }
        }
        $scope.temp = [];//去除数据
        $scope.editors[$scope.index]['nodate'] = false;
    }
    //享立减商品搜索
    $scope.searchShareGoods = function(){
        // $scope.searchTitle = '';
        $scope.goodList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page=1&title='+ $scope.searchTitle, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.goodList.push({
                        "id":val.id,
                        "name":val.title,
                        "thumbnail":imgUrl + val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_time,
                        "product_id":val.product_id,
                        "url":'/shop/product/detail/'+val.wid+'/'+val.product_id+'?activityId='+val.id
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.share_good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page='+page+'&title='+ $scope.searchTitle,function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                // console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.goodList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":imgUrl + val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_time,
                                            "product_id":val.product_id,
                                            "url":'/shop/product/detail/'+val.wid+'/'+val.product_id+'?activityId='+val.id
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#shareGoodModel'),$('#share-modal-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    //显示享立减商品删除
    $scope.showShareDelete = function($index){
        $scope.editors[$scope.index]['activitys'][$index]['deleteBtn'] = true;
    }
    //隐藏享立减商品删除
    $scope.hideShareDelete = function($index){
        $scope.editors[$scope.index]['activitys'][$index]['deleteBtn'] = false;
    }
    //删除享立减商品
    $scope.deleteShareGood = function($index){
        $scope.editors[$scope.index]['activity_id'].splice($index,1);
        $scope.editors[$scope.index]['activitys'].splice($index,1);
    };
    //图片导航添加会员卡
    $scope.chooseMen = function ($index,position) {
        $scope.temp = [];
        $scope.pageList = [];
        $scope.searchTitle = '';
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.link_type = 1;
        var wid = $('#wid').val();
        $.get('/merchants/microPage/memberCard?page=1', function (data) {
            // console.log(data);
            angular.forEach(data.data.data, function (val, key) {
                $scope.$apply(function () {
                    $scope.pageList.push({
                        "id": val.id,
                        "name": val.title,
                        "url": host + 'shop/member/detail/' + wid + '/' + val.id,
                        "power_desc": val.power_desc
                    })
                })
            })
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/microPage/memberCard?page='+page, function (response) {
                        // console.log(response);
                        if (response.status == 1) {
                            $scope.pageList = [];
                            angular.forEach(response.data.data, function (val, key) {
                                $scope.$apply(function () {
                                    $scope.pageList.push({
                                        "id": val.id,
                                        "name": val.title,
                                        "url": host + 'shop/member/detail/' + wid + '/' + val.id,
                                        "power_desc": val.power_desc
                                    })
                                })
                            })
                            // console.log($scope.pageList);
                        }
                    })
                }
            });
            showModel($('#page_model_card'), $('#page-dialog-card'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
            // console.log($scope.pageList);
        }, 'json')
    }

    //图片广告拼团确定
    $scope.chooseMenModelSure = function($index,list){
        commonServer.chooseMenModelSure($index,list,$scope);
    }
    $scope.searchMenTitle = ''
    $scope.searchMenModel = function () {
        $scope.temp = [];
        // $scope.pageId = [];
        var wid = $('#wid').val();
        $scope.pageList = [];
        $.get('/merchants/microPage/memberCard?page=1&keyword='+ $scope.searchMenTitle, function (data) {
            // console.log(data);
            angular.forEach(data.data.data, function (val, key) {
                $scope.$apply(function () {
                    $scope.pageList.push({
                        "id": val.id,
                        "name": val.title,
                        "url": host + 'shop/member/detail/' + wid + '/' + val.id,
                        "power_desc": val.power_desc
                    })
                })
            })
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/microPage/memberCard?page='+ page +'&keyword='+ $scope.searchMenTitle, function (response) {
                        if (response.errCode == 0) {
                            $scope.pageList = [];
                            angular.forEach(response.data.data, function (val, key) {
                                $scope.$apply(function () {
                                    $scope.pageList.push({
                                        "id": val.id,
                                        "name": val.title,
                                        "url": host + 'shop/member/detail/' + wid + '/' + val.id,
                                        "power_desc": val.power_desc
                                    })
                                })
                            })
                        }
                    })
                }
            });
            showModel($('#page_model_card'), $('#page-dialog-card'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        }, 'json')
    }

    $scope.textColor = function (e) {
        console.log(e);
    }
}])
   // 模态框添加分组
   $(".btn_left").on('click', function () {
    var name = $('.add_group_input').val();
    if(!name){
        return false
    }
    $.ajax({
        url:'/merchants/myfile/addClassify',
        type: 'POST',
        data:{
            name:name,
            _token:_token,
        },
        success:function (data) {
            console.log(data);
            if(data.status == 1){
                var _group = '<li class="js-category-item" data-id="'+data.data.id+'">'+data.data.name+'\
                            <span>0</span>\
                        </li>';
                $('.category-list').append(_group);
                $(".add_group_box").addClass('hide')
            }
        }
    })
}) 

// 添加分组 by 崔源 2018.10.22
$(".btn_right").on('click',function () {
    $(".add_group_box").addClass('hide')
    $('.add_group_list').attr('data-id','1');
})
$(".add_group_list").on('click',function () {
    var id = $(this).attr('data-id');
    if(id == 1){
        $(this).attr('data-id','2');
        $(".add_group_box").removeClass('hide')
    }else {
        $(this).attr('data-id','1');
        $(".add_group_box").addClass('hide')
    }
});

