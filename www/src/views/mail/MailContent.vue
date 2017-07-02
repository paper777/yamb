<template>

  <div class="main">

    <!-- Loader -->
    <div class="page-loading loader-inner ball-pulse" v-if="isLoading">
      <div> </div> <div> </div> <div> </div>
    </div>

    <!-- Mail -->
    <div>
      <div class="mail" v-if="!error">
        <div class='mail-title'>
          <h4 class="title is-4">
            {{ mail.title }}
          </h4>
        </div>
        <div class="mail-inscribe">
          <span class="sender is-pulled-left">from: &nbsp;{{ mail.sender }}</span>
          <span class="time is-pulled-right">{{ mail.time }}</span>
        </div>
        <div class="clear"></div>
        <hr>
        <div class="mail-content">
          <div v-html="mail.content"></div>
        </div>
      </div>

      <div v-else class="error">
        <div> Oops, there is an error... </div>
        <div>{{ errorMessage }}</div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div class="modal" v-bind:class="{'is-active': modalActive.replyModal}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">回复信件</p>
          <button class="delete" @click="closeModal"></button>
        </header>
        <section class="modal-card-body">
          <div class="field">
            <label class="label">标题</label>
            <p class="control">
              <input class="input" type="text"
                     v-model="replyForm.title"
                     v-bind:class="{'is-danger': replyForm.isTitleDanger}"
                     @focus="cancelDanger"
              />
            </p>
            <p class="help is-danger"
               v-bind:class="{'invisible': !replyForm.isTitleDanger}"
            >
              标题不能为空
            </p>
          </div>
          <div class="field">
            <label class="label">内容</label>
            <p class="control">
                <textarea class="textarea"
                          v-model="replyForm.content"
                          v-bind:class="{'is-danger': replyForm.isContentDanger}"
                          @focus="cancelDanger"
                >
                </textarea>
            </p>
            <p class="help is-danger"
               v-bind:class="{'invisible': !replyForm.isContentDanger}">
              内容不能为空
            </p>
          </div>
        </section>
        <footer class="modal-card-foot">
          <a class="button is-primary" @click="sendReply">发送信件</a>
          <a class="button" @click="closeModal">取消</a>
        </footer>
      </div>
    </div>

    <!-- Forward Modal -->
    <div class="modal" v-bind:class="{'is-active': modalActive.forwardModal}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">转发信件</p>
          <button class="delete" @click="closeModal"></button>
        </header>
        <section class="modal-card-body">
          <div class="field">
            <label class="label">收件人</label>
            <p class="control">
              <input class="input" type="text"
                     v-model="forwardForm.targetUser"
                     v-bind:class="{'is-danger': forwardForm.isTargetUserDanger}"
                     @focus="cancelDanger"
              />
            </p>
            <p class="help is-danger"
               v-bind:class="{'invisible': !forwardForm.isTargetUserDanger}">
              收件人不能为空
            </p>
          </div>
        </section>
        <footer class="modal-card-foot">
          <a class="button is-primary" @click="forwardMail">转发信件</a>
          <a class="button" @click="closeModal">取消</a>
        </footer>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal" v-bind:class="{'is-active': modalActive.deleteModal}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">删除信件</p>
          <button class="delete" @click="closeModal"></button>
        </header>
        <section class="modal-card-body">
          <p>是否确认删除信件？</p>
        </section>
        <footer class="modal-card-foot">
          <a class="button is-danger" @click="deleteMail">删除信件</a>
          <a class="button" @click="closeModal">取消</a>
        </footer>
      </div>
    </div>

    <!-- FAB -->
    <div>
      <fab
        :position="fab.position"
        :bg-color="fab.bgColor"
        :actions="fab.fabActions"
        :shadow="fab.shadow"
        @forward="fabForwardClicked"
        @reply="fabReplyClicked"
        @delete="fabDeleteClicked"
      ></fab>
    </div>

  </div>

</template>

<script>
  import * as api from 'api/mail'
  import fab from 'components/fab'

  export default {
    data() {
      return {
        query: {
          type: '',
          num: ''
        },
        mail: {
          num: null,
          title: null,
          sender: null,
          time: null,
          content: null
        },
        isLoading: true,
        error: false,
        errorMessage: '',

        modalActive: {
          replyModal: false,
          forwardModal: false,
          deleteModal: false,
        },

        replyForm: {
          isTitleDanger: false,
          isContentDanger: false,
          title: '',
          content: '',
        },

        forwardForm: {
          isTargetUserDanger: false,
          targetUser: '',
        },

        fab: {
          //bgColor: '#00d1b2',
          bgColor: '#8899aa',
          position: 'bottom-right',
          shadow: false,
          rippleShow: true,
          fabActions: [
            {
              name: 'forward',
              icon: 'arrow_forward',
              // tooltip: '转发'
            },
            {
              name: 'reply',
              icon: 'reply',
              // tooltip: '回复'
            },
            {
              name: 'delete',
              icon: 'delete',
              // tooltip: '删除'
            },
          ]
        }
      }
    },

    components: {
      fab
    },

    created() {
      this.query = this.$route.params;
      this.fetchMail();
    },

    methods: {
      fetchMail() {
        this.isLoading = true;
        api.getMail(this.query.type, this.query.num).then((res) =>{
          if (! res.success) {
            console.log("Get mail failed!");
            this.error = true;
            this.errorMessage = `type: ${this.query.type}, num: ${this.query.num}`;
            return false;
          }
          this.mail.num = res.data.num;
          this.mail.title = res.data.title;
          this.mail.sender = res.data.sender;
          this.mail.time = res.data.time;
          this.mail.content = res.data.content;
        });
        this.error = false;
        this.isLoading = false;
      },

      fabReplyClicked() {
        this.modalActive.replyModal = !this.modalActive.replyModal;
      },

      fabForwardClicked() {
        this.modalActive.forwardModal = !this.modalActive.forwardModal;
      },

      fabDeleteClicked() {
        this.modalActive.deleteModal = !this.modalActive.deleteModal;
      },

      closeModal() {
        this.modalActive.replyModal = false;
        this.modalActive.forwardModal = false;
        this.modalActive.deleteModal = false;
      },

      cancelDanger() {
        this.replyForm.isTitleDanger = false;
        this.replyForm.isContentDanger = false;
        this.forwardForm.isTargetUserDanger = false;
      },

      sendReply() {
        // TODO
        this.replyForm.isTitleDanger = ('' === this.replyForm.title);
        this.replyForm.isContentDanger = ('' === this.replyForm.content);
        if (this.replyForm.isTitleDanger || this.replyForm.isContentDanger) {
          return false;
        }
        // Send request
        // Handle res
        // Show result
      },

      forwardMail() {
        // TODO
        this.forwardForm.isTargetUserDanger = ('' === this.forwardForm.targetUser);
        if (this.forwardForm.isTargetUserDanger) {
          return false;
        }
        // Send request
        // Handle res
        // Show result
      },

      deleteMail() {
        // TODO
        // Send request
        // if success -> jump to mail list
        // else -> Show result
      }
    }
  }
</script>

<style scoped>
  .mail {
    padding-left: 15px;
    padding-right: 15px;
  }
  .mail-inscribe {
    color: #aaa;
  }
  .clear {
    clear: both;
  }
  .error {
    text-align:center;
    color: #aaa;
  }
  .textarea {

  }
  .invisible {
    visibility: hidden;
  }
</style>
