<template>
  <div class="main">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <!--<h1>
        it works!
        </h1>-->
    <section class="ReplyRead" v-show="! isLoading">
      <div class="container">
        <div class="thread-header" v-show="currentPage == 1">
          <h2>{{ title }}</h2>
          <div class="columns is-mobile">
            <div class="column is-7">
              <small>{{ time }}</small>
            </div>
            <div class="column">
              <a @click="jumpToBoard(board.name)"><small>{{ board.description + '/' + board.name }}</small></a>
            </div>
          </div>
          <hr>
        </div>

        <div v-if="mainPost" class="reply" v-show="currentPage == 1">
          <div class="poster media">
            <figure class="media-left">
              <p class="image is-32x32"> <img :src="mainPost.poster.face_url"> </p>
            </figure>
            <div class="media-content">
              <div class="content">
                <h4> {{ mainPost.poster.id }}</h4>
                <small> {{ mainPost.time }} </small>
              </div>
            </div>
            <div class="media-right">
              <span>楼主</span>
            </div>
          </div>
          <div class="article-body content"  v-html="mainPost.content"> </div>
          <hr>
        </div>
        <div class="oops" v-else>
          // TODO threads without header
          // CONTRIBUTING: https://github.com/paper777/yamb
          <hr>
        </div>


        <div class="popular-replies" v-show="currentPage == 1">
          <span class="tag is-danger reply-tag">精彩回复</span> 
          <div class="post" v-for="(article, index) in popularReplies" :key="index">
            <div class="poster media">
              <figure class="media-left">
                <p class="image is-32x32"> <img :src="article.poster ? article.poster.face_url : ''"> </p>
              </figure>
              <div class="media-content">
                <div class="content">
                  <h4> {{ article.poster ? article.poster.id : '已注销'}}</h4>
                  <small> {{ article.time }}</small>
                </div>
              </div>
              <div class="media-right">
                <span>{{ article.pos == 1 ? '沙发' : (article.pos == 2 ? '板凳' : article.pos + '楼') }}</span>
              </div>
            </div>
            <div class="article-body content" v-html="article.content"> </div>
            <hr>
          </div>
        </div>

        <div class="posts">
          <span class="tag is-primary reply-tag" v-show="currentPage == 1">全部回复</span>
          <div class="post" v-for="(article, index) in posts" :key="index">
            <div class="poster media">
              <figure class="media-left">
                <p class="image is-32x32"> <img :src="article.poster ? article.poster.face_url : ''"> </p>
              </figure>
              <div class="media-content">
                <div class="content">
                  <h4> {{ article.poster ? article.poster.id : '已注销'}}</h4>
                  <small> {{ article.time }}</small>
                </div>
              </div>
              <div class="media-right">
                <span>{{ article.pos == 1 ? '沙发' : article.pos == 2 ? '板凳' : article.pos +  '楼' }}</span>
              </div>
            </div>
            <div class="article-body content" v-html="article.content"> </div>
            <hr>
          </div>
        </div>
      </div>
    </section>
    <section class="paginate" v-show="! isLoading && totalPage > 1">
      <div class="card">
        <header class="columns is-mobile">
          <div class="column"> <a>顶10</a> </div>
          <div class="column">
            <a @click="getPrevPage">上一页</a>
          </div>
          <div class="column">
            <a>{{ currentPage + '/' + totalPage }}</a>
          </div>
          <div class="column">
            <a @click="getNextPage">下一页</a>
          </div>
          <div class="column">
            <a @click="getReply">回复</a>
          </div>
        </header>
      </div>
    </section>
  </div>
</template>

<script>
import * as api from 'api/ReplyRead'

export default {
  data () {
    return {
      query: {
        // board: '',
        id: ''
      },

      isLoading: true,
      
      title: '加载中...',
      time: '',
      gid: 0,
      board: {
        description: 'loading',
        name: '...'
      },

      // pagination
      currentPage: 1,
      totalPage: 1,

      anony: false,

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
      if (page in this.cachePosts) {
        this.isLoading = false;
        return this.posts = this.cachePosts[page];
      }
      api.getReplyRead(this.query.id, { page }).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }

        const data = res.data;
        console.log(res.data);
        if (page == 1) {
          this.title = data.title;
          this.time = data.time;
          this.gid = data.gid;
          this.board = data.board;
          this.anony = data.anony;
          this.popularReplies = data.popularReplies;
          this.totalPage = data.pagination.total;
          if (data.articles[0]['id'] == this.gid) {
            [this.mainPost, ...this.posts] = data.articles;
          } else {
            this.posts = data.articles;
          }
        } else {
          this.posts = data.articles;
        }
        this.cachePosts[page] = this.posts;
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.isLoading = false;
      });
    },

    jumpToBoard(name) {
      this.$router.push('/board/' + name);
    },

    getPrevPage() {
      if (this.currentPage <= 1) {
        return false;
      }
      this.currentPage --;
      this.fetchArticles();
    },

    getNextPage() {
      if (this.currentPage >= this.totalPage) {
        return false;
      }
      this.currentPage ++;
      this.fetchArticles();
    },

    getReply() {
    }

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
