<template>
  <div class="main">
    <section class="main-1">
      <h1 class="main-1-title">注销当前账号</h1>
      <p class="main-1-info">注销账号后，您将无法使用该账号，包括但不限于：</p>
    </section>
    <section class="main-2">
      <p class="main-2-info">1.无法登录、使用会搜云商家账号。</p>
      <p class="main-2-info">2.移除该账号下的所有账号信息。</p>
      <p class="main-2-info">2.该账号下的个人资料和历史信息都无法找回。</p>
    </section>
    <footer class="footer">
      <div @click="delUser">
        <x-button type="primary">注销</x-button>
      </div>
      <div class="agreement">
        点击注销即代表已阅读并同意
        <span @click="goAgreement">
        《注销账户重要提醒》
        </span>
      </div>
    </footer>
  </div>
</template>

<script>
import { XButton } from 'vux';
import api from '@/config/api.js';

export default {
  components: {
    XButton,
  },
  methods: {
    /**
     * @author: 魏冬冬（zbf5279@dingtalk.com）
     * @description: 删除用户
     * @param {}
     * @return {void}
     * @Date: 2019-12-27 17:21:40
     */
    delUser() {
      const that = this;
      this.$vux.confirm.show({
        // 组件除show外的属性
        title: '注销账号',
        content: '注销后将不可恢复，确定要注销账号吗？',
        onCancel() {
        },
        onConfirm() {
          that.$axios.get(`${api}/account/auth/logoff`, { params: { token: that.$route.query.token } }).then(res => {
            if (res.data.status === 1) {
              that.$vux.confirm.show({
                // 组件除show外的属性
                title: '注销成功',
                content: '账号已注销成功，您将退出登录',
                showCancelButton: false,
                onConfirm() {
                },
              });
            } else {
              that.$vux.toast.text(res .data.info, 'middle');
            }
          });
        },
      });
    },
    /**
     * @author: 魏冬冬（zbf5279@dingtalk.com）
     * @description: 跳转到协议
     * @param {void}
     * @return {void}
     * @Date: 2019-12-27 17:21:46
     */
    goAgreement() {
      this.$router.push({
        path: '/agreement',
      });
    },
  },
  mounted() {
    // [1,2,4,5,6,7] 5
    // let arr = [1,2,3,4,5,6,7]
    // function sortArray(arr, n) {
    //   let len = n % arr.length
    //   arr = arr.splice(-len).concat(arr)
    //   return arr
    // }
    // sortArray(arr,8)
    // /^1[3456789]\d{9}$/
    
  }
};
</script>
<style lang="less">
@import '../assets/global.less';
.main {
  padding: 0 10px;
}
.main-1 {
  .main-1-title {
    color: @fontTitleColor;
    font-size: 20px;
    margin-top: 35px;
    font-weight: Bold;
  }
  .main-1-info {
    color: #333;
    font-size: 15px;
    margin-top: 25px;
  }
}
.main-2 {
  .main-2-info {
    margin-top: 5px;
    color: #999;
  }
  margin-top: 5px;
}
.footer {
  position: fixed;
  bottom: 20px;
  left: 10px;
  right: 10px;
  .agreement {
    font-size: 12px;
    color:#999;
    margin-top:18px;
    text-align: center;
    span {
      color: #3197FA;
    }
  }
}
</style>