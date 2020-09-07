import moment from 'moment'

// 封装函数 传参是几天
function laterDay(number) {
  return moment().add('days', number).format('M日DD日')
}
class TimeDay {
  laterSenvenDay(n) {
    const arrayDay = []
    for (let i = 0; i < n; i++) {
      arrayDay.push(laterDay(i))
    }
    return arrayDay
  }
}
let getHost = (that) => {
  if (that.$route.query.api_host) {
    window.localStorage.setItem('api_host', that.$route.query.api_host)
    return that.$route.query.api_host || ''
  } else {
    return (window.localStorage.getItem('api_host') !== 'undefined' && window.localStorage.getItem('api_host') !== null) ? window.localStorage.getItem('api_host') : ''
  }
}

let getToken = (that) => {
  if (that.$route.query.token) {
    window.localStorage.setItem('token', that.$route.query.token)
    return that.$route.query.token || ''
  } else {
    return (window.localStorage.getItem('token') !== 'undefined' && window.localStorage.getItem('token') !== null) ? window.localStorage.getItem('token') : ''
  }
}
export default {
  TimeDay,
  getHost,
  getToken
}
