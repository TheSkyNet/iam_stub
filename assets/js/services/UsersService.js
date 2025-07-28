const {AuthService} = require("./AuthserviceService");

const UsersService = {
    baseUrl: '/api/users',

    /**
     * Get authorization headers
     */
    getAuthHeaders: function() {
        return AuthService.getAuthHeaders();
    },

    /**
     * Get all users
     */
    getAll: function() {
        return m.request({
            method: 'GET',
            url: this.baseUrl,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Get user by ID
     */
    getById: function(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Create new user
     */
    create: function(data) {
        return m.request({
            method: 'POST',
            url: this.baseUrl,
            body: data,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Update user
     */
    update: function(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Delete user
     */
    delete: function(id) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Search users
     */
    search: function(query) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/search`,
            params: { q: query },
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Assign role to user
     */
    assignRole: function(userId, roleName) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/${userId}/roles`,
            body: { role: roleName },
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Remove role from user
     */
    removeRole: function(userId, roleName) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${userId}/roles/${roleName}`,
            headers: this.getAuthHeaders()
        });
    }
};

export {UsersService};