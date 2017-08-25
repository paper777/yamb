<template>
  <section class="contributor-section">
    <div class="card">
      <div class="card-content content">
        <h5>特别感谢</h5>
        <ul>
          <li v-for="(user, index) in contributors" :key="index">
            <router-link :to="'/user/' + user.id"> <p>{{ user.id }} </p> </router-link>
            <p>{{ user.work }}</p>
          </li>
        </ul>
      </div>
      <footer class="card-footer">
        <a href="https://github.com/paper777/yamb" class="card-footer-item button is-primary">参与开发</a>
      </footer>
    </div>
  </section>
</template>

<script>
export default {
  data () {
    return {
      contributors: [
        {

          id: 'D7474126',
          work: '站内信 设计与编码',
        },
        {
          id: 'raaay0608',
          work: '回复/艾特 设计与编码'
        }
      ]
    }
  }
}
</script>

<style>
.content {
    padding: 1rem;
}
</style>
