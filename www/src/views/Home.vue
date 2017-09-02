<template>
<div class="container">
  <div class="tabs is-fullwidth">
    <ul>
      <li :class="{ 'is-active': navActived.topten }"><a @click="navClicked('topten')">今日十大</a></li>
      <li :class="{ 'is-active': navActived.timeline }"><a @click="navClicked('timeline')">发现</a></li>
      <li :class="{ 'is-active': navActived.fav }"><a @click="navClicked('fav')">版面</a></li>
      <li :class="{ 'is-active': navActived.profile }"><a @click="navClicked('profile')">我</a></li>
    </ul>
  </div>
  <router-view></router-view>
</div>
</template>

<script>
export default {
  name: 'home',

  data () {
    return {
      navActived: {
        topten: true,
        timeline: false,
        fav: false,
        profile: false
      },
    }
  },

  watch: {
    $route(to, from) {
      let activeTab = to.meta.parent ? to.meta.parent : to.name;
      this.changeTab(activeTab);
    }
  },

  created() {
    let activeTab = this.$route.meta.parent ? this.$route.meta.parent : this.$route.name;
    if (activeTab == 'home') {
      return this.$router.push('/home/topten');
    }
    this.changeTab(activeTab);
  },

  methods: {
    changeTab(active) {
      if (active == 'home') {
        active = 'topten';
      }
      for (let key in this.navActived) {
        if (key == active) {
          this.navActived[key] = true;
        } else {
          this.navActived[key] = false;
        }
      }
    },

    navClicked(item) {
      this.changeTab(item);
      this.$router.push('/home/' + item);
    }
  }
}
</script>

<style scoped>
.tabs {
    margin-bottom: 0;
    background-color: #fff;
}
</style>
