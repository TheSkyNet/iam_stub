import m from "mithril";
import {AuthService} from "./AuthserviceService";

class UsersService {
    constructor() {
        this.baseUrl = '/api/users';
    }

    /**
     * Get authorization headers
     */
    getAuthHeaders() {
        return AuthService.getAuthHeaders();
    }

    /**
     * Get all users
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
     * Get user by ID
     */
    getById(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Create new user
     */
    create(data) {
        return m.request({
            method: 'POST',
            url: this.baseUrl,
            body: data,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Update user
     */
    update(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Delete user
     */
    delete(id) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Search users
     */
    search(query) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/search`,
            params: { q: query },
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Assign role to user
     */
    assignRole(userId, roleName) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/${userId}/roles`,
            body: { role: roleName },
            headers: this.getAuthHeaders()
        });
    }

    /**
     * Remove role from user
     */
    removeRole(userId, roleName) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${userId}/roles/${roleName}`,
            headers: this.getAuthHeaders()
        });
    }
}

export {UsersService};