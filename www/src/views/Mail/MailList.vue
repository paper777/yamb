<template>
  <section class="maillist">
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div></div>
      <div></div>
      <div></div>
    </div>

    <div>
      <div v-for="(mail, index) in mails" :key="index" class="mails">
        <mail-item
          :read="mail.read"
          :num="mail.num"
          :sender="mail.sender"
          :title="mail.title"
          :time="mail.time"
          :size="mail.size"
          :linker="mailLinker(mail)"
        />
      </div>
    </div>

    <div class="card" v-if="!isLoading">
      <div class="columns is-mobile pagination">
        <div class="column">
          <a @click="changePage(1)" v-bind:class="{ disabled: isFirstPage }">首页</a>
        </div>
        <div class="column">
          <a @click="changePage(currentPage - 1)" v-bind:class="{ disabled: isFirstPage }">上页</a>
        </div>
        <div class="column">
          <span>{{ `${currentPage}/${totalPage}` }}</span>
        </div>
        <div class="column">
          <a @click="changePage(currentPage + 1)" v-bind:class="{ disabled: isLastPage }">下页</a>
        </div>
        <div class="column">
          <a @click="changePage(totalPage)" v-bind:class="{ disabled: isLastPage }">末页</a>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
// TODO: pagination
import * as api from "api/mail";
import MailItem from "components/MailItem";
export default {
  data() {
    return {
      isLoading: true,
      currentPage: 1,
      totalPage: 0,
      mails: []
    };
  },

  computed: {
    type: function() {
      return this.$route.params.type;
    },
    notification: function() {
      return this.$route.query.notification;
    },
    isFirstPage: function() {
      return this.currentPage <= 1;
    },
    isLastPage: function() {
      return this.currentPage >= this.totalPage;
    }
  },

  components: {
    MailItem
  },

  watch: {
    "$route.params.type"(newType, oldType) {
      this.mails = [];
      this.fetchMailList(newType);
    }
  },

  created() {
    this.fetchMailList();
    if (this.notification) {
      this.$toast(this.notification);
    }
  },

  methods: {
    async fetchMailList() {
      this.isLoading = true;
      const res = await api.getMailList(this.type, {
        page: this.currentPage
      });
      this.isLoading = false;
      if (!res.success) {
        return false;
      }
      this.mails = res.data.mails;
      this.currentPage = res.data.pagination.current;
      this.totalPage = res.data.pagination.total;
      this.top();
    },

    top() {
      document.body.scrollTop = document.documentElement.scrollTop = 0;
    },

    changePage(page) {
      if (page < 1 || page > this.totalPage) {
        return false;
      }
      this.currentPage = page;
      this.fetchMailList();
    },

    mailLinker(mailItem) {
      return `/mail/${this.type}/show/${mailItem.num}`;
    }
  }
};
</script>

<style scoped>
.pagination {
  text-align: center;
}
.disabled {
  color: #aaa;
}
.columns {
  margin: 0;
}
</style>
