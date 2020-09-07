$(function(){
    // 点击图片放大 by 崔源 2018.11.29
    $('.amp-img ').hide();
    $('.case-main-program-ul img').click(function(e){      
        var t1=$(window).height(); //获取屏幕高度
        var t2=$(window).width();  //获取屏幕宽度
        console.log(t1);
        console.log(t2);
        var _this = this
        var curImg = e.target || e.srcElement
        _this.imgSrc = curImg.src
        if (type==1) {
            var img='<img class="" src='+_this.imgSrc+'>'+'</img>'
            $('.amp-img').html(img);
        } else if(type==2) {
            var img='<img class="" src='+_this.imgSrc+'>'+'</img>'
            $('.amp-img').html(img);
        }else{
            var img='<img class="" src='+_this.imgSrc+'>'+'</img>'
            $('.amp-img').html(img);
        }
       $('.import-data-mask').show();
       $('.amp-img').show();
       $('.amp-img-close img').show();
       $("body").css('overflow','hidden');
    })
    $('.import-data-mask').click(function(){
        $('.amp-img').hide();
        $('.import-data-mask').hide();
        $('.amp-img-close img').hide();
        $("body").css('overflow','scroll');
    })
    $('.amp-img-close img').click(function(){
        $('.amp-img').hide();
        $('.import-data-mask').hide();
        $('.amp-img-close img').hide();
        $("body").css('overflow','scroll');
    })
})