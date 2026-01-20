import m from "mithril";
import { AuthService } from "./AuthserviceService";

class LMSService {
    constructor() {
        this.baseUrl = '/api/lms';
    }

    getStatus() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/status`,
            headers: AuthService.getAuthHeaders()
        });
    }

    refresh() {
        return m.request({
            method: "POST",
            url: `${this.baseUrl}/refresh`,
            headers: AuthService.getAuthHeaders()
        });
    }

    test(integration, prompt) {
        return m.request({
            method: "POST",
            url: `${this.baseUrl}/test`,
            body: { integration, prompt },
            headers: AuthService.getAuthHeaders()
        });
    }
}

export { LMSService };
