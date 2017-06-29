<template>
  <div id="app">
    <navbar></navbar>
    <router-view></router-view>
  </div>
</template>

<script>

import Navbar from 'components/Navbar'
export default {
  name: 'app',
  components: {
    Navbar
  }
}
</script>

<style>
@import '~normalize.css/normalize.css';
@import '~bulma/css/bulma.css';
@import '~loaders.css/loaders.min.css';
@import './assets/iconfont/iconfont.css';
.page-loading {
    text-align: center;
    margin: 32px 0 32px 0;
}
.ball-pulse > div {
    background-color: #00d1b2;
}
</style>
