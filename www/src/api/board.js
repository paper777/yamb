import { get, post } from './wrapper'

export const getBoard = (board, params) => get(`board/${board}`, params)
