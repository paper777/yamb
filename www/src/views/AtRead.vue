<template>
  <div class="main">
    <!--<loading>-->
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <!--<content>-->
    <!--<section class="AtRead" v-show="! isLoading">-->
    <div class="AtRead" v-if="!isLoading">

      <div class="Attitle">
        <h4 class="title is-4">
          {{this.title}}
        </h4>
      </div>

      <div class="read-info">
        <span class="sender is-pulled-left">from: &nbsp;{{ "this.sender" }}</span>
          <span class="time is-pulled-right">{{ this.time }}</span>
      </div>
      <div class="clear"></div>
      <hr>

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
import * as api from 'api/AtRead'

export default {
  data () {
    return {
      query: {
        id: ''
      },
      AtRead: {
        index: null,
        title:null,
        time:null,
        sender:null,
        content:null
      },

      isLoading: true,
      error:false,
      isButtonLoading:'',
      
      
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
    this.fetchAtRead();
  },

  methods: {
    fetchAtRead() {
      this.isLoading = true;
      const page = this.currentPage;
      
      api.getAtRead({refer:this.query.id, index:0}).then((res) => {
        if (! res.success) {
          console.log(this.query.id);
          console.log(res);
          console.log("get at failed!");
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
          this.pos = data.pos;
          this.isButtonLoading='';
          
        
       
        this.isLoading = false;
      });
    },

    jumpToArticle(data) {
      console.log(this.board_name);
      console.log(this.group_id);
      console.log(this.pos);
      this.$router.push('/article/' + this.board_name + "/" + this.group_id + "?pos=" + this.pos);

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
.at-tag {
    margin: 4px 0;
}


.AtRead {
    padding-left: 15px;
    padding-right: 15px;
  }
  .read-info {
    color: #aaa;
  }
  .clear {
    clear: both;
  }
  .error {
    text-align:center;
    color: #aaa;
  }
  .textarea {

  }
  .invisible {
    visibility: hidden;
  }
</style>
