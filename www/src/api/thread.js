import { get, post } from './wrapper'

export const getThread = (board, id, params) => get(`article/${board}/${id}`, params)
export const voteup = (board, id, params = {}) => post(`article/${board}/voteup/${id}`, params)
export const votedown = (board, id, params = {}) => post(`article/${board}/votedown/${id}`, params)
