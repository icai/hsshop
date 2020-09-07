import apis from '../apis/url'

apis.install = (Vue) => {
  Vue.prototype.$apis = apis
}

export default apis
