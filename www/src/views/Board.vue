<template>
  <div class="board">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div class="container" v-show="! isLoading">
      <div class="board-description">
        <div class="media">
          <content class="media-content">
            <h4 class="head"> {{ description }}</h4>
          </content>
        </div>
      </div>
      <div class="posts">
        <div class="post" v-for="(article, index) in posts" :key="index" @click="getArticle(article.gid)">
          <div class="poster media">
            <div class="media-content">
              <div class="content">
                <p> <strong> {{ article.title }}</strong> </p>
              </div>
              <nav class="level is-mobile">
                <div class="level-left">
                  <span class="level-item">{{ article.poster }}</span>
                  <span class="level-item"> {{ article.replyTime }} 更新</span>
                </div>
                <div class="level-right">
                  <span class="level-item"> {{ article.replyCount }} 人回复</span>
                </div>
              </nav>
            </div>
          </div>
          <hr>
        </div>
      </div>
    </div>
    <section class="paginate" v-show="! isLoading && totalPage > 1">
      <div class="card">
        <header class="columns is-mobile">
          <div class="column">
            <a @click="getPrevPage">上一页</a>
          </div>
          <div class="column">
            <a>{{ currentPage + '/' + totalPage }}</a>
          </div>
          <div class="column">
            <a @click="getNextPage">下一页</a>
          </div>
        </header>
      </div>
    </section>

  </div>
</template>

<script>
import * as api from 'api/board'

export default {
  data () {
    return {
      query: {
        board: null
      },

      isLoading: true,

      // pagination
      currentPage: 1,
      totalPage: 1,

      canPost: false,
      isAdmin: false,
      isBM: false,
      name: '',
      description: '',

      posts: [],
      cachedPages: {}
    }
  },

  created() {
    this.query = this.$route.params;
    this.fetchBoards();
  },

  methods: {
    fetchBoards() {
      this.isLoading = true;
      const page = this.currentPage;
      if (page in this.cachedPages) {
        this.isLoading = false;
        return this.posts = this.cachedPages[page];
      }

      api.getBoard(this.query.board, { page }).then((res) => {
        if (! res.success) {
          return false;
        }

        const data = res.data;
        this.name = data.name;
        this.description = data.description;
        this.totalPage = data.pagination.total;
        this.canPost = data.canPost;
        this.isAdmin = data.Admin;
        this.isBM = data.isBM;
        this.posts = data.posts;

        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.isLoading = false;
      });
    },

    getArticle(gid) {
      this.$router.push(`/article/${this.name}/${gid}`);
    },

    getPrevPage() {
      if (this.currentPage <= 1) {
        return false;
      }
      this.currentPage --;
      this.fetchBoards();
    },

    getNextPage() {
      if (this.currentPage >= this.totalPage) {
        return false;
      }
      this.currentPage ++;
      this.fetchBoards();
    }

  }
}
</script>

<style scoped>
.container {
    padding: 12px 12px;
    background-color: #fff;
}

.board-description {
  padding-bottom: 12px;
}

hr {
margin: 18px 0 18px 0;
}
.paginate {
    text-align: center;
    margin: 1px;
}

</style>

