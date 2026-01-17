// SSO/OAuth error page (DaisyUI + Mithril)
import m from "mithril";
// Notes:
// - Place under pages folder per request (not under components)
// - Uses global mithril `m` and global `window.showToast`

function parseQuery() {
    try {
        const qs = (window.location && window.location.search) ? window.location.search.replace(/^\?/, '') : '';
        return m.parseQueryString(qs) || {};
    } catch (_) {
        return {};
    }
}

function providerLabel(provider) {
    if (!provider) return 'Unknown Provider';
    const map = {
        google: 'Google',
        github: 'GitHub',
        facebook: 'Facebook',
        generic: 'OAuth 2.0',
    };
    return map[String(provider).toLowerCase()] || String(provider);
}

function pickMessage(q) {
    const candidates = [q.message, q.error_description, q.error, q.detail, q.title];
    for (const c of candidates) {
        if (typeof c === 'string' && c.trim()) return c.trim();
    }
    return 'SSO sign-in could not be completed. Please try again.';
}

export const SSOErrorPage = {
    oninit() {
        this.q = parseQuery();
        this.provider = this.q.provider || this.q.source || '';
        this.message = pickMessage(this.q);
    },
    tryAgain() {
        const provider = this.provider || (this.q && this.q.provider);
        if (!provider) {
            if (window.showToast) window.showToast('Missing provider. Choose a provider and try again from the login page.', 'warning');
            m.route.set('/login');
            return;
        }
        // Start backend OAuth flow
        window.location.href = `/api/oauth/redirect?provider=${encodeURIComponent(provider)}`;
    },
    copyDetails() {
        try {
            const data = {
                provider: this.provider,
                code: this.q.code,
                error: this.q.error,
                error_description: this.q.error_description,
                state: this.q.state,
                message: this.message,
                query: this.q,
            };
            const text = JSON.stringify(data, null, 2);
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    if (window.showToast) window.showToast('Copied error details to clipboard', 'info');
                }).catch(() => {
                    if (window.showToast) window.showToast('Unable to copy to clipboard', 'warning');
                });
            } else {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); if (window.showToast) window.showToast('Copied error details to clipboard', 'info'); } catch(_) {}
                document.body.removeChild(ta);
            }
        } catch (e) {
            if (window.showToast) window.showToast('Failed to prepare details', 'error');
        }
    },
    view() {
        const provider = providerLabel(this.provider);
        const raw = this.q || {};
        return m('section.hero', { class: 'min-h-[70vh] bg-base-200' }, [
            m('div', { class: 'hero-content w-full flex-col' }, [
                m('div', { class: 'text-center' }, [
                    m('h1', { class: 'text-3xl font-bold flex items-center justify-center gap-2' }, [
                        m('span.badge.badge-error.badge-lg', 'SSO Error'),
                        m('span', ` ${provider}`)
                    ]),
                    m('p', { class: 'py-4 text-base-content/80' }, this.message)
                ]),
                m('div', { class: 'card shadow bg-base-100 w-full max-w-3xl' }, [
                    m('div', { class: 'card-body' }, [
                        m('div', { class: 'alert alert-error shadow' }, [
                            m('span', this.message)
                        ]),
                        m('div', { class: 'grid grid-cols-1 md:grid-cols-2 gap-4' }, [
                            this.provider ? m('div', { class: 'form-control' }, [
                                m('label.label', [m('span.label-text', 'Provider')]),
                                m('div.badge.badge-outline', provider)
                            ]) : null,
                            raw.code ? m('div', { class: 'form-control' }, [
                                m('label.label', [m('span.label-text', 'Code')]),
                                m('div.kbd.kbd-sm', String(raw.code))
                            ]) : null,
                            raw.state ? m('div', { class: 'form-control' }, [
                                m('label.label', [m('span.label-text', 'State')]),
                                m('div', { class: 'kbd kbd-sm break-all' }, String(raw.state))
                            ]) : null,
                        ]),
                        m('details', { class: 'mt-2' }, [
                            m('summary', { class: 'cursor-pointer select-none' }, 'Show technical details'),
                            m('pre', { class: 'mt-2 whitespace-pre-wrap text-sm bg-base-200 p-2 rounded' }, JSON.stringify(raw, null, 2))
                        ]),
                        m('div', { class: 'card-actions justify-end mt-4 gap-2' }, [
                            m('button.btn', { onclick: () => m.route.set('/login') }, 'Back to Login'),
                            m('button.btn.btn-outline', { onclick: () => m.route.set('/') }, 'Home'),
                            m('button.btn.btn-warning', { onclick: () => this.tryAgain() }, this.provider ? `Try ${provider} again` : 'Try again'),
                            m('button.btn.btn-ghost', { onclick: () => this.copyDetails() }, 'Copy details')
                        ])
                    ])
                ])
            ])
        ]);
    }
};
