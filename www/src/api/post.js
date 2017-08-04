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

export const replyArticle = (board, article, params = {}) => post(`article/${board}/post/${article}`, params);
