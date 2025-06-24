// LoginModule.js
const {MessageDisplay} = require("../components/MessageDisplay");
const Login = {
    user: {
        email: '',
        password: '',
    },

    login: function() {
        return m.request({
            method: "POST",
            url: "/auth",
            withCredentials: true,
            body: Login.user,
        }).then(function(result) {
            if (!result.success) {
                MessageDisplay.setMessage(result.message, 'error');
                return;
            }

            Object.assign(Auth, result.data);
            MessageDisplay.setMessage(result.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }).catch(error => {
            let errorMessage = 'Unable to connect to the server. Please try again later.';

            if (error.response && error.response.message) {
                errorMessage = error.response.message;
            } else if (error.code === 429) {
                errorMessage = 'Too many attempts. Please try again later.';
            }

            MessageDisplay.setMessage(errorMessage, 'error');
            console.error("Login failed:", error);
        });
    }
};
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
                window.location.href = '/';  // Changed from '/login' to '/'
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


const Auth = {};

const LoginList = {
    view: function() {
        return [
            m(MessageDisplay), // Add the message display at the top
            m(".login-page", [
            m(".login-container", [
                m(".card.shadow-lg", [
                    m(".card-header.text-center.bg-white.border-0.pt-4", [
                        m("h4.text-dark.mb-0", "Welcome Back"),
                        m("p.text-muted.small", "Please sign in to continue")
                    ]),
                    m(".card-body.px-4.py-4", [
                        m("form", {
                            onsubmit: (e) => {
                                e.preventDefault();
                                Login.login();
                            }
                        }, [
                            m(".form-group", [
                                m("label.small.text-muted", "Email Address"),
                                m("input.form-control.form-control-lg[type=email][required]", {
                                    placeholder: "Enter your email",
                                    value: Login.user.email,
                                    onchange: (e) => {
                                        Login.user.email = e.target.value;
                                    }
                                })
                            ]),
                            m(".form-group", [
                                m("label.small.text-muted", "Password"),
                                m("input.form-control.form-control-lg[type=password][required]", {
                                    placeholder: "Enter your password",
                                    value: Login.user.password,
                                    onchange: (e) => {
                                        Login.user.password = e.target.value;
                                    }
                                })
                            ]),
                            m(".form-group", [
                                m(".custom-control.custom-checkbox", [
                                    m("input.custom-control-input#rememberMe[type=checkbox]"),
                                    m("label.custom-control-label.small[for=rememberMe]",
                                        "Remember me"
                                    )
                                ])
                            ]),
                            m("button.btn.btn-primary.btn-block.btn-lg.mb-3[type=submit]",
                                "Sign In"
                            )
                        ])
                    ])
                ])
            ])
        ])
        ];
    }
};

module.exports = { LoginList, Login, Auth , Logout};