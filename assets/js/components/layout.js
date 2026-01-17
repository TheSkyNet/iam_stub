import m from "mithril";
const { AuthService } = require("../services/AuthserviceService");
const { Icon } = require("./Icon");
import Footer from "./Footer";
import Toasts from "./Toasts";

function layout(view) {
    return {
        view: function (vnode) {
            return m(".flex.flex-col.min-h-screen", [
                m(Toasts),
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
                    m(".navbar-end")
                ]),
                m("main.flex-grow.bg-base-200", {
                    "id": "main"
                }, [
                    m(view, vnode.attrs)
                ]),
                m(Footer)
            ]);
        }
    };
}

export {layout}
