// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import Axios from 'axios';

import { sync  } from 'vuex-router-sync';
import App from './App';
import router from './routes/index';
import store from './store';

import Toast from './plugins/toast';

Vue.use(Toast);

Vue.prototype.$http = Axios;
Vue.prototype.$http.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
};

sync(store, router);

router.afterEach((to, from, next) => {
  if (_hmt != false) { // Do not use `if (_hmt)`! JavaScript MAGIC!  - Henryzhao
      if (to.path) {
          _hmt.push(['_trackPageview', '/n' + to.fullPath]);
      }
  }
});

const app = new Vue({
  router,
  store,
  beforeCreate: () => {
    // if the request is from nforum Index,
    // transform the hash to a request url
    // and try to redirect to it's page
    let hash = window.location.hash;
    if (hash.startsWith('#!')) {
      const url = '/' + hash.substring(2);
      router.push(url);
    }
  },
  render: h => h(App)
}).$mount('#app');
