// LoginModule.js
import m from "mithril";
// Import MessageDisplay and AuthService
const {MessageDisplay} = require("../components/MessageDisplay");
const {AuthService} = require("../services/AuthserviceService");
const {OAuthButtons, OAuthCallback} = require("../components/OAuthButtons");

// Authentication state
const Auth = {};

// Login service
const Login = {
    user: {
        email: '',
        password: '',
        rememberMe: false,
    },

    login: function() {
        return AuthService.login(Login.user.email, Login.user.password, Login.user.rememberMe)
            .then(function(result) {
                MessageDisplay.setMessage(result.message, 'success');
                setTimeout(() => {
                    m.route.set('/');
                    m.redraw();
                }, 1500);
            })
            .catch(error => {
                let errorMessage = 'Unable to connect to the server. Please try again later.';

                if (error.message) {
                    errorMessage = error.message;
                }

                MessageDisplay.setMessage(errorMessage, 'error');
                console.error("Login failed:", error);
            });
    }
};

// QR Code Login service
const QRLogin = {
    qrData: null,
    sessionToken: null,
    isPolling: false,
    showQR: false,

    generateQR: function() {
        QRLogin.showQR = true;
        QRLogin.qrData = null;
        QRLogin.sessionToken = null;

        return AuthService.generateQRCode()
            .then(function(result) {
                if (result.success) {
                    QRLogin.qrData = result.data;
                    QRLogin.sessionToken = result.data.session_token;
                    QRLogin.startPolling();
                    m.redraw();
                } else {
                    MessageDisplay.setMessage(result.message || 'Failed to generate QR code', 'error');
                }
            })
            .catch(error => {
                MessageDisplay.setMessage('Failed to generate QR code. Please try again.', 'error');
                console.error("QR generation failed:", error);
            });
    },

    startPolling: function() {
        if (QRLogin.isPolling || !QRLogin.sessionToken) return;

        QRLogin.isPolling = true;

        AuthService.startQRPolling(
            QRLogin.sessionToken,
            // onSuccess
            function() {
                QRLogin.isPolling = false;
                QRLogin.showQR = false;
                MessageDisplay.setMessage('Login successful via QR code!', 'success');
                setTimeout(() => {
                    m.route.set('/');
                    m.redraw();
                }, 1500);
            },
            // onError
            function(error) {
                QRLogin.isPolling = false;
                MessageDisplay.setMessage('QR login failed. Please try again.', 'error');
                console.error("QR login failed:", error);
                m.redraw();
            },
            // onExpired
            function() {
                QRLogin.isPolling = false;
                QRLogin.showQR = false;
                MessageDisplay.setMessage('QR code expired. Please generate a new one.', 'warning');
                m.redraw();
            }
        );
    },

    cancelQR: function() {
        QRLogin.isPolling = false;
        QRLogin.showQR = false;
        QRLogin.qrData = null;
        QRLogin.sessionToken = null;
        m.redraw();
    },

    authenticateFromMobile: function(sessionToken) {
        return AuthService.authenticateQR(sessionToken)
            .then(function(result) {
                if (result.success) {
                    MessageDisplay.setMessage('QR code authenticated successfully!', 'success');
                } else {
                    MessageDisplay.setMessage(result.message || 'Failed to authenticate QR code', 'error');
                }
                return result;
            })
            .catch(error => {
                MessageDisplay.setMessage('Failed to authenticate QR code. Please try again.', 'error');
                console.error("QR authentication failed:", error);
                throw error;
            });
    }
};

// Register service
const Register = {
    user: {
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
    },

    register: function() {
        if (Register.user.password !== Register.user.confirmPassword) {
            MessageDisplay.setMessage("Passwords do not match", 'error');
            return;
        }

        return AuthService.register(Register.user.name, Register.user.email, Register.user.password)
            .then(function() {
                MessageDisplay.setMessage("Registration successful! You are now logged in.", 'success');
                setTimeout(() => {
                    m.route.set('/');
                    m.redraw();
                }, 1500);
            })
            .catch(error => {
                let errorMessage = 'Unable to connect to the server. Please try again later.';

                if (error.message) {
                    errorMessage = error.message;
                }

                MessageDisplay.setMessage(errorMessage, 'error');
                console.error("Registration failed:", error);
            });
    }
};

// Forgot password service
const ForgotPassword = {
    email: '',

    sendReset: function() {
        return m.request({
            method: "POST",
            url: "/auth/forgot-password",
            withCredentials: true,
            body: { email: ForgotPassword.email },
        }).then(function(result) {
            if (!result.success) {
                MessageDisplay.setMessage(result.message, 'error');
                return;
            }

            MessageDisplay.setMessage("Password reset email sent! Check your inbox.", 'success');
        }).catch(error => {
            let errorMessage = 'Unable to connect to the server. Please try again later.';

            if (error.response && error.response.message) {
                errorMessage = error.response.message;
            }

            MessageDisplay.setMessage(errorMessage, 'error');
            console.error("Password reset failed:", error);
        });
    }
};

