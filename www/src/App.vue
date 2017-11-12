<template>
  <div id="app">
    <navbar></navbar>
    <div class="app-main">
      <router-view></router-view>
    </div>
    <footer class="footer">
      <div class="container">
        <div class="content has-text-centered">
          <p>
            <a href="http://m.byr.cn">旧版</a>
            <span class="separator">|</span>
            <a @click="backToNforum()">电脑版</a>
            <span class="separator">|</span>
            <a href="http://developers.byr.cn/mobile">客户端</a>
          </p>
          <p> BYR-team@{{ year }} </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
 import Navbar from 'components/Navbar'
 import { getBackToNforum, getBackToYamb } from 'api/home'

 export default {
   name: 'app',

   components: {
     Navbar
   },

   data () {
     return {
       year: new Date().getFullYear()
     }
   },

   mounted() {
       getBackToYamb()
   },

   methods: {
     backToNforum() {
       getBackToNforum().then((res) => {
         if (! res.success) {
           return this.$toast(res.message)
         }
         window.location.href = "/"
       })
     }
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
 .title {
   font-weight: 400;
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
 .footer {
   padding: 1rem 1rem;
 }
</style>
