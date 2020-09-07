import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'
import actions from './action'
import getters from './getters'

Vue.use(Vuex)

const state = {
	status: 'online', //在线状态online在线，busy忙碌offline离线,
    user: {
	  custId: '', //客服id
    custname: '',
    headurl: '',
    shopId: '',
    shop_name: '',
    shop_logo: '',
    isheader: '',
    crm_token: ''
  }//客户信息
}

export default new Vuex.Store({
	state,
	getters,
	actions,
	mutations,
})
