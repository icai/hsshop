new Vue({
  el: '#main',
  data:{
    detail:null
  },
  methods: {
    
  },
  beforeCreate: function () {
      
  },
  created: function () {
    this.$http.get('/shop/order/refundVerify/'+wid + '/'+refundID).then(function(data){
      if(data.body.status == 1){
        this.detail = data.body.data
      }else{
        tool.tip(data.body.info)
      }
    })
  }
})