<template>
  <div class="main">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <section class="thread" v-show="! isLoading">
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

        <div v-if="mainPost && currentPage == 1" class="article">
          <div class="poster media">
            <figure class="media-left">
              <p @click="jumpToUser(mainPost)" class="image is-32x32"> <img :src="mainPost.poster.face_url"> </p>
            </figure>
            <div class="media-content">
              <div class="content">
                <h4 @click="jumpToUser(mainPost)" :class="[ mainPost.poster.gender == 'm' ? 'gender-m' : 'gender-f' ]"> {{ mainPost.poster.id }}</h4>
                <small> <span>楼主</span> {{ mainPost.time }} </small>
              </div>
            </div>
            <div class="media-right">
              <span>
                <a v-if="mainPost.op" @click="edit(mainPost)"><i class="iconfont icon-edit"></i></a>
              </span>
              <span>
                <a @click="reply(mainPost)"><i class="iconfont icon-comments"></i></a>
              </span>
              <span>
                <a v-if="! mainPost.voted" @click="voteup(mainPost,-1)">
                  <i class="iconfont icon-good"></i>
                </a>
                <i v-else class="iconfont icon-good voted"></i>
                {{ mainPost.voteup_count }}
              </span>
              <span>
                <a v-if="! mainPost.voteddown" @click="votedown(mainPost, -1)">
                  <i class="iconfont icon-bad"></i>
                </a>
                <i v-else class="iconfont icon-bad voted"></i>
                {{ mainPost.votedown_count }}
              </span>
            </div>
          </div>
          <div class="article-body content"  v-html="mainPost.content"> </div>
        </div>
        <div class="oops" v-else-if="! mainPost">
          // TODO threads without header
          <br>
          // CONTRIBUTING: <a href="https://github.com/paper777/yamb">Go To Github Page</a>
          <hr>
        </div>


        <div class="popular-replies" v-if="currentPage == 1 && popularReplies.length">
          <div class="reply-tag">
            <span class="tag is-danger">精彩回复</span> 
            <b></b>
          </div>
          <div class="post" v-for="(article, index) in popularReplies" :key="index">
            <div class="poster media">
              <figure class="media-left">
                <p @click="jumpToUser(article)" class="image is-32x32"> <img :src="article.poster ? article.poster.face_url : ''"> </p>
              </figure>
              <div class="media-content">
                <div class="content">
                  <h4 @click="jumpToUser(article)" :class="[ article.poster ? (article.poster.gender == 'm' ? 'gender-m' : 'gender-f') : 'gender-u' ]"> {{ article.poster ? article.poster.id : '已注销'}}</h4>
                  <small>
                    <span>{{ article.pos == 1 ? '沙发' : (article.pos == 2 ? '板凳' : article.pos + '楼') }}</span>
                    {{ article.time }}
                  </small>
                </div>
              </div>
              <div class="media-right">
              <span>
                <a v-if="mainPost.op" @click="edit(mainPost)"><i class="iconfont icon-edit"></i></a>
              </span>
                <span>
                  <a @click="reply(mainPost)"><i class="iconfont icon-comments"></i></a>
                </span>
                <span>
                  <a v-if="! article.voted" @click="voteup(article, index)">
                    <i class="iconfont icon-good"></i>
                  </a>
                  <i v-else class="iconfont icon-good voted"></i>
                  {{ article.voteup_count }}
                </span>
                <span>
                  <a v-if="! article.voteddown" @click="votedown(article, index)">
                    <i class="iconfont icon-bad"></i>
                  </a>
                  <i v-else class="iconfont icon-bad voted"></i>
                  {{ article.votedown_count }}
                </span>
              </div>
            </div>
            <div class="article-body content" v-html="article.content"> </div>
            <hr v-if="index < popularReplies.length - 1">
          </div>
        </div>

        <div class="posts">
          <div class="reply-tag" v-if="currentPage == 1 && posts.length">
            <span class="tag is-primary">全部回复</span>
            <b></b>
          </div>
          <div class="post" :id="article.pos" v-for="(article, index) in posts" :key="index">
            <div class="poster media">
              <figure class="media-left">
                <p @click="jumpToUser(article)" class="image is-32x32"> <img :src="article.poster ? article.poster.face_url : ''"> </p>
              </figure>
              <div class="media-content">
                <div class="content">
                  <h4 @click="jumpToUser(article)" :class="[ article.poster ? (article.poster.gender == 'm' ? 'gender-m' : 'gender-f') : 'gender-u' ]" v-show="article.votedown_count - article.voteup_count < article.votedown_min"> {{ article.poster ? article.poster.id : '已注销'}}</h4>
                  <small>
                  <h4 :class="[ article.poster ? (article.poster.gender == 'm' ? 'gender-m' : 'gender-f') : 'gender-u' ]" v-show="article.votedown_count - article.voteup_count >= article.votedown_min">****</h4>
                    <span>{{ article.pos == 1 ? '沙发' : article.pos == 2 ? '板凳' : article.pos +  '楼' }}</span>
                    {{ article.time }}
                  </small>
                </div>
              </div>
              <div class="media-right">
                <span>
                  <a v-if="mainPost.op" @click="edit(article)"><i class="iconfont icon-edit"></i></a>
                </span>
                <span>
                  <a @click="reply(article)"><i class="iconfont icon-comments"></i></a>
                </span>
                <span>
                  <a v-if="! article.voted" @click="voteup(article, index)">
                    <i class="iconfont icon-good"></i>
                  </a>
                  <i v-else class="iconfont icon-good voted"></i>
                  {{ article.voteup_count }}
                </span>
                <span>
                  <a v-if="! article.voteddown" @click="votedown(article, index)">
                    <i class="iconfont icon-bad"></i>
                  </a>
                  <i v-else class="iconfont icon-bad voted"></i>
                  {{ article.votedown_count }}
                </span>
              </div>
            </div>
            <div class="article-body content" v-html="article.content" :class="{ 'selected-content': article.pos == position, 'remove-selected': removeSelected }" v-show="article.votedown_count - article.voteup_count < article.votedown_min"> </div>
            <div class="a-background" v-show="article.votedown_count - article.voteup_count >= article.votedown_min">
                  <p>此楼踩的人太多已被折叠。</p>
                  <p>请多多注意自己的发言哦...</p>
                  <a class="a-func-dispCai button" @click="votedown_view(article.pos)">手贱想看</a>
            </div>
            <hr>
          </div>
        </div>
      </div>
    </section>
    <section class="paginate" v-show="showVoteChoice" style="margin-bottom: 10px;">
      <div class="card">
      <header class="columns  is-mobile paginate-items">
            <div class="column">
              <a v-if="mainPost && ! mainPost.voted" @click="voteup(mainPost, -1);showVote();">赞 {{ mainPost.voteup_count }}</a>
              <a v-else @click="showVote()">赞 {{ mainPost.voteup_count }}</a>
            </div>
            <div class="column">
              <a v-if="mainPost && ! mainPost.voteddown" @click="votedown(mainPost, -1);showVote();">踩 {{ mainPost.votedown_count }}</a>
              <a v-else @click="showVote()">踩 {{ mainPost.votedown_count }}</a>
            </div>
      </header>
      </div>
    </section>
    <section class="paginate" v-if="! isLoading">
      <div class="card">
        <header class="columns is-mobile paginate-items">
          <div class="column">
            <a @click="showVote">
            <span>
              <i class="iconfont icon-less up"></i>
              <i class="iconfont icon-moreunfold down"></i>
            </span>
              赞 {{ mainPost.voteup_count }}
            </a>
          </div>
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
            <a @click="reply(mainPost)">回复</a>
          </div>
        </header>
      </div>
    </section>
  </div>
