import {UsersService} from "../services/UsersService";
import { Icon } from "./Icon";

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
        return m(".container.mx-auto.p-6.max-w-7xl", [
            // Enhanced breadcrumb
            m(".breadcrumbs.text-sm.mb-8", [
                m("ul.bg-base-200.rounded-lg.px-4.py-2", [
                    m("li", [
                        m("a.flex.items-center.gap-2.hover:text-primary.transition-colors", {
                            onclick: () => m.route.set('/admin')
                        }, [
                            m(Icon, { name: 'fa-solid fa-gauge-high', class: 'w-4 h-4' }),
                            "Admin Dashboard"
                        ])
                    ]),
                    m("li.text-base-content.font-medium", [
                        m("span.flex.items-center.gap-2", [
                            m(Icon, { name: 'fa-solid fa-users', class: 'w-4 h-4' }),
                            "Users Management"
                        ])
                    ])
                ])
            ]),

            // Header section with icon
            m(".flex.items-center.gap-4.mb-8", [
                m(".avatar.placeholder", [
                    m(".bg-primary.text-primary-content.rounded-full.w-16.h-16", [
                        m(Icon, { name: 'fa-solid fa-users', class: 'w-8 h-8' })
                    ])
                ]),
                m("div.flex-1", [
                    m("h1.text-4xl.font-bold.text-base-content", "Users Management"),
                    m("p.text-base-content.opacity-70.text-lg", "Manage user accounts, roles, and permissions")
                ]),
                m("button.btn.btn-primary.btn-lg.gap-2", {
                    onclick: () => this.createUser()
                }, [
                    m(Icon, { name: 'fa-solid fa-user-plus', class: 'w-5 h-5' }),
                    "Add New User"
                ])
            ]),

            // Enhanced error display
            this.data.error ? m(".alert.alert-error.mb-6.shadow-lg", [
                m(Icon, { name: 'fa-solid fa-circle-exclamation', class: 'w-6 h-6' }),
                m("span.font-medium", this.data.error)
            ]) : null,

            // Enhanced loading state
            this.data.loading ? m(".flex.justify-center.items-center.py-12", [
                m(".loading.loading-spinner.loading-lg")
            ]) : null,

            // Enhanced content card
            !this.data.loading ? m(".card.bg-base-100.shadow-2xl.border.border-base-300", [
                m(".card-body.p-8", [
                    // Stats header
                    m(".flex.items-center.justify-between.mb-6", [
                        m(".flex.items-center.gap-3", [
                            m("div.bg-secondary.text-secondary-content.rounded-full.p-2", [
                                m(Icon, { name: 'fa-solid fa-address-book', class: 'w-5 h-5' })
                            ]),
                            m("div", [
                                m("h2.text-2xl.font-semibold.text-base-content", "User Directory"),
                                m("p.text-base-content.opacity-70", `${this.data.items.length} users registered`)
                            ])
                        ])
                    ]),

                    // Enhanced table or empty state
                    this.data.items.length > 0 ? 
                        m(".overflow-x-auto.rounded-lg.border.border-base-300", [
                            m("table.table.table-zebra.w-full", [
                                m("thead.bg-base-200", [
                                    m("tr", [
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-hashtag', class: 'w-4 h-4' }),
                                                "ID"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-user', class: 'w-4 h-4' }),
                                                "Name"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-envelope', class: 'w-4 h-4' }),
                                                "Email"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-user-shield', class: 'w-4 h-4' }),
                                                "Roles"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-circle-check', class: 'w-4 h-4' }),
                                                "Status"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m(Icon, { name: 'fa-solid fa-calendar-days', class: 'w-4 h-4' }),
                                                "Created"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content.text-center", "Actions")
                                    ])
                                ]),
                                m("tbody", 
                                    this.data.items.map(item => 
                                        m("tr.hover:bg-base-200.transition-colors", [
                                            m("td.font-mono.text-sm", item.id),
                                            m("td", [
                                                m("div.flex.items-center.gap-3", [
                                                    m(".avatar.placeholder", [
                                                        m(".bg-neutral-focus.text-neutral-content.rounded-full.w-8.h-8", [
                                                            m("span.text-xs", (item.name || 'U').charAt(0).toUpperCase())
                                                        ])
                                                    ]),
                                                    m("div.font-medium", item.name || 'N/A')
                                                ])
                                            ]),
                                            m("td.text-sm.opacity-70", item.email || 'N/A'),
                                            m("td", [
                                                item.roles && item.roles.length > 0 ?
                                                    m(".flex.flex-wrap.gap-1", 
                                                        item.roles.map(role => 
                                                            m("span.badge.badge-primary.badge-sm", role)
                                                        )
                                                    ) :
                                                    m("span.text-base-content.opacity-50.text-sm", "No roles assigned")
                                            ]),
                                            m("td", [
                                                item.email_verified ? 
                                                    m("div.flex.items-center.gap-2", [
                                                        m("div.w-2.h-2.bg-success.rounded-full"),
                                                        m("span.badge.badge-success.badge-sm", "Verified")
                                                    ]) :
                                                    m("div.flex.items-center.gap-2", [
                                                        m("div.w-2.h-2.bg-warning.rounded-full"),
                                                        m("span.badge.badge-warning.badge-sm", "Pending")
                                                    ])
                                            ]),
                                            m("td.text-sm.opacity-70", item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'),
                                            m("td", [
                                                m(".flex.gap-2.justify-center", [
                                                    m("button.btn.btn-sm.btn-outline.btn-primary.gap-1", {
                                                        onclick: () => this.editUser(item)
                                                    }, [
                                                        m(Icon, { name: 'fa-solid fa-pen-to-square', class: 'w-3 h-3' }),
                                                        "Edit"
                                                    ]),
                                                    m("button.btn.btn-sm.btn-outline.btn-error.gap-1", {
                                                        onclick: () => this.deleteUser(item)
                                                    }, [
                                                        m(Icon, { name: 'fa-solid fa-trash-can', class: 'w-3 h-3' }),
                                                        "Delete"
                                                    ])
                                                ])
                                            ])
                                        ])
                                    )
                                )
                            ])
                        ]) :
                        m(".text-center.py-16", [
                            m(".avatar.placeholder.mb-4", [
                                m(".bg-base-300.text-base-content.rounded-full.w-20.h-20", [
                                    m(Icon, { name: 'fa-solid fa-users', class: 'w-10 h-10' })
                                ])
                            ]),
                            m("h3.text-xl.font-semibold.text-base-content.mb-2", "No Users Found"),
                            m("p.text-base-content.opacity-70.mb-6", "Get started by creating your first user account"),
                            m("button.btn.btn-primary.btn-lg.gap-2", {
                                onclick: () => this.createUser()
                            }, [
                                m(Icon, { name: 'fa-solid fa-user-plus', class: 'w-5 h-5' }),
                                "Create First User"
                            ])
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