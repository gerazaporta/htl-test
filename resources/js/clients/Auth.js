import { APIClient } from "./APIClient";

export class Auth {
    
    constructor() {
        this.apiClient = new APIClient('login');
    }

    login(credentials) {
        return this.apiClient.post('', {
            email: credentials.email,
            password: credentials.password,
        });
    }
}
