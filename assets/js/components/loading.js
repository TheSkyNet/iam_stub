import m from "mithril";
const { Icon } = require('./Icon');

function loading() {
    return m('div', { class: 'flex items-center justify-center py-12' }, [
        m(Icon, { name: 'fa-solid fa-circle-notch', class: 'fa-spin text-4xl text-primary', ariaLabel: 'Loading' })
    ]);
}

export { loading }