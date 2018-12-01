<template>
  <router-link :to="linker">
    <section class="box">
      <div class="mail-list-item">

        <h4 class="title is-4"
            v-bind:class="{ notRead: !read , hasRead: read}">
          {{ title }}
        </h4>

        <div class="inscribe">
          <span class="is-pulled-left">from&nbsp; {{ sender }}</span>
          &nbsp;
          <span class="is-pulled-right">{{ time }}</span>
        </div>

      </div>
    </section>
  </router-link>
</template>

<script>
export default {
  name: "mailitem",
  props: ["read", "num", "sender", "title", "time", "size", "linker"]
};
</script>

<style scoped>
.box {
  border-radius: 0;
}
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
