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

      <div class="notification is-primary is-fullwidth"
           v-bind:class="{'is-hidden': !hasNotification}"
      >
        <button class="delete large" @click="closeNotification"></button> <!-- TODO: `.is-large` not working -->
        {{ notificationMessage }}
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
        hasNotification: false,
        notificationMessage: '这里是个notification',
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

      if(this.$route.query.hasNotification) {

        switch (this.$route.query.notificationType) {
          case "deleted":
            this.notificationMessage = `已删除邮件 "${this.$route.query.mailTitle}"`;
            break;
          default:
            break;
        }

        this.hasNotification = true;

        let t = this;
        let timer = setTimeout(function(){
          t.closeNotification();
        }, 3000);
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

      closeNotification() {
        // TODO: May add disappear animation
        this.hasNotification = false;
      }
    }
  }
</script>

<style scoped>
  .notification {
    width: 100%;
    position: fixed;
    bottom: 0;
  }
</style>
