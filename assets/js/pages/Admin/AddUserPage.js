import m from "mithril";
import { Icon } from "../../components/Icon";

const AddUserPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".max-w-2xl.mx-auto", [
                m(".flex.items-center.gap-4.mb-6", [
                    m(m.route.Link, { href: "/admin/users", class: "btn btn-circle btn-ghost" }, m(Icon, { icon: "fa-solid fa-arrow-left" })),
                    m("h1.text-3xl.font-bold", "Add New User")
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Full Name")),
                                m("input.input.input-bordered", { type: "text", placeholder: "Jane Doe" })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Email")),
                                m("input.input.input-bordered", { type: "email", placeholder: "jane@example.com" })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Role")),
                                m("select.select.select-bordered", [
                                    m("option", { disabled: true, selected: true }, "Pick a role"),
                                    m("option", "Admin"),
                                    m("option", "Editor"),
                                    m("option", "Member")
                                ])
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Password")),
                                m("input.input.input-bordered", { type: "password", placeholder: "********" })
                            ])
                        ]),
                        m(".card-actions.justify-end.mt-6", [
                            m(m.route.Link, { href: "/admin/users", class: "btn btn-ghost" }, "Cancel"),
                            m("button.btn.btn-primary", [
                                m(Icon, { icon: "fa-solid fa-user-plus" }),
                                " Create User"
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default AddUserPage;
