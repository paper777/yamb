<template>
  <section class="fav-boards">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div class="bread-nav" v-if="parent >= 0">
      <a class="button is-info is-outlined is-small" @click="getParent()">返回</a>
    </div>
    <div class="empty-tip" v-if="boards.length == 0">
      <p class="subtitle is-6"> 收藏版面空空如也哦 </p>
    </div>
    <div class="columns is-multiline is-mobile is-gapless">
      <div v-for="(board, index) in boards" :key="board.name" class="column is-half">
        <div class="card fav-board">
          <div class="card-content">
            <a @click="jumpToBoard(board)">
            <p class="title is-6">{{ board.dir ? '[收藏目录]' : '' }} {{  board.desc }}</p>
            <p v-if="! board.dir" class="sub-title is-8">今日{{ board.new }}个讨论</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import * as api from 'api/home'
export default {
  data () {
    return {
      level: 0,
      isLoading: true,
      parent: -1,
      boards: []
    }
  },

  created() {
    this.level = this.$route.query.level ? this.$route.query.level : 0;
    this.fetchBoards();
  },

  methods: {
    fetchBoards() {
      this.isLoading = true;
      api.getFavBoards(this.level).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }
        const data = res.data;
        this.boards = data.boards;
        this.parent = data.parent;
        this.isLoading = false;
      });
    },

    jumpToBoard(board) {
      if (! board.dir) {
        return this.$router.push('/board/' + board.name);
      }

      this.level = board.level;
      this.fetchBoards();

    },

    getParent() {
      this.level = this.parent;
      this.fetchBoards();
    }
  }

}
</script>
<style scoped>
.card-content {
  padding: 8px 4px 8px 12px;
}
.empty-tip {
    text-align: center;
    margin: 48px 16px 0 16px;
}
.bread-nav {
    margin: 0 0 8px 0;
}
.column.is-half {
    padding: 0 1px 1px 0; 
}

</style>
