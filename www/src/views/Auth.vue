<template>
<section class="login">
<div class="main container">
  <div class="header hero is-primary">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-vcentered">
          <div class="column">
            <p class="title">
            北邮人论坛
            </p>
            <p class="subtitle">
            北邮人温馨的家园
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
<div>
</div>
  <div class="content">
    <form class="form">
      <label class="label">用户名</label>
      <p class="control">
        <input class="input" type="text" name="username" v-model="username">
      </p>
      <label class="label">密码</label>
      <p class="control">
        <input class="input" type="password" name="password" v-model="password">
        <span class="help is-danger" v-show="error">账户名或者密码错误</span>
      </p>
      <p class="control">
      <button class="button is-primary" type="button" @click="doLogin">登录</button>
      </p>
    </form>
  </div>
  <div class="footer">
    <div class="container">
      <div class="content has-text-centered">
        <p> BYR-team@{{ year }} </p>
      </div>
    </div>
  </div>
</div>
</section>

</template>

<script>
import * as api from 'api/auth';
import { mapActions } from 'vuex'

export default {
  data () {
    return {
      username: '',
      password: '',
      error: false,
      year: new Date().getFullYear()
    }
  },

  methods: {
    doLogin() {
      api.login(this.username, this.password).then((res) => {
        if (! res.success) {
          this.error = true;
        } else {
          this.login(res.data);
          this.error = false;
          this.$router.push('/');
        }
      });
    },

    ...mapActions([
      'login'
    ]),

  }

}
</script>

<style scoped>
.content {
  padding: 1.25rem;
}
.form {
  width: 300px;
  margin: auto;
}
</style>
