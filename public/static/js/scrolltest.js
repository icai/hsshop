$(function() {
    /*------- 鼠标滚轮 开始 -------*/
    var scrollFunc = function (e) {
        e = e || window.event;
        if ( e.wheelDelta ) {                                                                       // 判断浏览器IE，谷歌滑轮事件 
            if ( e.wheelDelta > 0 ) {                                                               // 当滑轮向上滚动时

            }
            if ( e.wheelDelta < 0 ) {                                                               // 当滑轮向下滚动时

            }
        } else if ( e.detail ) {                                                                    //Firefox滚轮事件
            if ( e.detail < 0 ) {                                                                   //当滑轮向上滚动时

            }
            if ( e.detail > 0 ) {                                                                   //当滑轮向下滚动时

            }
        }
    }
    
    if (document.addEventListener) {                                //给页面绑定滑轮滚动事件
        document.addEventListener('DOMMouseScroll', scrollFunc, false);
    }
    //滚动滑轮触发scrollFunc方法
    document.onmousewheel = scrollFunc;
    /*------- 鼠标滚轮 结束 -------*/
});