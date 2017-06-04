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
    <vue-recyclist
    :list="posts"
    :size="recyclist.size"
    :tombstone="true"
    :loadmore="nextPage">
    <template slot="tombstone" scope="props">
    <div class="item tombstone">
      <div class="avatar"></div>
      <div class="bubble">
        <p></p>
        <p></p>
        <p></p>
        <div class="meta">
          <time class="posted-date"></time>
        </div>
      </div>
    </div>
    </template>
      <template slot="item" scope="props">
      <div :id="props.data.id"> 
        <div class="poster media">
          <figure class="media-left">
          <p class="image is-32x32"> <img :src="props.data.poster ? props.data.poster.face_url : ''"> </p>
          </figure>
          <div class="media-content">
            <div class="content">
              <h4> {{ props.data.poster ? props.data.poster.id : '已注销'}}</h4>
              <small> {{ props.data.time }}</small>
            </div>
          </div>
          <div class="media-right">
            <span>{{ props.index == 0 ? '沙发' : props.index == 1 ? '板凳' : props.index + 1 + '楼' }}</span>
          </div>
        </div>
        <div class="article-body content" v-html="props.data.content"> </div>
        <hr>
      </div>
      </template>
    </vue-recyclist>

  </div>
</div>
</template>
<script>
import * as api from 'api/thread'

import VueRecyclist from 'vue-recyclist'

export default {
  data () {
    return {
      query: {
        board: '',
        id: ''
      },
      recyclist: {
        size: 9,
        spinner: true,
        tombstone: !+localStorage['tombstone']
      },
      title: '加载中...',
      time: '',
      gid: 0,
      board: {
        description: 'loading',
        name: '...'
      },
      lastPage: 0,
      anony: false,
      mainPost: null,
      posts:  []
    }
  },

  components: {
    VueRecyclist
  },

  watch: {
    tombstone(val) {
      localStorage['tombstone'] = +!val;
      this.id = 0;
      this.list = [];
      this.loadmore();
    }
  },

  created() {
    this.query = this.$route.params;
    //this.fetchArticles(this.query, this.$route.query);
  },

  methods: {
    nextPage() {
      const page = ++ this.lastPage;
      api.getThread(this.query.board, this.query.id, { page }).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }

        const data = res.data;
        if (page == 1) {
          this.title = data.title;
          this.time = data.time;
          this.gid = data.gid;
          this.board = data.board;
          this.anony = data.anony;
          if (data.articles[0]['id'] == this.gid) {
            [this.mainPost, ...this.posts] = data.articles;
            //this.recyclist.size = data.articles.length - 1;
          } else {
            this.posts = data.articles;
          }
        } else {
          this.posts = this.posts.concat(data.articles);
          //this.recyclist.size = data.articles.length;
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
