import axios from "axios";

export class APIClient {
    base_url;
    endpoint;
    constructor(endpoint) {
        this.base_url = 'http://localhost:80/api';
        this.endpoint = endpoint
    }

    post(endpoint = '', data) {
        return axios.post(this.base_url + '/' + this.endpoint + (endpoint ? ('/' + endpoint) : '') , data)
            .catch((error) => { console.log(error); });
    }

    put(endpoint = '', data) {
        return axios.put(this.base_url + '/' + this.endpoint + (endpoint ? ('/' + endpoint) : '') , data)
            .catch((error) => { console.log(error); });
    }

    delete(endpoint = '') {
        return axios.delete(this.base_url + '/' + this.endpoint + (endpoint ? ('/' + endpoint) : ''))
            .catch((error) => { console.log(error); });
    }

    get(endpoint = '') {
        return axios.get(this.base_url + '/' + this.endpoint + (endpoint ? ('/' + endpoint) : ''))
            .catch((error) => { console.log(error); });
    }
}
