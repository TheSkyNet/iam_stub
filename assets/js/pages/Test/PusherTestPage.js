import m from "mithril";
import { Icon } from "../../components/Icon";

const PusherTestPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", [
                        m(Icon, { name: "fa-solid fa-broadcast-tower" }),
                        " Pusher Real-time Test"
                    ]),
                    m("p", "Test real-time notifications and websocket connectivity."),
                    m(".mockup-code.mt-4", [
                        m("pre", { "data-prefix": "$" }, m("code", "Listening for events...")),
                        m("pre.text-success", { "data-prefix": ">" }, m("code", "Connected to Pusher!"))
                    ]),
                    m(".card-actions.justify-end.mt-6", [
                        m("button.btn.btn-primary", "Send Test Event")
                    ])
                ])
            ])
        ]);
    }
};

export default PusherTestPage;
