const {Logout} = require("../Login/LoginModule");

function adminLayout(view) {
    return {
        view: (vnode) => {
            return [
                // Simple top navigation
                m("nav.navbar.sticky-top", {
                    style: {
                        background: '#8198c4',
                        padding: '1rem'
                    }
                }, [
                    m("a.navbar-brand.text-white", {
                        href: "#",
                        style: { fontSize: '1.2rem' }
                    }, "Admin Panel"),
                    m("a.nav-link.text-white", {
                        onclick: Logout.logout,
                        style: { cursor: 'pointer' }
                    }, "Sign out")
                ]),

                // Main container
                m("div.container-fluid", [
                    m("div.row", [
                        // Simple sidebar
                        m("nav.col-md-3.sidebar", {
                            style: {
                                background: '#f8f9fa',
                                minHeight: 'calc(100vh - 60px)',
                                padding: '1rem'
                            }
                        }, [
                            m("ul.nav.flex-column", [
                                navItem("/", "Dashboard", "octicon-home"),
                                navItem("/user", "Users", "octicon-person"),
                                navItem("/settings", "Settings", "octicon-gear")
                            ])
                        ]),

                        // Main content
                        m("main.col-md-9", {
                            role: "main",
                            style: {
                                padding: '2rem',
                                minHeight: 'calc(100vh - 60px)'
                            }
                        }, m(view, vnode.attrs))
                    ])
                ])
            ]
        }
    }
}

// Helper function for nav items
function navItem(href, text, icon) {
    return m("li.nav-item",
        m(m.route.Link, {
            class: "nav-link",
            href: href,
            style: {
                color: '#333',
                padding: '0.5rem 1rem',
                borderRadius: '0.25rem',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
                textDecoration: 'none',
                transition: 'background 0.2s'
            }
        }, [
            m("i.octicon", {
                class: icon,
                style: {
                    fontSize: '14px'
                }
            }),
            text
        ])
    )
}

module.exports = { adminLayout }
