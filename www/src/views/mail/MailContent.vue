<template>
  <div class="main">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div class="mail" v-if="!error">
      <div class='mail-title'>
        <h4 class="title is-4">
          {{ mail.title }}
        </h4>
      </div>
      <div class="mail-inscribe">
        <span class="sender">from: &nbsp;{{ mail.sender }}</span>
        <span class="time">{{ mail.time }}</span>
      </div>
      <div class="clear"></div>
      <hr>
      <div class="mail-content">
        <div v-html="mail.content"></div>
      </div>
    </div>
    <div v-else class="error">
      <div> Oops, there is an error... </div>
      <div>{{ errorMessage }}</div>
    </div>
  </div>
</template>

<script>
  import * as api from 'api/mail'
  export default {
    data() {
      return {
        query: {
          type: '',
          num: ''
        },
        mail: {
          num: null,
          title: null,
          sender: null,
          time: null,
          content: null
        },
        isLoading: true,
        error: false,
        errorMessage: '',
      }
    },

    created() {
      this.query = this.$route.params;
      this.fetchMail();
    },

    methods: {
      fetchMail() {
        this.isLoading = true;
        api.getMail(this.query.type, this.query.num).then((res) =>{
          if (! res.success) {
            console.log("Get mail failed!");
            this.error = true;
            this.errorMessage = `type: ${this.query.type}, num: ${this.query.num}`;
            return false;
          }
          this.mail.num = res.data.num;
          this.mail.title = res.data.title;
          this.mail.sender = res.data.sender;
          this.mail.time = res.data.time;
          this.mail.content = res.data.content;
        });
        this.error = false;
        this.isLoading = false;
      }
    }
  }
</script>

<style scoped>
  .mail {
    padding-left: 15px;
    padding-right: 15px;
  }
  .mail-inscribe {
    color: #aaa;
  }
  .sender {
    float:left;
  }
  .time {
    float:right;
  }
  .clear {
    clear: both;
  }
  .error {
    text-align:center;
    color: #aaa;
  }
</style>
