const {AuthService} = require("../services/AuthserviceService");

const Profile = {
    oninit: () => {
        // Redirect to login if not authenticated
        if (!AuthService.isLoggedIn()) {
            m.route.set('/login');
            return;
        }
    },
    
    view: () => {
        const user = AuthService.getUser();
        const userData = user?.user || user; // Handle different response formats
        
        return m(".container.mx-auto.p-6", [
            m(".max-w-2xl.mx-auto", [
                m(".bg-white.shadow-lg.rounded-lg.p-6", [
                    m("h1.text-3xl.font-bold.text-gray-800.mb-6", "User Profile"),
                    
                    // User Information Section
                    m(".mb-8", [
                        m("h2.text-xl.font-semibold.text-gray-700.mb-4", "Personal Information"),
                        m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                            m(".form-control", [
                                m("label.label", [
                                    m("span.label-text", "Name")
                                ]),
                                m("input.input.input-bordered", {
                                    type: "text",
                                    value: userData?.name || '',
                                    readonly: true
                                })
                            ]),
                            m(".form-control", [
                                m("label.label", [
                                    m("span.label-text", "Email")
                                ]),
                                m("input.input.input-bordered", {
                                    type: "email",
                                    value: userData?.email || '',
                                    readonly: true
                                })
                            ])
                        ])
                    ]),
                    
                    // API Key Management Section
                    m(".mb-8", [
                        m("h2.text-xl.font-semibold.text-gray-700.mb-4", "API Key Management"),
                        m(".bg-gray-50.p-4.rounded-lg", [
                            m("p.text-sm.text-gray-600.mb-4", 
                                "API keys allow you to authenticate with our API programmatically. Keep your API key secure and don't share it publicly."
                            ),
                            m(".flex.items-center.justify-between", [
                                m("div", [
                                    m("p.font-medium", "Current API Key:"),
                                    m("p.text-sm.text-gray-500.font-mono", 
                                        userData?.api_key ? userData.api_key : "No API key generated"
                                    )
                                ]),
                                m("button.btn.btn-primary", {
                                    onclick: () => {
                                        AuthService.generateApiKey()
                                            .then(response => {
                                                if (response.success) {
                                                    alert('API key generated successfully!');
                                                    // Refresh user data
                                                    AuthService.validateCurrentUser().then(() => {
                                                        m.redraw();
                                                    });
                                                } else {
                                                    alert('Failed to generate API key: ' + response.message);
                                                }
                                            })
                                            .catch(error => {
                                                alert('Error generating API key: ' + error.message);
                                            });
                                    }
                                }, "Generate New API Key")
                            ])
                        ])
                    ]),
                    
                    // Account Actions
                    m(".flex.justify-between.items-center", [
                        m("button.btn.btn-outline", {
                            onclick: () => {
                                m.route.set('/');
                            }
                        }, "Back to Home"),
                        
                        m("button.btn.btn-error", {
                            onclick: () => {
                                if (confirm('Are you sure you want to logout?')) {
                                    AuthService.logout().then(() => {
                                        m.route.set('/');
                                    });
                                }
                            }
                        }, "Logout")
                    ])
                ])
            ])
        ]);
    }
};

module.exports = {Profile};