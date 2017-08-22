<template>
  <section class="user">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>
    <div class="user card" v-show="! isLoading">
      <div class="card-content">
        <div class="media">
          <figure class="media-left">
            <p class="image" style="height: 100px; width: 100px;">
              <img :src="profile.face_url">
            </p>

          </figure>
          <div class="media-content">
            <div class="content">
              <p class="title is-4"> <strong>{{ profile.id }}</strong> </p>
              <div class="items">
                <p class="subtitle is-6"> {{ profile.user_name }} </p>
                <p class="subtitle is-6">
                  <i class="iconfont role icon-account" aria-hidden="true"></i>
                  <span class="role">{{ profile.role }}</span>
                  <i v-if="profile.gender == 'm'" class="iconfont icon-male" aria-hidden="true"></i>
                  <i v-else class="iconfont icon-female" aria-hidden="true"></i>
                </p>
                <p class="subtitle is-6">
                  <a @click="sendMail()" class="button is-outlined is-primary is-small">
                    <i class="small iconfont icon-email"></i>
                    &nbsp;私信
                  </a>
                  <a @click="addFriend()" class="button is-outlined is-info is-small">
                    <i class="small iconfont icon-add"></i>
                    添加好友
                  </a>
                </p>

              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <a class="card-footer-item">
          <div> {{ profile.life }}</div>
          <div>生命力</div>
        </a>
        <a class="card-footer-item">
          <div> {{ profile.post_count }} </div>
          <div> 文章 </div>
        </a>
        <a class="card-footer-item">
          <div> {{ profile.score }} </div>
          <div> 积分 </div>
        </a>
      </div>
    </div>

    <div class="pannel-box" v-show="! isLoading">
      <div class="basic-info box">
        <span> 等级 </span>
        <span> {{ profile.level }} </span>
      </div>
      <div class="basic-info box">
        <span> 状态 </span>
        <span> {{ profile.isOnline ? "在线" : "离线" }} </span>
      </div>
      <div class="basic-info box">
        <span> 星座 </span>
        <span> {{ profile.astro }} </span>
      </div>
      <div class="basic-info box">
        <span> 主页 </span>
        <span> {{ profile.home_page ? profile.home_page : '--' }} </span>
      </div>
      <div class="basic-info box">
        <span> QQ </span>
        <span> {{ profile.qq ? profile.qq : '--' }} </span>
      </div>
    </div>

  </section>
</template>

<script>
import { getUser } from 'api/home';
export default {
  data () {
    return {
      isLoading: true,
      profile: {

      }
    }
  },

  created() {
    this.fetchData();
    console.log(this.$route);
  },

  methods: {
    fetchData() {
      let id = this.$route.params.id;
      getUser(id).then((res) => {
        this.isLoading = false;
        if (! res.success) {
          return this.$toast('未知用户', {
            callback: () => {
              this.$router.go(-1);
            }
          });
        }

        Object.assign(this.profile, res.data);

      })
    },

    sendMail() {
      this.$toast('Ooops, 功能开发中...');
    },

    addFriend() {
      this.$toast('Ooops, 功能开发中...');
    }
  }
}
</script>

<style scoped>
span.role {
    padding-right: 8px;
    border-right: 1px solid #4a4a4a;
}
img {
  width: auto;
}
.card-footer-item {
    border-right: 0;
    display: block;
    text-align: center;
}
.card-footer-item div {
    color: #333;
}
.pannel-box {
    margin: 16px 0;
    border-radius: 5px;
}
.pannel-box .box {
    margin-bottom: 0;
    border-radius: 0;
    padding: 1rem;
}
.basic-info span:last-child {
    float: right;
}
.interactions {
    margin: 4px 0;
}
</style>
