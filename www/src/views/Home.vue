<template>
<div class="container">
  <navbar></navbar>
  <div class="tabs is-fullwidth">
    <ul>
      <li :class="{ 'is-active': navActived.topten }"><a @click="navClicked('topten')">今日十大</a></li>
      <li :class="{ 'is-active': navActived.timeline }"><a @click="navClicked('timeline')">时间线</a></li>
      <li :class="{ 'is-active': navActived.favboards }"><a @click="navClicked('favboards')">收藏版面</a></li>
      <li :class="{ 'is-active': navActived.profile }"><a @click="navClicked('profile')">我</a></li>
    </ul>
  </div>
  <profile :profile="profile" v-show="navActived.profile"></profile>
  <!--
  <feed 
  v-for="(feed, index) in topTen"
  :title="feed.title"
  :desciption="feed.content"
  :author="feed.user.id"
  :board="feed.board"
  :attachment="feed.attachment ? feed.attachment : false"
  ></feed>
  -->
</div>
</template>

<script>
import Navbar from 'components/Navbar'
import Feed from 'components/Feed'
import * as api from 'api/home'
import { mapGetters, mapActions } from 'vuex'
import Profile from './partials/Profile'

export default {
  name: 'hot-feeds',

  data () {
    return {
      navActived: {
        topten: false,
        timeline: false,
        favboards: false,
        profile: true
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
    //this.fetchTopTen();
  },

  components: {
    Navbar,
    Profile,
    Feed
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
        if (res) {
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
