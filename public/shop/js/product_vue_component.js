Vue.component("custom-template",{
	props: ["lists","host","sid"],
    delimiters: ['[[', ']]'], 
    created:function(){
        console.log(this.host)
        console.log(this.sid)
    },
	template: '<div><div v-for="(list, index) in lists" v-if="lists.length" v-cloak>'+
					'<!-- 富文本编辑器 -->'+
                    '<div class="custom-richtext js-custom-richtext js-lazy-container" v-if="list[\'type\']==\'shop_detail\'">'+
                        '<div v-html = "list[\'content\']"></div>'+
                    '</div>'+
                    '<!-- 富文本编辑器 -->'+
                    '<goods v-if="list[\'type\']==\'goods\'" :list="list"></goods>'+
                    '<!-- 富文本编辑器 -->'+
                    '<rich-text v-if="list[\'type\']==\'rich_text\'" :list="list"></rich-text>'+
                    '<!-- 富文本编辑器 -->'+

                    '<!-- 图片广告 -->'+
                    '<image-ad v-if="list[\'type\']==\'image_ad\' && list[\'images\'].length > 0" :list="list"></image-ad>'+
                    '<!-- 图片广告 -->'+
                    '<!-- 标题样式 -->'+
                    '<title-style v-if="list[\'type\']==\'title\'" :list="list"></title-style>'+
                    '<!-- 标题样式 -->'+

                    '<!-- 进入店铺 -->'+
                    '<store-in v-if="list[\'type\']==\'store\'" :list="list"></store-in>'+
                    '<!-- 进入店铺 -->'+

                    '<!-- 优惠券样式 -->'+
                    '<ul class="custom-coupon" v-if="list.type==\'coupon\' && list.couponList.length > 0">'+
                        '<li v-for="coupon in list.couponList">'+
                            '<a :href="coupon.url">'+
                                '<div class="custom-coupon-price">'+
                                    '<span>￥</span><span v-html="coupon.amount"></span>'+
                                '</div>'+
                                '<div class="custom-coupon-desc" v-html="coupon.limit_desc"></div>'+
                            '</a>'+
                        '</li>'+
                    '</ul>'+
                    '<!-- 优惠券样式 -->'+
                    '<!-- 视频组件 -->'+
                    '<cvideo :list="list" v-if="list.type == \'video\'"></cvideo>'+
                    '<!-- 公告样式 -->'+
                    '<notice v-if="list.type == \'notice\'" :content = "list.content"></notice>'+
                    '<!-- 公告样式 -->'+
                    '<!-- 商品搜索 -->'+
                    '<search :list="list" :host="host" :wid="sid" v-if="list.type == \'search\'"></search>'+
                    '<!-- 商品搜索 -->'+
                    '<!-- 商品列表 -->'+
                    '<goods-list v-if="list[\'type\']==\'goodslist\'" :list="list"></goods-list>'+
                    '<!-- 商品列表 -->'+
                    '<!-- 商品分组 -->'+
                    '<good-group v-if="list.type == \'good_group\' && (list.top_nav.length || list.left_nav.length)" :content="list"></good-group>'+
                    '<!-- 图片导航 -->'+
                    '<image-link v-if="list.type == \'image_link\'" :content="list.images"></image-link>'+
                    '<!-- 图片导航 -->'+
                    '<!-- 文本链接 -->'+
                    '<text-link v-if="list.type == \'textlink\'" :list="list"></text-link>'+
                    '<!-- 文本链接 -->'+
                '</div></div>',

});