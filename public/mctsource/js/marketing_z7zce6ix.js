$(function () {
    function selPages(){
        this.showPage = [1]; 
        this.selPages = [];
        this.pagesData = {}; 
        this.is_multiple = 0; //是否多选 0.单选 1.多选
        this.url = ""; //请求链接
        this.create_url = ''; // 新建页面链接
        this.init = function(obj){ //初始化数据
            this.showPage =[1];
            this.selPages =[];
            this.is_multiple = obj.is_multiple || 0; 
            this.url = obj.url || "";
            this.create_url = obj.create_url || "#";
        }
        this.open = function(obj){ //打开微页面选择界面  
            var _this = this;
            _this.init(obj);
            //窗口骨架
            var el = this.createEl({el:"div",id:"myModal",class:"modal"}); 
            el.style.display ="block";
            var el1 = this.createEl({el:"div",class:"modal-dialog"}); 
            var el2 = this.createEl({el:"div",class:"modal-content"}); 
            var html = '<div class="modal-header"><button type="button" class="close">&times;</button>';
            html+='<ul class="list"><li class="js_small list_active" style="padding:0;">微页面&#X3000;</li>';
            html+=' <li class="js_manage" style="display:block;border-right:none;padding:0 0 0 14px;"><a class="co_38f" target="_blank" href="'+this.create_url+'">新建微页面</a>';    
            html+='</li></ul></div>';
            el2.innerHTML = html;
            var modal_body = this.createEl({el:"div",class:"modal-body"});
            var table = this.createEl({el:"table",class:"sel-goods-table"});
            var thead = this.createEl({el:"thead"});
            html = '<tr><th class="title"><span  class="line30">标题</span>&nbsp;&nbsp;<a class="co_38f line30 refresh" href="javascript:void(0);" >刷新</a></th>';
            html+='<th class="set_time" style="line-height: 30px;">创建时间</th><th class="search"><input type="text" /><button class="btn btn-default">搜</button></th></tr>';
            thead.innerHTML = html;
            var tbody = this.createEl({el:"tbody",class:"small"});
            table.appendChild(thead);
            tbody.style.height="386px"; 
            var data = {
                page: 1,
                title: '', //搜索内容
            }
            
            var json =  this.getDataJson(data); 
            var resultStr = this.addDom(1, json.data);
            tbody.innerHTML = resultStr;
            table.appendChild(tbody);
            modal_body.appendChild(table);
            var modal_footer = this.createEl({el:"div",class:"modal-footer clearfix"});
            html = '<span class="use-btn">确定使用</span>';
            modal_footer.innerHTML = html;
            var myModalPage = this.createEl({el:"div",class:"myModalPage"});
            //myModalPage.innerHTML = this.getPageStr(myModalPage);
            modal_footer.appendChild(myModalPage);
            el2.appendChild(modal_body);
            el2.appendChild(modal_footer);
            el1.appendChild(el2);
            var mark = this.createEl({el:"div",class:"mark"});
            el.appendChild(mark);
            el.appendChild(el1);
            document.body.appendChild(el);
            this.getPageStr(json);
            $(document).on("click", '.js-btn-default', function () { 
                var arr_data = _this.pagesData.data[$(this).attr("data-i")];
                if ($(this).hasClass('btn-primary')) {
                    $(this).removeClass('btn-primary');
                    var rindex = 0;
                    for(var i=0;i<_this.selPages.length;i++){
                        if(arr_data.id == _this.selPages[i].id){
                            rindex = i;
                            break;
                        }
                    } 
                    _this.selPages.splice(rindex,1);
                    $(this).text("选取");
                } else { 
                    if(_this.is_multiple==0){
                        $(".js-btn-default").removeClass('btn-primary').text("选取"); 
                        _this.selPages[0]=arr_data; 
                    }else{
                        _this.selPages.push(arr_data); 
                    }
                    $(this).addClass('btn-primary');   
                    $(this).text("取消");
                } 
                if (_this.selPages.length>0) {
                    $(".use-btn").show();
                } else {
                    $(".use-btn").hide();
                }
            });
            // 确定选中的微页面
            $(document).on('click', '.use-btn', function () {
                _this.unloadEvent(); 
                $("#myModal").remove();
                obj.success(_this.selPages);
            });
            $(document).on('click','.close',function(){
                _this.unloadEvent();
                $("#myModal").remove(); 
            }); 
            // 刷新
            $(document).on("click",".sel-goods-table .title .refresh",function(){   
                _this.setData(1,"");
                $(".sel-goods-table .search input[type='text']").val('');
            });
            // 搜索
            $(document).on("click", ".sel-goods-table .search .btn", function () {
                var title = $('.search input').val();
                _this.setData(1,title);
            });
        }
        this.setData = function(page,title){
            var data = {
                page: page,
                title: title //搜索内容
            }
            var json = this.getDataJson(data);
            var resultStr = this.addDom(1, json.data);
            $('.sel-goods-table tbody').html(resultStr);
            this.getPageStr(json);
        }
        this.createEl = function(obj){
            var el = document.createElement(obj.el);
            if(obj.id) {el.id = obj.id;}
            if(obj.class) {el.className=obj.class;}
            return el;
        } 
        this.getDataJson = function(data){
            var result ={},that=this;   
            var url = this.url;
            $.ajax({
                type:"get",
                url:url,
                data:data,
                async:false,//同步
                dataType:"json",
                success:function(json){
                    if(json.errCode==0){
                        that.pagesData = json;
                        result = json;
                    } 
                },
                error:function(){
                    console.log("异常");
                }
            });
            return result;
        }
        this.getPageStr = function(res){
            var _this = this;
            _this.showPage=[];
            $('.myModalPage').extendPagination({
                totalCount: res.total,//数据总数
                // showCount: res.last_page,//展示页数
                limit: res.pageSize,//每页展示条数
                callback: function (page, limit, totalCount) { 
                    var title = $(".search input").val();
                    var url = _this.url + '?page=' + page + "&title=" + title;
                    $.get(url, function (res) {
                        if (res.errCode == 0) {
                            _this.pagesData = res;
                            _this.successBase(res, title);
                            if (_this.showPage.indexOf(page) == -1) {
                                _this.showPage.push(page);
                            }
                        }
                    });
                }
            });
        }
        this.addDom = function(page, data) { 
            var resultStr ="";
            for (var i = 0; i < data.length; i++) {
                resultStr += '<tr data-id=' + data[i].id + ' class=page' + page + '>\<td>';
                data[i].page_title && (resultStr += '<a class="co_38f js-goods-title" href="javascript:void(0);">' + data[i].page_title);
                data[i].title && (resultStr += '<a class="co_38f js-goods-title" href="javascript:void(0);">' + data[i].title);
                data[i].create_time && (resultStr += '</a>\</td>\<td>' + data[i].create_time);
                data[i].created_at && (resultStr += '</a>\</td>\<td>' + data[i].created_at);
                resultStr += '</td>\<td><button data-i="'+i+'" class="btn btn-default js-btn-default">选取</button></td>\</tr>';
            }
            return resultStr;
        }
        this.successBase = function(res, title) {//交互成功后执行的基础方法 用于分页
            var page = res.currentPage;
            var current_page = res.currentPage;
            var data = res.data;
            $("#myModal .small tr").addClass("hide");
            var resultStr = this.addDom(page, data, title);
            $("#myModal .small").append(resultStr);
        }
        this.unloadEvent = function(){
            $(document).off('click', '.use-btn');
            $(document).off('click', '.close');
            $(document).off('click', '.sel-goods-table .title .refresh');
            $(document).off('click', '.sel-goods-table .search .btn');
            $(document).off('click', '.js-btn-default');
        }
    }
    var selPages = new selPages();
    
    $('body').on('click','.js-chooseLit',function(){
        selPages.open({
            url: '/merchants/xcx/micropage/select',
            create_url: '/merchants/marketing/liteAddPage',
            success:function(res){
                if (res.length>0){
                    litUrl = res[0].id;
                    $('.dropdown-warp .js-link-lit').text(res[0].title);
                    $('.dropdown-warp .js-chooseLit').addClass('hide');
                    $('.dropdown-warp .js-link-lit-box').removeClass('hide');
                    var value = weiUrl + ',' + litUrl;
                    $("#link_id").val(value);
                }
            }
        })
    })
    $('body').on('click','.js-chooseWei',function(){
        selPages.open({
            url: '/merchants/store/selectPage',
            create_url: '/merchants/store/showMicroPage/create/7',
            success:function(res){
                if (res.length>0){
                    weiUrl = res[0].id;
                    $('.dropdown-warp .js-link-wei').text(res[0].page_title);
                    $('.dropdown-warp .js-chooseWei').addClass('hide');
                    $('.dropdown-warp .js-link-wei-box').removeClass('hide');
                    var value = weiUrl + ',' + litUrl;
                    $("#link_id").val(value);
                }
            }
        })
    })
    $('body').on('click','.js-delete-wei',function(){
        $('.dropdown-warp .js-link-wei').text('');
        $('.dropdown-warp .js-chooseWei').removeClass('hide');
        $('.dropdown-warp .js-link-wei-box').addClass('hide');
        weiUrl = '';
        var value = weiUrl + ',' + litUrl;
        $("#link_id").val(value);
    })
    $('body').on('click','.js-delete-lit',function(){
        $('.dropdown-warp .js-link-lit').text('');
        $('.dropdown-warp .js-chooseLit').removeClass('hide');
        $('.dropdown-warp .js-link-lit-box').addClass('hide');
        litUrl = '';
        var value = weiUrl + ',' + litUrl;
        $("#link_id").val(value);
        
    })
    
    /**
     * 新增功能(立即使用跳转到指定页面功能)
     * @author  txw 
     * @date  2017/9/21
     */ 
     //选择链接类型
    $("body").on('click','.dropdown-menu li',function(){
        var type = $(this).attr("data-type");
        switch(type){
            case "goods":
                $("#link_type").val("1");
                var iszd = $("input[name='range_type']:checked").val();
                var ids = "";
                if(iszd==1){
                    $(".appoint_module .checked").each(function(){
                        ids+=$(this).attr("data-id")+',';
                    });
                    if(ids!=""){
                        ids = ids.substr(0,ids.length-1);
                    }else{
                        tipshow("请先添加商品","wran");
                        return;
                    }
                }
                selGoods.open({
                    ids:ids,
                    success:function(res){
                        if(res.length>0){
                            setLinkWarpContent(res[0].title,res[0].id,1);
                        } 
                    }
                });
                break;
            case "homepage":  
                setLinkWarpContent("店铺主页",0,0);
                break;
            case "goodsList":  
                setLinkWarpContent("商品列表",0,2);
                break;
            case "autoLink":  
                $("#link_type").val(3);
                $(".dropdown-warp .sel_warp").addClass('hide');
                $(".dropdown-warp .autoWrap").removeClass('hide');
                $('.dropdown-warp .js-chooseLit').removeClass('hide');
                $('.dropdown-warp .js-chooseWei').removeClass('hide');
                $('.dropdown-warp .js-link-lit-box').addClass('hide');
                $('.dropdown-warp .js-link-wei-box').addClass('hide');
                break;
        }
    });
    //设置链接容器内容
    function setLinkWarpContent(text,id,type){
        $("#link_type").val(type);
        $(".dropdown-warp .link_warp").removeClass('hide');
        $(".dropdown-warp .link_style").html(text);
        $("#link_id").val(id);
        $(".dropdown-warp .sel_warp").addClass('hide');
    }

    //删除链接 
    $("body").on("click",".dropdown-warp .js_close_link",function(){
        $(".dropdown-warp .sel_warp").removeClass('hide');
        $(".dropdown-warp .link_warp").addClass('hide');
        $("#link_id").val('');
        $("#link_type").val('');
    });
    //删除链接 
    $("body").on("click",".dropdown-warp .js_close_auto_link",function(){
        $(".dropdown-warp .sel_warp").removeClass('hide');
        $(".dropdown-warp .autoWrap").addClass('hide');
        $("#link_id").val('');
        $("#link_type").val('');
    });

    $('#addCouponForm').bootstrapValidator({
        message: '不能为空', // 设置默认提示语
        trigger: 'blur', // 设置验证默认触发事件(失焦时验证)
        // excluded:[],//只对禁用域不进行验证         
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: { // 验证
            title: { // 活动名称
                validators: {
                    notEmpty: {
                        message: '名称不能为空',
                    },
                    stringLength: {
                        max: 10,
                    },
                    stringLength: {
                        min: 1,
                    },
                }
            },
            total: { // 发行量
                validators: {
                    notEmpty: {
                        message: '发行量不能为空',
                    },
                    integer: {
                        'default': '请输入有效的整数值'
                    },
                    greaterThan: {
                        value: 1
                    },
                }
            },
            amount: { // 面值下限
                validators: {
                    notEmpty: {
                        message: '面值不能为空',
                    },
                    numeric: {
                        'default': '请输入有效的面值，可以是小数'
                    },
                    greaterThan: {
                        value: 0.01
                    },
                    lessThan: {
                        value: 10000
                    },
                }
            },
            value_random_to: { // 面值上限
                validators: {
                    notEmpty: {
                        message: '面值不能为空',
                    },
                    numeric: {
                        'default': '优惠券面值范围的上限必须大于下限'
                    },
                    greaterThan: {
                        value: 'values',
                        message: '优惠券面值范围的上限必须大于下限',
                    },
                    lessThan: {
                        value: 10000
                    },
                }
            },
            limit_amount: { //使用门槛
                enabled: false,
                validators: {
                    notEmpty: {},

                    numeric: {
                        'default': '请输入有效的面值，可以是小数'
                    },
                    greaterThan: {
                        value: 'values', // 关联两个控件
                        message: '订单限制金额必须大于等于优惠券的面值',
                    },
                }
            },
            color: { // 颜色
                validators: {
                    notEmpty: {
                        'message': '颜色不能为空'
                    }
                }
            },
            weixin_title: { // 卡券标题
                validators: {
                    notEmpty: {
                        'message': '卡券标题不能为空',
                    }
                }
            },
            weixin_subtitle: { // 卡券副标题
                validators: {
                    notEmpty: {
                        'message': '卡券副标题不能为空',
                    }
                }
            },
            quota: {
                validators: { // 每人限领
                    notEmpty: {
                        'message': '每人限领',
                    }
                }
            },
            start_at: { // 开始时间
                validators: {
                    notEmpty: {
                        'message': '生效时间不能为空',
                    },
                },
                trigger: 'blur'// 设置验证默认触发事件(失焦时验证) 
            },
            end_at: { // 过期时间
                validators: {
                    notEmpty: {
                        'message': '过期时间不能为空',
                    },
                },
                trigger: 'blur' // 设置验证默认触发事件(失焦时验证)
            },
            
        }
    });

     /**
     * 图片选择后的回调函数
     */
    selImgCallBack = function(resultSrc){ 
        if(resultSrc.length>0){
            //2018.10.16 优惠券分享页图片尺寸限制
            var num = parseInt(resultSrc[0].imgWidth / resultSrc[0].imgHeight * 100) / 100
            var sum = parseInt(750 / 750 * 100) / 100
            if( num < sum - 0.2 || num > sum + 0.2){
                tipshow('图片比例非1:1，请重新上传','warm');
                return false;
            }
            if(parseInt(resultSrc[0].imgWidth)<400){
                tipshow('图片尺寸小于400px，请重新上传','warm');
                return false;
            }
            $("input[name='share_img']").val(resultSrc[0].imgSrc);
            $("#img_share_img").attr("src",_host+resultSrc[0].imgSrc).parent().removeClass('hide');
            $(".js-add-picture").html("修改图片").removeClass("add-goods");
        } 
    }
    
    // 删除分享图片
    $("body").on("click",".share_img_close",function(){
        $("input[name='share_img']").val('');
        $(this).parent().addClass('hide');
        $(".js-add-picture").html("+添加图片").addClass("add-goods");
    });
    
    $(".js-add-picture").click(function(){
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['880px', '715px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
    }); 

    // 验证

    var range_value;   //指定商品
    var host = window.location.host;
    $('.submit_btn').click(function (e) {
        var _this = this;
        e.preventDefault();
        $(_this).attr("disabled","disabled");
        $('#addCouponForm').bootstrapValidator('validate');
        if (!$("#addCouponForm").data('bootstrapValidator').isValid()) {
            $(_this).removeAttr("disabled"); 
            return false;
        } 
        var share_title = $('input[name="share_title"]').val();
        var share_desc = $('textarea[name="share_desc"]').val();
        var share_img = $('input[name="share_img"]').val();
        if((share_title == '' && share_desc  == '' && share_img == '') || (share_title != '' && share_desc  != '' && share_img != '')){
            if ($(".goods_appoint").is(":checked")) { //是否选中指定商品
                range_value = [];
                var couponId = $("#couponId").val();
                if ($(".appoint_module table a").length == 0) {
                    $(".error").show();
                    $(_this).removeAttr("disabled");
                    return false;
                } else {
                    for (var i = 0; i < $(".appoint_module table").find(".checked").length; i++) {
                        range_value.push($($(".appoint_module table").find(".checked")[i]).attr("data-id"));
                    }
                    range_value = range_value.toString();
                    if (isEditConpon) {
                        postAdit(range_value, couponId); 
                        return;
                    } else {
                        postAdit(range_value, "");
                        return;
                    }

                }
            }
            if($(".help-block1").length>0){
                tipshow("请完善信息","warn");
                $(_this).removeAttr("disabled");
                return;
            }
            if ($("#link_type").val() == 3) {
                if ($('.js-link-lit').text()=='' && $('.js-link-wei').text()=='') {
                    tipshow("请至少选择一个跳转链接","warn");
                    $(_this).removeAttr("disabled");
                    return;
                }
            }
            if (isEditConpon) {
                var couponId = $("#couponId").val();
                post(couponId);
                return;
            } else {
                post("");
            }
            function post(couponId) {
                $.post("?merchants/marketing/coupon/set/" + couponId, $("form").serialize(), function (res) {
                    mesg(res);
                })
            }
            function mesg(res) {
                if (res.status == 1) {
                    tipshow("保存成功", "info", 1000);
                    window.location.href = "http://" + host + "/merchants/marketing/coupons/all/";
                }else{
                    tipshow(res.info, "warn");
                    $(_this).removeAttr("disabled");
                }
            }
            function postAdit(range_value, couponId) {
                $.post("?merchants/marketing/coupon/set/" + couponId, ($("form").serialize() + "&range_value=" + range_value), function (res) {
                    mesg(res);
                    window.location.href = "http://" + host + "/merchants/marketing/coupons/all/";
                    return;
                })
            }
        }else{
           
            if(!share_img && share_title && share_desc){
                tipshow("请填写分享图片","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
            if(!share_title && share_img && share_desc){
                tipshow("请填写分享标题","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
            if(!share_desc && share_title && share_img){
                tipshow("请填写分享内容","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
            if(share_img){
                tipshow("请填写分享标题及内容","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
            if(share_title){
                tipshow("请填写分享内容及图片","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
            if(share_desc){
                tipshow("请填写分享标题及图片","warn");
                $(_this).removeAttr("disabled");
                return false;
            }
        } 
    });
 
    

    var start = {
      elem: '#startTime',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: laydate.now(), //设定最小日期为当前日期
      max: '2099-06-16 23:59:59', //最大日期
      istime: true,
      istoday: false,
      choose: function(datas){
        $('#startTime').val(datas);
        $('#startTime').focus();
        $('#startTime').blur();
         end.min = datas; //开始日选好后，重置结束日的最小日期
         end.start = datas //将结束日的初始值设定为开始日
      }
    };
    var end = {
      elem: '#endTime',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: laydate.now(),
      max: '2099-06-16 23:59:59',
      istime: true,
      istoday: false,
      choose: function(datas){
        $('#endTime').val(datas);
        $('#endTime').focus();
        $('#endTime').blur();
        start.max = datas; //结束日选好后，重置开始日的最大日期
      }
    };
    laydate(start);
    laydate(end);
   
    /* 交互效果 */
    // 优惠券名称
    $('.js_coupons_name').blur(function () {
        $('.coupons_title').text($(this).val());
    });
    //  随机
    $('.js_random_btn').click(function () {
        
        if ($(this).prop('checked')) { // 选中
            $('.js_random').removeClass('no'); // 随机上线显示
        } else { // 取消随机
            $('.js_random').addClass('no'); // 随机上线隐藏
        }
    });

    // 面值下线设置
    $('.js_lowerLimit').blur(function () {
        if(isEditConpon){
            return false;
        }
        //update by 魏冬冬 2018-6-28 解决显示NAN的问题
        var _lowerVal = $(this).val() ? parseFloat($(this).val()).toFixed(2): ''; // 下限值
        //end
        var _upperval = $('.js_upperLimit').val(); // 上限值
        var _html = '￥' + _lowerVal;

        if (_upperval) { // 如果存在下限值
            if (_lowerVal > _upperval && $('.js_random_btn').prop('checked')) {
                
                tipshow('优惠券面值范围的上限必须大于下限!', 'warn');
                $('.coupons_denomination').text('￥ 0.00');
                $('.js_lowerLimit').val(''); // 清空下限
                // 从新验证下限
                $('.edit_form').data('bootstrapValidator').updateStatus('values', 'NOT_VALIDATED', null)
                    .validateField('values');
                return; // 结束程序
            } else {
                _html += '~' + parseFloat(_upperval).toFixed(2);
            }
        }
        $('.coupons_denomination').text(_html);
        if($(".use_limit").is(":checked")){
            setMesg()
        }
    });

    // 面值上线设置
    $('.js_upperLimit').blur(function () {
        var _lowerVal = parseFloat($('.js_lowerLimit').val()); // 下限值
        //update by 魏冬冬 2018-6-28 解决显示NAN的问题
        var _upperval = $(this).val() ? parseFloat($(this).val()).toFixed(2) : ''; // 上限值
        //end
        if (!_lowerVal) {
            _lowerVal = '0.00';
        }
        var _html = '￥' + parseFloat(_lowerVal).toFixed(2);
        if (!_upperval) {
            layer.msg('优惠券面值范围必须大于等于 0.01 元', {
                skin: 'lose_tip',
                offset: '40px',
                time: 2000
            });
            $(this).val(''); // 清空上限
            $('.edit_form').data('bootstrapValidator').updateStatus('value_random_to', 'NOT_VALIDATED', null)
                .validateField('value_random_to');
            return;
        } else {
            _html += '~' + _upperval;
        }
        $('.coupons_denomination').text(_html);
    });

    // 使用微信
    $('.is_sync_weixin').click(function () {
        if ($(this).prop('checked')) { // 同步到微信
            $('.weixin_group,.weixin_set').removeClass('no');
        } else { // 取消同步到微信
            $('.weixin_group,.weixin_set').addClass('no');
        }
    });



    // 微信标题
    $('.js_weixin_title').blur(function () {
        var _val = $(this).val();
        var _html = '';
        if (_val) {
            _html += _val;
        } else {
            _html += '微商城';
        }
        $('.card_name').text(_html);
    });

    // 微信副标题
    $('.js_sub_title').blur(function () {
        var _val = $(this).val(),
            _html = '';
        if (_val) {
            _html += _val;
        } else {
            _html += '微信卡券标题';
        }
        $('.card_limit').text(_html);
    });


    // 可使用商品
    $('.goods_range').click(function () {
        var _objTip = $('.' + $(this).data('tip')); // 对应的提示对象
        _objTip.removeClass('no').siblings('.tip_des').addClass('no'); // 对应的提示显示，其他的提示隐藏
        $('.appoint_module').addClass('no'); // 指定商品隐藏
        /**
         * @author huoguanghui
         * 选中指定商品 清除立即指定跳转
         */
        //清楚内容
        $(".dropdown-warp .sel_warp").removeClass('hide');
        $(".dropdown-warp .link_warp").addClass('hide');
        $("#link_id").val('');
        $("#link_type").val('');
        //显示商品列表
        $(".sel_warp .goodsList").addClass('no');
    });

    // 指定商品
    $('.goods_appoint').click(function () {
        $('.appoint_module').removeClass('no'); // 指定商品显示
        /**
         * @author huoguanghui
         * 选中指定商品 清除立即指定跳转
         */
        //清楚内容
        $(".dropdown-warp .sel_warp").removeClass('hide');
        $(".dropdown-warp .link_warp").addClass('hide');
        $("#link_id").val('');
        $("#link_type").val('');
        //隐藏商品列表
        $(".sel_warp .goodsList").removeClass('no');
    });

    // 添加商品的弹框显示

    var _product = 1;//默认已上架商品
    $('.js_add_goods').click(function () {  
        //新代码
        var href = _host+"merchants/product/create"; 
        selGoods.open({success:callback,href:href,is_multiple:1,postData:{filter_negotiable:1}});
    });

    //选择商品回调函数
    function callback(data){
        var _html ="";
        for(var i=0;i<data.length;i++){
            var is_select = false; //false 商品未选， true商品已选
            $(".appoint_module table tbody .checked").each(function(){
                var id = $(this).attr("data-id");
                if(id==data[i].id){//判断商品是否已选
                    is_select = true;
                    return false;
                }
            });
            if(!is_select){ 
                _html += ' <tr>';
                _html += ' <td><a class="checked" href="javascript:;" data-id="'+data[i].id+'">'+data[i].title+'</a></td>';
                _html += ' <td><a class="del_goods blue_38f f12" href="javascript:void(0);">删除</a></td>';
                _html += ' </tr>';
            }
            
        }
        $('.appoint_module table tbody').append(_html);
    }

    // 删除商品
    $('body').on('click', '.del_goods', function () {
        // if(isEditConpon){
        //     return;
        // }
        $(this).parents('tr').remove();
    });


    // input设为不可用
    var url = window.location.href;
    var arr = url.split("/");
    var isEditConpon = false;
    if (url[url.length - 1] != "/") {

        if (arr[arr.length - 1] != "set") {
            isEditConpon = true;
//          setInputDisable()     微信卡券无法设置
        }
    } else {
        if (arr[arr.length - 2] != "set") {
            isEditConpon = true;
            setInputDisable()
        }
    }

    // 设置input不可选
    function setInputDisable() {
        $("form input").attr("readonly", "true");
        $("form select").attr({
            "onfocus": "this.defaultIndex=this.selectedIndex;",
            "onchange": "this.selectedIndex=this.defaultIndex;"
        });
        $("input[name='title'], input[name='total'], input[name='expire_remind'], input[name='is_share']").removeAttr("readonly");
        $("input[name='is_sync_weixin']").attr("onclick", "return false");
        $("input[name='is_sync_weixin']").unbind();
        if ($(".unlimited").is(":checked")) {   //使用门槛是否选中
            $(".use_limit").attr("disabled", "disabled");
        } else {
            $(".unlimited").attr("disabled", "disabled");
        }
        if ($(".goods_range").is(":checked")) {   //指定商品是否选中
            $(".goods_appoint").attr("disabled", "disabled");

        } else {
            $(".js_add_goods").hide();
            $(".goods_range").attr("disabled", "disabled");
        }
        $(".js_random_btn").attr("onclick", "return false");
    }



    // 不设置门槛
    $(".no_use").click(function () {
        $(".help-block1").remove();
        $(".coupons_limit").html("不限制");
    })

    // 设置门槛
    $(".use_limit").click(function () {
        setMesg();
    })
    //设置使用门槛后显示在页面上的信息
    function setMesg() {
        var pre = $("input[name='limit_amount']").val() || 0;
        var price = $("input[name='amount']").val() || 0;
        var html = '<span class="help-block1" style="color:#a94442;text-align:center;display:block">';
        html += '订单限制金额必须大于等于优惠券的面值</span>';
        if ($(".help-block1").length == 1) { return; }
        if (parseFloat(pre) < parseFloat(price)) {
            $(".coupons_limit").html("订单满xx元(含运费)");
            $("input[name='limit_amount']").parent().parent().parent().after(html);
        } else {
            // 当值为0时
            if (pre && pre != "0") {
                price = pre

            } else {
                price = "xx";
            }
            $(".coupons_limit").html("订单满" + price + "元(含运费)");
            $(".help-block1").remove();
        }
    }
    $(".js_limit").change(function(){
        $(".help-block1").remove();
        if($(".use_limit").is(":checked")){
            setMesg()
        }
    });

    // 观察面值是否小于门槛
    function lt() {
        if ($(".use_limit").is(":checked")) {
            var price = $("input[name='limit_amount']").val() || 0;
            if ($("input[name='amount']").val() > price) {
                if ($(".help-block1")) {
                    return;
                }
                $("input[name='limit_amount']").parent().parent().parent().after(html);;
            } else {
                $(".help-block1").remove();
            }
        }
    }
    $("input[name='amount']").blur(function () {
        lt();

        if ($(".use_limit").is(":checked")) {
            setMesg();
        }
    })
    $("input[name='limit_amount']").blur(function () {
        lt();
        if ($(".use_limit").is(":checked")) {
            setMesg();
        }
    })


   
	
//	微信卡券颜色
	var classArr = ["Color010","Color020","Color030","Color040","Color050","Color060","Color070","Color080","Color081",
    "Color082","Color090","Color100","Color101","Color102"]
    var html = '';
    for(var i = 0;i < classArr.length;i ++){
        html += '<li class="'+classArr[i]+'"></li>'        
    }
    $('.bgColor_cap').append(html);
    $('.controls').hover(function() {
        $('.bgColor_cap').show();
    },function() {
        $('.bgColor_cap').hide();
    });
    //选择背景颜色
    $(document).on('click','.bgColor_cap li',function(){    	
        $('.bgColor').attr('class','bgColor');
        $('.bgColor').addClass($(this).attr('class'));
        var color_num = $(this).attr('class');
        $(".color-num").val(color_num);
        $('.card_module').attr('class','card_module');
        $('.card_module').addClass(color_num);
        $('.card_module').css('background-color',$(this).css('background-color'))
        $('.bgColor_cap').hide();
        $('input[name="bg_color"]').val($(this).attr('class'));
    });

    // 分享加图
    $('#file').on('change', function(){
        var reader = new FileReader();
        reader.readAsDataURL(this.files[0]);
        if(this.files[0].size > 102400){
            tipshow("图片不能超过100K","warn");
            return;
        }
        reader.onload = function(e){
            $('.share_img').attr('src',this.result);
            $('.share_img').show();
        }
        var formData = new FormData();
        formData.append("file", document.getElementById('file').files[0]);
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
                res = JSON.parse(res);
                logo = res.data.FileInfo['path'];
                $('input[name="share_img"]').val(logo);
            },
            error:function(){

            }
        })
    });	

    /**
     * 有效期选择
     * @author  huoguanghui
     * @date 2017年11月14日10:44:21
     */
    // 有效期规则选择事件
    $("input[name='expire_type']").on("click",function(){
        $("input[name='expire_days']").val("");//初始化数据
        // $('#addCouponForm').data("bootstrapValidator").resetForm();//重置表单
        $('#addCouponForm').data("bootstrapValidator").updateStatus("start_at",  "NOT_VALIDATED",  null );
        $('#addCouponForm').data("bootstrapValidator").updateStatus("end_at",  "NOT_VALIDATED",  null );
        selectInput();
    })
    //只能选择数字
    $(document).on("keypress",".expire_days",function(e){
        var key = window.event ? e.keyCode : e.which;
        // console.log(key)
        if((48<=key && key<=57) || key == 8){
            var num = parseFloat($(this).val()+e.key);
            if(num > 365 || num == 0){
                e.preventDefault();
            }
            $("input[name='expire_days']").val($(this).val());
        }else{
            e.preventDefault();
        }
    })
    //选择有效期
    selectInput();
    function selectInput(){
        switch ($("input[name='expire_type']:checked").val()) {
            case "0":
                //0
                $(".startTime").removeClass('no');
                $(".endTime").removeClass('no');
                //1
                $(".second .expire_days").addClass('no');
                $(".second .info").addClass('no');
                $(".second .number").removeClass('no');
                $(".second .expire_days").remove();
                //2
                $(".third .expire_days").addClass('no');
                $(".third .info").addClass('no');
                $(".third .number").removeClass('no');
                $(".third .expire_days").remove();
                break;
            case "1":
                //0
                $(".startTime").addClass('no');
                $(".endTime").addClass('no');
                //1
                $(".second .expire_days").removeClass('no');
                $(".second .info").removeClass('no');
                $(".second .number").addClass('no');
                $(".second .expire_days").remove();
                $(".second .number").after('<input class="expire_days" type="text"  name="expire_days" value="'+$("input[name='expire_type']:checked").data("days")+'" />');//解决jq交互传递两个expire_days属性
                //2
                $(".third .expire_days").addClass('no');
                $(".third .info").addClass('no');
                $(".third .number").removeClass('no');
                $(".third .expire_days").remove();
                break;
            case "2":
                //0
                $(".startTime").addClass('no');
                $(".endTime").addClass('no');
                //1
                $(".second .expire_days").addClass('no');
                $(".second .info").addClass('no');
                $(".second .number").removeClass('no');
                $(".second .expire_days").remove();
                
                //2
                $(".third .expire_days").removeClass('no');
                $(".third .info").removeClass('no');
                $(".third .number").addClass('no');
                $(".third .expire_days").remove();
                $(".third .number").after('<input class="expire_days" type="text" name="expire_days" value="'+$("input[name='expire_type']:checked").data("days")+'" />');
                break;
            default:
                // statements_def
                break;
        }
    }
   
})
