import { get, post } from './wrapper';

export const addAttachment = (board, file, articleId = null) => {
  let url = `attachment/${board}/add`;
  if (articleId) {
    url += `/${articleId}`;
  }

  return post(url, { file });
};

export const deleteAttachment = (board, name, articleId = null) => {
  let url = `attachment/${board}/delete`;
  if (articleId) {
    url += `/${articleId}`;
  }

  return post(url, { name });
};

export const newArticle = (board, params = {}) => post(`article/${board}/post`, params);
export const replyArticle = (board, id, params = {}) => post(`article/${board}/post/${id}`, params);
export const editArticle = (board, id, params = {}) => post(`article/${board}/edit/${id}`, params);

export const prePost = (board, params = {}) => post(`article/${board}/prepost`, params);
export const preReply = (board, id, params = {}) => post(`article/${board}/prereply/${id}`, params);
export const getEdit = (board, id, params = {}) => get(`article/${board}/predit/${id}`, params);
