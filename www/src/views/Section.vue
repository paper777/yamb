<template>
  <section class="Section board-list">
    <div class="columns is-multiline is-mobile is-gapless search">
      <div class="column is-12">
        <form action="" v-on:submit.prevent="searchBoard">
          <p class="control has-icons-left">
            <input v-model="search_content" class="input" type="text" placeholder="查找版面"></input>
            <span class="icon is-small is-left">
              <i class="iconfont icon-search"></i>
            </span>
          </p>
        </form>
      </div>
    </div>
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <!-- Boards -->
    <section-card :section="section" v-for="(section, i) in boards" :key="section.name"> </section-card>
  </section>
</template>
<script>
import Vue from 'vue';
import * as api from 'api/home'
import SectionCard from 'components/SectionCard'
export default {
  data () {
    return {
      isLoading: true,
      doms: {},
      search_content: '',
      boards: []
    }
  },

   components: {
     SectionCard
   },

  created() {
    this.fetchSections();
  },

  methods: {
    fetchSections() {
      this.isLoading = true;
      api.getSections().then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }
        const data = res.data;
        this.boards = data.boards;
        this.isLoading = false;
      });
    },

    searchBoard() {
      if(this.search_content.trim() == '') {
        this.boards = []
        return this.fetchSections();
      }
      this.isLoading = true;
      api.searchBoards({ name: this.search_content }).then((res) => {
        if (! res.success) {
          // TODO
          return false;
        }

        this.boards = res.data.boards;
        this.isLoading = false;
      })
    }
  }
}
</script>
<style scoped>
 .Section {
   overflow-y: hidden;
 }
 .search {
   margin-top: 1px;
 }
 .Section .empty-tip {
   text-align: center;
   margin: 48px 16px 0 16px;
 }
 .Section .bread-nav {
   margin: 0 0 8px 0;
 }
 .Section .column.is-half {
   padding: 0 1px 1px 0; 
 }
 .Section .clear-both {
   clear: both;
 }
 .Section .search {
   margin-bottom: 1px !important;
   display: flex;
   display: -webkit-flex;
   align-content: center;
   align-items: center;
 }
 .search .input {
   border-radius: 0;
   -webkit-box-shadow: none;
   box-shadow: none;
 }
</style>
