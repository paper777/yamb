<template>
<section class="hero is-primary">
<div class="">
  <header class="nav">
  <div class="container">
    <div class="nav-left">
      <div class="nav-item">
        北邮人论坛LOGO
      </div>
    </div>
    <div class="nav-center">
      <div class="nav-item">
        paper777
      </div>
    </div>
    <span id="nav-toggle" class="nav-toggle" @click="clickMenu">
      <span></span>
      <span></span>
      <span></span>
    </span>
    <div :class="{ 'nav-right': true,  'nav-menu': true, 'is-active': menuActive }" @click="clickMenu">
      <a class="nav-item" href="">消息</a>
      <a class="nav-item" href="">分区</a>
      <a class="nav-item" href="">收录</a>
      <a class="nav-item" href="">注销</a>
    </diV>
  </div>
  </header>
</div>
<!--
<div class="hero-body">
  <div class="container">
    <div class="columns is-vcentered">
      <div class="card">
        <div class="card-content">
          <div class="content">
            下载论坛手机客户端
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="hero-foot">
  <div class="container">
    <nav class="tabs is-boxed">
    <ul>
      <li :class="navActived.topten"><router-link to="topten">今日十大</a></li>
      <li :class="navActived.timeline"><router-link to="">时间线</a></li>
      <li :class="navActived.topten"><router-link to="topten">收藏版面</a></li>
      <li :class="navActived.topten"><router-link to="topten">我</a></li>
    </ul>
    </nav>
  </div>
</div>
-->
</section>
</template>

<script>
export default {
  name: 'Navbar',

  data () {
    return {
      menuActive: false,
    }
  },

  //created() {
  //  let route = this.$route;
  //  this.activeNavbar(route.name);
  //},

  methods: {
    clickMenu() {
      this.menuActive = ! this.menuActive;
      console.log(this.menuActive);
    }
    //activeNavbar(name) {
    //  for (let i in this.navActived) {
    //    this.navActived[i] = i == name ? true : false;
    //  }
    //}
  }

  //watch: {
  //  $route (route) {
  //    this.activeNavbar(route.name);
  //  }
  //}

}
</script>

<style scoped>
.nav .nav-right a {
  text-align: right;
}

</style>
