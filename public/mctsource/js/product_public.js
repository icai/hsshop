app.factory('commonServer',function($timeout,$http){
    //资源路径
    var commonServer = {};
    commonServer.index = 0;//editing当前索引值
    commonServer.color = '#ffffff';//富文本设置背景颜色
    commonServer.temp = [];//临时转存数组
    commonServer.tempSure = false;//选择商品确定按钮
    commonServer.chooseSureBtn = false; //选择广告图片确定按钮
    commonServer.tempUploadImage = [];//临时转存数组P
    commonServer.eventKind = 1;//区分点击事件1，为添加广告多图，2为重新上传单图。
    commonServer.advImageIndex = null //重新上传图片索引记录
    commonServer.changeImange = false; //判断是否是member修改图片
    commonServer.advsImagesIndex = 0;//选择商品图片链接判断广告图片位置
    commonServer.shopLinkPosition = 1;//记录选择商品链接位置1为图片广告，2为标题
    commonServer.switchIndex = 1;//记录选择链接的index
    commonServer.Pindex = 1;//记录商品模板子分类选择链接时父元素的index
    commonServer.callPhoneIndex = null;//联系方式图片index
    commonServer.callPhoneType = 1;//联系方式图片默认自定义
    //添加富文本
    commonServer.addeditor = function($scope,ue,position){
        $scope.removeClassEditing();
        var html = '<div>';
        html += '<p style="margin: 0 0 1em 0;">点此编辑『富文本』内容 ——&gt;</p>';
        html += '<p style="margin: 0 0 1em 0;">你可以对文字进行<strong>加粗</strong>、<em>斜体</em>、<span style="text-decoration: underline;">下划线</span>、<span style="text-decoration: line-through;">删除线</span>、文字<span style="color: rgb(0, 176, 240);">颜色</span>、<span style="background-color: rgb(255, 192, 0); color: rgb(255, 255, 255);">背景色</span>、以及字号<span style="font-size: 20px;">大</span><span style="font-size: 14px;">小</span>等简单排版操作。</p>';
        html += '<p style="margin: 0 0 1em 0;">还可以在这里加入表格了</p>';
        html += '<table><tbody>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">中奖客户</td><td width="104" valign="top" style="word-break: break-all;">发放奖品</td><td width="104" valign="top" style="word-break: break-all;">备注</td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">猪猪</td><td width="104" valign="top" style="word-break: break-all;">内测码</td><td width="104" valign="top" style="word-break: break-all;"><em><span style="color: rgb(255, 0, 0);">已经发放</span></em></td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">大麦</td><td width="104" valign="top" style="word-break: break-all;">积分</td><td width="104" valign="top" style="word-break: break-all;"><a href="javascript: void(0);" target="_blank">领取地址</a></td></tr>';
        html += '</tbody></table>';
        html += '<p style="text-align: left;"><span style="text-align: left;">也可在这里插入图片、并对图片加上超级链接，方便用户点击。</span></p></div>';
        // var ue = initUeditor('editor');//初始化编辑器
        ue.ready(function() { 
            ue.setContent(''); 
        });
        if(position == 1){
            //1代表底部添加，2代表加内容添加
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':3, //3为富文本，4商品，5商品列表
                    'type':'rich_text',
                    'content':'',
                    'editing':'editing',
                    'bgcolor':'#ffffff',
                    'initShow':true,
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':3, //3为富文本，4商品，5商品列表
                'type':'rich_text',
                'content':'',
                'editing':'editing',
                'bgcolor':'#ffffff',
                'initShow':true,
                'is_add_content':false
            })
        }
        $scope.color = '#ffffff';
        $scope.initCartRight();
    };
    // 左侧点击
    commonServer.tool = function(event,editor,$scope,top,ue){
        editor['editing'] = 'editing';
        editor['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-top);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    $scope.editors[$scope.index].showRight = true;
                    if(event.currentTarget.getAttribute('data-type')=='member'){
                        $scope.editors[$scope.index]['cardRight'] = 1;
                        $timeout(function(){
                            // ue.setContent($('.editing').children('editor-text').html());
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
                            // alert($('.editing').children('editor-text').find('.custom-richtext').html())
                            console.log($('.editing').find('.custom-richtext').html());
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
                    }
                }
            }) 
        },200)
    }
    // 初始化右边栏
    commonServer.initCartRight = function(index,$scope,top){
        $scope.baseInfo = false;
        $timeout(function(){
            var ele = document.getElementsByClassName('editing');
            $('.card_right_list').css('margin-top',ele[0].offsetTop-top);
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    console.log($scope.index);
                }
            })
        },100);
    }
    commonServer.removeClassEditing = function($scope){
        console.log($scope)
        if($scope.editors.length>0){
            angular.forEach($scope.editors,function(data,key){
                data.editing ='';
            })
        }
        $('.app-field').removeClass('editing');
    }
    commonServer.checkModules = function($scope,$index){
        $scope.editors[$scope.index].list[$index].checked = $scope.editors[$scope.index].list[$index].checked ? false : true
        console.log($scope.editors[$scope.index].list[$index].checked,1111111)
    }
    commonServer.checkCaifuyan = function($scope,$index){
        $scope.is_open_weath = $scope.is_open_weath ? 0 : 1;
        $http.post('/merchants/store/isOpenWeath',{is_open_weath:$scope.is_open_weath}).success(function(res){
            console.log(res)
        })
    }



    commonServer.addboder = function(editor,$scope){
        // console.log(editor.currentTarget);
        // editor.currentTarget.parentNode.parentNode.childNodes[9].style.display ='block'
        editor.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'
    }
    commonServer.removeboder = function($event,editor,$scope){
        // console.log(editor);
        $timeout(function(){
            if(editor['editing'] !='editing'){
                $event.currentTarget.style.border = '2px dashed rgba(255,255,255,0.5)';
            }
        },100)
    }
    // 添加商品
    commonServer.addgoods = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {   
                    'showRight':true,
                    'cardRight':4, //3为富文本，4商品，5商品列表
                    'type':'goods',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                    'cardStyle':1,
                    'showSell':true,
                    'btnStyle':1, //分四种情况
                    'goodName':false, //默认不显示
                    'goodInfo':false,
                    'priceShow':true,//默认显示
                    'nodate':true,
                    'goods':[],
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
                    'products_id':[],
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{   
                'showRight':true,
                'cardRight':4, //3为富文本，4商品，5商品列表
                'type':'goods',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                'cardStyle':1,
                'showSell':true,
                'btnStyle':1, //分四种情况
                'goodName':false, //默认不显示
                'goodInfo':false,
                'priceShow':true,//默认显示
                'nodate':true,
                'goods':[],
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
                'products_id':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();//初始化右边
    }
    // 优惠券添加
    commonServer.addCoupon = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':8, //3为富文本，4商品，5商品列表，6为标题 8为优惠券
                    'type':'coupon',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'show':1, //1为初始化，2为没数据，3为有数据
                    'couponList':[],
                    'coupons_id':[],
                    'couponStyle': 1, // 优惠券样式1,2,3,4
                    'couponColor': 1, // 优惠券颜色1,2,3,4，5
                    'is_add_content':false                
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                    'showRight':true,
                    'cardRight':8, //3为富文本，4商品，5商品列表，6为标题 8为优惠券
                    'type':'coupon',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'show':1, //1为初始化，2为没数据，3为有数据
                    'couponList':[],
                    'coupons_id':[],
                    'couponStyle': 1, // 优惠券样式1,2,3,4
                    'couponColor': 1, // 优惠券颜色1,2,3,4，5
                    'is_add_content':false                
                })
        }
        $scope.initCartRight();
    }
    // 删除一张优惠券
    commonServer.deleteCoupon = function($scope,$index){
        console.log($scope.editors[$scope.index]['couponList'][$index]['id']);
        angular.forEach($scope.editors[$scope.index]['coupons_id'],function(val,key){
            if(val == $scope.editors[$scope.index]['couponList'][$index]['id']){
                $scope.editors[$scope.index]['coupons_id'].splice(key,1);
            }
        })
        $scope.editors[$scope.index]['couponList'].splice($index,1);
        // $scope.editors[$scope.index]['coupons_id'].splice($index,1);
    }
    //crmember右侧修改背景图
    commonServer.changeBg = function($scope){
        $scope.choosePosition = 1;
        $scope.eventKind=2;
        $scope.changeImange = true;
        $scope.uploadShow = false;
        $scope.grounps = [];
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                console.log($scope.uploadImages);
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                            // console.log($scope.uploadImages);
                        })
                    }
                });
            })
            $scope.initchooseAdvImage();
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
    }
    //显示model
    commonServer.showModel = function($scope){
        $scope.searchTitle = '';
        $scope.goodList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.goodList.push({
                        "id":val.id,
                        "name":val.title,
                        "thumbnail":imgUrl + val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_at,
                        "timestamp":"15:57:27" ,
                        "url":'/shop/product/detail/'+val.wid+'/'+val.id 
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.goodList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":imgUrl + val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_at,
                                            "timestamp":"15:57:27",
                                            "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                                        })
                                    })
                                })
                            }
                        })
                        // $http.get('/merchants/product/getproducts?ajax=json&page='+page).success(function(response) {
                        //     console.log(response);
                        //     // $scope.goodList = 
                        //     if(response.status ==1){
                        //         $scope.goodList = [];
                        //         angular.forEach(response.data.list,function(val,key){
                        //             $scope.goodList.push({
                        //                 "name":val.title,
                        //                 "thumbnail":val.img,
                        //                 "info":"",
                        //                 "price":val.price,
                        //                 "timeDay":val.created_at,
                        //                 "timestamp":"15:57:27"  
                        //             })
                        //         })
                        //     }
                        // });
                    }
                });
            showModel($('#myModal'),$('#modal-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
        // $http.get('/merchants/product/getproducts?ajax=json&page=1').success(function(data){
        //     angular.forEach(data.data.list,function(val,key){
        //         $scope.goodList.push({
        //             "name":val.title,
        //             "thumbnail":val.img,
        //             "info":"",
        //             "price":val.price,
        //             "timeDay":val.created_at,
        //             "timestamp":"15:57:27"  
        //         })
        //     })
        //     var totalCount = data.data.total_count, showCount = 10,
        //         limit = data.data.per_page;
        //         $('.pagenavi').extendPagination({
        //             totalCount: totalCount,
        //             showCount: showCount,
        //             limit: limit,
        //             callback: function (page, limit, totalCount) {
        //                 $http.get('/merchants/product/getproducts?ajax=json&page='+page).success(function(response) {
        //                     console.log(response);
        //                     // $scope.goodList = 
        //                     if(response.status ==1){
        //                         $scope.goodList = [];
        //                         angular.forEach(response.data.list,function(val,key){
        //                             $scope.goodList.push({
        //                                 "name":val.title,
        //                                 "thumbnail":val.img,
        //                                 "info":"",
        //                                 "price":val.price,
        //                                 "timeDay":val.created_at,
        //                                 "timestamp":"15:57:27"  
        //                             })
        //                         })
        //                     }
        //                 });
        //             }
        //         });
        //     showModel($('#myModal'),$('#modal-dialog'));
        //     $('.js-choose').removeClass('btn-primary');//初始化选择按钮
        //     $('.js-choose').html('选取');
        // })
    }
    /*
    *@author huoguanghui 商品选择及分组
    */
    //切换商品及分类导航
    commonServer.switchProductNav = function($index,$scope){
        $scope.productModal.navIndex = $index;
        getProductModalList($scope)
    }
    //选择商品链接model
    commonServer.showShopModel = function($index,position,$scope){
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.link_type = 2;
        getProductModalList($scope)
    }
    commonServer.searchProductList = function($scope){
        getProductModalList($scope,$scope.searchTitle)
    }
    //获取商品及分组列表
    function getProductModalList($scope,keyword){
        var wid = $('#wid').val();
        var keyword = keyword?keyword:'';
        $scope.productModal.list = [];//初始化数据
        switch ($scope.productModal.navIndex)
        {
            case 0://商品
                var _url = '/merchants/linkTo/get?type=1';
                var shopUrl = '/shop/product/detail/';
            break;
            case 1://分组
                var _url = '/merchants/linkTo/get?type=2';
                var shopUrl = '/shop/group/detail/';
            break;
        }
        $.get(_url+'&wid='+wid +'&page=1&title='+keyword, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                console.log(val);
                $scope.$apply(function(){
                    $scope.productModal.list.push({
                        "id":val.id,
                        "name":val.title,
                        "thumbnail": val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_at,
                        "timestamp":"" ,
                        "url":shopUrl+val.wid+'/'+val.id 
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get(_url+'&wid='+ wid +'&page='+page+'&title='+keyword,function(response){
                            if(response.status ==1){
                                $scope.productModal.list = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.productModal.list.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_at,
                                            "timestamp":"",
                                            "url":shopUrl+val.wid+'/'+val.id   
                                        })
                                    })
                                })
                            }
                        });
                        
                    }
                });
            showModel($('#chooseShopModel'),$('#chooseShopModel-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    /** 
     * uptade at 2018/08/13 华亢
     * 添加享立减外链时少个参数
    */
    //  获取享立减商品
    function getShareModalList($scope,keyword){

    	$scope.searchTitle = '';
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
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.share_good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                console.log(response);
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

    //切换营销活动中活动
    commonServer.switchNav = function($index,$scope){
        $scope.activityIndex = $index;
        $scope.activity_list = [];//先清空数据
        var _token = $('meta[name="csrf-token"]').attr('content');
        //判断是哪一个活动
        switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                var url = "/merchants/marketing/wheelList?pagesize=6";
            break;
            case 1://砸金蛋
                var url = "/merchants/marketing/egg/index?size=6";
            break;
        }
        $.post(url,{_token:_token},function(data){
            if(data.data[0]['data'].length){
                angular.forEach(data.data[0]['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.activity_list.push(val);
                    })
                })
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
             $('.activity_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $scope.activity_list = [];
                        $.post(url,{_token:_token,page:page},function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.activity_list.push(val)
                                    })
                                })
                            }
                        })
                       
                    }
                });
            showModel($('#activity_model'),$('#activity-dialog'));
        })
    }
    //显示营销活动弹窗
    commonServer.chooseActivity = function($index,position,$scope){
        $scope.switchIndex = $index;
        $scope.activity_list = [];
        $scope.searchTitle = '';
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        var _token = $('meta[name="csrf-token"]').attr('content');
        //判断是哪一个活动
        switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                var url = "/merchants/marketing/wheelList?pagesize=6";
            break;
            case 1://砸金蛋
                var url = "/merchants/marketing/egg/index?size=6";
            break;
        }
        console.log(url);
        $.post(url,{_token:_token},function(data){
            if(data.data[0]['data'].length){
                angular.forEach(data.data[0]['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.activity_list.push(val);
                    })
                })
            }
            var totalCount = data.data[0].total, showCount = 10,
            limit = data.data[0].per_page;
            $('.activity_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $scope.activity_list = [];
                    $.post(url,{_token:_token,page:page},function(response){
                        if(response.status ==1){
                            $scope.goodList = [];
                            angular.forEach(response.data[0].data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.activity_list.push(val)
                                })
                            })
                        }
                    })
                   
                }
            });
            showModel($('#activity_model'),$('#activity-dialog'));
        })
    }
    //选择营销活动
    commonServer.chooseActivitySure = function($index,$scope,list){
        switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                var linkUrl = '/shop/activity/wheel/'+list.wid+'/'+list.id;
            break;
            case 1://砸金蛋
                var linkUrl = '/shop/activity/egg/index/'+list.wid+'/'+list.id;
            break;
        }
        if($scope.shopLinkPosition == 1){
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.title;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = linkUrl;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        }else if($scope.shopLinkPosition == 2){
            $scope.editors[$scope.index]['linkName'] = list.title;
            $scope.editors[$scope.index]['linkUrl'] = linkUrl;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 3){
            //一级导航
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrlName'] = list.title;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrl'] = linkUrl;
            console.log($scope.menus['menu']);
        }else if($scope.shopLinkPosition == 4){
            //二级导航
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrlName'] = list.title;
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrl'] = linkUrl;
        }else if($scope.shopLinkPosition == 5){
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkName'] = list.title;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkUrl'] = linkUrl;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['dropDown'] = false;
        }
        //add by 韩瑜 2018-8-10
        //魔方添加营销活动入口
        else if($scope.shopLinkPosition == 8){
        	console.log(linkUrl)
        	$scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkUrl'] = linkUrl;
        	$scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 11;//11为营销活动
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.title;
        }
        //add by 韩瑜 2018-9-17
        //商品分组模板营销活动确定
        else if($scope.shopLinkPosition == 9){
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = linkUrl;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.title;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
        }
        hideModel($('#activity_model'));
    }
    //选择营销活动搜索
    commonServer.searchActivity = function($scope){
        $scope.activity_list = [];
        //判断是哪一个活动
        switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                var url = "/merchants/marketing/wheelList?pagesize=6";
            break;
            case 1://砸金蛋
                var url = "/merchants/marketing/egg/index?size=6";
            break;
        }
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post(url,{_token:_token,keyword:$scope.searchTitle},function(data){
            console.log(data);
            if(data.data[0]['data'].length){
                angular.forEach(data.data[0]['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.activity_list.push(val);
                    })
                })
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
             $('.activity_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $scope.activity_list = [];
                        $.post('/merchants/marketing/wheelList?',{_token:_token,pagesize:8,page:page,keyword:$scope.searchTitle},function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.activity_list.push(val)
                                    })
                                })
                            }
                        })
                       
                    }
                });
            showModel($('#activity_model'),$('#activity-dialog'));
        })
    }
    //add by 韩瑜 2018-8-10
    //新建营销活动
    commonServer.newActivity = function($scope){
    	//判断是哪个活动
    	switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                window.open('/merchants/marketing/addWheel');
            break;
            case 1://砸金蛋
        	    window.open('/merchants/marketing/egg/add');
            break;
        }
    }
    //end
    //预约选择弹窗
    commonServer.chooseAppoint = function($index,position,$scope){
        $scope.appointMent = [];//预约列表
        $scope.advsImagesIndex = $index;
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post('/merchants/wechat/bookListApi',{_token:_token},function(data){
            if(data['data']['data'].length){
                angular.forEach(data['data']['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.appointMent.push(val);
                    })
                })
            }
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
             $('.appoint_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $scope.appointMent = [];
                        $.post('/merchants/wechat/bookListApi',{_token:_token,page:page},function(response){
                            if(response.status ==1){
                                $scope.appointMent = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.appointMent.push(val)
                                    })
                                })
                            }
                        })
                       
                    }
                });
            showModel($('#activity_appointment'),$('#activity-dialog-appointment'));
        })
    }
    //预约选择弹窗确定
    commonServer.chooseAppointSure = function($index,$scope,list){
        console.log($scope.advsImagesIndex);
        // console.log($scope.index)
        console.log($scope.editors[$scope.index]['images'][$scope.advsImagesIndex]);
        $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.title;
        $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
        $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
        $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
        // $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        hideModel($('#activity_appointment'));
    }
    //预约选择弹窗搜索
    commonServer.searchAppoint = function($scope){
        $scope.appointMent = [];//预约列表
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post('/merchants/wechat/bookListApi',{_token:_token,title:$scope.searchTitle},function(data){
            if(data['data'].length){
                angular.forEach(data['data']['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.appointMent.push(val);
                    })
                })
            }
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
             $('.appoint_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $scope.appointMent = [];
                        $.post('/merchants/wechat/bookListApi',{_token:_token,page:page,title:$scope.searchTitle},function(response){
                            if(response.status ==1){
                                $scope.appointMent = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.appointMent.push(val)
                                    })
                                })
                            }
                        })
                       
                    }
                });
            showModel($('#activity_appointment'),$('#activity-dialog-appointment'));
        })
    }
    //显示优惠券选择model
    commonServer.showCouponModel = function($scope){
        $scope.searchTitle = '';
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.is_limited == 0){
                        val.limit_desc = '无限制';
                    }else{
                        val.limit_desc = '满'+val.limit_amount+'元可用';
                    }
                    $scope.couponList.push({
                        "id":val.id,
                        "name":val.title,
                        "info":val.description,
                        "amount":val.amount,
                        "amount_random_max":val.amount_random_max,
                        "is_limited":val.is_limited,
                        "limit_amount":val.limit_amount,
                        "is_random":val.is_random,
                        "limit_desc":val.limit_desc
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.coupon_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.couponList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        if(val.is_limited == 0){
                                            val.limit_desc = '无限制';
                                        }else{
                                            val.limit_desc = '满'+val.limit_amount+'元可用';
                                        }
                                        $scope.couponList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "info":val.description,
                                            "amount":val.amount,
                                            "amount_random_max":val.amount_random_max,
                                            "is_limited":val.is_limited,
                                            "limit_amount":val.limit_amount,
                                            "is_random":val.is_random,
                                            "limit_desc":val.limit_desc
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_coupon_model'),$('#coupon_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    // 显示卡密选择model
    commonServer.showCardIdModel = function($scope){
        $scope.searchTitle = '';
        $scope.cardIdList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=20&wid='+ wid, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.cardIdList.push({
                        "id":val.id,
                        "name":val.title,
                        "begin_time":val.begin_time,
                        "end_time":val.end_time,
                        "type":val.type,
                        "stock":val.stock
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.cardId_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=20&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.cardIdList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.cardIdList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "begin_time":val.begin_time,
                                            "end_time":val.end_time,
                                            "type":val.type,
                                            "stock":val.stock
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_card_model'),$('#card_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    // 选择卡密确定按钮
    commonServer.chooseCardIdSure = function($scope){
        if($scope.temp.length>1){
            tipshow('目前只能选择一个活动哦','warn')
            return false
        }
        $scope.baseinfo['cam_id'] = $scope.temp[0].id;
        $scope.cam_id = $scope.temp[0].id;
        $scope.camName = $scope.temp[0].name;
        hideModel($('#my_card_model'));//隐藏Model
        $scope.temp = [];//去除数据
    }
    // 选取卡密
    commonServer.chooseCardId = function($index,list,$scope){
        var wid = $('#wid').val();
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
            $scope.temp.push({'name':list.name,'id':list.id});//添加数据
        }
    }
    // 卡密弹窗搜索
    commonServer.searchCardId = function($scope){
        $scope.cardIdList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=20&wid='+ wid +'&page=1&title=' + $scope.searchTitle, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.cardIdList.push({
                        "id":val.id,
                        "name":val.title,
                        "begin_time":val.begin_time,
                        "end_time":val.end_time,
                        "type":val.type,
                        "stock":val.stock
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.cardId_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=20&wid='+ wid + '&title=' + $scope.searchTitle,function(response){
                            if(response.status ==1){
                                $scope.cardIdList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.cardIdList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "begin_time":val.begin_time,
                                            "end_time":val.end_time,
                                            "type":val.type,
                                            "stock":val.stock
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_card_model'),$('#card_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json') 
    }
    //显示魔方优惠券选择model  add by 黄新琴  2018-7-18
    commonServer.showCubeCouponModel =  function($index,position,$scope){
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.is_limited == 0){
                        val.limit_desc = '无限制';
                    }else{
                        val.limit_desc = '满'+val.limit_amount+'元可用';
                    }
                    $scope.couponList.push({
                        "id":val.id,
                        "name":val.title,
                        "info":val.description,
                        "amount":val.amount,
                        "amount_random_max":val.amount_random_max,
                        "is_limited":val.is_limited,
                        "limit_amount":val.limit_amount,
                        "is_random":val.is_random,
                        "limit_desc":val.limit_desc,
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.coupon_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.couponList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        if(val.is_limited == 0){
                                            val.limit_desc = '无限制';
                                        }else{
                                            val.limit_desc = '满'+val.limit_amount+'元可用';
                                        }
                                        $scope.couponList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "info":val.description,
                                            "amount":val.amount,
                                            "amount_random_max":val.amount_random_max,
                                            "is_limited":val.is_limited,
                                            "limit_amount":val.limit_amount,
                                            "is_random":val.is_random,
                                            "limit_desc":val.limit_desc,
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#cube_coupon_model'),$('#cube_coupon_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    // 优惠券弹窗搜索
    commonServer.searchCoupon = function($scope){
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1&title=' + $scope.searchTitle, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.is_limited == 0){
                        val.limit_desc = '无限制';
                    }else{
                        val.limit_desc = '满'+val.limit_amount+'元可用';
                    }
                    $scope.couponList.push({
                        "id":val.id,
                        "name":val.title,
                        "info":val.description,
                        "amount":val.amount,
                        "amount_random_max":val.amount_random_max,
                        "is_limited":val.is_limited,
                        "limit_amount":val.limit_amount,
                        "is_random":val.is_random,
                        "limit_desc":val.limit_desc
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.coupon_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page + '&title=' + $scope.searchTitle,function(response){
                            if(response.status ==1){
                                $scope.couponList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.couponList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "info":val.description,
                                            "amount":val.amount,
                                            "amount_random_max":val.amount_random_max,
                                            "is_limited":val.is_limited,
                                            "limit_amount":val.limit_amount,
                                            "is_random":val.is_random,
                                            "limit_desc":val.limit_desc
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_coupon_model'),$('#coupon_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json') 
    }
     // 魔方优惠券弹窗搜索  add by 黄新琴 2018-7-18
     commonServer.searchCubeCoupon = function($scope){
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1&title=' + $scope.searchTitle, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.is_limited == 0){
                        val.limit_desc = '无限制';
                    }else{
                        val.limit_desc = '满'+val.limit_amount+'元可用';
                    }
                    $scope.couponList.push({
                        "id":val.id,
                        "name":val.title,
                        "info":val.description,
                        "amount":val.amount,
                        "amount_random_max":val.amount_random_max,
                        "is_limited":val.is_limited,
                        "limit_amount":val.limit_amount,
                        "is_random":val.is_random,
                        "limit_desc":val.limit_desc,
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.coupon_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page + '&title=' + $scope.searchTitle,function(response){
                            if(response.status ==1){
                                $scope.couponList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.couponList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "info":val.description,
                                            "amount":val.amount,
                                            "amount_random_max":val.amount_random_max,
                                            "is_limited":val.is_limited,
                                            "limit_amount":val.limit_amount,
                                            "is_random":val.is_random,
                                            "limit_desc":val.limit_desc,
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#cube_coupon_model'),$('#cube_coupon_model-dialogg'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json') 
    }
    // 选择商品model搜索
    commonServer.goSearch = function($scope,title){
        $scope.goodList = [];
        $http.get('/merchants/product/getproducts?ajax=json&title='+title+'&page=1').success(function(data){
            angular.forEach(data.data.list,function(val,key){
                $scope.goodList.push({
                    "id":val.id,
                    "name":imgUrl + val.title,
                    "thumbnail":val.img,
                    "info":"",
                    "price":val.price,
                    "timeDay":val.created_at,
                    "timestamp":"15:57:27",
                    "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                })
            })
            console.log(data)
            var totalCount = data.data.total_count, showCount = 10,
                limit = data.data.per_page;
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/product/getproducts?ajax=json&page='+page+'&title='+title).success(function(response) {
                            console.log(response);
                            // $scope.goodList = 
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data.list,function(val,key){
                                    $scope.goodList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "thumbnail":imgUrl + val.img,
                                        "info":"",
                                        "price":val.price,
                                        "timeDay":val.created_at,
                                        "timestamp":"15:57:27",
                                        "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                                    })
                                })
                            }
                        });
                    }
                });
        })
    }
    // 隐藏model
    commonServer.hideModel = function(){ 
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
        hideModel($('#spell_Modal'));
        hideModel($('#page_spell_model'));
        hideModel($('#video_model'));
        hideModel($('#activity_appointment'));
        hideModel($('#page_model_pintuan'));
        hideModel($('#page_model_card'));
        hideModel($('#cube_coupon_model'));
        hideModel($('#research_model'));
    }
    //选择商品
    commonServer.choose = function($index,$scope,list){
        console.log(list.id);
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
            $scope.temp.push({'id':list.id,'name':list.name,'thumbnail':list.thumbnail,'price':list.price,'info':list.info,'url':list.url});//添加数据
        }
    }
    //选择享立减商品
    commonServer.choose_shareGoods = function($index,position,$scope){
		$scope.xiangLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.link_type = 8;
        getShareModalList($scope)
    }
    //选择享立减商品确认选择
    commonServer.chooseShopLink_shareGoods= function($index,$scope,list){
    	$scope.goods_show = true;//选择之后隐藏享立减商品确认选择按钮
        if($scope.xiangLinkPosition == 1){
            console.log(list,'this is a flag')
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;       
        }else if($scope.xiangLinkPosition == 2){
            //魔方添加商品
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 3;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.name;
        }
        hideModel($('#shareGoodModel'));
    }
    //图片广告选择商品链接
    commonServer.chooseShopLink = function($index,$scope,list){
        if($scope.shopLinkPosition == 1){
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        }else if($scope.shopLinkPosition == 2){
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 3){
            $scope.menus['menu'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            console.log($scope.menus['menu'][$scope.advsImagesIndex]);
        }else if($scope.shopLinkPosition == 4){
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 5){
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 6){
            //冰冰模板背景链接选择
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 7){
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 8){
            //魔方添加商品
            if($scope.productModal.navIndex == 1){
                // 商品分组
                $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 7;
            }else{
                //商品链接
                $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 1;
            }
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.name;
        }
        /* add by 韩瑜
         * date 2018-9-17
         * 商品分组模板链接到商品
         */
        else if($scope.shopLinkPosition == 9){
			$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
        }
        hideModel($('#chooseShopModel'));
    }
    //图片广告选择微页面链接
    commonServer.choosePageLink = function($index,position,$scope){
        $scope.temp = [];
        // $scope.pageId = [];
        $scope.pageList = [];
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.link_type = 1;
        var wid = $('#wid').val();
        $.get('/merchants/store/selectPage?page=1', function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.page_title,
                        "url":val.url,
                        "created_at":val.created_at
                    })
                })
            })
            
            // console.log($scope.goodList);
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
                                            "created_at":val.created_at
                                        })
                                    })
                                })
                                console.log($scope.pageList);
                            }
                        })
                    }
                });
            showModel($('#page_model'),$('#page-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    //图片广告拼团确定
    commonServer.chooseSpellModelSure = function($index,list,$scope){
        console.log($scope.shopLinkPosition);
        if($scope.shopLinkPosition == 1){
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            // $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        }else if($scope.shopLinkPosition == 2){
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 3){
            //一级导航
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 4){
            //二级导航
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 5){
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 6){
            // 冰冰模板背景链接
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 7){
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 8){
            //魔方添加微页面
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 2;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.name;
        }
        //add by 韩瑜 2018-9-18
        //商品分组模板页
        else if($scope.shopLinkPosition == 9){
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.name;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
        }
        hideModel($('#page_model_pintuan'));
    }
    // 图片广告选择微页面链接确定
    commonServer.choosePageLinkSure = function($index,list,$scope){
        console.log($scope.shopLinkPosition);
        if($scope.shopLinkPosition == 1){
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        }else if($scope.shopLinkPosition == 2){
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 3){
            //一级导航
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 4){
            //二级导航
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 5){
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 6){
            // 冰冰模板背景链接
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 7){
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 8){
            //魔方添加微页面
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 2;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.name;
        }
        /* add by 韩瑜
         * date 2018-9-17
         * 商品分组模板链接到微页面
         */
        else if($scope.shopLinkPosition == 9){
			$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
        }
        hideModel($('#page_model'));
    }
    //搜索微页面
    commonServer.searchPage = function($scope){
       $scope.temp = [];
        // $scope.pageId = [];
       var wid = $('#wid').val();
       $scope.pageList = [];
       $.get('/merchants/store/selectPage?page=1&title=' + $scope.searchTitle, function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.page_title,
                        "url":val.url,
                        "created_at":val.created_at
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/store/selectPage?page=' + page + '&title=' + $scope.searchTitle,function(response){
                            if(response.errCode ==0){
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
                            }
                        })
                    }
                });
            showModel($('#page_model'),$('#page-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    // 选取优惠券
    commonServer.chooseCoupon = function($index,list,$scope){
        var wid = $('#wid').val();
        if($('.choose_btn_'+list.id).hasClass('btn-primary')){
            $('.choose_btn_'+list.id).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('选取'); //改变按钮显示状态
            angular.forEach($scope.temp,function(val,key){
                if(val.id == list.id){
                    $scope.temp.splice(key,1);//清除数据
                    console.log($scope.temp);
                }
            })
        }else{
            $('.choose_btn_'+list.id).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('取消'); //改变按钮显示状态
            $scope.temp.push({'name':list.name,'amount':list.amount,'limit_desc':list.limit_desc,'id':list.id,'url':'/shop/activity/couponDetail/'+ wid + '/' + list.id});//添加数据
        }
    }
    // add by 黄新琴  2018-7-18 魔方选取优惠券
    commonServer.chooseCubeCoupon = function($index,list,$scope){
    	if($scope.shopLinkPosition == 8){
	        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 10;
	        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
	        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] =  list.name;
        }
    	//add by 韩瑜 2018-9-17
        //商品分组模板优惠券确定
    	else if($scope.shopLinkPosition == 9){
    		var wid = $('#wid').val();
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['id'] = list.id;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.name;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] ='/shop/activity/couponDetail/' + wid + '/' + list.id;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
    	}
        hideModel($('#cube_coupon_model'));//隐藏Model
    }
    //确定选择商品
    commonServer.chooseSure =function($scope){
        hideModel($('#myModal'));//隐藏Model
        if($scope.editors[$scope.index]['nodate']==true){
            $scope.editors[$scope.index]['thGoods'] = [];
            $scope.editors[$scope.index]['goods'] = [];
        }
        if($scope.temp.length>0){
            // $scope.editors[$scope.index].products_id = [];
            // var num = $scope.editors[$scope.index]['goods'].length;//记录删除唯一标识
            for(var i=0;i<$scope.temp.length;i++){
                // num ++;
                $scope.editors[$scope.index].products_id.push($scope.temp[i].id);
                // $scope.temp[i]['delete_id'] = num;  
                if($scope.editors[$scope.index]['goods'].length == 0){
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
                $scope.editors[$scope.index]['goods'].push($scope.temp[i]);//合并数组
            }
        }
        $scope.temp = [];//去除数据
        $scope.editors[$scope.index]['nodate'] = false;
    }
    // 商品搜索弹窗搜索
    commonServer.searchGoods = function($scope){
        $scope.goodList = [];
        var wid = $('#wid').val();
        $http.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page=1'+ '&title=' + $scope.searchTitle).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                $scope.goodList.push({
                    "id":val.id,
                    "name":val.title,
                    "thumbnail":imgUrl + val.img,
                    "info":"",
                    "price":val.price,
                    "timeDay":val.created_at,
                    "timestamp":"15:57:27",
                    "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            // $scope.goodList = 
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.goodList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "thumbnail":imgUrl + val.img,
                                        "info":"",
                                        "price":val.price,
                                        "timeDay":val.created_at,
                                        "timestamp":"15:57:27",
                                        "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                                    })
                                })
                            }
                        });
                    }
                });
        })
    }
    // add by 赵彬 2018-8-15
    //拼团商品搜索
    commonServer.searchSpell = function($scope){
        $scope.spellGoodList = [];
        $http.get('/merchants/grouppurchase/groupList?title=' + $scope.searchTitle).then(function(data){
            console.log(data.data.data[0]);
            if(data.data.data[0].data.length){
                $scope.spellGoodList = [];  
                angular.forEach(data.data.data[0].data,function(val,key){
                    console.log(val)
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
                        $http.get('/merchants/grouppurchase/groupList?page='+page + '?title=' + $scope.searchTitle).success(function(response) {
                           $scope.spellGoodList = [];  
                            angular.forEach(data.data.data[0].data,function(val,key){
                                $scope.spellGoodList.push(val);
                            })
                        });
                    }
                });
            showModel($('#spell_Modal'),$('#spell-modal-dialog'));
        })
    }
    //end
    // 选择优惠券确定按钮
    commonServer.chooseCouponSure = function($scope){
        hideModel($('#my_coupon_model'));//隐藏Model
        if($scope.temp.length>0){
            for(var i=0;i<$scope.temp.length;i++){
                if(i<3){
                    if($scope.editors[$scope.index]['couponList'].length <= 2){
                        $scope.editors[$scope.index]['couponList'].push($scope.temp[i]);//合并数组
                        $scope.editors[$scope.index]['coupons_id'].push($scope.temp[i]['id']);
                    }
                }
            }
        }
        console.log($scope.editors[$scope.index]['coupons_id']);
        $scope.temp = [];//去除数据
    }
    //显示删除按钮
    commonServer.showDelete = function($index,$scope){
        $scope.editors[$scope.index]['goods'][$index]['deleteBtn'] = true;
    }
    //隐藏删除按钮
    commonServer.hideDelete = function($index,$scope){
        $scope.editors[$scope.index]['goods'][$index]['deleteBtn'] = false;
    }
    //删除图片
    commonServer.delete = function($index,$scope){
        $scope.editors[$scope.index]['products_id'].splice($index,1);
        $scope.editors[$scope.index]['goods'].splice($index,1);
        // angular.forEach($scope.editors[$scope.index]['goods'],function(val,key){
        //     if(val.delete_id == undefined && val.delete_id == image.delete_id){
        //         $scope.editors[$scope.index]['goods'].splice(key,1);
        //     }
        // })
        // console.log(Math.floor($index/3));
        $scope.editors[$scope.index]['thGoods'][Math.floor($index/3)].splice($index%3,1);
        if($scope.editors[$scope.index]['thGoods'][Math.floor($index/3)].length==0){
            $scope.editors[$scope.index]['thGoods'].splice([Math.floor($index/3)],1)
        }
    }
     //删除模块
    commonServer.deleteAll = function($index,$scope){
        $scope.editors.splice($index,1);
        $scope.index--;
        if($scope.index>=0){
            $scope.editors[$scope.index]['showRight'] = false;
        }
    }
    // 广告图片添加
    commonServer.addAdvImages = function($scope,position){
        $scope.removeClassEditing();
        if(position== 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':5, //3为富文本，4商品，5商品列表
                    'type':'image_ad',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'advsListStyle':2, //1默认，2，折叠轮播，3分开显示
                    'advSize':1, //1为大图，2为小图
                    'resize_image':1, //1放大 0 不放大
                    'images':[],
                    'is_add_content':false    
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':5, //3为富文本，4商品，5商品列表
                'type':'image_ad',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'advsListStyle':2, //1默认，2，折叠轮播，3分开显示
                'advSize':1, //1为大图，2为小图
                'resize_image':1, //1放大 0 不放大
                'images':[],
                'is_add_content':false    
            })
        }
        $scope.initCartRight();
    }
    commonServer.initchooseAdvImage = function($scope){
        $scope.tempUploadImage =[];
        angular.forEach($scope.uploadImages,function(data,index){
            data.isShow = false;
        })
    }
    //点击添加广告弹出model
    commonServer.addAdvs = function($scope,p_index,$index){
    	$scope.advImageIndex = p_index;//保存父级index
    	$scope.advImageIndex2 = $index;//保存子级index
        $scope.uploadShow = false;
        $scope.eventKind=1;
        $scope.grounps = [];
        $scope.choosePosition = 1;//图片广告
        // $scope.eventKind = 1;
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
    }
    // 分享添加图片
    commonServer.addShareImages = function($scope){
        $scope.uploadShow = false;
        $scope.grounps = [];
        $scope.choosePosition = 1;//图片广告
        // $scope.eventKind = 1;
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                //判断分组是否有数据 2018-10-22 update by 倪凯嘉
                if(response.data[0].data.length){
                    $scope.picNumber=true;
                }else{
                    $scope.picNumber=false;
                }
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
    }
    // 点击图片分组
    commonServer.chooseGroup = function($scope,grounp){
        angular.forEach($scope.grounps,function(val,key){
            val.isactive = false;
        })
        var classifyId = grounp.id;
        $('input[name="classifyId"]').val(classifyId);
        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(data){
            //判断分组是否有数据 2018-10-22 update by 倪凯嘉
            if(data.data[0].data.length){
                $scope.picNumber=true;
            }else{
                $scope.picNumber=false;
            }
            angular.forEach(data.data[0].data,function(val,key){
                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
            })
            $scope.uploadImages = data.data[0].data;
            var totalCount = data.data[0].total, showCount = 10,
            limit = data.data[0].per_page;
            $('.ui-pagination').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(data){
                        angular.forEach(data.data[0].data,function(val,key){
                            val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];

                        })
                        $scope.uploadImages = data.data[0].data;
                    })
                }
            });
        })
        grounp.isactive = true;
    }
    /**
     * @author: 
     * @description: 
     * @param {string} 图片路径 [image]
     * @param {number} 索引 [$index]
     * @param {object} 页面数据 [$scope]
     * @return: void
     * @Date:
     * update: 戴江淮（npr5778@dingtalk.com）2019-10-29 增加会员卡封面图
     */
    commonServer.chooseImage = function(image,$index,$scope){
        if($scope.eventKind == 1){
            if(image.isShow==false){
                image.isShow=true;
                image['index'] = $index;
                image['chooseLink'] = false; //控制是否显示已经选过链接
                image['dropDown'] = false;
                image['linkName'] = '';
                image['pageCurrent'] = false;
                $scope.tempUploadImage.push(image);
            }else{
                image.isShow=false;
                for(var i=0;i<$scope.tempUploadImage.length;i++){
                    if($scope.tempUploadImage[i]['index']==$index){
                        $scope.tempUploadImage.splice(i,1);
                    }
                }
            }
        }else if($scope.eventKind == 2){
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 3){
            //微页面分享图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 4){
            // 签到分享图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 5){
            // 魔方添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 6){
            // 联系方式添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 7){
            // 商品分组模板banner添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 8){
            // 商品分组模板item添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        } else if ($scope.eventKind == 9) {
            // 会员卡选择图片
            $scope.initchooseAdvImage();
            if ($scope.tempUploadImage.length > 1) {
                $scope.tempUploadImage = [];
            } else {
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }
    }
    //选择广告图片确定按钮
    // 上传尺寸限制 by 崔源 2018.10.19
    commonServer.chooseAdvSureBtn = function($scope){
        console.log($scope,'scope')
        if($scope.tempUploadImage.length>0){
            angular.forEach($scope.tempUploadImage,function(val,key){
                val.image_id = val.FileInfo.id;
            })
        }
        if($scope.eventKind == 1){
            var temloadImg=$scope.tempUploadImage
            for(var i=temloadImg.length - 1; i>=0; i--){
                $scope.safeApply(function(){
                $scope.image = angular.copy($scope.tempUploadImage[i]);
                var advertisingsize=$scope.image['FileInfo']['img_size'].split('x');
                //宽advertisingsize[0]
                //高advertisingsize[1]
                if (parseInt(advertisingsize[0]) < 150) {
                    tipshow('图片宽度小于150px，请重新上传','warm');
                    $scope.hideModel();
                    return false;
                } else {
                    if($scope.image['FileInfo']['path'].indexOf(imgUrl)>=0){
                        $scope.image['FileInfo']['path'] =  $scope.image['FileInfo']['path'];
                    }else{
                        $scope.image['FileInfo']['path'] =  imgUrl + $scope.image['FileInfo']['path'];
                    }
                }
                $scope.editors[$scope.index]['images'].push($scope.image);
            })
         }
        }else if($scope.eventKind == 2 && !$scope.changeImange){
            if($scope.choosePosition == 1){
                // 图片广告
                var display=$scope.tempUploadImage[0]['FileInfo']['img_size'].split('x');
                var displaysize=$scope.tempUploadImage[0]['FileInfo']['size']
                if (parseInt(display[0]) < 150) {
                    tipshow('图片宽度小于150px，请重新上传','warm');
                    $scope.hideModel();
                     return false;
                }
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]=$scope.tempUploadImage[0];
            }else if($scope.choosePosition == 2){
                // 图片导航
                var picnav=$scope.tempUploadImage[0]['FileInfo']['img_size'].split('x');
                if (picnav[0] / picnav[1] < 0.8 || picnav[0] / picnav[1] > 1.2) {
                    tipshow('比例非1:1，请上传正确的比例图片','warm');
                    $scope.hideModel();
                     return false;
                }else{
                    if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                        $scope.editors[$scope.index]['images'][$scope.advImageIndex]['thumbnail'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                    }else{
                        $scope.editors[$scope.index]['images'][$scope.advImageIndex]['thumbnail'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                    }
                } 
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['image_id'] = $scope.tempUploadImage[0]['FileInfo']['id'];
            }
        }else if($scope.changeImange){
            if($scope.choosePage == 2){
                //会员主页
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[$scope.index]['thumbnail'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[$scope.index]['thumbnail'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
                $scope.editors[$scope.index]['path'] = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else if($scope.choosePage == 1){
                //美妆小店
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 3){
                //冰冰模板背景选择
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 4){
                //冰冰模板修改图标
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['lists'][$scope.advImageIndex]['icon'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['lists'][$scope.advImageIndex]['icon'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 5){
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['lists'][$scope.advImageIndex]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['lists'][$scope.advImageIndex]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }
        }else if($scope.eventKind == 3){
            console.log(333333)
            //微页面分享图片选择
            var micshare=$scope.tempUploadImage[0]['FileInfo']['img_size'].split('x')
            var size = $scope.tempUploadImage[0]['FileInfo']['size']
            console.log(size, 'size')
            if (parseInt(micshare[0]) / parseInt(micshare[1]) > 1.2 || parseInt(micshare[0]) / parseInt(micshare[1]) < 0.8 ) {
                tipshow('图片比例非1:1，请重新上传图片','warm');
                $scope.hideModel();
                 return false;
            }
            else if (parseInt(micshare[0]) < 400){
                tipshow('图片尺寸小于400px，请重新上传', 'warm');
                $scope.hideModel();
                return false;
            }
            else if (parseInt(size) > 1024000 * 3){
                tipshow('图片容量超过3M，请重新上传', 'warm');
                $scope.hideModel();
                return false;
            }
            else{
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.pageSeting.share_img = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.pageSeting.share_img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                } 
                $scope.addPic="修改图片";
                $scope.classShow=false;        
            }
        }else if($scope.eventKind == 4){
            //签到分享
            //增加图片尺寸限制 by倪凯嘉 2018.10.17
            let imgSize=$scope.tempUploadImage[0]['FileInfo'].img_size.split("x");
            let imgWidth=imgSize[0];//图片实际宽度
            let imgHeight=imgSize[1];//图片实际高度
            let num = parseInt(imgWidth / imgHeight * 100) / 100
            let sum = parseInt(750 / 750 * 100) / 100
            if( num < sum - 0.2 || num > sum + 0.2){
                tipshow("图片比例非1:1，请重新上传","warn");
                return false
            }
            if(imgWidth < 400){
                tipshow("图片尺寸小于400px，请重新上传","warn");
                return false
            }
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                $scope.editors[0].share_img = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else{
                $scope.editors[0].share_img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
            }
            $scope.addPic="修改图片";
            $scope.classShow=false;
        }else if($scope.eventKind == 6){
            //联系方式添加图片
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                if($scope.callPhoneType == 2){
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['icon'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                } 
            }else{
                if($scope.callPhoneType == 2){
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['icon'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];;
                }else{
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }
        }else if($scope.eventKind == 5){
            //魔方功能添加图片
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else{
                $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
            }
            $scope.editors[$scope.index].isAddPic = true;
            if($scope.cube.selectLayoutIndex == 0){//判断第一个图片设置宽高比
                var img = $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img;
                var realWidth;//真实的宽度
                var realHeight;//真实的高度
                //这里做下说明，$("<img/>")这里是创建一个临时的img标签，类似js创建一个new Image()对象！
                $("<img/>").attr("src", img).load(function() {
                    /*
                    如果要获取图片的真实的宽度和高度有三点必须注意
                    1、需要创建一个image对象：如这里的$("<img/>")
                    2、指定图片的src路径
                    3、一定要在图片加载完成后执行如.load()函数里执行
                    */
                    realWidth = this.width;
                    realHeight = this.height;
                    //如果真实的宽度大于浏览器的宽度就按照100%显示
                    $scope.$apply(function(){
                        $scope.editors[$scope.index].aspectRatio = realHeight/realWidth;//设置高宽比
                    })
                })
            }
            //验证图片是否添加完整
            var isAllImgAdd = true;
            angular.forEach($scope.editors[$scope.index].content,function(val,index){
                if(!val.img){ //有图片未添加
                    isAllImgAdd = false;
                }
            })
            //若是图片添加完整通过判断
            if(isAllImgAdd){
                $scope.editors[$scope.index].isPromptAddPic = false;
            }
            console.log($scope.tempUploadImage[0]['FileInfo']['path'])
        }
        /* add by 韩瑜 
         * 2018-9-17
         * 商品分组模板banner添加图片
         */
        else if($scope.eventKind == 7){
        	var url = $scope.tempUploadImage[0]['FileInfo']['path']
			if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
				//去除域名
				url = url.replace(imgUrl, "")
            }
            $scope.editors[$scope.index]['classifyList'][$scope.advImageIndex].thumbnail = url;
        }else if($scope.eventKind == 8){//商品分组模板item添加图片
        	var url = $scope.tempUploadImage[0]['FileInfo']['path']
			if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
				//去除域名
				url = url.replace(imgUrl, "")
            }
            $scope.editors[$scope.index]['classifyList'][$scope.advImageIndex]['subClassifyList'][$scope.advImageIndex2].thumbnail = url;
        } else if ($scope.eventKind == 9) {
            var url = $scope.tempUploadImage[0]['FileInfo']['path']
            if ($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl) >= 0) {
                url = url.replace(imgUrl, "")
            }
            $scope.editors[$scope.index].cardList[$scope.advImageIndex]['card_img'] = imgUrl + url
        }
        //end
        $scope.hideModel();
    }
     //广告图片重新上传
    commonServer.reUpload = function($index,$scope){
        $scope.eventKind=2;
        $scope.changeImange = false;
        $scope.uploadShow = false;
        // $scope.eventKind = 1;
        $scope.choosePosition = 1;//图片广告
        $scope.grounps = []; 
        
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
        $scope.advImageIndex = $index
        showModel($('#myModal-adv'),$('#modal-dialog-adv'));
    }
    //删除广告图片
    commonServer.removeAdvImages = function($index,$scope){
        $scope.editors[$scope.index]['images'].splice($index,1);
    }
    // 选择链接
    commonServer.chooseLinkUrl = function($event,$index,position,url,linktype,$scope){
        $scope.link_type = linktype;
        if(position==1){
            $scope.editors[$scope.index]['images'][$index]['linkUrl'] = url;
            $scope.editors[$scope.index]['images'][$index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['images'][$index]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$index]['dropDown'] = false;
            $scope.editors[$scope.index]['images'][$index]['link_type'] = linktype;
        }else if(position==2){
            $scope.editors[$scope.index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['linkUrl'] = url;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if(position==3){
            $scope.editors[$scope.index]['images'][$index]['linkUrl'] = url;
            $scope.editors[$scope.index]['images'][$index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['images'][$index]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$index]['link_type'] = linktype;
            if($scope.editors[$scope.index]['images'][$index]['dropDown']){
                $scope.editors[$scope.index]['images'][$index]['dropDown'] = false;
            }else{
                $scope.editors[$scope.index]['images'][$index]['dropDown'] = true;
            }
        }else if(position == 5){
            $scope.editors[$scope.index]['textlink'][$index]['linkUrl'] = url;
            $scope.editors[$scope.index]['textlink'][$index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['textlink'][$index]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$index]['link_type'] = linktype;
            if($scope.editors[$scope.index]['textlink'][$index]['dropDown']){
                $scope.editors[$scope.index]['textlink'][$index]['dropDown'] = false;
            }else{
                $scope.editors[$scope.index]['textlink'][$index]['dropDown'] = true;
            }
        }else if(position == 6){
            // 冰冰模板背景链接选择
            $scope.editors[$scope.index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['linkUrl'] = url;
            $scope.editors[$scope.index]['dropDown'] = $scope.editors[$scope.index]['dropDown'] == true ? false : true;
        }else if(position == 7){
            $scope.editors[$scope.index]['lists'][$index]['linkName'] = $event.target.text;
            $scope.editors[$scope.index]['lists'][$index]['linkUrl'] = url;
            $scope.editors[$scope.index]['lists'][$index]['dropDown'] = $scope.editors[$scope.index]['lists'][$index]['dropDown'] == true ? false : true;
        }
    }
    //链接删除
    commonServer.removeLink = function($index,position,$scope){
        if(position==1){
            $scope.editors[$scope.index]['images'][$index]['chooseLink'] = false;
            $scope.editors[$scope.index]['images'][$index]['linkUrl'] = '';
            $scope.editors[$scope.index]['images'][$index]['linkName'] = '';
            $scope.editors[$scope.index]['images'][$index]['link_type'] = '';
        }else if(position==2){
            $scope.editors[$scope.index]['chooseLink'] = false;
            $scope.editors[$scope.index]['linkUrl'] = '';
            $scope.editors[$scope.index]['linkName'] = '';
            $scope.editors[$scope.index]['link_type'] = '';
        }else if(position == 3){
            $scope.editors[$scope.index]['textlink'][$index]['chooseLink'] = false;
            $scope.editors[$scope.index]['textlink'][$index]['linkUrl'] = '';
            $scope.editors[$scope.index]['textlink'][$index]['linkName'] = '';
            $scope.editors[$scope.index]['textlink'][$index]['link_type'] = '';
        }
    }
    // 显示dropdown
    commonServer.showDown = function($index,position,$scope){
        if(position==1){
            $scope.editors[$scope.index]['images'][$index]['dropDown'] = true;
        }else if(position==2){
            $scope.editors[$scope.index]['dropDown'] = true;
        }else if(position == 5){
            $scope.editors[$scope.index]['textlink'][$index]['dropDown'] = true;
        }
    }
    //自定义link
    commonServer.customLink = function($event,$index,position,$scope){
        $('.js-link-placeholder').val('');
        $('.popover-link-wrap').show();
        if(position==1){
            $scope.sureProverPosition = 1;
            $scope.advImageIndex = $index;
            $('.popover-link-wrap').css('top',$('#settinglink_' + $index).offset().top-30);
            $('.popover-link-wrap').css('left',$('#settinglink_' + $index).offset().left-$('.popover-link-wrap').width());
        }else if(position==2){
            $scope.sureProverPosition = 2;
            $('.popover-link-wrap').css('top',$('#setlink_' + $scope.index).offset().top-30);
            $('.popover-link-wrap').css('left',$('#setlink_' + $scope.index).offset().left-$('.popover-link-wrap').width());
        }else if(position==3){
            $scope.sureProverPosition = 2;
            $('.popover-link-wrap').css('top',$('#stlink_' + $scope.index).offset().top-30);
            $('.popover-link-wrap').css('left',$('#stlink_' + $scope.index).offset().left-$('.popover-link-wrap').width());
        }else if(position==4){
            $scope.sureProverPosition = 2;
            $('.popover-link-wrap').css('top',$('#change_link_' + $scope.index).offset().top-40);
            $('.popover-link-wrap').css('left',$('#change_link_' + $scope.index).offset().left-$('.popover-link-wrap').width()-90);
        }else if(position==5){
            $scope.sureProverPosition = 2;
            $('.popover-link-wrap').css('top',$('#cglink_' + $scope.index).offset().top-40);
            $('.popover-link-wrap').css('left',$('#cglink_' + $scope.index).offset().left-$('.popover-link-wrap').width()-140);
        }else if(position==6){
            $scope.sureProverPosition = 1;
            $scope.advImageIndex = $index;
            $('.popover-link-wrap').css('top',$('#link_name_').offset().top-30);
            $('.popover-link-wrap').css('left',$('#link_name_').offset().left-$('.popover-link-wrap').width()-15);
        }else if(position == 7){
            //冰冰模板一级
            $scope.sureProverPosition = 2;
            $('.popover-link-wrap').css('top',$('#guanwang').offset().top-30);
            $('.popover-link-wrap').css('left',$('#guanwang').offset().left-$('.popover-link-wrap').width()+15);
        }else if(position == 8){
            //冰冰模板二级
            $scope.sureProverPosition = 3;
            $scope.advImageIndex = $index;
            $('.popover-link-wrap').css('top',$('#guanwang_level_'+ $index).offset().top-30);
            $('.popover-link-wrap').css('left',$('#guanwang_level_'+ $index).offset().left-$('.popover-link-wrap').width()+15);
        }
    }
    commonServer.sureProver = function($scope){
        var url = $('.js-link-placeholder').val().substr(0,4).toLowerCase() == "http" ? $('.js-link-placeholder').val() : "http://" + $('.js-link-placeholder').val();
        if($scope.sureProverPosition==1){
            $scope.editors[$scope.index]['images'][$scope.advImageIndex]['linkUrl'] = url;
            if($scope.editors[$scope.index]['images'][$scope.advImageIndex]['linkUrl'] !=''){
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['chooseLink'] = true;
            }else{
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['chooseLink'] = false;
            }
        }else if($scope.sureProverPosition==2){
            $scope.editors[$scope.index]['linkUrl'] = url;
            $scope.editors[$scope.index]['linkName'] = url;
            if($scope.editors[$scope.index]['linkUrl'] != ''){
                $scope.editors[$scope.index]['chooseLink'] = true;
            }else{
                $scope.editors[$scope.index]['chooseLink'] = false;
            }
        }else if($scope.sureProverPosition==3){
            $scope.editors[$scope.index]['lists'][$scope.advImageIndex]['linkUrl'] = url;
            $scope.editors[$scope.index]['lists'][$scope.advImageIndex]['linkName'] = url;
        }
        $('.popover').hide();
    }
    commonServer.cancelProver = function(){
        $('.popover').hide();
    }
    //添加标题
    commonServer.addTitle = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':6, //3为富文本，4商品，5商品列表，6为标题
                    'type':'title',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'titleName':'',
                    'titleStyle':1,//1为传统样式，2模仿微信图文样式
                    'subTitle':'',
                    'showPosition':1,//1居左显示，2居中，3居右
                    'bgColor':'#ffffff',
                    'addLink':false,//是否添加一个文本链接
                    'chooseLink':false,
                    'dropDown':false,
                    'linkName':'',//链接名字
                    'linkUrl':'',
                    'date':'',
                    'author':'',
                    'wlinkTitle':'',
                    'wlinkUrlChoose':1,//1引导关注，2其他链接
                    'wlinkUrl':'',
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':6, //3为富文本，4商品，5商品列表，6为标题
                'type':'title',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'titleName':'',
                'titleStyle':1,//1为传统样式，2模仿微信图文样式
                'subTitle':'',
                'showPosition':1,//1居左显示，2居中，3居右
                'bgColor':'#ffffff',
                'addLink':false,//是否添加一个文本链接
                'chooseLink':false,
                'dropDown':false,
                'linkName':'',//链接名字
                'linkUrl':'',
                'date':'',
                'author':'',
                'wlinkTitle':'',
                'wlinkUrlChoose':1,//1引导关注，2其他链接
                'wlinkUrl':'',
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    // 传统样式添加一个文本链接
    commonServer.addLink = function($scope){
        $scope.editors[$scope.index]['addLink'] = true;
    }
    // 关闭文本导航
    commonServer.deleteLinkWb = function($scope){
        $scope.editors[$scope.index]['addLink'] = false;
        $scope.editors[$scope.index]['chooseLink'] = false;
        $scope.editors[$scope.index]['linkTitle'] = '';
        $scope.editors[$scope.index]['linkName'] = '';
        $scope.editors[$scope.index]['linkUrl'] = '';
    }
    //绑定laydate
    commonServer.bindDate = function(start){
        laydate(start);
    }
    //添加店铺导航
    commonServer.addShop = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':7, //3为富文本，4商品，5商品列表，6为标题,7为进入店铺
                    'type':'store',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'store_name':store.shop_name,
                    'id':store.id,
                    'url':store.url,
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':7, //3为富文本，4商品，5商品列表，6为标题,7为进入店铺
                'type':'store',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'store_name':store.shop_name,
                'id':store.id,
                'url':store.url,
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加公告
    commonServer.addNotice = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                    'type':'notice',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'content':'',
                    'placeholder':'请填写内容，如果过长，将会在手机上滚动显示',
                    'is_add_content':false,
                    'colorBg':'#ffffcc',
                    'txtBg':'#ff9900',
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                'type':'notice',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'content':'',
                'placeholder':'请填写内容，如果过长，将会在手机上滚动显示',
                'is_add_content':false,
                'colorBg':'#ffffcc',
                'txtBg':'#ff9900',
            })
        }
        $scope.initCartRight();
        console.log($scope.editors);
    }
    // 添加搜索
    commonServer.addSearch = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':11, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'search',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'bgColor':'#ffffff',
                    'is_add_content':false,
                    "searchName": "搜索商品",
                    "searchStyle": 1,//搜索样式
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':11, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'search',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'bgColor':'#ffffff',
                'is_add_content':false,
                "searchName": "搜索商品",
                "searchStyle": 1,//搜索样式
            })
        }
        $scope.initCartRight();
    }
    // 添加商品列表
    commonServer.addGoodsList = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push({
                'showRight':true,
                'cardRight':12, //3为富文本，4商品，5商品列表
                'type':'goodslist',
                // 'content':$sce.trustAsHtml(html),
                'groupName':'',
                'group_id':'',
                'editing':'editing',
                'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                'cardStyle':1,
                'showSell':true,
                'btnStyle':1, //分四种情况
                'goodName':false, //默认不显示
                'goodInfo':false,
                'priceShow':true,//默认显示
                'nodate':true,
                'showNum':6,
                'goods':[],
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
                'is_add_content':false
            })
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':12, //3为富文本，4商品，5商品列表
                'type':'goodslist',
                // 'content':$sce.trustAsHtml(html),
                'groupName':'',
                'group_id':'',
                'editing':'editing',
                'listStyle':3, //列表样式：1大图显示，2小图显示，3一大一小显示，4，详细列表
                'cardStyle':1,
                'showSell':true,
                'btnStyle':1, //分四种情况
                'goodName':false, //默认不显示
                'goodInfo':false,
                'priceShow':true,//默认显示
                'nodate':true,
                'showNum':6,
                'goods':[],
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
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //商品列表商品分组选择
    commonServer.addShopGroup = function($scope,position,$index){
        $scope.goodsGroupList = [];
        $scope.searchTitle = '';
        $scope.position = position;
        var wid = $('#wid').val();
        $scope.temp = [];
        $scope.groupIndex = $index;
        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page=1'+ '&title=').success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                $scope.goodsGroupList.push({
                    "id":val.id,
                    "name":val.title,
                    "created_at":val.created_at
                })
            })
            if(data.status == 1){
                showModel($('#goodslist_model'),$('#goodslist_model_dialog'));
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.page_shopgroup').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            // $scope.goodList = 
                            if(response.status ==1){
                                $scope.goodsGroupList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.goodsGroupList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.created_at
                                    })
                                })
                            }
                        });
                    }
                });
        })
    }
    // 商品列表分组搜索
    commonServer.searchShopGroup = function($scope){
        $scope.goodsGroupList = [];
        var wid = $('#wid').val();
        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page=1'+ '&title=' + $scope.searchTitle).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                $scope.goodsGroupList.push({
                    "id":val.id,
                    "name":val.title,
                    "created_at":val.created_at
                })
            })
            if(data.status == 1){
                showModel($('#goodslist_model'),$('#goodslist_model_dialog'));
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.page_shopgroup').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            // $scope.goodList = 
                            if(response.status ==1){
                                $scope.goodsGroupList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.goodsGroupList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.created_at
                                    })
                                })
                            }
                        });
                    }
                });
        })
    }
    //商品列表商品分组选择确定
    commonServer.chooseShopGroupSure = function($index,list,$scope){
    	console.log(list,'list')
        if($scope.position == 1){
            $scope.editors[$scope.index]['groupName'] = list.name;
            $scope.editors[$scope.index]['group_id'] = list.id;
            hideModel($('#goodslist_model'),$('#goodslist_model_dialog'));
        }else if($scope.position == 2){
        	console.log(list.isActive,'list.isActive')
            list.num = 10;
            if(list.isActive){
            	console.log($scope.temp,'$scope.temp')
                list.isActive = false;
                angular.forEach($scope.temp,function(val,key){
                    if(list.id == val.id){
                        $scope.temp.splice(key,1);
                    } 
                })
            }else{
                $scope.temp.push(list);
                list.isActive = true;
            }
        }else if($scope.position == 3){
            if($scope.editors[$scope.index]['group_type'] == 1){
                $scope.editors[$scope.index]['left_nav'][$scope.groupIndex] = list;
            }else if($scope.editors[$scope.index]['group_type'] == 2){
                $scope.editors[$scope.index]['top_nav'][$scope.groupIndex] = list;
            }
            hideModel($('#goodslist_model'));
        }
    }
    //自定义模块添加
    commonServer.addModel = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':13, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'model',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'modelName':'',
                    'id':'',
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':13, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'model',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'modelName':'',
                'id':'',
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    // 显示添加自定义模块model
    commonServer.showComponentModel = function($scope){
        $scope.searchTitle = '';
        $scope.components = [];//自定义模块数组
        $http.get('/merchants/store/getCustomtemplate?page=1&title='+$scope.searchTitle).success(function(data){
            if(data.data.length>0){
                angular.forEach(data.data,function(val,key){
                    $scope.components.push({
                        name:val.name,
                        id:val.id,
                        created_at:val.created_at
                    })
                })
            }
            console.log($scope.components)
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                $('.page_component').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/store/getCustomtemplate?page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            // $scope.goodList = 
                            if(data.data.length > 1){
                                $scope.components = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.components.push({
                                        "id":val.id,
                                        "name":val.name,
                                        "created_at":val.created_at
                                    })
                                })
                            }
                        });
                    }
                });

        })
        showModel($('#component_model'),$('#component_model_dialog'));
    }
    // 添加自定义Model确定
    commonServer.chooseComponent = function($scope,$index,list){
        $scope.editors[$scope.index]['modelName'] = list.name;
        $scope.editors[$scope.index]['id'] = list.id;
        hideModel($('#component_model'));
    }
    //搜索自定义model
    commonServer.searchComponent = function($scope){
        $scope.components = [];//自定义模块数组
        $http.get('/merchants/store/getCustomtemplate?page=1&title='+$scope.searchTitle).success(function(data){
            if(data.data.length>0){
                angular.forEach(data.data,function(val,key){
                    $scope.components.push({
                        name:val.name,
                        id:val.id,
                        created_at:val.created_at
                    })
                })
            }
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                $('.page_component').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/store/getCustomtemplate?page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            // $scope.goodList = 
                            if(data.data.length > 1){
                                $scope.components = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.components.push({
                                        "id":val.id,
                                        "name":val.name,
                                        "created_at":val.created_at
                                    })
                                })
                            }
                        });
                    }
                });

        })
    }   
    // 添加商品分组
    commonServer.addGoodGroup = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':14, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'good_group',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'left_nav':[],//左侧菜单
                    'top_nav':[],//顶部菜单
                    'group_type':1,//1为左侧菜单，2为顶部菜单
                    'is_add_content':false,
                    //update by 华亢 2018-7-12
                    'group_pinpu':0, // 0为不平铺，1为平铺
                    'listStyle': 0, //update by 邓钊 2018-8-29 0详细列表 1小图
                    'btnStyle':0, //0 样式1  1 样式2  2 样式3  3 样式4
                    //end
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':14, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'good_group',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'left_nav':[],//左侧菜单
                'top_nav':[],//顶部菜单
                'group_type':1,//1为左侧菜单，2为顶部菜单
                'is_add_content':false,
                //update by 华亢 2018-7-12
                'group_pinpu':0, // 0为不平铺，1为平铺
                'listStyle': 0, //update by 邓钊 2018-8-29 0详细列表 1小图
                'btnStyle':0, //0 样式1  1 样式2  2 样式3  3 样式4
                //end
            })
        }
        $scope.initCartRight();
    }
    // 商品分组数量选择
    commonServer.chooseNum = function($scope,list,num){
        list.num = num;
    }
    // 选择分组确定
    commonServer.chooseGroupSure = function($scope){
        if($scope.temp.length>0){
            if($scope.editors[$scope.index]['group_type'] == 2){
                if($scope.editors[$scope.index]['top_nav'].length + $scope.temp.length>4){
                    tipshow('分组数量不能大于4个','warn');
                    hideModel($('#goodslist_model'));
                    return;
                }
            }
            angular.forEach($scope.temp,function(val,key){
                if($scope.editors[$scope.index]['group_type'] == 1){
                    $scope.editors[$scope.index]['left_nav'].push(val);
                }else if($scope.editors[$scope.index]['group_type'] == 2){
                    $scope.editors[$scope.index]['top_nav'].push(val);
                }
            })
            $scope.editors[$scope.index]['width'] = 100 / $scope.editors[$scope.index]['top_nav'].length;
        }
        hideModel($('#goodslist_model'));
    }
    // 删除一个选中分组
    commonServer.deleteGroup = function($index,$scope){
        if($scope.editors[$scope.index]['group_type'] == 1){
            $scope.editors[$scope.index]['left_nav'].splice($index,1);
        }else{
            $scope.editors[$scope.index]['top_nav'].splice($index,1);
        }
        $scope.editors[$scope.index]['width'] = 100/$scope.editors[$scope.index]['top_nav'].length;
    }
    // 添加图片导航
    commonServer.addLinkImages = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':15, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'image_link',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'images':[
                        {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                        {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                        {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                        {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''}
                    ],
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':15, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'image_link',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'images':[
                    {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                    {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                    {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''},
                    {'linkTitle':'','linkName':'','linkUrl':'','chooseLink':'','dropDown':false,'thumbnail':'','image_id':'','link_type':'','link_id':''}
                ],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加图片导航选择图片
    commonServer.chooseLinkImage = function($scope,$index){
        $scope.choosePosition = 2;
        $scope.eventKind=2;
        $scope.changeImange = false;
        $scope.uploadShow = false;
        $scope.grounps = [];
        $scope.advImageIndex = $index;
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                console.log($scope.uploadImages);
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                             console.log($scope.uploadImages);
                        })
                    }
                });
            })
            $scope.initchooseAdvImage();
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
    }
    //获取拼图列表
    commonServer.getSpellList = function($scope){
        $http.get('/merchants/grouppurchase/groupList?pageSize=6').then(function(data){
            if(data.data.data[0].data.length){
                $scope.spellGoodList = [];  
                angular.forEach(data.data.data[0].data,function(val,key){
                    $scope.spellGoodList.push(val);
                })
            }
             var totalCount = data.data.data[0].total, showCount = 10,
                limit = data.data.data[0].per_page;
                $('.spell_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/grouppurchase/groupList?page='+ page +'&pageSize=6').success(function(data) {
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
    //拼团链接选取 @author huoguanghui
    commonServer.chooseSpell_sure = function($index,list,$scope){
        if($scope.spellLinkPosition == 2){//魔方
            //魔方添加商品
            console.log(list)
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 4;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.title;
        }
        hideModel($('#spell_Modal'));//隐藏Model
    }
    /**
     * 魔方功能
     * @author  huoguanghui
     * @created 2017年12月28日16:00:18
     */
    /**
     * 拼团外链
     * @author  huoguanghui
     */
    commonServer.choose_spells = function($index,position,$scope){
        $scope.spellLinkPosition = position;
        commonServer.getSpellList($scope);
    }
    /**
     * 签到外链
     */
    commonServer.chooseSign = function($scope){
        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 6;
        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = 0;
        $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = "签到";
    }
     /** 
     * 添加魔方组件
     */
    commonServer.addCube = function($scope,position){
        $scope.cube.selectLayoutIndex = 0;//初始化魔方布局下标
        // console.log(position)
        $scope.removeClassEditing();
        if(position == 1){//添加
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':25, 
                    'type':'cube',
                    'editing':'editing',
                    'is_add_content':false,
                    "isPromptAddPic":false,//是否提示添加图片  默认false
                    "isAddPic":false,//是否添加图片
                    "telType":0,    //模板类型 默认为0  0~7  1行两个~1行5个
                    "margin": 0,    // 图片间隙
                    "addTitle": true, //是否添加蒙版标题
                    "aspectRatio":0,     //图片高宽比
                    "resize_image":1, //1放大0不放大
                    "position":angular.copy($scope.cube.data.position),   //布局
                    "content":angular.copy($scope.cube.data.content) //数据        
                }
            );
        }else{//加内容
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':25, 
                'type':'cube',
                'editing':'editing',
                'is_add_content':false,
                "isAddPic":false,
                "isPromptAddPic":false,
                "telType":0, 
                "margin": 0,
                "addTitle": true,
                "aspectRatio":0,
                "resize_image":1, //1放大0不放大  
                "position":angular.copy($scope.cube.data.position),
                "content":angular.copy($scope.cube.data.content)
            })
        }
        $scope.initCartRight();
    }
    /**
     * 选择魔方模板
     * @param index  当前元素下标
     * 设置模板下标 布局初始化为选中第一个
     * 修改魔方数据
     */
    commonServer.selectedTel = function($scope,index){
        $scope.cube.selectTelIndex = index;
        $scope.cube.selectLayoutIndex = 0;
        $scope.editors[$scope.index].telType = index;//模板类型赋值
        $scope.editors[$scope.index].position = $scope.cube.template[index].position;//布局赋值
        $scope.editors[$scope.index].addTitle = true;
        if($scope.editors[$scope.index].content.length > $scope.cube.template[index].position.length){//魔方数据超出删除
            $scope.editors[$scope.index].content = $scope.editors[$scope.index].content.slice(0,$scope.cube.template[index].position.length);
        }else if($scope.editors[$scope.index].content.length < $scope.cube.template[index].position.length){//魔方数据不足补充
            var list = $scope.cube.template[index].position;
            angular.forEach(list,function(data,index,array){
                var item ={   //魔方数据  初始数据     放在循环内部  在外部存在深浅copy问题
                    type: 0,        //链接类型 1 商品 2 微页面   默认0
                    id: 0,          //当前活动链接id
                    linkTitle:"",   //链接名称
                    img:"",         //选中图片
                    title: ""       //蒙版标题
                }
                if(!$scope.editors[$scope.index].content[index]){//数据不存在 添加数据
                    $scope.editors[$scope.index].content.push(item);
                }
            })
        }
    }
    /**
     * 选择魔方布局
     * @param index  当前元素下标
     */
    commonServer.selectedLayout = function($scope,index){
        $scope.cube.selectLayoutIndex = index;
    }
     /**
     * 魔方滑动组件点击设置间距
     * add by 黄新琴 2018/8/31
     */
    commonServer.changeMargin = function(e,$scope){
        var offX = e.offsetX,
            width = $(e.currentTarget).width();
        var margin = Math.round(offX/width*30);
        $scope.editors[$scope.index].margin = margin;
    }
    /**
     * 魔方删除链接
     */
    commonServer.deleteLink = function($scope){
        $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].type = 0;
        $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].id = 0;
        $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].linkTitle = "";
    }
    /*魔方功能end*/
    /*
    *todo 自定义组件营销活动模块
    *@author huoguanghui
    *@func 1.添加营销活动
    *@func 2.添加秒杀弹框
    *@func 3.秒杀活动搜索
    *@func 5.秒杀列表刷新
    */
    //添加营销活动 
    // commonServer.addActive = function($scope,position){
    //     $scope.removeClassEditing();
    //     if(position == 1){
    //         $scope.editors.push({
    //             'showRight': true,
    //             'cardRight': 19,//营销活动右侧显示
    //             'type': 'marketing_active',
    //             'addActiveShow': true,//true 显示添加活动  false 显示活动
    //             'editing': 'editing',
    //             'content': [],
    //             'is_add_content':false
    //         });
    //     }else if(position == 2){
    //         $scope.editors.splice($scope.index+1,0,{
    //             'showRight': true,
    //             'cardRight': 19,//营销活动右侧显示
    //             'type': 'marketing_active',
    //             'addActiveShow': true,//true 显示添加活动  false 显示活动
    //             'editing': 'editing',
    //             'content': [],
    //             'is_add_content':false
    //         })
    //     }
    //     $scope.initCartRight();
    // }
    //添加秒杀弹框
    //秒杀列表添加  use：秒杀弹框 秒杀弹框搜索 
    function killListAdd($scope,wid,title){
        $.get('/merchants/linkTo/get?type=13&platform=1&wid='+ wid +'&page=1&title='+title, function(data) {
            $scope.$apply(function(){
                $scope.killList = [];//每次调用重置内容
            })
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.killList.push({
                        "id":val.id,
                        "invalidate_at":val.invalidate_at,
                        "activeName":val.title,
                        "activeType":"秒杀", //活动类型
                        "limit_num":val.limit_num,// >0开启限购
                        "start_at":val.start_at,
                        "end_at":val.end_at,
                        "now_at":val.now_at,
                        "product":val.product,//后台填充数据
                        "sku":val.sku,//后台填充数据
                        "timeDay":val.created_at,
                        "seckill_stock":val.seckill_stock,//剩余
                        "seckill_price":val.seckill_price,
                        "seckill_oprice":val.seckill_oprice,
                        'seckill_sold_num':val.seckill_sold_num
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.kill_page_component').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=13&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.killList = [];
                                console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.killList.push({
                                            "id":val.id,
                                            "invalidate_at":val.invalidate_at,
                                            "activeName":val.title,
                                            "activeType":"秒杀", //活动类型
                                            "limit_num":val.limit_num,// >0开启限购
                                            "start_at":val.start_at,
                                            "end_at":val.end_at,
                                            "product":val.product,
                                            "sku":val.sku,
                                            "timeDay":val.created_at,
                                            "seckill_stock":val.seckill_stock,//剩余
                                            "seckill_price":val.seckill_price,
                                            "seckill_oprice":val.seckill_oprice,
                                            'seckill_sold_num':val.seckill_sold_num
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            
        },'json')
    }
    
    commonServer.killModelShow = function($scope){
        var wid = $('#wid').val();
        killListAdd($scope,wid,"");
        showModel($('#kill_model'),$('#kill_model_dialog'));
    }
    //秒杀搜索
    commonServer.searchKill = function($scope){
        var wid = $('#wid').val();
        killListAdd($scope,wid,$scope.searchKillTitle);
    }
    //秒杀活动选择
    commonServer.chooseKill = function(index,list,$scope){
        if($scope.skillLinkPosition == 1){//营销
            $scope.editors[$scope.index].content = [];
            var seckill_stock = 0;//秒杀库存
            for(var j = 0;j <list['sku'].length;j ++){
                seckill_stock += parseInt(list['sku'][j]['seckill_stock']);
            }
            if(seckill_stock > 0){
                list['productStatus'] = false;//false 为正常销售
            }else{
                list['productStatus'] = true;
            }
            $scope.editors[$scope.index].content.push(list);
            $scope.editors[$scope.index].killList.push(list);
            $scope.editors[$scope.index].seckillIds.push(list.id);
        }else if($scope.skillLinkPosition == 2){//秒杀
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 5;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.activeName;
        }
        hideModel($("#kill_model"));
    }
    //秒杀列表刷新
    commonServer.killRefresh = function($scope){
        var wid = $('#wid').val();
        killListAdd($scope,wid,"");
    }
    /** 
     * 视频模块组件
     * @author  huoguanghui
     * file_mine  1 图片  2 视频  3 音频
     */
    //获取视频列表
    function getVideoList($scope,classifyId){
        var _token = $('meta[name="csrf-token"]').attr('content');
        var classifyId = arguments[1]?arguments[1]:0;
        $.post('/merchants/myfile/getUserFileByClassify',{file_mine:2,page:1,classifyId:classifyId,_token:_token}, function(data) {
            $scope.$apply(function(){
                $scope.video.modelVideoList = [];//每次调用重置内容
            })
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.file_cover == ''){
                        val.file_cover = 'hsshop/image/static/video_bg.jpg';
                    }
                    $scope.video.modelVideoList.push(val)
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.video_model_page').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.post('/merchants/myfile/getUserFileByClassify',{file_mine:2,page:page,classifyId:classifyId,_token:_token},function(response){
                            if(response.status ==1){
                                $scope.video.modelVideoList = [];
                                console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.video.modelVideoList.push(val)
                                    })
                                })
                            }
                        })
                    }
                });
            
        },'json')
    }
    //初始化视频模态框
    function initVideoModel($scope){
        $scope.video = {
            checkedIndex:-1,//视频选中下标
            checkedItem:null,//视频选中对象
            groupingIndex:0,//分组下标
            groupList:[],//视频弹框分组
            groupingId:0,//分组id
            modeosearchTitle: "",//视频模态框搜索
            modelVideoList:[],//模态框视频列表
        }
    }
    //获取视频分组
    function getVideoGroup($scope){
        $.get('/merchants/myfile/getClassify?file_mine=2', function(data) {
            console.log(data)
            if(data.status == 1){
                $scope.$apply(function(){
                    $scope.video.groupList = [];//每次调用重置内容
                })
                angular.forEach(data.data,function(val,key){
                    $scope.$apply(function(){
                        $scope.video.groupList.push({
                            "id":val.id,
                            "name":val.name,
                            "number":val.number
                        })
                    })
                })

            }
        },'json')
    }
    //新增视频模块
    commonServer.addVideo = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':24, 
                    'type':'video',
                    'editing':'editing',
                    'id':0,//视频id
                    'videoItem':{},//视频信息
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':24, 
                'type':'video',
                'editing':'editing',
                'id':0,//视频id
                'videoItem':{},//视频信息
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //视频搜索功能
    // commonServer.videoSearch = function(e){
    //     if(e.keyCode==13){//回车
    //         // to do something
    //     }
    // }
    //打开视频弹框
    commonServer.openVideoModel = function($scope){
        getVideoList($scope);
        getVideoGroup($scope);
        showModel($('#video_model'),$('#video_model_dialog'));
    }
    //隐藏视频弹框
    commonServer.hideVideoModel = function($scope){
        initVideoModel($scope);
        hideModel($('#video_model'));
    }
    //切换视频分组
    commonServer.switchVideoGroup = function($scope,item,index){
        $scope.video.groupingIndex = index;
        $scope.video.groupingId = item.id;
        getVideoList($scope,item.id);
    }
    //选择视频
    commonServer.checkedVideoItem = function($scope,item,index){
        $scope.video.checkedItem = item;
        $scope.video.checkedIndex = index;
    }
    //确认使用视频
    commonServer.sureUseVideo = function($scope){
        $scope.editors[$scope.index].id = $scope.video.checkedItem.id;
        $scope.editors[$scope.index].videoItem =  $scope.video.checkedItem;
        hideModel($('#video_model'));
        initVideoModel($scope);
    }

    //删除活动
    commonServer.deleteActive = function($scope){
        $scope.editors[$scope.index].content = [];//清空数据
    }
    // 添加自定义Model确定
    commonServer.chooseComponent = function($scope,$index,list){
        $scope.editors[$scope.index]['modelName'] = list.name;
        $scope.editors[$scope.index]['id'] = list.id;
        hideModel($('#component_model'));
    }
    // 文本导航添加
    commonServer.addtextLink = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':16, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'textlink',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'textlink':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':16, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'textlink',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'textlink':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    // 添加一个文本链接
    commonServer.addOneTextLink = function($scope){
        $scope.editors[$scope.index]['textlink'].push({'titleName':'','dropDown':false,'linkName':'','linkUrl':'',})
    }
    
    // 删除一个文本链接
    commonServer.deleteOneTextLink = function($index,$scope){
        $scope.editors[$scope.index]['textlink'].splice($index,1);
    }
    //微页面加内容
    commonServer.addContent = function(event,$index,editor,$scope,top){
        $scope.index = $index;
        $scope.editors[$index]['is_add_content'] = true;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        // console.log(event.currentTarget.offsetParent.offsetParent);
        event.currentTarget.offsetParent.offsetParent.className += ' editing';
        event.currentTarget.offsetParent.offsetParent.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetParent.offsetParent.offsetTop-top);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    $scope.editors[$scope.index].showRight = true;
                    editor.is_add_content = true
                    console.log(editor);
                    if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='member'){
                        $scope.editors[$scope.index]['cardRight'] = 1;
                        $timeout(function(){
                            // ue.setContent($('.editing').children('editor-text').html());
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='category'){
                        $scope.editors[$scope.index]['cardRight'] = 2;
                        $timeout(function(){
                            var desc = document.getElementsByClassName('page_desc')[0];
                            ue_category.setContent(desc.innerHTML);
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='rich_text'){
                        $scope.editors[$scope.index]['cardRight'] = 3;
                        $timeout(function(){
                            console.log($('.editing').children('editor-text').find('.custom-richtext').html());
                            ue.setContent($('.editing').children('editor-text').find('.custom-richtext').html());
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='goods'){
                        $scope.editors[$scope.index]['cardRight'] = 4;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='image_ad'){
                        $scope.editors[$scope.index]['cardRight'] = 5;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='title'){
                        $scope.editors[$scope.index]['cardRight'] = 6;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='store'){
                        $scope.editors[$scope.index]['cardRight'] = 7;
                    }  
                }
            }) 
        },200)
        event.stopPropagation();
    }
    //添加会员卡
    commonServer.addCard = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':21, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                    'type':'card',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'cardList':[],
                    'card_ids':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':21, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧 
                'type':'card',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'cardList':[],
                'card_ids':[],
                'is_add_content':false
            })
        }
        console.log($scope.editors);
        $scope.initCartRight();
    }
    //显示会员卡
    commonServer.showCardModel = function($scope){
        $scope.searchTitle = '';
        $scope.cardList = [];
        $.get('/merchants/microPage/memberCard?page=1', function(data) {
            angular.forEach(data.data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.cardList.push({
                        "id":val.id,
                        "name":val.title,
                        "power_desc":val.power_desc,
                        "img": _host + 'mctsource/images/card.png',
                        'card_img': ''
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
                // alert(totalCount)
                $('.card_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/microPage/memberCard?page='+page,function(response){
                            if(response.status ==1){
                                $scope.cardList = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.cardList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "power_desc":val.power_desc,
                                            "img": _host + 'mctsource/images/card.png',
                                            'card_img': ''
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_card_model'),$('#card_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    // 选取会员卡
    commonServer.chooseCard = function($index,list,$scope){
        var wid = $('#wid').val();
        if($('.choose_btn_'+list.id).hasClass('btn-primary')){
            $('.choose_btn_'+list.id).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('选取'); //改变按钮显示状态
                angular.forEach($scope.temp,function(val,key){
                    if(val.id == list.id){
                        $scope.temp.splice(key,1);//清除数据
                        console.log($scope.temp);
                    }
                })
        }else{
            $('.choose_btn_'+list.id).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('取消'); //改变按钮显示状态
            $scope.temp.push({'name':list.name, 'id':list.id, 'power_desc':list.power_desc, 'img':list.img, 'card_img': ''}); // 添加数据
        }
        console.log($scope.temp);
    }
    // 选择会员卡确定按钮
    commonServer.chooseCardSure = function($scope){
        hideModel($('#my_card_model'));//隐藏Model
        if($scope.temp.length>0){
            for(var i=0;i<$scope.temp.length;i++){
                if(i<3){
                    if($scope.editors[$scope.index]['cardList'].length <= 2){
                        $scope.editors[$scope.index]['cardList'].push($scope.temp[i]);//合并数组
                        $scope.editors[$scope.index]['card_ids'].push($scope.temp[i]['id']);
                    }
                }
            }
        }
        console.log($scope.editors[$scope.index]['cardList']);
        $scope.temp = [];//去除数据
    }
    // 删除会员卡
    commonServer.deleteCard = function($scope,$index){
        angular.forEach($scope.editors[$scope.index]['card_ids'],function(val,key){
            if(val == $scope.editors[$scope.index]['cardList'][$index]['id']){
                $scope.editors[$scope.index]['card_ids'].splice(key,1);
            }
        })
        $scope.editors[$scope.index]['cardList'].splice($index,1);
        // $scope.editors[$scope.index]['coupons_id'].splice($index,1);
    }
    /**
     * @author: 戴江淮（npr5778@dingtalk.com）
     * @description: 添加会员卡封面图片
     * @param {object} 页面数据 [$scope]
     * @param {number} 索引 [$index]
     * @return: 
     * @Date: 2019-10-25 10:18:11
     */
    commonServer.addCouponImg = function($scope, $index) {
        $scope.changeImange = false;
        $scope.uploadShow = false;
        $scope.grounps = [];
        $scope.advImageIndex = $index;
        $scope.isCardImg = true
        // $scope.index = $index
        $http.get('/merchants/myfile/getClassify').success(function (data) {
            angular.forEach(data.data, function (val, key) {
                if (key == 0) {
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify', {classifyId:classifyId}).success(function (response) {
                angular.forEach(response.data[0].data, function (val, key) {
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                console.log($scope.uploadImages);
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify', {classifyId:classifyId,page:page}).success(function (response) {
                            angular.forEach(response.data[0].data,function (val, key) {
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                            // console.log($scope.uploadImages);
                        })
                    }
                });
            })
            $scope.initchooseAdvImage();
            showModel($('#myModal-adv'), $('#modal-dialog-adv'));
        })
    }
    // 会员卡弹窗搜索
    commonServer.searchCard = function($scope){
        $scope.cardList = [];
        $.get('/merchants/microPage/memberCard?page=1&keyword='+ $scope.searchTitle, function(data) {
            angular.forEach(data.data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.cardList.push({
                        "id":val.id,
                        "name":val.title,
                        "power_desc":val.power_desc,
                        "img": _host + 'mctsource/images/card.png',
                        'card_img': ''
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data.total, showCount = 10,
                limit = data.data.per_page;
                // alert(totalCount)
                $('.card_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/microPage/memberCard?page='+ page +'&keyword='+ $scope.searchTitle,function(response){
                            if(response.status ==1){
                                $scope.cardList = [];
                                angular.forEach(response.data.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.cardList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "power_desc":val.power_desc,
                                            "img": _host + 'mctsource/images/card.png',
                                            'card_img': ''
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#my_card_model'),$('#card_model-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    /* add by 韩瑜
     * date 2018-9-17
     * 图片分组模板添加分组
     */
    commonServer.addBigGroup = function($scope){
    	$scope.editors[$scope.index]['classifyList'].push(
    		{
	    		"category_name":"分类一",
				"thumbnail":"mctsource/images/grouppagebanner.png",
				"subClassifyList":
				[
					{
					"category_name":"子分类",
					"thumbnail":"mctsource/images/grouppageitem.png",
					"linkUrl":"",
					"linkName":""
					},
				],
	    	}
    	)
    }
    commonServer.addSmallGroup = function($scope,$index){
        $scope.editors[$scope.index]['classifyList'][$index]['subClassifyList'].push(
			{
				"category_name":"子分类",
				"thumbnail":"mctsource/images/grouppageitem.png",
				"linkUrl":"",
				"linkName":""
			}
    	)
    }
    //图片自定义外链
    commonServer.changeWaiLink = function($index,position,$event,$scope){
        // console.log(.parentNode);
        $scope.advImageIndex = $index;
        if($($event.currentTarget).parents('.dropdown').offset()){
            var left = $($event.currentTarget).parents('.control-group').offset().left-280;
            var height = $($event.currentTarget).parents('.dropdown').offset().top - 40;
        }else{
            var left = $($event.currentTarget).parents('.cube-add-href').offset().left-280;
            var height = $($event.currentTarget).parents('.cube-set-href').offset().top - 40;
        }
        $('#wailink_input').val('');
        $('#setWaiLink').css('display','block');
        $('#setWaiLink').css('left',left);
        $('#setWaiLink').css('top',height);              
    };
    commonServer.cancelSetLink = function(){
        $('#setWaiLink').hide();
        $('#outLink').hide();
    }
    commonServer.sureSetLink = function($scope){
        var url = $('#wailink_input').val().substr(0,4).toLowerCase() == "http" ? $('#wailink_input').val() : "http://" + $('#wailink_input').val();
        if($scope.editors[$scope.index]['images']==undefined){
            if($scope.editors[$scope.index]['content']==undefined) {
                // 标题添加外链确定
                $scope.editors[$scope.index]['linkName'] = url;
                $scope.editors[$scope.index]['linkUrl'] = url;
                $scope.editors[$scope.index]['chooseLink'] = true;
                $scope.editors[$scope.index]['dropDown'] = false;
            } else {
                // 魔方添加外链确定
                $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 8;
                $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = 1;
                $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = url;
            }
            
        }else{
            // 图片广告，图片导航添加外链确定
            $scope.editors[$scope.index]['images'][$scope.advImageIndex]['linkName'] = url;
            $scope.editors[$scope.index]['images'][$scope.advImageIndex]['linkUrl'] = url;
            if($scope.editors[$scope.index]['images'][$scope.advImageIndex]['linkUrl'] !=''){
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['chooseLink'] = true;
            }else{
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['chooseLink'] = false;
            }
        }
        $('#setWaiLink').hide();
    }
    //图片导航会员卡确定
    commonServer.chooseMenModelSure = function($index,list,$scope){
        console.log($scope.shopLinkPosition);
        if($scope.shopLinkPosition == 1){
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['dropDown'] = false;
            // $scope.editors[$scope.index]['images'][$scope.advsImagesIndex]['link_type'] = $scope.link_type;
        }else if($scope.shopLinkPosition == 2){
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['chooseLink'] = true;
            $scope.editors[$scope.index]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 3){
            //一级导航
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 4){
            //二级导航
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrlName'] = list.name;
            $scope.menus['menu'][$scope.outerIndex]['submenus'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 5){
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['linkUrl'] = list.url;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['link_id'] = list.id;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['chooseLink'] = true;
            $scope.editors[$scope.index]['textlink'][$scope.advsImagesIndex]['dropDown'] = false;
        }else if($scope.shopLinkPosition == 6){
            // 冰冰模板背景链接
            $scope.editors[$scope.index]['linkName'] = list.name;
            $scope.editors[$scope.index]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 7){
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkName'] = list.name;
            $scope.editors[$scope.index]['lists'][$scope.advsImagesIndex]['linkUrl'] = list.url;
        }else if($scope.shopLinkPosition == 8){
            //魔方添加微页面
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 2;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = list.id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = list.name;
        }
        hideModel($('#page_model_card'));
    }


    //添加留言板
    commonServer.addResearch = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':30, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'research',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'resList':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':30, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'research',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'resList':[],
                'is_add_content':false
            })
        }
        console.log($scope.editors);
        $scope.initCartRight();
    }
    //显示留言板列表Model
    function researchAdd($scope,wid,title,type){
        $.get('/merchants/linkTo/get?type=19&platform=2&wid='+ wid +'&page=1&title='+title+'&activity_type='+type, function(data) {
            $scope.$apply(function(){
                $scope.pageList = [];//每次调用重置内容
            })
            // 根据type不同 跳转至不同页面 0:在线报名,1:预约,2:投票
            if(typeof type == 'number'){
                $('.new_window').attr({href:'/merchants/marketing/researches/'+type})
            }
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "invalidate_at":val.invalidate_at,
                        "name":val.title,
                        "created_at":val.created_at,
                        "times_type":val.times_type, //活动类型
                        "start_at":val.start_at,
                        "end_at":val.end_at,
                        "updated_at":val.now_at
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
                    $.get('/merchants/linkTo/get?type=19&wid='+ wid +'&page='+page+'&title='+title+'&activity_type='+type,function(response){
                        if(response.status ==1){
                            $scope.pageList = [];
                            console.log(response);
                            angular.forEach(response.data[0].data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageList.push({
                                        "id":val.id,
                                        "invalidate_at":val.invalidate_at,
                                        "name":val.title,
                                        "created_at":val.created_at,
                                        "times_type":val.times_type, //活动类型
                                        "start_at":val.start_at,
                                        "end_at":val.end_at,
                                        "updated_at":val.now_at
                                    })
                                })
                            })
                        }
                    })
                }
            });

        },'json')
    }
    commonServer.showResearchModel = function ($scope,position,type) {
        var wid = $('#wid').val();
        researchAdd($scope,wid,'',type);
        showModel($('#research_model'),$('#research-dialog'));
    }
    //选取留言板活动
    commonServer.chooseResearch = function(index,list,$scope){
        $scope.editors[$scope.index].resList = [];
        $scope.editors[$scope.index].resList.push(list)
        hideModel($("#research_model"));
    }
    //删除留言活动
    commonServer.deleteResearch = function ($scope) {
        $scope.editors[$scope.index].resList = [];
    }
    //秒杀搜索
    commonServer.searchRes = function($scope){
        var wid = $('#wid').val();
        researchAdd($scope,wid,$scope.researchTitle);
    }
    //添加投票活动
    commonServer.addResearchVote = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':31, 
                    'type':'researchVote',
                    'editing':'editing',
                    'resList':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':31, 
                'type':'researchVote',
                'editing':'editing',
                'resList':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加预约活动
    commonServer.addResearchAppoint = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':33, 
                    'type':'researchAppoint',
                    'editing':'editing',
                    'resList':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':33, 
                'type':'researchAppoint',
                'editing':'editing',
                'resList':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加报名活动
    commonServer.addResearchSign = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':32, 
                    'type':'researchSign',
                    'editing':'editing',
                    'resList':[],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':32, 
                'type':'researchSign',
                'editing':'editing',
                'resList':[],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加秒杀活动
    commonServer.addSecondKill = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'listStyle':1, //列表样式：1大图显示，2小图显示，3详细列表
                    'showRight':true,
                    'showTitle':true,//是否显示商品名称
                    'cardRight':36, 
                    'type':'seckill_list',
                    'editing':'editing',
                    'showTimer':true,//是否隐藏倒计时
                    'hideOut':false,//隐藏已售罄
                    'hideEnd':false,//隐藏已结束
                    'remanent':true,//剩余是否显示
                    'remanentStyle':1,//剩余显示 默认文本
                    'showBtn':true,
                    'btnStyle':1,//按钮样式
                    'killList':[],
                    'headImage':_host+'static/images/pc_sec_kill_nav.png',
                    'thSecGoods':[
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'tip':'10',
                            'secPrice':'29.66',
                            'oPrice':'39.66',
                            'remanentMount':'100'
                        },
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'tip':'10',
                            'secPrice':'29.66',
                            'oPrice':'39.66',
                            'remanentMount':'100'
                        },
                        {
                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                            'name':'这里显示商品名称',
                            'tip':'10',
                            'secPrice':'29.66',
                            'oPrice':'39.66',
                            'remanentMount':'100'
                        }
                    ],
                    'seckillIds':[]
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'listStyle':1, //列表样式：1大图显示，2小图显示，3详细列表
                'showRight':true,
                'showTitle':true,//是否显示商品名称
                'cardRight':36, 
                'type':'seckill_list',
                'editing':'editing',
                'showTimer':true,//是否隐藏倒计时
                'hideOut':false,//隐藏已售罄
                'hideEnd':false,//隐藏已结束
                'remanent':true,//剩余是否显示
                'remanentStyle':1,//剩余显示 默认文本
                'showBtn':true,
                'btnStyle':1,//按钮样式
                'killList':[],
                'headImage':_host+'static/images/pc_sec_kill_nav.png',
                'thSecGoods':[
                    {
                        'thumbnail':_host + 'static/images/product_img_1.jpg',
                        'name':'这里显示商品名称',
                        'tip':'10',
                        'secPrice':'29.66',
                        'oPrice':'39.66',
                        'remanentMount':'100'
                    },
                    {
                        'thumbnail':_host + 'static/images/product_img_1.jpg',
                        'name':'这里显示商品名称',
                        'tip':'10',
                        'secPrice':'29.66',
                        'oPrice':'39.66',
                        'remanentMount':'100'
                    },
                    {
                        'thumbnail':_host + 'static/images/product_img_1.jpg',
                        'name':'这里显示商品名称',
                        'tip':'10',
                        'secPrice':'29.66',
                        'oPrice':'39.66',
                        'remanentMount':'100'
                    }
                ],
                'seckillIds':[]
            })
        }
        $scope.initCartRight();
    }
    commonServer.chooseKillItem = function($index,$scope,list){
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
            $scope.temp.push({
                'id':list.id,
                'product':list.product,
                'end_at':list.end_at,
                'start_at':list.start_at,
                "seckill_stock":list.seckill_stock,//剩余
                "seckill_price":list.seckill_price,
                "seckill_oprice":list.seckill_oprice,
                'seckill_sold_num':list.seckill_sold_num,
                'activeName':list.activeName
            });//添加数据
        }
    };
    commonServer.sureKill =function($scope){
        if($scope.skillLinkPosition == 1){//营销
            $scope.editors[$scope.index].killList=$scope.editors[$scope.index].killList.concat($scope.temp)
            var arr = [];
            for(var i in $scope.temp){
                arr.push($scope.temp[i].id)
            }
            $scope.editors[$scope.index].seckillIds=$scope.editors[$scope.index].seckillIds.concat(arr);
            $scope.temp = [];
        }else if($scope.skillLinkPosition == 2){//秒杀
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['type'] = 5;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['id'] = $scope.temp[$scope.temp.length-1].id;
            $scope.editors[$scope.index]['content'][$scope.cube.selectLayoutIndex]['linkTitle'] = $scope.temp[$scope.temp.length-1].activeName;
        }
        /* add by 韩瑜
         * date 2018-9-18
         * 商品分组模板链接秒杀确定
         */
        else if($scope.shopLinkPosition == 9){
    		var wid = $('#wid').val();
    		$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = "/shop/seckill/detail/" + wid + "/" + $scope.temp[$scope.temp.length-1].id;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = $scope.temp[$scope.temp.length-1].activeName;
        	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
    	}
        hideModel($("#kill_model"));
    };
    //add by 韩瑜 2018-9-13
    //清空搜索框内文字
    commonServer.clearsearch =function($scope){
    	$scope.editors[$scope.index]['searchName'] = '';
    };
    //清空搜索框背景颜色
    commonServer.clearcolor =function($scope){
    	$scope.editors[$scope.index]['bgColor'] = '#ffffff';
    };
    //2018-9-17
    //商品分组模板显示营销活动弹窗
    commonServer.GroupChooseActivity = function($scope,p_index,$index,position){
    	$scope.Pindex = p_index;
    	$scope.GroupLinkType = 7;
        $scope.switchIndex = $index;
        $scope.shopLinkPosition = position;
        $scope.activity_list = [];
        $scope.searchTitle = '';
        $scope.advsImagesIndex = $index;
        var _token = $('meta[name="csrf-token"]').attr('content');
        //判断是哪一个活动
        switch ($scope.activityIndex)
        {
            case 0://营运大转盘
                var url = "/merchants/marketing/wheelList?pagesize=6";
            break;
            case 1://砸金蛋
                var url = "/merchants/marketing/egg/index?size=6";
            break;
        }
        console.log(url);
        $.post(url,{_token:_token},function(data){
        	console.log(data)
            if(data.data[0]['data'].length){
                angular.forEach(data.data[0]['data'],function(val,key){
                    $scope.$apply(function(){
                        $scope.activity_list.push(val);
                    })
                })
            }
            var totalCount = data.data[0].total, showCount = 10,
            limit = data.data[0].per_page;
            $('.activity_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $scope.activity_list = [];
                    $.post(url,{_token:_token,page:page},function(response){
                        if(response.status ==1){
                            $scope.goodList = [];
                            angular.forEach(response.data[0].data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.activity_list.push(val)
                                })
                            })
                        }
                    })
                   
                }
            });
            showModel($('#activity_model'),$('#activity-dialog'));
        })
    }
    //商品分组模板显示微页面弹窗
    commonServer.GroupChoosePageLink = function($scope,p_index,$index,position){
        $scope.temp = [];
        $scope.Pindex = p_index;
        $scope.GroupLinkType = 1;
        $scope.pageList = [];
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.link_type = 1;
        var wid = $('#wid').val();
        $.get('/merchants/store/selectPage?page=1', function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.page_title,
                        "url":val.url,
                        "created_at":val.created_at
                    })
                })
            })
            
            // console.log($scope.goodList);
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
                                            "created_at":val.created_at
                                        })
                                    })
                                })
                                console.log($scope.pageList);
                            }
                        })
                    }
                });
            showModel($('#page_model'),$('#page-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    //商品分组模板显示商品弹窗
    commonServer.GroupChooseShop = function($scope,p_index,$index,position){
        $scope.shopLinkPosition = position;
        $scope.GroupLinkType = 2;
        $scope.Pindex = p_index;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        var wid = $('#wid').val();
        var keyword = keyword?keyword:'';
        $scope.productModal.list = [];//初始化数据
        switch ($scope.productModal.navIndex)
        {
            case 0://商品
                var _url = '/merchants/linkTo/get?type=1';
                var shopUrl = '/shop/product/detail/';
            break;
            case 1://分组
                var _url = '/merchants/linkTo/get?type=2';
                var shopUrl = '/shop/group/detail/';
            break;
        }
        $.get(_url+'&wid='+wid +'&page=1&title='+keyword, function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                console.log(val);
                $scope.$apply(function(){
                    $scope.productModal.list.push({
                        "id":val.id,
                        "name":val.title,
                        "thumbnail": val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_at,
                        "timestamp":"" ,
                        "url":shopUrl+val.wid+'/'+val.id 
                    })
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get(_url+'&wid='+ wid +'&page='+page+'&title='+keyword,function(response){
                            if(response.status ==1){
                                $scope.productModal.list = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.productModal.list.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_at,
                                            "timestamp":"",
                                            "url":shopUrl+val.wid+'/'+val.id   
                                        })
                                    })
                                })
                            }
                        });
                        
                    }
                });
            showModel($('#chooseShopModel'),$('#chooseShopModel-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    //商品分组模板显示优惠券弹窗
    commonServer.GroupCouponModel =  function($scope,p_index,$index,position){
        $scope.shopLinkPosition = position;
        $scope.Pindex = p_index;
        $scope.GroupLinkType = 3;
        $scope.advsImagesIndex = $index;
        $scope.searchTitle = '';
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    if(val.is_limited == 0){
                        val.limit_desc = '无限制';
                    }else{
                        val.limit_desc = '满'+val.limit_amount+'元可用';
                    }
                    $scope.couponList.push({
                        "id":val.id,
                        "name":val.title,
                        "info":val.description,
                        "amount":val.amount,
                        "amount_random_max":val.amount_random_max,
                        "is_limited":val.is_limited,
                        "limit_amount":val.limit_amount,
                        "is_random":val.is_random,
                        "limit_desc":val.limit_desc
                    })
                })
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.coupon_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.couponList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        if(val.is_limited == 0){
                                            val.limit_desc = '无限制';
                                        }else{
                                            val.limit_desc = '满'+val.limit_amount+'元可用';
                                        }
                                        $scope.couponList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "info":val.description,
                                            "amount":val.amount,
                                            "amount_random_max":val.amount_random_max,
                                            "is_limited":val.is_limited,
                                            "limit_amount":val.limit_amount,
                                            "is_random":val.is_random,
                                            "limit_desc":val.limit_desc
                                        })
                                    })
                                })
                            }
                        })
                    }
                });
            showModel($('#cube_coupon_model'),$('#cube_coupon_model-dialog'));
            $('.jsshopLinkPosition-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
    }
    //商品分组模板显示秒杀
    commonServer.GroupKillModel = function($scope,p_index,$index,position){
    	$scope.shopLinkPosition = position;
    	$scope.Pindex = p_index;
    	$scope.GroupLinkType = 4;
    	$scope.advsImagesIndex = $index;
        var wid = $('#wid').val();
        killListAdd($scope,wid,"");
        showModel($('#kill_model'),$('#kill_model_dialog'));
    }
    //商品分组模板显示拼团
    commonServer.GroupSpellModel = function($scope,p_index,$index,position){
        $scope.temp = [];
        $scope.Pindex = p_index;
        $scope.GroupLinkType = 5;
        $scope.pageList = [];
        $scope.searchTitle = '';
        $scope.shopLinkPosition = position;
        $scope.advsImagesIndex = $index;
        var wid = $('#wid').val();
        $.get('/merchants/grouppurchase/groupList?pageSize=6', function(data) {
        	console.log(data.data[0].data)
            angular.forEach(data.data[0].data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.id,
                        "name":val.title,
                        "url":'/shop/grouppurchase/detail/'+val.id+'/'+wid,
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
                                        "url":'/shop/grouppurchase/detail/'+val.id+'/'+wid,
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
    // 商品分组模板选择链接
    commonServer.GroupLinkUrl = function($event,p_index,$index,url,type,$scope){
    	console.log(p_index,type)
    	$scope.Pindex = p_index;
    	$scope.advsImagesIndex = $index;
    	console.log(url)
    	if(type == 8){//店铺主页
    		$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = url;
	    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = $event.target.text;
	    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = type;
    	}else{
			$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = url;
	    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = $event.target.text;
	    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = type;
    	}
    }
    //商品分组模板显示享立减
    commonServer.GroupShareEventModel = function($scope,p_index,$index){
        $scope.temp = [];
        $scope.Pindex = p_index;
        $scope.GroupLinkType = 6;
        $scope.pageList = [];
        $scope.searchTitle = '';
        $scope.advsImagesIndex = $index;
        var wid = $('#wid').val();
        $.get('/merchants/shareEvent/getList?pageSize=6', function(data) {
        	console.log(data.data.data)
            angular.forEach(data.data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageList.push({
                        "id":val.product_id,
                        "activityId":val.id,
                        "name":val.title,
                        "url":'/shop/product/detail/' + wid + '/' + val.product_id + '?activityId=' + val.id,
                        "created_at":val.created_at
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
                    $.get('/merchants/shareEvent/getList?page='+page+'&pageSize=6',function(response){
                        if(response.status == 1){
                            $scope.pageList = [];
                            angular.forEach(response.data.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageList.push({
				                        "id":val.product_id,
				                        "activityId":val.id,
				                        "name":val.title,
				                        "url":'/shop/product/detail/' + wid + '/' + val.product_id + '?activityId=' + val.id,
				                        "created_at":val.created_at
				                    })
                                })
                            })
                        }
                    })
                }
            });
            showModel($('#page_model_shareEvent'),$('#page-dialog_shareEvent'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');//显示Model
        },'json')
    }
    //商品分组模板选择享立减确定
    commonServer.GroupShareEventSure = function($scope,$index,list){
    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkUrl'] = list.url;
    	console.log(list.url,12312312312)
    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['linkName'] = list.name;
    	$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advsImagesIndex]['GroupLinkType'] = $scope.GroupLinkType;
    	hideModel($('#page_model_shareEvent'),$('#page-dialog_shareEvent'));
    }
    //商品分组模板选择自定义外链
    commonServer.GroupOutLink = function(p_index,$index,$event,$scope){
        $scope.Pindex = p_index;
        $scope.advImageIndex = $index;
        $scope.GroupLinkType = 11;
        if($($event.currentTarget).parents('.dropdown').offset()){
            var left = $($event.currentTarget).parents('.control-group').offset().left-280;
            var height = $($event.currentTarget).parents('.dropdown').offset().top - 40;
        }else{
            var left = $($event.currentTarget).parents('.cube-add-href').offset().left-280;
            var height = $($event.currentTarget).parents('.cube-set-href').offset().top - 40;
        }
        $('#outLink_input').val('');
        $('#outLink').css('display','block');
        $('#outLink').css('left',left);
        $('#outLink').css('top',height);
    };
    //商品分组模板选择自定义外链确定
    commonServer.GroupSureSetLink = function($scope){
    	console.log($scope.Pindex,$scope.advImageIndex)
        var url = $('#outLink_input').val().substr(0,4).toLowerCase() == "http" ? $('#outLink_input').val() : "http://" + $('#outLink_input').val();
		$scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advImageIndex]['linkUrl'] = url;
        $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advImageIndex]['linkName'] = url;
        $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$scope.advImageIndex]['GroupLinkType'] = $scope.GroupLinkType;
        console.log(url)
        $('#outLink').hide();
    }
	//商品分组模板删除链接
    commonServer.deleteGroupLink = function($scope,p_index,$index){
    	$scope.Pindex = p_index;
        $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$index]['linkUrl'] = '';
        $scope.editors[$scope.index]['classifyList'][$scope.Pindex]['subClassifyList'][$index]['linkName'] = '';
    }
    //end
    return commonServer;
});
