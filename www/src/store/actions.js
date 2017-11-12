import * as types from './mutation-types'

export const login = ({ commit }, user) => commit(types.LOGIN, user);

export const logout = ({ commit }, user) => commit(types.LOGOUT);

export const decreaseRefer = ({ commit }, type) => commit(types.DECREASE_REFER, type);
