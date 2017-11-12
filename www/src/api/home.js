import { get, post } from './wrapper'

export const getTopTen = (params = null) => get('home/topten', params)

export const getBanners = (params = null) => get('banner', params)

export const getTimeline = (params = null) => get('home/timeline', params)

export const getReply = (params = null) => get('refer/reply' , params)

export const getAt = (params = null) => get('refer/at' , params)

export const getReplyRead = (params = null) => get('refer/reply' , params)

export const getProfile = (params = null) => get('user/profile', params)

export const getUser = (id, params = null) => get('user/query/' + id, params)

export const getFavBoards = (level, params = {}) => get(`home/fav/${level}`, params)

export const getSections = (params = {}) => get('section', params)

export const searchBoards = (params = { name: null }) => get(`section/${params.name}`)

export const getBackToNforum = (params = { name: null }) => get(`back-to-nforum`)
