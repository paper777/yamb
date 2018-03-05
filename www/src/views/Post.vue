<template>
  <section class="post-main">
    <input class="input" type="text" placeholder="标题（必填）" v-model="title">
    <textarea class="textarea" placeholder="说说我的看法吧" rows="10" v-model="content"></textarea>
    <div class="toolbar card">
      <header class="columns is-mobile">
        <div class="column is-2"> <i class="iconfont icon icon-smile" @click="expandSmileBox()"></i>
        </div>
        <div class="column is-2 file-field" v-if="attachment">
            <i class="iconfont icon icon-img"></i>
              <input type="file" accept="image/*" name="attachment" id="attachment">
        </div>
        <div class="column is-2">
          <i @click="insertMention()" class="iconfont icon icon-at"></i>
        </div>
        <div class="column is-2 post-button" :class="attachment ? 'is-offset-5' : 'is-offset-7'">
          <a @click="post()">发表</a>
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

    <div class="tool-tip content">
      <blockquote>未认证手机号的用户请使用电脑版在控制面板处进行手机号验证</blockquote>
    </div>

    <div class="loading-section" v-if="pageLoading">
        <div class="loading ball-pulse"><div></div><div></div><div></div></div>
    </div>
  </section>
</template>

<script>
import * as api from 'api/post.js';
export default {
  data () {
    return  {
      pageLoading: false,
      attachment: false,
      attachmentSize: 0,
      type: 'new', // new article OR reply article OR edit article

      board: '',

      // reply
      reid: null,
      reTitle: '',

      // edit
      id: null,
      originalAttachments: [],
      deletedAttachments: [],

      attachInputBinded: false,
      attachInput2Binded: false,


      title: '',
      content: '',
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
    let query = this.$route.query;
    if (! query.board) {
        return this.$router.push('/404');
    }
    this.board = query.board;
    if (query.type == 'reply') {
      if (! query.reid) {
        return this.$router.push('/404');
      }
      this.reid = query.reid;
      this.type = 'reply';
      this.reTitle = query.retitle ? decodeURIComponent(query.retitle) : '';
      this.title = "Re: " + this.reTitle;
    } else if (query.type == 'edit') {
      this.type = 'edit';
      this.id = query.id;
    } else {
      this.type = 'new';
    }
  },

  mounted() {
    this.pageLoading = true;
    switch (this.type) {
    case 'reply':
      api.preReply(this.board, this.reid).then((res) => {
        this.pageLoading = false;
        if (res.success) {
          this.attachment = res.data.attachment;
          if (this.attachment) {
            this.$nextTick(() => {
              if (! this.attachInputBinded) {
                this.bindUploadEvent('attachment');
                this.attachInputBinded = true;
              }
            });
          }
          return true;
        }
        this.$toast(res.message, {
          callback: () => {
            this.$router.go(-1);
          }
        });
      });
      break;

    case 'edit':
      api.getEdit(this.board, this.id).then((res) => {
        this.pageLoading = false;
        if (! res.success) {
          return this.$toast(res.message, {
            callback: () => {
              this.$router.go(-1);
            }
          });
        }

        this.title = res.data.title;
        this.content = res.data.content;
        if (res.data.attachments.length) {
          this.originalAttachments = res.data.attachments;
          res.data.attachments.forEach((item) => {
            this.uploadItems.push(`/att/${this.board}/${this.id}/${item.pos}`);
            this.attachmentSize += item.size;
          });
        }
        if (res.data.attachment) {
          this.attachment = true;
          this.$nextTick(() => {
            if (! this.attachInputBinded) {
              this.bindUploadEvent('attachment');
              this.attachInputBinded = true;
            }
            if (! this.attachInput2Binded) {
              this.bindUploadEvent('attachment2');
              this.attachInput2Binded = true;
            }
          });

        }
      });

      break;

    case 'new':
      api.prePost(this.board).then((res) => {
        this.pageLoading = false;
        if (res.success) {
          this.attachment = res.data.attachment;
          if (this.attachment) {
            this.$nextTick(() => {
              if (! this.attachInputBinded) {
                this.bindUploadEvent('attachment');
                this.attachInputBinded = true;
              }
            });
          }
          return true;
        }
        this.$toast(res.message, {
          callback: () => {
            this.$router.go(-1);
          }
        });
      });

      break;
    }
  },

  methods: {
    expandSmileBox() {
      this.showSmileBox = this.showSmileBox ? false : true;
    },

    bindUploadEvent(id) {
      let $file = document.getElementById(id);
      this.showSmileBox = false;
      $file.addEventListener('change', () => {
        let reader = new FileReader();
        reader.onloadend = () => {
          this.uploadItems.push(reader.result);
          this.insertImage();
          if (this.uploadItems.length == 1) {
            if (! this.attachInput2Binded) {
              this.bindUploadEvent('attachment2');
              this.attachInput2Binded = true;
            }
          }
          this.showUploadLoader = false;
        }

        const file = $file.files[0];
        if (! file) {
          return false;
        }

        if (this.attachmentSize + file.size > 5242880) {
          this.$toast('图片大小不能超过5Mb');
          return false;
        }

        if (this.uploadItems.length >= 20) {
          this.$toast('附件数量不能超过20个');
          return false;
        }

        this.attachmentSize += file.size;
        this.files.push(file);
        this.showUploadLoader = true;
        reader.readAsDataURL(file);
      }, true);

    },

    clickSmileSuite(name) {
      this.activeSmileSuite = name;
    },

    insertSmile(name, i) {
      // TODO
      this.content += `[${name}${i}]`;
    },

    insertMention() {
      // TODO
      this.content += '@';
    },

    insertImage() {
      let tag = `[upload=${this.uploadItems.length}][/upload]`;
      // TODO
      this.content += tag;
    },

    removeImage(index) {
      let tag = `[upload=${index + 1}][/upload]`;
      let url = this.uploadItems[index];
      this.uploadItems.splice(index, 1);
      if (! url.startsWith('/att')) {
        this.files.splice(index, 1);
      } else {
        let pos = url.split('/').pop()
        for (let i in this.originalAttachments) {
          if (this.originalAttachments[i].pos == pos) {
            this.deletedAttachments.push(this.originalAttachments[i]['name']);
          }
        }
      }
      this.content = this.content.replace(tag, '');
    },

    post() {
      this.pageLoading = true;

      let addAtt = () => {
        for (let i in this.files) {
          api.addAttachment(this.board, this.files[i], this.id ? this.id : null).then((response) => {
            if (! response.success) {
              this.pageLoading = false;
              return this.$toast(response.message);
            }
            if (i == this.files.length - 1) {
              this.postArticle();
            }
          });
        }
      };

      if (! this.deletedAttachments.length) {
        if (this.files.length == 0) {
          this.postArticle();
        } else {
          addAtt();
        }
        return true;
      }

      for (let i in this.deletedAttachments) {
        api.deleteAttachment(this.board, this.deletedAttachments[i], this.id).then((response) => {
          if (! response.success) {
            this.pageLoading = false;
            return this.$toast(response.message);
          }
          if (i == this.deletedAttachments.length - 1) {
            if (this.files.length == 0) {
              this.postArticle();
            } else {
              addAtt();
            }
          }
        });
      }
    },

    postArticle() {
      let data = {
        subject: this.title,
        content: this.content
      };
      switch (this.type) {
        case 'new':
          api.newArticle(this.board, data).then((response) => {
            this.pageLoading = false;
            if (response.success) {
              this.pageLoading = false;
              return this.$router.go(-1);
            }
            this.$toast(response.message);
          });
          break;

        case 'reply':
          api.replyArticle(this.board, this.reid, data).then((response) => {
            this.pageLoading = false;
            if (response.success) {
              this.pageLoading = false;
              return this.$router.go(-1);
            }
            this.$toast(response.message);
          });
          break;

        case 'edit':
          api.editArticle(this.board, this.id, data).then((response) => {
            this.pageLoading = false;
            if (response.success) {
              this.pageLoading = false;
              return this.$router.go(-1);
            }
            this.$toast(response.message);
          });

          break;

        default: break;
      }

      return true;
    }
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
 .tool-tip {
   margin: 1.5rem 0.5rem 0 0.5rem;
 }
</style>
