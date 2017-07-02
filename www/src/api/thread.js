import { get, post } from './wrapper'

export const getThread = (board, id, params) => get(`article/${board}/${id}`, params)
export const voteup = (board, params) => post(`article/${board}/voteup`, params)
