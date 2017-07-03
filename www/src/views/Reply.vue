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

  </section>
</template>
<script>
import * as api from 'api/home'
import Feed from 'components/Feed'

export default {
  data () {
    return {
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
      // this.isButtonLoading = 'is-loading';
      api.getReply({ page }).then((res) => {
        if (! res.success) {
          console.log("Get reply list failed! Please try again!")
          return false;
        }

        console.log(res.data.article);
        this.feeds = this.feeds.concat(res.data.article);
        this.isLoading = false;
        this.isButtonLoading = '';
        console.log(res.data);
      });
    },
    // console.log(res.data);

    replyLinker(feed) {
        console.log(feed.id);
        // this.$router.push("ReplyRead",{feedid:feed.id});
      // return "/refer/reply/read.json&" + feed.id;
      return "/refer/reply/"+ feed.id;
      // return "/article/" + feed.board_name + '/' + feed.group_id
    },

    // loadMore() {
    //   this.fetchReply(++ this.page);
    // }
  }
}
</script>

<style scoped>
.load-more {
    margin: 4px 0 4px 0;
}
</style>
