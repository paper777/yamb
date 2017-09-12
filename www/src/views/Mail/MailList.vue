<template>
  <section class="maillist">

    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>

    <div>
      <div v-for="(mail, index) in mails" class="mails">
        <mail-item
          :read="mail.read"
          :num="mail.num"
          :sender="mail.sender"
          :title="mail.title"
          :time="mail.time"
          :size="mail.size"
          :linker="mailLinker(mail)">
        </mail-item>
      </div>
    </div>

  </section>
</template>
<script>
  // TODO: pagination
  import * as api from 'api/mail'
  import MailItem from 'components/MailItem'
  export default {
    data() {
      return {
        isLoading: true,
        type: '',
        mails: [],
      }
    },

    components: {
      MailItem,
    },

    watch: {
      '$route.params.type'(newType, oldType) {
        this.mails = [];
        this.fetchMailList(newType);
      }
    },

    created() {
      this.type = this.$route.params.type;
      this.fetchMailList(this.type);

      if(this.$route.query.notification) {
        this.$toast(this.$route.query.notification);
      }
    },

    methods: {
      fetchMailList(type) {
        let t = this;
        this.isLoading = true;
        api.getMailList(type)
          .then((res) =>{
            if(!res.success) {
              return false;
            }
            this.mails = this.mails.concat(res.data.mails);
          });
        this.isLoading = false;
      },

      mailLinker(mailItem) {
        // FIXME: this sometimes give a wrong `type` value, after quick swapping between tabs.
        return '/mail/' + this.type + '/show/' + mailItem.num;
      },

    }
  }
</script>

<style scoped>
</style>
