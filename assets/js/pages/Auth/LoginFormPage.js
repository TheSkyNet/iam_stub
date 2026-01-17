import m from "mithril";
import { Icon } from "../../components/Icon";

const LoginFormPage = {
    view: () => {
        return m(".flex.justify-center.items-center.min-h-screen.bg-base-200", [
            m(".card.w-96.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title.justify-center", "Login"),
                    m(".form-control", [
                        m("label.label", m("span.label-text", "Email")),
                        m("input.input.input-bordered", { type: "email", placeholder: "email@example.com" })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text", "Password")),
                        m("input.input.input-bordered", { type: "password", placeholder: "password" }),
                        m("label.label", [
                            m(m.route.Link, { href: "/forgot-password", class: "label-text-alt link link-hover" }, "Forgot password?")
                        ])
                    ]),
                    m(".form-control.mt-6", [
                        m("button.btn.btn-primary", [
                            m(Icon, { name: "fa-solid fa-right-to-bracket" }),
                            " Login"
                        ])
                    ]),
                    m(".divider", "OR"),
                    m(".text-center", [
                        m("p", "Don't have an account?"),
                        m(m.route.Link, { href: "/register", class: "link link-primary" }, "Register now")
                    ])
                ])
            ])
        ]);
    }
};

export default LoginFormPage;
