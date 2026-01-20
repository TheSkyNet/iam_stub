import ToastService from '../services/ToastService';

/**
 * Formats various error types into a human-readable string
 * @param {any} value 
 * @returns {string}
 */
export const toMessageStringSync = (value) => {
    if (!value) {
        return 'An unknown error occurred';
    }
    if (typeof value === 'string') {
        return value;
    }
    
    // Handle Error objects
    if (value instanceof Error) {
        return value.message || String(value);
    }

    // Handle Arrays
    if (Array.isArray(value)) {
        return value.map(item => toMessageStringSync(item)).join('; ');
    }

    // Handle API responses
    if (typeof value === 'object' && value !== null) {
        // Try to find a string message in common fields
        let msg = '';
        const possibleKeys = ['message', 'error', 'detail', 'title', 'reason'];
        
        for (const key of possibleKeys) {
            const val = value[key];
            if (val) {
                if (typeof val === 'string') {
                    msg = val;
                    break;
                } else if (typeof val === 'object' && val !== null) {
                    // Try one level deeper
                    const nested = val.message || val.error || val.detail || val.title || val.reason;
                    if (typeof nested === 'string') {
                        msg = nested;
                        break;
                    }
                }
            }
        }
        
        // If we found a message, return it verbatim (as per guidelines)
        if (msg) {
            return String(msg);
        }

        // Handle validation errors only if no top-level message was found
        if (value.errors && typeof value.errors === 'object') {
            let extra = '';
            if (Array.isArray(value.errors)) {
                extra = value.errors
                    .map(e => (typeof e === 'string' ? e : JSON.stringify(e)))
                    .join('; ');
            } else {
                extra = Object.entries(value.errors)
                    .map(([field, errors]) => {
                        const fieldErrors = Array.isArray(errors) ? errors.join(', ') : String(errors);
                        return `${field}: ${fieldErrors}`;
                    })
                    .join('; ');
            }
            
            if (extra) {
                return String(extra);
            }
        }
    }

    // Fallback for Promise rejections that might be Response-like
    if (value && typeof value === 'object' && value.statusText) {
        return `Request failed: ${value.status} ${value.statusText}`;
    }

    try {
        return JSON.stringify(value);
    } catch (e) {
        return String(value);
    }
};

/**
 * Global function to show a toast
 * @param {any} message 
 * @param {string} type 
 */
window.showToast = (message, type = 'info') => {
    const msg = toMessageStringSync(message);
    // Truncate if too long (MAX 2000 chars as per guidelines)
    const truncated = msg.length > 2000 ? msg.substring(0, 1997) + '...' : msg;
    ToastService.show(truncated, type);
};

let isProcessingError = false;

/**
 * Initialize global error listeners
 */
export const initGlobalErrorHandling = () => {
    window.addEventListener('error', (event) => {
        if (isProcessingError) {
            return;
        }
        isProcessingError = true;
        
        try {
            // Skip some known noisy errors if needed
            window.showToast(event.error || event.message, 'error');
            
            // Report to backend if endpoint exists
            reportErrorToBackend({
                message: event.message,
                level: 'error',
                url: window.location.href,
                user_agent: navigator.userAgent,
                context: {
                    stack: event.error ? event.error.stack : null
                }
            });
        } finally {
            // Use a short timeout to reset the flag, allowing consecutive real errors 
            // but blocking immediate recursive ones
            setTimeout(() => { isProcessingError = false; }, 100);
        }
    });

    window.addEventListener('unhandledrejection', (event) => {
        if (isProcessingError) {
            return;
        }
        isProcessingError = true;

        try {
            window.showToast(event.reason, 'error');
            
            reportErrorToBackend({
                message: toMessageStringSync(event.reason),
                level: 'error',
                url: window.location.href,
                user_agent: navigator.userAgent,
                context: {
                    reason: event.reason
                }
            });
        } finally {
            setTimeout(() => { isProcessingError = false; }, 100);
        }
    });
};

/**
 * Reports error to backend API
 * @param {object} payload 
 */
const reportErrorToBackend = (payload) => {
    // We use a simple fetch to avoid circular dependencies or complex auth if it fails
    fetch('/api/errors', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).catch(err => console.warn('Failed to report error to backend:', err));
};
