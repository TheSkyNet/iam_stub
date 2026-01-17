import m from "mithril";

const NavUserMenu = {
    view: (vnode) => {
        const { 
            avatarUrl = "https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp",
            items = [
                { label: "Profile", href: "/profile", badge: "New" },
                { label: "Settings", href: "/settings" },
                { label: "Logout", href: "/logout" }
            ]
        } = vnode.attrs;

        return m(".dropdown.dropdown-end", [
            m(".btn.btn-ghost.btn-circle.avatar", {
                tabindex: 0,
                role: "button"
            }, [
                m(".w-10.rounded-full", [
                    m("img", {
                        alt: "User Avatar",
                        src: avatarUrl
                    })
                ])
            ]),
            m("ul.menu.menu-sm.dropdown-content.bg-base-100.rounded-box.z-1.mt-3.w-52.p-2.shadow", {
                tabindex: -1
            }, items.map(item => m("li", [
                m(m.route.Link, { 
                    href: item.href, 
                    class: item.badge ? "justify-between" : "" 
                }, [
                    item.label,
                    item.badge ? m("span.badge", item.badge) : null
                ])
            ])))
        ]);
    }
};

export default NavUserMenu;
