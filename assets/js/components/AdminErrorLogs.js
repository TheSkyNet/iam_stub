// Admin Error Logs viewer using DaisyUI
const { AuthService } = require("../services/AuthserviceService");

const AdminErrorLogs = {
    state: {
        items: [],
        total: 0,
        limit: 20,
        offset: 0,
        level: '',
        q: '',
        since: '',
        loading: false,
        selected: null,
        cleanupDays: 30,
    },

    oninit() {
        this.fetchLogs();
    },

    fetchLogs() {
        this.state.loading = true;
        const params = new URLSearchParams();
        if (this.state.level) params.set('level', this.state.level);
        if (this.state.q) params.set('q', this.state.q);
        if (this.state.since) params.set('since', this.state.since);
        params.set('limit', String(this.state.limit));
        params.set('offset', String(this.state.offset));

        return m.request({
            method: 'GET',
            url: `/api/errors?${params.toString()}`,
            headers: AuthService.getAuthHeaders(),
        }).then(resp => {
            if (resp && resp.success) {
                this.state.items = resp.data?.items || [];
                this.state.total = resp.data?.total || 0;
                this.state.limit = resp.data?.limit || this.state.limit;
                this.state.offset = resp.data?.offset || this.state.offset;
            } else {
                window.showToast(resp?.message || 'Failed to load error logs', 'error');
            }
        }).catch(err => {
            window.showToast(err, 'error');
        }).finally(() => { this.state.loading = false; m.redraw(); });
    },

    viewLog(item) {
        this.state.selected = item;
    },

    deleteLog(id) {
        if (!confirm('Delete this error log?')) return;
        m.request({
            method: 'DELETE',
            url: `/api/errors/${id}`,
            headers: AuthService.getAuthHeaders(),
        }).then(resp => {
            if (resp && resp.success) {
                window.showToast('Deleted', 'success');
                this.fetchLogs();
            } else {
                window.showToast(resp?.message || 'Delete failed', 'error');
            }
        }).catch(err => window.showToast(err, 'error'));
    },

    cleanup() {
        const days = parseInt(this.state.cleanupDays || 30, 10);
        m.request({
            method: 'POST',
            url: '/api/errors/cleanup',
            headers: AuthService.getAuthHeaders(),
            body: { days }
        }).then(resp => {
            if (resp && resp.success) {
                window.showToast(`Cleanup removed ${resp.data?.deleted ?? 0} rows`, 'success');
                this.fetchLogs();
            } else {
                window.showToast(resp?.message || 'Cleanup failed', 'error');
            }
        }).catch(err => window.showToast(err, 'error'));
    },

    changePage(delta) {
        const next = this.state.offset + delta * this.state.limit;
        if (next < 0) return;
        if (next >= this.state.total) return;
        this.state.offset = next;
        this.fetchLogs();
    },

    view() {
        const s = this.state;
        const totalPages = Math.max(1, Math.ceil(s.total / s.limit));
        const currentPage = Math.floor(s.offset / s.limit) + 1;

        return m('.container.mx-auto.p-6.space-y-6', [
            m('.flex.items-center.justify-between', [
                m('h1.text-3xl.font-bold', 'Admin: Error Logs'),
                m('div.form-control', [
                    m('div.join', [
                        m('select.select.select-bordered.join-item', {
                            value: s.level,
                            onchange: e => { s.level = e.target.value; }
                        }, [
                            m('option', { value: '' }, 'All levels'),
                            m('option', { value: 'error' }, 'error'),
                            m('option', { value: 'warning' }, 'warning'),
                            m('option', { value: 'info' }, 'info'),
                        ]),
                        m('input.input.input-bordered.join-item', {
                            placeholder: 'Search message or URL',
                            value: s.q,
                            oninput: e => s.q = e.target.value
                        }),
                        m('input.input.input-bordered.join-item', {
                            type: 'date',
                            value: s.since,
                            oninput: e => s.since = e.target.value
                        }),
                        m('button.btn.btn-primary.join-item', { onclick: () => { s.offset = 0; this.fetchLogs(); } }, 'Search')
                    ])
                ])
            ]),

            m('.card.bg-base-100.shadow', [
                m('.card-body', [
                    m('div.flex.justify-between.items-center.mb-2', [
                        m('div', [
                            s.loading ? m('span.loading.loading-spinner.loading-sm') : null,
                            m('span.ml-2.opacity-70', `${s.total} results`)
                        ]),
                        m('div.flex.gap-2.items-center', [
                            m('input.input.input-bordered.w-24', {
                                type: 'number', min: 1,
                                value: s.cleanupDays,
                                oninput: e => s.cleanupDays = e.target.value
                            }),
                            m('button.btn.btn-warning', { onclick: () => this.cleanup() }, 'Cleanup (days)'),
                            m('button.btn', { onclick: () => this.fetchLogs() }, 'Refresh')
                        ])
                    ]),

                    m('div.overflow-x-auto', [
                        m('table.table', [
                            m('thead', [
                                m('tr', [
                                    m('th', 'ID'),
                                    m('th', 'Level'),
                                    m('th', 'Message'),
                                    m('th', 'URL'),
                                    m('th', 'User'),
                                    m('th', 'When'),
                                    m('th', 'Actions'),
                                ])
                            ]),
                            m('tbody', s.items.map(item => m('tr', [
                                m('td', item.id),
                                m('td', item.level),
                                m('td.max-w-[28ch].truncate', item.message),
                                m('td.max-w-[28ch].truncate', item.url || ''),
                                m('td', item.user_id || '-'),
                                m('td', item.created_at),
                                m('td', [
                                    m('button.btn.btn-xs', { onclick: () => this.viewLog(item) }, 'View'),
                                    m('button.btn.btn-xs.btn-error.ml-2', { onclick: () => this.deleteLog(item.id) }, 'Delete')
                                ])
                            ])))
                        ])
                    ]),

                    m('div.flex.justify-between.items-center.mt-4', [
                        m('div', `Page ${currentPage} of ${totalPages}`),
                        m('div.join', [
                            m('button.btn.join-item', { disabled: s.offset <= 0, onclick: () => this.changePage(-1) }, 'Prev'),
                            m('button.btn.join-item', { disabled: (s.offset + s.limit) >= s.total, onclick: () => this.changePage(1) }, 'Next'),
                        ])
                    ])
                ])
            ]),

            // Modal for details
            s.selected ? m('.modal.modal-open', [
                m('.modal-box', [
                    m('h3.font-bold.text-lg', `Error #${s.selected.id}`),
                    m('p.mt-1', [ m('span.badge', s.selected.level), m('span.ml-2', s.selected.created_at) ]),
                    m('p.mt-2.break-words', [ m('strong', 'Message: '), s.selected.message ]),
                    s.selected.url ? m('p.mt-2.break-words', [ m('strong', 'URL: '), s.selected.url ]) : null,
                    s.selected.user_agent ? m('details.mt-2', [
                        m('summary.cursor-pointer', 'User Agent'),
                        m('div.mt-1.text-xs.break-words', s.selected.user_agent)
                    ]) : null,
                    m('div.mt-2', [
                        m('p.font-semibold', 'Context:'),
                        m('pre.whitespace-pre-wrap.bg-base-200.p-2.rounded', JSON.stringify(s.selected.context, null, 2) || '-')
                    ]),
                    m('div.modal-action', [
                        m('button.btn', { onclick: () => s.selected = null }, 'Close')
                    ])
                ])
            ]) : null
        ]);
    }
};

export { AdminErrorLogs };
