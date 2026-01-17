import m from "mithril";
const {AuthService} = require("../services/AuthserviceService");
const {MessageDisplay} = require("./MessageDisplay");
const { Icon } = require("./Icon");

const Profile = {
    // Mobile QR scanner state
    qrScanner: {
        isScanning: false,
        scannedToken: null,
        isProcessing: false
    },

    // Quick Mobile Login state (reverse flow)
    quickMobileLogin: {
        isGenerating: false,
        qrData: null,
        sessionToken: null,
        isPolling: false,
        showQR: false
    },

    oninit: () => {
        // Redirect to login if not authenticated
        if (!AuthService.isLoggedIn()) {
            m.route.set('/login');

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
                                        m("div.text-6xl.text-blue-400.mb-4", m(Icon, { name: 'fa-solid fa-mobile-screen-button' })),
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

                    // Quick Mobile Login Section (Reverse Flow)
                    m(".mb-8", [
                        m("h2.text-xl.font-semibold.text-gray-700.mb-4", "Quick Mobile Login"),
                        m(".bg-green-50.p-4.rounded-lg", [
                            m("p.text-sm.text-gray-600.mb-4", 
                                "Generate a QR code on your mobile device that you can scan from your PC to quickly log into your mobile device. Perfect for when you're already logged in on your computer and want to quickly access your account on your phone."
                            ),

                            // QR Generation Interface
                            !Profile.quickMobileLogin.showQR ? [
                                m(".flex.items-center.justify-between.mb-4", [
                                    m("div", [
                                        m("p.font-medium", "Mobile QR Code Generator"),
                                        m("p.text-sm.text-gray-500", "Generate QR code for desktop to scan")
                                    ]),
                                    m("button.btn.btn-success", {
                                        disabled: Profile.quickMobileLogin.isGenerating,
                                        onclick: () => {
                                            Profile.quickMobileLogin.isGenerating = true;
                                            Profile.quickMobileLogin.showQR = true;

                                            AuthService.generateMobileQRCode()
                                                .then(response => {
                                                    Profile.quickMobileLogin.isGenerating = false;
                                                    if (response.success) {
                                                        Profile.quickMobileLogin.qrData = response.data;
                                                        Profile.quickMobileLogin.sessionToken = response.data.session_token;
                                                        Profile.quickMobileLogin.isPolling = true;

                                                        // Start polling for desktop authentication
                                                        AuthService.startMobileQRPolling(
                                                            Profile.quickMobileLogin.sessionToken,
                                                            // onSuccess
                                                            function() {
                                                                Profile.quickMobileLogin.isPolling = false;
                                                                Profile.quickMobileLogin.showQR = false;
                                                                MessageDisplay.setMessage('Mobile login successful! You are now logged in.', 'success');
                                                                m.redraw();
                                                            },
                                                            // onError
                                                            function(error) {
                                                                Profile.quickMobileLogin.isPolling = false;
                                                                MessageDisplay.setMessage('Mobile login failed. Please try again.', 'error');
                                                                console.error("Mobile QR login failed:", error);
                                                                m.redraw();
                                                            },
                                                            // onExpired
                                                            function() {
                                                                Profile.quickMobileLogin.isPolling = false;
                                                                Profile.quickMobileLogin.showQR = false;
                                                                MessageDisplay.setMessage('QR code expired. Please generate a new one.', 'warning');
                                                                m.redraw();
                                                            }
                                                        );

                                                        m.redraw();
                                                    } else {
                                                        MessageDisplay.setMessage(response.message || 'Failed to generate mobile QR code', 'error');
                                                        Profile.quickMobileLogin.showQR = false;
                                                        m.redraw();
                                                    }
                                                })
                                                .catch(error => {
                                                    Profile.quickMobileLogin.isGenerating = false;
                                                    Profile.quickMobileLogin.showQR = false;
                                                    MessageDisplay.setMessage('Failed to generate mobile QR code. Please try again.', 'error');
                                                    console.error("Mobile QR generation failed:", error);
                                                    m.redraw();
                                                });
                                        }
                                    }, Profile.quickMobileLogin.isGenerating ? "Generating..." : "Generate QR Code")
                                ])
                            ] : [
                                // QR Code Display
                                m(".text-center.mb-4", [
                                    Profile.quickMobileLogin.qrData ? [
                                        m("div.mb-4", [
                                            m("p.text-sm.text-gray-600.mb-4", "Scan this QR code from your PC to log into this mobile device"),
                                            m("div.flex.justify-center.mb-4", [
                                                m("img", {
                                                    src: Profile.quickMobileLogin.qrData.qr_code,
                                                    alt: "QR Code for Mobile Login",
                                                    style: "max-width: 250px; height: auto;"
                                                })
                                            ]),
                                            Profile.quickMobileLogin.isPolling ? [
                                                m("div.flex.items-center.justify-center.gap-2.mb-4", [
                                                    m("span.loading.loading-spinner.loading-sm"),
                                                    m("span.text-sm", "Waiting for PC authentication...")
                                                ]),
                                                m("p.text-xs.text-gray-500", "QR code expires in 5 minutes")
                                            ] : null,
                                            m("button.btn.btn-outline.btn-sm", {
                                                onclick: () => {
                                                    Profile.quickMobileLogin.isPolling = false;
                                                    Profile.quickMobileLogin.showQR = false;
                                                    Profile.quickMobileLogin.qrData = null;
                                                    Profile.quickMobileLogin.sessionToken = null;
                                                    m.redraw();
                                                }
                                            }, "Cancel")
                                        ])
                                    ] : [
                                        m("div.flex.items-center.justify-center.gap-2.mb-4", [
                                            m("span.loading.loading-spinner.loading-sm"),
                                            m("span.text-sm", "Generating QR code...")
                                        ])
                                    ]
                                ])
                            ],

                            // Instructions
                            m(".mt-4.p-3.bg-green-100.rounded-lg", [
                                m("h4.font-semibold.text-green-800.mb-2", "How to use Quick Mobile Login:"),
                                m("ol.text-sm.text-green-700.space-y-1", [
                                    m("li", "1. Click 'Generate QR Code' on your mobile device"),
                                    m("li", "2. Open your browser on your PC and make sure you're logged in"),
                                    m("li", "3. Scan the QR code from your PC (you can use your browser or any QR scanner)"),
                                    m("li", "4. Your mobile device will automatically log in once scanned")
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

export {Profile};
