<template>
<div class="container">
  <div class="main">
    <div class="header content" >
      <h1>{{ title }}</h1>
      <div class="columns is-mobile">
        <div class="column is-7">
          <span>{{ time }}</span>
        </div>
        <div class="column">
          <a><span>{{ board.description + '/' + board.name }}</span></a>
        </div>
      </div>
    </div>
    <hr>

    <div v-if="mainPost" class="article">
      <div class="poster media">
        <figure class="media-left">
        <p class="image is-32x32"> <img :src="mainPost.poster.face_url"> </p>
        </figure>
        <div class="media-content">
          <div class="content">
            <h4> {{ mainPost.poster.id }}</h4>
            <small> {{ mainPost.time }}</small>
          </div>
        </div>
        <div class="media-right">
          <span>楼主</span>
        </div>
      </div>
      <div class="article-body content"  v-html="mainPost.content"> </div>
    </div>
    <div class="oops" v-else>
      // TODO threads without header
    </div>
  </div>

  <hr>
  <div class="posts">
    <div class="post" v-for="(post, index) in posts"> 
      <div class="poster media">
        <figure class="media-left">
          <p class="image is-32x32"> <img :src="post.poster.face_url"> </p>
        </figure>
        <div class="media-content">
          <div class="content">
            <h4> {{ post.poster.id }}</h4>
            <small> {{ post.time }}</small>
          </div>
        </div>
        <div class="media-right">
          <span>{{ index == 0 ? '沙发' : index == 1 ? '板凳' : index + 1 + '楼' }}</span>
        </div>
      </div>
      <div class="article-body content" v-html="post.content"> </div>
      <hr>
    </div>
  </div>
</div>
</template>
<script>
import * as api from 'api/thread'

export default {
  data () {
    return {
      title: '加载中...',
      time: '',
      gid: 0,
      board: {
        description: 'loading',
        name: '...'
      },
      anony: false,
      mainPost: null,
      posts:  []
    }
  },

  created() {
    this.fetchArticles(this.$route.params, this.$route.query);
  },

  methods: {
    fetchArticles({board, id}, params) {
      api.getThread(board, id, params).then((res) => {
        if (res.success) {
          const data = res.data;
          this.title = data.title;
          this.time = data.time;
          this.gid = data.gid;
          this.board = data.board;
          this.anony = data.anony;
          if (! data.articles.length) {
            // TODO
          }
          if (data.articles[0]['id'] == this.gid) {
            [this.mainPost, ...this.posts] = data.articles;
          } else {
            this.posts = data.articles;
          }
        } else {
          // TODO
        }
      });
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
}
.poster.media {
  margin: 8px 0;
}
.article-body.content {
  color: black;
}
.main .board {
}
</style>
