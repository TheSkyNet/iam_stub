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
                ]),
                m(".card.bg-accent.text-accent-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-list-check" }),
                            " Jobs"
                        ]),
                        m("p", "Monitor and manage background jobs."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/jobs", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ]),
                m(".card.bg-error.text-error-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-bug" }),
                            " Error Logs"
                        ]),
                        m("p", "View and cleanup application error logs."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/errors", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ]),
                m(".card.bg-info.text-info-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-gears" }),
                            " Settings"
                        ]),
                        m("p", "Manage site configuration and settings."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/settings", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ]),
                m(".card.bg-neutral.text-neutral-content.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-brain" }),
                            " LMS & AI"
                        ]),
                        m("p", "Configure LMS and AI platform integrations."),
                        m(".card-actions.justify-end", [
                            m(m.route.Link, { href: "/admin/lms", class: "btn btn-sm btn-ghost" }, "Manage")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default AdminPage;
