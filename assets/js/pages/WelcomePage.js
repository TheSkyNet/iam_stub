import m from "mithril";
import { Icon } from "../components/Icon";

const WelcomePage = {
    view: () => {
        return m(".container.mx-auto.p-4.min-h-screen.flex.flex-col.items-center.justify-center", [
            m(".text-center.mb-12", [
                m("h1.text-5xl.font-bold.mb-4", "Welcome to IamLab"),
                m("p.text-xl.text-base-content/70", "Your Phalcon-based Laboratory for Identity and Access Management."),
            ]),
            
            m(".flex.flex-wrap.justify-center.gap-6.w-full", [
                // Card 1: Components
                m("div", {"class":"card bg-base-100 image-full w-96 shadow-sm"}, [
                    m("figure", 
                        m("img", {"src":"https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=800&q=80","alt":"Components"})
                    ),
                    m("div", {"class":"card-body"}, [
                        m("h2", {"class":"card-title"}, "Components"),
                        m("p", "Explore our DaisyUI v5 component library."),
                        m("div", {"class":"card-actions justify-end"}, 
                            m(m.route.Link, { href: "/components", class: "btn btn-primary" }, "View Library")
                        )
                    ])
                ]),

                // Card 2: Authentication
                m("div", {"class":"card bg-base-100 image-full w-96 shadow-sm"}, [
                    m("figure", 
                        m("img", {"src":"https://images.unsplash.com/photo-1557682250-33bd709cbe85?auto=format&fit=crop&w=800&q=80","alt":"Auth"})
                    ),
                    m("div", {"class":"card-body"}, [
                        m("h2", {"class":"card-title"}, "Authentication"),
                        m("p", "Secure JWT & OAuth integration services."),
                        m("div", {"class":"card-actions justify-end"}, 
                            m(m.route.Link, { href: "/login", class: "btn btn-primary" }, "Get Started")
                        )
                    ])
                ]),

                // Card 3: Real-time
                m("div", {"class":"card bg-base-100 image-full w-96 shadow-sm"}, [
                    m("figure", 
                        m("img", {"src":"https://images.unsplash.com/photo-1557683311-eac922347aa1?auto=format&fit=crop&w=800&q=80","alt":"Real-time"})
                    ),
                    m("div", {"class":"card-body"}, [
                        m("h2", {"class":"card-title"}, "Real-time"),
                        m("p", "WebSockets and Server-Sent Events."),
                        m("div", {"class":"card-actions justify-end"}, 
                            m(m.route.Link, { href: "/sse-test", class: "btn btn-primary" }, "Test SSE")
                        )
                    ])
                ]),

                // Card 4: Error Service
                m("div", {"class":"card bg-base-100 image-full w-96 shadow-sm"}, [
                    m("figure", 
                        m("img", {"src":"https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&w=800&q=80","alt":"Testing"})
                    ),
                    m("div", {"class":"card-body"}, [
                        m("h2", {"class":"card-title"}, "Error Service"),
                        m("p", "Global error handling and reporting."),
                        m("div", {"class":"card-actions justify-end"}, 
                            m(m.route.Link, { href: "/test", class: "btn btn-primary" }, "Run Tests")
                        )
                    ])
                ])
            ])
        ]);
    }
};

export default WelcomePage;
