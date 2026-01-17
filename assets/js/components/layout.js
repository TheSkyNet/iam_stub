import m from "mithril";
const {AuthService} = require("../services/AuthserviceService");
const { Icon } = require("./Icon");

function layout(view) {
    return {
        oninit: function() {
            // Initialize AuthService on app load
            AuthService.init();
        },
        view: function(vnode) {
            // const isLoggedIn = AuthService.isLoggedIn();
            // const user = AuthService.getUser();

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
