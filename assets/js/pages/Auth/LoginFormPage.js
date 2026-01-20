import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, CheckboxField, SubmitButton } from "../../components/Form";

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
                                m(FormField, {
                                    label: "Email",
                                    icon: "fa-solid fa-envelope",
                                    type: "email",
                                    placeholder: "email@example.com",
                                    autocomplete: "email",
                                    value: LoginFormPage.email,
                                    oninput: (e) => LoginFormPage.email = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    label: "Password",
                                    icon: "fa-solid fa-lock",
                                    type: "password",
                                    placeholder: "••••••••",
                                    autocomplete: "current-password",
                                    value: LoginFormPage.password,
                                    oninput: (e) => LoginFormPage.password = e.target.value,
                                    required: true
                                }),
                                
                                m("div.mt-2.text-left", [
                                    m(m.route.Link, { href: "/forgot-password", class: "link link-hover text-xs" }, "Forgot password?")
                                ]),

                                m(SubmitButton, {
                                    class: "btn-primary w-full mt-6",
                                    loading: LoginFormPage.isLoading,
                                    icon: "fa-solid fa-right-to-bracket"
                                }, " Login")
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
