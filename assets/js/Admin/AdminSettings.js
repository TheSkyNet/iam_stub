import m from "mithril";

const AdminSettings = {
    settings: [],
    loading: false,
    error: null,
    newSetting: {
        key: '',
        value: '',
        type: 'string',
        description: ''
    },
    editingId: null,

    oninit: function() {
        this.loadSettings();
    },

    loadSettings: function() {
        this.loading = true;
        m.request({
            method: 'GET',
            url: '/api/v1/settings'
        })
            .then(result => {
                this.settings = result;
            })
            .catch(error => {
                this.error = error.message;
            })
            .finally(() => {
                this.loading = false;
            });
    },

    saveSetting: function(setting) {
        const isNew = !setting.id;
        const method = isNew ? 'POST' : 'PUT';
        const url = isNew ? '/api/v1/settings' : `/api/v1/settings/${setting.key}`;

        return m.request({
            method: method,
            url: url,
            body: setting
        })
            .then(() => {
                this.loadSettings();
                this.editingId = null;
                this.newSetting = { key: '', value: '', type: 'string', description: '' };
            });
    },

    deleteSetting: function(key) {
        if (confirm('Are you sure you want to delete this setting?')) {
            m.request({
                method: 'DELETE',
                url: `/api/v1/settings/${key}`
            })
                .then(() => {
                    this.loadSettings();
                });
        }
    },

    view: function() {
        let formContent = null;
        if (this.editingId) {
            const currentSetting = this.editingId === 'new' ? this.newSetting : this.settings.find(s => s.id === this.editingId);
            formContent = m(".card.bg-base-200.mb-6", [
                m(".card-body", [
                    m("h2.card-title", this.editingId === 'new' ? "Add New Setting" : "Edit Setting"),
                    m("form", {
                        onsubmit: (e) => {
                            e.preventDefault();
                            this.saveSetting(currentSetting);
                        }
                    }, [
                        m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Key")),
                                m("input.input.input-bordered", {
                                    type: "text",
                                    value: currentSetting?.key,
                                    disabled: this.editingId !== 'new',
                                    oninput: (e) => { if (this.editingId === 'new') this.newSetting.key = e.target.value; }
                                })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Value")),
                                m("input.input.input-bordered", {
                                    type: "text",
                                    value: currentSetting?.value,
                                    oninput: (e) => {
                                        if (this.editingId === 'new') {
                                            this.newSetting.value = e.target.value;
                                        } else {
                                            currentSetting.value = e.target.value;
                                        }
                                    }
                                })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Type")),
                                m("select.select.select-bordered", {
                                    value: currentSetting?.type,
                                    onchange: (e) => {
                                        if (this.editingId === 'new') {
                                            this.newSetting.type = e.target.value;
                                        } else {
                                            currentSetting.type = e.target.value;
                                        }
                                    }
                                }, ['string', 'integer', 'float', 'boolean', 'array', 'json'].map(type => 
                                    m("option", { value: type }, type)
                                ))
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Description")),
                                m("textarea.textarea.textarea-bordered", {
                                    value: currentSetting?.description,
                                    oninput: (e) => {
                                        if (this.editingId === 'new') {
                                            this.newSetting.description = e.target.value;
                                        } else {
                                            currentSetting.description = e.target.value;
                                        }
                                    }
                                })
                            ])
                        ]),
                        m(".card-actions.justify-end.mt-4", [
                            m("button.btn.btn-ghost", { onclick: () => { this.editingId = null; } }, "Cancel"),
                            m("button.btn.btn-primary", { type: "submit" }, "Save Setting")
                        ])
                    ])
                ])
            ]);
        }

        return m(".p-4.space-y-6", [
            m(".flex.justify-between.items-center", [
                m("h1.text-3xl.font-bold", "Site Settings"),
                m("button.btn.btn-primary", { onclick: () => { this.editingId = 'new'; } }, "Add Setting")
            ]),

            formContent,

            m(".card.bg-base-100.shadow-xl", [
                m(".overflow-x-auto", [
                    m("table.table.table-zebra", [
                        m("thead", [
                            m("tr", [
                                m("th", "Key / Description"),
                                m("th", "Value / Type"),
                                m("th.text-right", "Actions")
                            ])
                        ]),
                        m("tbody", [
                            this.loading ? m("tr", m("td.text-center", { colspan: 3 }, m("span.loading.loading-spinner.loading-lg"))) :
                            this.settings.length === 0 ? m("tr", m("td.text-center", { colspan: 3 }, "No settings found.")) :
                            this.settings.map(setting => m("tr", [
                                m("td", [
                                    m(".font-bold", setting.key),
                                    m(".text-sm.opacity-50", setting.description)
                                ]),
                                m("td", [
                                    m("code.text-xs", setting.value),
                                    m("br"),
                                    m("span.badge.badge-ghost.badge-sm", setting.type)
                                ]),
                                m("td.text-right.space-x-2", [
                                    m("button.btn.btn-sm.btn-ghost", { onclick: () => { this.editingId = setting.id; } }, "Edit"),
                                    m("button.btn.btn-sm.btn-error.btn-ghost", { onclick: () => this.deleteSetting(setting.key) }, "Delete")
                                ])
                            ]))
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export { AdminSettings };