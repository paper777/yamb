<template>
<router-link :to="linker">
  <section class="reply box">
  <div class="reply-content ">
    <div class="content">
      <h4 class="title is-4">
        <slot name="header">
        {{ title }}
        </slot>
      </h4>
      <div class="media">
        <p class="media-content">
        {{ desciption }}
        </p>
        <div class="cover media-right" v-if="attachment">
          <img class="image is-64x64" :src="attachment">
        </div>
      </div>
    </div>
    <div class="user">
      by&nbsp<span class="user">{{ user }}</span>
      from&nbsp<span class="board">{{ board }}</span>
    </div>
  </div>
  </section>
</router-link>
</template>

<script>
export default {
  name: 'reply',
  props: [
    'id',
    'title',
    'index',
    'board',
    'read',
    'time'
  ]
}
</script>

<style scoped>
.reply-content p {
  color: #aaa;
}

.reply-content .user {
  color: #ccc;
}


</style>


