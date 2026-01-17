import m from "mithril";
import { Icon } from "../Icon";

const NavIndicator = {
    view: (vnode) => {
        const { icon, badgeClass = "badge-primary", onclick } = vnode.attrs;
        return m("button.btn.btn-ghost.btn-circle", { onclick: onclick }, [
            m(".indicator", [
                m(Icon, { icon: icon }),
                m("span.badge.badge-xs.indicator-item", { class: badgeClass })
            ])
        ]);
    }
};

export default NavIndicator;
