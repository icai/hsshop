function chose_mult_set_ini(select,values){
    if (values) {
        var arr = JSON.parse(values);
        var length = arr.length;
        var value = '';
        for(i=0;i<length;i++){
            value = arr[i];
            console.log(value);
            $(select+' option'+"[value='"+value+"']").attr('selected','selected');
        }
        $(select).trigger("liszt:updated");
    }
}
$(function () {
})
app.controller('myCtrl',['$scope','$sce','$timeout','$http','commonServer',function($scope, $sce,$timeout,$http,commonServer) {
    $scope.wai_setting = true; //暂时显示图片广告外链，后期删除
    $scope.baseInfo = true;
    //add by 邓钊 2018-6-27
    $scope.type = 0;
    //end
    $scope.pageSeting = {
        title:'调查留言',
        start_at:'',
        end_at:'',
        times_type:'',
        //add by 赵彬 2018-8-8
        background_color:'',
        submit_button_title:'',
        submit_button_color:''
        //end
    };//页面设置
    $scope.host = imgUrl;
    $scope._host = _host;
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
    $scope.advsImagesIndex = commonServer.advsImagesIndex;//点击图片索引
    $scope.shopLinkPosition = commonServer.shopLinkPosition; //记录选择商品链接位置1为图片广告，2为标题
    $scope.choosePosition = 1;//1为图片广告，2为图片导航
    $scope.link_type = 1 //1.为微页面及分类2.商品及分类3.店铺主页4.会员主页
    $scope.choosePage = 1 //1为美妆小店，2为微页面
    $scope.changeImange = false;//默认选中图片不是改变背景
    $scope.is_custom = 1;//自定义模块默认可以添加
    /*@author huoguanghui start*/
    $scope.activity_list = [] //营销活动列表
    $scope.activityNavList = ["幸运大转盘","砸金蛋"];//营销活动导航列表
    $scope.activityIndex = 0;//营销活动 活动选择
    $scope.searchTitle = '';
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
    /*@author huoguanghui end*/

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
    // $('.chosen_select').chosen();
    $scope.couponList = [];//优惠券列表
    $scope.goodList = [
        {
            "name":"实物商品（购买时需填写收货地址，测试商品，不发货，不退款",
            "thumbnail": _host + "mctsource/images/test-card.jpg",
            "info":"这是商品通知",
            "price":"￥1",
            "timeDay":"2016-09-22",
            "timestamp":"15:57:27",
            "url":''  
        }
    ];
    $scope.QQlist = []  //QQ客服列表
    $scope.uploadImages = []  //选择图片数组
    console.log(editData)
    $scope.editData = editData
    console.log($scope.editData);
    if($scope.editData){
        //add by 邓钊 2018-60-27
        $scope.type = editData.type
        //end
        //add by 赵彬 2018-8-8
        var activity_type = editData.type;
        if(activity_type == 0){
            $(".crumb_nav a[href='javascript:void(0)']").text('在线报名');
        }else if(activity_type == 1){
            $(".crumb_nav a[href='javascript:void(0)']").text('在线预约');
        }else if(activity_type == 2){
            $(".crumb_nav a[href='javascript:void(0)']").text('在线投票');
        }
        //end
        var newDate = Date.parse(new Date())
        console.log(newDate);
        console.log(Date.parse(new Date(editData.start_at)));
        console.log(Date.parse(new Date(editData.end_at)));
        if(Date.parse(new Date(editData.start_at)) < newDate && newDate < Date.parse(new Date(editData.end_at)) && !Date.parse(new Date(editData.invalidate_at))){
            $(".radio_box").find('input').attr('disabled','true')
            $("#startTime").attr('disabled','true')
        }
        $scope.pageSeting.title = editData.title;
        $scope.pageSeting.start_at = editData.start_at;
        $scope.pageSeting.end_at = editData.end_at;
        $scope.pageSeting.times_type = editData.times_type;
        $scope.pageSeting.background_color = editData.background_color;
        $scope.pageSeting.submit_button_title = editData.submit_button_title;
        $scope.pageSeting.submit_button_color = editData.submit_button_color;
        $scope.page_template = editData.rules;
        $scope.first_card = true;
        $scope.editors = []
        for(var key in $scope.page_template){
            $scope.editors.push($scope.page_template[key])
        }
        console.log($scope.editors);
        angular.forEach($scope.editors,function(val,key){
            val['showRight'] = false
            if(val.card_right){
                val['cardRight'] = val.card_right
            }
            if(val.type == 'time'){
                val['textLength']=[
                    {
                        'value': 0,
                        'key': '单选时间'
                    },
                    {
                        'value': 1,
                        'key': '区间时间'
                    },
                ]
                val['selectedSite'] = val['textLength'][val.rule_time_type]
            }else if(val.type == 'text'){
                val['textLength']=[
                    {
                        'value': 0,
                        'key': '长'
                    },
                    {
                        'value': 1,
                        'key': '中'
                    },
                    {
                        'value': 2,
                        'key': '短'
                    }
                ]
                val['selectedSite'] = val['textLength'][val.rule_text_width]
            }else if(val.type == "vote_image"){
                for(var i = 0; i < val.sub_rules.length; i++){
                    val.sub_rules[i]['imgflag'] = true
                    if(val.sub_rules[i].image != ''){
                        if(val.sub_rules[i].image.split('/')[0] == "hsshop"){
                            val.sub_rules[i].image = imgUrl + val.sub_rules[i].image
                        }else{
                            val.sub_rules[i].image = _host + val.sub_rules[i].image
                        } 
                    }
                }
            }else if(val.type == "appoint_image"){
                for(var i = 0; i < val.sub_rules.length; i++){
                    val.sub_rules[i]['imgflag'] = true
                    if(val.sub_rules[i].image != ''){
                        if(val.sub_rules[i].image.split('/')[0] == "hsshop"){
                            val.sub_rules[i].image = imgUrl + val.sub_rules[i].image
                        }else{
                            val.sub_rules[i].image = _host + val.sub_rules[i].image
                        } 
                    }
                }
            }else if(val.type == 'set_image'){
                if(val.sub_rules[0].image != ''){
                    if(val.sub_rules[0].image.split('/')[0] == "hsshop"){
                        val.sub_rules[0].image = imgUrl + val.sub_rules[0].image
                    }else{
                        val.sub_rules[0].image = _host + val.sub_rules[0].image
                    } 
                }
                for(var i = 0; i < val.sub_rules.length; i++){
                    val.sub_rules[i]['rule_image_flag'] = true
                }
                
            }else if(val.type == "appoint_text"){
                val['selectedSite']= {
                    'title': '请选择',
                    'type':'option'
                }
                if(val.rule_appoint_default){
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(val.sub_rules[i]['title'] == val.rule_appoint_default){
                            val['default_num'] = i
                            console.log(val);
                            break;
                        }
                    }
                }else{
                    val['default_num'] = 0
                }

            }else if(val.type == "appoint_time"){
                val['selectedSite']= {
                    'title': '请选择',
                    'type':'option'
                }
                if(val.rule_appoint_default){
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(val.sub_rules[i]['title'] == val.rule_appoint_default){
                            val['default_num'] = i
                            console.log(val);
                            break;
                        }
                    }
                }else{
                    val['default_num'] = 0
                }
            }else if(val.type == "vote_text"){
                val['qita'] = false
                for(var i = 0; i < val.sub_rules.length; i++){
                    if(val.sub_rules[i]['type'] == 'other'){
                        val['qita'] = true
                        break;
                    }
                }
            }else if(val.type == "phone"){
                var tel = val.rule_phone_value.indexOf('-')
                if(tel != -1){
                    var telArr = val.rule_phone_value.split('-');
                    val['min_mobile'] = telArr[0]
                    val['mobile'] = telArr[1]
                }else{
                    val['min_mobile'] = ''
                    val['mobile'] = val.rule_phone_value
                }

            }
        })
    }


    // 添加或修改微页面信息
    $scope.processPage=function(isValid,isshow){
        if(!$scope.pageSeting.title){
            tipshow("调查留言标题不能为空", "warn");
            return false;
        }
        if(!$scope.pageSeting.start_at){
            tipshow("调查留言开始时间不能为空", "warn");
            return false;
        }
        if(!$scope.pageSeting.end_at){
            tipshow("调查留言结束时间不能为空", "warn");
            return false;
        }
        $scope.iserror = true;
        var is_show = isshow;
        if(isValid){
            var timestamp1 = Date.parse(new Date($("#startTime").val().replace(/-/g,"/")));
            var timestamp2 = Date.parse(new Date($("#endTime").val().replace(/-/g,"/")));
            if(!timestamp1 || !timestamp2){
                tipshow("请填写头部标题的有效时间", "warn");
                return false;
            }
            if(timestamp2 <= timestamp1){
                tipshow("结束时间必须大于开始时间", "warn");
                return false;
            }
            if(typeof $scope.editors == 'string' && $scope.editors == ''){
                $scope.editors = [];
            }
            $scope.postData = angular.copy($scope.editors);
            //需后台插入的数据进行初始化 避免垃圾数据
            for(var k = 0; k < $scope.postData.length; k++){
                var val = $scope.postData[k]
                if (val.type == 'vote_text') {
                    if (val.max_options < val.min_options) {
                        tipshow("文本投票的最少选项不能大于最大选项", "warn");
                        return false;
                    } else if (val.max_options > val.sub_rules.length) {
                        tipshow("文本投票的最大选项不能超过选项数", "warn");
                        return false;
                    } else if (val.min_options > val.sub_rules.length) {
                        tipshow("文本投票的最小选项不能超过选项数", "warn");
                        return false;
                    } else if(val.sub_rules.length < 2){
                        tipshow("文本投票的选项不能少于两个", "warn");
                        return false
                    }
                }
                if (val.type == 'vote_image') {
                    if (val.max_options < val.min_options) {
                        tipshow("图片投票的最少选项不能大于最大选项", "warn");
                        return false;
                    } else if (val.max_options > val.sub_rules.length) {
                        tipshow("图片投票的最大选项不能超过选项数", "warn");
                        return false;
                    } else if (val.min_options > val.sub_rules.length) {
                        tipshow("图片投票的最小选项不能超过选项数", "warn");
                        return false;
                    } else if(val.sub_rules.length < 2){
                        tipshow("图片投票的选项不能少于两个", "warn");
                        return false
                    }
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(!val.sub_rules[i].imgflag){
                            tipshow("至少需要上传一张图片调查选项的图片", "warn");
                            return false;
                        }
                    }
                }
                if (val.type == 'phone') {
                    if(!val.min_mobile && val.mobile.length == 11){
                        var phone_a =  /^1(3|4|5|7|8|9)\d{9}$/.test(val.mobile)
                        if (!phone_a) {
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                        val.rule_phone_value = val.mobile
                    }else if(!val.min_mobile && val.mobile){
                        var mobile = /(^[0-9]{7,8}$)/.test(val.mobile)
                        if(!mobile){
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                        val.rule_phone_value = val.mobile
                    }else if(val.min_mobile && val.mobile){
                        if(/^((\+?86)|(\(\+86\)))$/.test(val.min_mobile)){
                            var min_mobile = true
                        }else if(/^0\d{2,3}$/.test(val.min_mobile)){
                            var min_mobile = true
                        }
                        if(val.mobile.length == 11){
                            var mobile = /^1(3|4|5|7|8|9)\d{9}$/.test(val.mobile)
                        }else{
                            var mobile = /(^\d{7,8}$)/.test(+val.mobile)
                        }
                        if(min_mobile && mobile){
                            val.rule_phone_value = val.min_mobile+'-'+ val.mobile
                        }else{
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                    }else if(val.min_mobile && !val.mobile){
                        tipshow("手机号码输入错误，请重填", "warn");
                        return false;
                    }else if(!val.min_mobile && !val.mobile){
                        tipshow("手机号码输入错误，请重填", "warn");
                        return false;
                    }

                }
                if(val.type == 'appoint_text'){
                    if(val.sub_rules.length < 2){
                        tipshow("文本预约的选项不能少于两个", "warn");
                        return false
                    }
                }
                if(val.type == 'appoint_image'){
                    if(val.sub_rules.length < 2){
                        tipshow("图片预约的选项不能少于两个", "warn");
                        return false
                    }
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(!val.sub_rules[i].imgflag){
                            tipshow("至少需要上传一张图片作为选择预约人选项的图片", "warn");
                            return false;
                        }
                    }
                }
                if(val.type == 'set_image'){
                    console.log(val.sub_rules)
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(val.title == ''){
                            if(!val.sub_rules[i].rule_image_flag){
                                tipshow("请上传一张图片作为图片设置的图片", "warn");
                                return false;
                            }
                        }
                    }
                }
            }
            //魔方数据不完整则退出
            // $scope.postData = JSON.stringify($scope.postData);
            $scope.pageSeting.start_at = $("#startTime").val();
            $scope.pageSeting.end_at = $("#endTime").val();
            angular.forEach($scope.postData,function(val,key){
                if(val.type == 'appoint_text'){
                    if(val.selectedSite.title == '请选择'){
                        val.rule_appoint_default = ''
                    }else{
                        val.rule_appoint_default = val.selectedSite.title
                    }
                    // val.rule_appoint_default = val.selectedSite.title ? val.selectedSite.title : ''
                }
                if(val.type == 'time'){
                    val.rule_time_type = val.selectedSite.value
                }
                if(val.type == 'text'){
                    val.rule_text_width = val.selectedSite.value
                }
                if(val.type == 'set_image'){
                    if(val.sub_rules[0].image != ''){
                        val.sub_rules[0].image = val.sub_rules[0].image.replace(imgUrl,'')
                    }   
                }
            })
            var data = {
                // wid:$scope.editData.wid,
                type:$scope.type,
                title:$scope.pageSeting.title,
                start_at:$scope.pageSeting.start_at,
                end_at:$scope.pageSeting.end_at,
                rules:$scope.postData,
                times_type:$scope.pageSeting.times_type,
                background_color:$scope.pageSeting.background_color,
                submit_button_title:$scope.pageSeting.submit_button_title,
                submit_button_color:$scope.pageSeting.submit_button_color,
                status:1,
                is_show:1,
                _token:$('meta[name="csrf-token"]').attr('content'),
                is_show:is_show,
            }
            console.log(data);
            $('.btn_grounp button').attr('disabled','disabled');
            $.ajax({
                type:'post',
                url:'/merchants/marketing/researchEdit/'+$scope.editData.id,
                data:data,
                success:function (data) {
                    console.log(data);
                    if(data.status==1){
                        tipshow('修改活动成功！');
                        setTimeout(function(){
                            window.location.href = '/merchants/marketing/researches/'+$scope.type;
                        },1000)
                    }else{
                        console.log(222)
                        $('.btn_grounp button').removeAttr('disabled');
                        tipshow('修改活动失败！','warn');
                    }
                },
                error:function(msg){
                    console.log(msg)
                    $('.btn_grounp button').removeAttr('disabled');
                    tipshow('修改活动失败!','warn');
                }
            })
        }else{
            $scope.postData = angular.copy($scope.editors);
            for(var k = 0; k < $scope.postData.length; k++){
                var val = $scope.postData[k]
                if(!val.title){
                    switch (val.type){
                        case "time":
                            tipshow("选择时间标题不能为空", "warn");
                            return false;
                        case "text":
                            tipshow("文本标题不能为空", "warn");
                            return false;
                        case "phone":
                            tipshow("电话号码标题不能为空", "warn");
                            return false;
                        case "vote_text":
                            tipshow("文本投票标题不能为空", "warn");
                            return false;
                        case "vote_image":
                            tipshow("图片投票标题不能为空", "warn");
                            return false;
                        case "appoint_text":
                            tipshow("文本预约标题不能为空", "warn");
                            return false;
                        case "appoint_image":
                            tipshow("图片预约标题不能为空", "warn");
                            return false;
                        case "address":
                            tipshow("地域调查标题不能为空", "warn");
                            return false;
                        case "image":
                            tipshow("图片上传标题不能为空", "warn");
                            return false;
                    }
                }
            }
        }
    }
    //预览效果
    $scope.previewPage=function(isValid,isshow){
        if(!$scope.pageSeting.title){
            tipshow("调查留言标题不能为空", "warn");
            return false;
        }
        if(!$scope.pageSeting.start_at){
            tipshow("调查留言开始时间不能为空", "warn");
            return false;
        }
        if(!$scope.pageSeting.end_at){
            tipshow("调查留言结束时间不能为空", "warn");
            return false;
        }
        $scope.iserror = true;
        var is_show = isshow;
        if(isValid){
            var timestamp1 = Date.parse(new Date($("#startTime").val()));
            var timestamp2 = Date.parse(new Date($("#endTime").val()));
            if(!timestamp1 || !timestamp2){
                tipshow("请填写头部标题的有效时间", "warn");
                return false;
            }
            if(timestamp2 <= timestamp1){
                tipshow("结束时间必须大于开始时间", "warn");
                return false;
            }
            if(typeof $scope.editors == 'string' && $scope.editors == ''){
                $scope.editors = [];
            }
            $scope.postData = angular.copy($scope.editors);
            //需后台插入的数据进行初始化 避免垃圾数据
            for(var k = 0; k < $scope.postData.length; k++){
                var val = $scope.postData[k]
                if (val.type == 'vote_text') {
                    if (val.max_options < val.min_options) {
                        tipshow("文本投票的最少选项不能大于最大选项", "warn");
                        return false;
                    } else if (val.max_options > val.sub_rules.length) {
                        tipshow("文本投票的最大选项不能超过选项数", "warn");
                        return false;
                    } else if (val.min_options > val.sub_rules.length) {
                        tipshow("文本投票的最小选项不能超过选项数", "warn");
                        return false;
                    } else if(val.sub_rules.length < 2){
                        tipshow("文本投票的选项不能少于两个", "warn");
                        return false
                    }
                }
                if (val.type == 'vote_image') {
                    if (val.max_options < val.min_options) {
                        tipshow("图片投票的最少选项不能大于最大选项", "warn");
                        return false;
                    } else if (val.max_options > val.sub_rules.length) {
                        tipshow("图片投票的最大选项不能超过选项数", "warn");
                        return false;
                    } else if (val.min_options > val.sub_rules.length) {
                        tipshow("图片投票的最小选项不能超过选项数", "warn");
                        return false;
                    } else if(val.sub_rules.length < 2){
                        tipshow("图片投票的选项不能少于两个", "warn");
                        return false
                    }
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(!val.sub_rules[i].imgflag){
                            tipshow("至少需要上传一张图片调查选项的图片", "warn");
                            return false;
                        }
                    }
                }
                if (val.type == 'phone') {
                    if(!val.min_mobile && val.mobile.length == 11){
                        var phone_a =  /^1(3|4|5|7|8|9)\d{9}$/.test(val.mobile)
                        if (!phone_a) {
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                        val.rule_phone_value = val.mobile
                    }else if(!val.min_mobile && val.mobile){
                        var mobile = /(^[0-9]{7,8}$)/.test(val.mobile)
                        if(!mobile){
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                        val.rule_phone_value = val.mobile
                    }else if(val.min_mobile && val.mobile){
                        if(/^((\+?86)|(\(\+86\)))$/.test(val.min_mobile)){
                            var min_mobile = true
                        }else if(/^0\d{2,3}$/.test(val.min_mobile)){
                            var min_mobile = true
                        }
                        if(val.mobile.length == 11){
                            var mobile = /^1(3|4|5|7|8|9)\d{9}$/.test(val.mobile)
                        }else{
                            var mobile = /(^\d{7,8}$)/.test(+val.mobile)
                        }
                        if(min_mobile && mobile){
                            val.rule_phone_value = val.min_mobile+'-'+ val.mobile
                        }else{
                            tipshow("手机号码输入错误，请重填", "warn");
                            return false;
                        }
                    }else if(val.min_mobile && !val.mobile){
                        tipshow("手机号码输入错误，请重填", "warn");
                        return false;
                    }else if(!val.min_mobile && !val.mobile){
                        tipshow("手机号码输入错误，请重填", "warn");
                        return false;
                    }

                }
                if(val.type == 'appoint_text'){
                    if(val.sub_rules.length < 2){
                        tipshow("文本预约的选项不能少于两个", "warn");
                        return false
                    }
                }
                if(val.type == 'appoint_image'){
                    if(val.sub_rules.length < 2){
                        tipshow("图片预约的选项不能少于两个", "warn");
                        return false
                    }
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(!val.sub_rules[i].imgflag){
                            tipshow("至少需要上传一张图片作为选择预约人选项的图片", "warn");
                            return false;
                        }
                    }
                }
                if(val.type == 'set_image'){
                    console.log(val)
                    for(var i = 0; i < val.sub_rules.length; i++){
                        if(val.title == ''){
                            if(!val.sub_rules[i].rule_image_flag){
                                tipshow("请上传一张图片作为图片设置的图片", "warn");
                                return false;
                            }
                        }
                    }
                }
            }
            //魔方数据不完整则退出
            // $scope.postData = JSON.stringify($scope.postData);
            $scope.pageSeting.start_at = $("#startTime").val();
            $scope.pageSeting.end_at = $("#endTime").val();
            angular.forEach($scope.postData,function(val,key){
                if(val.type == 'appoint_text'){
                    if(val.selectedSite.title == '请选择'){
                        val.rule_appoint_default = ''
                    }else{
                        val.rule_appoint_default = val.selectedSite.title
                    }
                    // val.rule_appoint_default = val.selectedSite.title ? val.selectedSite.title : ''
                }
                if(val.type == 'time'){
                    val.rule_time_type = val.selectedSite.value
                }
                if(val.type == 'text'){
                    val.rule_text_width = val.selectedSite.value
                }
                if(val.type == 'set_image'){
                    if(val.sub_rules[0].image != ''){
                        val.sub_rules[0].image = val.sub_rules[0].image.replace(imgUrl,'')
                    }
                }
            })
            var data = {
                // wid:$scope.editData.wid,
                type:$scope.type,
                title:$scope.pageSeting.title,
                start_at:$scope.pageSeting.start_at,
                end_at:$scope.pageSeting.end_at,
                rules:$scope.postData,
                times_type:$scope.pageSeting.times_type,
                status:1,
                is_show:1,
                _token:$('meta[name="csrf-token"]').attr('content'),
                is_show:is_show,
                background_color:$scope.pageSeting.background_color,
                submit_button_title:$scope.pageSeting.submit_button_title,
                submit_button_color:$scope.pageSeting.submit_button_color,
            }
            console.log(data);
            $('.btn_grounp button').attr('disabled','disabled');
            $.ajax({
                type:'post',
                url:'/merchants/marketing/researchEdit/'+$scope.editData.id,
                data:data,
                success:function (data) {
                    console.log(data);
                    if(data.status==1){
                        tipshow('修改活动成功！');
                        setTimeout(function(){
                            window.location.href = '/merchants/marketing/researchPreview/'+$scope.editData.id;
                        },1000)
                    }else{
                        console.log(222)
                        $('.btn_grounp button').removeAttr('disabled');
                        tipshow('修改活动失败！','warn');
                    }
                },
                error:function(msg){
                    console.log(msg)
                    $('.btn_grounp button').removeAttr('disabled');
                    tipshow('修改活动失败!','warn');
                }
            })
        }else{
            $scope.postData = angular.copy($scope.editors);
            for(var k = 0; k < $scope.postData.length; k++){
                var val = $scope.postData[k]
                if(!val.title){
                    switch (val.type){
                        case "time":
                            tipshow("选择时间标题不能为空", "warn");
                            return false;
                        case "text":
                            tipshow("文本标题不能为空", "warn");
                            return false;
                        case "phone":
                            tipshow("电话号码标题不能为空", "warn");
                            return false;
                        case "vote_text":
                            tipshow("文本投票标题不能为空", "warn");
                            return false;
                        case "vote_image":
                            tipshow("图片投票标题不能为空", "warn");
                            return false;
                        case "appoint_text":
                            tipshow("文本预约标题不能为空", "warn");
                            return false;
                        case "appoint_image":
                            tipshow("图片预约标题不能为空", "warn");
                            return false;
                        case "address":
                            tipshow("地域调查标题不能为空", "warn");
                            return false;
                        case "image":
                            tipshow("图片上传标题不能为空", "warn");
                            return false;
                    }
                }
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
            // 'order_link':'/shop/order/index/'+store.id
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
        commonServer.initCartRight($scope.index,$scope,135);
    }
    $scope.addeditor = function(position){
        commonServer.addeditor($scope,ue,position);
    } 
    // 左侧每个列表
    $scope.tool = function($event,editor){
        $scope.first_card = false;
        commonServer.tool($event,editor,$scope,135,ue);
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

    //crmember右侧修改背景图
    $scope.changeBg = function(){
        commonServer.changeBg($scope);
    }

    //显示model
    $scope.showModel = function(){
        commonServer.showModel($scope);
    }

    // 隐藏model
    $scope.hideModel = function(){
        commonServer.hideModel();
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
            
            console.log($scope.goodList);
            var totalCount = data.total, showCount = 10,
                limit = data.pageSize;
                // alert(totalCount)
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
                                            "url":val.url,
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
                console.log(res);
                var logo = res.data.FileInfo['path'];
                $scope.$apply(function(){
                    $scope.editors[$scope.index]['logo'] = imgUrl + logo;
                })
                // console.log($scope.editors[$scope.index]);
                $.post('/merchants/currency/index',{id:id,logo:logo,_token:$('meta[name="csrf-token"]').attr('content')},function(data){
                    console.log(data);
                    // window.location.reload();
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
                            console.log(response);
                            // $scope.goodList = 
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
                            console.log(response);
                            // $scope.goodList = 
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
                            console.log(response);
                            // $scope.goodList = 
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
    //选择拼团列表model
    $scope.showSpellModel = function(){
        $scope.goods_show = true;
        $http.get('/merchants/grouppurchase/groupList?pageSize=6').then(function(data){
            console.log(data.data.data[0].data);
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
            
            // console.log($scope.goodList);
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
                                console.log($scope.pageList);
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
    /**
     * 视频模块
     * @author huoguanghui
     */
    //新增视频模块
    $scope.addVideo =function(position){
        commonServer.addVideo($scope,position)
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
    // 指定商品模态框刷新
    $scope.refresh = function(){
        $scope.searchGoods();
    }
    uploader.on('uploadSuccess', function (file, response) {
        if (response.status == 1) {
            $scope.$apply(function () {
                response.data['FileInfo']['path'] = imgUrl + response.data['FileInfo']['path'];
                $scope.tempUploadImage.unshift(response.data);
                console.log($scope.tempUploadImage);
            })
        }
    });




    // 文字添加
    $scope.addtext = function(position){
        commonServer.addtext($scope,position);
    }
    // 添加日期
    $scope.adddataTime = function(){
        commonServer.adddataTime($scope);
    }
    //添加电话
    $scope.addTel = function(position){
        commonServer.addTel($scope,position);
    }
    //添加邮箱
    $scope.addEmail = function(position){
        commonServer.addEmail($scope,position);
    }
    //文本选项添加
    $scope.addTextOption = function(position){
        commonServer.addTextOption($scope,position);
    }
    // 删除一个文本选项
    $scope.closeOpn = function($index){
        commonServer.closeOpn($scope,$index);
    }
    // 添加一个文本选项
    $scope.addOption = function(){
        commonServer.addOption($scope);
    }
    // 添加一个其他文本选项
    $scope.addQita = function(){
        commonServer.addQita($scope);
    }
    // 图片选项添加
    $scope.addImages = function(position){
        commonServer.addImages($scope,position);
    }
    // 添加一个图片选项
    $scope.addImgOpn = function(){
        commonServer.addImgOpn($scope);
    }
    // 删除一个图片选项
    $scope.removeImgVote = function($index){
        commonServer.removeImgVote($scope,$index);
    }
    //图片上传添加
    $scope.addUpload = function(position){
        commonServer.addUpload($scope,position);
    }
    //添加文本预约
    $scope.txtBooking = function(position){
        commonServer.txtBooking($scope,position);
    }
    //添加文本预约选项
    $scope.addBookingOpt = function(){
        commonServer.addBookingOpt($scope);
    }
    // 删除一个文本预约选项
    $scope.removeBookingOpt = function($index){
        commonServer.removeBookingOpt($scope,$index);
    }
    // 添加图片预约
    $scope.imgBooking = function(position){
        commonServer.addImaBooking($scope,position);
    }
    // 添加一个图片预约选项
    $scope.addimgBookingOpn = function(){
        commonServer.addimgBookingOpn($scope);
    }
    // 删除一个图片预约选项
    $scope.removeimgBooking = function($index){
        commonServer.removeimgBooking($scope,$index);
    }
    // 分隔线
    $scope.addSeparator = function($index){
        commonServer.addSeparator($scope,$index);
    }
    //图片选项添加图片
    $scope.addShareImages = function($index){
        commonServer.addShareImages($scope,$index);
    }
    //删除图片选项图片
    $scope.removeShareImg = function($index){
        commonServer.removeShareImg($scope,$index);
    }
    // 分割线
    $scope.separatorLine = function($index){
        commonServer.separatorLine($scope,$index);
    }
    $scope.lineArr = ['dotted','dashed','solid','double','']
    $scope.alignWay = ['left-ali','center-ali','right-ali']
    $scope.alignWay = ['left-ali','center-ali','right-ali']
    //分割线样式切换
    $scope.lineType = function($index){
        console.log(this)
        commonServer.lineType($scope,$index);
    }
    //标题对齐方式切换
    $scope.alignType = function($index){
        commonServer.alignType($scope,$index);
    }
    //描述对齐方式切换
    $scope.contAlign = function($index){
        commonServer.contAlign($scope,$index);
    }
    //数字
    $scope.addNum = function($index){
        commonServer.addNum($scope,$index);
    }
    //预约时段
    $scope.timeBooking = function($index){
        commonServer.timeBooking($scope,$index);
    }
    //外观形式
    $scope.faceType = function($index){
        commonServer.faceType($scope,$index);
    }
    //图片设置
    $scope.imgSet = function($index){
        commonServer.imgSet($scope,$index);
    }

    $('.num_inp').on('input',function(){
        this.value = this.value.replace(/[^\d]/g,'')
    })
   
}])
