<template>
<div class="container">
  <div class="tabs is-fullwidth">
    <ul>
      <li :class="{ 'is-active': navActived.topten }"><a @click="navClicked('topten')">今日十大</a></li>
      <li :class="{ 'is-active': navActived.timeline }"><a @click="navClicked('timeline')">时间线</a></li>
      <li :class="{ 'is-active': navActived.favboards }"><a @click="navClicked('favboards')">收藏版面</a></li>
      <li :class="{ 'is-active': navActived.profile }"><a @click="navClicked('profile')">我</a></li>
    </ul>
  </div>
  <topten :feeds="topTen" v-show="navActived.topten"></topten>
  <profile :profile="profile" v-show="navActived.profile"></profile>
</div>
</template>

<script>
import * as api from 'api/home'
import { mapGetters, mapActions } from 'vuex'
import Profile from './partials/Profile'
import Topten from './partials/Topten'

export default {
  name: 'hot-feeds',

  data () {
    return {
      navActived: {
        topten: true,
        timeline: false,
        favboards: false,
        profile: false
      },
      topTen: [
        {
          id: 111,
          title: '加载中...',
          content: '加载中...',
          user: {
            id: 'paper777'
          },
          board: '钱塘人家·浙江(Zhejiang)',
          attachment: null
        }
      ]
    }
  },

  created() {
    this.fetchProfile();
    this.fetchTopTen();
  },

  components: {
    Topten,
    Profile,
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
    },

    fetchTopTen() {
      api.getTopTen().then((res) => {
        if (! res.success) {
          return false;
        }
        this.topTen = res.data;
      });
    },

    fetchProfile() {
      if (this.profile.isLogin) {
        return;
      }
      api.getProfile().then((res) => {
        if (res.success) {
          this.login(res.data);
        }
      });
    },

    ...mapActions([
      'login'
    ])

  },

  computed: {
    ...mapGetters([
      'profile'
    ])
  }
}
</script>

<style scoped>
.tabs {
  margin-bottom: 8px;
}
</style>
