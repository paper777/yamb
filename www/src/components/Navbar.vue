<template>
<section class="hero is-primary">
  <div class="">
    <header class="nav">
      <div class="container">
        <div class="nav-left">
          <div class="nav-item"> 北邮人论坛-alpha</div>
        </div>
        <div class="nav-center">
          <div class="nav-item">
            {{ profile.id }}
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
