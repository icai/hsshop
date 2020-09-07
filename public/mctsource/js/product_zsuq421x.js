app.controller('myCtrl',['$scope','$sce','$timeout','$http','commonServer',function($scope, $sce,$timeout,$http,commonServer) {
    //资源路径
    $scope.editors = [];//循环列表
    $scope.index = commonServer.index;//editing当前索引值
    $scope.color = commonServer.color;//富文本设置背景颜色
    $scope.temp = commonServer.temp;//临时转存数组
    $scope.tempSure = commonServer.tempSure;//选择商品确定按钮
    $scope.chooseSureBtn = commonServer.chooseSureBtn; //选择广告图片确定按钮
    $scope.tempUploadImage = commonServer.tempUploadImage;//临时转存数组
    $scope.eventKind = commonServer.eventKind;//区分点击事件1，为添加广告多图，2为重新上传单图。
    $scope.advImageIndex = commonServer.advImageIndex //重新上传图片索引记录
    $scope.uploadShow = false; //判断上传可图片model显示
    $scope.id = 0 //编辑修改id
    console.log(store)
    $scope.shop_url = store.url //店铺主页url;
    $scope.member_url = store.member_url;//会员主页URL
    $scope.group_url = '/shop/grouppurchase/index/' + store.id;
    $scope.community_url = '/shop/microforum/forum/index/'+ store.id;//微社区url 
    $scope.baseInfo = {
        title:'',
        style:1,//1为普通版，2为简洁流畅版。
        show:false
    };
    /*@author huoguanghui start*/
    $scope.host = imgUrl;
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
    /*@author huoguanghui end*/
    var ue = initUeditor('editor');//初始化编辑器
    bindEventEditor(ue,$scope);//初始化编辑器
    laydate.skin('molv'); //切换皮肤，请查看skins下面皮肤库
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
    //循环列表
    if(getUrl()[2] != 'editgroup'){
        $scope.editors = [
            {   
                'showRight':true,
                'cardRight':10, //3为富文本，4商品，5商品列表
                'type':'goods_group',
                'editing':'editing',
                'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表,10代表商品分组
                'cardStyle':1,
                'first_priority':0,
                'second_priority':1,
                'showSell':true,
                'btnStyle':1, //分四种情况
                'goodName':false, //默认不显示
                'goodInfo':false,
                'priceShow':true,//默认显示
                'nodate':true,
                'show_tag_title':true,//是否显示分组名称 1 是 0 否
                'groupTitle':'商品分组',
                'signInfo':'',
                'goods':[
                    {
                        'thumbnail': _host + 'static/images/product_img_1.jpg',
                        'name':'这里显示商品名称',
                        'info':'这里显示商品通知',
                        'price':'￥1',
                        'deleteBtn':true,
                    },
                    {
                        'thumbnail':_host + 'static/images/product_img_1.jpg',
                        'name':'这里显示商品名称',
                        'info':'这里显示商品通知',
                        'price':'￥2',
                        'deleteBtn':true,
                    }
                ],
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
                ]
            }
        ];
    }
    //初始化编辑器
    var ue_group = initUeditor('category_editor');
    ue_group.addListener("selectionchange", function () {
        var content = ue_group.getContent();
        // console.log(content);
        $('.group_desc p').html(content);
        $scope.editors[0]['signInfo'] = content;
        // console.log($sce.trustAsHtml(content))
        console.log($scope.editors[0]['signInfo'])
    });
    //判断是否为修改
    if(getUrl()[2]=='editgroup'){
        $http.get('/merchants/product/getgroup?id=' + getUrl()[3]).success(function(data){
            $scope.id = data.data.id;
            $scope.editors = data.data.tpl.data;
            console.log($scope.editors);
            if($scope.editors.length>0 && typeof($scope.editors) != 'string'){
                angular.forEach($scope.editors,function(val,key){
                    //有商品属于该商品分组时 图片不显示 修复 Herry 20180208
                    if(val.type=='goods_group'){
                        val.thGoods = [];
                        val.goods = val.goods == undefined ? []:val.goods;
                        if(val.goods.length>0){
                            angular.forEach(val.goods,function(val1,key1){
                                val1.thumbnail = host + val1.thumbnail;
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
                    }
                    if(val.type == 'goods'){
                        val.thGoods = [];
                        val.goods = val.goods == undefined ? []:val.goods; 
                        if(val.goods.length>0){
                            angular.forEach(val.goods,function(val1,key1){
                                val1.thumbnail = host + val1.thumbnail;
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
                        console.log(val);
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.FileInfo.m_path = imgUrl + val1.FileInfo.m_path;
                                val1.FileInfo.path = imgUrl + val1.FileInfo.path;
                            })
                        }
                    }
                    if(val.type=='header'){
                        val.bg_image = imgUrl + val.bg_image;
                    }
                    if(val.type=="image_link"){
                        if(val.images.length > 0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.thumbnail = imgUrl + val1.thumbnail;
                            })
                        }
                    }
                    if(val.type == "coupon"){
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
                    
                    //秒杀活动
                    if(val.type=="marketing_active"){
                        if(val['content'].length==0){
                            return;
                        }
                        var seckill_stock = 0;//秒杀库存
                        for(var j = 0;j < val['content'][0]['sku'].length;j ++){
                            seckill_stock += parseInt(val['content'][0]['sku'][j]['seckill_stock']);
                        }
                        console.log(seckill_stock)
                        if(seckill_stock > 0){
                            val['content'][0]['productStatus'] = false;
                        }else{
                            val['content'][0]['productStatus'] = true;
                        }
                        var endTime= new Date(val['content'][0]['end_at']);
                        var nowTime = new Date();
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
                        console.log($scope.editors)
                    }
                    
                })
            }
            $timeout(function(){
                $('.group_desc').eq(0).html(data.data.tpl.data[0].signInfo);
                console.log(ue_group);
                ue_group.addListener("ready", function () {
                    ue_group.setContent(data.data.tpl.data[0].signInfo);
                });
             },100)
            // console.log(data.data.tpl.data[0].signInfo)
            // console.log($('.group_desc p').html())
        })
    }
    // 第一优先级
    $scope.firstData = [
        {value:0,text:'序号越大越靠前'},
        {value:3,text:'最热的排在前面'}
    ]
    // 第二优先级
    $scope.secondData = [
        {value:1,text:'创建时间越晚越靠前'},
        {value:2,text:'创建时间越早越靠前'},
        {value:3,text:'最热的排在前面'},
    ]
    //监听第一优先级有没有数据显示按钮
    $scope.$watch("editors[0].first_priority",function(newVal,oldVal){
        if(newVal == 0){
            $scope.secondData = [
                {value:1,text:'创建时间越晚越靠前'},
                {value:2,text:'创建时间越早越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }else if(newVal ==3){
            $scope.secondData = [
                {value:0,text:'序号越大越靠前'},
                {value:1,text:'创建时间越晚越靠前'},
                {value:2,text:'创建时间越早越靠前'}
            ]
        }else if(newVal ==1){
            $scope.secondData = [
                {value:0,text:'序号越大越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }else if(newVal ==2){
            $scope.secondData = [
                {value:0,text:'序号越大越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }
    },true)
    // 监听第二优先级
    $scope.$watch("editors[0].second_priority",function(newVal,oldVal){
        if(newVal == 0){
            $scope.firstData = [
                {value:1,text:'创建时间越晚越靠前'},
                {value:2,text:'创建时间越早越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }else if(newVal ==3){
            $scope.firstData = [
                {value:0,text:'序号越大越靠前'},
                {value:1,text:'创建时间越晚越靠前'},
                {value:2,text:'创建时间越早越靠前'}
            ]
        }else if(newVal ==1){
            $scope.firstData = [
                {value:0,text:'序号越大越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }else if(newVal ==2){
            $scope.firstData = [
                {value:0,text:'序号越大越靠前'},
                {value:3,text:'最热的排在前面'}
            ]
        }
    },true) 
    // $($('first_priority').children('option')).remove();
    // $('#first_priority option[index="0"]').remove();
    // console.log($('#first_priority').children());
    // $('#first_priority').change(function(){
    //     alert(1)
    // });
    // $('#first_priority').trigger('change');
    $scope.couponList = [];//券列表
    $scope.goodList = [
        {
            "name":"实物商品（购买时需填写收货地址，测试商品，不发货，不退款",
            "thumbnail":"https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/554c17f049181649f35168d8fb367663.jpg",
            "info":"这是商品通知",
            "price":"￥1",
            "timeDay":"2016-09-22",
            "timestamp":"15:57:27"  
        }
    ];
    $scope.uploadImages = [];//图片数组
    $scope.addeditor = function(position){
        commonServer.addeditor($scope,ue,position)
    };
    // 左侧点击
    $scope.tool = function(event,editor){
        $scope.baseInfo.show = false;    
        editor['editing'] = 'editing';
        editor['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-25);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                }
                $scope.editors[$scope.index]['showRight'] = true;
                if(event.currentTarget.getAttribute('data-type')=='member'){
                    $scope.editors[$scope.index]['cardRight'] = 1;
                    $timeout(function(){
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='category'){
                    $scope.editors[$scope.index]['cardRight'] = 2;
                    $timeout(function(){
                        var desc = document.getElementsByClassName('page_desc')[0];
                        ue_category.setContent(desc.innerHTML);
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='rich_text'){
                    $scope.editors[$scope.index]['cardRight'] = 3;
                    $timeout(function(){
                        ue.setContent($('.editing').find('.custom-richtext').html());
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='goods'){
                    $scope.editors[$scope.index]['cardRight'] = 4;
                }else if(event.currentTarget.getAttribute('data-type')=='image_ad'){
                    $scope.editors[$scope.index]['cardRight'] = 5;
                }else if(event.currentTarget.getAttribute('data-type')=='title'){
                    $scope.editors[$scope.index]['cardRight'] = 6;
                }else if(event.currentTarget.getAttribute('data-type')=='store'){
                    $scope.editors[$scope.index]['cardRight'] = 7;
                }else if(event.currentTarget.getAttribute('data-type')=='goods_group'){
                    $scope.editors[$scope.index]['cardRight'] = 10;
                }
            }) 
        },100)
    }
    //提交表单
    $scope.repeatsub = true;
    $scope.sumbitForm = function(isValid){
        $scope.iserror = true;
        if(isValid){
            $scope.postData = angular.copy($scope.editors);
            angular.forEach($scope.postData,function(val,key){
                if(val.type == 'goods'){
                   val.goods = [];
                   val.thGoods = [];
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
            })
            $scope.postData = JSON.stringify($scope.postData);
            if($scope.repeatsub){
            	$scope.repeatsub = false;
            	$http.post('/merchants/product/addgroup',{data:$scope.postData,id:$scope.id}).success(function(response) {
	                if(response.status == 1){
	                    tipshow(response.info);
	                    setTimeout(function(){
	                        window.location.href = '/merchants/product/productGroup';
	                    })
	                    // window.location.href = '/merchants/product/productGroup';
	                }
	            });
            }
            
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
    // 添加边框
    $scope.addboder = function(editor,position){
        commonServer.addboder(editor,$scope,position);
    }
    // 减去边框
    $scope.removeboder = function($event,editor){
        commonServer.removeboder($event,editor,$scope);
    }
    //初始化清除editing
    $scope.removeClassEditing = function(){
        commonServer.removeClassEditing($scope);
    }
    // 初始化右边栏
    $scope.initCartRight = function(index){
        $scope.baseInfo.show = false;
        $timeout(function(){
            var ele = document.getElementsByClassName('editing');
            $('.card_right_list').css('margin-top',ele[0].offsetTop-25);
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                }
        })
        //     $scope.crSetting = {
        //         'showRight':true,
        //         'cardRight':index, //3为富文本，4商品，5广告添加
        //         'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
        //         'cardStyle':1,//1卡片样式，2瀑布流样式，3极简样式，4，促销
        //         'showSell':true,
        //         'btnStyle':1, //分四种情况
        //         'goodName':false, //商品名字默认不显示
        //         'goodInfo':false, //商品简介默认不显示
        //         'priceShow':true, //商品价格默认显示
        //     };
        //     console.log($scope.index)  
        },100);
    }
    // 优惠券添加
    $scope.addCoupon = function(position){
       commonServer.addCoupon($scope,position);
    }
    // 删除一个优惠券
    $scope.deleteCoupon = function($index){
        commonServer.deleteCoupon($scope,$index);
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
    //商品及分类搜索
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
    // 隐藏model
    $scope.hideModel = function(){
        commonServer.hideModel();
    }
    //选择商品
    $scope.choose = function($index,list){
        commonServer.choose($index,$scope,list);
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
    //确定选择商品
    $scope.chooseSure =function(position){
        commonServer.chooseSure($scope,position);
    }
    // 选择优惠券确定按钮
    $scope.chooseCouponSure = function(){
        commonServer.chooseCouponSure($scope);
    }
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
    //显示删除按钮
    $scope.showDelete = function($index){
        commonServer.showDelete($index,$scope);
    }
    //隐藏删除按钮
    $scope.hideDelete = function($index){
        commonServer.hideDelete($index,$scope);
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
        commonServer.sureProver($scope);
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
    $scope.searchTitle = '';//搜索内容
    // 选择商品点击搜索
    $scope.goSearch = function(){
        commonServer.goSearch($scope,$scope.searchTitle);
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
    // 搜索自定义模块
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
    // 微页面加内容
    $scope.addContent = function(event,$index,editor,top){
        commonServer.addContent(event,$index,editor,$scope,top);
    }
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
        if(obj.cardRight != undefined || obj.type != undefined ){
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
    //监听temp有没有数据显示按钮
    $scope.$watch("temp",function(newVal,oldVal){
        console.log($scope.temp);
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
      uploader.on('uploadSuccess', function (file, response) {
            if (response.status == 1) {
                $scope.$apply(function () {
                    response.data['FileInfo']['s_path'] = imgUrl + response.data['FileInfo']['s_path'];
                    $scope.tempUploadImage.unshift(response.data);
                    console.log($scope.tempUploadImage);
                })
            }

        });
}])
