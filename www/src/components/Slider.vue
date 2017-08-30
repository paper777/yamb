<template>
  <section
    class="slider-section"
    :style="{
           width: `${width}px`,
           height: `${height}px`
           }">
    <div class="slider" v-show="ready">
      <div
        class="item"
        v-for="(item, index) in items"
        :class="{
               animating: isAnimating(index),
               active: index == activeIndex
               }"
        :style="{
               transform: `translateX(${ calculatetranslate(index) }px)`,
               '-webkit-transform': `translateX(${ calculatetranslate(index) }px)`,
               '-moz-transform': `translateX(${ calculatetranslate(index) }px)`,
               '-o-transform': `translateX(${ calculatetranslate(index) }px)`
               }"
        @click="clickItem(item)" >
        <figure>
          <img :src="item.image_url"/>
        </figure>
      </div>
    </div>

    <div class="focus-item">
    <ul>
      <li v-for="(item, index) in items">
        <div :class="{ active: activeIndex == index }">
        </div>
      </li>
    </ul>
    </div>
  </section>
</template>

<script>
 export default {
   name: 'slider',

   props: {
     items: {
       type: Array,
       required: true
     },
   },

   data () {
     return {
       ready: false,
       width: 0,
       height: 0,

       activeIndex: 0,
       oldActiveIndex: 0,

       timer: 0,
       interval: 3000
     }
   },

   mounted() {
     this.width = screen.width - 4;
     this.height = this.width * .25;
     this.startTimer();
   },

   beforeDestroy() {
     clearInterval(this.timer);
   },

   methods: {
     startTimer() {
       if (this.timer) return;
       this.timer = setInterval(this.play, this.interval);
       if (! this.ready) {
         this.ready = true;
       }
     },

     play() {
       this.oldActiveIndex = this.activeIndex;
       if (this.activeIndex < this.items.length - 1) {
         this.activeIndex ++;
       } else {
         this.activeIndex = 0;
       }
     },

     isAnimating(index) {
       return index == this.oldActiveIndex || index == this.activeIndex;
     },

     calculatetranslate(index) {
       if (Math.abs(this.activeIndex - index) == this.items.length - 1) {
         return this.activeIndex > index ? this.width : -this.width;
       }
       return (index - this.activeIndex) * this.width;
     },

     clickItem(item) {
       window.location.href = item.url;
     },

     delayed(index) {
       return 5 * index;
     }
   }

 }

</script>

<style scoped>
 .slider-section {
   margin: 2px;
   overflow-x: hidden;
   position: relative;
 }
 ul {
   list-style: none;
 }
 .slider img {
   width: auto;
 }
 .item {
   width: 100%;
   height: 100%;
   position: absolute;
   display: inline-block;
   top: 0;
   left: 0;
 }
 .item.active {
 }
 .animating {
   transition: transform 0.4s ease-in-out;
   -webkit-transition: -webkit-transform 0.4s ease-in-out;
   -moz-transition: -moz-transform 0.4s ease-in-out;
   -o-transition: -o-transform 0.4s ease-in-out;
 }
 .focus-item {
   position: absolute;
   bottom: 2%;
   left: 0;
   right: 0;
 }
 .focus-item ul {
   position: relative;
   display: flex;
   justify-content: center;
   align-items: center;
 }
 .focus-item li {
   width: 0.5rem;
   height: 0.5rem;
   border-radius: 50%;
   background-color: #fff;
   margin-right: 0.5rem;
   float: left;
 }
 .focus-item li>div {
   opacity: 0;
   width: 0.5rem;
   height: 0.5rem;
   border-radius: 50%;
   background-color: #00d1b2;
   animation-name: fade;
   animation-timing-function: linear;
   animation-iteration-count: infinite;
 }
 .focus-item li .active {
   opacity: 1;
 }
</style>
