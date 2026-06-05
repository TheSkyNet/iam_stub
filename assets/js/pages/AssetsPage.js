import m from "mithril";
import { WebSocketService } from "../services/WebSocketService";
import { Icon } from "../components/Icon";
import Card from "../components/DaisyUI/Card";
import Button from "../components/DaisyUI/Button";
import Alert from "../components/DaisyUI/Alert";
import Badge from "../components/DaisyUI/Badge";
import TextInput from "../components/DaisyUI/TextInput";
import Chat from "../components/DaisyUI/Chat";

const AssetsPage = {
    message: "",

    oninit: function() {
        AssetsPage.message = "";
        WebSocketService.connect();
    },

    onremove: function() {
        WebSocketService.disconnect();
    },

    sendMessage: function() {
        if (AssetsPage.message && AssetsPage.message.trim()) {
            WebSocketService.send(AssetsPage.message);
            AssetsPage.message = "";
            m.redraw();
        }
    },

    view: function() {
        return m(".container.mx-auto.p-6.md:p-10.max-w-7xl.space-y-12", [
            m("header.space-y-4", [
                m("h1.text-4xl.font-black", "Framework Assets & Examples"),
                m("p.text-xl.opacity-60", "Explore built-in, out-of-the-box features with no 3rd party APIs.")
            ]),

            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                // WebSocket Example
                m(Card, { title: "Native WebSockets", class: "bg-base-200" }, [
                    m(".space-y-4", [
                        m(".flex.items-center.gap-2", [
                            m("span", "Status:"),
                            m(Badge, { 
                                color: WebSocketService.status === "connected" ? "success" : 
                                       WebSocketService.status === "connecting" ? "warning" : "error" 
                            }, WebSocketService.status)
                        ]),

                        m(".h-64.overflow-y-auto.bg-base-100.rounded-xl.p-4.space-y-2", [
                            WebSocketService.messages.length === 0 
                                ? m(".text-center.opacity-50.py-20", "No messages yet")
                                : WebSocketService.messages.map(msg => {
                                    if (msg.type === 'welcome') {
                                        return m(".text-center.text-xs.opacity-50", msg.message);
                                    }
                                    return m(Chat, {
                                        header: [`ID: ${msg.from} `, m("time.text-xs.opacity-50", msg.time)],
                                        position: msg.from === WebSocketService.socket?.resourceId ? "end" : "start",
                                        color: msg.from === WebSocketService.socket?.resourceId ? "primary" : "neutral"
                                    }, msg.message);
                                })
                        ]),

                        m(".flex.gap-2", [
                            m(TextInput, { 
                                placeholder: "Type a message...", 
                                class: "flex-1",
                                value: AssetsPage.message,
                                oninput: (e) => { AssetsPage.message = e.target.value; },
                                onkeydown: (e) => { if (e.key === "Enter") AssetsPage.sendMessage(); }
                            }),
                            m(Button, { color: "primary", onclick: AssetsPage.sendMessage }, "Send")
                        ]),
                        
                        m(Alert, { type: "info", class: "text-xs" }, [
                            m(Icon, { icon: "fa-solid fa-circle-info" }),
                            m("span", "This uses a native PHP server running via Ratchet. In Docker, it is managed by supervisor and proxied by Nginx.")
                        ])
                    ])
                ]),

                // Other assets example
                m(Card, { title: "Native Server-Sent Events (SSE)", class: "bg-base-200" }, [
                    m(".space-y-4", [
                        m("p", "SSE is another out-of-the-box real-time solution for unidirectional data streams."),
                        m(Button, { 
                            color: "secondary", 
                            onclick: () => m.route.set("/sse-test") 
                        }, "Go to SSE Demo"),
                        
                        m("div.divider", "Core Helpers"),
                        m(".grid.grid-cols-2.gap-2", [
                            m(".p-4.bg-base-100.rounded-xl.text-center", [
                                m(Icon, { icon: "fa-solid fa-shield-halved", class: "text-2xl mb-2" }),
                                m(".text-xs.font-bold", "Encryption")
                            ]),
                            m(".p-4.bg-base-100.rounded-xl.text-center", [
                                m(Icon, { icon: "fa-solid fa-envelope", class: "text-2xl mb-2" }),
                                m(".text-xs.font-bold", "Email Service")
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default AssetsPage;
