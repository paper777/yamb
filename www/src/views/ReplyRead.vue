<template>
  <div class="main">
    <!--<loading>-->
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <!--<content>-->
    <!--<section class="ReplyRead" v-show="! isLoading">-->
    <div class="ReplyRead" v-if="!isLoading">

      <div class="Replytitle">
        <h4 class="title is-4">
          {{this.title}}
        </h4>
      </div>

      <div class="read-info">
        <!--<span class="sender is-pulled-left">from: &nbsp;{{ this.sender }}</span>-->
          <span class="time is-pulled-right">{{ this.time }}</span>
      </div>
      <div></div>

      <div class="read-content">
        <div v-html="this.content"></div>
      </div>
      <header class="card-header">
        <a :class="'card-header-title button is-primary ' + isButtonLoading" @click="jumpToArticle()">展开帖子</a>
      </header>

    </div>
    
  </div>
    <!--</section>-->
</template>

<script>
import * as api from 'api/ReplyRead'

export default {
  data () {
    return {
      query: {
        id: ''
      },
      ReplyRead: {
        index: null,
        title:null,
        time:null,
        sender:null,
        content:null
      },

      isLoading: true,
      error:false,
      
      
      // posts
      mainPost: null,
      posts: [],
      popularReplies: [],
      cachePosts: {}
    }
  },

  watch: {
  },

  created() {
    this.query = this.$route.params;
    this.fetchReplyRead();
  },

  methods: {
    fetchReplyRead() {
      this.isLoading = true;
      const page = this.currentPage;
      
      api.getReplyRead({refer:this.query.id, index:0}).then((res) => {
        if (! res.success) {
          console.log(this.query.id);
          console.log(res);
          console.log("get reply failed!");
          return false;
        }

        
        const data = res.data;
        console.log(res.data);

          this.title = data.title;
          this.time = data.time;
          this.id = data.id;
          this.board_name = data.board_name;
          this.content = data.content;
          this.group_id = data.group_id;
          
          
        
       
        this.isLoading = false;
      });
    },

    jumpToArticle(data) {
      console.log(this.board_name);
      console.log(this.group_id);
      this.$router.push('/article/' + this.board_name + "/" + this.group_id);

    },

   
    
  }
}
</script>

<style scoped>
h4 {
    margin: 0;
}
.container {
    padding: 12px 12px;
    background-color: #fff;
}
.poster.media {
    margin: 8px 0;
}
.article-body.content {
    color: black;
}

.tread-header {
    height: 60px;
}
.paginate {
    text-align: center;
    margin: 1px;
}
.reply-tag {
    margin: 4px 0;
}
</style>
