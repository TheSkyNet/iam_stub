// Icon helper component for Font Awesome icons in Mithril
import m from "mithril";
// Usage: m(Icon, { icon: 'fa-solid fa-user', class: 'w-4 h-4', title: 'User' })
const Icon = {
    view: (vnode) => {
        const { icon, ...attrs } = vnode.attrs;
        // Default to decorative icons unless ariaLabel provided
        const ariaHidden = !attrs.ariaLabel;

        return m('i', {
            ...attrs,
            class: `${icon} ${attrs.class || ""}`.trim(),
            "aria-hidden": ariaHidden ? "true" : undefined
        });
    }
};

export { Icon };
