const RolesService = {
    baseUrl: '/api/roles',

    /**
     * Get all roless
     */
    getAll: function() {
        return m.request({
            method: 'GET',
            url: this.baseUrl
        });
    },

    /**
     * Get roles by ID
     */
    getById: function(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`
        });
    },

    /**
     * Create new roles
     */
    create: function(data) {
        return m.request({
            method: 'POST',
            url: this.baseUrl,
            body: data
        });
    },

    /**
     * Update roles
     */
    update: function(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data
        });
    },

    /**
     * Delete roles
     */
    delete: function(id) {
        return m.request({
            method: 'DELETE',
            url: `${this.baseUrl}/${id}`
        });
    },

    /**
     * Search roless
     */
    search: function(query) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/search`,
            params: { q: query }
        });
    }
};

export {RolesService};