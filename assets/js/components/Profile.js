const {AuthService} = require("../services/AuthserviceService");
const {MessageDisplay} = require("./MessageDisplay");

const Profile = {
    // Mobile QR scanner state
    qrScanner: {
        isScanning: false,
        scannedToken: null,
        isProcessing: false
    },

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
            m(MessageDisplay),
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

                    // Mobile Login Section
                    m(".mb-8", [
                        m("h2.text-xl.font-semibold.text-gray-700.mb-4", "Mobile Login"),
                        m(".bg-blue-50.p-4.rounded-lg", [
                            m("p.text-sm.text-gray-600.mb-4", 
                                "Use your mobile device to scan QR codes and authenticate on desktop computers. This allows you to securely login to your account from any desktop using your phone."
                            ),

                            // QR Scanner Interface
                            !Profile.qrScanner.isScanning ? [
                                m(".flex.items-center.justify-between.mb-4", [
                                    m("div", [
                                        m("p.font-medium", "QR Code Scanner"),
                                        m("p.text-sm.text-gray-500", "Scan QR codes from desktop login pages")
                                    ]),
                                    m("button.btn.btn-primary", {
                                        onclick: () => {
                                            Profile.qrScanner.isScanning = true;
                                            Profile.qrScanner.scannedToken = null;
                                            m.redraw();
                                        }
                                    }, "Start Scanner")
                                ])
                            ] : [
                                // Scanner Active State
                                m(".text-center.mb-4", [
                                    m("div.bg-white.border-2.border-dashed.border-blue-300.rounded-lg.p-8.mb-4", [
                                        m("div.text-6xl.text-blue-400.mb-4", "📱"),
                                        m("h3.text-lg.font-semibold.text-gray-700.mb-2", "QR Scanner Active"),
                                        m("p.text-sm.text-gray-600.mb-4", "Point your camera at a QR code on a desktop login page"),

                                        // Manual Token Input (for testing/fallback)
                                        m(".mt-4", [
                                            m("p.text-xs.text-gray-500.mb-2", "Or paste QR code data manually:"),
                                            m("textarea.textarea.textarea-bordered.w-full.text-xs", {
                                                placeholder: "Paste QR code JSON data here...",
                                                rows: 3,
                                                onchange: (e) => {
                                                    try {
                                                        const qrData = JSON.parse(e.target.value);
                                                        if (qrData.type === 'qr_login' && qrData.session_token) {
                                                            Profile.qrScanner.scannedToken = qrData.session_token;
                                                            m.redraw();
                                                        }
                                                    } catch (error) {
                                                        console.error('Invalid QR data:', error);
                                                    }
                                                }
                                            })
                                        ])
                                    ]),

                                    // Scanned Token Display
                                    Profile.qrScanner.scannedToken ? [
                                        m(".bg-green-50.border.border-green-200.rounded-lg.p-4.mb-4", [
                                            m("div.flex.items-center.mb-2", [
                                                m("span.text-green-600.mr-2", "✓"),
                                                m("span.font-semibold.text-green-800", "QR Code Detected")
                                            ]),
                                            m("p.text-sm.text-gray-600.mb-3", "Session Token: " + Profile.qrScanner.scannedToken.substring(0, 20) + "..."),
                                            m(".flex.gap-2", [
                                                m("button.btn.btn-success.btn-sm", {
                                                    disabled: Profile.qrScanner.isProcessing,
                                                    onclick: () => {
                                                        Profile.qrScanner.isProcessing = true;
                                                        AuthService.authenticateQR(Profile.qrScanner.scannedToken)
                                                            .then(response => {
                                                                Profile.qrScanner.isProcessing = false;
                                                                if (response.success) {
                                                                    MessageDisplay.setMessage('Desktop login authenticated successfully!', 'success');
                                                                    Profile.qrScanner.isScanning = false;
                                                                    Profile.qrScanner.scannedToken = null;
                                                                } else {
                                                                    MessageDisplay.setMessage(response.message || 'Authentication failed', 'error');
                                                                }
                                                                m.redraw();
                                                            })
                                                            .catch(error => {
                                                                Profile.qrScanner.isProcessing = false;
                                                                MessageDisplay.setMessage('Authentication failed: ' + error.message, 'error');
                                                                m.redraw();
                                                            });
                                                    }
                                                }, Profile.qrScanner.isProcessing ? "Authenticating..." : "Authenticate Desktop"),
                                                m("button.btn.btn-outline.btn-sm", {
                                                    onclick: () => {
                                                        Profile.qrScanner.scannedToken = null;
                                                        m.redraw();
                                                    }
                                                }, "Scan Again")
                                            ])
                                        ])
                                    ] : null,

                                    m("button.btn.btn-outline.btn-sm", {
                                        onclick: () => {
                                            Profile.qrScanner.isScanning = false;
                                            Profile.qrScanner.scannedToken = null;
                                            Profile.qrScanner.isProcessing = false;
                                            m.redraw();
                                        }
                                    }, "Cancel Scanner")
                                ])
                            ],

                            // Instructions
                            m(".mt-4.p-3.bg-blue-100.rounded-lg", [
                                m("h4.font-semibold.text-blue-800.mb-2", "How to use Mobile Login:"),
                                m("ol.text-sm.text-blue-700.space-y-1", [
                                    m("li", "1. Go to the login page on a desktop computer"),
                                    m("li", "2. Click the 'QR Code' tab on the login form"),
                                    m("li", "3. Use this scanner to scan the QR code displayed"),
                                    m("li", "4. Click 'Authenticate Desktop' to complete the login")
                                ])
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
