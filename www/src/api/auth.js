import { get, post } from './wrapper'

export const login = (username, password) => post('auth/login', {username, password})
export const logout = (username, password) => get('auth/logout')
