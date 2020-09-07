(function(doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function() {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth) return;
            if (clientWidth >= 750) {
                docEl.style.fontSize = '100px';
            } else {
                docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
            }
        };
    if (!doc.addEventListener) return;
    recalc();
})(document, window);

 /**
html 提示信息;
bgcolor:提示背景颜色;值为 info，warn
time:提示显示时间默认2秒;
**/
function tipshow(html,bgcolor,time){
    $(".info_tip").remove(); 
    var bgcolor = bgcolor || 'info';
    var a = arguments[2] ? arguments[2] : 2000; 
    var tipHtml = '<div class="info_tip">'+ html +'</div>';
    $('body').append(tipHtml);
    if(bgcolor == "info"){
        $('.info_tip').css('background-color','#45b182')
    }else if(bgcolor == "warn"){
        $('.info_tip').css('background-color','#ff1313')
    }
    var w = $(".info_tip").width()/2;
    if(w>450)
        $(".info_tip").css({"margin-left":-w+"px"});
    $('.info_tip').show(100);
    setTimeout(function(){
        $('.info_tip').remove();
    },a); 
}

