import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, SubmitButton } from "../../components/Form";

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
                window.showToast(err, 'error');
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
                m("p.text-sm.mb-4.text-base-content.opacity-70", "Please enter and confirm your new password."),

                m("form", { onsubmit: ResetPasswordFormPage.handleResetPassword }, [
                    m(FormField, {
                        label: "New Password",
                        icon: "fa-solid fa-lock",
                        type: "password",
                        placeholder: "••••••••",
                        value: ResetPasswordFormPage.password,
                        oninput: (e) => ResetPasswordFormPage.password = e.target.value,
                        required: true
                    }),
                    m(FormField, {
                        label: "Confirm New Password",
                        icon: "fa-solid fa-lock",
                        type: "password",
                        placeholder: "••••••••",
                        value: ResetPasswordFormPage.confirmPassword,
                        oninput: (e) => ResetPasswordFormPage.confirmPassword = e.target.value,
                        required: true
                    }),

                    m(SubmitButton, {
                        class: "btn-primary w-full mt-6",
                        loading: ResetPasswordFormPage.isLoading,
                        icon: "fa-solid fa-key"
                    }, " Reset Password")
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
