import m from "mithril";
import {AuthService} from "./AuthserviceService";

class SettingsService {
    constructor() {
        this.baseUrl = '/api/settings';
    }

    /**
     * Get authorization headers
     */
    getAuthHeaders() {
        return AuthService.getAuthHeaders();
    }

    /**
     * Get all settings
     */
    getAll() {
        return m.request({
            method: 'GET',
            url: this.baseUrl,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Alias for getById
     */
    get(id) {
        return this.getById(id);
    }

    /**
     * Get setting by ID
     */
    getById(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Update setting
     */
    update(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data,
            headers: this.getAuthHeaders()
        });
    }
}

export {SettingsService};
