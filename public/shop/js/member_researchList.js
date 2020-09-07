$('nav').hide()

var app = new Vue({
    el:'#app',
    delimiters:['${','}'],
    data:function(){
        return {
            list:data,
            wid:$('input[name="wid"]').val(),
            host:$('input[name="host"]').val()
        }
    },
    mounted() {
        $('.pageLoading').hide()
    },
    computed:{
        targetURL:function(){
            return this.host+'shop/member/researchDetail/'+this.wid+'/'
        }
    }
})

