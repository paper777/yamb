<template>
  <section class="fav-boards">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div class="bread-nav" v-if="parent >= 0">
      <a class="button is-info is-outlined is-small" @click="getParent()">返回</a>
    </div>

    <div class="empty-tip" v-if="! isLoading && boards.length == 0">
      <p class="subtitle is-6"> 收藏版面空空如也哦 </p>
    </div>

    <div class="columns is-multiline is-mobile is-gapless" v-if="! isLoading" @click="jumpToBoard(board)">
      <div class="column is-half" v-if="level == 0">
        <div class="card fav-board">
          <div class="card-content">
            <a @click="jumpToSection">
              <p class="title is-6">所有版面</p>
              <p class="sub-title is-6">查找/添加收藏</p>
            </a>
          </div>
        </div>
      </div>

      <div v-for="(board, index) in boards" :key="board.desc + board.level" class="column is-half" @click="jumpToBoard(board)">
        <div class="card fav-board">
          <div class="card-content">
            <a>
            <p class="title is-6">{{ board.dir ? '[目录]' : '' }} {{  board.desc }}</p>
            <p class="sub-title is-6">今日{{ board.new }}个讨论</p>
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
          this.$toast(res.message);
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

    jumpToSection() {
      this.$router.push('/section');
    },

    getParent() {
      this.level = this.parent;
      this.fetchBoards();
    }
  }

}
</script>
<style scoped>
 .fav-board {
   height: 80px;
   display: flex;
   align-items: center;
 }
 .card-content {
   padding: 0.5rem;
   white-space: nowrap;
   text-overflow: ellipsis;
   overflow-x: hidden;
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
