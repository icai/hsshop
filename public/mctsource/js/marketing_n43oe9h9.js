$(function () {
    // 开始时间
    var start = {
        elem: '#start_date',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2099-06-16 23:59:59', //最大日期
        istime: true,
        istoday: false,
    };
    laydate(start);
    // 点击出来拍卖物品
    function domDisplayNone(dom) {
        $(dom).css("display", "none")
    }
    function domDisplayBlock(dom) {
        $(dom).css("display", "block")
    }
    $(".click-none").click(function () {
        domDisplayBlock(".auction-details")
        domDisplayNone(".widget-list-filter")
        domDisplayBlock('.fixed-bottom')
    })

    // 导航切换
    $('.tab_nav a').click(function () {
        domDisplayBlock(".widget-list-filter")
        domDisplayNone('.fixed-bottom')
        domDisplayNone(".auction-details")
    })

    // 时间验证
    function timeTdClick() {
        $("td").click(function () {
            if (!$(this).hasClass("laydate_void")) {
                $(".start-date").removeClass("has-error")

            }
        })
    }

    $("#start_date").click(function () {
        timeTdClick()
    })

    // 计算持续时间
    $(".calc-start").click(function () {
        var startPrice = $("input[name='startPrice']").val()
        var endPrice   = $("input[name='endPrice']").val()
        var startDate  = $("input[name='startDate']").val()
        var anyMinute  = $("input[name='minute']").val()
        var anyMinuteDownMoney = $("input[name='downMoney']").val()
        var stillMinute = Math.ceil((startPrice - endPrice) / anyMinuteDownMoney * anyMinute)
        //页面上显示的时间 
        var viewMinute;
        var viewHour;
        var viewDay;
        var viewMonth;
        var viewYear;
        if (stillMinute <= 60) {
            $(".calc-time").html("约为" + stillMinute + "分钟")
            isCalcTime(startDate, 0, 0, 0, 0, stillMinute)
        } else {
            viewHour  = Math.floor(stillMinute / 60)
            viewDay   = Math.floor(viewHour / 24)
            viewMonth = Math.floor(viewDay / 30)
            viewYear  = Math.floor(viewMonth / 12)
            var stillHour = viewHour % 24
            var stillDay  = viewDay % 30
            stillMinute   = stillMinute % 60
            isCalcTime(startDate, viewYear, viewMonth, viewDay, viewHour, stillMinute)
            if (stillHour >= 0) { $(".calc-time").html("约为" + stillHour + "小时" + stillMinute + "分钟") }
            if (stillDay >= 0) { $(".calc-time").html("约为" + stillDay + "天" + stillHour + "小时" + stillMinute + "分钟") }
        }
    })
    // 是否计算时间
    function isCalcTime(startDate, viewYear, viewMonth, viewDay, viewHour, viewMinute) {
        if (startDate) {
            calcTime(startDate, viewYear, viewMonth, viewDay, viewHour, viewMinute)
        }
        else {
            return
        }
    }
    // 计算时间
    function calcTime(startDate, viewYear, viewMonth, viewDay, viewHour, viewMinute) {
        var year   = parseInt(startDate.substring(0, 4))
        var month  = parseInt(startDate.substring(5, 7))
        var day    = parseInt(startDate.substring(8, 10))
        var hour   = parseInt(startDate.substring(11, 13))
        var minute = parseInt(startDate.substring(14, 16))
        var second = parseInt(startDate.substring(17, 19))
        if (viewYear == 0 && viewMonth == 0 && viewDay == 0 && viewHour == 0) {
            viewMinute += minute
            if (viewMinute >= 60) {
                viewMinute = viewMinute % 60
                hour += 1
            }
            if (hour == 24) { hour = 0; day += 1 }
            $(".end-time").html("预计" + year + "/" + month + "/" + day + " " + hour + ":" + viewMinute + ":" + second + "结束")
        } else {
            // 在页面上显示
            viewMinute += minute
            if (viewMinute >= 60) {
                viewMinute = viewMinute % 60
                hour += 1
            }
            if (hour == 24) { hour = 0; day += 1 }
            viewHour += hour
            viewHour = viewHour % 24
            if (viewHour > 12) {
                viewHour = "下午" + (viewHour - 12)
            } else {
                viewHour = "上午" + viewHour
            }
            viewDay += day
            viewDay = viewDay % 30
            if (Math.floor(viewDay / 30) > 1) {
                viewMonth += Math.floor(viewDay / 30)
            }
            if (viewDay == 0) {
                viewDay = 30
            }
            viewMonth += month
            if (viewMonth / 12 > 1) {
                viewYear += Math.floor(viewMonth / 12)
            }
            viewYear += year
            viewMonth = viewMonth % 12
            if (viewMonth == 0) { viewMonth = 12 }
            $(".end-time").html("预计" + viewYear + "/" + viewMonth + "/" + viewDay + viewHour + ":" + viewMinute + ":" + second + "结束")
        }
    }
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

    $(".modalBtn").each(function () {
        $(this).click(function () {
            var imgSrc = $(this).parent().parent().find("img").attr("src")
            $(".add_img").css("display", "block")
            $(".add_img img").attr("src", imgSrc)
            $(".img").css("display", "none")
            $(".main-img").css("height", "320px")
            $(".main-img").html("<img src=" + imgSrc + " class='addImg'>")
            $('#myModal').modal('hide')
            delImg()
        })
    })
    var delImg = function () {
        $(".delImg").on("click", function () {
            $(".add_img").css("display", "none")
            $(".img").css("display", "block")
            $(".main-img").html("拍卖商品主图")
            $(".main-img").css("height", "200px")
        })
    }

    $(".btn-no").click(function () {
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
                if (startTime) {
                    alert("通过验证")
                    $(".successPromrt").css("display", "block")
                    setTimeout(function () {
                        $(".successPromrt").css("display", "none")
                    }, 3000);
                } else {
                    $(".start-date").addClass("has-error")
                    alert("没有通过验证")
                }
            }
            else {
                // 如果没通过验证给input加上错误提示
                alert('表单没有通过验证');
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
