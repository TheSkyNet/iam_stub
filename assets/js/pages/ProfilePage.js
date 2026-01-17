import m from "mithril";
import { Icon } from "../components/Icon";

const ProfilePage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", [
                        m(Icon, { icon: "fa-solid fa-user" }),
                        " User Profile"
                    ]),
                    m("p", "Manage your account settings and preferences."),
                    m(".divider"),
                    m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                        m(".form-control", [
                            m("label.label", m("span.label-text", "Username")),
                            m("input.input.input-bordered", { type: "text", value: "johndoe", readonly: true })
                        ]),
                        m(".form-control", [
                            m("label.label", m("span.label-text", "Email")),
                            m("input.input.input-bordered", { type: "email", value: "john@example.com", readonly: true })
                        ])
                    ]),
                    m(".card-actions.justify-end.mt-6", [
                        m("button.btn.btn-primary", "Update Profile")
                    ])
                ])
            ])
        ]);
    }
};

export default ProfilePage;
