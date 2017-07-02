// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Axios from 'axios'

import { sync  } from 'vuex-router-sync'
import App from './App'
import router from './routes/index'
import store from './store'

// These are required by FAB
require('./assets/animate.min.css');
require('./assets/icon.css');

Vue.prototype.$http = Axios
Vue.prototype.$http.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest'
};

sync(store, router);

const app = new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app');
