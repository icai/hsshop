new Vue({
  el: '#main',
  data:{
    imgUrl:imgUrl,
    formData:{
      imgs:[],
      remark:'',
      express_name:'',
      express_no:'',
      refundID:refundID
    },
    token:$('meta[name="csrf-token"]').attr('content'),
    number:0,//字体数量
    toastShow:false,
    msg:''
  },
  methods: {
    //插件图片上传
    imgUploader:function(){
      var that = this;
      var buttonUp = document.getElementById('btnUp');
        new AjaxUpload(buttonUp, {
            action: '/shop/order/upfile/'+wid,
            name: 'file',
            data: {'_token':that.formData._token},
            onSubmit: function (file, ext) {
                if (!(ext && /^(jpg|jpeg|JPG|JPEG|png)$/.test(ext))) {
                    alert('图片格式不正确,请选择 jpg 格式的文件!', '系统提示');
                    return false;
                }
          
          //上传动画
              hstool.load();
            },
            onComplete: function (file, response) {
                response =JSON.parse(response)
                console.log(response);
                if (response.status == 1){
                    //隐藏上传动画
                  hstool.closeLoad();
                  var imgSrc = imgUrl+""+response.data.path;
                  that.formData.imgs.push(response.data.path);
                }else{
                    alert(response.info);
                }
    
            }
        });
    },
    
    //删除图片
    delImgIndex:function(index){
      this.formData.imgs.splice(index,1);
    },
     submit:function(){
        var that = this;
        if(this.formData.express_name == ''){
            this.msg = '快递公司不能为空';
            this.toastShow = true;
            setTimeout(function(){
              that.toastShow = false;
            },2000)
            return;
        }
        if(this.formData.express_no == ''){
            this.msg = '快递单号不能为空';
            this.toastShow = true;
            setTimeout(function(){
              that.toastShow = false;
            },2000)
            return;
        }
        var data = {};
        data.refundID = this.formData.refundID;
        data._token = this.token;
        data.data = {};
        data.data['imgs'] = this.formData.imgs;
        data.data['express_name'] = this.formData.express_name;
        data.data['express_no'] = this.formData.express_no;
        data.data['remark'] = this.formData.remark;
        this.$http.post('/shop/order/refundReturn/'+ wid + '/' + refundID,data).then(function(data){
          if(data.body.status){
            tool.tip(data.body.info);
            setTimeout(function(){
              window.location.href = "/shop/order/refundDetailView/"+ wid + '/' + data.body.data.oid + "/" + data.body.data.pid  + "/" + data.body.data.prop_id;
            },2000)
          }
        })
     },
     changeInput:function(){
        this.number = this.formData.remark.length;
     }
  },
  beforeCreate: function () {
      
  },
  created: function () {
    
  },
  mounted: function () {
    this.$nextTick(function () {
      this.imgUploader()
    })
  }
})