// Logout service
const Logout = {
    logout: function() {
        return m.request({
            method: "POST",
            url: "/auth/logout",
            withCredentials: true
        }).then(function(result) {
            if (!result.success) {
                MessageDisplay.setMessage(result.message, 'error');
                return;
            }

            MessageDisplay.setMessage(result.message, 'success');
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
        }).catch(error => {
            let errorMessage = 'Unable to connect to the server. Please try again later.';

            if (error.response && error.response.message) {
                errorMessage = error.response.message;
            } else if (error.code === 429) {
                errorMessage = 'Too many attempts. Please try again later.';
            }

            MessageDisplay.setMessage(errorMessage, 'error');
            console.error("Logout failed:", error);
        });
    }
};

// Login component
const LoginForm = {
    view: function() {
        return [
            m(MessageDisplay),
            m(".min-h-screen.flex.items-center.justify-center.p-8", [
                m(".w-full.max-w-md.mx-auto", [
                    m(".card.bg-base-100.shadow-xl", [
                        m(".card-body.text-center.pt-8.pb-4", [
                            m("h2.text-2xl.font-bold.text-base-content.mb-2", "Welcome Back"),
                            m("p.text-sm.text-base-content.opacity-70", "Please sign in to continue")
                        ]),
                        m(".card-body.px-8.py-6", [
                            // Login method toggle
                            m(".tabs.tabs-boxed.mb-6", [
                                m("a.tab", {
                                    class: !QRLogin.showQR ? "tab-active" : "",
                                    onclick: () => {
                                        QRLogin.cancelQR();
                                    }
                                }, "Email & Password"),
                                m("a.tab", {
                                    class: QRLogin.showQR ? "tab-active" : "",
                                    onclick: () => {
                                        QRLogin.generateQR();
                                    }
                                }, "QR Code")
                            ]),

                            // Traditional login form
                            !QRLogin.showQR ? m("form", {
                                onsubmit: (e) => {
                                    e.preventDefault();
                                    Login.login();
                                }
                            }, [
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Email Address")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=email][required]", {
                                        placeholder: "Enter your email",
                                        value: Login.user.email,
                                        onchange: (e) => {
                                            Login.user.email = e.target.value;
                                        }
                                    })
                                ]),
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Password")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=password][required]", {
                                        placeholder: "Enter your password",
                                        value: Login.user.password,
                                        onchange: (e) => {
                                            Login.user.password = e.target.value;
                                        }
                                    })
                                ]),
                                m(".form-control.w-full.mb-4", [
                                    m(".label.cursor-pointer.justify-start.gap-3", [
                                        m("input.checkbox.checkbox-primary.checkbox-sm[type=checkbox]", {
                                            checked: Login.user.rememberMe,
                                            onchange: (e) => {
                                                Login.user.rememberMe = e.target.checked;
                                            }
                                        }),
                                        m("span.label-text.text-sm", "Remember me")
                                    ])
                                ]),
                                m("button.btn.btn-primary.w-full.mb-4[type=submit]", "Sign In"),

                                // OAuth buttons
                                m(OAuthButtons)
                            ]) : 

                            // QR Code login section
                            m(".text-center", [
                                QRLogin.qrData ? [
                                    m("div.mb-4", [
                                        m("p.text-sm.text-base-content.opacity-70.mb-4", "Scan this QR code with your mobile device to login"),
                                        m("div.flex.justify-center.mb-4", [
                                            m("img", {
                                                src: QRLogin.qrData.qr_code,
                                                alt: "QR Code for Login",
                                                style: "max-width: 250px; height: auto;"
                                            })
                                        ]),
                                        QRLogin.isPolling ? [
                                            m("div.flex.items-center.justify-center.gap-2.mb-4", [
                                                m("span.loading.loading-spinner.loading-sm"),
                                                m("span.text-sm", "Waiting for authentication...")
                                            ]),
                                            m("p.text-xs.text-base-content.opacity-50", "QR code expires in 5 minutes")
                                        ] : null,
                                        m("button.btn.btn-outline.btn-sm", {
                                            onclick: QRLogin.cancelQR
                                        }, "Cancel")
                                    ])
                                ] : [
                                    m("div.flex.items-center.justify-center.gap-2.mb-4", [
                                        m("span.loading.loading-spinner.loading-sm"),
                                        m("span.text-sm", "Generating QR code...")
                                    ])
                                ]
                            ]),

                            // Links (only show when not in QR mode)
                            !QRLogin.showQR ? m(".text-center.space-y-2", [
                                m("div", [
                                    m(m.route.Link, {
                                        class: "link link-primary text-sm",
                                        href: "/register"
                                    }, "Don't have an account? Sign up")
                                ]),
                                m("div", [
                                    m(m.route.Link, {
                                        class: "link link-primary text-sm",
                                        href: "/forgot-password"
                                    }, "Forgot your password?")
                                ])
                            ]) : null
                        ])
                    ])
                ])
            ])
        ];
    }
};

