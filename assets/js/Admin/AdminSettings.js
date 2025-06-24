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
                m.redraw();
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
        return m("div.settings-page", {
            style: {
                background: '#fff',
                borderRadius: '8px',
                padding: '1.5rem',
                boxShadow: '0 2px 4px rgba(0,0,0,0.05)'
            }
        }, [
            // Header
            m("div.header", {
                style: {
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    marginBottom: '2rem'
                }
            }, [
                m("h1", {
                    style: {
                        color: '#9BA5D0',
                        fontSize: '2rem',
                        margin: 0
                    }
                }, [
                    m("i.octicon.octicon-gear", {
                        style: { marginRight: '0.75rem' }
                    }),
                    "Site Settings"
                ]),
                m("button.btn", {
                    style: {
                        background: '#8E96C8',
                        color: '#fff',
                        border: 'none',
                        padding: '0.5rem 1rem',
                        borderRadius: '4px'
                    },
                    onclick: () => {
                        this.editingId = 'new';
                    }
                }, "Add Setting")
            ]),

            // New/Edit Setting Form
            this.editingId && m("div.card", {
                style: {
                    background: '#F8F9FE',
                    border: '1px solid #E6E9F4',
                    borderRadius: '6px',
                    marginBottom: '1.5rem',
                    padding: '1.5rem'
                }
            }, [
                m("h3", { style: { color: '#6B7399', marginBottom: '1rem' } },
                    this.editingId === 'new' ? "Add New Setting" : "Edit Setting"
                ),
                m("form", {
                    onsubmit: (e) => {
                        e.preventDefault();
                        this.saveSetting(this.editingId === 'new' ? this.newSetting :
                            this.settings.find(s => s.id === this.editingId));
                    }
                }, [
                    // Form fields
                    m("div.form-group", [
                        m("label", { style: { color: '#6B7399' } }, "Key"),
                        m("input.form-control[type=text]", {
                            value: this.editingId === 'new' ? this.newSetting.key :
                                this.settings.find(s => s.id === this.editingId)?.key,
                            onchange: (e) => {
                                if (this.editingId === 'new') {
                                    this.newSetting.key = e.target.value;
                                }
                            },
                            style: {
                                border: '1px solid #E6E9F4',
                                borderRadius: '4px',
                                padding: '0.5rem'
                            }
                        })
                    ]),
                    m("div.form-group", [
                        m("label", { style: { color: '#6B7399' } }, "Value"),
                        m("input.form-control[type=text]", {
                            value: this.editingId === 'new' ? this.newSetting.value :
                                this.settings.find(s => s.id === this.editingId)?.value,
                            onchange: (e) => {
                                if (this.editingId === 'new') {
                                    this.newSetting.value = e.target.value;
                                } else {
                                    const setting = this.settings.find(s => s.id === this.editingId);
                                    if (setting) setting.value = e.target.value;
                                }
                            },
                            style: {
                                border: '1px solid #E6E9F4',
                                borderRadius: '4px',
                                padding: '0.5rem'
                            }
                        })
                    ]),
                    m("div.form-group", [
                        m("label", { style: { color: '#6B7399' } }, "Type"),
                        m("select.form-control", {
                            value: this.editingId === 'new' ? this.newSetting.type :
                                this.settings.find(s => s.id === this.editingId)?.type,
                            onchange: (e) => {
                                if (this.editingId === 'new') {
                                    this.newSetting.type = e.target.value;
                                } else {
                                    const setting = this.settings.find(s => s.id === this.editingId);
                                    if (setting) setting.type = e.target.value;
                                }
                            },
                            style: {
                                border: '1px solid #E6E9F4',
                                borderRadius: '4px',
                                padding: '0.5rem'
                            }
                        }, [
                            ['string', 'integer', 'float', 'boolean', 'array', 'json'].map(type =>
                                m("option", { value: type }, type)
                            )
                        ])
                    ]),
                    m("div.form-group", [
                        m("label", { style: { color: '#6B7399' } }, "Description"),
                        m("textarea.form-control", {
                            value: this.editingId === 'new' ? this.newSetting.description :
                                this.settings.find(s => s.id === this.editingId)?.description,
                            onchange: (e) => {
                                if (this.editingId === 'new') {
                                    this.newSetting.description = e.target.value;
                                } else {
                                    const setting = this.settings.find(s => s.id === this.editingId);
                                    if (setting) setting.description = e.target.value;
                                }
                            },
                            style: {
                                border: '1px solid #E6E9F4',
                                borderRadius: '4px',
                                padding: '0.5rem'
                            }
                        })
                    ]),
                    m("div.form-group", { style: { marginTop: '1rem' } }, [
                        m("button.btn", {
                            type: "submit",
                            style: {
                                background: '#8E96C8',
                                color: '#fff',
                                border: 'none',
                                padding: '0.5rem 1rem',
                                borderRadius: '4px',
                                marginRight: '0.5rem'
                            }
                        }, "Save"),
                        m("button.btn", {
                            onclick: () => { this.editingId = null; },
                            style: {
                                background: '#9BA5D0',
                                color: '#fff',
                                border: 'none',
                                padding: '0.5rem 1rem',
                                borderRadius: '4px'
                            }
                        }, "Cancel")
                    ])
                ])
            ]),

            // Settings List
            m("div.settings-list", {
                style: {
                    background: '#F8F9FE',
                    borderRadius: '6px',
                    border: '1px solid #E6E9F4'
                }
            }, [
                this.loading ? m("div.loading", "Loading...") :
                    this.settings.map(setting =>
                        m("div.setting-item", {
                            style: {
                                padding: '1rem',
                                borderBottom: '1px solid #E6E9F4',
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center'
                            }
                        }, [
                            m("div.setting-info", [
                                m("h4", { style: { color: '#6B7399', margin: '0' } }, setting.key),
                                m("p", { style: { color: '#9BA5D0', margin: '0.25rem 0' } }, setting.description),
                                m("small", { style: { color: '#8E96C8' } }, `Type: ${setting.type}, Value: ${setting.value}`)
                            ]),
                            m("div.setting-actions", [
                                m("button.btn", {
                                    onclick: () => { this.editingId = setting.id; },
                                    style: {
                                        background: '#9BA5D0',
                                        color: '#fff',
                                        border: 'none',
                                        padding: '0.25rem 0.5rem',
                                        borderRadius: '4px',
                                        marginRight: '0.5rem'
                                    }
                                }, "Edit"),
                                m("button.btn", {
                                    onclick: () => this.deleteSetting(setting.key),
                                    style: {
                                        background: '#FF9999',
                                        color: '#fff',
                                        border: 'none',
                                        padding: '0.25rem 0.5rem',
                                        borderRadius: '4px'
                                    }
                                }, "Delete")
                            ])
                        ])
                    )
            ])
        ]);
    }
};

module.exports = { AdminSettings };