<template>
  <section class="reply">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div v-for="(feed, index) in feeds" class="feeds">
      <feed 
        :key="index"
        :linker="replyLinker(feed)"
        :title="feed.title"
        :desciption="feed.content"
        :author="feed.user.id"
        :board="feed.board"
        :attachment="feed.attachment ? feed.attachment : false">
      </feed>
    </div>
    <div class="load-more card" v-show="! isLoading">
      <header class="card-header">
        <a :class="'card-header-title button is-primary ' + isButtonLoading" @click="loadMore()">加载更多...</a>
      </header>
    </div>
  </section>
</template>
<script>
import * as api from 'api/home'
import Feed from 'components/Feed'

export default {
  data () {
    return {
      page: 1,
      isButtonLoading: '',
      isLoading: true,
      feeds: []
    }
  },

  components: {
    Feed
  },

  created() {
    this.fetchReply(this.page);
  },

  methods: {
    fetchReply(page) {
      this.isLoading = true;
      this.isButtonLoading = 'is-loading';
      api.getReply({ page }).then((res) => {
        if (! res.success) {
          return false;
        }

        this.feeds = this.feeds.concat(res.data.article);
        this.isLoading = false;
        this.isButtonLoading = '';
        console.log(res.data);
      });
    },
    // console.log(res.data);

    replyLinker(feed) {
        console.log(feed.id);
      return "/refer/reply/" + feed.id;
    },

    loadMore() {
      this.fetchReply(++ this.page);
    }
  }
}
</script>

<style scoped>
.load-more {
    margin: 4px 0 4px 0;
}
</style>
