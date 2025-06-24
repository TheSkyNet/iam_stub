const PusherTest = {
    pusherClient: null,
    isConnected: false,
    connectionState: 'disconnected',
    testChannel: null,
    messages: [],
    testMessage: '',

    oninit: function() {
        // Initialize Pusher client if available
        if (typeof pusherClient !== 'undefined') {
            this.pusherClient = pusherClient;
            this.initializePusher();
        }
    },

    initializePusher: async function() {
        try {
            const initialized = await this.pusherClient.initialize();
            if (initialized) {
                // Listen for connection events
                this.pusherClient.addEventListener('pusher:connected', () => {
                    PusherTest.isConnected = true;
                    PusherTest.connectionState = 'connected';
                    m.redraw();
                });

                this.pusherClient.addEventListener('pusher:disconnected', () => {
                    PusherTest.isConnected = false;
                    PusherTest.connectionState = 'disconnected';
                    m.redraw();
                });

                this.pusherClient.addEventListener('pusher:state_change', (states) => {
                    PusherTest.connectionState = states.current;
                    m.redraw();
                });

                // Subscribe to test channel
                this.subscribeToTestChannel();
            }
        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
            this.addMessage('Error: Failed to initialize Pusher - ' + error.message, 'error');
        }
    },

    subscribeToTestChannel: function() {
        if (!this.pusherClient) return;

        this.testChannel = this.pusherClient.subscribe('test-channel', {
            'test-event': (data) => {
                PusherTest.addMessage(`Received: ${JSON.stringify(data)}`, 'received');
                m.redraw();
            }
        });

        if (this.testChannel) {
            this.addMessage('Subscribed to test-channel', 'info');
        }
    },

    sendTestMessage: function() {
        if (!this.testMessage.trim()) return;

        // Send test message via API
        m.request({
            method: "POST",
            url: "/api/pusher/trigger",
            withCredentials: true,
            body: {
                channel: 'test-channel',
                event: 'test-event',
                data: {
                    message: this.testMessage,
                    timestamp: new Date().toISOString(),
                    sender: 'Test User'
                }
            }
        }).then((result) => {
            if (result.success) {
                PusherTest.addMessage(`Sent: ${PusherTest.testMessage}`, 'sent');
                PusherTest.testMessage = '';
            } else {
                PusherTest.addMessage(`Error sending: ${result.message}`, 'error');
            }
            m.redraw();
        }).catch((error) => {
            PusherTest.addMessage(`Error: ${error.message || 'Failed to send message'}`, 'error');
            m.redraw();
        });
    },

    addMessage: function(text, type = 'info') {
        this.messages.push({
            text: text,
            type: type,
            timestamp: new Date().toLocaleTimeString()
        });

        // Keep only last 50 messages
        if (this.messages.length > 50) {
            this.messages = this.messages.slice(-50);
        }
    },

    getMessageClass: function(type) {
        switch (type) {
            case 'sent': return 'text-blue-600';
            case 'received': return 'text-green-600';
            case 'error': return 'text-red-600';
            case 'info': return 'text-gray-600';
            default: return 'text-gray-600';
        }
    },

    clearMessages: function() {
        this.messages = [];
    },

    view: function() {
        return m(".min-h-screen.bg-base-200.p-8", [
            m(".container.mx-auto.max-w-4xl", [
                m(".text-center.mb-8", [
                    m("h1.text-4xl.font-bold.text-base-content.mb-4", "Pusher Real-time Test"),
                    m("p.text-lg.text-base-content.opacity-70", "Test real-time WebSocket communication with Pusher.js")
                ]),

                // Connection Status
                m(".card.bg-base-100.shadow-xl.mb-6", [
                    m(".card-body", [
                        m("h2.card-title.text-2xl.mb-4", "Connection Status"),
                        m(".flex.items-center.gap-4", [
                            m(".badge", {
                                class: this.isConnected ? 'badge-success' : 'badge-error'
                            }, this.isConnected ? 'Connected' : 'Disconnected'),
                            m("span.text-sm.opacity-70", `State: ${this.connectionState}`),
                            this.pusherClient && this.pusherClient.getSocketId() ? 
                                m("span.text-xs.opacity-50", `Socket ID: ${this.pusherClient.getSocketId()}`) : null
                        ])
                    ])
                ]),

                // Test Message Sender
                m(".card.bg-base-100.shadow-xl.mb-6", [
                    m(".card-body", [
                        m("h2.card-title.text-2xl.mb-4", "Send Test Message"),
                        m(".form-control.w-full", [
                            m(".label", [
                                m("span.label-text", "Message")
                            ]),
                            m("input.input.input-bordered.w-full", {
                                type: "text",
                                placeholder: "Enter your test message...",
                                value: this.testMessage,
                                onchange: (e) => {
                                    this.testMessage = e.target.value;
                                },
                                onkeypress: (e) => {
                                    if (e.key === 'Enter') {
                                        this.sendTestMessage();
                                    }
                                }
                            })
                        ]),
                        m(".card-actions.justify-end.mt-4", [
                            m("button.btn.btn-primary", {
                                onclick: () => this.sendTestMessage(),
                                disabled: !this.isConnected || !this.testMessage.trim()
                            }, "Send Message")
                        ])
                    ])
                ]),

                // Message Log
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m(".flex.justify-between.items-center.mb-4", [
                            m("h2.card-title.text-2xl", "Message Log"),
                            m("button.btn.btn-sm.btn-outline", {
                                onclick: () => this.clearMessages()
                            }, "Clear")
                        ]),
                        m(".bg-base-200.rounded.p-4.h-64.overflow-y-auto", [
                            this.messages.length === 0 ? 
                                m(".text-center.text-gray-500.italic", "No messages yet. Send a test message to see real-time communication in action!") :
                                this.messages.map((msg, index) => 
                                    m(".mb-2", { key: index }, [
                                        m("span.text-xs.opacity-50", `[${msg.timestamp}] `),
                                        m("span", {
                                            class: this.getMessageClass(msg.type)
                                        }, msg.text)
                                    ])
                                )
                        ])
                    ])
                ]),

                // Instructions
                m(".card.bg-base-100.shadow-xl.mt-6", [
                    m(".card-body", [
                        m("h2.card-title.text-2xl.mb-4", "How to Test"),
                        m("ol.list-decimal.list-inside.space-y-2.text-base-content.opacity-70", [
                            m("li", "Make sure Pusher credentials are configured in your .env file"),
                            m("li", "Check the connection status above - it should show 'Connected'"),
                            m("li", "Type a message in the input field and click 'Send Message'"),
                            m("li", "Open this page in multiple browser tabs to see real-time communication"),
                            m("li", "Messages sent from one tab will appear in all other tabs instantly"),
                            m("li", "Check the browser console for detailed Pusher logs")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export { PusherTest };