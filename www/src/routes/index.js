import VueRouter from 'vue-router'
import Vue from 'vue'

Vue.use(VueRouter)

import Home from 'views/Home'
import Auth from 'views/Auth'
import Thread from 'views/Thread'
import Board from 'views/Board'

import store from 'store'


const router = new VueRouter({
  mode: 'history',
  base: '/n/',
  routes: [
    {
      name: 'Home',
      path: '/',
      component: Home,
      meta: {
        requireLogin: true
      }
    },

    {
      name: 'Auth',
      path: '/login',
      component: Auth
    },

    {
      name: 'Thread',
      path: '/article/:board/:id',
      component: Thread
    },

    {
      name: 'Board',
      path: '/board/:board/',
      component: Board
    },

    {
      path: '*',
      redirect: '/' 
    }
  ]
});



router.beforeEach((to, from, next) => {
  const isLogin = store.state.auth.isLogin;
  if (to.name == "Auth" && isLogin) {
    return next('/');
  }

  next();
});


export default router
