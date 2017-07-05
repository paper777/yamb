import { get, post } from './wrapper'

/**
 * Get mail list
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param params - could be null
 */
export const getMailList = (type, params) => get(`mail/${type}`, params);
export const getInboxMailList = (params) => get(`mail/inbox`, params);
export const getOutboxMailList = (params) => get(`mail/outbox`, params);
export const getDeletedMailList = (params) => get(`mail/deleted`, params);

/**
 * Get mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - could be null
 */
export const getMail = (type, num, params) => get(`mail/${type}/show/${num}`);

/**
 * Send a mail
 * @param params - { title: string, content: string, id: target account id }
 */
export const sendMail = (params) => post(`mail/send`, params);

/**
 * Reply a mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - { title: string, content: string, num: int }
 */
export const replyMail = (type, num, params) => post(`mail/${type}/send/${num}`, params);

/**
 * Forward a mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - { id: (the account id of receiver) }
 */
export const forwardMail = (type, num, params) => post(`mail/${type}/forward/${num}`, params);

/**
 * Delete a mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - could be null
 */
export const deleteMail = (type, num, params) => post(`mail/${type}/delete/${num}`, params);
