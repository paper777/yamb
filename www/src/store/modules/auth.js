import { LOGIN, LOGOUT, DECREASE_REFER } from '../mutation-types'
const state = {
  isLogin: false,
  user: {}
};

const mutations = {
  [LOGIN] (state, user) {
    state.isLogin = true;
    state.user = user;
  },

  [LOGOUT] (state, user) {
    state.user = {}
    state.isLogin = false;
  },

  [DECREASE_REFER] (state, type = 'reply') {
    let user = state.user;
    if (type == 'reply') {
      let c = user.new_reply > 0 ? user.new_reply : 1;
      user.new_reply = --c;
    } else if (type == 'at') {
      let c = user.new_at > 0 ? user.new_at : 1;
      user.new_at = --c;
    }
    state.user = user;
  }
};

export default {
  state,
  mutations
};
