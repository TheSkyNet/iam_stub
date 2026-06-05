
/**
 * Formats various error types into a human-readable string.
 * This is the central formatter that SHOULD be used by everyone.
 * KISS: Keep it simple. Prefer the backend's 'message' field.
 * NEVER show [object Object] or JSON dumps in the UI.
 * 
 * @param {any} value 
 * @returns {string}
 */
export const toMessageStringSync = (value) => {
    // KISS - Keep it simple, stupid. No more complex recursion or JSON dumps in the UI.
    if (value === null || value === undefined) {
        return 'An unexpected error occurred';
    }

    // 1. If it's already a string, use it (unless it's just technical noise like [object Object])
    if (typeof value === 'string') {
        const lower = value.toLowerCase();
        if (lower.includes('[object ') || lower === 'undefined' || lower === 'null') {
            return 'An unexpected error occurred';
        }
        return value;
    }

    // 2. If it's an object, try to extract a human-readable message
    if (typeof value === 'object') {
        // Mithril.js response object or standard JS Error
        const response = value.response || value;
        
        // Priority for our backend standard: { message: "..." }
        if (typeof response.message === 'string' && response.message.trim() !== '') {
            return response.message;
        }

        // Validation errors usually have a first error we can show
        if (response.errors) {
            const first = Array.isArray(response.errors) ? response.errors[0] : Object.values(response.errors)[0];
            if (typeof first === 'string') return first;
            if (Array.isArray(first) && typeof first[0] === 'string') return first[0];
            if (first && typeof first === 'object' && typeof first.message === 'string') return first.message;
        }

        // Other common fields in order of preference
        const commonFields = ['error', 'detail', 'msg', 'title', 'statusText', 'status_message'];
        for (const field of commonFields) {
            if (typeof response[field] === 'string' && response[field].trim() !== '' && !response[field].includes('[object ')) {
                return response[field];
            }
        }
        
        // Handle HTTP status if available
        if (typeof value.status === 'number' && value.status > 0) {
            return `Error ${value.status}: ${value.statusText || 'Request failed'}`;
        }
    }

    // 3. Final fallback: never show [object Object] or JSON dumps in the UI.
    return 'An unexpected error occurred';
};

/**
 * Global function to show a notification (fallback to console)
 * @param {any} message 
 * @param {string} type 
 */
window.showToast = (message, type = 'info') => {
    const msg = toMessageStringSync(message);
    // Truncate if too long (MAX 2000 chars as per guidelines)
    const truncated = msg.length > 2000 ? msg.substring(0, 1997) + '...' : msg;
    console.log(`[${type.toUpperCase()}] ${truncated}`);
};

// Also make the formatter available globally
window.formatError = toMessageStringSync;

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
            const message = event.message || (event.error ? event.error.message : '');
            
            // Skip some known noisy or non-critical errors
            const noisyPatterns = [
                'Request for font',
                'blocked at visibility level',
                'mozOrientation is deprecated',
                'onmozorientationchange is deprecated',
                'Partitioned cookie or storage access',
                'm.stripe.network',
                'js.stripe.com',
                'hcaptcha.com',
                'newassets.hcaptcha.com',
                'Content-Security-Policy',
                'script-src-elem',
                'script-src \'self\'',
                'sha256-',
                'Report-Only policy',
                'hcaptcha-invisible',
                'elements-inner-card',
                'universal-link-modal-inner',
                'elements-inner-link-button-for-card',
                'reflow.js',
                'utils.js', // hCaptcha/Stripe internal
                'URL constructor: is not a valid URL',
                'Source map error',
                'Script error.', // Usually cross-origin errors with no detail
                'stripe-js-v3', // Internal Stripe script
                'stripe.com',
                'stripecdn.com',
                'hcaptcha-checkbox',
                'hcaptcha-challenge'
            ];

            if (noisyPatterns.some(pattern => message && message.includes(pattern))) {
                return;
            }

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
            const reasonString = toMessageStringSync(event.reason);
            
            // Skip some known noisy or non-critical errors
            const noisyPatterns = [
                'Request for font',
                'blocked at visibility level',
                'm.stripe.network',
                'js.stripe.com',
                'hcaptcha.com',
                'newassets.hcaptcha.com',
                'Content-Security-Policy',
                'script-src-elem',
                'script-src \'self\'',
                'sha256-',
                'Report-Only policy',
                'hcaptcha-invisible',
                'elements-inner-card',
                'universal-link-modal-inner',
                'elements-inner-link-button-for-card',
                'reflow.js',
                'utils.js',
                'URL constructor: is not a valid URL',
                'Source map error',
                'Script error.',
                'stripe-js-v3',
                'stripe.com',
                'stripecdn.com',
                'hcaptcha-checkbox',
                'hcaptcha-challenge'
            ];

            if (noisyPatterns.some(pattern => reasonString && reasonString.includes(pattern))) {
                return;
            }

            window.showToast(event.reason, 'error');
            
            reportErrorToBackend({
                message: reasonString,
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

    // Quietly ignore CSP violations - they are almost always from third-party iframes
    window.addEventListener('securitypolicyviolation', (event) => {
        // We log them to console for developers but don't show toasts or report to backend
        console.debug('CSP Violation ignored:', event.violatedDirective, event.blockedURI);
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
