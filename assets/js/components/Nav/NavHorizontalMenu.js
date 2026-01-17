import m from "mithril";

const NavHorizontalMenu = {
    view: (vnode) => {
        const { items = [] } = vnode.attrs;
        return m("ul.menu.menu-horizontal.px-1", items.map(item => m("li", [
            item.submenu ? m("details", [
                m("summary", item.label),
                m("ul.p-2.bg-base-100.w-40.z-1", item.submenu.map(sub => m("li", m(m.route.Link, { href: sub.href }, sub.label))))
            ]) : m(m.route.Link, { href: item.href }, item.label)
        ])));
    }
};

export default NavHorizontalMenu;
