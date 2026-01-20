import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const UsersPage = {
    users: [],
    loading: true,
    error: null,

    oninit: function() {
        this.loadUsers();
    },

    loadUsers: function() {
        this.loading = true;
        this.error = null;
        
        return m.request({
            method: "GET",
            url: "/api/users",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.users = response.data;
            } else {
                this.error = response.message || "Failed to load users";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading users";
            this.loading = false;
        });
    },

    deleteUser: function(id) {
        if (!confirm("Are you sure you want to delete this user?")) {
            return;
        }
        return m.request({
            method: "DELETE",
            url: `/api/users/${id}`,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast("User deleted", "success");
                this.loadUsers();
            } else {
                window.showToast(response, "error");
            }
        }).catch((err) => {
            window.showToast(err, "error");
        });
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Users Management"),
                m(".flex.gap-2", [
                    m("button.btn.btn-outline.btn-sm", { onclick: () => this.loadUsers() }, [
                        m(Icon, { icon: "fa-solid fa-rotate" }),
                        " Refresh"
                    ]),
                    m(m.route.Link, { href: "/admin/users/add", class: "btn btn-primary" }, [
                        m(Icon, { icon: "fa-solid fa-user-plus" }),
                        " Add User"
                    ])
                ])
            ]),

            this.loading 
                ? m(".flex.justify-center.p-12", m("span.loading.loading-spinner.loading-lg"))
                : this.error
                    ? m(".alert.alert-error", [
                        m(Icon, { icon: "fa-solid fa-circle-exclamation" }),
                        m("span", this.error)
                    ])
                    : m(".overflow-x-auto.bg-base-100.rounded-xl.shadow", [
                        m("table.table.table-zebra", [
                            m("thead", [
                                m("tr", [
                                    m("th", "ID"),
                                    m("th", "Name"),
                                    m("th", "Email"),
                                    m("th", "Roles"),
                                    m("th.text-right", "Actions")
                                ])
                            ]),
                            m("tbody", [
                                this.users.length === 0 
                                    ? m("tr", m("td.text-center[colspan=5]", "No users found"))
                                    : this.users.map(user => m("tr", [
                                        m("td", user.id),
                                        m("td", user.name),
                                        m("td", user.email),
                                        m("td", (user.roles || []).map(role => m(".badge.badge-ghost.mr-1", role))),
                                        m("td.text-right", [
                                            m(m.route.Link, { href: `/admin/users/edit/${user.id}`, class: "btn btn-sm btn-ghost" }, m(Icon, { icon: "fa-solid fa-user-pen" })),
                                            m("button.btn.btn-sm.btn-ghost.text-error", {
                                                onclick: () => this.deleteUser(user.id),
                                                title: "Delete User"
                                            }, m(Icon, { icon: "fa-solid fa-trash" }))
                                        ])
                                    ]))
                            ])
                        ])
                    ])
        ]);
    }
};

export default UsersPage;
