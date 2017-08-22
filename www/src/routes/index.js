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

import Mail from 'views/Mail/Mail'
import MailList from 'views/Mail/MailList'
import MailContent from 'views/Mail/MailContent'

import Refer from 'views/Refer'

import User from 'views/User'

import Post from 'views/Post'

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
      component: Board,
      meta: {
        requireLogin: true
      }
    },

    {
      name: 'post',
      path: '/post',
      component: Post,
      meta: {
        requireLogin: true
      }
    },

    {
      name: 'mail',
      path: '/mail',
      component: Mail,
      meta: {
        requireLogin: true
      },
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
      name: 'reply',
      path: '/reply',
      component: Refer,
      meta: {
        requireLogin: true
      }
    },

    {
      name: 'at',
      path: '/at',
      component: Refer,
      meta: {
        requireLogin: true
      }
    },

    {
      name: 'user',
      path: '/user/:id',
      component: User,
      meta: {
        requireLogin: true
      }
    },

    {
      name: '404',
      path: '/404',
      component: {
        template: '<div> 啊，这是哪儿？</div>'
      }
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
