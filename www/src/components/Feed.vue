<template>
  <router-link target="_blank" :to="linker" class="main">
    <section class="feed box">
      <div class="feed-content ">
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
        <div class="author">
          by&nbsp<span class="author">{{ author }}</span>
          from&nbsp<span class="board">{{ board }}</span>
        </div>
      </div>
    </section>
  </router-link>
</template>

<script>
  export default {
    name: 'feed',
    props: [
      'title',
      'desciption',
      'author',
      'board',
      'attachment',
      'linker'
    ]
  }
</script>

<style scoped>
.feed {
    border-radius: 0;
}
.feed-content p {
    color: #aaa;
}

.feed-content .author {
    color: #ccc;
}


</style>


