<template>
  <router-link :to="linker">
    <section class="box">
      <div class="mail-list-item">

        <h4 class="title is-4"
            v-bind:class="{ notRead: !read , hasRead: read}">
          {{ title }}
          <!-- The num here is for debugging -->
          <span class="num">{{ num }}</span>
        </h4>

        <div class="inscribe">
          <span class="sender">from&nbsp {{ sender }}</span>
          &nbsp<span class="time">{{ time }}</span>
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
    font-weight: normal;
  }
  .hasRead {
    color: #aaa;
  }
  .sender {
    float: left;
  }
  .time {
    float: right;
  }
  .num {
    color: #ccc;
    font-weight: normal;
    float: right;
  }
</style>
