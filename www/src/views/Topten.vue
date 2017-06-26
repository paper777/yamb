<template>
<section class="topten">
  <feed 
    v-for="(feed, index) in topTen"
    :key="index"
    linker="/"
    :title="feed.title"
    :desciption="feed.content"
    :author="feed.user.id"
    :board="feed.board"
    :attachment="feed.attachment ? feed.attachment : false">
  </feed>
</section>
</template>

<script>
import Feed from 'components/Feed'

import * as api from 'api/home'

export default {
  data () {
    return {
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
      api.getTopTen().then((res) => {
        if (! res.success) {
          return false;
        }
        this.topTen = res.data;
      });
    }
  }
}
</script>
