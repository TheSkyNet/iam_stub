import m from "mithril";
import { Icon } from "../../components/Icon";

const RegisterFormPage = {
    view: () => {
        return m(".flex.justify-center.items-center.min-h-screen.bg-base-200", [
            m(".card.w-96.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title.justify-center", "Register"),
                    m(".form-control", [
                        m("label.label", m("span.label-text", "Name")),
                        m("input.input.input-bordered", { type: "text", placeholder: "John Doe" })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text", "Email")),
                        m("input.input.input-bordered", { type: "email", placeholder: "email@example.com" })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text", "Password")),
                        m("input.input.input-bordered", { type: "password", placeholder: "password" })
                    ]),
                    m(".form-control.mt-6", [
                        m("button.btn.btn-primary", [
                            m(Icon, { icon: "fa-solid fa-user-plus" }),
                            " Register"
                        ])
                    ]),
                    m(".divider", "OR"),
                    m(".text-center", [
                        m("p", "Already have an account?"),
                        m(m.route.Link, { href: "/login", class: "link link-primary" }, "Login here")
                    ])
                ])
            ])
        ]);
    }
};

export default RegisterFormPage;
