import m from "mithril";

const Checkbox = {
    view: ({ attrs }) => {
        const { color, size, ...props } = attrs;
        const classes = [
            "checkbox",
            color && `checkbox-${color}`,
            size && `checkbox-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { type: "checkbox", ...props, class: classes });
    }
};

export default Checkbox;
