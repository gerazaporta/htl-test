import {APIClient} from "./APIClient";

export class BaseResourceClient {
    apiClient;
    constructor(endpoint) {
        this.apiClient = new APIClient(endpoint);
    }

    getOne(id) {
        return this.apiClient.get(id);
    }

    list(query = {}) {
        return this.apiClient.get();
    }
}