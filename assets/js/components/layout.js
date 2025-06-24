function layout(view) {
    return {
        view: (vnode) => {
            return [
                m("nav", {"class": "nav sidebar", "id": "menu", "role": "navigation"},
                    m("div", {"class": "container-fluid"},
                        [
                            m(m.route.Link, {"class": "link brand", "href": "/"},
                                [
                                    m("img", {
                                        "class": "img-fluid",
                                        "src": "/img/IAMLablogo-export.png",
                                        "alt": "Iam Labs"
                                    }),
                                    m("div", {"class": "sr-only"},
                                        [
                                            " I am ",
                                            m("span", {"class": "link-text"},
                                                "Lab"
                                            )
                                        ]
                                    )
                                ]
                            ),
                            m("div", {"id": "navCollapse"},
                                m("ul", {"class": "inner"},
                                    [
                                        m("li", {"class": "item"},
                                            m(m.route.Link, {"class": "link", "href": "/projects"},
                                                [
                                                    m("span", {"class": "link-text"},
                                                        "Projects"
                                                    ),
                                                    m("i", {"class": "octicon octicon-beaker"})
                                                ]
                                            )
                                        ),
                                        m("li", {"class": "item"},
                                            m(m.route.Link, {"class": "link", "href": "/packages"},
                                                [
                                                    m("span", {"class": "link-text"},
                                                        "Packages"
                                                    ),
                                                    m("i", {"class": "octicon octicon-mark-github"})
                                                ]
                                            )
                                        ),
                                        m("li", {"class": "item"},
                                            m(m.route.Link, {"class": "link", "href": "/code"},
                                                [
                                                    m("span", {"class": "link-text"},
                                                        "Code"
                                                    ),
                                                    m("i", {"class": "octicon octicon-code"})
                                                ]
                                            )
                                        ),
                                        m("li", {"class": "item"},
                                            m(m.route.Link, {"class": "link", "href": "/about"},
                                                [
                                                    m("span", {"class": "link-text"},
                                                        "About"
                                                    ),
                                                    m("i", {"class": "octicon octicon-broadcast"})
                                                ]
                                            )
                                        )
                                    ]
                                )
                            )
                        ]
                    )
                ),
                m("div", {"class": "container-fluid main h-100 ", "id": "main"},
                    m(view, vnode.attrs)
                )
            ]
        }
    }


}

module.exports = {layout}
