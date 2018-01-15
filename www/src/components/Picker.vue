<template>
  <transition name="picker">
    <div class="picker-wrapper" v-show="visible">
      <div class="picker-container">
        <div class="picker-header">
          <slot name="header"></slot>
        </div>
        <div class="picker-body" @click.stop>
          <i class="border-bottom-1px"></i>
          <i class="border-top-1px"></i>
          <div class="picker-wheel" ref="pickerWheel">
            <ul class="wheel-scroll">
              <li class="wheel-item" v-for="(item, index) in dataItems" :key="index">{{ item.label }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
 import BScroll from 'better-scroll'

 const NAME = 'picker'
 const EVENT_CHANGE = 'change'

 export default {
   name: NAME,

   data () {
     return {
       visible: false,
       selectedIndex: null,
       dataItems: [],
       wheel: null
     }
   },

   props: {
     data: {
       type: Array,
       required: true
     }
   },

   watch: {
     data(newData) {
       this.setData(newData)
     }
   },

   methods: {
     show() {
       if (this.visible) {
         return true
       }

       this.visible = true

       if (! this.wheel) {
         this.wheel = this._createWheel(this.$refs.pickerWheel)
       } else {
         this.wheel.enable()
       }
     },

     hide() {
       if (! this.visible) {
         return true
       }

       this.visible = false

       this.wheel.disable()

     },

     canConfirm() {
       return this.wheel && ! this.wheel.isInTransition
     },

     setData(data) {
       this.dataItems = data
       if (this.visible) {
         this.$nextTick(() => {
           this.wheel.refresh()
         })
       }
     },

     _createWheel(wrapper) {
       const wheel = new BScroll(wrapper, {
         wheel: {
           selectedIndex: 0
         },
         //observeDOM: false
       })

       wheel.on('scrollEnd', () => {
         let v = this.dataItems[wheel.getSelectedIndex()]
         this.$emit(EVENT_CHANGE, v)
       })

       return wheel
     }
   }
 }
</script>

<style scoped>
 .picker-enter .picker-leave-to {
   opacity: 0;
 }

 .picker-enter-active {
   transition: all 0.3 ease-in-out;
 }

 .picker-wrapper {
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   z-index: 999;
   background-color: rgba(37, 38, 45, .4);
 }

 .picker-container {
   position: absolute;
   left: 0;
   bottom: 0;
   width: 100%;
   z-index: 600;
   height: 273px;
   background-color: white;
 }

 .picker-header {
   position: relative;
   height: 60px;
 }

 .picker-body {
   padding: 0 1rem;
   position: relative;
   top: 20px;
 }

 .picker-body > i {
   position: absolute;
   z-index: 10;
   left: 0;
   width: 100%;
   height: 68px;
   pointer-events: none;
   transform: translateZ(0);
 }
 .picker-body > i.border-bottom-1px {
   top: 0;
   background: linear-gradient(to top, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8))
 }

 .picker-body > i.border-top-1px {
   bottom: 0;
   background: linear-gradient(to bottom, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8))
 }

 .picker-wheel {
   height: 173px;
   padding: 0;
   overflow: hidden;
 }

 .wheel-scroll {
   margin-top: 68px;
   line-height: 36px;
 }

 .wheel-scroll > li {
   height: 36px;
   overflow: hidden;
   white-space: nowrap;
   font-size: 20px;
 }

 .border-top-1px:before {
   content: "";
   display: block;
   border-top: 1px solid #ebebeb;
   position: absolute;
   width: 100%;
   left: 0;
   top: 0;
 }

.border-top-1px:after {
   content: "";
   display: block;
 }
.border-bottom-1px:before {
   content: "";
   display: block;
 }

 .border-bottom-1px:after {
   content: "";
   display: block;
   position: absolute;
   width: 100%;
   border-top: 1px solid #ebebeb;
   left: 0;
   bottom: 0;
 }
</style>
