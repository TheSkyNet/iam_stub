import m from "mithril";
import { Icon } from "../../components/Icon";
import { SseService } from "../../services/SseService";

const SseTestPage = {
    events: [],
    connection: null,

    oninit: () => {
        SseTestPage.events = [];
    },

    onremove: () => {
        if (SseTestPage.connection) {
            SseTestPage.connection.close();
        }
    },

    startClock: () => {
        if (SseTestPage.connection) SseTestPage.connection.close();
        SseTestPage.events = [];
        SseTestPage.events.push({ time: new Date().toLocaleTimeString(), message: "Starting clock SSE..." });
        
        SseTestPage.connection = SseService.clock({ count: 5, interval: 1000 });
        SseTestPage.connection.listen('tick', (e) => {
            const data = JSON.parse(e.data);
            SseTestPage.events.push({ 
                time: new Date().toLocaleTimeString(), 
                message: `Clock tick: ${data.time} (index: ${data.index})` 
            });
            m.redraw();
        });
    },

    startEcho: () => {
        if (SseTestPage.connection) SseTestPage.connection.close();
        SseTestPage.events = [];
        const msg = "Hello from IamLab!";
        SseTestPage.events.push({ time: new Date().toLocaleTimeString(), message: `Starting echo SSE with message: "${msg}"` });
        
        SseTestPage.connection = SseService.echo(msg);
        SseTestPage.connection.listen('echo', (e) => {
            const data = JSON.parse(e.data);
            SseTestPage.events.push({ 
                time: new Date().toLocaleTimeString(), 
                message: `Echo received: ${data.message}` 
            });
            m.redraw();
        });
    },

    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", [
                        m(Icon, { icon: "fa-solid fa-stream" }),
                        " Server-Sent Events (SSE) Test"
                    ]),
                    m("p", "Test real-time one-way communication from server to client."),
                    
                    m(".flex.gap-2.mt-4", [
                        m("button.btn.btn-primary", { onclick: SseTestPage.startClock }, [
                            m(Icon, { icon: "fa-solid fa-clock" }),
                            " Start 5-Tick Clock"
                        ]),
                        m("button.btn.btn-secondary", { onclick: SseTestPage.startEcho }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Test Echo"
                        ]),
                        m("button.btn.btn-ghost", { 
                            onclick: () => {
                                if (SseTestPage.connection) SseTestPage.connection.close();
                                SseTestPage.events.push({ time: new Date().toLocaleTimeString(), message: "Connection closed." });
                            } 
                        }, "Close Connection")
                    ]),

                    m(".mt-6", [
                        m("h3.font-bold.mb-2", "Event Log:"),
                        m(".bg-base-300.p-4.rounded-lg.overflow-y-auto", [
                            SseTestPage.events.length === 0 
                                ? m("p.italic.text-base-content.opacity-50", "No events yet. Click a button above to start.")
                                : SseTestPage.events.map((e, i) => m(".mb-1.font-mono.text-sm", [
                                    m("span.text-primary", `[${e.time}] `),
                                    m("span", e.message)
                                ]))
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default SseTestPage;
