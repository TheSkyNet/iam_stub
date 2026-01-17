import m from "mithril";

const NavBrand = {
    view: (vnode) => {
        const { href = "/", name = "daisyUI" } = vnode.attrs;
        return m(m.route.Link, {
            href: href,
            class: "btn btn-ghost text-xl"
        }, name);
    }
};

export default NavBrand;
