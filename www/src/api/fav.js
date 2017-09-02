import { get, post } from './wrapper'

export const opFav = (params = null) => post('fav/op', params)
