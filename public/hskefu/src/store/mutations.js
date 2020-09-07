
import {CHANGE_STATUS, LOGIN} from './mutation-types.js'

// import {setStore, getStore} from '../config/mUtils'

// import {localapi, proapi} from 'src/config/env'

export default {
	// 记录当前经度纬度
	[CHANGE_STATUS](state,status) {
        localStorage.setItem('kefuStatus', status);
		state.status = status;
	},
    [LOGIN](state,user) {
	  // console.log(user)
    state.user = user;
	}

}
