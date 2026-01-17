import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const RegisterFormPage = {
    name: '',
    email: '',
    password: '',
    isLoading: false,

    handleRegister: (e) => {
        e.preventDefault();
        RegisterFormPage.isLoading = true;

        AuthService.register(RegisterFormPage.name, RegisterFormPage.email, RegisterFormPage.password)
            .then(() => {
                m.route.set('/');
            })
            .catch((err) => {
                console.error('Registration error:', err);
                window.showToast(err, 'error');
            })
            .finally(() => {
                RegisterFormPage.isLoading = false;
                m.redraw();
            });
    },

    view: () => {
        const registerIcon = RegisterFormPage.isLoading 
            ? m("span.loading.loading-spinner")
            : m(Icon, { icon: "fa-solid fa-user-plus" });

        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1550745165-9bc0b252726f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body", [
                            m("h2.card-title.justify-center.text-2xl.font-bold", "Register"),
                            m("p.mb-4.text-base-content.opacity-70", "Join us today! Create your account."),

                            m("form", { onsubmit: RegisterFormPage.handleRegister }, [
                                m("fieldset.fieldset", [
                                    m("legend.fieldset-legend", "Name"),
                                    m("label.input.w-full", [
                                        m(Icon, { icon: "fa-solid fa-user", class: "opacity-50" }),
                                        m("input", { 
                                            type: "text", 
                                            placeholder: "John Doe", 
                                            class: "grow",
                                            value: RegisterFormPage.name,
                                            oninput: (e) => RegisterFormPage.name = e.target.value,
                                            required: true
                                        })
                                    ]),

                                    m("legend.fieldset-legend", "Email"),
                                    m("label.input.w-full", [
                                        m(Icon, { icon: "fa-solid fa-envelope", class: "opacity-50" }),
                                        m("input", { 
                                            type: "email", 
                                            placeholder: "email@example.com", 
                                            class: "grow",
                                            value: RegisterFormPage.email,
                                            oninput: (e) => RegisterFormPage.email = e.target.value,
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
                                            value: RegisterFormPage.password,
                                            oninput: (e) => RegisterFormPage.password = e.target.value,
                                            required: true
                                        })
                                    ]),

                                    m("button.btn.btn-primary.w-full.mt-6", { 
                                        type: "submit",
                                        disabled: RegisterFormPage.isLoading
                                    }, [
                                        registerIcon,
                                        " Register"
                                    ])
                                ]),
                            ]),

                            m(".divider", "OR"),
                            m(".text-center", [
                                m("p.text-sm", "Already have an account?"),
                                m(m.route.Link, { href: "/login", class: "link link-primary font-semibold" }, "Login here")
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default RegisterFormPage;
