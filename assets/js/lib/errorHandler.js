import ToastService from '../services/ToastService';

/**
 * Formats various error types into a human-readable string.
 * This is the central formatter that SHOULD be used by everyone.
 * KISS: Keep it simple. Prefer the backend's 'message' field.
 * 
 * @param {any} value 
 * @returns {string}
 */
export const toMessageStringSync = (value) => {

    console.log('toMessageStringSync', value);
    debugger;
    if (value === null || value === undefined) {
        return 'An unknown error occurred';
    }
    
    if (typeof value === 'string') {
        return value;
    }
    
    // Handle Error objects
    if (value instanceof Error) {
        return value.message || String(value);
    }

    // Handle common API response shapes
    if (typeof value === 'object') {
        // Priority 1: Backend provided 'message' (from aAPI::dispatchError)
        if (typeof value.message === 'string' && value.message.trim() !== '') {
            return value.message;
        }

        // Priority 2: Other common string error fields
        const otherKeys = ['error', 'detail', 'title', 'reason', 'msg', 'response', 'data'];
        for (const key of otherKeys) {
            const val = value[key];
            if (typeof val === 'string' && val.trim() !== '') {
                return val;
            }
        }

        // Priority 3: Mithril/XHR responseText
        if (value.responseText && typeof value.responseText === 'string') {
            try {
                const parsed = JSON.parse(value.responseText);
                return toMessageStringSync(parsed);
            } catch (e) {
                return value.responseText;
            }
        }

        // Priority 4: If it has validation errors, try to extract something
        if (value.errors) {
            if (Array.isArray(value.errors)) {
                const first = value.errors[0];
                if (first) {
                    if (typeof first === 'string') {
                        return first;
                    }
                    if (typeof first.message === 'string') {
                        return first.message;
                    }
                }
            }
            if (typeof value.errors === 'object' && value.errors !== null) {
                const firstVal = Object.values(value.errors)[0];
                if (typeof firstVal === 'string') {
                    return firstVal;
                }
                if (firstVal && typeof firstVal.message === 'string') {
                    return firstVal.message;
                }
                if (Array.isArray(firstVal) && typeof firstVal[0] === 'string') {
                    return firstVal[0];
                }
            }
        }
    }

    // Fallback: Avoid [object Object] if possible by using JSON stringify
    try {
        const str = JSON.stringify(value);
        if (str && str !== '{}' && str !== '[]') {
            return str;
        }
    } catch (e) {}

    const fallback = String(value);
    return fallback === '[object Object]' ? 'An unexpected error occurred' : fallback;
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
