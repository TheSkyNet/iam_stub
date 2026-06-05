import m from "mithril";

const Link = {
    view: ({ attrs, children }) => {
        const { color, hover, ...props } = attrs;
        const classes = [
            "link",
            color && `link-${color}`,
            hover && "link-hover",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("a", { ...props, class: classes }, children);
    }
};

export default Link;
