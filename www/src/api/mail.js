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
 * @param params - { title: string, content: string, id: int }
 */
export const sendMail = (params) => post(`mail/send`);

/**
 * Reply a mail
 * @param params - { type: string , title: string, content: string, id: int }
 */
export const replyMail = (params) => post(`mail/send`);

/**
 * Forward a mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - {id: (the account id of receiver)}
 */
export const forwardMail = (type, num, params) => post(`mail/${type}/forward/${num}`);

/**
 * Delete a mail
 * @param type - one of ['inbox', 'outbox', 'deleted']
 * @param num - the mail num (id)
 * @param params - could be null
 */
export const deleteMail = (type, num, params) => post(`mail/${type}/delete/${num}`);
