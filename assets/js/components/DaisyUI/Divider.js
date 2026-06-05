import m from "mithril";

const Divider = {
    view: ({ attrs, children }) => {
        const { horizontal, vertical, color, ...props } = attrs;
        const classes = [
            "divider",
            horizontal && "divider-horizontal",
            vertical && "divider-vertical",
            color && `divider-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes, role: "separator" }, children);
    }
};

export default Divider;
