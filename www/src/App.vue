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
@import './assets/iconfont/iconfont.css';
</style>
