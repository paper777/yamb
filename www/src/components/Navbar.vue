<template>
<section class="hero is-primary">
  <div class="">
    <header class="nav">
      <div class="container">
        <div class="nav-left">
          <div class="nav-item">
            <h3 class="title is-6" @click="$router.push('/home/topten')">北邮人论坛-alpha</h3>
          </div>
        </div>
        <div class="nav-center">
          <div class="nav-item">
            <!--
            <a @click="search">search</a>
            -->
          </div>
        </div>
      </div>
    </header>
  </div>
</section>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Navbar',

  data () {
    return {
      menuActive: false,
    }
  },

  computed: {
    ...mapGetters([
      'profile'
    ])
  },

  methods: {
  }

}
</script>

<style scoped>
.nav .nav-right a {
  text-align: right;
}
</style>
