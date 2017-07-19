<template>
<section class="topten">
  <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
    <div> </div> <div> </div> <div> </div>
  </div>
  <div v-for="(feed, index) in topTen" class="feeds">
    <feed 
      :key="index"
      :linker="jumpToArticle(feed)"
      :title="feed.title"
      :desciption="feed.content"
      :author="feed.user.id"
      :board="feed.board"
      :attachment="feed.attachment ? feed.attachment : false">
    </feed>
  </div>
</section>
</template>

<script>
import Feed from 'components/Feed'

import * as api from 'api/home'

export default {
  data () {
    return {
      isLoading: true,
      topTen: []
    }
  }, 

  components: {
    Feed
  },

  created() {
    this.fetchTopten();
  },

  methods: {
    fetchTopten() {
      this.isLoading = true;
      api.getTopTen().then((res) => {
        if (! res.success) {
          return false;
        }
        this.topTen = res.data;
        this.isLoading = false;
      });
    },

    jumpToArticle(feed) {
      return '/article/' + feed.board_name + '/' + feed.group_id;
    }
  }
}
</script>
