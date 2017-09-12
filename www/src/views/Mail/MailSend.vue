<template>
  <section class="post-main">
    <input class="input" type="text" placeholder="标题（必填）" v-model="title">
    <span class="input to">To: {{ to }}</span>
    <textarea class="textarea" placeholder="写下你想对TA说什么吧" rows="10" v-model="content"></textarea>
    <div class="toolbar card">
      <header class="columns is-mobile">
        <div class="column is-2"> <i class="iconfont icon icon-smile" @click="expandSmileBox()"></i>
        </div>
        <div class="column is-2 post-button is-offset-7">
          <a @click="onSend()">发送</a>
        </div>
      </header>
    </div>
    <section class="smile-box" v-if="showSmileBox">
      <div class="tabs">
        <ul>
          <li :class="activeSmileSuite == 'em' ? 'is-active' : ''" @click="clickSmileSuite('em')"><a>经典</a></li>
          <li :class="activeSmileSuite == 'ema' ? 'is-active' : ''" @click="clickSmileSuite('ema')"><a>悠嘻猴</a></li>
          <li :class="activeSmileSuite == 'emb' ? 'is-active' : ''" @click="clickSmileSuite('emb')"><a>兔斯基</a></li>
          <li :class="activeSmileSuite == 'emc' ? 'is-active' : ''" @click="clickSmileSuite('emc')"><a>洋葱头</a></li>
        </ul>
      </div>
      <div class="smile-images" v-for="meta in smiles" :key="meta.name" v-if="meta.name == activeSmileSuite">
        <figure :class="`image is-${meta.size}`" v-for="i in meta.serials" :key="i" @click="insertSmile(meta.name, i)">
          <img alt="" :src="`//bbs.byr.cn/img/ubb/${meta.name}/${i}.gif`"/>
        </figure>
      </div>
    </section>
    <section class="upload-box" v-if="attachment && showUploadBox">

      <div class="upload-item" v-for="(item, index) in uploadItems" :key="index" >
        <span :style="'background-image: url(' + item + ')'"></span>
        <div class="close-btn" @click="removeImage(index)">
          <i class="icon iconfont icon-wrong"></i>
        </div>
      </div>

      <div class="upload-item" v-show="showUploadLoader">
        <div class="ball-pulse"><div></div><div></div><div></div></div>
      </div>
      <div class="upload-item new-upload" v-show="uploadItems.length">
        <div class="new-upload-icon">
          <i class="icon iconfont icon-add"></i>
        </div>
          <input type="file" accept="image/*" name="attachment" id="attachment2">
      </div>

    </section>

    <div class="loading-section" v-if="pageLoading">
        <div class="loading ball-pulse"><div></div><div></div><div></div></div>
    </div>
  </section>
</template>

<script>
import * as api from 'api/mail.js';
export default {
  data () {
    return  {
      pageLoading: false,
      attachment: true,
      type: 'new', // new article OR reply article OR edit article
      title: '',
      content: '',
      to: '',
      showSmileBox: false,
      activeSmileSuite: 'em',
      smiles: [
        {
          name: 'em',
          size: '32x32',
          serials:[...Array(73).keys()].map(n => n + 1)
        },
        {
          name: 'ema',
          size: '48x48',
          serials:[...Array(42).keys()]
        },
        {
          name: 'emb',
          size: '48x48',
          serials:[...Array(25).keys()]
        },
        {
          name: 'emc',
          size: '48x48',
          serials:[...Array(53).keys()]
        },
      ],

      showUploadBox: true,
      showUploadLoader: false,
      uploadItems: [],
      files: []

    };

  },

  created() {
    this.query = this.$route.query;
    this.action = this.query.action;
    this.title = this.query.title;
    this.to = this.query.to;
  },

  mounted() {
    this.pageLoading = false;
  },

  methods: {
    expandSmileBox() {
      this.showSmileBox = this.showSmileBox ? false : true;
    },

    clickSmileSuite(name) {
      this.activeSmileSuite = name;
    },

    insertSmile(name, i) {
      // TODO
      this.content += `[${name}${i}]`;
    },

    onSend() {
      switch(this.action) {
        case "reply":
          this.replyMail();
          break;
        case "new":
          this.sendMail();
          break;
        default:
          this.$toast('参数错误');
      }
    },

    sendMail() {
      let data = {
        title: this.title,
        content: this.content,
        id: this.query.to,
      };
      if (!data.id || !data.title || !data.content) {
        this.pageLoading = false;
        if (!data.id)
          this.$toast('Oops，忘记要发给谁了...返回上级重进此页面试一下？');
        else if (!data.title)
          this.$toast('Oops，标题不能为空');
        else if (!data.content)
          this.$toast('Oops，内容不能为空');
        return false;
      }
      api.sendMail(data)
        .then(res => {
          this.pageLoading = false;
          if (!res.success) {
            this.$toast('发送失败...');
            return false;
          }
          this.$toast('发送成功', {
            duration: 1000,
            callback: () => {
              this.$router.go(-1);
            }
          });
        })
    },

    replyMail() {
      let data = {
        title: this.title,
        content: this.content,
      };
      if (!data.title || !data.content) {
        this.pageLoading = false;
        if (!data.title)
          this.$toast('Oops，标题不能为空');
        else if(!data.content)
          this.$toast('Oops，内容不能为空');
        return false;
      }
      api.replyMail(this.query.type, this.query.num, data)
        .then(res => {
          this.pageLoading = false;
          if (!res.success) {
            this.$toast('发送失败...');
            return false;
          }
          this.$toast('发送成功', {
            duration: 1000,
            callback: () => {
              this.$router.go(-1);
            }
          });
        });
    },



  }
}
</script>
<style scoped>
.input, .textarea {
    border-radius: 0;
    border-color: #fff;
}
.toolbar .columns {
    padding: 0 1rem;
}
.column {
    width: auto !important;
}
.post-button {
    margin-left: 45%;
}
.column, .file-field {
    position: relative;
}
.file-field input {
    position: absolute;
    top: 0;
    left: 0;
    width: 48px;
    opacity: 0;
}
.smile-box {
    margin-top: 1rem;
}
.smile-box > .tabs {
    margin-bottom: 0.3rem;
}
.smile-images > figure {
    display: inline-block;
    margin: 0 1px;
}
.upload-box {
    margin-top: 1rem;
    padding: 0 0.5rem;
}

.upload-item {
    margin: 1rem 1rem 0 0;
    position: relative;
    display: inline-block;
    height: 120px;
    width: 80px;
}
.upload-item > span {
    height: 100%;
    width: 100%;
    display: inline-block;
    box-shadow: 0 1px 3px 1px rgba(0,0,0,.2);
    background-size: cover;
    background-position: center;
}

.upload-item > .close-btn {
    position: absolute;
    width: 20px;
    height: 20px;
    top: -12px;
    right: -10px;
}
.ball-pulse {
    position: absolute;
    top: 50px;
    left: 0;
}
.new-upload-icon {
    position: absolute;
    top: 48px;
    left: 28px;
}
.new-upload {
    box-shadow: 0 1px 3px 1px rgba(0,0,0,.2);
}
.new-upload input {
    position: absolute;
    top: 0;
    left: 0;
    width: 80px;
    height: 120px;
    opacity: 0;
}

.loading-section {
    position: fixed;
    z-index: 5000;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, .25);
}
.loading-section .loading {
    width: 12rem;
    height: 4rem;
    position: relative;
    text-align: center;

}
.to {
    color: #777777;
}
</style>
