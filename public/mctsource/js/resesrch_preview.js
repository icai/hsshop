// create by 赵彬 2018-8-7
new Vue({
    el:'#container',
    data:{
        lists:[],
        _host:APP_SOURCE_URL,
        host:APP_HOST,
        imgUrl:APP_IMG_URL,
        data:data,
        codeUrl:'',
        codeShow:false
    },
    methods:{

    },
    beforeCreate: function () {
        var newRules = [];
        for(var key in data.rules){
            newRules.push(data.rules[key])
        }

        this.lists = newRules
        console.log(newRules)
    },
    created: function () {
        var newRules = [];
        for(var key in data.rules){
            newRules.push(data.rules[key])
        }
        this.lists = newRules;
        this.$http.get('/merchants/marketing/researchXcxQrCode/'+data.id).then(
            function(res){
                var data = res.body.data
                if(data.errCode == 0){
                    this.codeShow = true
                    this.codeUrl ='data:image/png;base64,'+ data.data
                }
        })
    }
})