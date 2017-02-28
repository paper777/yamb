import VueRouter from 'vue-router'
import Vue from 'vue'

Vue.use(VueRouter)

import Home from 'views/Home'
import Auth from 'views/Auth'

import store from '../store'


const router = new VueRouter({
  mode: 'history',
  base: '/paper/nforum/n/',
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

    //if (to.meta && to.meta.requireLogin && ! isLogin) {
    //    return next('/login');
    //}
    next();
});


export default router
