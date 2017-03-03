import { get, post } from './wrapper'

export const getThread = (board, id, params) => get(`article/${board}/${id}`, params)
