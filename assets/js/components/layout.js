import m from "mithril";
import { AuthService } from "../services/AuthserviceService";
import { Icon } from "./Icon";
import Footer from "./Footer";

function layout(view) {
    return {
        view: function (vnode) {
            const isLoggedIn = AuthService.isLoggedIn();

            let authLinks;
            if (isLoggedIn) {
                authLinks = [
                    m(m.route.Link, {
                        href: "/profile",
                        class: "btn btn-ghost btn-sm"
                    }, "Profile"),
                    m(m.route.Link, {
                        href: "/payments",
                        class: "btn btn-ghost btn-sm"
                    }, "Payments"),
                    m("button.btn.btn-ghost.btn-sm.text-error", {
                        onclick: () => {
                            AuthService.logout().then(() => m.route.set('/login'));
                        }
                    }, "Logout")
                ];
            } else {
                authLinks = [
                    m(m.route.Link, {
                        href: "/login",
                        class: "btn btn-ghost btn-sm"
                    }, "Login"),
                    m(m.route.Link, {
                        href: "/register",
                        class: "btn btn-primary btn-sm ml-2"
                    }, "Register")
                ];
            }

            return m(".min-h-screen.flex.flex-col", [
                m(".navbar.bg-base-100.shadow", [
                    m(".navbar-start", [
                        m(m.route.Link, {
                            class: "btn btn-ghost text-xl",
                            href: "/"
                        }, "Phalcon Stub")
                    ]),
                    m(".navbar-end", authLinks)
                ]),
                m("main.flex-grow.bg-base-200", [
                    m(view, vnode.attrs)
                ]),
                m(Footer)
            ]);
        }
    };
}

export {layout}
