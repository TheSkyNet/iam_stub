// Simple DaisyUI-based toast notification and error reporter

// --- Message formatting helpers to prevent "[object Object]" toasts ---
const MAX_MESSAGE_LENGTH = 2000; // keep messages reasonable in UI

function truncate(str, max = MAX_MESSAGE_LENGTH) {
    if (typeof str !== 'string') return '';
    return str.length > max ? str.slice(0, max) + '…' : str;
}

function safeJsonStringify(value) {
    try {
        return JSON.stringify(value, (k, v) => {
            if (v instanceof Error) {
                return { name: v.name, message: v.message, stack: v.stack };
            }
            return v;
        });
    } catch (_) {
        return '';
    }
}

function pickFirstString(obj, keys) {
    if (!obj || typeof obj !== 'object') return undefined;
    for (const key of keys) {
        const val = obj[key];
        if (typeof val === 'string' && val.trim()) return val.trim();
    }
    return undefined;
}

// Extract a human-friendly message from any value (sync version)
function toMessageStringSync(value) {
    if (value == null) return 'An unexpected error occurred';
    if (typeof value === 'string') return value.trim() || 'An unexpected error occurred';

    // Error instance
    if (value instanceof Error) {
        return value.message ? value.message : (value.name || 'Error');
    }

    // Fetch Response-like (sync best-effort)
    if (typeof value === 'object' && typeof value.status === 'number' && typeof value.statusText === 'string') {
        // We cannot synchronously read the body here; show a concise fallback
        return `Request failed: ${value.status} ${value.statusText}`.trim();
    }

    // Common API error shapes
    const direct = pickFirstString(value, ['message', 'error', 'error_description', 'detail', 'title']);
    if (direct) return direct;

    // Nested containers often seen in HTTP libs
    const responseData = value.response && (value.response.data || value.response);
    const nested = pickFirstString(responseData || value.data || {}, ['message', 'error', 'error_description', 'detail', 'title']);
    if (nested) return nested;

    // Arrays of validation errors
    if (Array.isArray(value.errors)) {
        const first = value.errors.find(e => typeof e?.message === 'string')?.message
            || value.errors.find(e => typeof e === 'string');
        if (first) return String(first);
    }

    // Fallback: avoid [object Object]
    const json = safeJsonStringify(value);
    if (json && json !== '{}') return json;

    try { return String(value); } catch { return 'An unexpected error occurred'; }
}

// Attempt to extract message asynchronously (handles Fetch Response bodies)
async function toMessageString(value) {
    try {
        // Response-like (Fetch)
        if (value && typeof value === 'object' && typeof value.status === 'number' && typeof value.text === 'function') {
            // Try JSON body first
            try {
                const clone = typeof value.clone === 'function' ? value.clone() : value;
                const data = await clone.json();
                const msg = toMessageStringSync(data);
                if (msg) return msg;
            } catch (_) {
                // Not JSON, try text
                try {
                    const clone = typeof value.clone === 'function' ? value.clone() : value;
                    const text = await clone.text();
                    if (typeof text === 'string' && text.trim()) return text.trim();
                } catch (_) {}
            }
            // Fallback to status line
            return toMessageStringSync(value);
        }
    } catch (_) {
        // Ignore and use sync fallback
    }
    return toMessageStringSync(value);
}

function ensureToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast toast-top toast-center z-50';
        document.body.appendChild(container);
    }
    return container;
}

export function showToast(message, type = 'error', timeoutMs = 6000) {
    // Accept any value and format to human-readable text safely
    const text = truncate(toMessageStringSync(message));
    const container = ensureToastContainer();
    const alert = document.createElement('div');
    const typeClass = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : type === 'info' ? 'alert-info' : 'alert-error';
    alert.className = `alert ${typeClass} shadow`;

    const span = document.createElement('span');
    span.textContent = text || 'An unexpected error occurred';
    alert.appendChild(span);

    // Close button
    const btn = document.createElement('button');
    btn.className = 'btn btn-ghost btn-xs ml-2';
    btn.textContent = '×';
    btn.setAttribute('aria-label', 'Close');
    btn.onclick = () => alert.remove();
    alert.appendChild(btn);

    container.appendChild(alert);
    setTimeout(() => {
        if (alert.parentNode) alert.remove();
    }, timeoutMs);
}

// Helper that handles Response-like objects and other async-extractable messages
export async function showToastAsync(message, type = 'error', timeoutMs = 6000) {
    const text = truncate(await toMessageString(message));
    return showToast(text, type, timeoutMs);
}

function postError(payload) {
    try {
        fetch('/api/errors', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        }).catch(() => {/* ignore */});
    } catch (e) {
        // ignore
    }
}

export function initErrorHandler() {
    if (window.__errorHandlerInitialized) return;
    window.__errorHandlerInitialized = true;

    window.addEventListener('error', (event) => {
        const message = toMessageStringSync(event.message || event.error || 'Script error');
        const payload = {
            message,
            level: 'error',
            url: window.location.href,
            user_agent: navigator.userAgent,
            context: {
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error && event.error.stack ? String(event.error.stack) : undefined
            }
        };
        showToast(`An error occurred: ${message}`, 'error');
        postError(payload);
    });

    window.addEventListener('unhandledrejection', (event) => {
        const reason = event.reason;
        // Best-effort sync toast first to give immediate feedback without [object Object]
        const quickMessage = toMessageStringSync(reason || 'Unhandled promise rejection');
        showToast(`An error occurred: ${quickMessage}`, 'error');

        // Try to improve with async extraction (e.g., Response body)
        toMessageString(reason).then((finalMessage) => {
            if (finalMessage && finalMessage !== quickMessage) {
                showToast(finalMessage, 'error');
            }
        }).catch(() => {/* ignore */});

        // Prepare payload for server (include stack when present)
        let stack;
        if (reason && typeof reason === 'object' && reason.stack) {
            stack = String(reason.stack);
        }
        const payload = {
            message: quickMessage,
            level: 'error',
            url: window.location.href,
            user_agent: navigator.userAgent,
            context: { stack }
        };
        postError(payload);
    });
}
