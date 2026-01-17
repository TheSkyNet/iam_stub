import m from 'mithril';

/**
 * Service to manage toast notifications
 */
const ToastService = {
    toasts: [],

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - The type of toast (info, success, warning, error)
     * @param {number} duration - How long to show the toast in ms (default 5000)
     */
    show(message, type = 'info', duration = 5000) {
        const id = Math.random().toString(36).substring(2, 9);
        const toast = { id, message, type };
        
        this.toasts.push(toast);
        m.redraw();

        if (duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, duration);
        }
        
        return id;
    },

    /**
     * Remove a toast by ID
     * @param {string} id 
     */
    remove(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            this.toasts.splice(index, 1);
            m.redraw();
        }
    },

    /**
     * Clear all toasts
     */
    clear() {
        this.toasts = [];
        m.redraw();
    }
};

export default ToastService;