</template>

<script>
import * as api from 'api/thread'

export default {
  data () {
    return {
      query: {
        board: '',
        id: ''
      },

      position: 0,

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
      cachePosts: {},

      //show vote up&down
      showVoteChoice: false,

      removeSelected: false
    }
  },

  watch: {
  },

  created() {
    let location = this.$route.query;
    if (location.page) {
      this.currentPage = location.page;
    }

    if (location.pos) {
      this.currentPage = parseInt(location.pos / 10) + 1;
      this.position = location.pos;
    }
    
    this.query = this.$route.params;
    this.fetchArticles();
  },

  methods: {
    fetchArticles() {
      this.isLoading = true;
      const page = this.currentPage;
      if (page in this.cachePosts) {
        this.isLoading = false;
        return this.posts = this.cachePosts[page];
      }
      api.getThread(this.query.board, this.query.id, { page }).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }

        const data = res.data;
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
          if (data.head && ! this.mainPost) {
            this.mainPost = data.head;
          }
        }
        document.title = this.title + ' -北邮人论坛';
        this.cachePosts[page] = this.posts;
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.isLoading = false;

        if (this.position > 0) {
          this.$nextTick(() => {
            let $dom = document.getElementById(this.position);
            if (! $dom) {
              return ;
            }

            $dom.scrollIntoView();
            setTimeout(() => {
              this.removeSelected = true;
            }, 2000)
          });
        } 
      });
    },

    jumpToBoard(name) {
      this.$router.push('/board/' + name);
    },

    jumpToUser(article) {
      if (article.poster) {
        this.$router.push('/user/' + article.poster.id);
      }
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

    showVote(){
      this.showVoteChoice = !this.showVoteChoice;
    },

    voteup(article, index) {
      api.voteup(this.board.name, article.id, {id:article.id}).then((res) => {
        if (! res.success) {
          // TODO 
          return false;
        }
        article.voted = true;
        article.voteddown = false;
        article.voteup_count = res.data.up_count;
        article.votedown_count = res.data.down_count;
        if (index == -1) {
          return this.mainPost = article;
        }
        this.posts[index] = article;
      });
    },

    votedown(article, index) {
      api.votedown(this.board.name, article.id, { id: article.id }).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }
        article.voted = false;
        article.voteddown = true;
        article.voteup_count = res.data.up_count;
        article.votedown_count = res.data.down_count;
        if (index == -1) {
          return this.mainPost = article;
        }
        this.posts[index] = article;
      });
    },

    votedown_view(index) {
      var article_dom = document.getElementById(index);
      var username = article_dom.getElementsByClassName("content")[0].getElementsByTagName("h4");
      username[0].style.display = "block";
      username[1].style.display = "none";
      article_dom.getElementsByClassName("article-body")[0].style.display = "block";
      article_dom.getElementsByClassName("a-background")[0].style.display = "none";
    },

    edit(article) {
      let url = `type=edit&id=${article.id}&board=${this.board.name}`;
      this.$router.push('/post?' + url);
    },

    reply(article) {
      const title = encodeURIComponent(this.title);
      let url = `type=reply&reid=${article.id}&board=${this.board.name}&retitle=${title}`;
      this.$router.push('/post?' + url);
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
.gender-m {
    color: #57e5d5;
}
.gender-f {
    color: #ff6693;
}
.article-body.content {
    color: black;
}
.content {
  overflow-wrap: break-word;
  word-wrap: break-word;

  word-break: break-word;

  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto;
}

.tread-header {
    height: 60px;
}
.paginate {
    text-align: center;
    margin: 1px;
}
.paginate-items {
  margin-left: 0;
  margin-right: 0;
}
.reply-tag {
    margin: 16px 0;
}
.reply-tag b {
    display: inline-block;
    height: 1px;
    width: 79%;
    background: #ddd;
    float: right;
    margin-top: 13px;
}
.media-right a {
    color: #4a4a4a;
}
.voted {
    color: #00d1b2;
}
.selected-content {
    background-color: #eaeaec;
}
.remove-selected {
    background-color: #fff;
}
.up,.down {   
    font-size: 10px;
    line-height:10px;
}
.up {
    position:absolute;
    top:30%;
}
.down {
    position:relative;
    top:15%;
}
</style>
