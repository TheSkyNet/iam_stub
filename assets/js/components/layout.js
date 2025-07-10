const {AuthService} = require("../services/AuthserviceService");

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
                            // Show user menu when logged in
                            isLoggedIn ? [
                                m("li", [
                                    m("div.dropdown.dropdown-end", [
                                        m("div.btn.btn-ghost.btn-sm", {
                                            tabindex: "0",
                                            role: "button"
                                        }, [
                                            m("span", `Welcome, ${user?.name || user?.user?.name || 'User'}`),
                                            m("svg.w-4.h-4.ml-1", {
                                                fill: "none",
                                                stroke: "currentColor",
                                                viewBox: "0 0 24 24"
                                            }, [
                                                m("path", {
                                                    "stroke-linecap": "round",
                                                    "stroke-linejoin": "round",
                                                    "stroke-width": "2",
                                                    d: "M19 9l-7 7-7-7"
                                                })
                                            ])
                                        ]),
                                        m("ul.dropdown-content.menu.p-2.shadow.bg-base-100.rounded-box.w-52", {
                                            tabindex: "0"
                                        }, [
                                            m("li", [
                                                m("a", {
                                                    onclick: () => {
                                                        m.route.set('/profile');
                                                    }
                                                }, [
                                                    m("svg.w-4.h-4", {
                                                        fill: "none",
                                                        stroke: "currentColor",
                                                        viewBox: "0 0 24 24"
                                                    }, [
                                                        m("path", {
                                                            "stroke-linecap": "round",
                                                            "stroke-linejoin": "round",
                                                            "stroke-width": "2",
                                                            d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                        })
                                                    ]),
                                                    "Profile"
                                                ])
                                            ]),
                                            m("li", [
                                                m("a", {
                                                    onclick: () => {
                                                        AuthService.logout().then(() => {
                                                            m.route.set('/');
                                                            m.redraw();
                                                        });
                                                    }
                                                }, [
                                                    m("svg.w-4.h-4", {
                                                        fill: "none",
                                                        stroke: "currentColor",
                                                        viewBox: "0 0 24 24"
                                                    }, [
                                                        m("path", {
                                                            "stroke-linecap": "round",
                                                            "stroke-linejoin": "round",
                                                            "stroke-width": "2",
                                                            d: "M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                                        })
                                                    ]),
                                                    "Logout"
                                                ])
                                            ])
                                        ])
                                    ])
                                ])
                            ] : [
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

module.exports = {layout}
