import Vuex from 'vuex'
import Vue from 'vue'

import * as actions from './actions'
import * as getters from './getters'

import auth from './modules/auth'

Vue.use(Vuex);

const store = new Vuex.Store({
    actions,
    getters,
    modules: {
        auth
    },  
    state: { },  
    mutations: { }
});
export default store;
