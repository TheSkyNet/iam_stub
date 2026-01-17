import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const ResetPasswordFormPage = {
    password: '',
    confirmPassword: '',
    isLoading: false,
    isSuccess: false,
    token: '',

    oninit: (vnode) => {
        ResetPasswordFormPage.token = m.route.param('token');
        if (!ResetPasswordFormPage.token) {
            window.showToast("Missing reset token.", "error");
            m.route.set('/login');
        }
    },

    handleResetPassword: (e) => {
        e.preventDefault();
        
        if (ResetPasswordFormPage.password !== ResetPasswordFormPage.confirmPassword) {
            window.showToast("Passwords do not match.", "error");
            return;
        }

        ResetPasswordFormPage.isLoading = true;

        AuthService.resetPassword(ResetPasswordFormPage.token, ResetPasswordFormPage.password)
            .then(() => {
                ResetPasswordFormPage.isSuccess = true;
                window.showToast("Password has been reset successfully.", "success");
            })
            .catch((err) => {
                console.error('Reset password error:', err);
            })
            .finally(() => {
                ResetPasswordFormPage.isLoading = false;
                m.redraw();
            });
    },

    view: () => {
        let content;
        if (ResetPasswordFormPage.isSuccess) {
            content = [
                m(".alert.alert-success.mt-4", [
                    m(Icon, { icon: "fa-solid fa-circle-check" }),
                    m("span", "Your password has been reset successfully.")
                ]),
                m(".text-center.mt-6", [
                    m(m.route.Link, { href: "/login", class: "btn btn-primary" }, "Go to Login")
                ])
            ];
        } else {
            content = [
                m("p.text-sm.mb-4", { class: "text-base-content/70" }, "Please enter your new password below."),

                m("form", { onsubmit: ResetPasswordFormPage.handleResetPassword }, [
                    m("fieldset.fieldset", [
                        m("legend.fieldset-legend", "New Password"),
                        m("label.input.w-full", [
                            m(Icon, { icon: "fa-solid fa-lock", class: "opacity-50" }),
                            m("input", { 
                                type: "password", 
                                placeholder: "••••••••", 
                                class: "grow",
                                value: ResetPasswordFormPage.password,
                                oninput: (e) => ResetPasswordFormPage.password = e.target.value,
                                required: true
                            })
                        ]),

                        m("legend.fieldset-legend", "Confirm New Password"),
                        m("label.input.w-full", [
                            m(Icon, { icon: "fa-solid fa-lock", class: "opacity-50" }),
                            m("input", { 
                                type: "password", 
                                placeholder: "••••••••", 
                                class: "grow",
                                value: ResetPasswordFormPage.confirmPassword,
                                oninput: (e) => ResetPasswordFormPage.confirmPassword = e.target.value,
                                required: true
                            })
                        ]),

                        m("button.btn.btn-primary.w-full.mt-6", { 
                            type: "submit",
                            disabled: ResetPasswordFormPage.isLoading
                        }, [
                            ResetPasswordFormPage.isLoading 
                                ? m("span.loading.loading-spinner")
                                : m(Icon, { icon: "fa-solid fa-key" }),
                            " Reset Password"
                        ])
                    ]),
                ])
            ];
        }

        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body", [
                            m("h2.card-title.justify-center.text-2xl.font-bold", "Reset Password"),
                            content
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default ResetPasswordFormPage;
