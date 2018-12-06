<template>
  <div class="main">
    <!-- Loader -->
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div></div>
      <div></div>
      <div></div>
    </div>

    <!-- Mail -->
    <div>
      <div class="mail" v-if="!error">
        <div class="mail-title">
          <h4 class="title is-4">{{ mail.title }}</h4>
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
        <div>Oops, there is an error...</div>
        <div>{{ errorMessage }}</div>
      </div>
    </div>

    <!-- FAB -->
    <div v-if="this.mailType !== 'deleted' && !error">
      <float-button @click.native="reply"></float-button>
    </div>
  </div>
</template>

<script>
import * as api from "api/mail";
import FloatButton from "components/ReplyFloatButton";

export default {
  data() {
    return {
      mail: {
        num: "",
        title: "",
        sender: "",
        time: "",
        content: ""
      },

      modalActive: {
        replyModal: false,
        forwardModal: false,
        deleteModal: false
      },

      replyForm: {
        isTitleDanger: false,
        isContentDanger: false,
        title: "",
        content: ""
      },

      forwardForm: {
        isTargetUserDanger: false,
        targetUser: ""
      },

      isLoading: true,

      error: false,
      errorMessage: ""
    };
  },

  computed: {
    mailType: function() {
      return this.$route.params.type;
    },
    mailNum: function() {
      return this.$route.params.num;
    }
  },

  components: {
    FloatButton
  },

  created() {
    this.fetchMail();
  },

  methods: {
    async fetchMail() {
      this.isLoading = true;
      const res = await api.getMail(this.mailType, this.mailNum);
      if (!res.success) {
        this.error = true;
        this.$toast("Oops, there is an error...");
        this.isLoading = false;
        return false;
      }
      this.error = false;

      this.mail.num = res.data.num;
      this.mail.title = res.data.title;
      this.mail.sender = res.data.sender;
      this.mail.time = res.data.time;
      this.mail.content = res.data.content;
      this.replyForm.title = this.mail.title.startsWith("Re: ")
        ? this.mail.title
        : "Re: " + this.mail.title;
      this.isLoading = false;
    },

    reply() {
      this.$router.push({
        name: `mailSend`,
        query: {
          action: "reply",
          to: this.mail.sender,
          title: this.mail.title.startsWith("Re: ")
            ? this.mail.title
            : "Re: " + this.mail.title,
          type: this.mailType,
          num: this.mailNum
        }
      });
    }
  }
};
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
  text-align: center;
  color: #aaa;
}
.textarea {
}
.invisible {
  visibility: hidden;
}
</style>
