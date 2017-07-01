<template>
  <div class="container">
    <div class="tabs is-fullwidth">
      <ul>
        <li :class="{ 'is-active': mailTabActivated.inbox }"><a @click="mailTabClicked('inbox')">收件箱</a></li>
        <li :class="{ 'is-active': mailTabActivated.outbox }"><a @click="mailTabClicked('outbox')">发件箱</a></li>
        <li :class="{ 'is-active': mailTabActivated.deleted }"><a @click="mailTabClicked('deleted')">垃圾箱</a></li>
      </ul>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
  export default {
    // name: 'mail',

    data () {
      return {
        mailTabActivated: {
          inbox: true,
          outbox: false,
          deleted: false
        },
      }
    },

    watch: {
      $route(to, from) {
        this.changeMailTab(to.name);
      }
    },

    created() {
      let type = this.$route.params.type;
      this.changeMailTab(type);
    },

    methods: {
      mailTabClicked(type) {
        this.changeMailTab(type);
        this.$router.push('/mail/' + type);
      },

      changeMailTab(active) {
        for (let key in this.mailTabActivated) {
          this.mailTabActivated[key] = (key === active);
        }
      },
    }

  }
</script>

<style scoped>
  .tabs {
    margin-bottom: 8px;
  }
</style>
