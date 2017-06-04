import axios from 'axios';
import router from '../routes'

axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

let base = '/n/b';

export const get = (url, params) => {
    return axios.get(`${base}/${url}.json`, { params: params })
        .then(res => res.data)
        .catch((error) => {
            if (error.response) {
                switch (error.response.status) {
                    case 401:
                        router.push('/login');
                        break;
                    default: console.log(error.response);
                }
            } else {
                console.log(error);
            }
        });
};

export const post = (url, params) => {
    const formData = new FormData();
    Object.keys(params).forEach(key => formData.append(key, params[key]));
    return axios.post(`${base}/${url}.json`, formData)
        .then(res => res.data)
        .catch((error) => {
            if (error.response) {
                switch (error.response.status) {
                    case 401:
                        Vue.$router.push('/login');
                        break;
                    default: console.log(error.response);
                }
            } else {
                console.log(error);
            }
        });
};
