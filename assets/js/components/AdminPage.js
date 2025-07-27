const {AuthService} = require("../services/AuthserviceService");

const AdminPage = {
    data: {
        stats: {
            totalUsers: 0,
            totalRoles: 0,
            activeUsers: 0,
            loading: true
        },
        user: null
    },

    oninit: function(vnode) {
        this.data.user = AuthService.getUser();
        this.loadStats();
    },

    loadStats: function() {
        this.data.stats.loading = true;
        
        // Load basic stats - you can expand this with actual API calls
        Promise.all([
            this.loadUserStats(),
            this.loadRoleStats()
        ]).then(() => {
            this.data.stats.loading = false;
            m.redraw();
        }).catch(error => {
            console.error('Failed to load admin stats:', error);
            this.data.stats.loading = false;
            m.redraw();
        });
    },

    loadUserStats: function() {
        // Placeholder - replace with actual API call
        return new Promise(resolve => {
            setTimeout(() => {
                this.data.stats.totalUsers = 25;
                this.data.stats.activeUsers = 18;
                resolve();
            }, 500);
        });
    },

    loadRoleStats: function() {
        // Try to load roles from the API
        return fetch('/api/roles', {
            headers: {
                'Authorization': `Bearer ${AuthService.accessToken}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                this.data.stats.totalRoles = data.data.length;
            } else {
                this.data.stats.totalRoles = 4; // Default roles count
            }
        })
        .catch(() => {
            this.data.stats.totalRoles = 4; // Fallback
        });
    },

    view: function(vnode) {
        return m(".container.mx-auto.p-6", [
            // Header
            m(".mb-8", [
                m("h1.text-4xl.font-bold.text-base-content.mb-2", "Admin Dashboard"),
                m("p.text-base-content.opacity-70", `Welcome back, ${this.data.user?.name || 'Admin'}!`)
            ]),

            // Stats Cards
            this.data.stats.loading ? 
                m(".flex.justify-center.items-center.py-12", [
                    m(".loading.loading-spinner.loading-lg")
                ]) :
                m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-4.gap-6.mb-8", [
                    // Total Users Card
                    m(".card.bg-primary.text-primary-content.shadow-xl", [
                        m(".card-body", [
                            m(".flex.items-center.justify-between", [
                                m("div", [
                                    m("h2.card-title.text-2xl", this.data.stats.totalUsers),
                                    m("p.opacity-80", "Total Users")
                                ]),
                                m(".text-4xl.opacity-60", "👥")
                            ])
                        ])
                    ]),

                    // Active Users Card
                    m(".card.bg-success.text-success-content.shadow-xl", [
                        m(".card-body", [
                            m(".flex.items-center.justify-between", [
                                m("div", [
                                    m("h2.card-title.text-2xl", this.data.stats.activeUsers),
                                    m("p.opacity-80", "Active Users")
                                ]),
                                m(".text-4xl.opacity-60", "✅")
                            ])
                        ])
                    ]),

                    // Total Roles Card
                    m(".card.bg-info.text-info-content.shadow-xl", [
                        m(".card-body", [
                            m(".flex.items-center.justify-between", [
                                m("div", [
                                    m("h2.card-title.text-2xl", this.data.stats.totalRoles),
                                    m("p.opacity-80", "System Roles")
                                ]),
                                m(".text-4xl.opacity-60", "🔐")
                            ])
                        ])
                    ]),

                    // System Status Card
                    m(".card.bg-warning.text-warning-content.shadow-xl", [
                        m(".card-body", [
                            m(".flex.items-center.justify-between", [
                                m("div", [
                                    m("h2.card-title.text-2xl", "Online"),
                                    m("p.opacity-80", "System Status")
                                ]),
                                m(".text-4xl.opacity-60", "⚡")
                            ])
                        ])
                    ])
                ]),

            // Quick Actions
            m(".card.bg-base-100.shadow-xl.mb-8", [
                m(".card-body", [
                    m("h2.card-title.text-2xl.mb-4", "Quick Actions"),
                    m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-4", [
                        // Role Management
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => m.route.set('/admin/roles')
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "👑"),
                                m("span.font-semibold", "Manage Roles"),
                                m("span.text-sm.opacity-70", "Create, edit, and delete user roles")
                            ])
                        ]),

                        // User Management (placeholder)
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => {
                                // TODO: Implement user management
                                alert('User management coming soon!');
                            }
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "👤"),
                                m("span.font-semibold", "Manage Users"),
                                m("span.text-sm.opacity-70", "View and manage user accounts")
                            ])
                        ]),

                        // System Settings (placeholder)
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => {
                                // TODO: Implement system settings
                                alert('System settings coming soon!');
                            }
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "⚙️"),
                                m("span.font-semibold", "System Settings"),
                                m("span.text-sm.opacity-70", "Configure system preferences")
                            ])
                        ]),

                        // Analytics (placeholder)
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => {
                                // TODO: Implement analytics
                                alert('Analytics coming soon!');
                            }
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "📊"),
                                m("span.font-semibold", "Analytics"),
                                m("span.text-sm.opacity-70", "View system usage statistics")
                            ])
                        ]),

                        // Logs (placeholder)
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => {
                                // TODO: Implement logs
                                alert('System logs coming soon!');
                            }
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "📋"),
                                m("span.font-semibold", "System Logs"),
                                m("span.text-sm.opacity-70", "View application logs and errors")
                            ])
                        ]),

                        // Backup (placeholder)
                        m("button.btn.btn-outline.btn-lg.h-auto.p-6", {
                            onclick: () => {
                                // TODO: Implement backup
                                alert('Backup management coming soon!');
                            }
                        }, [
                            m(".flex.flex-col.items-center.gap-2", [
                                m(".text-3xl", "💾"),
                                m("span.font-semibold", "Backup"),
                                m("span.text-sm.opacity-70", "Manage system backups")
                            ])
                        ])
                    ])
                ])
            ]),

            // Recent Activity (placeholder)
            m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title.text-2xl.mb-4", "Recent Activity"),
                    m(".space-y-3", [
                        m(".flex.items-center.gap-3.p-3.bg-base-200.rounded-lg", [
                            m(".avatar.placeholder", [
                                m(".bg-neutral-focus.text-neutral-content.rounded-full.w-8", [
                                    m("span.text-xs", "U")
                                ])
                            ]),
                            m("div", [
                                m("p.font-medium", "New user registered"),
                                m("p.text-sm.opacity-70", "2 minutes ago")
                            ])
                        ]),
                        m(".flex.items-center.gap-3.p-3.bg-base-200.rounded-lg", [
                            m(".avatar.placeholder", [
                                m(".bg-primary.text-primary-content.rounded-full.w-8", [
                                    m("span.text-xs", "R")
                                ])
                            ]),
                            m("div", [
                                m("p.font-medium", "Role permissions updated"),
                                m("p.text-sm.opacity-70", "15 minutes ago")
                            ])
                        ]),
                        m(".flex.items-center.gap-3.p-3.bg-base-200.rounded-lg", [
                            m(".avatar.placeholder", [
                                m(".bg-success.text-success-content.rounded-full.w-8", [
                                    m("span.text-xs", "S")
                                ])
                            ]),
                            m("div", [
                                m("p.font-medium", "System backup completed"),
                                m("p.text-sm.opacity-70", "1 hour ago")
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export {AdminPage};