import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const LoginFormPage = {
    email: '',
    password: '',
    isLoading: false,

    handleLogin: (e) => {
        e.preventDefault();
        LoginFormPage.isLoading = true;
        
        AuthService.login(LoginFormPage.email, LoginFormPage.password)
            .then(() => {
                m.route.set('/');
            })
            .catch((err) => {
                console.error('Login error:', err);
                window.showToast(err, 'error');
            })
            .finally(() => {
                LoginFormPage.isLoading = false;
                m.redraw();
            });
    },

    view: () => {
        let loginIcon = m(Icon, { icon: "fa-solid fa-right-to-bracket" });
        if (LoginFormPage.isLoading) {
            loginIcon = m("span.loading.loading-spinner");
        }

        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body", [
                            m("h2.card-title.justify-center.text-2xl.font-bold", "Login"),
                            m("p.mb-4.text-base-content.opacity-70", "Welcome back! Please enter your details."),
                            
                            m("form", { onsubmit: LoginFormPage.handleLogin }, [
                                m("fieldset.fieldset", [
                                    m("legend.fieldset-legend", "Email"),
                                    m("label.input.w-full", [
                                        m(Icon, { icon: "fa-solid fa-envelope", class: "opacity-50" }),
                                        m("input", { 
                                            type: "email", 
                                            placeholder: "email@example.com", 
                                            class: "grow",
                                            value: LoginFormPage.email,
                                            oninput: (e) => LoginFormPage.email = e.target.value,
                                            required: true
                                        })
                                    ]),

                                    m("legend.fieldset-legend", "Password"),
                                    m("label.input.w-full", [
                                        m(Icon, { icon: "fa-solid fa-lock", class: "opacity-50" }),
                                        m("input", { 
                                            type: "password", 
                                            placeholder: "••••••••", 
                                            class: "grow",
                                            value: LoginFormPage.password,
                                            oninput: (e) => LoginFormPage.password = e.target.value,
                                            required: true
                                        })
                                    ]),
                                    
                                    m("div.mt-2.text-left", [
                                        m(m.route.Link, { href: "/forgot-password", class: "link link-hover text-xs" }, "Forgot password?")
                                    ]),

                                    m("button.btn.btn-primary.w-full.mt-6", { 
                                        type: "submit",
                                        disabled: LoginFormPage.isLoading
                                    }, [
                                        loginIcon,
                                        " Login"
                                    ])
                                ]),
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
