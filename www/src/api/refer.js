import { get, post } from './wrapper'

export const getRefer = (type, params = null) => get('refer/' + type, params)

export const getAt = (params = null) => get('refer/at' , params)

export const setRead = (type, params = null) => get('refer/' + type + '/read' , params)
