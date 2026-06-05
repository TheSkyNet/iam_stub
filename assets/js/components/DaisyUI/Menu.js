import m from "mithril";

const Menu = {
    view: ({ attrs, children }) => {
        const { horizontal, vertical, ...props } = attrs;
        const classes = [
            "menu",
            horizontal && "menu-horizontal",
            vertical && "menu-vertical",
            "bg-base-200",
            "rounded-box",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("ul", { 
            ...props, 
            class: classes, 
            role: "menu" 
        }, children);
    }
};

export default Menu;
