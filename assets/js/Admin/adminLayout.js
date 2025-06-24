const {Logout} = require("../Login/LoginModule");

function adminLayout(view) {
    return {
        view: (vnode) => {
            return [
                // Top navigation using DaisyUI navbar
                m("nav.navbar.bg-primary.text-primary-content", [
                    m(".navbar-start", [
                        m(m.route.Link, {
                            class: "btn btn-ghost text-xl",
                            href: "/"
                        }, "Admin Panel")
                    ]),
                    m(".navbar-end", [
                        m("button.btn.btn-ghost", {
                            onclick: Logout.logout
                        }, "Sign out")
                    ])
                ]),

                // Main container using DaisyUI drawer
                m(".drawer.lg:drawer-open", [
                    m("input#drawer-toggle.drawer-toggle[type=checkbox]"),

                    // Main content
                    m(".drawer-content.flex.flex-col", [
                        m("main.flex-1.p-6.bg-base-200.min-h-screen", {
                            role: "main"
                        }, m(view, vnode.attrs))
                    ]),

                    // Sidebar
                    m(".drawer-side", [
                        m("label.drawer-overlay[for=drawer-toggle]"),
                        m("aside.w-64.min-h-full.bg-base-100", [
                            m("ul.menu.p-4.space-y-2", [
                                navItem("/", "Dashboard"),
                                navItem("/user", "Users"),
                                navItem("/settings", "Settings")
                            ])
                        ])
                    ])
                ])
            ]
        }
    }
}

// Helper function for nav items using DaisyUI menu
function navItem(href, text) {
    return m("li", [
        m(m.route.Link, {
            class: "btn btn-ghost justify-start",
            href: href
        }, text)
    ])
}

module.exports = { adminLayout }
