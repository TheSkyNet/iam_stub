import m from "mithril";
import { Icon } from "../../components/Icon";

const EditUserPage = {
    view: (vnode) => {
        const userId = vnode.attrs.id;
        return m(".container.mx-auto.p-4", [
            m(".max-w-2xl.mx-auto", [
                m(".flex.items-center.gap-4.mb-6", [
                    m(m.route.Link, { href: "/admin/users", class: "btn btn-circle btn-ghost" }, m(Icon, { icon: "fa-solid fa-arrow-left" })),
                    m("h1.text-3xl.font-bold", `Edit User #${userId}`)
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Full Name")),
                                m("input.input.input-bordered", { type: "text", value: "John Doe" })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Email")),
                                m("input.input.input-bordered", { type: "email", value: "john@example.com" })
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Role")),
                                m("select.select.select-bordered", [
                                    m("option", { selected: true }, "Admin"),
                                    m("option", "Editor"),
                                    m("option", "Member")
                                ])
                            ]),
                            m(".form-control", [
                                m("label.label", m("span.label-text", "Status")),
                                m(".flex.items-center.gap-2.mt-2", [
                                    m("input.toggle.toggle-success", { type: "checkbox", checked: true }),
                                    m("span", "Active")
                                ])
                            ])
                        ]),
                        m(".card-actions.justify-end.mt-6", [
                            m(m.route.Link, { href: "/admin/users", class: "btn btn-ghost" }, "Cancel"),
                            m("button.btn.btn-primary", [
                                m(Icon, { icon: "fa-solid fa-save" }),
                                " Save Changes"
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default EditUserPage;
