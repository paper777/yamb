<template>
  <section class="toast-section">
    <div class="toast" :class="[visible ? 'fade-in' : 'fade-out']">
      <p>{{ message }}</p>
    </div>
  </section>
</template>

<script>
export default {
  name: 'toast',

  data () {
    return {
      visible: false,
      message: ''
    }
  }
}
</script>

<style scoped>
  @keyframes fade-in {
    0%   { opacity: 0; transform: scale(0.7); }
    100% { opacity: 1; transform: scale(1); }
  }

  @keyframes fade-out {
    0%   { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(0.7); }
  }

  .toast-section {
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    z-index: 5000 !important;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .toast-section .fade-in {
      animation-name: fade-in;
      animation-duration: 0.3s;
      animation-fill-mode: both;
  }

  .toast-section .fade-out {
      animation-name: fade-out;
      animation-duration: 0.3s;
      animation-fill-mode: both;
  }

  .toast-section .toast {
      width: 12rem;
      height: 4rem;
      position: relative;
      background: rgba(0, 0, 0, 0.6);
      border-radius: 8px;
      overflow: hidden;
      line-height: 2rem;
      text-align: center;
  }

  .toast .icon{
      font-size: 1.2rem;
      color: #fff;
  }

  .toast p {
      font-size: 1.25rem;
      line-height: 3.5rem;
      color: #fff;
  }
</style>
