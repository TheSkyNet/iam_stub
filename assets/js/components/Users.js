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
        return m(".container.mx-auto.p-6.max-w-7xl", [
            // Enhanced breadcrumb
            m(".breadcrumbs.text-sm.mb-8", [
                m("ul.bg-base-200.rounded-lg.px-4.py-2", [
                    m("li", [
                        m("a.flex.items-center.gap-2.hover:text-primary.transition-colors", {
                            onclick: () => m.route.set('/admin')
                        }, [
                            m("svg.w-4.h-4", {
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                            }, [
                                m("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    d: "M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"
                                })
                            ]),
                            "Admin Dashboard"
                        ])
                    ]),
                    m("li.text-base-content.font-medium", [
                        m("span.flex.items-center.gap-2", [
                            m("svg.w-4.h-4", {
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                            }, [
                                m("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    d: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                })
                            ]),
                            "Users Management"
                        ])
                    ])
                ])
            ]),

            // Header section with icon
            m(".flex.items-center.gap-4.mb-8", [
                m(".avatar.placeholder", [
                    m(".bg-primary.text-primary-content.rounded-full.w-16.h-16", [
                        m("svg.w-8.h-8", {
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24"
                        }, [
                            m("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                d: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                            })
                        ])
                    ])
                ]),
                m("div.flex-1", [
                    m("h1.text-4xl.font-bold.text-base-content", "Users Management"),
                    m("p.text-base-content.opacity-70.text-lg", "Manage user accounts, roles, and permissions")
                ]),
                m("button.btn.btn-primary.btn-lg.gap-2", {
                    onclick: () => this.createUser()
                }, [
                    m("svg.w-5.h-5", {
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24"
                    }, [
                        m("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                        })
                    ]),
                    "Add New User"
                ])
            ]),

            // Enhanced error display
            this.data.error ? m(".alert.alert-error.mb-6.shadow-lg", [
                m("svg.w-6.h-6", {
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24"
                }, [
                    m("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    })
                ]),
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
                                m("svg.w-5.h-5", {
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                }, [
                                    m("path", {
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round",
                                        "stroke-width": "2",
                                        d: "M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    })
                                ])
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
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M7 20l4-16m2 16l4-16M6 9h14M4 15h14"
                                                    })
                                                ]),
                                                "ID"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                    })
                                                ]),
                                                "Name"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                    })
                                                ]),
                                                "Email"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                                    })
                                                ]),
                                                "Roles"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    })
                                                ]),
                                                "Status"
                                            ])
                                        ]),
                                        m("th.font-semibold.text-base-content", [
                                            m("div.flex.items-center.gap-2", [
                                                m("svg.w-4.h-4", {
                                                    fill: "none",
                                                    stroke: "currentColor",
                                                    viewBox: "0 0 24 24"
                                                }, [
                                                    m("path", {
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11m-6 0h6m-6 0a2 2 0 00-2 2v8a2 2 0 002 2h4a2 2 0 002-2v-8a2 2 0 00-2-2"
                                                    })
                                                ]),
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
                                                        m("svg.w-3.h-3", {
                                                            fill: "none",
                                                            stroke: "currentColor",
                                                            viewBox: "0 0 24 24"
                                                        }, [
                                                            m("path", {
                                                                "stroke-linecap": "round",
                                                                "stroke-linejoin": "round",
                                                                "stroke-width": "2",
                                                                d: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                            })
                                                        ]),
                                                        "Edit"
                                                    ]),
                                                    m("button.btn.btn-sm.btn-outline.btn-error.gap-1", {
                                                        onclick: () => this.deleteUser(item)
                                                    }, [
                                                        m("svg.w-3.h-3", {
                                                            fill: "none",
                                                            stroke: "currentColor",
                                                            viewBox: "0 0 24 24"
                                                        }, [
                                                            m("path", {
                                                                "stroke-linecap": "round",
                                                                "stroke-linejoin": "round",
                                                                "stroke-width": "2",
                                                                d: "M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            })
                                                        ]),
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
                                    m("svg.w-10.h-10", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                        })
                                    ])
                                ])
                            ]),
                            m("h3.text-xl.font-semibold.text-base-content.mb-2", "No Users Found"),
                            m("p.text-base-content.opacity-70.mb-6", "Get started by creating your first user account"),
                            m("button.btn.btn-primary.btn-lg.gap-2", {
                                onclick: () => this.createUser()
                            }, [
                                m("svg.w-5.h-5", {
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                }, [
                                    m("path", {
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round",
                                        "stroke-width": "2",
                                        d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                    })
                                ]),
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