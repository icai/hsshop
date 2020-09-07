$(function () {
    var swiper = new Swiper('#swiper_APP', {
        pagination: '#swiper_APP .swiper-pagination',
        loop: true,
        autoplay: 3000,
        slideShadows: true
    });
    $('.application-box').on('click', '.application-item', function () {
        if ($(this).children('.arrow-icon').hasClass('arrow-icon-active')) {
            $(this).children('.arrow-icon').removeClass('arrow-icon-active');
            $(this).siblings('ul').addClass('none');
        } else {
            $(this).children('.arrow-icon').addClass('arrow-icon-active');
            $(this).siblings('ul').removeClass('none');
        }
    });
    var swiper2 = new Swiper('.service-slider .swiper-container', {
        pagination: '.service-slider .swiper-pagination',
        slidesPerView: 1.5,
        slideShadows: true,
        loop: true
    });
})