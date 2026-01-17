import m from "mithril";
import { AuthService } from "../services/AuthserviceService";
import { Icon } from "./Icon";
import Footer from "./Footer";
import Toasts from "./Toasts";

function layout(view) {
    return {
        view: function (vnode) {
            const isLoggedIn = AuthService.isLoggedIn();
            const user = AuthService.getUser();

            let authLinks;
            if (isLoggedIn) {
                authLinks = [
                    m("span.mr-4.hidden.md:inline-block", `Welcome, ${user?.name || user?.email || 'User'}`),
                    m(m.route.Link, {
                        href: "/profile",
                        class: "btn btn-ghost btn-sm"
                    }, [
                        m(Icon, { icon: "fa-solid fa-user" }),
                        " Profile"
                    ]),
                    m("button.btn.btn-ghost.btn-sm.text-error", {
                        onclick: () => {
                            AuthService.logout().then(() => m.route.set('/login'));
                        }
                    }, [
                        m(Icon, { icon: "fa-solid fa-right-from-bracket" }),
                        " Logout"
                    ])
                ];
            } else {
                authLinks = [
                    m(m.route.Link, {
                        href: "/login",
                        class: "btn btn-ghost btn-sm"
                    }, [
                        m(Icon, { icon: "fa-solid fa-right-to-bracket" }),
                        " Login"
                    ]),
                    m(m.route.Link, {
                        href: "/register",
                        class: "btn btn-primary btn-sm ml-2"
                    }, [
                        m(Icon, { icon: "fa-solid fa-user-plus" }),
                        " Register"
                    ])
                ];
            }

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
                    m(".navbar-end", authLinks)
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
