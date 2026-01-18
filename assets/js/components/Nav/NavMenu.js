import m from "mithril";
import { Icon } from "../Icon";

const NavMenu = {
    view: (vnode) => {
        const { 
            items = [], 
            icon = "fa-solid fa-bars", 
            buttonClass = "btn btn-ghost btn-circle",
            dropdownClass = ""
        } = vnode.attrs;

        return m(".dropdown", { class: dropdownClass }, [
            m(".btn", {
                class: buttonClass,
                tabindex: 0,
                role: "button"
            }, [
                m(Icon, { icon: icon })
            ]),
            m("ul.menu.menu-sm.dropdown-content.bg-base-100.rounded-box.z-1.mt-3.w-52.p-2.shadow", {
                tabindex: -1
            }, items.map(item => m("li", [
                item.submenu ? [
                    m(m.route.Link, { href: "#" }, item.label),
                    m("ul.p-2", item.submenu.map(sub => m("li", m(m.route.Link, { href: sub.href }, sub.label))))
                ] : m(m.route.Link, { href: item.href }, item.label)
            ])))
        ]);
    }
};

export default NavMenu;