// Register component
const RegisterForm = {
    view: function() {
        return [
            m(MessageDisplay),
            m(".min-h-screen.flex.items-center.justify-center.p-8", [
                m(".w-full.max-w-md.mx-auto", [
                    m(".card.bg-base-100.shadow-xl", [
                        m(".card-body.text-center.pt-8.pb-4", [
                            m("h2.text-2xl.font-bold.text-base-content.mb-2", "Create Account"),
                            m("p.text-sm.text-base-content.opacity-70", "Please fill in your information")
                        ]),
                        m(".card-body.px-8.py-6", [
                            m("form", {
                                onsubmit: (e) => {
                                    e.preventDefault();
                                    Register.register();
                                }
                            }, [
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Full Name")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=text][required]", {
                                        placeholder: "Enter your full name",
                                        value: Register.user.name,
                                        onchange: (e) => {
                                            Register.user.name = e.target.value;
                                        }
                                    })
                                ]),
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Email Address")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=email][required]", {
                                        placeholder: "Enter your email",
                                        value: Register.user.email,
                                        onchange: (e) => {
                                            Register.user.email = e.target.value;
                                        }
                                    })
                                ]),
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Password")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=password][required]", {
                                        placeholder: "Enter your password",
                                        value: Register.user.password,
                                        onchange: (e) => {
                                            Register.user.password = e.target.value;
                                        }
                                    })
                                ]),
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Confirm Password")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=password][required]", {
                                        placeholder: "Confirm your password",
                                        value: Register.user.confirmPassword,
                                        onchange: (e) => {
                                            Register.user.confirmPassword = e.target.value;
                                        }
                                    })
                                ]),
                                m("button.btn.btn-primary.w-full.mb-4[type=submit]", "Create Account"),

                                // OAuth buttons
                                m(OAuthButtons),

                                m(".text-center.space-y-2", [
                                    m(m.route.Link, {
                                        class: "link link-primary text-sm",
                                        href: "/login"
                                    }, "Already have an account? Sign in")
                                ])
                            ])
                        ])
                    ])
                ])
            ])
        ];
    }
};

// Forgot password component
const ForgotPasswordForm = {
    view: function() {
        return [
            m(MessageDisplay),
            m(".min-h-screen.flex.items-center.justify-center.p-8", [
                m(".w-full.max-w-md.mx-auto", [
                    m(".card.bg-base-100.shadow-xl", [
                        m(".card-body.text-center.pt-8.pb-4", [
                            m("h2.text-2xl.font-bold.text-base-content.mb-2", "Reset Password"),
                            m("p.text-sm.text-base-content.opacity-70", "Enter your email to receive reset instructions")
                        ]),
                        m(".card-body.px-8.py-6", [
                            m("form", {
                                onsubmit: (e) => {
                                    e.preventDefault();
                                    ForgotPassword.sendReset();
                                }
                            }, [
                                m(".form-control.w-full.mb-4", [
                                    m(".label", [
                                        m("span.label-text.text-sm.font-medium", "Email Address")
                                    ]),
                                    m("input.input.input-bordered.w-full.focus:input-primary[type=email][required]", {
                                        placeholder: "Enter your email",
                                        value: ForgotPassword.email,
                                        onchange: (e) => {
                                            ForgotPassword.email = e.target.value;
                                        }
                                    })
                                ]),
                                m("button.btn.btn-primary.w-full.mb-4[type=submit]", "Send Reset Link"),
                                m(".text-center.space-y-2", [
                                    m(m.route.Link, {
                                        class: "link link-primary text-sm",
                                        href: "/login"
                                    }, "Back to login")
                                ])
                            ])
                        ])
                    ])
                ])
            ])
        ];
    }
};

// Legacy component for backward compatibility
const LoginList = LoginForm;

export { 
    LoginForm, 
    RegisterForm, 
    ForgotPasswordForm, 
    LoginList, 
    Login, 
    Register, 
    ForgotPassword, 
    Auth, 
    Logout,
    QRLogin,
    OAuthCallback
};
