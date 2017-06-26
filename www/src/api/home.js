import { get, post } from './wrapper'

export const getTopTen = (params = null) => get('topten', params)

export const getTimeLine = (params = null) => get('timeline', params)

export const getProfile = (params = null) => get('user/profile', params)

export const getFavBoards = (level, params = {}) => get(`fav/${level}`, params)
