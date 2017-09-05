<template>

  <div class="main">

    <!-- Loader -->
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>

    <!-- Mail -->
    <div>
      <div class="mail" v-if="!error">
        <div class='mail-title'>
          <h4 class="title is-4">
            {{ mail.title }}
          </h4>
        </div>
        <div class="mail-inscribe">
          <span class="sender is-pulled-left">from: &nbsp;{{ mail.sender }}</span>
          <span class="time is-pulled-right">{{ mail.time }}</span>
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

    <!-- FAB -->
    <div v-if="!error">
      <float-button @click.native='reply'></float-button>
    </div>

  </div>

</template>

<script>
  import * as api from 'api/mail';
  import FloatButton from 'components/FloatButton';

  export default {
    data() {
      return {

        query: {
          type: '',
          num: ''
        },

        mail: {
          num: '',
          title: '',
          sender: '',
          time: '',
          content: ''
        },

        modalActive: {
          replyModal: false,
          forwardModal: false,
          deleteModal: false,
        },

        replyForm: {
          isTitleDanger: false,
          isContentDanger: false,
          title: '',
          content: '',
        },

        forwardForm: {
          isTargetUserDanger: false,
          targetUser: '',
        },

        isLoading: true,

        error: false,
        errorMessage: '',

      }
    },

    components: {
      FloatButton
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
            this.error = true;
            this.errorMessage = `type: ${this.query.type}, num: ${this.query.num}`;
            return false;
          }
          this.mail.num = res.data.num;
          this.mail.title = res.data.title;
          this.mail.sender = res.data.sender;
          this.mail.time = res.data.time;
          this.mail.content = res.data.content;

          this.replyForm.title = this.mail.title.startsWith('Re: ') ?
            this.mail.title: 'Re: ' + this.mail.title;
        });
        this.error = false;
        this.isLoading = false;
      },

      reply() {
          let type = this.query.type;
          let num = this.query.num;
          let action = 'reply';
          let to = this.mail.sender;
          let title = this.mail.title.startsWith('Re: ') ?
            this.mail.title: 'Re: ' + this.mail.title;
          let url = `/mail/reply?type=${type}&num=${num}&action=${action}&to=${to}&title=${title}`;
          this.$router.push(url);
      },

    }
  }
</script>

<style scoped>
  .mail {
    padding: 16px 16px;
    background-color: #fff;
  }
  .mail-inscribe {
    color: #aaa;
    margin: 8px 0;
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
