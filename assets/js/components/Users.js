import {UsersService} from "../services/UsersService";

const Users = {
    data: {
        items: [],
        loading: false,
        error: null
    },

    oninit: function(vnode) {
        this.loadUsers();
    },

    loadUsers: function() {
        this.data.loading = true;
        this.data.error = null;

        UsersService.getAll()
            .then(response => {
                this.data.items = response.data || [];
                this.data.loading = false;
                m.redraw();
            })
            .catch(error => {
                this.data.error = error.message || 'Failed to load users';
                this.data.loading = false;
                m.redraw();
            });
    },

    view: function(vnode) {
        return m(".container.mx-auto.p-6", [
            m("h1.text-3xl.font-bold.text-base-content.mb-6", "Users Management"),

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
                        m("h2.card-title", "Users"),
                        m("button.btn.btn-primary", {
                            onclick: () => this.createUser()
                        }, "Add User")
                    ]),

                    // Items list
                    this.data.items.length > 0 ? 
                        m(".overflow-x-auto", [
                            m("table.table.table-zebra.w-full", [
                                m("thead", [
                                    m("tr", [
                                        m("th", "ID"),
                                        m("th", "Name"),
                                        m("th", "Email"),
                                        m("th", "Roles"),
                                        m("th", "Email Verified"),
                                        m("th", "Created"),
                                        m("th", "Actions")
                                    ])
                                ]),
                                m("tbody", 
                                    this.data.items.map(item => 
                                        m("tr", [
                                            m("td", item.id),
                                            m("td", item.name || 'N/A'),
                                            m("td", item.email || 'N/A'),
                                            m("td", [
                                                item.roles && item.roles.length > 0 ?
                                                    item.roles.map(role => 
                                                        m("span.badge.badge-primary.badge-sm.mr-1", role)
                                                    ) :
                                                    m("span.text-gray-500", "No roles")
                                            ]),
                                            m("td", [
                                                item.email_verified ? 
                                                    m("span.badge.badge-success.badge-sm", "Verified") :
                                                    m("span.badge.badge-warning.badge-sm", "Unverified")
                                            ]),
                                            m("td", item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'),
                                            m("td", [
                                                m("button.btn.btn-sm.btn-outline.mr-2", {
                                                    onclick: () => this.editUser(item)
                                                }, "Edit"),
                                                m("button.btn.btn-sm.btn-error", {
                                                    onclick: () => this.deleteUser(item)
                                                }, "Delete")
                                            ])
                                        ])
                                    )
                                )
                            ])
                        ]) :
                        m(".text-center.py-8", [
                            m("p.text-base-content.opacity-70", "No users found"),
                            m("button.btn.btn-primary.mt-4", {
                                onclick: () => this.createUser()
                            }, "Create First User")
                        ])
                ])
            ]) : null
        ]);
    },

    createUser: function() {
        m.route.set('/admin/users/add');
    },

    editUser: function(item) {
        m.route.set(`/admin/users/edit/${item.id}`);
    },

    deleteUser: function(item) {
        if (confirm(`Are you sure you want to delete user "${item.name}" (${item.email})?`)) {
            UsersService.delete(item.id)
                .then(() => {
                    this.loadUsers();
                })
                .catch(error => {
                    this.data.error = error.message || 'Failed to delete user';
                    m.redraw();
                });
        }
    }
};

export {Users};