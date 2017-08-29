<template>
  <section
    class="slider-section"
    :style="{
           width: `${width}px`,
           height: `${height}px`
           }">
    <ul
      class="slider"
      :style="{
             'animation-duration': `${items.length * hangonTime}s`
             }">
      <li class="item" v-for="(item, index) in items" @click="clickItem(item)">
        <figure>
          <img
            :src="item.image_url"
            :style="{
                  width: `${width}px`,
                  height: `${height}px`
                  }"/>
        </figure>
        <div class="focus-item"></div>
      </li>
    </ul>
    <div class="focus-item">
    <ul>
      <li v-for="(item, index) in items">
        <div
          :style="{
                  'animation-duration': `${items.length * hangonTime}s`,
                  'animation-delay': `${index * hangonTime}s`
                  }">

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
       hangonTime: 4,
       width: 0,
       height: 0,
     }
   },

   mounted() {
     this.width = screen.width - 4;
     this.height = this.width * .25;
   },

   methods: {
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
   overflow: hidden;
   position: relative;
 }
 ul {
   list-style: none;
 }
 .slider {
   width: 300%;
   height: 100%;
   margin-left: 0;
   animation-name: slide;
   animation-timing-function: ease-out;
   animation-iteration-count: infinite;
 }
 .item {
   float: left;
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

 @-webkit-keyframes fade {
  0%   { opacity: 1 }
  23%  { opacity: 1 }
  33%  { opacity: 0 }
  56%  { opacity: 0 }
  66%  { opacity: 0 }
  90%  { opacity: 0 }
  100% { opacity: 1 }
 }

 @-webkit-keyframes slide {
   0% { margin-left: 0; }
   23% { margin-left: 0; }
   33% { margin-left: calc(-100%) }
   56% { margin-left: calc(-100%) }
   66% { margin-left: calc(-200%) }
   90% { margin-left: calc(-200%) }
   100% { margin-left: 0; }
 }
</style>
