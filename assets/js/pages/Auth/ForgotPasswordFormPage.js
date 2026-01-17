import m from "mithril";
import { Icon } from "../../components/Icon";

const ForgotPasswordFormPage = {
    view: () => {
        return m(".flex.justify-center.items-center.min-h-screen.bg-base-200", [
            m(".card.w-96.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title.justify-center", "Forgot Password"),
                    m("p.text-sm.text-center", "Enter your email address and we'll send you a link to reset your password."),
                    m(".form-control.mt-4", [
                        m("label.label", m("span.label-text", "Email")),
                        m("input.input.input-bordered", { type: "email", placeholder: "email@example.com" })
                    ]),
                    m(".form-control.mt-6", [
                        m("button.btn.btn-primary", [
                            m(Icon, { name: "fa-solid fa-paper-plane" }),
                            " Send Reset Link"
                        ])
                    ]),
                    m(".text-center.mt-4", [
                        m(m.route.Link, { href: "/login", class: "link link-hover text-sm" }, "Back to Login")
                    ])
                ])
            ])
        ]);
    }
};

export default ForgotPasswordFormPage;
