import m from "mithril";
const {AuthService} = require("../services/AuthserviceService");
const {MessageDisplay} = require("./MessageDisplay");
const { Icon } = require("./Icon");

const DesktopQRScanner = {
    // Desktop QR scanner state
    scanner: {
        isScanning: false,
        scannedToken: null,
        isProcessing: false
    },

    oninit: () => {
        // Redirect to login if not authenticated
        if (!AuthService.isLoggedIn()) {
            m.route.set('/login');

        }
    },

    view: () => {
        const user = AuthService.getUser();
        const userData = user?.user || user;

        return m(".container.mx-auto.p-6", [
            m(MessageDisplay),
            m(".max-w-2xl.mx-auto", [
                m(".bg-white.shadow-lg.rounded-lg.p-6", [
                    m("h1.text-3xl.font-bold.text-gray-800.mb-6", "Desktop QR Scanner"),
                    m("p.text-gray-600.mb-6", `Welcome ${userData?.name || 'User'}! Use this scanner to authenticate mobile QR codes.`),

                    // Desktop QR Scanner Section
                    m(".mb-8", [
                        m("h2.text-xl.font-semibold.text-gray-700.mb-4", "Mobile QR Code Scanner"),
                        m(".bg-blue-50.p-4.rounded-lg", [
                            m("p.text-sm.text-gray-600.mb-4", 
                                "Scan QR codes generated on your mobile device to quickly log into your mobile account. This allows you to authenticate your mobile sessions from your desktop."
                            ),

                            // QR Scanner Interface
                            !DesktopQRScanner.scanner.isScanning ? [
                                m(".flex.items-center.justify-between.mb-4", [
                                    m("div", [
                                        m("p.font-medium", "Mobile QR Code Scanner"),
                                        m("p.text-sm.text-gray-500", "Scan QR codes from your mobile device")
                                    ]),
                                    m("button.btn.btn-primary", {
                                        onclick: () => {
                                            DesktopQRScanner.scanner.isScanning = true;
                                            DesktopQRScanner.scanner.scannedToken = null;
                                            m.redraw();
                                        }
                                    }, "Start Scanner")
                                ])
                            ] : [
                                // Scanner Active State
                                m(".text-center.mb-4", [
                                    m("div.bg-white.border-2.border-dashed.border-blue-300.rounded-lg.p-8.mb-4", [
                                        m("div.text-6xl.text-blue-400.mb-4", m(Icon, { name: 'fa-solid fa-desktop' })),
                                        m("h3.text-lg.font-semibold.text-gray-700.mb-2", "Desktop QR Scanner Active"),
                                        m("p.text-sm.text-gray-600.mb-4", "Point your camera at a QR code on your mobile device"),

                                        // Manual Token Input (for testing/fallback)
                                        m(".mt-4", [
                                            m("p.text-xs.text-gray-500.mb-2", "Or paste mobile QR code data manually:"),
                                            m("textarea.textarea.textarea-bordered.w-full.text-xs", {
                                                placeholder: "Paste mobile QR code JSON data here...",
                                                rows: 3,
                                                onchange: (e) => {
                                                    try {
                                                        const qrData = JSON.parse(e.target.value);
                                                        if (qrData.type === 'mobile_qr_login' && qrData.session_token) {
                                                            DesktopQRScanner.scanner.scannedToken = qrData.session_token;
                                                            m.redraw();
                                                        }
                                                    } catch (error) {
                                                        console.error('Invalid mobile QR data:', error);
                                                        MessageDisplay.setMessage('Invalid QR code data. Please try again.', 'error');
                                                    }
                                                }
                                            })
                                        ])
                                    ]),

                                    // Scanned Token Display
                                    DesktopQRScanner.scanner.scannedToken ? [
                                        m(".bg-green-50.border.border-green-200.rounded-lg.p-4.mb-4", [
                                            m("div.flex.items-center.mb-2", [
                                                m("span.text-green-600.mr-2", "✓"),
                                                m("span.font-semibold.text-green-800", "Mobile QR Code Detected")
                                            ]),
                                            m("p.text-sm.text-gray-600.mb-3", "Session Token: " + DesktopQRScanner.scanner.scannedToken.substring(0, 20) + "..."),
                                            m(".flex.gap-2", [
                                                m("button.btn.btn-success.btn-sm", {
                                                    disabled: DesktopQRScanner.scanner.isProcessing,
                                                    onclick: () => {
                                                        DesktopQRScanner.scanner.isProcessing = true;
                                                        AuthService.authenticateMobileQR(DesktopQRScanner.scanner.scannedToken)
                                                            .then(response => {
                                                                DesktopQRScanner.scanner.isProcessing = false;
                                                                if (response.success) {
                                                                    MessageDisplay.setMessage('Mobile session authenticated successfully!', 'success');
                                                                    DesktopQRScanner.scanner.isScanning = false;
                                                                    DesktopQRScanner.scanner.scannedToken = null;
                                                                } else {
                                                                    MessageDisplay.setMessage(response.message || 'Authentication failed', 'error');
                                                                }
                                                                m.redraw();
                                                            })
                                                            .catch(error => {
                                                                DesktopQRScanner.scanner.isProcessing = false;
                                                                MessageDisplay.setMessage('Authentication failed: ' + error.message, 'error');
                                                                m.redraw();
                                                            });
                                                    }
                                                }, DesktopQRScanner.scanner.isProcessing ? "Authenticating..." : "Authenticate Mobile"),
                                                m("button.btn.btn-outline.btn-sm", {
                                                    onclick: () => {
                                                        DesktopQRScanner.scanner.scannedToken = null;
                                                        m.redraw();
                                                    }
                                                }, "Scan Again")
                                            ])
                                        ])
                                    ] : null,

                                    m("button.btn.btn-outline.btn-sm", {
                                        onclick: () => {
                                            DesktopQRScanner.scanner.isScanning = false;
                                            DesktopQRScanner.scanner.scannedToken = null;
                                            DesktopQRScanner.scanner.isProcessing = false;
                                            m.redraw();
                                        }
                                    }, "Cancel Scanner")
                                ])
                            ],

                            // Instructions
                            m(".mt-4.p-3.bg-blue-100.rounded-lg", [
                                m("h4.font-semibold.text-blue-800.mb-2", "How to use Desktop QR Scanner:"),
                                m("ol.text-sm.text-blue-700.space-y-1", [
                                    m("li", "1. Make sure you're logged in on your mobile device"),
                                    m("li", "2. Go to your profile page on mobile and find 'Quick Mobile Login'"),
                                    m("li", "3. Click 'Generate QR Code' on your mobile device"),
                                    m("li", "4. Use this scanner to scan the QR code displayed on mobile"),
                                    m("li", "5. Click 'Authenticate Mobile' to complete the mobile login")
                                ])
                            ])
                        ])
                    ]),

                    // Navigation Actions
                    m(".flex.justify-between.items-center", [
                        m("button.btn.btn-outline", {
                            onclick: () => {
                                m.route.set('/');
                            }
                        }, "Back to Home"),

                        m("button.btn.btn-outline", {
                            onclick: () => {
                                m.route.set('/profile');
                            }
                        }, "Go to Profile")
                    ])
                ])
            ])
        ]);
    }
};

export {DesktopQRScanner};