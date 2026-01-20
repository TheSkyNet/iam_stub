import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, SubmitButton } from "../../components/Form";

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
                                m(FormField, {
                                    label: "Name",
                                    icon: "fa-solid fa-user",
                                    placeholder: "John Doe",
                                    autocomplete: "name",
                                    value: RegisterFormPage.name,
                                    oninput: (e) => RegisterFormPage.name = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    label: "Email",
                                    icon: "fa-solid fa-envelope",
                                    type: "email",
                                    placeholder: "email@example.com",
                                    autocomplete: "email",
                                    value: RegisterFormPage.email,
                                    oninput: (e) => RegisterFormPage.email = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    label: "Password",
                                    icon: "fa-solid fa-lock",
                                    type: "password",
                                    placeholder: "••••••••",
                                    autocomplete: "new-password",
                                    value: RegisterFormPage.password,
                                    oninput: (e) => RegisterFormPage.password = e.target.value,
                                    required: true
                                }),

                                m(SubmitButton, {
                                    class: "btn-primary w-full mt-6",
                                    loading: RegisterFormPage.isLoading,
                                    icon: "fa-solid fa-user-plus"
                                }, " Register")
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
