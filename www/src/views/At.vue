<template>
  <section class="at">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div v-for="(feed, index) in feeds" class="feeds">
      <feed 
        :key="index"
        :linker="articleLinker(feed)"
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
    this.fetchAt(this.page);
  },

  methods: {
    fetchAt(page) {
      this.isLoading = true;
      this.isButtonLoading = 'is-loading';
      api.getAt({ page }).then((res) => {
        if (! res.success) {
          return false;
        }

        this.feeds = this.feeds.concat(res.data.article);
        this.isLoading = false;
        this.isButtonLoading = '';
        console.log(res.data);
      });
    },

    articleLinker(feed) {
      // console.log(feed.id);
      return "/refer/at/" + feed.id;
    },

    loadMore() {
      this.fetchAt(++ this.page);
    }
  }
}
</script>

<style scoped>
.load-more {
    margin: 4px 0 4px 0;
}
</style>
