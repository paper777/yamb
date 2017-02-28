<template>
<section class="topten">
  <feed 
    v-for="(feed, index) in feeds"
    linker="/"
    :title="feed.title"
    :desciption="feed.content"
    :author="feed.user.id"
    :board="feed.board"
    :attachment="feed.attachment ? feed.attachment : false">
  </feed>
</section>
</template>

<script>
import Feed from 'components/Feed'

export default {
  data () {
    return {

    }
  }, 

  components: {
    Feed
  },

  props: [
    'feeds'
  ],

  methods: {
  }
}
</script>
