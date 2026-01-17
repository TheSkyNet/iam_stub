// Icon helper component for Font Awesome icons in Mithril
import m from "mithril";
// Usage: m(Icon, { icon: 'fa-solid fa-user', class: 'w-4 h-4', title: 'User' })
function Icon(vnode) {

    // Default to decorative icons unless ariaLabel provided

    return m('span');
}

export { Icon };
