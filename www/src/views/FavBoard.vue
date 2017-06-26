<template>
  <section class="fav-boards">
    <div class="columns is-multiline is-mobile is-gapless">
      <div v-for="(board, index) in boards" :key="board.name" class="column is-half">
        <div class="card">
          <div class="card-content">
            <a @click="jumpToBoard(board.name)">
            <p class="title is-6">{{ board.desc }}</p>
            <p class="sub-title is-8">今日{{ board.new }}只新帖</p>
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
      parent: 0,
      boards: []
    }
  },

  created() {
    this.level = this.$route.query.level ? this.$route.query.level : 0;
    this.fetchBoards();
  },

  methods: {
    fetchBoards() {
      api.getFavBoards(this.level).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }
        const data = res.data;
        this.boards = data.boards;
        this.parent = data.parent;
      });
    },

    jumpToBoard(name) {
      this.$router.push('/board/' + name);
    }
  }

}
</script>
<style scoped>
.card-content {
  padding: 8px 0 8px 12px;
}
</style>
