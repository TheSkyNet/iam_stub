// Test Page component demonstrating toasts and error reporting
// Uses DaisyUI components per project guidelines

const { AuthService } = require("../services/AuthserviceService");

const TestPage = {
    data: {
        message: 'Hello from Test Page',
        apiResponse: null,
        loading: false,
    },

    triggerToast: function(type) {
        const map = {
            success: 'Success! Everything worked.',
            info: 'FYI: This is an informational message.',
            warning: 'Heads up! Something might need attention.',
            error: 'Whoops! Something went wrong.'
        };
        if (window.showToast) {
            window.showToast(map[type] || 'Toast', type);
        } else if (typeof alert !== 'undefined') {
            alert(map[type] || 'Toast');
        }
    },

    throwErrorSync: function() {
        throw new Error('TestPage: Synchronous error thrown');
    },

    triggerUnhandledRejection: function() {
        // Create a rejected promise without catch so global handler catches it
        Promise.reject(new Error('TestPage: Unhandled promise rejection'));
    },

    fetchFailing: function() {
        this.data.loading = true;
        m.redraw();
        fetch('/api/does-not-exist', { headers: { 'Accept': 'application/json' } })
            .then(async res => {
                if (!res.ok) {
                    // Let the global error handler format the message nicely
                    if (window.showToastAsync) {
                        await window.showToastAsync(res, 'error');
                    } else if (window.showToast) {
                        window.showToast(`Request failed: ${res.status} ${res.statusText}`, 'error');
                    }
                    throw new Error('Request failed');
                }
                return res.json();
            })
            .then(json => {
                this.data.apiResponse = json;
            })
            .catch(() => {/* handled via toast */})
            .finally(() => { this.data.loading = false; m.redraw(); });
    },

    sendErrorLog: function() {
        const body = {
            message: this.data.message || 'Test error from TestPage',
            level: 'error',
            url: window.location.href,
            context: { page: 'TestPage', when: new Date().toISOString() }
        };
        this.data.loading = true;
        m.request({
            method: 'POST',
            url: '/api/errors',
            body,
            headers: { 'Content-Type': 'application/json' }
        }).then(resp => {
            if (resp && resp.success) {
                window.showToast('Error log created (id: ' + (resp.data?.id || '?') + ')', 'success');
            } else {
                window.showToast(resp?.message || 'Failed to create error log', 'error');
            }
        }).catch(err => {
            window.showToast(err, 'error');
        }).finally(() => { this.data.loading = false; m.redraw(); });
    },

    view: function() {
        return m('.container.mx-auto.p-6.space-y-6', [
            m('.mb-4', [
                m('h1.text-3xl.font-bold', 'Test Page'),
                m('p.opacity-70', 'Use this page to test toasts and the error logging service.')
            ]),

            // Toast buttons
            m('.card.bg-base-100.shadow', [
                m('.card-body', [
                    m('h2.card-title', 'Toasts'),
                    m('.flex.flex-wrap.gap-2', [
                        m('button.btn.btn-success', { onclick: () => this.triggerToast('success') }, 'Success'),
                        m('button.btn.btn-info', { onclick: () => this.triggerToast('info') }, 'Info'),
                        m('button.btn.btn-warning', { onclick: () => this.triggerToast('warning') }, 'Warning'),
                        m('button.btn.btn-error', { onclick: () => this.triggerToast('error') }, 'Error')
                    ])
                ])
            ]),

            // Error triggers
            m('.card.bg-base-100.shadow', [
                m('.card-body.space-y-2', [
                    m('h2.card-title', 'Trigger Errors'),
                    m('div.flex.flex-wrap.gap-2', [
                        m('button.btn', { onclick: () => this.throwErrorSync() }, 'Throw Error'),
                        m('button.btn', { onclick: () => this.triggerUnhandledRejection() }, 'Unhandled Rejection'),
                        m('button.btn', { onclick: () => this.fetchFailing() }, [
                            this.data.loading ? m('span.loading.loading-spinner.loading-xs') : null,
                            'Fetch failing endpoint'
                        ])
                    ])
                ])
            ]),

            // Send explicit Error Log
            m('.card.bg-base-100.shadow', [
                m('.card-body.space-y-2', [
                    m('h2.card-title', 'Send Error Log to Backend'),
                    m('input.input.input-bordered.w-full', {
                        value: this.data.message,
                        oninput: (e) => this.data.message = e.target.value,
                        placeholder: 'Custom error message'
                    }),
                    m('button.btn.btn-primary', { onclick: () => this.sendErrorLog() }, [
                        this.data.loading ? m('span.loading.loading-spinner.loading-xs') : null,
                        'Send to /api/errors'
                    ])
                ])
            ])
        ]);
    }
};

export { TestPage };
