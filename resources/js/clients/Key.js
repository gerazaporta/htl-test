import { BaseResourceClient } from "./BaseResourceClient";

export class Key extends BaseResourceClient {
    constructor() {
        super('keys');
    }
}