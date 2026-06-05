import m from "mithril";

const Badge = {
    view: ({ attrs, children }) => {
        const { color, size, outline, ...props } = attrs;
        const classes = [
            "badge",
            color && `badge-${color}`,
            size && `badge-${size}`,
            outline && "badge-outline",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("span", { ...props, class: classes }, children);
    }
};

export default Badge;
