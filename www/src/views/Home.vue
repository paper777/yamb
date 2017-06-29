<template>
<div class="container">
  <div class="tabs is-fullwidth">
    <ul>
      <li :class="{ 'is-active': navActived.topten }"><a @click="navClicked('topten')">今日十大</a></li>
      <li :class="{ 'is-active': navActived.timeline }"><a @click="navClicked('timeline')">时间线</a></li>
      <li :class="{ 'is-active': navActived.fav }"><a @click="navClicked('fav')">收藏版面</a></li>
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

  created() {
    let activeTab = this.$route.name;
    if (activeTab == 'Home') {
      activeTab = 'topten';
    }
    for (let key in this.navActived) {
      if (key == activeTab) {
        this.navActived[key] = true;
      } else {
        this.navActived[key] = false;
      }
    }
  },

  methods: {
    navClicked(item) {
      for (let key in this.navActived) {
        if (key == item) {
          this.navActived[key] = true;
        } else {
          this.navActived[key] = false;
        }
      }

      this.$router.push('/home/' + item);
    }
  }
  
}
</script>

<style scoped>
.tabs {
  margin-bottom: 8px;
}
</style>
