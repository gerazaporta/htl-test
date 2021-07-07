import { BaseResourceClient } from "./BaseResourceClient";

export class Order extends BaseResourceClient {
    constructor() {
        super('orders');
    }

    create(data) {
        return this.apiClient.post('', data);
    }
    
    update(id, data) {
        return this.apiClient.put(id, data);
    }

    delete(id) {
        return this.apiClient.delete(id);
    }
}