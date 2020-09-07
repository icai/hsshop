$(function(){
    $('.custom-tag-list-side-menu-left li').click(function(){
            $('.active-left').removeClass('active-left')
            $(this).addClass('active-left')
    })
    $('.custom-tag-list-side-menu-right li').click(function(){
        $('.active-right').removeClass('active-right')
        $(this).addClass('active-right')
    })
})