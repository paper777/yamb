<template>
  <router-link :to="linker">
    <section class="box">
      <div class="mail-list-item">

        <h4 class="title is-4"
            v-bind:class="{ notRead: !read , hasRead: read}">
          {{ title }}
          <!-- The num here is for debugging -->
          <span class="num is-pulled-right">{{ num }}</span>
        </h4>

        <div class="inscribe">
          <span class="is-pulled-left">from&nbsp; {{ sender }}</span>
          &nbsp
          <span class="is-pulled-right">{{ time }}</span>
        </div>

      </div>
    </section>
  </router-link>
</template>

<script>
  export default {
    name: 'mailitem',
    props: [
      'read',
      'num',
      'sender',
      'title',
      'time',
      'size',
      'linker'
    ]
  }
</script>

<style scoped>
  .inscribe {
    color: #ccc;
  }
  .notRead {
    /*font-weight: normal;*/
  }
  .hasRead {
    color: #aaa;
  }
  .num {
    color: #ccc;
  }
</style>
