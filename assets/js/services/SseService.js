// Simple EventSource wrapper with DaisyUI toasts and cleanup

export class SseConnection {
    constructor(url, { withCredentials = false, headers = {} } = {}) {
        this.url = url;
        this.withCredentials = withCredentials;
        this.headers = headers;
        this.es = null;
        this.listeners = [];
        this.onOpen = null;
        this.onError = null;
        this.closed = false;
    }

    start() {
        if (this.es) return this;
        // Some environments support EventSource via polyfills with headers; native doesn't allow custom headers
        const ES = window.EventSource || window.EventSourcePolyfill;
        this.es = new ES(this.url, { withCredentials: this.withCredentials, headers: this.headers });
        this.es.onopen = (e) => {
            if (typeof this.onOpen === 'function') this.onOpen(e);
        };
        this.es.onerror = (e) => {
            const statusText = (e && e.status && e.statusText) ? `${e.status} ${e.statusText}` : 'connection error';
            if (typeof window.showToast === 'function') {
                window.showToast(`SSE error: ${statusText}`, 'error');
            }
            if (typeof this.onError === 'function') this.onError(e);
        };
        // Re-attach any buffered listeners
        this.listeners.forEach(({ event, cb }) => this.es.addEventListener(event, cb));
        return this;
    }

    listen(event, cb) {
        this.listeners.push({ event, cb });
        if (this.es) this.es.addEventListener(event, cb);
        return this;
    }

    close() {
        if (this.es && !this.closed) {
            try { this.es.close(); } catch (_) {}
            this.closed = true;
        }
        return this;
    }
}

export const SseService = {
    clock({ count = 10, interval = 1000, retry = 2000 } = {}) {
        const params = new URLSearchParams({ count: String(count), interval: String(interval), retry: String(retry) });
        const url = `/api/sse/clock?${params.toString()}`;
        return new SseConnection(url).start();
    },
    echo(message = 'Hello from SSE!', { retry = 2000 } = {}) {
        const params = new URLSearchParams({ message, retry: String(retry) });
        const url = `/api/sse/echo?${params.toString()}`;
        return new SseConnection(url).start();
    },
    from(url) { return new SseConnection(url).start(); }
};
