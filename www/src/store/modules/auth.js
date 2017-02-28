import { LOGIN, LOGOUT } from '../mutation-types'
const state = {
    isLogin: false,
    user: {}
}

const mutations = {
    [LOGIN] (state, user) {
        state.isLogin = true;
        state.user = user;
    },

    [LOGOUT] (state, user) {
        state.user = {}
        state.isLogin = false;
    }
}

export default {
    state,
    mutations
}
