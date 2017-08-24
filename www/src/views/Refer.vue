<template>
  <section class="reply">

    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>

    <div v-for="(feed, index) in feeds" @click="setRead(feed)">
      <feed
        :key="index"
        :linker="replyLinker(feed)"
        :title="feed.title"
        :desciption="feed.content"
        :author="feed.user"
        :board="feed.board"
        :backgroundColor="feed.read ? 'white' : 'grey'"
        :attachment="feed.attachment ? feed.attachment : false">
      </feed>
    </div>

  </section>
</template>
<script>
import * as api from 'api/refer'
import Feed from 'components/Feed'
import { mapActions } from 'vuex'

export default {
  data () {
    return {
      isLoading: true,
      type: '',
      feeds: []
    }
  },

  components: {
    Feed
  },

  created() {
    let route = this.$route;
    this.type = route.name;
    this.fetchRefer(this.page);
  },

  methods: {
    fetchRefer(page) {
      this.isLoading = true;
      api.getRefer(this.type, { page }).then((res) => {
        if (! res.success) {
          return false;
        }

        this.feeds = this.feeds.concat(res.data.article);
        this.isLoading = false;
      });
    },

    setRead(feed) {
      if (feed.read) {
        return true;
      }
      let index = feed.index;
      api.setRead(this.type, { index }).then((res) => {
        if (res.success) {
          feed.read = true;
          this.decreaseRefer(this.type);
        }
      });
    },

    replyLinker(feed) {
      return '/article/' + feed.board + '/' + feed.group_id + "?pos=" + feed.pos;
    },

    ...mapActions([
      'decreaseRefer'
    ])

  }
}
</script>

<style scoped>
.load-more {
    margin: 4px 0 4px 0;
}
</style>
