import m from "mithril";
import { Icon } from "../../components/Icon";

const TestPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m("h1.text-3xl.font-bold.mb-6", "Generic Test Page"),
            m(".grid.grid-cols-1.md:grid-cols-2.gap-6", [
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "Component Tests"),
                        m("p", "General UI component testing area."),
                        m(".flex.gap-2.flex-wrap", [
                            m("button.btn.btn-xs", "xs"),
                            m("button.btn.btn-sm", "sm"),
                            m("button.btn.btn-md", "md"),
                            m("button.btn.btn-lg", "lg")
                        ])
                    ])
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "Route Tests"),
                        m("p", "Testing SPA routing and parameters."),
                        m(m.route.Link, { href: "/test?param=value", class: "btn btn-outline" }, "Test with Params")
                    ])
                ])
            ])
        ]);
    }
};

export default TestPage;
