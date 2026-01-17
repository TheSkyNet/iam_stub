import m from "mithril";

const NavSearchInput = {
    view: (vnode) => {
        return m("input.input.input-bordered.w-24.md:w-auto", {
            type: "text",
            placeholder: "Search",
            ...vnode.attrs
        });
    }
};

export default NavSearchInput;
