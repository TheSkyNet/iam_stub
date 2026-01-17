import m from "mithril";
import { Icon } from "../../components/Icon";

const AdminPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m("h1.text-3xl.font-bold.mb-6", "Admin Dashboard"),
            m(".grid.grid-cols-1.md:grid-cols-3.gap-6", [
                m(".card.bg-primary.text-primary-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-users" }),
                            " Users"
                        ]),
                        m("p", "Manage application users and their access."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/users", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ]),
                m(".card.bg-secondary.text-secondary-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-user-shield" }),
                            " Roles"
                        ]),
                        m("p", "Configure system roles and permissions."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/roles", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default AdminPage;
