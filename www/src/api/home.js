import { get, post } from './wrapper'

export const getTopTen = (params = null) => get('topten', params)

export const getTimeline = (params = null) => get('timeline', params)

export const getReply = (params = null) => get('refer/reply' , params)

export const getAt = (params = null) => get('refer/at' , params)

export const getReplyRead = (params = null) => get('refer/reply' , params)

export const getProfile = (params = null) => get('user/profile', params)

export const getFavBoards = (level, params = {}) => get(`fav/${level}`, params)
