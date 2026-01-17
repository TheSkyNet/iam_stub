// Icon helper component for Font Awesome icons in Mithril
import m from "mithril";
// Usage: m(Icon, { icon: 'fa-solid fa-user', class: 'w-4 h-4', title: 'User' })
const Icon = {
    view: (vnode) => {
        const { icon, name, ...attrs } = vnode.attrs;
        const iconClass = name || icon;
        // Default to decorative icons unless ariaLabel provided
        const ariaHidden = !attrs.ariaLabel;

        return m('i', {
            ...attrs,
            class: `${iconClass} ${attrs.class || ""}`.trim(),
            "aria-hidden": ariaHidden ? "true" : undefined
        });
    }
};

export { Icon };
