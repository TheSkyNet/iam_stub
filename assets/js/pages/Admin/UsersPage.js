import m from "mithril";
import { Icon } from "../../components/Icon";

const UsersPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Users Management"),
                m(m.route.Link, { href: "/admin/users/add", class: "btn btn-primary" }, [
                    m(Icon, { icon: "fa-solid fa-user-plus" }),
                    " Add User"
                ])
            ]),
            m(".overflow-x-auto.bg-base-100.rounded-xl.shadow", [
                m("table.table", [
                    m("thead", [
                        m("tr", [
                            m("th", "ID"),
                            m("th", "Name"),
                            m("th", "Email"),
                            m("th", "Role"),
                            m("th.text-right", "Actions")
                        ])
                    ]),
                    m("tbody", [
                        m("tr", [
                            m("td", "1"),
                            m("td", "John Doe"),
                            m("td", "john@example.com"),
                            m("td", m(".badge.badge-ghost", "Admin")),
                            m("td.text-right", [
                                m(m.route.Link, { href: "/admin/users/edit/1", class: "btn btn-sm btn-ghost" }, m(Icon, { icon: "fa-solid fa-user-pen" })),
                                m("button.btn.btn-sm.btn-ghost.text-error", m(Icon, { icon: "fa-solid fa-trash" }))
                            ])
                        ]),
                        m("tr", [
                            m("td", "2"),
                            m("td", "Jane Smith"),
                            m("td", "jane@example.com"),
                            m("td", m(".badge.badge-ghost", "Member")),
                            m("td.text-right", [
                                m(m.route.Link, { href: "/admin/users/edit/2", class: "btn btn-sm btn-ghost" }, m(Icon, { icon: "fa-solid fa-user-pen" })),
                                m("button.btn.btn-sm.btn-ghost.text-error", m(Icon, { icon: "fa-solid fa-trash" }))
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default UsersPage;
