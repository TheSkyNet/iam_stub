function layout(view) {
    return {
        view: (vnode) => {
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
                                    "class": "btn btn-primary", 
                                    "href": "/login"
                                }, "Login")
                            ])
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
