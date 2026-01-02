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
                            isLoggedIn ? [
                                m("li", [
                                    m("div.dropdown.dropdown-end", [
                                        m("div.btn.btn-ghost.btn-sm", {
                                            tabindex: "0",
                                            role: "button"
                                        }, [
                                            m("span", `Welcome, ${user?.name || user?.user?.name || 'User'}`),
                                            m(Icon, { name: 'fa-solid fa-chevron-down', class: 'w-4 h-4 ml-1', title: 'Menu' })
                                        ]),
                                        m("ul.dropdown-content.menu.p-2.shadow.bg-base-100.rounded-box.w-52", {
                                            tabindex: "0"
                                        }, [
                                            AuthService.isAdmin() ? m("li", [
                                                m("a", {
                                                    onclick: () => {
                                                        m.route.set('/admin/error-logs');
                                                    }
                                                }, [
                                                    m(Icon, { name: 'fa-solid fa-triangle-exclamation', class: 'w-4 h-4', title: 'Error Logs' }),
                                                    "Error Logs"
                                                ])
                                            ]) : null,
                                            m("li", [
                                                m("a", {
                                                    onclick: () => {
                                                        m.route.set('/profile');
                                                    }
                                                }, [
                                                    m(Icon, { name: 'fa-solid fa-user', class: 'w-4 h-4', title: 'Profile' }),
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
                                                    m(Icon, { name: 'fa-solid fa-right-from-bracket', class: 'w-4 h-4', title: 'Logout' }),
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
