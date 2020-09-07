$(function () {

    function domDisplayNone(dom) {
        $(dom).css("display", "none")
    }
    function domDisplayBlock(dom) {
        $(dom).css("display", "block")
    }
    // 导航切换
    $(".click").click(function () {
        domDisplayNone(".click-none")
        domDisplayBlock(".set-order")
        domDisplayBlock(".fixed-bottom")
    })
    $('.tab_nav a').click(function () {
        domDisplayBlock(".click-none")
        domDisplayNone(".fixed-bottom")
        domDisplayNone(".set-order")
    })
    // 开始时间
    var start = {
        elem: '#start_date',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
        choose: function (datas) {
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    // 结束时间
    var end = {
        elem: '#end_date',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(),
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function (datas) {
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };
    laydate(start);
    laydate(end);
    /* 手动选择时间 */

    // 切换返现方式
    $(".click-random").click(function () {
        domDisplayBlock(".random")
    })
    $(".click-fixed").click(function () {
        domDisplayNone(".random")
    })

    // 时间验证
    function timeTdClick() {
        $("td").click(function () {
            if (!$(this).hasClass("laydate_void")) {
                $(".start-date").removeClass("has-error")
                $(".end-date").removeClass("has-error")
            }
        })
    }

    $("#start_date").click(function () {
        timeTdClick()
    })

    $("#end_date").click(function () {
        timeTdClick()
    })

    function judgeInputIsEmpty() {
        var arr = []
        $(".val").each(function () {
            if ($.trim($(this).val()) == "") {
                $(this).parent().addClass("has-error")
                return
            } else {
                arr.push("true")
            }
        })
        return arr
    }
    $(".val").each(function () {
        $(this).change(function () {
            $(this).parent().removeClass("has-error")
        })
    })
    $(".btn-yes").click(function () {
        judgeInputIsEmpty()
    })
     $(".btn-no").click(function(){
        window.location.reload();//刷新当前页面.
    })
})

// angular
angular.module('app', [])
    .controller('testController', function ($scope, timeValid) {
        //获取到表单是否验证通过
        $scope.save = function () {
            if ($scope.myForm.$valid) {
                // 表单通过验证
                var startTime = timeValid("#start_date", ".start-date")
                var endTime = timeValid("#end_date", ".end-date")
                if (startTime && endTime) {
                    $(".successPromrt").css("display","block")
                    setTimeout(function () {
                        $(".successPromrt").css("display","none")
                    }, 3000);
                } else {
                    $(".start-date").addClass("has-error")
                    $(".end-date").addClass("has-error")
                    alert("没有通过验证")
                }
            }
            else {
                // 如果没通过验证给input加上错误提示
                var startTime = timeValid("#start_date", ".start-date")
                var endTime = timeValid("#end_date", ".end-date")
                if (! startTime && endTime) {
                    $(".start-date").addClass("has-error")
                    $(".end-date").addClass("has-error")
                } 
            }

        }
    })
    .service("timeValid", function () {
        function timeValid(inputValDom, addClassDom) {
            if ($.trim($(inputValDom).val()) == "") {
                $(addClassDom).addClass("has-error")
                return false
            } else {
                return true
            }
        }
        return timeValid
    })

