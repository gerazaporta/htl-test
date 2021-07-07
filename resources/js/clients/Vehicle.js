import { BaseResourceClient } from "./BaseResourceClient";

export class Vehicle extends BaseResourceClient {
    constructor() {
        super('vehicles');
    }
}