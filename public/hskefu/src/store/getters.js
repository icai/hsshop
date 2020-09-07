export default {
	userList: state => {//通过方法访问
      if(!state.user.custId){
        state.user.custId = localStorage.getItem('custId');
        state.user.custname = localStorage.getItem('custname');
        state.user.headurl = localStorage.getItem('headurl');
        state.user.shopId = localStorage.getItem('shopId');
        state.user.isheader = localStorage.getItem('isheader');
        state.user.crm_token = localStorage.getItem('crm_token');
        state.user.shop_name = localStorage.getItem('shop_name');
        state.user.shop_logo = localStorage.getItem('shop_logo');
      }
      return state.user
  },
  getStatus: state => {
    if(!state.status){
      state.status = localStorage.getItem("kefuStatus");
      return state.status
    }
  }
}
// localStorage.getItem("kefuStatus")