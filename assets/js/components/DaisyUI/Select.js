import m from "mithril";

const Select = {
    view: ({ attrs, children }) => {
        const { color, size, ghost, ...props } = attrs;
        const classes = [
            "select",
            color && `select-${color}`,
            size && `select-${size}`,
            ghost && "select-ghost",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("select", { ...props, class: classes }, children);
    }
};

export default Select;
