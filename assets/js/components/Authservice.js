const Authservice = {
    data: {
        items: [],
        loading: false,
        error: null
    },

    oninit: function(vnode) {
        this.loadAuthservices();
    },

    loadAuthservices: function() {
        this.data.loading = true;
        this.data.error = null;

        AuthserviceService.getAll()
            .then(response => {
                this.data.items = response.data || [];
                this.data.loading = false;
                m.redraw();
            })
            .catch(error => {
                this.data.error = error.message || 'Failed to load authservices';
                this.data.loading = false;
                m.redraw();
            });
    },

    view: function(vnode) {
        return m(".container.mx-auto.p-6", [
            m("h1.text-3xl.font-bold.text-base-content.mb-6", "Authservice Management"),

            // Error display
            this.data.error ? m(".alert.alert-error.mb-4", [
                m("span", this.data.error)
            ]) : null,

            // Loading state
            this.data.loading ? m(".flex.justify-center.items-center.py-8", [
                m(".loading.loading-spinner.loading-lg")
            ]) : null,

            // Content
            !this.data.loading ? m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m(".flex.justify-between.items-center.mb-4", [
                        m("h2.card-title", "Authservices"),
                        m("button.btn.btn-primary", {
                            onclick: () => this.createAuthservice()
                        }, "Add Authservice")
                    ]),

                    // Items list
                    this.data.items.length > 0 ? 
                        m(".overflow-x-auto", [
                            m("table.table.table-zebra.w-full", [
                                m("thead", [
                                    m("tr", [
                                        m("th", "ID"),
                                        m("th", "Name"),
                                        m("th", "Created"),
                                        m("th", "Actions")
                                    ])
                                ]),
                                m("tbody", 
                                    this.data.items.map(item => 
                                        m("tr", [
                                            m("td", item.id),
                                            m("td", item.name || 'N/A'),
                                            m("td", item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'),
                                            m("td", [
                                                m("button.btn.btn-sm.btn-outline.mr-2", {
                                                    onclick: () => this.editAuthservice(item)
                                                }, "Edit"),
                                                m("button.btn.btn-sm.btn-error", {
                                                    onclick: () => this.deleteAuthservice(item)
                                                }, "Delete")
                                            ])
                                        ])
                                    )
                                )
                            ])
                        ]) :
                        m(".text-center.py-8", [
                            m("p.text-base-content.opacity-70", "No authservices found"),
                            m("button.btn.btn-primary.mt-4", {
                                onclick: () => this.createAuthservice()
                            }, "Create First Authservice")
                        ])
                ])
            ]) : null
        ]);
    },

    createAuthservice: function() {
        // TODO: Implement create functionality
        console.log('Create Authservice');
    },

    editAuthservice: function(item) {
        // TODO: Implement edit functionality
        console.log('Edit Authservice', item);
    },

    deleteAuthservice: function(item) {
        if (confirm(`Are you sure you want to delete this authservice?`)) {
            AuthserviceService.delete(item.id)
                .then(() => {
                    this.loadAuthservices();
                })
                .catch(error => {
                    this.data.error = error.message || 'Failed to delete authservice';
                    m.redraw();
                });
        }
    }
};

export {Authservice};