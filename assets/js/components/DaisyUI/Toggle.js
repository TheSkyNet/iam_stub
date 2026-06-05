import m from "mithril";

const Toggle = {
    view: ({ attrs }) => {
        const { color, size, ...props } = attrs;
        const classes = [
            "toggle",
            color && `toggle-${color}`,
            size && `toggle-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { type: "checkbox", ...props, class: classes });
    }
};

export default Toggle;
