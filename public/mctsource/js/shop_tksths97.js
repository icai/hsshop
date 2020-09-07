$(function(){
    $('.js-switch').click(function(){
        if($(this).hasClass('ui-switcher-off')){
            $(this).removeClass('ui-switcher-off');
            $(this).addClass('ui-switcher-on');
        }else{
            $(this).removeClass('ui-switcher-on');
            $(this).addClass('ui-switcher-off');
        }
    })
})
app.controller('myCtrl',['$scope','$sce','$timeout','commonServer','$http',function($scope, $sce,$timeout,commonServer,$http) {
    $scope.baseInfo = {
		id:0,
        showPosition:1,//展示位置1页面头部，2页面底部
        showPage:[], //出现的页面
        ison:0 //是否启用0为关闭，1为开启
    };
    $scope.wai_setting = true; //显示外链
	//$scope.baseInfo = _baseinfo;
	//$scope.editors = _editors;
    $scope.editors = [];//循环列表
    $scope.index = commonServer.index;//editing当前索引值
    $scope.color = commonServer.color;//富文本设置背景颜色
    $scope.temp = commonServer.temp;//临时转存数组
    $scope.tempSure = commonServer.tempSure;//选择商品确定按钮
    $scope.chooseSureBtn = commonServer.chooseSureBtn; //选择广告图片确定按钮
    $scope.tempUploadImage = commonServer.tempUploadImage;//临时转存数组
    $scope.eventKind = commonServer.eventKind;//区分点击事件1，为添加广告多图，2为重新上传单图。
    $scope.advImageIndex = commonServer.advImageIndex //重新上传图片索引记录
    $scope.changeImange = commonServer.changeImange; //判断是否是member修改图片
    $scope.uploadShow = false; //判断上传可图片model显示
	$scope.shop_url = store.url //店铺主页url;
    $scope.member_url = store.member_url;//会员主页URL
    $scope.group_url = '/shop/grouppurchase/index/' + store.id;
    $scope.community_url = '/shop/microforum/forum/index/'+ store.id;//微社区url 
    $scope.shopPage = [
        {title:'微页面',value:1,checked:false},
        //{title:'微页面分类',value:2,checked:false},
        //{title:'商品',value:3,checked:false},
        //{title:'商品标签',value:4,checked:false},
        {title:'店铺主页',value:5,checked:false},
        {title:'会员主页',value:6,checked:false}
    ]
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
    $scope.goodList = [];//商品列表
    $scope.couponList = [];//券列表
    $scope.uploadImages = [] //上传图片列表
    // 添加边框
    $scope.addboder = commonServer.addboder;
    // 减去边框
    $scope.removeboder = commonServer.removeboder;
    //初始化清除editing
    $scope.removeClassEditing = function(){
        commonServer.removeClassEditing($scope);
    }
	//$scope.baseInfo = _baseinfo;
	//$scope.editors = _editors;
    // 初始化右边栏
    $scope.index = 0
    $scope.initCartRight = function(){
        $scope.first_card = false;
        $timeout(function(){
            var ele = document.getElementsByClassName('editing');
            $('.card_right_list').css('margin-top',ele[0].offsetTop-150);
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                }
            })
        },100);
    }
	//获取公共广告信息
	$http({ method: 'GET',url: '/merchants/store/selectNotice'
    }).success(function(response){
		console.log(response);
		if(response.errCode==0&&response.data.length!=0){
            if(response.data.baseInfo.ison==1){
                $('.js-switch').addClass('ui-switcher-on');
                $('.js-switch').removeClass('ui-switcher-off');
            }else{
                $('.js-switch').removeClass('ui-switcher-on');
                $('.js-switch').addClass('ui-switcher-off');
            }
			$scope.baseInfo=response.data.baseInfo; 
			angular.forEach($scope.shopPage,function(val,key){
				if($.inArray(val.value,$scope.baseInfo.showPage) != -1){
					val.checked = true;
				}
			})
            if(response.data.editors !== null){
                $scope.editors = response.data.editors;
                angular.forEach($scope.editors,function(val,key){
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
                        console.log(val);
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.FileInfo.m_path = imgUrl + val1.FileInfo.m_path;
                                val1.FileInfo.path = imgUrl + val1.FileInfo.path;
                            })
                        }
                    }
                    if(val.type=="image_link"){
                        if(val.images.length > 0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.thumbnail = imgUrl + val1.thumbnail;
                            })
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
                })
            }
		}
	}) 
	//添加公共广告信息  $scope.baseInfo = {id:0,showPosition:1,//展示位置1页面头部，2页面底部 
	//	showPage:[], //出现的页面 ison:0 //是否启用0为关闭，1为开启 }; 
	$scope.processNotice = function(isValid){
        $scope.iserror = true;
        if(isValid){
            $('.btn_grounp button').attr('disabled','disabled');
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
            $http({  
               method:'post',  
               url:'/merchants/store/processNotice',  
               data:{id:$scope.baseInfo.id,ison:$scope.baseInfo.ison,showPage:$scope.baseInfo.showPage,showPosition:$scope.baseInfo.showPosition,data:$scope.postData}
            }).success(function(response){ 
                if(response.errCode==0){
                    tipshow('保存成功!');
                    // layer.alert('保存成功！',function(){
                    //     window.location.reload();
                    // });
                }else{
                    tipshow(response.errMsg);
                }
                $('.btn_grounp button').removeAttr('disabled');
            }).error(function(){
                tipshow('保存失败！');
                $('.btn_grounp button').removeAttr('disabled');
            })  
        }
	}
    $scope.addeditor = function(position){
        commonServer.addeditor($scope,ue,position);
    } 
    // 左侧每个列表
    $scope.tool = function(event,editor){
        $scope.first_card = false;
        editor['editing'] = 'editing';
        editor['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-150);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key; 
                    $scope.editors[$scope.index].showRight = true;
                    if(event.currentTarget.getAttribute('data-type')=='member'){
                        $scope.editors[$scope.index]['cardRight'] = 1;
                        $timeout(function(){
                            ue.setContent($('.editing').children('editor-text').html());
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
                        console.log($scope.editors);
                    }else if(event.currentTarget.getAttribute('data-type')=='store'){
                        $scope.editors[$scope.index]['cardRight'] = 7;
                    }
                }
            }) 
        },100)
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
    // 广告开启关闭赋值
    $scope.switcher = function($event){
        if($event.target.className == 'js-switch ui-switcher pull-right ui-switcher-off'){
            $scope.baseInfo.ison = 1;
        }else{
            $scope.baseInfo.ison = 0;
        }
    }
    // 出现页面多选
    $scope.change = function(){
        $scope.baseInfo.showPage = [];
        angular.forEach($scope.shopPage,function(val,key){
            if(val.checked){
                $scope.baseInfo.showPage.push(val.value);
            }
        })
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
    // 基本信息点击、
    // $scope.showBaseInfo = function(){
    //     // alert('a')
    //     $scope.baseInfo = true;
    //     angular.forEach($scope.editors,function(data){
    //         data.showRight =false;
    //     })
    //     var baseinfo = document.getElementsByClassName('baseinfo');
    //     baseinfo[0].style.opacity = '1';
    //     // $('.card_right').css('margin-top',$('#baseinfo').offset());
    // }
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
    //显示优惠券弹窗
    $scope.showCouponModel = function(){
        commonServer.showCouponModel($scope);
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
    // 优惠券弹窗搜索
    $scope.searchCoupon = function(){
        commonServer.searchCoupon($scope);
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
    //确定选择商品
    $scope.chooseSure =function(position){
        commonServer.chooseSure($scope,position);
    }
    // 选择优惠券确定按钮
    $scope.chooseCouponSure = function(){
        commonServer.chooseCouponSure($scope);
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
        $scope.uploadShow = false;
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
    // setTimeout(function(){
    //     console.log($scope.editors);
    // },1000)
    $scope.addLink = function(){
        console.log($scope.editors);
        commonServer.addLink($scope);
    }
    // 关闭文本导航
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
            })
        }

    });
}])
