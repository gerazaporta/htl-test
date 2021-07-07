import { BaseResourceClient } from "./BaseResourceClient";

export class Technician extends BaseResourceClient {
    constructor() {
        super('technicians');
    }
}