<template>
  <div id="app">
    <navbar></navbar>
    <div class="app-main">
      <router-view></router-view>
    </div>
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
html {
  overflow-x: visible;
}
body {
  overflow-x: visible;
}
.app-main {
    background-color: #f7f8fa;
}
.page-loading {
    text-align: center;
    margin: 32px 0 32px 0;
}
.ball-pulse > div {
    background-color: #00d1b2;
}
</style>
