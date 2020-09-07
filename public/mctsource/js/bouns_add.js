var app = angular.module('myApp', ['ngDraggable']);
app.controller('myCtrl',['$scope','$http',function($scope, $http) {
    $scope.microPage = { //小程序微页面数据
        title:'',
        id:'',
        created_at:'',
        is_home:''
    }
    $scope.shop_microPage = { //微商城微页面数据
        title:'',
        id:'',
        created_at:'',
        is_home:'',
        url:''
    }
    $scope.shop_micro_page = '' //小程序or微商城判断 1 小程序  2 微商城
    $scope.shop_searchTitle = '' //微商城搜索标题
    $scope.searchTitle = '' //微页面模态框搜索标题
    $scope.couponList = '' //优惠券模态框数据列表
    $scope.couponTitle = '' //优惠券模态框搜索标题
    $scope.coupon_li = [] //页面展示的优惠券列表
    $scope.imageUrl = '' //图片数据
    $scope.tempUploadImage = [] //临时存储图片数据
    $scope.start_at = '' //活动开始时间
    $scope.end_at = '' //活动结束时间
    $scope.grantType = 0 //发放方式类型
    $scope.grantVal = '' // 发放方式弹出时间
    $scope.timeType = 0 //领取次数类型
    $scope.timeVal = '' // 领取次数
    $scope.bounsTitle = '' //活动标题
    $scope.getData = '' //临时存储提交的数据
    $scope.spanFlag = true //防止重复提交

    $scope.submit = function(){
        $scope.start_at = $("#startTime").val()
        $scope.end_at = $("#endTime").val()
        if(!$scope.bounsTitle){
            tipshow('活动标题不能为空','warn');
            return false
        }
        if($scope.bounsTitle){
            if($scope.bounsTitle.length > 10){
                tipshow('活动标题必须在1-10个字内','warn');
                return false
            }
        }
        if(!$scope.start_at || !$scope.end_at){
            tipshow('活动时间不能为空','warn');
            return false
        }
        if(Date.parse(new Date($scope.end_at)) <= Date.parse(new Date($scope.start_at))) {
            tipshow('活动结束时间不能小于或等于开始时间','warn');
            return false
        }
        if(!$scope.coupon_li.length){
            tipshow('优惠券不能为空','warn');
            return false
        }
        if($scope.grantType == 1 && !$scope.grantVal){
            tipshow('红包弹出时间不能为空','warn');
            return false
        }
        if($scope.timeType == 1 && !$scope.timeVal){
            tipshow('红包领取次数不能为空','warn');
            return false
        }
        if($scope.grantType == 1 && $scope.grantVal){
            if(!(/^[0-9]+.?[0-9]*$/.test($scope.grantVal))){
                tipshow('红包弹出时间只能为数字','warn');
                return false
            }
        }
        if($scope.timeType == 1 && $scope.timeVal){
            if(!(/^[1-9]+[0-9]*]*$/.test($scope.timeVal))){
                tipshow('红包领取只能为数字','warn');
                return false
            }
        }
        var arr = []
        for(var i = 0; i < $scope.coupon_li.length; i++){
            arr.push($scope.coupon_li[i].id + '')
        }
        if($scope.timeType == 0){
            var limit_times = 0
        }else{
            var limit_times = $scope.timeVal
        }
        if($scope.grantType == 0){
            var show_interval = 0
        }else{
            var show_interval = $scope.grantVal
        }
        if($scope.imageUrl){
            var images = $scope.imageUrl.replace(imgUrl,'')
        }else{
            var images = ''
        }
        var getData = {
            title: $scope.bounsTitle,
            start_at:$scope.start_at,
            end_at:$scope.end_at,
            coupon_ids: arr,
            image:images,
            micro_page_id:$scope.microPage.id,
            shop_micro_page_id:$scope.shop_microPage.id,
            show_interval:show_interval,
            limit_times:limit_times,
            _token:$('meta[name="csrf-token"]').attr('content'),
        }
        $scope.getData = getData
        $('.t-footer input[type="button"]').attr('disabled');
        $.ajax({
            type:'get',
            url:'/merchants/marketing/bonus/isOn',
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
            },
            success:function (data) {
                console.log(data);
                if(data.status==1){
                    if(data.data.is_on == 1){
                        $(".tip_del").show()
                    }else{
                        $.ajax({
                            type:'post',
                            url:'/merchants/marketing/bonus/add',
                            data:$scope.getData,
                            success:function (data) {
                                console.log(data);
                                if(data.status==1){
                                    tipshow('添加活动成功！');
                                    setTimeout(function(){
                                        window.location.href = '/merchants/marketing/bonus/index';
                                    },1000)
                                }else{
                                    $('.t-footer input[type="button"]').removeAttr('disabled');
                                    // 许立 2018年7月9日 具体报错提示
                                    tipshow(data.info,'warn');
                                }
                            },
                            error:function(msg){
                                $('.t-footer input[type="button"]').removeAttr('disabled');
                                tipshow('添加活动失败!','warn');
                            }
                        })
                    }
                }
            },
            error:function(msg){
                $('.t-footer input[type="button"]').removeAttr('disabled');
                tipshow('添加活动失败!','warn');
            }
        })

    }
    $scope.submit_del_btn = function(num){
        $scope.getData['use_this'] = num
        if($scope.spanFlag){
            $scope.spanFlag = false
            $.ajax({
                type:'post',
                url:'/merchants/marketing/bonus/add',
                data:$scope.getData,
                success:function (data) {
                    console.log(data);
                    if(data.status==1){
                        tipshow('添加活动成功！');
                        setTimeout(function(){
                            window.location.href = '/merchants/marketing/bonus/index';
                        },1000)
                    }else{
                        $('.t-footer input[type="button"]').removeAttr('disabled');
                        // 许立 2018年7月9日 具体报错提示
                        tipshow(data.info,'warn');
                    }
                },
                error:function(msg){
                    $('.t-footer input[type="button"]').removeAttr('disabled');
                    tipshow('添加活动失败!','warn');
                }
            })
        }

    }
    $scope.close_btn_del = function(){
        $(".tip_del").hide()
    }
    /*
* @auther 邓钊
* @desc 选择时间插件数据
* @date 2018-7-17
* */
    var start = {
        elem: '#startTime',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas){
            $('#startTime').attr("value",datas);
            $scope.startTime = datas
            var end_at = $('#endTime').val()
            timesFlag(end_at,datas)
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var end ={
        elem: '#endTime',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(),
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas){
            $('#endTime').attr("value",datas);
            var start_at = $('#startTime').val()
            timesFlag(datas,start_at)
        }
    };
    laydate(start);
    laydate(end);

    function timesFlag(end_at,start_at) {
        if (!end_at || !start_at) {
            return false
        } else {
            $.ajax({
                url: '/merchants/marketing/bonus/isTimeValid',
                data: {
                    start_at: start_at,
                    end_at: end_at,
                    bonus_id: 0,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'post',
                success: function (res) {
                    if (res.status == 1) {
                        if (res.data.is_valid == 0) {
                            tipshow('当前时间段存在进行中的活动，请重新选择！', 'warn');
                            $("#startTime").val($scope.start_at)
                            $("#endTime").val($scope.end_at)
                        }
                    }
                }
            })
        }
    }
    /*
    * @auther 邓钊
    * @desc 删除图片
    * @date 2018-7-17
    * */
    $scope.closeImg = function(){
        $scope.imageUrl = ''
    }
    /*
    * @auther 邓钊
    * @desc 红包发放方式
    * @date 2018-7-17
    * @param num 红包发放的类型
    * */
    $scope.grantClick = function(num){
        if(num == 1){
            $scope.grantType = 1
        }else{
            $scope.grantType = 0
        }
    }
    /*
    * @auther 邓钊
    * @desc 红包领取次数
    * @date 2018-7-17
    * @param num 红包领取次数的类型
    * */
    $scope.timeClick = function(num){
        if(num == 1){
            $scope.timeType = 1
        }else{
            $scope.timeType = 0
        }
    }

    /*
    * @auther 邓钊
    * @desc 删除微页面
    * @date 2018-7-16
    * */
    $scope.closePage = function(num){
        if(num == 1){
            $scope.microPage.title = ''
            $scope.microPage.id = ''
        }else if(num == 2){
            $scope.shop_microPage.title = ''
            $scope.shop_microPage.id = ''
        }

    }
    /*
    * @auther 邓钊
    * @desc 获取微页面列表
    * @date 2018-7-16
    * */
    $scope.micropageModel =  function(num){
        $scope.shop_micro_page = num
        if(num == 1){
            $scope.temp = [];
            // $scope.searchTitle = '';
            $.get('/merchants/xcx/micropage/select?page=1', function(data) {
                $scope.pageList = [];
                angular.forEach(data.data,function(val,key){
                    $scope.$apply(function(){
                        $scope.pageList.push({
                            "id":val.id,
                            "name":val.title,
                            "is_home":val.url,
                            "created_at":val.create_time
                        })
                    })
                })

                var totalCount = data.total, showCount = 10,
                    limit = data.pageSize;
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/xcx/micropage/select?page=' + page,function(response){
                            // console.log(response);
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "is_home":val.url,
                                            "created_at":val.create_time
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
        }else if(num == 2){
            $scope.temp = [];
            $scope.searchTitle = '';
            $.get('/merchants/store/selectPage?page=1', function(data) {
                $scope.pageList = [];
                angular.forEach(data.data,function(val,key){
                    $scope.$apply(function(){
                        $scope.pageList.push({
                            "id":val.id,
                            "name":val.page_title,
                            "is_home":val.is_home,
                            "created_at":val.created_at,
                            "url": val.url
                        })
                    })
                })

                var totalCount = data.total, showCount = 10,
                    limit = data.pageSize;
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/store/selectPage?page=' + page,function(response){
                            // console.log(response);
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.page_title,
                                            "is_home":val.is_home,
                                            "created_at":val.created_at,
                                            "url": val.url
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
    }
    /*
    * @auther 邓钊
    * @desc 选取微页面
    * @date 2018-7-16
    * */
    $scope.choosePageLinkSure = function(list){
        if($scope.shop_micro_page == 1){
            $scope.microPage.title = list.name
            $scope.microPage.id = list.id
            $scope.microPage.is_home = list.is_home
            $scope.microPage.created_at = list.created_at
        }else if($scope.shop_micro_page == 2){
            $scope.shop_microPage = { //微商城微页面数据
                title:list.name,
                id:list.id,
                created_at:list.created_at,
                is_home:list.is_home,
                url:list.url
            }
        }

        hideModel($('#page_model'));
    }
    /*
   * @auther 邓钊
   * @desc 搜索微页面
   * @date 2018-7-16
   * */
    $scope.searchPage = function(){
        if($scope.shop_micro_page == 1){
            $scope.temp = [];
            $scope.pageList = [];
            $scope.searchTitle = $("#searchTitle").val()
            $.get('/merchants/xcx/micropage/select?page=1&title=' + $scope.searchTitle, function(data) {
                angular.forEach(data.data,function(val,key){
                    $scope.$apply(function(){
                        $scope.pageList.push({
                            "id":val.id,
                            "name":val.title,
                            "is_home":val.is_home,
                            "created_at":val.create_time
                        })
                    })
                })
                var totalCount = data.total, showCount = 10,
                    limit = data.pageSize;
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/xcx/micropage/select?page=' + page + '&title=' + $scope.searchTitle,function(response){
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "is_home":val.is_home,
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
        }else if($scope.shop_micro_page == 2){
            console.log($scope.shop_searchTitle);
            $scope.temp = [];
            $scope.pageList = [];
            $scope.shop_searchTitle = $("#shop_searchTitle").val()
            $.get('/merchants/store/selectPage?page=1&title=' + $scope.shop_searchTitle, function(data) {
                angular.forEach(data.data,function(val,key){
                    $scope.$apply(function(){
                        $scope.pageList.push({
                            "id":val.id,
                            "name":val.page_title,
                            "is_home":val.is_home,
                            "created_at":val.created_at,
                            "url": val.url
                        })
                    })
                })
                var totalCount = data.total, showCount = 10,
                    limit = data.pageSize;
                $('.page_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/store/selectPage?page=' + page + '&title=' + $scope.shop_searchTitle,function(response){
                            if(response.errCode == 0){
                                $scope.pageList = [];
                                angular.forEach(response.data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.pageList.push({
                                            "id":val.id,
                                            "name":val.page_title,
                                            "is_home":val.is_home,
                                            "created_at":val.created_at,
                                            "url": val.url
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
    }


    /*
   * @auther 邓钊
   * @desc 显示优惠券列表
   * @date 2018-7-16
   * */
    $scope.showCouponModel = function(){
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1', function(data) {
            console.log(data);
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
                        "start_at":val.start_at,
                        "end_at":val.end_at,
                        "range_type":val.range_type
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
                                        "start_at":val.start_at,
                                        "end_at":val.end_at,
                                        "range_type":val.range_type
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
    /*
    * @auther 邓钊
    * @desc 搜索优惠券列表
    * @date 2018-7-16
    * */
    $scope.searchCoupon = function(){
        $scope.couponList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page=1&title=' + $scope.couponTitle, function(data) {
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
                        "start_at":val.start_at,
                        "end_at":val.end_at,
                        "range_type":val.range_type
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
                    $.get('/merchants/linkTo/get?type=12&wid='+ wid +'&page='+page + '&title=' + $scope.couponTitle,function(response){
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
                                        "start_at":val.start_at,
                                        "end_at":val.end_at,
                                        "range_type":val.range_type
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
    /*
    * @auther 邓钊
    * @desc 选取优惠券
    * @date 2018-7-16
    * */
    $scope.chooseCoupon = function(list){
        console.log(list);
        var start_at = list.start_at.split(' ')
        var end_at = list.end_at.split(' ')
        if(list.range_type == 0){
            var range_type_title = '全品类'
        }else if(list.range_type == 1){
            var range_type_title = '部分劵'
        }
        if($scope.coupon_li.length >= 5){
            tipshow('优惠券最多可添加五张','warn');
            $scope.hideModel()
            return false
        }
        if($scope.coupon_li.length){
            for(var i = 0; i < $scope.coupon_li.length; i++){
                if($scope.coupon_li[i].id == list.id){
                    tipshow('请勿重复添加相同优惠券','warn');
                    return false
                }
            }
        }
        $scope.coupon_li.push({
            limit_desc:list.limit_desc,
            name:list.name,
            amount:list.amount,
            start_at:start_at[0],
            end_at:end_at[0],
            range_type_title:range_type_title,
            range_type:list.range_type,
            id:list.id
        })
        $scope.hideModel()
    }
    /*
    * @auther 邓钊
    * @desc 删除优惠券
    * @date 2018-7-16
    * */
    $scope.closeCoupon = function(index){
        $scope.coupon_li.splice(index,1)
    }
    /*
    * @auther 邓钊
    * @desc 打开图片选择模态框
    * @date 2018-7-16
    * */
    $scope.addAdvs = function(){
        layer.open({
            type: 2,
            title: false,
            closeBtn: false,
            // skin:"layer-tskin", //自定义layer皮肤
            move: false, //不允许拖动
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        });
        selImgCallBack = function(resultSrc) {
            console.log(resultSrc);
            if (resultSrc.length > 0) {
                var num = parseInt(parseInt(resultSrc[0].imgWidth) / parseInt(resultSrc[0].imgHeight) * 10) / 10
                if(num < num - 0.1 || num > num + 0.2){
                    tipshow("图片尺寸不符合，请重新选择图片","warn");
                    return false
                }
                if(parseInt(resultSrc[0].imgWidth) < 400){
                    tipshow("图片尺寸不符合，请重新选择图片","warn");
                    return false
                }
                $scope.safeApply(function () {
                    $scope.imageUrl = imgUrl + resultSrc[0].imgSrc
                })
            }
        }
    }

    /*
    * @auther 邓钊
    * @desc 更新异步获取的ng数据
    * @date 2018-7-16
    * */
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

    /*
    * @auther 邓钊
    * @desc 隐藏模态框
    * @date 2018-7-16
    * */
    $scope.hideModel = function(){
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
        hideModel($('#research_model'));
    }
}])