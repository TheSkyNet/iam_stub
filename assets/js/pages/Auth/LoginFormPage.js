import m from "mithril";
import { Icon } from "../../components/Icon";

const LoginFormPage = {
    view: () => {
        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body", [
                            m("h2.card-title.justify-center.text-2xl.font-bold", "Login"),
                            m("p.mb-4", { class: "text-base-content/70" }, "Welcome back! Please enter your details."),
                            
                            m("fieldset.fieldset", [
                                m("legend.fieldset-legend", "Email"),
                                m("label.input.w-full", [
                                    m(Icon, { icon: "fa-solid fa-envelope", class: "opacity-50" }),
                                    m("input", { type: "email", placeholder: "email@example.com", class: "grow" })
                                ]),

                                m("legend.fieldset-legend", "Password"),
                                m("label.input.w-full", [
                                    m(Icon, { icon: "fa-solid fa-lock", class: "opacity-50" }),
                                    m("input", { type: "password", placeholder: "••••••••", class: "grow" })
                                ]),
                                
                                m("div.mt-2", [
                                    m(m.route.Link, { href: "/forgot-password", class: "link link-hover text-xs" }, "Forgot password?")
                                ]),

                                m("button.btn.btn-primary.w-full.mt-6", [
                                    m(Icon, { icon: "fa-solid fa-right-to-bracket" }),
                                    " Login"
                                ])
                            ]),

                            m(".divider", "OR"),
                            m(".text-center", [
                                m("p.text-sm", "Don't have an account?"),
                                m(m.route.Link, { href: "/register", class: "link link-primary font-semibold" }, "Register now")
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default LoginFormPage;
