import m from "mithril";
const {AuthService} = require("../services/AuthserviceService");
const { Icon } = require("./Icon");

function layout(view) {
    return {
        oninit: () => {
            // Initialize AuthService on app load
            AuthService.init();
        },
        view: (vnode) => {
            const isLoggedIn = AuthService.isLoggedIn();
            const user = AuthService.getUser();

            return [
                m("nav.navbar.bg-base-100.shadow-lg", {
                    "id": "menu", 
                    "role": "navigation"
                }, [
                    m(".navbar-start", [
                        m(m.route.Link, {
                            "class": "btn btn-ghost text-xl", 
                            "href": "/"
                        }, "Phalcon Stub")
                    ]),
                    m(".navbar-end", [
                        m("ul.menu.menu-horizontal.px-1", [
                            m("li", [
                                m(m.route.Link, {
                                    "class": "btn btn-ghost btn-sm", 
                                    "href": "/pusher-test"
                                }, "Pusher Test")
                            ]),
                            m("li", [
                                m(m.route.Link, {
                                    "class": "btn btn-ghost btn-sm", 
                                    "href": "/test"
                                }, "Test Page")
                            ]),
                            // Show user menu when logged in
                             [
                                // Show login/register buttons when not logged in
                                m("li", [
                                    m(m.route.Link, {
                                        "class": "btn btn-ghost btn-sm",
                                        "href": "/register"
                                    }, "Register")
                                ]),
                                m("li", [
                                    m(m.route.Link, {
                                        "class": "btn btn-primary btn-sm",
                                        "href": "/login"
                                    }, "Login")
                                ])
                            ]
                        ])
                    ])
                ]),
                m(".min-h-screen.bg-base-200", {
                    "id": "main"
                }, [
                    m(view, vnode.attrs)
                ])
            ]
        }
    }
}

export {layout}
