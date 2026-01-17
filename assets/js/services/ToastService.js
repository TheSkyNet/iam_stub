import m from "mithril";

/**
 * Service for managing toast notifications
 */
const ToastService = {
    toasts: [],
    
    /**
     * Add a new toast
     * @param {string} message - The message to display
     * @param {string} type - success, info, warning, error
     * @param {number} timeoutMs - auto-remove timeout in ms
     */
    add: function(message, type = 'error', timeoutMs = 6000) {
        const id = Math.random().toString(36).slice(2, 9);
        this.toasts.push({ id, message, type });
        
        // Redraw to show the new toast
        m.redraw();
        
        if (timeoutMs > 0) {
            setTimeout(() => this.remove(id), timeoutMs);
        }
        return id;
    },
    
    /**
     * Remove a toast by id
     * @param {string} id 
     */
    remove: function(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
        m.redraw();
    }
};

export { ToastService };
