require(["jquery", "bootstrap", "layer"],function(jquery, bootstrap, layer) {
    $(function() {
        /**
          * 总开关按钮点击事件
          * 1.切换开关状态
          * 2.所有子开关切换成总开关状态
          * 3.若是关闭状态 则 子模块全部隐藏
          */
        $(".switch-total label").click(function() {
            var _this = this;
            var open = $(this).attr("data-is-open");
            var is_on = open == "0" ? 1 : 0;
            $.ajax({
                url: "/merchants/member/point/updateStorePointStatus?is_on=" + is_on,
                data: {},
                type: "get",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success: function(data) {
                    //保存成功后 移除新增栏目 插入新的ul  
                    if (data.errCode == 0) {
                        if (open == "1") {
                            //切换成关闭状态
                            $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                            //子开关全部关闭
                            $(".switch-small label").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                            //显示内容
                            $("#content_box").addClass("none");
                        } else {
                            //切换成开启状态
                            $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                            //子开关全部开启
                            $(".switch-small label").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                            //隐藏内容
                            $("#content_box").removeClass("none");
                            // update at 2018/7/13 by 华亢 toDo 总开关开启时，积分消耗规则不显示
                            $(".rule-warp").removeClass("none");
                            //end
                        }
                    } else {
                        tipshow(data.errMsg);
                    }
                },
                error: function() {
                    tipshow("异常", "wram");
                }
            });
        });
        selectStorePointStatus();
        selectPointRule();
        selectPointApplyRule();
        //总开关开启状态 
        function selectStorePointStatus() {
            $.ajax({
                url: "/merchants/member/point/selectStorePointStatus",
                data: {},
                type: "get",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success: function(res) {
                    if (res.errCode == "0") {
                        if (res.data == 0) {
                            //切换成关闭状态
                            $("#z_is_on").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                            //显示内容
                            $("#content_box").addClass("none");
                        } else {
                            //切换成开启状态
                            $("#z_is_on").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                            //隐藏内容
                            $("#content_box").removeClass("none");
                        }
                    }
                },
                error: function() {
                    tipshow("异常", "wram");
                }
            });
        }
        //获取积分生产信息 
        function selectPointRule() {
            $.ajax({
                url: "/merchants/member/point/selectPointRule",
                data: {},
                type: "get",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success: function(res) {
                    if (res.errCode == 0) {
                        var data = res.data;
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].type == "consume") {
                                $("#xf_basic_rule").val(data[i].basic_rule);
                                $("#xf_id").val(data[i].id);
                                var is_on = data[i].is_on;
                                if (is_on == "0") {
                                    $("#xf_is_on").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                                    $("#xf_is_on").parent().parent().parent().find(".rule-warp").addClass("none");
                                } else {
                                    $("#xf_is_on").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                                    $("#xf_is_on").parent().parent().parent().find(".rule-warp").removeClass("none");
                                }
                                //额外奖励规则 
                                var extra_item = data[i].extra_item;
                                for (var j = 0; j < extra_item.length; j++) {
                                    var html = '<p class="mt10" data-id="' + extra_item[j].id + '"><span>一次性消费</span>\n<input type="text" value="' + extra_item[j].used_money + '" class="t-number" name="xf_ycxxf" />元,\n<span>额外送</span>\n<input type="text" value="' + extra_item[j].reward_point + '" class="t-number" name="xf_integral" />积分\n <a href="javascript:;" class="t-bule delAddRule">删除</a></p>';
                                    $("#divAddRule").append(html);
                                }
                            } else if (data[i].type == "share") {
                                $("#fx_basic_rule").val(data[i].basic_rule);
                                $("#fx_limit_rule").val(data[i].limit_rule);
                                $("#fx_id").val(data[i].id);
                                var is_on = data[i].is_on;
                                if (is_on == "0") {
                                    $("#fx_is_on").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                                    $("#fx_is_on").parent().parent().parent().find(".rule-warp").addClass("none");
                                } else {
                                    $("#fx_is_on").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                                    $("#fx_is_on").parent().parent().parent().find(".rule-warp").removeClass("none");
                                }
                            }
                        }

                    }
                },
                error: function() {
                    tipshow("异常", "wram");
                }
            });
        }

        //获取积分消耗信息
        function selectPointApplyRule() {
            $.ajax({
                url: "/merchants/member/point/selectPointApplyRule",
                data: {},
                type: "get",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success: function(res) {
                    if (res.errMsg == 0) {
                        $("#xh_id").val(res.data.id);
                        $("#xh_is_on").attr("data-is-open", res.data.is_on);
                        if (res.data.is_on == 1) {
                            $("#xh_is_on").removeClass("ui-switcher-off");
                            $("#xh_is_on").addClass("ui-switcher-on");
                            $("#xh_is_on").parent().parent().parent().find(".rule-warp").removeClass("none");
                        } else {
                            $("#xh_is_on").parent().parent().parent().find(".rule-warp").addClass("none");
                        }
                        $("#xh_percent").val(res.data.percent);
                        $("#xh_rate").val(res.data.rate);
                    }
                },
                error: function() {
                    tipshow("异常", "wram");
                }
            });
        }

        //一次性消费数值 不能输入相同的，需要前端做判断
        $("body").on("change", "input[name='xf_ycxxf']",function() {
            var 
                arr =[],
                bl = false;
            $("input[name='xf_ycxxf']").each(function(){
                var val = $(this).val();
                if(val!=""){
                    if(arr.indexOf(val)>=0){
                        $(this).val("");
                        bl = true;
                        return;
                    }else{
                        arr.push(val);
                    }
                }
            });
            if(bl){
                tipshow("请输入不同的消费金额","wram");
            }
        });

        //积分生产提交
        //       $("#footer_save1").click(function() {
        $('#footer_save1').click(function() {
            //反正多次点击
            //           var _this = $(this);
            //           $(_this).find("button").attr("disabled","disabled");  
            $('#footer_save1').attr("disabled", "disabled");
            var xf_is_on = $("#xf_is_on").attr("data-is-open");
            var xf_basic_rule = $("#xf_basic_rule").val();
            var xf_id = $("#xf_id").val();
            var fx_is_on = $("#fx_is_on").attr("data-is-open");
            var fx_basic_rule = $("#fx_basic_rule").val();
            var fx_limit_rule = $("#fx_limit_rule").val();
            var fx_id = $("#fx_id").val();
            var extra_item = [];
            var err_msg = "";
            $("input[name='xf_ycxxf']").each(function(index, el) {
                $(this).on("input propertychange",
                function() {
                    $('#footer_save1 .btn-sm').attr("disabled", false);
                });
                var obj = {};
                obj.used_money = $(el).val();
                obj.reward_point = $("input[name='xf_integral']").eq(index).val();
                $("input[name='xf_integral']").on("input propertychange",
                function() {
                    $('#footer_save1 .btn-sm').attr("disabled", false);
                });
                var id = $(el).parent().attr("data-id");
                obj.id = id ? id: 0;
                if (obj.used_money == "" || obj.reward_point == "") {
                    err_msg = "请输入额外奖励规则";
                    return;
                } else {
                    extra_item.push(obj);
                }
            });
            if (err_msg != "") {
                tipshow(err_msg, "wram");
                return;
            }
            var data = {
                data: [{
                    "type": "consume",
                    "is_on": xf_is_on,
                    "basic_rule": xf_basic_rule,
                    "id": xf_id,
                    "extra_item": extra_item
                },
                {
                    "type": "share",
                    "is_on": fx_is_on,
                    "basic_rule": fx_basic_rule,
                    "limit_rule": fx_limit_rule,
                    "id": fx_id
                }]
            };
            $.ajax({
                url: "/merchants/member/point/processPointRule",
                data: data,
                type: "post",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success: function(res) {
                    if (res.errCode == 0) {
                        tipshow("操作成功");
                        setTimeout(function() {
                            location.reload();
                        },
                        500);
                    } else {
                        tipshow(res.errMsg, "wram");
                    }
                },
                error: function() {
                    tipshow("异常", "wram");
                },
                complete: function() {
                    //                  $(_this).find("button").removeAttr("disabled");
                    $('#footer_save1 .btn-sm').removeAttr("disabled");
                }
            });
        });

        //积分消耗提交
        $("#footer_save2").click(function() {
            var _this = $(this);
            $('#footer_save2').attr("disabled", "disabled");
            var id = $("#xh_id").val();
            var is_on = $("#xh_is_on").attr("data-is-open");
            var rate = $("#xh_rate").val();
            var percent = $("#xh_percent").val();

            //何书哲 2018年11月27日 积分抵现比例最大100约束
            if (percent > 100) {
                tipshow("抵现比例最大100", "warn");
                $('#footer_save2').removeAttr("disabled");
                return false;
            }

            var data = {
                "is_on": is_on,
                "rate": rate,
                "percent": percent
            };
            if (id == "") { //新增积分消耗规则   
                $.ajax({
                    url: "/merchants/member/point/addPointApplyRule",
                    data: data,
                    type: "get",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success: function(res) {
                        if (res.errCode == 0) {
                            tipshow("操作成功");
                            $("#xh_id").val(res.data);
                            setTimeout(function() {
                                location.reload();
                            },
                            500);
                        } else {
                            tipshow(res.errMsg);
                        }
                    },
                    error: function() {
                        tipshow("异常", "wram");
                    },
                    complete: function() {
                        $(_this).find("button").removeAttr("disabled");
                    }
                });
            } else {
                data.id = id;
                $.ajax({
                    url: "/merchants/member/point/updatePointApplyRule",
                    data: data,
                    type: "get",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success: function(res) {
                        if (res.errCode == 0) {
                            tipshow("操作成功");
                            setTimeout(function() {
                                location.reload();
                            },
                            500);
                        } else {
                            tipshow(res.errMsg);
                        }
                    },
                    error: function() {
                        tipshow("异常", "wram");
                    },
                    complete: function() {
                        $(_this).find("button").removeAttr("disabled");
                    }
                });
            }
        });

        //子选项卡点击事件
        $(".switch-small label").click(function() {
            var open = $(this).attr("data-is-open");
            if (open == "1") {
                $(this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                $(this).parent().parent().parent().find(".rule-warp").addClass("none");
            } else {
                $(this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                $(this).parent().parent().parent().find(".rule-warp").removeClass("none");
            }
        });

        //选项卡切换事件
        $(".t-tab li").click(function() {
            $(this).addClass("active").siblings().removeClass("active");
            $(".content-warp").eq($(this).index()).show().siblings().hide();
            var li_index = $(this).index();
            $(".footer-warp").each(function(index, el) {
                if (li_index == index) {
                    $(el).removeClass('none');
                } else {
                    $(el).addClass('none')
                }
            });
        });

        $(".add-subtract .add").click(function(e) {
            var num_obj = $(this).parent().find(".t-number");
            var value = num_obj.val();
            num_obj.val(parseFloat(value) + 1);
        });

        $(".add-subtract .subtract").click(function(e) {
            var num_obj = $(this).parent().find(".t-number");
            var value = num_obj.val();
            if (value > 0) {
                num_obj.val(parseFloat(value) - 1);
            }
        });

        $("#btnAddRule").click(function() {
            //只能添加5条规则   
            if ($("#divAddRule p").length < 5) {
                var html = '<p class="mt10"><span>一次性消费</span>\n<input type="text" value="" class="t-number" name="xf_ycxxf" />元,\n<span>额外送</span>\n<input type="text" value="" class="t-number" name="xf_integral" />积分\n <a href="javascript:;" class="t-bule delAddRule">删除</a></p>';
                $("#divAddRule").append(html);
            } else {
                tipshow("最多设置5个额外奖励规则");
            }
        });

        //删除额外奖励规则 
        $('body').on('click', '.delAddRule',function() {
            $(this).parent().remove();
        });
    });
});