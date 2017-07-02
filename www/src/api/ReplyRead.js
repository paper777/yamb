import { get, post } from './wrapper'

export const getReplyRead = (id, params) => get(`refer/reply/${id}`, params)
