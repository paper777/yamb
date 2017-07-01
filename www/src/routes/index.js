import VueRouter from 'vue-router'
import Vue from 'vue'

Vue.use(VueRouter)

import Home from 'views/Home'
import Auth from 'views/Auth'
import Thread from 'views/Thread'
import Board from 'views/Board'
import FavBoard from 'views/FavBoard'
import Topten from 'views/Topten'
import Profile from 'views/Profile'
import Timeline from 'views/Timeline'
import Mail from 'views/mail/Mail'
import MailList from 'views/mail/MailList'
import MailContent from 'views/mail/MailContent'

import store from 'store'


const router = new VueRouter({
  mode: 'history',
  base: '/n/',
  routes: [
    {
      name: 'home',
      path: '/home',
      component: Home,
      meta: {
        requireLogin: true
      },

      children: [
        {
          name: 'topten',
          path: 'topten',
          component: Topten
        },
        {
          name: 'fav',
          path: 'fav',
          component: FavBoard
        },
        {
          name: 'timeline',
          path: 'timeline',
          component: Timeline
        },
        {
          name: 'profile',
          path: 'profile',
          component: Profile
        },

      ]
    },

    {
      name: 'auth',
      path: '/login',
      component: Auth
    },

    {
      name: 'thread',
      path: '/article/:board/:id',
      component: Thread
    },

    {
      name: 'board',
      path: '/board/:board/',
      component: Board
    },

    {
      name: 'mail',
      path: '/mail',
      component: Mail,
      children: [
        {
          name: 'mailContent',
          path: ':type/show/:num',
          component: MailContent,
        },
        {
          name: 'mailList',
          path: ':type',
          component: MailList
        }
      ]
    },

    {
      path: '*',
      redirect: '/home/topten'
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
