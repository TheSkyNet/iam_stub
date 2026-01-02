// Icon helper component for Font Awesome icons in Mithril
// Usage: m(Icon, { name: 'fa-solid fa-user', class: 'w-4 h-4', title: 'User' })
function Icon(vnode) {
    const { name = '', class: extra = '', title = '', ariaLabel } = vnode.attrs || {};
    // Default to decorative icons unless ariaLabel provided
    const attrs = {
        class: `${name} ${extra}`.trim(),
        'aria-hidden': ariaLabel ? null : 'true',
        title: title || null,
        'aria-label': ariaLabel || null,
        role: ariaLabel ? 'img' : null,
    };
    return m('i', attrs);
}

module.exports = { Icon };
