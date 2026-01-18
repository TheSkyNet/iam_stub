import m from "mithril";
import { Icon } from "../components/Icon";

var AdminUser = {
    list: [],
    user: {
        name: '',
        email: '',
        password: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/user",
            withCredentials: true,
        })
            .then(function (result) {
                AdminUser.list = result
            })
    },
    delete(id) {
        return m.request({
                method: "DELETE",
                url: "/api/v1/user/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    },

    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/user/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminUser.user = result
        })
    },
    addUser() {
        return m.request({
                method: "POST",
                url: "/api/v1/user",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminUser.user,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    },
    updateUser() {
        return m.request({
                method: "POST",
                url: "/api/v1/user",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminUser.user,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    }
};

var AdminUserList = {
    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminUser.load(vnode.attrs.id);
        }
        AdminUser.loadList();
    },
    view: function (vnode) {
        return m(".p-4.space-y-6", [
            m("h1.text-3xl.font-bold", "Users"),
            
            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                // Form Section
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", vnode.attrs.id ? "Edit User" : "Add New User"),
                        m("form", {
                            onsubmit: (e) => {
                                e.preventDefault();
                                if (vnode.attrs.id) {
                                    AdminUser.updateUser();
                                } else {
                                    AdminUser.addUser();
                                }
                            }
                        }, [
                            m(".space-y-4", [
                                m(".form-control", [
                                    m("label.label", m("span.label-text", "Name")),
                                    m("input.input.input-bordered", {
                                        type: "text",
                                        value: AdminUser.user.name,
                                        oninput: (e) => {
                                            AdminUser.user.name = e.target.value;
                                        },
                                        placeholder: "Enter name"
                                    })
                                ]),
                                
                                m(".form-control", [
                                    m("label.label", m("span.label-text", "Email")),
                                    m("input.input.input-bordered", {
                                        type: "email",
                                        value: AdminUser.user.email,
                                        oninput: (e) => {
                                            AdminUser.user.email = e.target.value;
                                        },
                                        placeholder: "Enter email"
                                    })
                                ]),
                                
                                m(".form-control", [
                                    m("label.label", m("span.label-text", "Password")),
                                    m("input.input.input-bordered", {
                                        type: "password",
                                        value: AdminUser.user.password,
                                        oninput: (e) => {
                                            AdminUser.user.password = e.target.value;
                                        },
                                        placeholder: "Enter password"
                                    })
                                ]),
                                
                                m(".card-actions.justify-end.mt-4", [
                                    m("button.btn.btn-primary.btn-block", { type: "submit" }, "Save User")
                                ])
                            ])
                        ])
                    ])
                ]),
                
                // List Section
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "User List"),
                        m(".overflow-x-auto", [
                            m("table.table.table-zebra", [
                                m("thead", [
                                    m("tr", [
                                        m("th", "User Info"),
                                        m("th.text-right", "Actions")
                                    ])
                                ]),
                                m("tbody", AdminUser.list.map((user) => {
                                    return m("tr", [
                                        m("td", [
                                            m(".font-bold", user.name || user.title),
                                            m(".text-sm.opacity-50", user.email)
                                        ]),
                                        m("td.text-right.space-x-2", [
                                            m(m.route.Link, {
                                                href: `/user/${user.id}`,
                                                class: "btn btn-sm btn-ghost"
                                            }, m(Icon, { icon: 'fa-solid fa-pencil' })),
                                            m("button.btn.btn-sm.btn-error.btn-ghost", {
                                                type: "button",
                                                onclick: () => {
                                                    if (confirm("Are you sure you want to delete this user?")) {
                                                        AdminUser.delete(user.id);
                                                    }
                                                }
                                            }, m(Icon, { icon: 'fa-solid fa-trash-can' }))
                                        ])
                                    ]);
                                }))
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    },
    oncreate: function (vnode) {

        //$grid.masonry('layout');

// trigger initial layout
        //  $grid.masonry();
    },
    onupdate: function (vnode) {

    }
};


export {AdminUserList, AdminUser}