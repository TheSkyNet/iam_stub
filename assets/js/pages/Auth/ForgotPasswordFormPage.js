import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const ForgotPasswordFormPage = {
    email: '',
    isLoading: false,
    isSent: false,

    handleForgotPassword: (e) => {
        e.preventDefault();
        ForgotPasswordFormPage.isLoading = true;

        AuthService.resendPasswordReset(ForgotPasswordFormPage.email)
            .then(() => {
                ForgotPasswordFormPage.isSent = true;
            })
            .catch((err) => {
                console.error('Forgot password error:', err);
                window.showToast(err, 'error');
            })
            .finally(() => {
                ForgotPasswordFormPage.isLoading = false;
                m.redraw();
            });
    },

    view: () => {
        let resetIcon = m(Icon, { icon: "fa-solid fa-paper-plane" });
        if (ForgotPasswordFormPage.isLoading) {
            resetIcon = m("span.loading.loading-spinner");
        }

        let content;
        if (ForgotPasswordFormPage.isSent) {
            content = [
                m(".alert.alert-success.mt-4", [
                    m(Icon, { icon: "fa-solid fa-circle-check" }),
                    m("span", "If an account with that email exists, a password reset link has been sent.")
                ]),
                m(".text-center.mt-6", [
                    m(m.route.Link, { href: "/login", class: "btn btn-outline btn-sm" }, "Back to Login")
                ])
            ];
        } else {
            content = [
                m("p.text-sm.mb-4.text-base-content.opacity-70", "Enter your email address and we'll send you a link to reset your password."),

                m("form", { onsubmit: ForgotPasswordFormPage.handleForgotPassword }, [
                    m("fieldset.fieldset", [
                        m("legend.fieldset-legend", "Email"),
                        m("label.input.w-full", [
                            m(Icon, { icon: "fa-solid fa-envelope", class: "opacity-50" }),
                            m("input", { 
                                type: "email", 
                                placeholder: "email@example.com", 
                                class: "grow",
                                value: ForgotPasswordFormPage.email,
                                oninput: (e) => ForgotPasswordFormPage.email = e.target.value,
                                required: true
                            })
                        ]),

                        m("button.btn.btn-primary.w-full.mt-6", { 
                            type: "submit",
                            disabled: ForgotPasswordFormPage.isLoading
                        }, [
                            resetIcon,
                            " Send Reset Link"
                        ])
                    ]),
                ]),

                m(".text-center.mt-6", [
                    m(m.route.Link, { href: "/login", class: "link link-hover text-sm flex items-center justify-center gap-2" }, [
                        m(Icon, { icon: "fa-solid fa-arrow-left", class: "text-xs" }),
                        "Back to Login"
                    ])
                ])
            ];
        }

        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1454165833767-1290b4046bcd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body", [
                            m("h2.card-title.justify-center.text-2xl.font-bold", "Forgot Password"),
                            content
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default ForgotPasswordFormPage;
