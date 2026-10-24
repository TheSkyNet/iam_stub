import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { OAuthService } from "../../services/OAuthService";
import Modal from "../../components/DaisyUI/Modal";
import { Fieldset, FormField, CheckboxField, SubmitButton } from "../../components/Form";

const LoginFormPage = {
    email: '',
    password: '',
    rememberMe: true,
    isLoading: false,
    
    // QR Login state
    showQrModal: false,
    qrCodeData: null,
    qrLoading: false,
    qrStatus: 'pending', // pending, authenticated, expired, error

    // OAuth state
    providers: [],

    oninit: function() {
        // Load OAuth providers
        OAuthService.getProviders().then(providers => {
            this.providers = providers || [];
            m.redraw();
        }).catch(() => {
            this.providers = [];
        });
    },

    handleLogin: (e) => {
        e.preventDefault();
        LoginFormPage.isLoading = true;
        
        AuthService.login(LoginFormPage.email, LoginFormPage.password, LoginFormPage.rememberMe)
            .then((response) => {
                window.showToast(response.message || 'Login successful!', 'success');
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

    handleOAuthLogin: (provider) => {
        OAuthService.initiateLogin(provider).catch(err => {
            window.showToast(err, 'error');
        });
    },

    handleShowQR: () => {
        LoginFormPage.showQrModal = true;
        LoginFormPage.qrLoading = true;
        LoginFormPage.qrStatus = 'pending';
        
        AuthService.generateQRCode()
            .then(response => {
                if (response.success) {
                    LoginFormPage.qrCodeData = response.data;
                    LoginFormPage.startQRPolling(response.data.session_token);
                } else {
                    LoginFormPage.qrStatus = 'error';
                    window.showToast(response.message, 'error');
                }
            })
            .catch(err => {
                LoginFormPage.qrStatus = 'error';
                window.showToast(err, 'error');
            })
            .finally(() => {
                LoginFormPage.qrLoading = false;
                m.redraw();
            });
    },

    startQRPolling: (token) => {
        AuthService.startQRPolling(
            token,
            (response) => {
                LoginFormPage.qrStatus = 'authenticated';
                window.showToast('Login successful!', 'success');
                LoginFormPage.showQrModal = false;
                m.route.set('/');
            },
            (error) => {
                LoginFormPage.qrStatus = 'error';
                window.showToast(error, 'error');
            },
            () => {
                LoginFormPage.qrStatus = 'expired';
                window.showToast('QR code expired. Please try again.', 'warning');
            }
        );
    },

    view: () => {
        return m(".hero.min-h-screen", {
            style: { backgroundImage: "url(https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80)" }
        }, [
            m(".hero-overlay.bg-opacity-40"),
            m(".hero-content.text-center", [
                m(".max-w-md", [
                    m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                        m(".card-body.p-8", [
                            m("h2.card-title.justify-center.text-2xl.font-bold.mb-2", "Login"),
                            m("p.mb-6.text-base-content.opacity-70", "Welcome back! Please enter your details."),
                            
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
                                
                                m("div.mt-2.flex.justify-between.items-center", [
                                    m(CheckboxField, {
                                        label: "Remember me",
                                        checked: LoginFormPage.rememberMe,
                                        onchange: (e) => LoginFormPage.rememberMe = e.target.checked
                                    }),
                                    m(m.route.Link, { href: "/forgot-password", class: "link link-hover text-xs" }, "Forgot password?")
                                ]),

                                m(SubmitButton, {
                                    class: "btn-primary w-full mt-6",
                                    loading: LoginFormPage.isLoading,
                                    icon: "fa-solid fa-right-to-bracket"
                                }, " Login")
                            ]),

                            // QR Login Button
                            m("button.btn.btn-outline.w-full.mt-4", {
                                onclick: LoginFormPage.handleShowQR
                            }, [
                                m(Icon, { icon: "fa-solid fa-qrcode" }),
                                " Login with QR Code"
                            ]),

                            // OAuth Buttons
                            LoginFormPage.providers.length > 0 && [
                                m(".divider", "OR"),
                                m(".flex.flex-wrap.justify-center.gap-4", LoginFormPage.providers.map(provider => {
                                    const config = OAuthService.getProviderConfig(provider);
                                    return m("button.btn.btn-circle.btn-outline", {
                                        title: `Login with ${config.name}`,
                                        onclick: () => LoginFormPage.handleOAuthLogin(provider),
                                        style: { color: config.color, borderColor: config.color }
                                    }, m(Icon, { icon: config.icon }));
                                }))
                            ],

                            m(".divider", "OR"),
                            m(".text-center", [
                                m("p.text-sm", "Don't have an account?"),
                                m(m.route.Link, { href: "/register", class: "link link-primary font-semibold" }, "Register now")
                            ])
                        ])
                    ]),

                    // QR Modal
                    m(Modal, {
                        id: "qr-modal",
                        open: LoginFormPage.showQrModal,
                        onClose: () => {
                            LoginFormPage.showQrModal = false;
                            // Polling will stop automatically on next check if we add a flag, 
                            // but for now let's just keep it simple.
                        },
                        boxClass: "max-w-xs text-center"
                    }, [
                        m("h3.font-bold.text-lg.mb-4", "Scan to Login"),
                        LoginFormPage.qrLoading 
                            ? m(".flex.justify-center.p-8", m("span.loading.loading-spinner.loading-lg"))
                            : LoginFormPage.qrCodeData 
                                ? m(".flex.flex-col.items-center", [
                                    m("img.rounded-lg.shadow-md.mb-4", { 
                                        src: LoginFormPage.qrCodeData.qr_code,
                                        style: { width: '200px', height: '200px' }
                                    }),
                                    m("p.text-sm.opacity-70", "Open the mobile app and scan this code to log in instantly."),
                                    LoginFormPage.qrStatus === 'expired' && m(".alert.alert-warning.mt-4", "Code expired"),
                                    LoginFormPage.qrStatus === 'error' && m(".alert.alert-error.mt-4", "Login failed")
                                ])
                                : m("p.text-error", "Failed to load QR code")
                    ])
                ])
            ])
        ]);
    }
};

export default LoginFormPage;
