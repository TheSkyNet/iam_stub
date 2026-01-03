// Error Services Test Page (DaisyUI + Mithril)
// - Lives in pages/ (not in components/) per request
// - Exercises central error handler and /api/errors endpoint

export const ErrorServicesTestPage = {
    oninit() {
        this.last = null;
        this.working = false;
    },
    setLast(obj) {
        this.last = obj;
        m.redraw();
    },
    postSampleError() {
        this.working = true;
        const payload = {
            message: 'Test error from ErrorServicesTestPage',
            level: 'error',
            url: window.location.href,
            user_agent: navigator.userAgent,
            context: { source: 'errors-test-page', time: new Date().toISOString() }
        };
        fetch('/api/errors', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        }).then(async (res) => {
            this.working = false;
            try {
                const data = await res.json();
                this.setLast({ status: res.status, data });
                if (window.showToast) window.showToast('Posted sample error to server', 'info');
            } catch (_) {
                this.setLast({ status: res.status, text: await res.text().catch(() => '') });
            }
        }).catch((err) => {
            this.working = false;
            this.setLast({ error: String(err && err.message || err) });
            if (window.showToast) window.showToast('Failed to send sample error', 'error');
        });
    },
    callFailingApi() {
        fetch('/api/does-not-exist', { headers: { 'Accept': 'application/json' }})
            .then(async (res) => {
                // Will likely be 404 JSON per notFound handler
                this.setLast({ status: res.status, statusText: res.statusText });
                if (window.showToastAsync) await window.showToastAsync(res, 'error');
            })
            .catch((err) => {
                this.setLast({ error: err && err.message ? err.message : String(err) });
                if (window.showToast) window.showToast(err, 'error');
            });
    },
    throwJsError() {
        setTimeout(() => { throw new Error('Demo JS error from Errors Test Page'); }, 0);
        if (window.showToast) window.showToast('Scheduled a JS error to test global handler…', 'warning');
    },
    unhandledRejection() {
        setTimeout(() => { Promise.reject(new Error('Demo unhandled promise rejection')); }, 0);
        if (window.showToast) window.showToast('Scheduled an unhandled rejection…', 'warning');
    },
    view() {
        return m('.container mx-auto p-4', [
            m('.card bg-base-100 shadow', [
                m('.card-body', [
                    m('h2.card-title', 'Error Services Test'),
                    m('p.text-base-content/70', 'Use the buttons below to exercise the error reporting/formatting and toasts.'),
                    m('.grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2', [
                        m('button.btn btn-success', { onclick: () => window.showToast && window.showToast('Success toast demo', 'success') }, 'Show Success Toast'),
                        m('button.btn btn-info', { onclick: () => window.showToast && window.showToast('Info toast demo', 'info') }, 'Show Info Toast'),
                        m('button.btn btn-warning', { onclick: () => window.showToast && window.showToast('Warning toast demo', 'warning') }, 'Show Warning Toast'),
                        m('button.btn btn-error', { onclick: () => window.showToast && window.showToast('Error toast demo', 'error') }, 'Show Error Toast'),
                        m('button.btn', { class: this.working ? 'btn-disabled' : '', onclick: () => !this.working && this.postSampleError() }, this.working ? 'Posting…' : 'POST sample /api/errors'),
                        m('button.btn btn-outline', { onclick: () => this.callFailingApi() }, 'Call failing API (404)'),
                        m('button.btn btn-outline btn-warning', { onclick: () => this.throwJsError() }, 'Throw JS Error'),
                        m('button.btn btn-outline btn-warning', { onclick: () => this.unhandledRejection() }, 'Unhandled Rejection'),
                    ]),
                    m('div.mt-4', [
                        m('h3.font-semibold', 'Last result'),
                        this.last
                            ? m('pre.whitespace-pre-wrap text-sm bg-base-200 p-3 rounded', JSON.stringify(this.last, null, 2))
                            : m('p.text-base-content/60', 'No actions yet.')
                    ])
                ])
            ])
        ]);
    }
};
