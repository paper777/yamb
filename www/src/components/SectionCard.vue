<template>
  <section class="bbssection-card-section">
    <div class="bbssection">
      <div class="bbssection-content">
        <div v-if="section.dir" @click="toggle()" class="columns is-mobile is-gapless">
          <div class="column is-2">
            <div :class="sectionColor(section.id)" class="circle">
              <span>{{ section.first_level ? section.name[0] : section.name[0] }}</span>
            </div>
          </div>
          <div class="column is-9">
            <div class="pull-left section-name section-name-right">
              <div v-if="! section.first_level">子分区-{{ section.name }}</div>
              <div v-else>{{ section.name }}</div>
              <p class="title is-6">{{ section.desc }}</p>
            </div>
          </div>
          <div v-if="!section.dir" class="column is-1 icon-wrapper">
            <i class="iconfont icon-favorite" :class="section.fav ? 'icon-favoritesfilling' : 'icon-favorite'"></i>
          </div>
        </div>

        <div v-else class="columns is-mobile is-gapless" style="margin-left: 0.5rem">
          <div class="column is-10" @click="$router.push('/board/' + section.desc)">
            <p class="title is-6 board-name">{{ section.name }}</p>
            <p class="title is-6">{{ section.desc }}</p>
          </div>
          <div v-if="!section.dir" class="column is-2 icon-wrapper">
            <i class="iconfont icon-favorite" :class="section.fav ? 'icon-favoritesfilling' : 'icon-favorite'" @click="opFav(section)"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="children-section">
      <div class="children" v-if="isOpen" v-for="(child, i) in section.children" :key="child.name">
        <hr>
        <section-card :section="child"> </section-card>
      </div>
    </div>
  </section>
</template>

<script>
import * as api from '../api/fav.js'
export default {
  name: 'SectionCard',

  props: {
    section: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      isOpen: false
    }
  },

  created() {

  },

  methods: {
    toggle() {
      if (! this.isOpen && ! this.section.children.length) {
        return this.$toast('该分区无任何版面');
      }
      this.isOpen = ! this.isOpen;
    },

    sectionColor(id) {
      if (! Number.isInteger(id)) {
        id = id[0].charCodeAt() % 10;
      }
      return 'color-' + id ;
    },
    
    opFav(section) {
      if(section.dir)
        return

      let action = null;
      if(section.fav) {
        action = 'db'
      } else {
        action = 'ab'
      }
      api.opFav({
        ac: action,
        v: section.id
      }).then((res) => {
        if(res.success) {
          if(action == 'ab') {
            this.section.fav = true
            return this.$toast('收藏成功');
          } else if (action == 'db') {
            this.section.fav = false
            return this.$toast('取消收藏成功');
          } else {
            //TODO
          }
        }
      });
    }
  }
}

</script>

<style scoped>
.bbssection-card-section {
  background-color: white;
}
.bbssection {
  position: relative;
}
.bbssection-content {
  padding: 0.5rem 1rem;
}
.circle {
  position: relative;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background-color: whitesmoke;
  font-weight: bold;
  margin: .5rem 0;
}
.circle span {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  color: white;
 }
.section-name {
  font-size: large;
  font-weight: bold;
}
.board-name {
  margin-bottom: 0.5rem;
  font-weight: bold;
}
.news {
  float: right;
}
.children-section {
  padding-left: 2rem;
  position: relative;
}
.children::before {
  content: '';
  height: 100%;
  width: 1px;
  border-left: 1px dashed #858585;
  position: absolute;
  top: 0;
}
.children hr {
  background-color: white;
  border-top: 1px dashed #858585;
  position: absolute;
  left: 2rem;
  width: 0.6rem;
  height: 100%;
  margin: 2rem 0;
}
.color-0 {
  background-color: #ff1d3c;
}
.color-1 {
  background-color: #ff5597;
}
.color-2 {
  background-color: #de5ae0;
}
.color-3 {
  background-color: #329fe6;
}
.color-4 {
  background-color: #40c0f6;
}
.color-5 {
  background-color: #00bddc;
}
.color-6 {
  background-color: #00ad9f;
}
.color-7 {
  background-color: #50ae54;
}
.color-8 {
  background-color: #91c74b;
}
.color-9 {
  background-color: #ccdd45;
}
.icon-wrapper {
  display: flex;
  justify-content: flex-end;
  align-items: center;
}
.icon-favoritesfilling {
  color: #FFD700;
}
</style>
