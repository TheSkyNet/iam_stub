import m from "mithril";

class RolesService {
    constructor() {
        this.baseUrl = '/api/roles';
    }

    /**
     * Alias for getAll
     */
    get() {
        return this.getAll();
    }

    /**
     * Get all roles
     */
    getAll() {
        return m.request({
            method: 'GET',
            url: this.baseUrl
        });
    }

    /**
     * Get roles by ID
     */
    getById(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`
        });
    }

    /**
     * Create new roles
     */
    create(data) {
        return m.request({
            method: 'POST',
            url: this.baseUrl,
            body: data
        });
    }

    /**
     * Update roles
     */
    update(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data
        });
    }

    /**
     * Delete roles
     */
    delete(id) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${id}`
        });
    }

    /**
     * Search roles
     */
    search(query) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/search`,
            params: { q: query }
        });
    }
}

export {RolesService};